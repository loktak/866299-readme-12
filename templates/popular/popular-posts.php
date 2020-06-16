<div class="popular__posts">
    <?php foreach ($posts as $index => $post) : ?>
        <article class="popular__post post post-<?= $post['icon_type'] ?>">
            <header class="post__header">
                <h2><a href="post.php?post_id=<?= $post['id'] ?>"><?= anti_xss($post['title']) ?></a></h2>
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
                        <img src="uploads/<?= anti_xss($post['img']) ?>" 
                        alt="Фото от пользователя" width="360" height="240">
                    </div>
                <?php elseif ($post['icon_type'] === 'link') : ?>
                    <div class="post-link__wrapper">
                        <a class="post-link__external" 
                        href="<?= anti_xss($post['link']) ?>" target="_blank" title="Перейти по ссылке">
                            <div class="post-link__info-wrapper">
                                <div class="post-link__icon-wrapper">
                                    <img src="https://www.google.com/s2/favicons?domain=<?= anti_xss($post['link']) ?>" 
                                    alt="Иконка">
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
                            <?= embed_youtube_cover(anti_xss($post['video'])) ?>
                        </div>
                        <a href="<?= htmlspecialchars(anti_xss($post['video'])) ?>" 
                        class="post-video__play-big button" target="_blank">
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
                    <a class="post__author-link" 
                    href="profile.php?active_tab=posts&user_id=<?= $post['user_id'] ?>" 
                    title="Автор">
                        <div class="post__avatar-wrapper">
                            <img class="post__author-avatar" width="40px" 
                            src="userpics/<?= anti_xss($post['avatar']) ?>" alt="Аватар пользователя">
                        </div>
                        <div class="post__info">
                            <b class="post__author-name"><?= anti_xss($post['author_login']) ?></b>
                            <?php $post_date = new DateTime($post['post_date']); ?>
                            <time class="post__time" 
                            title="<?= $post_date->format('d.m.Y H:i') ?>" 
                            datetime="<?= $post_date->format('Y-m-d H:i:s') ?>">
                                <?= time_ago($post_date) ?>
                            </time>
                        </div>
                    </a>
                </div>
                <div class="post__indicators">
                    <div class="post__buttons">
                        <a class="post__indicator post__indicator--likes button" 
                        href="like.php?post_id=<?= $post['id'] ?>" title="Лайк">
                            <svg class="post__indicator-icon" width="20" height="17">
                                <use xlink:href="#icon-heart"></use>
                            </svg>
                            <svg class="post__indicator-icon post__indicator-icon--like-active" width="20" height="17">
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
                            <span><?= $post['comments_value'] ?></span>
                            <span class="visually-hidden">количество комментариев</span>
                        </a>
                    </div>
                </div>
            </footer>
        </article>
    <?php endforeach; ?>
</div>
<?php if ((int) $pages_count > 1) : ?>
    <div class="popular__page-links">
        <a class="popular__page-link popular__page-link--prev button button--gray" <?= ((int) $current_page !== 1) ?
        'href="popular.php?current_page=' . ($current_page - 1) . '"' : "" ?>>
            <?= ((int) $current_page === 1) ? 'Вы находитесь на первой странице' : 'Предыдущая страница' ?></a>
        <a class="popular__page-link popular__page-link--next button button--gray" 
        <?= ((int) $current_page !== (int) $pages_count) ?
        'href="popular.php?current_page=' . ($current_page + 1) . '"' : "" ?>>
            <?= ((int) $current_page === (int) $pages_count) ?
            'Вы находитесь на последней странице' : "следующая страница" ?></a>
    </div>
<?php endif ?>