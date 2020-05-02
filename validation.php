<?php

/** Функция проверяет что такое видео есть на youtube и оно доступно
 * @param string $youtube_url
 * 
 * @return string $result
 */
function chek_video_url($youtube_url)
{
  $filtred_url = filter_var($youtube_url);
  if ($filtred_url != NULL) {
    $result = check_youtube_url($filtred_url);
  } else {
    $result = 'ошибка';
  }
  return $result;
}


/** Функция проверяет заполнены ли поля формы по указаным ключам
 * @param array $required_fields
 * 
 * @return array массив данных
 */
function not_empty($required_fields)
{
  $errors = [];
  foreach ($required_fields as $key => $field) {
    if (empty($_POST[$field])) {
      $errors[$field] = "Поле должно быть заполнено";
    }
  }
  return $errors;
}

/** Функция проверяет ошибки по соответствующим ключам и записывает их в массив
 * @param array $rules массив со значениями которые надо проверить
 * @param array $errors массив с уже существующими ошибками
 * 
 * @return array массив данных с ошибками
 */
function check_rules($rules, $errors)
{
  foreach ($_POST as $key => $value) {
    if (empty($errors[$key]) && isset($rules[$key])) {
      $rule = $rules[$key];
      $errors[$key] = $rule();
    }
  }
  return $errors;
}

/** Функция поле тэги на соответсвтие тз
 * @param string $tags строчка тегов
 * 
 * @return string Ошибку если валидация не прошла
 */
function check_tags($tags)
{
  $tags_array = explode(" ", $tags);
  if (preg_match('/[^a-zа-я ]+/msiu', $tags)) {
    return 'Теги должны состоять только из букв.';
  } else {
    foreach ($tags_array as $tag) {
      if (mb_strlen($tag) > 20) {
        return 'Используется слишком длинный тег. Подберите синоним или убедитесь что тег состоит из одного слова';
      }
    }
  }
}

/** Функция проверяет текст на колличество символов в нем, и выводит сообщение если проверка не прошла
 * @param string $text сам текст
 * @param int $min минимальное значение символов
 * @param int $max максимальное значение символов
 * 
 * @return string Ошибку если валидация не прошла
 */
function validate_lenght($text, $min = 3, $max = 25)
{
  if (mb_strlen($text) < $min || mb_strlen($text) > $max) {
    return "Значение поля должно быть не меньше $min и не больше $max символов";
  }
}

/** Функция проверяет ссылку с помощью filter_var
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

/** Функция проверяет доступно ли видео по ссылке на youtube
 * @param string $url ссылка на видео
 * 
 * @return string Ошибку если валидация не прошла
 */
function check_youtube_link($url)
{
  $id = extract_youtube_id($url);
  $headers = get_headers('https://www.youtube.com/oembed?format=json&url=http://www.youtube.com/watch?v=' . $id);
  if (is_array($headers)) {
    preg_match('/^HTTP\\/\\d+\\.\\d+\\s+2\\d\\d\\s+.*$/', $headers[0]);
    $err_flag = strpos($headers[0], '200') ? '200' : '404';
  }
  if ($err_flag != 200) {
    return "Видео по такой ссылке не найдено. Проверьте ссылку на видео";
  }
}

/** Функция проверяет файл по ссылке. и если он соответствует критериям загружает его в папку uploads
 * @param string $url ссылка на сайт
 * 
 * @return string Ошибку если валидация не прошла
 */
function get_img_by_link($url)
{
  if (file_get_contents($url)) {  //@question даже валидная ссылка, если она не содержит в себе файла будет вызывать варнинг. как от него избавится без использования запрещенных @. Такая проверка обязаетльна по ТЗ
    $file_name = basename($url);
    $file_path = __DIR__ . "/uploads/" . $file_name;
    $file_info = new finfo(FILEINFO_MIME_TYPE);

    $mime_type = $file_info->buffer(file_get_contents($url));
    $valid_mime_types = ['image/png', 'image/jpeg', 'image/gif'];
    if (!in_array($mime_type, $valid_mime_types)) {
      return "Не подходящий формат изображения. Используйте jpg, png или gif";
    } else {
      file_put_contents($file_path, file_get_contents($url));
    }
  } else {
    return 'Файл по данной ссылке не найден';
  }
}

/** Функция определяет путь до загруженного файла основоваясь на том существует имя файла в массиве $_FILES или нет.
 * Она нужна что бы обойти ограничение по колличеству if. как это сделать оп другому я не знаю.
 * @param string $url
 * @param string $file_name
 * 
 * @return string путь до загруженного файла
 */
function get_file_path($url, $file_name)
{
  if ($file_name === NULL) {
    $file_name = basename($url);
  }
  return "uploads/" .$file_name;
}

/** Функция проверяет файл загруженный через форму обратной связи. и если он соответствует критериям загружает его в папку uploads
 * @param array $files массив данных о файле
 * 
 * @return string Ошибку если валидация не прошла
 */
function upload_post_picture($files)
{
  if (($files['picture']['size'] >= 104857600)) {
    return 'прикрепленный файл слишком большой';
  }
  $file_name = $files['picture']['name'];
  $file_path = __DIR__ . '/uploads/';
  $valid_mime_types = ['image/png', 'image/jpeg', 'image/gif'];
  if (!in_array($files['picture']['type'], $valid_mime_types)) {
    return 'Не подходящий формат прикрепленного изображения. Используйте jpg, png или gif. или воспользуйтесь ссылкой';
  } else {
    move_uploaded_file($files['picture']['tmp_name'], $file_path . $file_name);
  }
}

/** Функция принемает строку с тегами и возвращает массив без повторений
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
