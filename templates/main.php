<div class="container">
    <h1 class="page__title page__title--popular">Популярное</h1>
</div>
<div class="popular container">
    <div class="popular__filters-wrapper">
        <div class="popular__sorting sorting">
            <b class="popular__sorting-caption sorting__caption">Сортировка:</b>
            <ul class="popular__sorting-list sorting__list">
                <li class="sorting__item sorting__item--popular">
                    <a class="sorting__link <?= ($sorting_parameters['sort_value'] === 'views') ? 'sorting__link--active' : ""; ?> <?= ($sorting_parameters['sorting'] === 'ASC') ? 'sorting__link--reverse' : ""; ?>"
                       href="popular.php?sort_value=views&sorting=<?= ($sorting_parameters['sorting'] === 'DESC') ? "ASC" : "DESC" ?>">
                        <span>Популярность</span>
                        <svg class="sorting__icon" width="10" height="12">
                            <use xlink:href="#icon-sort"></use>
                        </svg>
                    </a>
                </li>
                <li class="sorting__item">
                    <a class="sorting__link <?= ($sorting_parameters['sort_value'] === 'likes') ? 'sorting__link--active' : ""; ?> <?= ($sorting_parameters['sorting'] === 'ASC') ? 'sorting__link--reverse' : ""; ?>"
                       href="popular.php?sort_value=likes&sorting=<?= ($sorting_parameters['sorting'] === 'DESC') ? "ASC" : "DESC" ?>">
                        <span>Лайки</span>
                        <svg class="sorting__icon" width="10" height="12">
                            <use xlink:href="#icon-sort"></use>
                        </svg>
                    </a>
                </li>
                <li class="sorting__item">
                    <a class="sorting__link <?= ($sorting_parameters['sort_value'] === 'post_date') ? 'sorting__link--active' : ""; ?> <?= ($sorting_parameters['sorting'] === 'ASC') ? 'sorting__link--reverse' : ""; ?>"
                       href="popular.php?sort_value=post_date&sorting=<?= ($sorting_parameters['sorting'] === 'DESC') ? "ASC" : "DESC" ?>">
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
                    <a class="filters__button filters__button--ellipse filters__button--all <?= ($sorting_parameters['type'] === 'all') ? 'filters__button--active' : ""; ?>"
                       href="<?= 'popular.php?type=all' ?>">
                        <span>Все</span>
                    </a>
                </li>
                <?php foreach ($types as $type): ?>
                    <li class="popular__filters-item filters__item">
                        <a href="<?= 'popular.php?current_page=1&type='.$type['icon_type'] ?>"
                           class="filters__button filters__button--<?= ($type['icon_type']) ?> <?= ($sorting_parameters['type'] == $type['icon_type']) ? 'filters__button--active' : ""; ?> button">
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
    <?= $popular_posts ?>
</div>
