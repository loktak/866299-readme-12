<h1 class="visually-hidden">Профиль</h1>
<div class="profile profile--default">
    <div class="profile__user-wrapper">
        <div class="profile__user user container">
            <div class="profile__user-info user__info">
                <div class="profile__avatar user__avatar">
                    <img class="profile__picture user__picture" width="100px" 
                    src="userpics/<?= anti_xss($profile_info['avatar']) ?>" alt="Аватар пользователя">
                </div>
                <div class="profile__name-wrapper user__name-wrapper">
                    <span class="profile__name user__name">
                        <?= get_profile_name_with_br(anti_xss($profile_info['login'])) ?></span>
                    <?php $profile__registration_date = new DateTime($profile_info['registration_date']); ?>
                    <time class="profile__user-time user__time"
                    title="<?= $profile__registration_date->format('d.m.Y H:i') ?>" 
                    datetime="<?= $profile__registration_date->format('Y-m-d H:i:s') ?>">
                        <?= time_ago($profile__registration_date, " на сайте") ?></time>
                </div>
            </div>
            <div class="profile__rating user__rating">
                <p class="profile__rating-item user__rating-item user__rating-item--publications">
                    <span class="user__rating-amount"><?= $profile_info['user_posts'] ?></span>
                    <span class="profile__rating-text user__rating-text">
                        <?= plural_form(
                            $profile_info['user_posts'],
                            array('публикация', 'публикации', 'публикаций')
                        ) ?></span>
                </p>
                <p class="profile__rating-item user__rating-item user__rating-item--subscribers">
                    <span class="user__rating-amount"><?= $profile_info['user_subs'] ?></span>
                    <span class="profile__rating-text user__rating-text">
                        <?= plural_form(
                            $profile_info['user_subs'],
                            array('подписчик', 'подписчика', 'подписчиков')
                        ) ?></span>
                </p>
            </div>
            <div class="profile__user-buttons user__buttons">
                <?php if ((int) $profile_info['is_subscribed'] === 1) : ?>
                    <a class="profile__user-button user__button 
                    user__button--subscription button button--main button--quartz"
                    href="subscription.php?user_id=<?= $profile_info['id'] ?>">Отписаться</a>
                    <a class="profile__user-button user__button user__button--writing button button--green"
                    href="messages.php?receiver_id=<?= $profile_info['id'] ?>">Сообщение</a>
                <?php elseif ($profile_info['id'] === $user_data['id']) : ?>
                    <a class="profile__user-button user__button user__button--subscription button button--main"
                    style="background-color:limegreen;" type="button">Это ваш профиль</a>
                <?php else : ?>
                    <a class="profile__user-button user__button user__button--subscription button button--main"
                    href="subscription.php?user_id=<?= $profile_info['id'] ?>">Подписаться</a>
                <?php endif ?>
            </div>
        </div>
    </div>
    <div class="profile__tabs-wrapper tabs">
        <div class="container">
            <div class="profile__tabs filters">
                <b class="profile__tabs-caption filters__caption">Показать:</b>
                <ul class="profile__tabs-list filters__list tabs__list">
                    <li class="profile__tabs-item filters__item">
                        <a class="profile__tabs-link filters__button tabs__item 
                        <?= ($active_tab === 'posts') ? 'filters__button--active tabs__item--active button' : ""; ?>"
                        href="profile.php?active_tab=posts">Посты</a>
                    </li>
                    <li class="profile__tabs-item filters__item">
                        <a class="profile__tabs-link filters__button tabs__item button 
                        <?= ($active_tab === 'likes') ? 'filters__button--active tabs__item--active button' : ""; ?>"
                        href="profile.php?active_tab=likes">Лайки</a>
                    </li>
                    <li class="profile__tabs-item filters__item">
                        <a class="profile__tabs-link filters__button tabs__item button 
                        <?= ($active_tab === 'subscriptions') ?
                        'filters__button--active tabs__item--active button' : ""; ?>"
                        href="profile.php?active_tab=subscriptions">Подписки</a>
                    </li>
                </ul>
            </div>
            <div class="profile__tab-content">
                <?= $profile_tab ?>
            </div>
        </div>
    </div>
</div>