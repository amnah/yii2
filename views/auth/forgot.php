<?php

/** @var yii\web\View $this */
/** @var yii\base\DynamicModel $model */
/** @var app\models\User $user */

use yii\helpers\Html;

$this->title = 'Forgot Password';

?>
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading"><?= Html::encode($this->title) ?></div>
                <div class="panel-body">

                    <?php if ($user): ?>

                        <div class="alert alert-success">
                            We have e-mailed your password reset link to  <strong><?= $user->email ?></strong>
                        </div>

                    <?php else: ?>

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

                        <div class="form-group">
                            <div class="col-md-8 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Send Password Reset Link
                                </button>
                            </div>
                        </div>

                        <?= Html::endForm() ?>

                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>