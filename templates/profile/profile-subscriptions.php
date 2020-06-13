<section class="profile__subscriptions tabs__content tabs__content--active">
    <h2 class="visually-hidden">Подриски</h2>
    <ul class="profile__subscriptions-list">
        <?php foreach ($subsribers

        as $subsriber): ?>
        <li class="post-mini post-mini--photo post user">
            <div class="post-mini__user-info user__info">
                <div class="post-mini__avatar user__avatar">
                    <a class="user__avatar-link" href="profile.php?user_id=<?= $subsriber['id'] ?>&active_tab=posts">
                        <img class="post-mini__picture user__picture"
                             src="userpics/<?= anti_xss($subsriber['avatar']) ?>" alt="Аватар пользователя">
                    </a>
                </div>
                <div class="post-mini__name-wrapper user__name-wrapper">
                    <a class="post-mini__name user__name"
                       href="profile.php?user_id=<?= $subsriber['id'] ?>&active_tab=posts">
                        <span><?= anti_xss($subsriber['login']) ?></span>
                    </a>
                    <?php $user_registration_date = new DateTime($subsriber['date']); ?>
                    <time class="post-mini__time user__additional"
                          title="<?= $user_registration_date->format('d.m.Y H:i') ?>"
                          datetime="<?= $user_registration_date->format('Y-m-d H:i:s') ?>"><?= time_ago($user_registration_date,
                            " на сайте") ?></time>
                </div>
            </div>
            <div class="post-mini__rating user__rating">
                <p class="post-mini__rating-item user__rating-item user__rating-item--publications">
                    <span class="post-mini__rating-amount user__rating-amount"><?= $subsriber['posts'] ?></span>
                    <span class="post-mini__rating-text user__rating-text"><?= plural_form($subsriber['posts'],
                            array('публикация', 'публикации', 'публикаций')) ?></span>
                </p>
                <p class="post-mini__rating-item user__rating-item user__rating-item--subscribers">
                    <span class="post-mini__rating-amount user__rating-amount"><?= $subsriber['subscribers'] ?></span>
                    <span class="post-mini__rating-text user__rating-text"><?= plural_form($subsriber['subscribers'],
                            array('подписчик', 'подписчика', 'подписчиков')) ?></span>
                </p>
            </div>
            <div class="post-mini__user-buttons user__buttons">
                <?php if ($subsriber['id'] === $user_data['id']): ?>
                    <a class="post-mini__user-button user__button user__button--subscription button button--main"
                       style="background-color:limegreen;">ЭТО ВЫ</a>
                <?php else: ?>
                <a href="subscription.php?user_id=<?= $subsriber['id'] ?>"
                   class="post-mini__user-button user__button user__button--subscription button button--main <?= (int)$subsriber['is_subscribed'] === 1 ? 'button--quartz">Отписаться' : '">Подписаться'; ?></a>
                <?php endif ?>
            </div>
            </li>
        <?php endforeach ?>
    </ul>
</section>
