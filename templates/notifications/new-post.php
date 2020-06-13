<p>
    Здравствуйте, <b><?= anti_xss($recipient['name']) ?></b>.<br>
    Пользователь <b><?= anti_xss($author) ?></b>только что опубликовал новую запись <a
        href="<?= $_SERVER['HTTP_HOST'] ?>/post.php?post_id=<?= $post_id ?>">«<b><?= anti_xss($post_title) ?></b>»</a><br>
    Посмотрите её на странице пользователя: <a style="font-weight:bold"
                                               href="<?= $_SERVER['HTTP_HOST'] ?>/profile.php?active_tab=posts&user_id=<?= $profile_id ?>"><?= anti_xss($author) ?></a>
</p>
