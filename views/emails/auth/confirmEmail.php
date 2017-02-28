<?php

/* @var $this \yii\web\View view component instance */
/* @var $message \yii\mail\MessageInterface the message being composed */
/* @var $user \app\models\User */
/* @var $confirmUrl string */

?>
<p>Hello <?= $user->email ?>.</p>
<p>Please confirm your email address.</p>

<p><a href="<?= $confirmUrl ?>">Confirm email address</a></p>
<p><?= $confirmUrl ?></p>