<?php

/** @var yii\web\View $this */
/** @var app\models\PasswordReset $passwordReset */

use yii\helpers\Html;

$this->title = 'Reset Password';

?>
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel <?= empty($passwordReset) ? 'panel-danger' : 'panel-default' ?>">
                <div class="panel-heading"><?= Html::encode($this->title) ?></div>
                <div class="panel-body">

                    <?php if (empty($passwordReset)): ?>

                        <p>Invalid token</p>

                    <?php else: ?>

                        <?= Html::beginForm('', 'post', ['class' => 'form-horizontal']) ?>

                        <?php $field = 'email'; ?>
                        <div class="form-group">
                            <?= Html::activeLabel($passwordReset->user, $field, ['class' => 'col-md-4 control-label']) ?>
                            <div class="col-md-6">
                                <?= Html::activeTextInput($passwordReset->user, $field, [
                                    'class' => 'form-control',
                                    'disabled' => true,
                                ]); ?>
                            </div>
                        </div>

                        <?php $field = 'password'; ?>
                        <div class="form-group <?= $passwordReset->user->hasErrors($field) ? 'has-error' : '' ?>">
                            <?= Html::activeLabel($passwordReset->user, $field, ['class' => 'col-md-4 control-label']) ?>
                            <div class="col-md-6">
                                <?= Html::activePasswordInput($passwordReset->user, $field, [
                                    'class' => 'form-control',
                                    'required' => true,
                                    'autofocus' => true,
                                ]); ?>
                                <span class="help-block">
                                    <strong><?= Html::error($passwordReset->user, $field) ?></strong>
                                </span>
                            </div>
                        </div>

                        <?php $field = 'confirm_password'; ?>
                        <div class="form-group <?= $passwordReset->user->hasErrors($field) ? 'has-error' : '' ?>">
                            <?= Html::activeLabel($passwordReset->user, $field, ['class' => 'col-md-4 control-label']) ?>
                            <div class="col-md-6">
                                <?= Html::activePasswordInput($passwordReset->user, $field, [
                                    'class' => 'form-control',
                                    'required' => true,
                                    'autofocus' => true,
                                ]); ?>
                                <span class="help-block">
                                    <strong><?= Html::error($passwordReset->user, $field) ?></strong>
                                </span>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-8 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">Reset Password</button>
                            </div>
                        </div>

                        <?= Html::endForm() ?>

                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>