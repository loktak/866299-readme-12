    <p>
        Здравствуйте, <b><?= $recipient['name'] ?></b>.<br>
        Пользователь <b><?= $author ?></b>только что опубликовал новую запись <a href="<?= $_SERVER['HTTP_HOST'] ?>/post.php?post_id=<?= $post_id ?>">«<b><?= $post_title ?></b>»</a><br>
        Посмотрите её на странице пользователя: <a style="font-weight:bold" href="<?= $_SERVER['HTTP_HOST'] ?>/profile.php?active_tab=posts&user_id=<?= $profile_id ?>"><?= $author ?></a>
    </p>
