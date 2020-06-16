<section class="profile__posts tabs__content tabs__content--active">
    <h2 class="visually-hidden">Публикации</h2>
    <?php foreach ($posts as $post) : ?>
        <article class="profile__post post post-<?= $post['type'] ?>">
            <a class="anchor" name="post-<?= $post['id'] ?>"></a>
            <header class="post__header">
                <?php if (!empty($post['original_id'])) : ?>
                    <div class="post__author">
                        <a class="post__author-link" 
                        href="profile.php?user_id=<?= $post['original_author_id'] ?>&active_tab=posts" title="Автор">
                            <div class="post__avatar-wrapper post__avatar-wrapper--repost">
                                <img class="post__author-avatar" width="60px"
                                src="userpics/<?= anti_xss($post['original_author_avatar']) ?>" 
                                alt="Аватар пользователя">
                            </div>
                            <div class="post__info">
                                <b class="post__author-name">Репост: <?= anti_xss($post['original_author_name']) ?></b>
                                <?php $original_post_date = new DateTime($post['original_date']); ?>
                                <time class="post__time" title="<?= $original_post_date->format('d.m.Y H:i') ?>" 
                                datetime="<?= $original_post_date->format('Y-m-d H:i:s') ?>">
                                <?= time_ago($original_post_date) ?></time>
                            </div>
                        </a>
                    </div>
                <?php endif ?>
            </header>
            <div class="post__main">
                <h2><a href="post.php?post_id=<?= $post['id'] ?>"><?= anti_xss($post['title']) ?></a></h2>
                </header>
                <div class="post__main">
                    <?php if ($post['type'] === 'photo') : ?>
                        <div class="post-photo__image-wrapper">
                            <img src="uploads/<?= anti_xss($post['img']) ?>"
                            alt="Фото от пользователя" width="760" height="396">
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
                                <a class="post-link__external" 
                                href="<?= anti_xss($post['link']) ?>" target="_blank" title="Перейти по ссылке">
                                    <div class="post-link__icon-wrapper">
                                        <img src="
                                        https://www.google.com/s2/favicons?domain=<?= anti_xss($post['link']) ?>" 
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
                        <footer class="post__footer">
                            <div class="post__indicators">
                                <div class="post__buttons">
                                    <a class="post__indicator post__indicator--likes button"
                                    href="like.php?post_id=<?= $post['id'] ?>" title="Лайк">
                                        <svg class="post__indicator-icon" width="20" height="17">
                                            <use xlink:href="#icon-heart"></use>
                                        </svg>
                                        <svg class="post__indicator-icon post__indicator-icon--like-active" 
                                        width="20" height="17">
                                            <use xlink:href="#icon-heart-active"></use>
                                        </svg>
                                        <span><?= $post['likes'] ?></span>
                                        <span class="visually-hidden">количество лайков</span>
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
                                <?php $post_date = new DateTime($post['post_date']); ?>
                                <time class="post__time" title="<?= $post_date->format('d.m.Y H:i') ?>" 
                                datetime="<?= $post_date->format('Y-m-d H:i:s') ?>"><?= time_ago($post_date) ?></time>
                            </div>
                            <ul class="post__tags">
                                <?php foreach ($hashtags[$post['id']] as $key => $value) : ?>
                                    <li>
                                        <a href="search.php?search_request=%23<?= $value['title'] ?>">
                                        #<?= $value['title'] ?></a>
                                    </li>
                                <?php endforeach ?>
                            </ul>
                        </footer>
                        <div class="comments">
                            <a class="comments__button button 
                            <?= ($comments_for_id === $post['id']) ? "visually-hidden" : ""; ?>"
                            href="profile.php?comments_for=<?= $post['id'] ?>#post-<?= $post['id'] ?>">Показать
                                комментарии</a>
                            <?php if ($comments_for_id === $post['id']) : ?>
                                <div class="comments__list-wrapper">
                                    <ul class="comments__list">
                                        <?php foreach ($comments[$post['id']] as $comment) : ?>
                                            <li class="comments__item user">
                                                <div class="comments__avatar">
                                                    <a class="user__avatar-link"
                                                    href="
                                                    profile.php?user_id=<?= $comment['user_id'] ?>&active_tab=posts">
                                                        <img class="comments__picture" width="40px"
                                                        src="userpics/<?= anti_xss($comment['avatar']) ?>" 
                                                        alt="Аватар пользователя">
                                                    </a>
                                                </div>
                                                <div class="comments__info">
                                                    <div class="comments__name-wrapper">
                                                        <a class="comments__user-name" 
                                                        href="profile.php?user_id=
                                                        <?= $comment['user_id'] ?>&active_tab=posts">
                                                            <span><?= anti_xss($comment['author']) ?></span>
                                                        </a>
                                                        <?php $comment_date = new DateTime($comment['comment_date']); ?>
                                                        <time class="comments__time" 
                                                        title="<?= $comment_date->format('d.m.Y H:i') ?>" 
                                                        datetime="<?= $comment_date->format('Y-m-d H:i:s') ?>">
                                                        <?= time_ago($comment_date) ?></time>
                                                    </div>
                                                    <p class="comments__text" 
                                                    style="
                                                    font-size:16px; color:black;text-align:left;font-weight:normal">
                                                        <?= anti_xss($comment['content']) ?>
                                                    </p>
                                                </div>
                                            </li>
                                        <?php endforeach ?>
                                    </ul>
                                    <?php if ($hidded_comments_count !== null) : ?>
                                        <a class="comments__more-link" 
                                        href="
                                        <?= $_SERVER['REQUEST_URI'] . "&show_comments=all#post-" . $post['id'] ?>">
                                            <span>Показать все комментарии</span>
                                            <sup class="comments__amount"><?= $hidded_comments_count ?></sup>
                                        </a>
                                    <?php endif ?>
                                </div>
                                <form class="comments__form form" method="post">
                                    <div class="comments__my-avatar">
                                        <img class="comments__picture" width="40px+" 
                                        src="userpics/<?= $user_data['avatar'] ?>" alt="Аватар пользователя">
                                    </div>
                                    <div class="form__input-section 
                                    <?= (!empty($errors['comment']) ? "form__input-section--error" : "") ?>">
                                        <textarea class="comments__textarea form__textarea form__input" 
                                        name="comment" id="comment" 
                                        placeholder="Ваш комментарий"><?= getPostValue('comment') ?></textarea>
                                        <label class="visually-hidden">Ваш комментарий</label>
                                        <button class="form__error-button button" type="button">!</button>
                                        <div class="form__error-text">
                                            <h3 class="form__error-title">Ошибка валидации</h3>
                                            <p class="form__error-desc">
                                                <?= (!empty($errors['comment']) ? $errors['comment'] : "") ?></p>
                                        </div>
                                    </div>
                                    <input type="hidden" value="<?= $post['id'] ?>" 
                                    name="post_id" id="post_id">
                                    <input type="hidden" value="<?= $post['user_id'] ?>" 
                                    name="author_id" id="author_id">
                                    <button class="comments__submit button button--green" 
                                    type="submit">Отправить</button>
                                </form>
                        </div>
                            <?php endif ?>
        </article>
    <?php endforeach ?>