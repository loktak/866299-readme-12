<?php

function include_template($name, $data)
{
    $name = 'templates/' . $name;
    $result = '';

    if (!file_exists($name)) {
        return $result;
    }

    ob_start();
    extract($data);
    require $name;

    $result = ob_get_clean();

    return $result;
}

/** 
 *The function cuts the text and adds a link to the full text if required 

 *The function takes two values: text ($text) and the maximum number of symbols($symbols).
 *1.Splitting the text into separate words and entering them in the $words array
 *   The "strlen" function counts the number of characters in each word and sums them in the variable $text_length
 *   Each calculated value is added to the $cropped_text array
 *   The loop stops working if $text_length >= $symbols
 *2.Using the "implode" function, we get the cropped text from the $cropping_text array and write it to the $text variable
 *3.The $post_text variable is responsible for displaying text in HTML code. In it, we know the tags and the actual text itself from the $text variable
 *4.The $post_full_text_link variable is responsible for displaying a link to the full text, if it was cropped. We enter the html code of the link in it
 *5 Entering a condition
 *   If the value of $text_length is greater than $symbols, add a colon at the end of the line and a link from $post_full_text_link to the entire text.
 *   If the value of $text_length is less than $symbols, just print the $post_text variable


 * @param string $text
 * @param int $symbols
 * @return string
 * @author Arseny Spirin <spirinars@ya.ru>
 */
function crop_text($text, $symbols = 300)
{

    $words = explode(" ", $text);

    $text_lenght = 0;

    foreach ($words as $word) {
        $text_lenght = $text_lenght + mb_strlen($word);
        $cropped_text[] = $word; // изначально я так и писал, но по какой-то причине, выводилось в массиве только последнее слово из всего текста. Вероятно ошибка была где-то еще.
        if ($text_lenght >= $symbols) {
            break;
        }
    };

    $text = implode(" ", $cropped_text);

    $post_text = "<p>" . $text . "</p>";

    if ($text_lenght > $symbols) {
        $text .= "...";
        $post_full_text_link = '<a class="post-text__more-link" "href="#">Читать далее</a>';
        $post_text = "<p>" . $text . "</p>" . $post_full_text_link;
        print($post_text);
    } else {
        print($post_text);
    }
}

/** 
 *This function replaces special characters with mnemonic characters 
 *
 * @param string $user_content
 * @return string
 * @author Arseny Spirin <spirinars@ya.ru>
 */
function anti_xss($user_content)
{
    return htmlspecialchars($user_content, ENT_QUOTES);
}

/** 
 *The function determines how much time has passed since the post was created and outputs the corresponding value
 *
 * @param Datetime $post_upload_time
 * @return string
 * @author Arseny Spirin <spirinars@ya.ru>
 */

function time_ago($post_upload_time)
{
    $current_time = new DateTime('now');
    $interval = $post_upload_time->diff($current_time);

    $months = $interval->format('%m');
    $days = $interval->format('%d');
    $hours = $interval->format('%H');
    $minutes = $interval->format('%i');
    $years = $interval->format('%Y');
    $ago = 0;

    if ($years != 0) {
        $years = floor($years);
        $ago = $years . ' ' . plural_form($years, array('год', 'года', 'лет')) . ' назад';
    } elseif ($months != 0) {
        $months = floor($months);
        $ago = $months . ' ' . plural_form($months, array('месец', 'месеца', 'месецев')) . ' назад';
    } elseif ($days > 7 && $days < 35) {
        $week = floor($days / 7);
        $ago = $week . ' ' . plural_form($week, array('неделю', 'недели', 'недель')) . ' назад';
    } elseif ($days != 0) {
        $ago = $days . ' ' . plural_form($days, array('день', 'дня', 'дней')) . ' назад';
    } elseif ($hours != 0) {
        $hours = floor($hours);
        $ago = $hours . ' ' . plural_form($hours, array('час', 'часа', 'часов')) . ' назад';
    } elseif ($minutes != 0) {
        $ago = $minutes . ' ' . plural_form($minutes, array('минуту', 'минуты', 'минут')) . ' назад';
    } else {
        $ago = 'меньше минуты назад';
    }

    return $ago;
}

/** 
 *The function declines the existing ones in accordance with the numerals
 *
 * @param int $n
 * @param array $forms
 * @return string
 * @author Arseny Spirin <spirinars@ya.ru>
 */
function plural_form($n, $forms)
{
    return $n % 10 == 1 && $n % 100 != 11 ? $forms[0] : ($n % 10 >= 2 && $n % 10 <= 4 && ($n % 100 < 10 || $n % 100 >= 20) ? $forms[1] : $forms[2]);
}


/** 
 * Генерирует случаную дату для поста
 */
function get_post_time($index)
{
    $random_date = generate_random_date($index);
    $post_date = new DateTime($random_date);
    return $post_date;
}

/**
 * Подключаемся к базе данных и проверяем есть подключение или нет.
 * @param string $host     Наименование локального хоста.
 * @param string $user     Имя пользователя БД
 * @param string $password Пароль пользователя БД
 * @param string $database Имя БД
 *
 * @return mysqli
 */
