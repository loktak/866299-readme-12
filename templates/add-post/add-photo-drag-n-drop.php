<input type="hidden" name="MAX_FILE_SIZE" value="104857600">
<?php //Это сделано для того что бы не крашился сервер если будет загружен слишком большой файл?>
<div class="adding-post__input-file-container form__input-container form__input-container--file">
    <div class="adding-post__input-file-wrapper form__input-file-wrapper">
        <div class="adding-post__file-zone adding-post__file-zone--photo form__file-zone dropzone">
            <input class="adding-post__input-file form__input-file" id="picture" type="file" name="picture" title=" ">
            <div class="form__file-zone-text">
                <span>Перетащите фото сюда</span>
            </div>
        </div>
        <button class="adding-post__input-file-button form__input-file-button form__input-file-button--photo button"
                type="button">
            <span>Выбрать фото</span>
            <svg class="adding-post__attach-icon form__attach-icon" width="10" height="20">
                <use xlink:href="#icon-attach"></use>
            </svg>
        </button>
    </div>
    <div class="adding-post__file adding-post__file--photo form__file dropzone-previews">

    </div>
</div>
