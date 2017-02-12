<?php

/** @var yii\web\View $this */
/** @var yii\base\DynamicModel $model */

use yii\helpers\Html;

$this->title = 'Register';

?>
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Register</div>
                <div class="panel-body">

                    <?= Html::beginForm('', 'post', ['class' => 'form-horizontal']) ?>

                    <?php $field = 'email'; ?>
                    <div class="form-group <?= $model->hasErrors($field) ? 'has-error' : '' ?>">
                        <?= Html::activeLabel($model, $field, ['class' => 'col-md-4 control-label']) ?>

                        <div class="col-md-6">
                            <?= Html::activeTextInput($model, $field, [
                                'class' => 'form-control',
                                'required' => true,
                                'type' => 'email',
                                'autofocus' => true,
                            ]); ?>

                            <?php if ($model->hasErrors($field)): ?>
                                <span class="help-block">
                                <strong><?= Html::error($model, $field) ?></strong>
                            </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php $field = 'username'; ?>
                    <div class="form-group <?= $model->hasErrors($field) ? 'has-error' : '' ?>">
                        <?= Html::activeLabel($model, $field, ['class' => 'col-md-4 control-label']) ?>

                        <div class="col-md-6">
                            <?= Html::activeTextInput($model, $field, [
                                'class' => 'form-control',
                                'required' => true,
                            ]); ?>

                            <?php if ($model->hasErrors($field)): ?>
                                <span class="help-block">
                                <strong><?= Html::error($model, $field) ?></strong>
                            </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php $field = 'password'; ?>
                    <div class="form-group <?= $model->hasErrors($field) ? 'has-error' : '' ?>">
                        <?= Html::activeLabel($model, $field, ['class' => 'col-md-4 control-label']) ?>

                        <div class="col-md-6">
                            <?= Html::activePasswordInput($model, $field, [
                                'class' => 'form-control',
                                'required' => true,
                            ]); ?>

                            <?php if ($model->hasErrors($field)): ?>
                                <span class="help-block">
                                <strong><?= Html::error($model, $field) ?></strong>
                            </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php $field = 'confirm_password'; ?>
                    <div class="form-group <?= $model->hasErrors($field) ? 'has-error' : '' ?>">
                        <?= Html::activeLabel($model, $field, ['class' => 'col-md-4 control-label']) ?>

                        <div class="col-md-6">
                            <?= Html::activePasswordInput($model, $field, [
                                'class' => 'form-control',
                                'required' => true,
                            ]); ?>

                            <?php if ($model->hasErrors($field)): ?>
                                <span class="help-block">
                                <strong><?= Html::error($model, $field) ?></strong>
                            </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-8 col-md-offset-4">
                            <button type="submit" class="btn btn-primary">
                                Register
                            </button>
                        </div>
                    </div>

                    <?= Html::endForm() ?>

                </div>
            </div>
        </div>
    </div>
</div>