<main class="page__main page__main--publication">
    <div class="container">
        <h1 class="page__title page__title--publication"><?= anti_xss($post_info['title']) ?></h1>
        <section class="post-details">
            <h2 class="visually-hidden">Публикация</h2>
            <div class="post-details__wrapper post-<?= $post_info['icon_type'] ?>">
                <div class="post-details__main-block post post--details">
                    <?php if (!empty($post_info['original_id'])): ?>
                        <div class="post__author">
                            <a class="post__author-link"
                               href="profile.php?user_id=<?= $post_info['original_author_id'] ?>&active_tab=posts"
                               title="Автор">
                                <div class="post__avatar-wrapper post__avatar-wrapper--repost">
                                    <img class="post__author-avatar" width="60px"
                                         src="userpics/<?= anti_xss($post_info['original_author_avatar']) ?>"
                                         alt="Аватар пользователя">
                                </div>
                                <div class="post__info">
                                    <b class="post__author-name">Репост: <?= anti_xss($post_info['original_author_name']) ?></b>
                                    <?php $original_post_date = new DateTime($post_info['original_date']); ?>
                                    <time class="post__time" title="<?= $original_post_date->format('d.m.Y H:i') ?>"
                                          datetime="<?= $original_post_date->format('Y-m-d H:i:s') ?>"><?= time_ago($original_post_date) ?></time>
                                </div>
                            </a>
                        </div>
                    <?php endif ?>
                    <?= $post_content ?>
                    <div class="post__indicators">
                        <div class="post__buttons">
                            <a class="post__indicator post__indicator--likes button"
                               href="like.php?post_id=<?= $post_info['id'] ?>" title="Лайк">
                                <svg class="post__indicator-icon" width="20" height="17">
                                    <use xlink:href="#icon-heart"></use>
                                </svg>
                                <svg class="post__indicator-icon post__indicator-icon--like-active" width="20"
                                     height="17">
                                    <use xlink:href="#icon-heart-active"></use>
                                </svg>
                                <span><?= $post_info['likes'] ?></span>
                                <span class="visually-hidden">количество лайков</span>
                            </a>
                            <a class="post__indicator post__indicator--comments button" href="post.php#last-comment"
                               title="Комментарии">
                                <svg class="post__indicator-icon" width="19" height="17">
                                    <use xlink:href="#icon-comment"></use>
                                </svg>
                                <span><?= $post_info['comments_count'] ?></span>
                                <span class="visually-hidden">количество комментариев</span>
                            </a>
                            <a class="post__indicator post__indicator--repost button"
                               href="repost.php?post_id=<?= $post_info['id'] ?>" title="Репост">
                                <svg class="post__indicator-icon" width="19" height="17">
                                    <use xlink:href="#icon-repost"></use>
                                </svg>
                                <span><?= $post_info['reposts'] ?></span>
                                <span class="visually-hidden">количество репостов</span>
                            </a>
                        </div>
                        <span class="post__view"><?= $post_info['views'] ?></span>
                    </div>
                    <div class="comments">
                        <form class="comments__form form" method="post">
                            <div class="comments__my-avatar">
                                <img class="comments__picture" width="40px"
                                     src="userpics/<?= anti_xss($user_data['avatar']) ?>" alt="Аватар пользователя">
                            </div>
                            <div
                                class="form__input-section <?= (!empty($errors['comment']) ? "form__input-section--error" : "") ?>">
                                <textarea class="comments__textarea form__textarea form__input" name="comment"
                                          id="comment"
                                          placeholder="Ваш комментарий"><?= getPostValue('comment') ?></textarea>
                                <label class="visually-hidden">Ваш комментарий</label>
                                <button class="form__error-button button" type="button">!</button>
                                <div class="form__error-text">
                                    <h3 class="form__error-title">Ошибка валидации</h3>
                                    <p class="form__error-desc"><?= (!empty($errors['comment']) ? $errors['comment'] : "") ?></p>
                                </div>
                            </div>
                            <input type="hidden" value="<?= $post_info['id'] ?>" name="post_id" id="post_id">
                            <input type="hidden" value="<?= $post_info['user_id'] ?>" name="author_id" id="author_id">
                            <button class="comments__submit button button--green" type="submit">Отправить</button>
                        </form>
                        <div class="comments__list-wrapper">
                            <ul class="comments__list">
                                <a class="visually-hidden" name="last-comment">последний комментарий<a>
                                        <?php foreach ($comments as $comment): ?>
                                            <li class="comments__item user">
                                                <div class="comments__avatar">
                                                    <a class="user__avatar-link"
                                                       href="profile.php?user_id=<?= $comment['user_id'] ?>">
                                                        <img class="comments__picture" width="40px"
                                                             src="userpics/<?= anti_xss($comment['avatar']) ?>"
                                                             alt="Аватар пользователя">
                                                    </a>
                                                </div>
                                                <div class="comments__info">
                                                    <div class="comments__name-wrapper">
                                                        <a class="comments__user-name"
                                                           href="profile.php?user_id=<?= $comment['user_id'] ?>">
                                                            <span><?= anti_xss($comment['author']) ?></span>
                                                        </a>
                                                        <?php $comment_date = new DateTime($comment['comment_date']); ?>
                                                        <time class="comments__time"
                                                              title="<?= $comment_date->format('d.m.Y H:i') ?>"
                                                              datetime="<?= $comment_date->format('Y-m-d H:i:s') ?>"><?= time_ago($comment_date) ?></time>
                                                    </div>
                                                    <p class="comments__text">
                                                        <?= anti_xss($comment['content']) ?>
                                                    </p>
                                                </div>
                                            </li>
                                        <?php endforeach ?>
                            </ul>
                            <?php if ($hidden_comments_count !== null): ?>
                                <a class="comments__more-link"
                                   href="<?= $_SERVER['REQUEST_URI']."&show_comments=all" ?>">
                                    <span>Показать все комментарии</span>
                                    <sup class="comments__amount"><?= $hidden_comments_count ?></sup>
                                </a>
                            <?php endif ?>
                        </div>
                    </div>
                </div>
                <div class="post-details__user user">
                    <div class="post-details__user-info user__info">
                        <div class="post-details__avatar user__avatar">
                            <a class="post-details__avatar-link user__avatar-link" href="#">
                                <img class="post-details__picture user__picture" width="60px"
                                     src="userpics/<?= anti_xss($post_info['avatar']) ?>" alt="Аватар пользователя">
                            </a>
                        </div>
                        <div class="post-details__name-wrapper user__name-wrapper">
                            <a class="post-details__name user__name"
                               href="profile.php?active_tab=posts&user_id=<?= $post_info['user_id'] ?>">
                                <span><?= anti_xss($post_info['author_login']) ?></span>
                            </a>
                            <?php $user_registration_date = new DateTime($post_info['registration_date']); ?>
                            <time class="post-details__time user__time"
                                  title="<?= $user_registration_date->format('d.m.Y H:i') ?>"
                                  datetime="<?= $user_registration_date->format('Y-m-d H:i:s') ?>"><?= time_ago($user_registration_date,
                                    ' на сайте') ?></time>
                        </div>
                    </div>
                    <div class="post-details__rating user__rating">
                        <p class="post-details__rating-item user__rating-item user__rating-item--subscribers">
                            <span
                                class="post-details__rating-amount user__rating-amount"><?= $post_info['subscribers'] ?></span>
                            <span
                                class="post-details__rating-text user__rating-text"><?= plural_form($post_info['subscribers'],
                                    array('подписчик', 'подписчика', 'подписчиков')) ?></span>
                        </p>
                        <p class="post-details__rating-item user__rating-item user__rating-item--publications">
                            <span
                                class="post-details__rating-amount user__rating-amount"><?= $post_info['user_posts'] ?></span>
                            <span
                                class="post-details__rating-text user__rating-text"><?= plural_form($post_info['user_posts'],
                                    array('публикация', 'публикации', 'публикаций')) ?></span>
                        </p>
                    </div>
                    <div class="post-details__user-buttons user__buttons">
                        <?php if ((int)$post_info['is_subscribed'] === 1): ?>
                            <a class="profile__user-button user__button user__button--subscription button button--main button--quartz"
                               href="subscription.php?user_id=<?= $post_info['user_id'] ?>">Отписаться</a>
                            <a class="profile__user-button user__button user__button--writing button button--green"
                               href="messages.php?receiver_id=<?= $post_info['user_id'] ?>">Сообщение</a>
                        <?php elseif ($post_info['user_id'] === $user_data['id']): ?>
                            <a class="profile__user-button user__button user__button--subscription button button--main"
                               style="background-color:limegreen;" type="button">Это ваш профиль</a>
                        <?php else: ?>
                            <a class="profile__user-button user__button user__button--subscription button button--main"
                               href="subscription.php?user_id=<?= $post_info['user_id'] ?>">Подписаться</a>
                        <?php endif ?>
                    </div>
                </div>
            </div>
        </section>
    </div>
</main>
