<?php

/**
 * Функция проверяет заполнены ли поля формы по указаным ключам
 * @param array $required_fields
 *
 * @return array массив данных
 */
function check_required_fields($required_fields)
{
    $errors = [];
    foreach ($required_fields as $key => $field) {
        if (empty($_POST[$field])) {
            $errors[$field] = "Поле должно быть заполнено";
        }
    }

    return $errors;
}

/**
 * Функция определяет название поля на русском, согласно его английскому названию
 * @param string $english_name английское название
 *
 * @return string $name Русское название;
 */
function get_field_name($english_name)
{
    switch ($english_name) {
        case 'heading':
            $name = 'Заголовок';
            break;
        case 'photo-url':
            $name = 'Ссылка из интеренета';
            break;
        case 'video-url':
            $name = 'Ссылка youtube';
            break;
        case 'post-text':
            $name = 'Текст поста';
            break;
        case 'cite-text':
            $name = 'Текст цитаты';
            break;
        case 'quote-author':
            $name = 'Автор';
            break;
        case 'post-link':
            $name = 'Ссылка';
            break;
        case 'tags':
            $name = 'Теги';
            break;
        case 'email':
            $name = 'Электронная почта';
            break;
        case 'login':
            $name = 'Логин';
            break;
        case 'password':
            $name = 'Пароль';
            break;
        case 'password-repeat':
            $name = 'Повтор пароля';
            break;
        case 'input-file':
            $name = 'Прикрепленное изображение';
    }

    return $name;
}

/**
 * Функция проверяет ошибки по соответствующим ключам и записывает их в массив
 * @param array $rules массив со значениями которые надо проверить
 * @param array $errors массив с уже существующими ошибками
 * @param array $array массив с данными для проверки
 *
 * @return array массив данных с ошибками
 */
function check_rules($rules, $errors, $array)
{
    foreach ($array as $key => $value) {
        if (empty($errors[$key]) && isset($rules[$key])) {
            $rule = $rules[$key];
            $errors[$key] = $rule();
        }
    }

    return $errors;
}

/**
 * Функция поле тэги на соответсвтие тз
 * @param string $tags строчка тегов
 *
 * @return string Ошибку если валидация не прошла
 */
function check_tags($tags)
{
    $tags_array = explode(" ", $tags);
    if (preg_match('/[^a-zа-я ]+/msiu', $tags)) {
        return 'Теги должны состоять только из букв.';
    }
    foreach ($tags_array as $tag) {
        if (mb_strlen($tag) > 20) {
            return "Используется слишком длинный тег: {$tag}. Подберите синоним или убедитесь что тег состоит из одного слова";
        }
    }
}

/**
 * Функция проверяет текст на колличество символов в нем, и выводит сообщение если проверка не прошла
 * @param string $text сам текст
 * @param int $min минимальное значение символов
 * @param int $max максимальное значение символов
 *
 * @return string Ошибку если валидация не прошла
 */
function validate_lenght($text, $min = 3, $max = 40)
{
    if (mb_strlen($text) < $min || mb_strlen($text) > $max) {
        return "Значение поля должно быть не меньше $min и не больше $max символов";
    }
}

/**
 * Функция проверяет ссылку с помощью filter_var
 * @param string $url ссылка
 *
 * @return string Ошибку если валидация не прошла
 */
function check_url($url)
{
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        return "Формат ссылки не верен.";
    }
}

/**
 * Функция проверяет email с помощью filter_var
 * @param string $email ссылка
 *
 * @return string Ошибку если валидация не прошла
 */
function check_email($email)
{
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return 'Введите корректный адреc электронной почты. Пример: email@example.com';
    }
}

/**
 * Функция проверяет доступно ли видео по ссылке на youtube
 * @param string $url ссылка на видео
 *
 * @return string Ошибку если валидация не прошла
 */
function check_youtube_link($url)
{
    $id = extract_youtube_id($url);
    $headers = get_headers('https://www.youtube.com/oembed?format=json&url=http://www.youtube.com/watch?v='.$id);
    if (!is_array($headers)) {
        return "Видео по такой ссылке не найдено. Проверьте ссылку на видео";
    }

    $err_flag = strpos($headers[0], '200') ? 200 : 404;

    if ($err_flag !== 200) {
        return "Видео по такой ссылке не найдено. Проверьте ссылку на видео";
    }
}

