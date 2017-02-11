<?php

/** @var yii\web\View $this */
/** @var yii\base\DynamicModel $model */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Login';

?>
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Login</div>
                <div class="panel-body">

                    <?= Html::beginForm('', 'post', ['class' => 'form-horizontal']) ?>

                    <div class="form-group <?= $model->hasErrors('email') ? 'has-error' : '' ?>">
                        <?= Html::activeLabel($model, 'email', ['class' => 'col-md-4 control-label']) ?>

                        <div class="col-md-6">
                            <?= Html::activeTextInput($model, 'email', [
                                'class' => 'form-control',
                                'required' => true,
                                'autofocus' => true,
                            ]); ?>

                            <?php if ($model->hasErrors('email')): ?>
                            <span class="help-block">
                                <strong><?= Html::error($model, 'email') ?></strong>
                            </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-group <?= $model->hasErrors('password') ? 'has-error' : '' ?>">
                        <?= Html::activeLabel($model, 'password', ['class' => 'col-md-4 control-label']) ?>

                        <div class="col-md-6">
                            <?= Html::activePasswordInput($model, 'password', [
                                'class' => 'form-control',
                                'required' => true,
                            ]); ?>

                            <?php if ($model->hasErrors('password')): ?>
                            <span class="help-block">
                                <strong><?= Html::error($model, 'password') ?></strong>
                            </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6 col-md-offset-4">
                            <div class="checkbox">
                                <?= Html::activeCheckbox($model, 'rememberMe') ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-8 col-md-offset-4">
                            <button type="submit" class="btn btn-primary">
                                Login
                            </button>

                            <a class="btn btn-link" href="<?= Url::to('/auth/forgot') ?>">
                                Forgot Your Password?
                            </a>
                        </div>
                    </div>

                    <?= Html::endForm() ?>

                </div>
            </div>
        </div>
    </div>
</div>