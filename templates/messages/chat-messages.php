<ul class="messages__list tabs__content tabs__content--active">
    <?php foreach ($messages as $message) : ?>
        <li class="messages__item <?= ((int)$message['sender_id'] === (int)$user_data['id']) ?
        'messages__item--my' : "" ?>">
            <div class="messages__info-wrapper">
                <div class="messages__item-avatar">
                    <a class="messages__author-link"
                       href="profile.php?active_tab=posts&user_id=<?= $message['sender_id'] ?>">
                        <img class="messages__avatar" src="userpics/<?= anti_xss($message['sender_avatar']) ?>"
                             alt="Аватар пользователя">
                    </a>
                </div>
                <div class="messages__item-info">
                    <a class="messages__author"
                       href="profile.php?active_tab=posts&user_id=<?= $message['sender_id'] ?>">
                        <?= anti_xss($message['sender_name']) ?>
                    </a>
                    <?php $message_date = new DateTime($message['date']); ?>
                    <time class="messages__time" title="<?= $message_date->format('d.m.Y H:i') ?>"
                          datetime="<?= $message_date->format('Y-m-d H:i:s') ?>">
                        <?= time_ago($message_date) ?>
                    </time>
                </div>
            </div>
            <p class="messages__text">
                <?= anti_xss($message['content']) ?>
            </p>
        </li>
    <?php endforeach ?>
    <a name="message_anchor" class="visuaaly-hidden"></a>
</ul>
