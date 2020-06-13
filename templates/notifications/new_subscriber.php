<p>
    Здравствуйте, <b><?= anti_xss($recipient['name']) ?></b>.<br>
    На вас подписался новый пользователь <b><?= anti_xss($subscriber) ?></b><br>
    Вот ссылка на его профиль: <a style="font-weight:bold"
                                  href="<?= $_SERVER['HTTP_HOST'] ?>/profile.php?active_tab=posts&user_id=<?= $subscriber_id ?>"><?= anti_xss($subscriber) ?></a>
</p>