/**
 * Функция проверяет файл по ссылке. и если он соответствует критериям загружает его в папку uploads
 * @param string $url ссылка на сайт
 *
 * @return string Ошибку если валидация не прошла
 */
function get_img_by_link($url)
{
    ob_start();
    $content = file_get_contents($url);
    ob_get_clean();

    if (!$content) {
        return 'Файл по данной ссылке не найден';
    }
    $url_with_parameters = explode("?", $url);
    $url = $url_with_parameters[0];
    $file_name = basename($url);
    $file_path = __DIR__."/uploads/".$file_name;
    $file_info = new finfo(FILEINFO_MIME_TYPE);

    $mime_type = $file_info->buffer($content);
    $valid_mime_types = ['image/png', 'image/jpeg', 'image/gif'];
    if (!in_array($mime_type, $valid_mime_types)) {
        return "Не подходящий формат изображения. Используйте jpg, png или gif";
    }
    file_put_contents($file_path, $content);

}

/**
 * Функция определяет путь до загруженного файла основоваясь на том существует имя файла в массиве $_FILES или нет.
 * Она нужна что бы обойти ограничение по колличеству if. как это сделать по другому я не знаю.
 * @param string $url
 * @param string $file_name
 *
 * @return string путь до загруженного файла
 */
function get_file_path($url, $file_name)
{
    if (!$file_name) {
        $file_name = basename($url);
    }

    return $file_name;
}

/**
 * Функция проверяет файл загруженный через форму обратной связи. и если он соответствует критериям загружает его в папку uploads
 * @param array $files массив данных о файле
 *
 * @return string Ошибку если валидация не прошла
 */
function upload_post_picture($files, $folder = '/uploads/')
{
    if (($files['picture']['size'] >= 104857600)) {
        return 'прикрепленный файл слишком большой';
    }
    $file_name = $files['picture']['name'];
    $file_path = __DIR__.$folder;
    $valid_mime_types = ['image/png', 'image/jpeg', 'image/gif'];
    if (!in_array(mime_content_type($files['picture']['tmp_name']), $valid_mime_types)) {
        return 'Не подходящий формат прикрепленного изображения. Используйте jpg, png или gif.';
    }
    move_uploaded_file($files['picture']['tmp_name'], $file_path.$file_name);
}

/**
 * Функция принемает строку с тегами и возвращает массив без повторений
 * @param string $tags_line строка с тегами
 *
 * @return array Масссив с тегами
 */
function tags_to_array($tags_line)
{
    $tags_line = anti_xss($tags_line);
    $tags_line = trim($tags_line);
    $tags_line = mb_strtolower($tags_line);
    $tags = explode(" ", $tags_line);

    return array_unique($tags, SORT_STRING);
}

/**
 * Функция проверяет введенный адрсе электронной почты на соответствие формату и на наличие такого адреса в базе данных
 * @param mysqli $link
 * @param string $email адрес электронной почты
 *
 * @return string Ошибки если валидация не прошла
 */
function email_validation($link, $email)
{
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return 'Введите корректный адрсе электронной почты. Пример: email@example.com';
    }
    $search_sql = "SELECT u.* FROM users u WHERE u.email = '$email'";
    $found_user = get_data($link, $search_sql);
    if (!empty($found_user)) {
        return 'Пользователь с таким адрессом электронной почты уже зарагестрирован. Возможно это вы. Попробуйте войти';
    }
}

/**
 * Функция проверяет введенный логин. На соответствие требованиям по длинне и по наличию такого же логина в базе данных
 * @param mysqli $link
 * @param string $login Введенный логин
 *
 * @return string Ошибки если валидация не прошла
 */
function login_validation($link, $login)
{
    $chek_lenght = validate_lenght($login, 3, 20);
    if ($chek_lenght !== null) {
        return $chek_lenght;
    }
    if (preg_match('/[^a-zа-я0-9]+/msiu', $login)) {
        return 'Логин должен состоять только из букв английского или русского алфавита и цифр';
    }

    $search_sql = "SELECT u.* FROM users u WHERE u.login = '$login'";
    $found_user = get_data($link, $search_sql);
    if (!empty($found_user)) {
        return "Логин: $login уже занят";
    }
}

/**
 * Функция сравнивает два значения
 * @param string | int $value_one
 * @param string | int $value_two
 *
 * @return string Ошибку если не равны
 */
function compare_values($value_one, $value_two)
{
    if ($value_one !== $value_two) {
        return 'Пароли не совпадают';
    }
}
