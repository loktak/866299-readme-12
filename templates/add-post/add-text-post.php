<div class="adding-post__textarea-wrapper form__textarea-wrapper">
    <label class="adding-post__label form__label" for="post-text">Текст поста <span
                class="form__input-required">*</span></label>
    <div class="form__input-section <?=(!empty($errors['post-text'])) ? "form__input-section--error" : ""?>">
        <textarea class="adding-post__textarea form__textarea form__input" id="post-text" name="post-text"
                  placeholder="Введите текст публикации"><?=getPostValue('post-text')?></textarea>
        <button class="form__error-button button" type="button">!<span
                    class="visually-hidden">Информация об ошибке</span></button>
        <div class="form__error-text">
            <h3 class="form__error-title">Обнаружена ошибка</h3>
            <p class="form__error-desc"><?=(!empty($errors['post-text']) ? $errors['post-text'] : "")?></p>
        </div>
    </div>
</div>
<div class="adding-post__textarea-wrapper form__input-wrapper" style="display:none">
    <label class="adding-post__label form__label" for="form-type">ТИП ПОСТА <span class="form__input-required">*</span></label>
    <div class="form__input-section">
        <input class="adding-post__input form__input" id="form-type" type="text" name="form-type" value="text">
    </div>
</div>
