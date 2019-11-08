<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Support map.">
    <script src="https://api-maps.yandex.ru/2.1/?apikey=<ваш API-ключ>&lang=ru_RU" type="text/javascript">
    </script>
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,400,700' rel='stylesheet' type='text/css'>
    <!--
    <link rel="stylesheet" href="../../web/css/spectre/spectre.min.css">
    <link rel="stylesheet" href="../../web/css/spectre/spectre-exp.min.css">
    <link rel="stylesheet" href="../../web/css/spectre/spectre-icons.min.css">
    -->
        <title><?= $this->title ?></title>
</head>
<body>
<?= $content ?>
</body>
</html>