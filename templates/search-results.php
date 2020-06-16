<h1 class="visually-hidden">Страница результатов поиска</h1>
<section class="search">
    <h2 class="visually-hidden">Результаты поиска</h2>
    <div class="search__query-wrapper">
        <div class="search__query container">
            <span>Вы искали:</span>
            <span class="search__query-text">"<?= anti_xss($search_request) ?>"</span>
        </div>
    </div>
    <div class="search__results-wrapper">
        <div class="container">
            <div class="search__content">
                <?php foreach ($posts as $post) : ?>
                    <article class="search__post post post-<?= $post['type'] ?>">
                        <header class="post__header post__author">
                            <a class="post__author-link" href="#" title="Автор">
                                <div class="post__avatar-wrapper">
                                    <img class="post__author-avatar" src="userpics/<?= anti_xss($post['avatar']) ?>"
                                         alt="Аватар пользователя" width="60" height="60">
                                </div>
                                <div class="post__info">
                                    <b class="post__author-name"><?= anti_xss($post['author']) ?></b>
                                    <?php $post_date = new DateTime($post['post_date']); ?>
                                    <span class="post__time"><?= time_ago($post_date) ?></span>
                                </div>
                            </a>
                        </header>
                        <div class="post__main">
                            <?php if ($post['type'] === "photo" || $post['type'] === "text") : ?>
                                <h2><a href="post.php?post_id=<?= $post['id'] ?>"><?= anti_xss($post['title']) ?></a>
                                </h2>
                            <?php endif ?>
                            <?php if ($post['type'] === 'photo') : ?>
                                <div class="post-photo__image-wrapper">
                                    <img src="uploads/<?= anti_xss($post['img']) ?>" alt="Фото от пользователя"
                                         width="760" height="396">
                                </div>
                            <?php elseif ($post['type'] === 'video') : ?>
                            <div class="post-video__block">
                                <div class="post-video__preview">
                                    <?= embed_youtube_video(anti_xss($post['video'])); ?>
                                </div>
                            <?php elseif ($post['type'] === 'text') : ?>
                                    <?= crop_text(anti_xss($post['content_text']), $post['id']) ?>
                            <?php elseif ($post['type'] === 'quote') : ?>
                                    <blockquote>
                                        <p><?= anti_xss($post['content_text']) ?></p>
                                        <cite><?= anti_xss($post['quote_author']) ?></cite>
                                    </blockquote>
                            <?php elseif ($post['type'] === 'link') : ?>
                                    <div class="post-link__wrapper">
                                        <a class="post-link__external" href="<?= anti_xss($post['link']) ?>"
                                           title="Перейти по ссылке">
                                            <div class="post-link__icon-wrapper">
                                                <img
                                                        src="https://www.google.com/s2/favicons?domain=
                                                        <?= anti_xss($post['link']) ?>"
                                                        alt="Иконка">
                                            </div>
                                            <div class="post-link__info">
                                                <h3><?= anti_xss($post['title']) ?></h3>
                                                <p><?= get_link_title(anti_xss($post['link'])) ?></p>
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
                                    <a class="post__indicator post__indicator--likes button" href="#" title="Лайк">
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
                                    <a class="post__indicator post__indicator--comments button" href="#"
                                       title="Комментарии">
                                        <svg class="post__indicator-icon" width="19" height="17">
                                            <use xlink:href="#icon-comment"></use>
                                        </svg>
                                        <span><?= $post['comments'] ?></span>
                                        <span class="visually-hidden">количество комментариев</span>
                                    </a>
                                </div>
                            </footer>
                    </article>
                <?php endforeach ?>
            </div>
        </div>
    </div>
</section>
