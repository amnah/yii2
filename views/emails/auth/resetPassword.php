<?php

/* @var $this \yii\web\View view component instance */
/* @var $message \yii\mail\MessageInterface the message being composed */
/* @var $passwordReset \app\models\PasswordReset */

$resetUrl = url(['auth/reset', 'token' => $passwordReset->token], true);

?>
<p>Hello <?= $passwordReset->user->email ?>.</p>
<p>Click here to reset your password:</p>

<p><a href="<?= $resetUrl ?>">Reset password</a></p>
<p><?= $resetUrl ?></p>