<?php

/* @var $this \yii\web\View view component instance */
/* @var $message \yii\mail\MessageInterface the message being composed */
/* @var $user \app\models\User */

$confirmUrl = url(['auth/confirm', 'email' => $user->email, 'confirmation' => $user->confirmation], true);

?>
<p>Hello <?= $user->email ?>.</p>
<p>Please confirm your email address.</p>

<p><a href="<?= $confirmUrl ?>">Confirm email address</a></p>
<p><?= $confirmUrl ?></p>