function database_conecting($host, $user, $password, $database)
{
    $link = mysqli_connect($host, $user, $password, $database);
    if ($link === false) {
        die("Ошибка подключения: " . mysqli_connect_error());
    }
    mysqli_set_charset($link, "utf8");
    return $link;
}

/**
 * Функция берет данные из запроса sql и возвращает двумерный массив
 * @param mysqli $link
 * @param string $sql запрос в базу данных
 * 
 * @return array двумерный массив данных
 */
function get_data($link, $sql)
{
    $result = mysqli_query($link, $sql);
    if (!$result) {
        $error = mysqli_error($link);
        die("Ошибка MySQL: " . $error);
    }
    $result = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $result;
}

/**
 * Функция вызывает список постов сортированных по популярности (по умолчанию) с указанием автора поста на главную страницу
 * @param mysqli $link
 * @param string $sort_value Выбор по какому параметру сортировать
 * @param string $sorting сортировка по возрастанию или убыванию
 * @return array двумерный массив данных
 */
function popular_posts($link, $sort_value = 'views', $sorting = ' DESC')
{
    $sql = "
    SELECT p.*, ct.icon_type, u.avatar, u.login AS author_login, IFNULL(l.likes, 0) AS likes, IFNULL(com.comments, 0) AS comments_value
    FROM posts p
    JOIN users u ON p.user_id = u.id
    JOIN content_type ct ON p.type_id = ct.id
    LEFT JOIN (SELECT l.post_id, COUNT(*) AS likes FROM likes l GROUP BY l.post_id) AS l ON l.post_id = p.id
    LEFT JOIN (SELECT com.post_id, COUNT(*) AS comments FROM comments com GROUP BY com.post_id) AS com ON com.post_id = p.id
    ORDER BY $sort_value $sorting
    ";
    return get_data($link, $sql);
}


/**
 * Функция вызывает список постов сортированных по популярности с указанием автора поста на главную страницу.
 * @param mysqli $link
 * @param string $type Выбор типа поста
 * @param string $sort_value Выбор по какому параметру сортировать
 * @param string $sorting сортировка по возрастанию или убыванию
 * 
 * @return array двумерный массив данных
 */
function popular_posts_category_sorting($link, $type, $sort_value = 'views', $sorting = ' DESC')
{
    $sql = "SELECT p.*, ct.icon_type, u.avatar, u.login AS author_login, IFNULL(l.likes, 0) AS likes, IFNULL(com.comments, 0) AS comments_value
    FROM posts p
    JOIN users u ON p.user_id = u.id
    JOIN content_type ct ON p.type_id = ct.id
    LEFT JOIN (SELECT l.post_id, COUNT(*) AS likes FROM likes l GROUP BY l.post_id) AS l ON l.post_id = p.id
    LEFT JOIN (SELECT com.post_id, COUNT(*) AS comments FROM comments com GROUP BY com.post_id) AS com ON com.post_id = p.id
    WHERE ct.icon_type = '$type'
    ORDER BY $sort_value $sorting";
    return get_data($link, $sql);
}

/**
 * Функция вызывает список категорий
 * @param mysqli $link
 * 
 * @return array двумерный массив данных
 */
function posts_categories($link)
{
    $sql = 'SELECT * FROM content_type';
    return get_data($link, $sql);
}

/**
 * Функция вызывает информацию по по посту используя его id.
 * @param mysqli $link
 * @param string $post_id id поста
 * 
 * @return array двумерный массив данных
 */
function get_post_info($link, $post_id)
{
    $sql = "SELECT p.*, ct.icon_type, u.avatar, u.login AS author_login, 
    IFNULL((SELECT COUNT(*) FROM likes l WHERE l.post_id = p.id), 0) AS likes,
    IFNULL((SELECT COUNT(*) FROM comments com WHERE com.post_id = p.id), 0) AS comments_count,
    IFNULL((SELECT COUNT(*) FROM subscriptions sub WHERE sub.userto_id = p.user_id), 0) AS subscribers
    FROM posts p
   JOIN users u ON p.user_id = u.id
   JOIN content_type ct ON p.type_id = ct.id
   LEFT JOIN likes l ON l.post_id = p.id
   LEFT JOIN comments com ON l.post_id = p.id
   LEFT JOIN subscriptions sub ON sub.userto_id = p.user_id
   WHERE p.id = $post_id
   GROUP BY l.post_id";
    $result = get_data($link, $sql);

    return empty($result) ? NULL : $result;
}

/**
 * Функция список постов одного пользователя по id пользователя.
 * @param mysqli $link
 * @param string $user_id id пользователя
 * 
 * @return array двумерный массив данных
 */
function get_user_posts_count($link, $user_id)
{
    $sql = "SELECT p.id FROM posts p WHERE user_id = $user_id";
    return get_data($link, $sql);
}

