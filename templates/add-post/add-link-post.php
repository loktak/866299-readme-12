<div class="adding-post__textarea-wrapper form__input-wrapper">
    <label class="adding-post__label form__label" for="post-link">Ссылка <span
                class="form__input-required">*</span></label>
    <div class="form__input-section <?=(!empty($errors['post-link'])) ? "form__input-section--error" : ""?>">
        <input class="adding-post__input form__input" id="post-link" type="text" name="post-link"
               placeholder="Введите ссылку" value="<?=getPostValue('post-link')?>">
        <button class="form__error-button button" type="button">!<span
                    class="visually-hidden">Информация об ошибке</span></button>
        <div class="form__error-text">
            <h3 class="form__error-title">Обнаружена ошибка</h3>
            <p class="form__error-desc"><?=(isset($errors['post-link']) ? $errors['post-link'] : "123")?></p>
        </div>
    </div>
</div>
<div class="adding-post__textarea-wrapper form__input-wrapper" style="display:none">
    <label class="adding-post__label form__label" for="form-type">ТИП ПОСТА <span class="form__input-required">*</span></label>
    <div class="form__input-section">
        <input class="adding-post__input form__input" id="form-type" type="text" name="form-type" value="link">
    </div>
</div>
