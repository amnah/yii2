<?php

/** @var \yii\web\View $this */
/** @var string $content */
/** @var \app\models\User $user */

use yii\helpers\Html;

$user = Yii::$app->user->identity;
$request = Yii::$app->request;
$min = !empty($min) ? '.min' : '';

?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>

    <title><?= Html::encode($this->title) ?></title>

    <link href="/compiled/vendor<?= $min ?>.css" rel="stylesheet">
    <link href="/compiled/compiled<?= $min ?>.css" rel="stylesheet">
</head>
<body>
<?php $this->beginBody() ?>
<div>
    <nav class="navbar navbar-default navbar-static-top">
        <div class="container">
            <div class="navbar-header">

                <!-- Collapsed Hamburger -->
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                    <span class="sr-only">Toggle Navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                <!-- Branding Image -->
                <a class="navbar-brand" href="<?= url('/') ?>">
                    My Company
                </a>
            </div>

            <div class="collapse navbar-collapse" id="app-navbar-collapse">
                <!-- Left Side Of Navbar -->
                <ul class="nav navbar-nav">
                    &nbsp;
                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="nav navbar-nav navbar-right">
                    <li><a href="<?= url('/site/about') ?>">About</a></li>
                    <li><a href="<?= url('/site/contact') ?>">Contact</a></li>

                    <?php if (!$user): ?>
                        <li><a href="<?= url('/auth/login') ?>">Login</a></li>
                        <li><a href="<?= url('/auth/register') ?>">Register</a></li>
                    <?php else: ?>

                        <li><a href="<?= url('/user/profile') ?>">Profile</a></li>
                        <li>
                            <a href="<?= url('/logout') ?>" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                Logout (<?= $user->username ?>)
                            </a>
                            <form id="logout-form" action="<?= url('/auth/logout') ?>" method="POST" style="display: none;">
                                <input type="hidden" name="<?= $request->csrfParam ?>" value="<?= $request->csrfToken ?>">
                            </form>
                        </li>

                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- content -->
    <?= $content ?>
    <!-- end content -->
</div>

<!-- Scripts -->
<script src="/compiled/vendor<?= $min ?>.js"></script>
<!--
<script src="/compiled/compiled<?= $min ?>.js"></script>
-->

<?php if (isset($this->blocks['javascript'])): ?>
    <?= $this->blocks['javascript'] ?>
<?php endif; ?>

<?php $this->endBody() ?>
</body>
</html>