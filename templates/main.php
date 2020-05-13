<div class="container">
    <h1 class="page__title page__title--popular">Популярное</h1>
</div>
<div class="popular container">
    <div class="popular__filters-wrapper">
        <div class="popular__sorting sorting">
            <b class="popular__sorting-caption sorting__caption">Сортировка:</b>
            <ul class="popular__sorting-list sorting__list">
                <li class="sorting__item sorting__item--popular">
                    <a class="sorting__link <?= ($sorting_parameters['sort_value'] === 'views') ? 'sorting__link--active' : ""; ?> <?= ($sorting_parameters['sorting'] === 'ASC') ? 'sorting__link--reverse' : ""; ?>" href="popular-cookie.php?sort_value=views&sorting=<?= ($sorting_parameters['sorting'] === 'DESC') ? "ASC" : "DESC" ?>">
                        <span>Популярность</span>
                        <svg class="sorting__icon" width="10" height="12">
                            <use xlink:href="#icon-sort"></use>
                        </svg>
                    </a>
                </li>
                <li class="sorting__item">
                    <a class="sorting__link <?= ($sorting_parameters['sort_value'] === 'likes') ? 'sorting__link--active' : ""; ?> <?= ($sorting_parameters['sorting'] === 'ASC') ? 'sorting__link--reverse' : ""; ?>" 
                    href="popular-cookie.php?sort_value=likes&sorting=<?= ($sorting_parameters['sorting'] === 'DESC') ? "ASC" : "DESC" ?>">
                        <span>Лайки</span>
                        <svg class="sorting__icon" width="10" height="12">
                            <use xlink:href="#icon-sort"></use>
                        </svg>
                    </a>
                </li>
                <li class="sorting__item">
                    <a class="sorting__link <?= ($sorting_parameters['sort_value'] === 'post_date') ? 'sorting__link--active' : ""; ?> <?= ($sorting_parameters['sorting'] === 'ASC') ? 'sorting__link--reverse' : ""; ?>" 
                    href="popular-cookie.php?sort_value=post_date&sorting=<?= ($sorting_parameters['sorting'] === 'DESC') ? "ASC" : "DESC" ?>">
                        <span>Дата</span>
                        <svg class="sorting__icon" width="10" height="12">
                            <use xlink:href="#icon-sort"></use>
                        </svg>
                    </a>
                </li>
            </ul>
        </div>
        <div class="popular__filters filters">
            <b class="popular__filters-caption filters__caption">Тип контента:</b>
            <ul class="popular__filters-list filters__list">
                <li class="popular__filters-item popular__filters-item--all filters__item filters__item--all">
                    <a class="filters__button filters__button--ellipse filters__button--all <?= ($sorting_parameters['type'] === 'all') ? 'filters__button--active' : ""; ?>" href="<?= 'popular-cookie.php?type=all' ?>">
                        <span>Все</span>
                    </a>
                </li>
                <?php foreach ($types as $type) : ?>
                    <li class="popular__filters-item filters__item">
                        <a href="<?= 'popular-cookie.php?type=' . $type['icon_type'] ?>" class="filters__button filters__button--<?= ($type['icon_type']) ?> <?= ($sorting_parameters['type'] == $type['icon_type']) ? 'filters__button--active' : ""; ?> button">
                            <span class="visually-hidden"><?= ($type['type_name']) ?></span>
                            <svg class="filters__icon" width="22" height="18">
                                <use xlink:href="#icon-filter-<?= ($type['icon_type']) ?>"></use>
                            </svg>
                        </a>
                    </li>
                <?php endforeach ?>
            </ul>
        </div>
    </div>
    <div class="popular__posts">
        <?php foreach ($posts as $index => $post) : ?>
            <article class="popular__post post post-<?= $post['icon_type'] ?>">
                <header class="post__header">
                    <h2><a href="post.php?post_id=<?= $post['id']?>"><?= $post['title'] ?></a></h2>
                </header>
                <div class="post__main">
                    <?php if ($post['icon_type'] === 'quote') : ?>
                        <blockquote>
                            <p>
                                <?= anti_xss($post['content_text']) ?>
                            </p>
                            <cite><?= anti_xss($post['quote_author']) ?></cite>
                        </blockquote>
                    <?php elseif ($post['icon_type'] === 'photo') : ?>
                        <div class="post-photo__image-wrapper">
                            <img src="uploads/<?= anti_xss($post['img']) ?>" alt="Фото от пользователя" width="360" height="240">
                        </div>
                    <?php elseif ($post['icon_type'] === 'link') : ?>
                        <div class="post-link__wrapper">
                            <a class="post-link__external" href="<?= anti_xss($post['link']) ?>" title="Перейти по ссылке">
                                <div class="post-link__info-wrapper">
                                    <div class="post-link__icon-wrapper">
                                        <img src="https://www.google.com/s2/favicons?domain=<?= $post['link']?>" alt="Иконка">
                                    </div>
                                    <div class="post-link__info">
                                        <h3><?= anti_xss($post['title']) ?></h3>
                                    </div>
                                </div>
                                <span><?= mb_strimwidth(anti_xss($post['link']), 0, 34, "...") ?></span>
                            </a>
                        </div>
                    <?php elseif ($post['icon_type'] === 'video') : ?>
                        <div class="post-video__block">
                            <div class="post-video__preview">
                                <?= embed_youtube_cover($post['video']) ?>
                            </div>
                            <a href="<?= htmlspecialchars($post['video']) ?>" class="post-video__play-big button" target="_blank">
                                <svg class="post-video__play-big-icon" width="14" height="14">
                                    <use xlink:href="#icon-video-play-big"></use>
                                </svg>
                                <span class="visually-hidden">Запустить проигрыватель</span>
                            </a>
                        </div>
                    <?php else : ?>
                        <?= crop_text(anti_xss($post['content_text']), $post['id']) ?>
                    <?php endif; ?>
                </div>
                <footer class="post__footer">
                    <div class="post__author">
                        <a class="post__author-link" href="#" title="Автор">
                            <div class="post__avatar-wrapper">
                                <img class="post__author-avatar" width="40px" src="userpics/<?= $post['avatar'] ?>" alt="Аватар пользователя">
                            </div>
                            <div class="post__info">
                                <b class="post__author-name"><?= anti_xss($post['author_login']) ?></b>
                                <?php $post_date = new DateTime($post['post_date']); ?>
                                <time class="post__time" title="<?= $post_date->format('d.m.Y H:i') ?>" datetime="<?= $post_date->format('Y-m-d H:i:s') ?>"><?= time_ago($post_date) ?></time>
                            </div>
                        </a>
                    </div>
                    <div class="post__indicators">
                        <div class="post__buttons">
                            <a class="post__indicator post__indicator--likes button" href="#" title="Лайк">
                                <svg class="post__indicator-icon" width="20" height="17">
                                    <use xlink:href="#icon-heart"></use>
                                </svg>
                                <svg class="post__indicator-icon post__indicator-icon--like-active" width="20" height="17">
                                    <use xlink:href="#icon-heart-active"></use>
                                </svg>
                                <span><?= $post['likes'] ?></span>
                                <span class="visually-hidden">количество лайков</span>
                            </a>
                            <a class="post__indicator post__indicator--comments button" href="#" title="Комментарии">
                                <svg class="post__indicator-icon" width="19" height="17">
                                    <use xlink:href="#icon-comment"></use>
                                </svg>
                                <span><?= $post['comments_value'] ?></span>
                                <span class="visually-hidden">количество комментариев</span>
                            </a>
                        </div>
                    </div>
                </footer>
            </article>
        <?php endforeach; ?>
    </div>
</div>