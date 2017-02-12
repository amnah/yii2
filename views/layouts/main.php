<?php

/** @var \yii\web\View $this */
/** @var string $content */
/** @var \app\models\User $user */

use yii\helpers\Html;

$user = Yii::$app->user->identity;
$request = Yii::$app->request;

?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>

    <title><?= Html::encode($this->title) ?></title>

    <link href="/compiled/vendor.css" rel="stylesheet">
    <link href="/compiled/compiled.css" rel="stylesheet">
</head>
<body>
<?php $this->beginBody() ?>
<div id="app">
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
                    My company
                </a>
            </div>

            <div class="collapse navbar-collapse" id="app-navbar-collapse">
                <!-- Left Side Of Navbar -->
                <ul class="nav navbar-nav">
                    &nbsp;
                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="nav navbar-nav navbar-right">
                    <!-- Authentication Links -->
                    <?php if (!$user): ?>
                        <li><a href="<?= url('/auth/login') ?>">Login</a></li>
                        <li><a href="<?= url('/auth/register') ?>">Register</a></li>
                    <?php else: ?>
                        <li class="dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                <?= $user->username ?> <span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu" role="menu">
                                <li><a href="<?= url('/home') ?>">Home</a></li>
                                <li>
                                    <a href="<?= url('/logout') ?>" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        Logout
                                    </a>
                                    <form id="logout-form" action="<?= url('/auth/logout') ?>" method="POST" style="display: none;">
                                        <input type="hidden" name="<?= $request->csrfParam ?>" value="<?= $request->csrfToken ?>">
                                    </form>
                                </li>
                            </ul>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <?= $content ?>
</div>

<!-- Scripts -->
<script src="/compiled/vendor.js"></script>
<script src="/compiled/compiled.js"></script>

<?php $this->endBody() ?>
</body>
</html>