<?php

/** @var yii\web\View $this */

use yii\helpers\Url;

$this->title = 'My Yii Application';

?>
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Welcome!</div>

                <div class="panel-body">
                    <?php if (Yii::$app->user->id): ?>
                        <p><a href="<?= Url::to('/user') ?>">User home</a></p>
                    <?php else: ?>
                        <p><a href="<?= Url::to('/auth/login') ?>">Login</a></p>
                        <p><a href="<?= Url::to('/auth/forgot') ?>">Forgot</a></p>
                        <p><a href="<?= Url::to('/auth/register') ?>">Register</a></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<example></example>
