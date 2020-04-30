<div class="adding-post__input-wrapper form__textarea-wrapper">
  <label class="adding-post__label form__label" for="cite-text">Текст цитаты <span class="form__input-required">*</span></label>
  <div class="form__input-section <?= (!empty($errors['cite-text'])) ? "form__input-section--error" : "" ?>">
    <textarea class="adding-post__textarea adding-post__textarea--quote form__textarea form__input" id="cite-text" name="cite-text" placeholder="Текст цитаты" ><?= getPostValue('cite-text') ?></textarea>
    <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
    <div class="form__error-text">
      <h3 class="form__error-title"><?= (!empty($errors['cite-text']) ? $errors['cite-text'] : "" )?></h3>
      <p class="form__error-desc">Давайте не будем цитировать целые книги. 75 символов будет вполне достаточно.</p>
    </div>
  </div>
</div>
<div class="adding-post__textarea-wrapper form__input-wrapper">
  <label class="adding-post__label form__label" for="quote-author">Автор <span class="form__input-required">*</span></label>
  <div class="form__input-section <?= (!empty($errors['quote-author'])) ? "form__input-section--error" : "" ?>">
    <input class="adding-post__input form__input" id="quote-author" type="text" name="quote-author" placeholder="Введите автора цитаты" value="<?= getPostValue('quote-author') ?>">
    <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
    <div class="form__error-text">
      <h3 class="form__error-title"><?= (!empty($errors['quote-author']) ? $errors['quote-author'] : "" )?></h3>
      <p class="form__error-desc">Не знаете кто это сказал? Так и напишите.</p>
    </div>
  </div>
</div>
<div class="adding-post__textarea-wrapper form__input-wrapper" style="display:none">
  <label class="adding-post__label form__label" for="form-type">ТИП ПОСТА <span class="form__input-required">*</span></label>
  <div class="form__input-section">
    <input class="adding-post__input form__input" id="form-type" type="text" name="form-type" value="quote">
  </div>
</div>