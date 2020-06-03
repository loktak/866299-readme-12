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
        Пользователь <b><?= $author ?></b>только что опубликовал новую запись <a href="<?= $_SERVER['HTTP_HOST'] ?>/post.php?post_id=<?= $post_id ?>">«<b><?= $post_title ?></b>»</a><br>
        Посмотрите её на странице пользователя: <a style="font-weight:bold" href="<?= $_SERVER['HTTP_HOST'] ?>/profile.php?active_tab=posts&user_id=<?= $profile_id ?>"><?= $author ?></a>
    </p>
</body>

</html>