/**
 * Функция создает url страницы с учетом уже существующих GET запросов
 * @param string $type
 * @param string $sort_value
 * @param string $sorting
 *
 * @return array двумерный массив данных
 */
function set_url($type, $sort_value, $sorting, $page_url = "index.php")
{
    $params = $_GET;

    $params['type'] = $type;
    $params['sort_value'] = $sort_value;
    $params['sorting'] = $sorting;
    $querry = http_build_query($params);
    $url = "/" . $page_url . "?" . $querry;
    return $url;
}

/**
 * Функция создает значение на основании того есть ли такое значение в POST запросе
 * @param string $name 
 * 
 * @return string значение из POST запроса
 */
function getPostValue($name)
{
    return $_POST[$name] ?? "";
}


//* Функция проверяет что такое видео есть на youtube и оно доступно
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
        if (empty($errors[$key])) {
            if (isset($rules[$key])) {
                $rule = $rules[$key];
                $errors[$key] = $rule();
            }
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
    $tags_array = [];
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
        if ($mime_type !== 'image/png' && $mime_type !== 'image/jpeg' && $mime_type !== 'image/gif') {
            return "Не подходящий формат изображения. Используйте jpg, png или gif";
        } else {
            file_put_contents($file_path, file_get_contents($url));
        }
    } else {
        return 'Файл по данной ссылке не найден';
    }
}

/** Функция проверяет файл загруженный через форму обратной связи. и если он соответствует критериям загружает его в папку uploads
 * @param array $files массив данных о файле
 * 
 * @return string Ошибку если валидация не прошла
 */
function upload_post_picture($files)
{
    if (($files['picture']['size'] < 5242880)) {
        $file_name = $files['picture']['name'];
        $file_path = __DIR__ . '/uploads/';
        if ($files['picture']['type'] !== 'image/png' && $files['picture']['type'] !== 'image/jpeg' && $files['picture']['type'] !== 'image/gif') {
            return 'Не подходящий формат прикрепленного изображения. Используйте jpg, png или gif. или воспользуйтесь ссылкой';
        } else {
            move_uploaded_file($files['picture']['tmp_name'], $file_path . $file_name);
        }
    } else {
        return 'прикрепленный файл слишком большой';
    }
}

/** Функция определяет как будет выглядеть путь до загруженного файла в зависимости от того был ли он загружен через форму или ссылкой
 * @param string $url ссылка на файл
 * @param array $files массив данных о файле
 * 
 * @return string $file_path путь до файла
 */
function get_file_path($url, $files)
{
    if (!empty($files['picture']['name'])) {
        $file_name = $files['picture']['name'];
    } else {
        $file_name = basename($url);
    }
    $file_path = "uploads/" . $file_name;
    return $file_path;
}


/** Функция выводит русское название в соответствии со значением анлийкого
 * @param string $text
 * 
 * @return string $russian_form_name название на русском
 */

function set_russian_form_name($text)
{
    switch ($text) { //делаем нормальные имена вкладкам
        case 'photo':
            $russian_form_name = 'изображения';

            break;
        case 'video':
            $russian_form_name = 'видео';
            break;
        case 'text':
            $russian_form_name = 'текстового поста';
            break;
        case 'link':
            $russian_form_name = 'ссылки';
            break;
        case 'quote':
            $russian_form_name = 'цитаты';
    }
    return $russian_form_name;
}


/**
 * Добавления поста вместе с тегами если есть.
 * @param mysqli $link
 * @param string $tags_line строчка с тегами
 * @return mysqli_stmt $stml Подготовленное выражение
 * 
 * @return string $post_id айди поста или ошибку
 */
function add_post_to_db($link, $tags_line, $stml)
{
    
    $tag_sql = 'INSERT INTO hashtags (title) VALUES (?)';
    $result = mysqli_stmt_execute($stml);
    if ($result) {
        $post_id = mysqli_insert_id($link);
    }
    if ($tags_line !== NULL) {
        $tags_line = anti_xss($tags_line);
        $tags_line = trim($tags_line);
        $tags_line = mb_strtolower($tags_line);
        $tags = explode(" ", $tags_line);
        foreach ($tags as $tag) { 
            $search_sql = "SELECT h.id FROM hashtags h WHERE h.title = '$tag'";
            $search_result = get_data($link, $search_sql)[0];
            if (!empty($search_result)) {
                $tag_id = $search_result['id'];
            } else {
                $values['title'] = $tag;
                $tag_stml = db_get_prepare_stmt($link, $tag_sql, $values);
                $result = mysqli_stmt_execute($tag_stml);
                $tag_id = mysqli_insert_id($link);
            }
            $tag_post_sql = "INSERT INTO hashtags_posts (tag_id, post_id) VALUES ($tag_id, $post_id)";
            $tag_post_stml = mysqli_prepare($link, $tag_post_sql);
            $result = mysqli_stmt_execute($tag_post_stml);
        }
    }
    if ($result) {
        return $post_id;
    } else {
        return 'Что-то пошло не так';
    }
    
}

