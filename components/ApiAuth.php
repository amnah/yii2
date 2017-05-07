<?php

namespace app\components;

use Yii;
use yii\di\Instance;
use yii\redis\Connection;
use yii\web\Request;
use yii\web\User as UserComponent;
use app\models\User;

/**
 * Authentication for api
 * This class checks for auth status from 1) cookies or 2) redis
 */
class ApiAuth extends \yii\filters\auth\HttpBearerAuth
{
    /**
     * @var Connection|string|array
     */
    public $redis = 'redis';

    /**
     * @var int Default expire time (seconds)
     */
    public $defaultTtl = 604800; // 604800 = 1 week

    /**
     * @var array Token data from redis
     */
    protected $tokenData = null;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->redis = Instance::ensure($this->redis, Connection::className());
    }

    /**
     * @inheritdoc
     */
    public function authenticate($userComponent, $request, $response)
    {
        // check if we're logged in via session/cookie
        // if so, we can return directly
        if ($userComponent->identity) {
            return $userComponent->identity;
        }

        // check for bearer token
        $token = $this->getBearerTokenFromHeader($request);
        if (!$token) {
            return null;
        }

        // attempt to login via token
        $user = $this->checkToken($token, $userComponent);
        if ($user) {
            // disable session and csrf validation (for stateless request)
            $userComponent->enableSession = false;
            $request->enableCsrfValidation = false;
            return $userComponent->login($user) ? $user : null;
        }
        return $this->handleFailure($response);
    }

    /**
     * @inheritdoc
     */
    public function handleFailure($response)
    {
        if ($response->format == 'html') {
            $user = $this->user ?: Yii::$app->getUser();
            return $user->loginRequired();
        }
        return parent::handleFailure($response);
    }

    /**
     * Get bearer token from header
     * @param Request $request
     * @return string|null
     */
    protected function getBearerTokenFromHeader($request)
    {
        $authHeader = $request->getHeaders()->get('Authorization');
        if ($authHeader && preg_match('/^Bearer\s+(.*?)$/', $authHeader, $matches)) {
            return $matches[1];
        }
        return null;
    }

    /**
     * Calculate token key for redis
     * @param string $token
     * @return string
     */
    protected function tokenKey($token)
    {
        return "api_auth:token:{$token}";
    }

    /**
     * Calculate user key for redis
     * @param int $userId
     * @return string
     */
    protected function userKey($userId)
    {
        return "api_auth:user:{$userId}";
    }

    /**
     * Parse redis data into [key => value] array
     * @param array $data
     * @return array
     */
    protected function parseRedisData($data)
    {
        $parsedData = [];
        for ($i=0; $i<count($data); $i+=2) {
            $parsedData[$data[$i]] = $data[$i+1];
        }
        return $parsedData;
    }

    /**
     * Check token and log user in
     * @param $token
     * @param UserComponent $userComponent
     * @return User|null
     */
    protected function checkToken($token, $userComponent)
    {
        // attempt to increase the num_uses field and check the result
        // if successful, we know that
        $tokenKey = $this->tokenKey($token);
        $data = $this->redis->executeCommand('HGETALL', [$tokenKey]);
        if (!$data) {
            return null;
        }

        /** @var User $user */
        // look up user
        $data = $this->parseRedisData($data);
        $user = $userComponent->identityClass;
        $user = $user::findIdentity($data['user_id']);
        if (!$user) {
            return null;
        }

        // update redis data
        $this->redis->executeCommand('HINCRBY', [$tokenKey, 'num_uses', 1]);
        if (!empty($data['ttl'])) {
            $this->redis->executeCommand('EXPIRE', [$tokenKey, $data['ttl']]);
        }

        // store token data and return user
        $this->tokenData = $data;
        return $user;
    }

    /**
     * Create token for specified user
     * @param User $user
     * @param int $ttl Expire time in seconds
     * @return string
     */
    public function createTokenForUser($user, $ttl = null)
    {
        // generate token and calculate expire time
        $token = Yii::$app->security->generateRandomString(32);
        if ($ttl === null) {
            $ttl = $this->defaultTtl;
        }

        // create redis hash for $tokenKey and set expiration
        $tokenKey = $this->tokenKey($token);
        $this->redis->executeCommand('HMSET', [$tokenKey, 'user_id', $user->id, 'ttl', $ttl]);
        if ($ttl) {
            $this->redis->executeCommand('EXPIRE', [$tokenKey, $ttl]);
        }

        // add token to redis set
        $userKey = $this->userKey($user->id);
        $this->redis->executeCommand('SADD', [$userKey, $token]);

        // return the token
        return $token;
    }

    /**
     * Remove token in header
     * @return bool
     */
    public function removeTokenFromHeader()
    {
        $request = $this->request ?: Yii::$app->getRequest();
        $token = $this->getBearerTokenFromHeader($request);
        $tokenKey = $this->tokenKey($token);

        // check for existence by getting user_id and then remove data
        $userId = $this->redis->executeCommand('HGET', [$tokenKey, 'user_id']);
        if ($userId) {
            $userKey = $this->userKey($userId);
            $this->redis->executeCommand('SREM', [$userKey, $token]);
            $this->redis->executeCommand('DEL', [$tokenKey]);
            return true;
        }

        return false;
    }

    /**
     * Get user from token in headers
     * @return User
     */
    public function getUserFromTokenInHeader()
    {
        $request = $this->request ?: Yii::$app->getRequest();
        $token = $this->getBearerTokenFromHeader($request);
        $tokenKey = $this->tokenKey($token);

        // check for existence by getting user_id and then remove data
        $userId = $this->redis->executeCommand('HGET', [$tokenKey, 'user_id']);
        if ($userId) {
            /** @var User $user */
            $userComponent = $this->user ?: Yii::$app->getUser();
            $user = $userComponent->identityClass;
            return $user::findIdentity($userId);
        }

        return null;
    }
}