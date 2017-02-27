<?php

/** @var yii\web\View $this */

$this->title = 'My Yii Application';

?>
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Welcome!</div>

                <div class="panel-body">

                    <?php if (Yii::$app->session->has('status')): ?>
                    <div class="alert alert-success">
                        <?= Yii::$app->session->getFlash('status') ?>
                    </div>
                    <?php endif; ?>

                    <?php if (Yii::$app->user->id): ?>
                        <p>Logged in as <?= Yii::$app->user->identity->email ?></p>
                    <?php else: ?>
                        <p><a href="<?= url('/auth/login') ?>">Login</a></p>
                        <p><a href="<?= url('/auth/forgot') ?>">Forgot</a></p>
                        <p><a href="<?= url('/auth/register') ?>">Register</a></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
