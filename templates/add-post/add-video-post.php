<div class="adding-post__input-wrapper form__input-wrapper">
    <label class="adding-post__label form__label" for="video-url">Ссылка youtube <span
                class="form__input-required">*</span></label>
    <div class="form__input-section <?=(!empty($errors['video-url'])) ? "form__input-section--error" : ""?>">
        <input class="adding-post__input form__input" id="video-url" type="text" name="video-url"
               placeholder="Введите ссылку на видео" value="<?=getPostValue('video-url')?>">
        <button class="form__error-button button" type="button">!<span
                    class="visually-hidden">Информация об ошибке</span></button>
        <div class="form__error-text">
            <h3 class="form__error-title">Обнаружена ошибка</h3>
            <p class="form__error-desc"><?=(!empty($errors['video-url']) ? $errors['video-url'] : "")?></p>
        </div>
    </div>
</div>
<div class="adding-post__textarea-wrapper form__input-wrapper" style="display:none">
    <label class="adding-post__label form__label" for="form-type">ТИП ПОСТА <span class="form__input-required">*</span></label>
    <div class="form__input-section">
        <input class="adding-post__input form__input" id="form-type" type="text" name="form-type" value="video">
    </div>
</div>
