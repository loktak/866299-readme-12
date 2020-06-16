<div class="page__main-section">
    <div class="container">
        <h1 class="page__title page__title--adding-post">Добавить публикацию</h1>
    </div>
    <div class="adding-post container">
        <div class="adding-post__tabs-wrapper tabs">
            <div class="adding-post__tabs filters">
                <ul class="adding-post__tabs-list filters__list tabs__list">
                    <?php foreach ($types as $type) : ?>
                        <li class="adding-post__tabs-item filters__item">
                            <a class="adding-post__tabs-link 
                            filters__button filters__button--<?= ($type['icon_type']) ?> 
                            <?= ($page_parameters['form-type'] === $type['icon_type']) ?
                            'filters__button--active' : "" ?>" href="add.php?type=<?= ($type['icon_type']) ?>">
                                <svg class="filters__icon" width="22" height="18">
                                    <use xlink:href="#icon-filter-<?= $type['icon_type'] ?>"></use>
                                </svg>
                                <span><?= $type['type_name'] ?></span>
                            </a>
                        </li>
                    <?php endforeach ?>
                </ul>
            </div>
            <div class="adding-post__tab-content">
                <section class="adding-post__<?= anti_xss($page_parameters['form-type']) ?>">
                    <h2 class="visually-hidden">Форма добавления <?= anti_xss($page_parameters['name']) ?></h2>
                    <form class="adding-post__form form" action="add.php" 
                    method="post" <?= ($page_parameters['form-type'] === 'photo') ?
                    'enctype="multipart/form-data"' : "" ?>>
                        <div class="form__text-inputs-wrapper">
                            <div class="form__text-inputs">
                                <div class="adding-post__input-wrapper form__input-wrapper">
                                    <label class="adding-post__label form__label" 
                                    for="heading">Заголовок <span 
                                    class="form__input-required">*</span></label>
                                    <div class="form__input-section <?= (!empty($errors['heading'])) ?
                                    "form__input-section--error" : "" ?>">
                                        <input class="adding-post__input form__input" id="heading" 
                                        type="text" name="heading" placeholder="Введите заголовок" 
                                        value="<?= getPostValue('heading') ?>">
                                        <button class="form__error-button button" type="button">!<span 
                                        class="visually-hidden">Информация об ошибке</span></button>
                                        <div class="form__error-text">
                                            <h3 class="form__error-title">Обнаружена ошибка</h3>
                                            <p class="form__error-desc"><?= (!empty($errors['heading']) ?
                                            $errors['heading'] : "") ?></p>
                                        </div>
                                    </div>
                                </div>
                                <?= $content ?>
                                <div class="adding-post__input-wrapper form__input-wrapper">
                                    <label class="adding-post__label form__label" for="tags">
                                        Теги
                                    </label>
                                    <div class="form__input-section <?= (!empty($errors['tags'])) ?
                                    "form__input-section--error" : "" ?>">
                                        <input class="adding-post__input form__input" 
                                        id="tags" type="text" name="tags" placeholder="Введите теги" 
                                        value="<?= getPostValue('tags') ?>">
                                        <button class="form__error-button button" type="button">!
                                            <span class="visually-hidden">
                                                Информация об ошибке
                                            </span></button>
                                        <div class="form__error-text">
                                            <h3 class="form__error-title">Обнаружена ошибка</h3>
                                            <p class="form__error-desc"><?= (!empty($errors['tags']) ?
                                            $errors['tags'] : "") ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form__invalid-block <?= (empty($errors)) ? 'visually-hidden' : "" ?>">
                                <b class="form__invalid-slogan">Пожалуйста, исправьте следующие ошибки:</b>
                                <ul class="form__invalid-list">
                                    <?php foreach ($errors as $key => $error) : ?>
                                        <li class="form__invalid-item"><?= get_field_name($key) . ": " . $error ?></li>
                                    <?php endforeach ?>
                                </ul>
                            </div>
                        </div>
                        <?= ($page_parameters['form-type'] === 'photo') ? include_template(
                            'add-post/add-photo-drag-n-drop.php',
                            []
                        ) : "" ?>
                        <div class="adding-post__buttons">
                            <button class="adding-post__submit button button--main" type="submit">Опубликовать</button>
                            <a class="adding-post__close" href="#">Закрыть</a>
                        </div>
                    </form>
                </section>
            </div>