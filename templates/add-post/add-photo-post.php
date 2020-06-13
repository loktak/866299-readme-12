<div class="adding-post__input-wrapper form__input-wrapper">
    <label class="adding-post__label form__label" for="photo-url">Ссылка из интернета</label>
    <div class="form__input-section <?= (!empty($errors['photo-url'])) ? "form__input-section--error" : "" ?>">
        <input class="adding-post__input form__input " id="photo-url" type="text" name="photo-url"
               placeholder="Введите ссылку" value="<?= getPostValue('photo-url') ?>">
        <button class="form__error-button button" type="button">!<span
                class="visually-hidden">Информация об ошибке</span></button>
        <div class="form__error-text">
            <h3 class="form__error-title">Обнаружена ошибка</h3>
            <p class="form__error-desc"><?= (!empty($errors['photo-url']) ? $errors['photo-url'] : "") ?></p>
        </div>
    </div>
</div>
<div class="adding-post__textarea-wrapper form__input-wrapper" style="display:none">
    <label class="adding-post__label form__label" for="form-type">ТИП ПОСТА <span class="form__input-required">*</span></label>
    <div class="form__input-section">
        <input class="adding-post__input form__input" id="form-type" type="text" name="form-type" value="photo">
    </div>
</div>
