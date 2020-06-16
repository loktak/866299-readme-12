<section class="profile__likes tabs__content tabs__content--active">
    <h2 class="visually-hidden">Лайки</h2>
    <ul class="profile__likes-list">
        <?php if (empty($likes)) : ?>
            <P style="font-size:36px;font-weight:bold;">Пользователю не поставили ни одно лайка. Вы можете быть первым
                =)) Или не быть! Вот в чем вопрос.</P>
        <?php endif ?>
        <?php foreach ($likes as $like) : ?>
            <li class="post-mini post-mini--<?= $like['type'] ?> post user">
                <div class="post-mini__user-info user__info">
                    <div class="post-mini__avatar user__avatar">
                        <a class="user__avatar-link" href="#">
                            <img class="post-mini__picture user__picture"
                                 src="userpics/<?= anti_xss($like['avatar']) ?>" alt="Аватар пользователя">
                        </a>
                    </div>
                    <div class="post-mini__name-wrapper user__name-wrapper">
                        <a class="post-mini__name user__name"
                           href="profile.php?user_id=<?= $like['user_id'] ?>&active_tab=posts">
                            <span><?= anti_xss($like['login']) ?></span>
                        </a>
                        <div class="post-mini__action">
                            <span class="post-mini__activity user__additional">Лайкнул вашу публикацию</span>
                            <?php $like_date = new DateTime($like['like_date']); ?></time>
                            <time class="post-mini__time user__additional"
                                  title="<?= $like_date->format('d.m.Y H:i') ?>"
                                  datetime="<?= $like_date->format('Y-m-d H:i:s') ?>"><?= time_ago($like_date) ?></time>
                        </div>
                    </div>
                </div>
                <div class="post-mini__preview">
                    <a class="post-mini__link" href="post.php?post_id=<?= $like['id'] ?>" title="Перейти на публикацию">
                        <?php if ($like['type'] === 'photo') : ?>
                            <div class="post-mini__image-wrapper">
                                <img class="post-mini__image" height="170px" src="uploads/<?= $like['img'] ?>"
                                     alt="Превью публикации">
                            </div>
                            <span class="visually-hidden"><?= $like['type'] ?></span>
                        <?php elseif ($like['type'] === 'video') : ?>
                            <div class="post-mini__image-wrapper">
                                <?= embed_youtube_cover(anti_xss($like['video'])) ?>
                            </div>
                            <span class="post-mini__play-big">
                                <svg class="post-mini__play-big-icon" width="12" height="13">
                                    <use xlink:href="#icon-video-play-big"></use>
                                </svg>
                            </span>
                            <span class="visually-hidden"><?= $like['type'] ?></span>
                        <?php else : ?>
                            <span class="visually-hidden"><?= $like['type'] ?></span>
                            <svg class="post-mini__preview-icon" width="20" height="21">
                                <use xlink:href="#icon-filter-<?= $like['type'] ?>"></use>
                            </svg>
                        <?php endif ?>
                    </a>
                </div>
            </li>
        <?php endforeach ?>
    </ul>
</section>
