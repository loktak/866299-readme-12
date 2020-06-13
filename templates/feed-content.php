<div class="container">
    <h1 class="page__title page__title--feed">Моя лента</h1>
</div>
<div class="page__main-wrapper container">
    <section class="feed">
        <h2 class="visually-hidden">Лента</h2>
        <div class="feed__main-wrapper">
            <div class="feed__wrapper">
                <?php foreach ($posts as $post): ?>
                    <article class="feed__post post post-<?= $post['type'] ?>">
                        <header class="post__header post__author">
                            <a class="post__author-link"
                               href="profile.php?user_id=<?= $post['user_id'] ?>&active_tab=posts" title="Автор">
                                <div class="post__avatar-wrapper">
                                    <img class="post__author-avatar" src="userpics/<?= anti_xss($post['avatar']) ?>"
                                         alt="Аватар пользователя" width="60" height="60">
                                </div>
                                <div class="post__info">
                                    <b class="post__author-name"><?= anti_xss($post['author_login']) ?></b>
                                    <span class="post__time"><?= time_ago(new DateTime($post['post_date'])) ?></span>
                                </div>
                            </a>
                        </header>
                        <div class="post__main">
                            <?php if ($post['type'] === 'photo' || $post['type'] === 'text'): ?>
                                <h2><a href="post.php?post_id=<?= $post['id'] ?>"><?= anti_xss($post['title']) ?></a>
                                </h2>
                            <?php endif ?>
                            <?php if ($post['type'] === 'photo'): ?>
                                <div class="post-photo__image-wrapper">
                                    <img src="uploads/<?= anti_xss($post['img']) ?>" alt="Фото от пользователя"
                                         width="760" height="396">
                                </div>
                            <?php elseif ($post['type'] === 'video'): ?>
                            <div class="post-video__block">
                                <div class="post-video__preview">
                                    <?= embed_youtube_video(anti_xss($post['video'])); ?>
                                </div>
                                <?php elseif ($post['type'] === 'text'): ?>
                                    <?= crop_text(anti_xss($post['content_text']), $post['id']) ?>
                                <?php elseif ($post['type'] === 'quote'): ?>
                                    <blockquote>
                                        <p><?= anti_xss($post['content_text']) ?></p>
                                        <cite><?= anti_xss($post['quote_author']) ?></cite>
                                    </blockquote>
                                <?php elseif ($post['type'] === 'link'): ?>
                                    <div class="post-link__wrapper">
                                        <a class="post-link__external" href="<?= anti_xss($post['link']) ?>"
                                           target="_blank" title="Перейти по ссылке">
                                            <div class="post-link__icon-wrapper">
                                                <img
                                                    src="https://www.google.com/s2/favicons?domain=<?= anti_xss($post['link']) ?>"
                                                    alt="Иконка">
                                            </div>
                                            <div class="post-link__info">
                                                <h3><?= anti_xss($post['title']) ?></h3>
                                                <p><?= get_link_title($post['link']) ?></p>
                                                <span><?= mb_strimwidth(anti_xss($post['link']), 0, 55, "...") ?></span>
                                            </div>
                                            <svg class="post-link__arrow" width="11" height="16">
                                                <use xlink:href="#icon-arrow-right-ad"></use>
                                            </svg>
                                        </a>
                                    </div>
                                <?php endif ?>
                            </div>
                            <footer class="post__footer post__indicators">
                                <div class="post__buttons">
                                    <a class="post__indicator post__indicator--likes button"
                                       href="like.php?post_id=<?= $post['id'] ?>" title="Лайк">
                                        <svg class="post__indicator-icon" width="20" height="17">
                                            <use xlink:href="#icon-heart"></use>
                                        </svg>
                                        <svg class="post__indicator-icon post__indicator-icon--like-active" width="20"
                                             height="17">
                                            <use xlink:href="#icon-heart-active"></use>
                                        </svg>
                                        <span><?= $post['likes'] ?></span>
                                        <span class="visually-hidden">количество лайков</span>
                                    </a>
                                    <a class="post__indicator post__indicator--comments button"
                                       href="post.php?post_id=<?= $post['id'] ?>#last-comment" title="Комментарии">
                                        <svg class="post__indicator-icon" width="19" height="17">
                                            <use xlink:href="#icon-comment"></use>
                                        </svg>
                                        <span><?= $post['comments_count'] ?></span>
                                        <span class="visually-hidden">количество комментариев</span>
                                    </a>
                                    <a class="post__indicator post__indicator--repost button"
                                       href="repost.php?post_id=<?= $post['id'] ?>" title="Репост">
                                        <svg class="post__indicator-icon" width="19" height="17">
                                            <use xlink:href="#icon-repost"></use>
                                        </svg>
                                        <span><?= $post['reposts'] ?></span>
                                        <span class="visually-hidden">количество репостов</span>
                                    </a>
                                </div>
                                <ul class="post__tags">
                                    <?php foreach ($hashtags[$post['id']] as $key => $value): ?>
                                        <li>
                                            <a href="search.php?search_request=%23<?= $value['title'] ?>">#<?= $value['title'] ?></a>
                                        </li>
                                    <?php endforeach ?>
                                </ul>
                            </footer>
                    </article>
                <?php endforeach ?>
            </div>
        </div>
        <ul class="feed__filters filters">
            <li class="feed__filters-item filters__item">
                <a class="filters__button <?= ($page_parameters['type'] === 'all') ? "filters__button--active" : "" ?>"
                   href="feed.php?type=all">
                    <span>Все</span>
                </a>
            </li>
            <?php foreach ($types as $type): ?>
                <li class="feed__filters-item filters__item">
                    <a class="filters__button filters__button--<?= ($type['icon_type']) ?> button <?= ($page_parameters['type'] === $type['icon_type']) ? 'filters__button--active' : "" ?>"
                       href="feed.php?type=<?= $type['icon_type'] ?>">
                        <span class="visually-hidden"><?= $type['type_name'] ?></span>
                        <svg class="filters__icon" width="22" height="18">
                            <use xlink:href="#icon-filter-<?= $type['icon_type'] ?>"></use>
                        </svg>
                    </a>
                </li>
            <?php endforeach ?>
        </ul>
    </section>
    <aside class="promo">
        <article class="promo__block promo__block--barbershop">
            <h2 class="visually-hidden">Рекламный блок</h2>
            <p class="promo__text">
                Все еще сидишь на окладе в офисе? Открой свой барбершоп по нашей франшизе!
            </p>
            <a class="promo__link" href="#">
                Подробнее
            </a>
        </article>
        <article class="promo__block promo__block--technomart">
            <h2 class="visually-hidden">Рекламный блок</h2>
            <p class="promo__text">
                Товары будущего уже сегодня в онлайн-сторе Техномарт!
            </p>
            <a class="promo__link" href="#">
                Перейти в магазин
            </a>
        </article>
        <article class="promo__block">
            <h2 class="visually-hidden">Рекламный блок</h2>
            <p class="promo__text">
                Здесь<br> могла быть<br> ваша реклама
            </p>
            <a class="promo__link" href="#">
                Разместить
            </a>
        </article>
    </aside>
</div>
