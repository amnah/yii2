<?php

namespace app\components;

use Yii;
use yii\di\Instance;
use yii\filters\RateLimitInterface;
use yii\redis\Connection;
use yii\web\Request;
use app\models\User;

/**
 * Authentication for api
 * This class checks for auth status from 1) cookies or 2) redis
 */
class ApiAuth extends \yii\filters\auth\HttpBearerAuth implements RateLimitInterface
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
     * @var array Rate limit requests per second, eg, [120, 60] = 120 requests per 60 seconds
     */
    public $rateLimit = [120, 60];

    /**
     * @var string Token from header. This will not be set in stateful requests
     */
    protected $token = null;

    /**
     * @var array Token data from redis. This will not be set in stateful requests
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
        $token = $this->getTokenFromHeader($request);
        if (!$token) {
            return null;
        }

        // attempt to login via token
        $increaseNumUses = true;
        $user = $this->getUserByToken($token, $increaseNumUses);
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
    public function getTokenFromHeader($request)
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
     * Get user by token
     * @param string $token
     * @param bool $increaseNumUses
     * @return User|null
     */
    public function getUserByToken($token, $increaseNumUses = false)
    {
        // get hash data from redis
        $tokenKey = $this->tokenKey($token);
        $data = $this->redis->executeCommand('HGETALL', [$tokenKey]);
        if (!$data) {
            return null;
        }

        // look up user
        /** @var User $user */
        $data = $this->parseRedisData($data);
        $userComponent = $this->user ?: Yii::$app->getUser();
        $user = $userComponent->identityClass;
        $user = $user::findIdentity($data['user_id']);
        if (!$user) {
            return null;
        }

        // update redis data
        if (!empty($data['ttl'])) {
            $this->redis->executeCommand('EXPIRE', [$tokenKey, $data['ttl']]);
        }
        if ($increaseNumUses) {
            $this->redis->executeCommand('HINCRBY', [$tokenKey, 'num_uses', 1]);    
        }

        // store token data and return user
        $this->token = $token;
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
     * Remove token
     * @param string $token
     * @return bool
     */
    public function removeToken($token)
    {
        // check for existence by getting user_id and then remove data
        $tokenKey = $this->tokenKey($token);
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
     * @inheritdoc
     */
    public function getRateLimit($request, $action)
    {
        return $this->rateLimit;
    }

    /**
     * @inheritdoc
     */
    public function loadAllowance($request, $action)
    {
        $tokenData = $this->tokenData;
        $allowance = isset($tokenData['allowance']) ? $tokenData['allowance'] : 0;
        $allowanceUpdatedAt = isset($tokenData['allowance_updated_at']) ? $tokenData['allowance_updated_at'] : 0;
        return [$allowance, $allowanceUpdatedAt];
    }

    /**
     * @inheritdoc
     */
    public function saveAllowance($request, $action, $allowance, $timestamp)
    {
        // update allowance only if we have a token (stateless requests only)
        if ($this->token) {
            $tokenKey = $this->tokenKey($this->token);
            $this->redis->executeCommand('HMSET', [$tokenKey, 'allowance', $allowance, 'allowance_updated_at', $timestamp]);
        }
    }
}