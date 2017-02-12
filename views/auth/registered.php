<?php

/** @var yii\web\View $this */

use yii\helpers\Html;

$this->title = 'Registered';

?>
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading"><?= Html::encode($this->title) ?></div>
                <div class="panel-body">
                    <div class="alert alert-success">
                        <p>User <strong><?= $user->email ?></strong> registered - Please check your email to confirm your address.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>