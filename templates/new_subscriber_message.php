<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
</head>

<body>
    <p>
    Здравствуйте, <b><?= $recipient['name'] ?></b>.<br>
    На вас подписался новый пользователь <b><?= $subscriber ?></b><br>
    Вот ссылка на его профиль: <a style="font-weight:bold" href="<?= $_SERVER['HTTP_HOST'] ?>/profile.php?active_tab=posts&user_id=<?= $subscriber_id ?>"><?= $subscriber ?></a>
    </p>
</body>

</html>