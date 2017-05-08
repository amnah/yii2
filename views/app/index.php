<?php

/** @var \yii\web\View $this */

$user = 'null'; // set as string for passing into javascript
if (Yii::$app->user->id) {
    $user = json_encode(Yii::$app->user->identity->toArray());
}
?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>My Company</title>

    <link href="<?= assetUrl("/compiled/vendor.css") ?>" rel="stylesheet">
    <link href="<?= assetUrl("/compiled/compiled.css") ?>" rel="stylesheet">
</head>
<body>
<?php $this->beginBody() ?>
<div id="app"></div>

<!-- Config -->
<script type="text/javascript">
    // comment all of these out to use the api authentication
    window.AppConfig = {
        apiUrl: '/v1/',
        csrf: '<?= Yii::$app->request->csrfToken ?>',
    };
    window.User = <?= $user ?>;
</script>

<!-- Scripts -->
<script src="<?= assetUrl("/compiled/vendor.js") ?>"></script>
<script src="<?= assetUrl("/compiled/compiled.js") ?>"></script>

<?php $this->endBody() ?>
</body>
</html>