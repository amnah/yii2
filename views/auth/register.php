<?php

/** @var yii\web\View $this */
/** @var app\models\User $user */

use yii\helpers\Html;

$this->title = 'Register';

?>
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading"><?= Html::encode($this->title) ?></div>
                <div class="panel-body">

                    <?= Html::beginForm('', 'post', ['class' => 'form-horizontal']) ?>

                    <?php $field = 'email'; ?>
                    <div class="form-group <?= $user->hasErrors($field) ? 'has-error' : '' ?>">
                        <?= Html::activeLabel($user, $field, ['class' => 'col-md-4 control-label']) ?>
                        <div class="col-md-6">
                            <?= Html::activeTextInput($user, $field, [
                                'class' => 'form-control',
                                'required' => true,
                                'type' => 'email',
                                'autofocus' => true,
                            ]); ?>
                            <span class="help-block">
                                <strong><?= Html::error($user, $field) ?></strong>
                            </span>
                        </div>
                    </div>

                    <?php $field = 'username'; ?>
                    <div class="form-group <?= $user->hasErrors($field) ? 'has-error' : '' ?>">
                        <?= Html::activeLabel($user, $field, ['class' => 'col-md-4 control-label']) ?>
                        <div class="col-md-6">
                            <?= Html::activeTextInput($user, $field, [
                                'class' => 'form-control',
                                'required' => true,
                            ]); ?>
                            <span class="help-block">
                                <strong><?= Html::error($user, $field) ?></strong>
                            </span>
                        </div>
                    </div>

                    <?php $field = 'password'; ?>
                    <div class="form-group <?= $user->hasErrors($field) ? 'has-error' : '' ?>">
                        <?= Html::activeLabel($user, $field, ['class' => 'col-md-4 control-label']) ?>
                        <div class="col-md-6">
                            <?= Html::activePasswordInput($user, $field, [
                                'class' => 'form-control',
                                'required' => true,
                            ]); ?>
                            <span class="help-block">
                                <strong><?= Html::error($user, $field) ?></strong>
                            </span>
                        </div>
                    </div>

                    <?php $field = 'confirm_password'; ?>
                    <div class="form-group <?= $user->hasErrors($field) ? 'has-error' : '' ?>">
                        <?= Html::activeLabel($user, $field, ['class' => 'col-md-4 control-label']) ?>
                        <div class="col-md-6">
                            <?= Html::activePasswordInput($user, $field, [
                                'class' => 'form-control',
                                'required' => true,
                            ]); ?>
                            <span class="help-block">
                                <strong><?= Html::error($user, $field) ?></strong>
                            </span>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-8 col-md-offset-4">
                            <button type="submit" class="btn btn-primary">Register</button>
                        </div>
                    </div>

                    <?= Html::endForm() ?>

                </div>
            </div>
        </div>
    </div>
</div>