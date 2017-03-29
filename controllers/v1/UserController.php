<?php

namespace app\controllers\v1;

use Yii;
use app\components\BaseController;
use app\models\User;

class UserController extends BaseController
{
    /**
     * Profile
     */
    public function actionProfile()
    {
        /** @var User $user */
        $user = Yii::$app->user->identity;
        $user->setScenario(User::SCENARIO_PROFILE);
        if ($user->loadPostAndSave()) {
            return ['success' => true, 'user' => $user];
        }

        return ['errors' => $user->errors, 'user' => $user];
    }
}
