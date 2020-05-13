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
 * @param $post_id айди поста для ссылки
 * @param int $symbols
 * 
 * 
 * @return string
 * @author Arseny Spirin <spirinars@ya.ru>
 */
function crop_text($text, $post_id, $symbols = 300)
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
        $post_full_text_link = '<a class="post-text__more-link" href="post.php?post_id=' . $post_id . '">Читать далее</a>';
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
 * Функция вызывает список постов от пользователь на которых подписан юзер и сортирует их по дате
 * @param mysqli $link
 * @param int $user_id айди юзера
 * 
 * @return array двумерный массив данных
 */
function get_posts_for_feed($link, $user_id)
{
    $sql = "SELECT DISTINCT p.*, u.login AS author_login, ct.icon_type AS type, u.avatar,
    IFNULL((SELECT COUNT(*) FROM likes l WHERE l.post_id = p.id), 0) AS likes,
    IFNULL((SELECT COUNT(*) FROM comments com WHERE com.post_id = p.id), 0) AS comments_count
    FROM posts p
    JOIN subscriptions sub ON sub.userto_id = p.user_id
    JOIN users u ON u.id = p.user_id
    JOIN content_type ct ON ct.id = p.type_id
    WHERE sub.user_id = $user_id
    ORDER BY p.post_date DESC";

    return get_data($link, $sql);
}

/**
 * Функция вызывает список постов от пользователь на которых подписан + учитывает тип поста юзер и сортирует их по дате
 * @param mysqli $link
 * @param int $user_id айди юзера
 * @param string название категории
 * 
 * @return array двумерный массив данных
 */
function get_posts_for_feed_by_category($link, $user_id, $category)
{
    $sql = "SELECT DISTINCT p.*, u.login AS author_login, ct.icon_type AS type, u.avatar,
    IFNULL((SELECT COUNT(*) FROM likes l WHERE l.post_id = p.id), 0) AS likes,
    IFNULL((SELECT COUNT(*) FROM comments com WHERE com.post_id = p.id), 0) AS comments_count
    FROM posts p
    JOIN subscriptions sub ON sub.userto_id = p.user_id
    JOIN users u ON u.id = p.user_id
    JOIN content_type ct ON ct.id = p.type_id
    WHERE sub.user_id = $user_id AND ct.icon_type = '$category'
    ORDER BY p.post_date DESC";

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
 * Функция вызывает список комментариев к определенному посту
 * @param mysqli $link
 * @param int $post_id айди поста
 * 
 * @return array двумерный массив данных
 */
function get_post_comments($link, $post_id)
{
    $sql = "SELECT com.*, u.login AS author, u.avatar
    FROM comments com
    JOIN posts p ON p.id = com.post_id
    JOIN users u ON u.id = com.user_id
    WHERE com.post_id = $post_id
    ORDER BY comment_date ASC";

    return get_data($link, $sql);
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
function set_url($type, $sort_value, $sorting, $page_url = "popular.php")
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
 * @param string $name название поля по которому ищем
 * 
 * @return string значение из POST запроса
 */
function getPostValue($name)
{
    $result = $_POST[$name] ?? "";
    return anti_xss($result);
}

/** 
 * Функция принемает массив с тегами и добавляет их в базу данных. Если такой тег уже есть то выдает существующий id. функция возвращает массив с id тегов 
 * @param mysqli $link
 * @param array $tags подготовленный массив с тегами
 * 
 * @return array массив с данными
 */
function add_tags_to_db($link, $tags)
{
    $tag_sql = 'INSERT INTO hashtags (title) VALUES (?)';
    foreach ($tags as $tag) {
        $search_sql = "SELECT h.id FROM hashtags h WHERE h.title = '$tag'";
        $search_result = get_data($link, $search_sql)[0] ?? NULL;
        if (!empty($search_result)) {
            $tags_id[] = $search_result['id'];
        } else {
            $values['title'] = $tag;
            $tag_stml = db_get_prepare_stmt($link, $tag_sql, $values);
            $result = mysqli_stmt_execute($tag_stml);
            if ($result) {
                $tags_id[] = mysqli_insert_id($link);
            } else {
                return 'не удалось добавить теги' . mysqli_error($link);
            }
        }
    }
    return $tags_id;
}

/**
 * Функция добавляет пост в базу данных
 * @param mysqli $link
 * @param mysqli $stml подготовленное выражение
 * 
 * @return int id поста
 */
function add_post_to_db($link, $stml)
{
    $result = mysqli_stmt_execute($stml);
    if (!$result) {
        return 'не удалось добавить пост' . mysqli_error($link);
    }
    return mysqli_insert_id($link);
}

/** 
 * Добавления поста вместе с тегами
 * @param mysqli $link
 * @param array $tags массив с тегами
 * @param int $post_id айди добавленного поста
 * 
 * @return boolean $post_id айди поста или ошибку
 */
function add_tags_to_posts($link, $tags, $post_id)
{
    $tags_id = add_tags_to_db($link, $tags);
    foreach ($tags_id as $tag_id) {
        $tag_post_sql = "INSERT INTO hashtags_posts (tag_id, post_id) VALUES ($tag_id, $post_id)";
        $tag_post_stml = mysqli_prepare($link, $tag_post_sql);
        $result = mysqli_stmt_execute($tag_post_stml);
        if (!$result) {
            return 'ошбика' . mysqli_error($link);
            break;
        }
    }
    return $result;
}



/** 
 * Функция выводит русское название в соответствии со значением английкого
 * @param string $text
 * 
 * @return string $russian_form_name название на русском
 */

function get_russian_form_name($text)
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
 * Функция получает данные о пользователе по email'у
 * @param mysqli $link
 * @param string $email адрес почты
 * 
 * @return array массив с данными о пользователе
 */
function get_user_data_by_email($link, $email)
{
    $sql = "SELECT * FROM users u WHERE u.email = '$email'";
    $result = get_data($link, $sql);

    return empty($result) ? NULL : $result[0];
}

/**
 * Функция получает значения тега title из сайта по ссылке
 * @param string $url адрес сайта
 * 
 * @return string Содержимое тега title из указаного сайта
 */
function get_link_title($url)
{
    $site = file_get_contents($url);
    if (preg_match('/<title>([^<]*)<\/title>/', $site, $matches) == 1) {
        return $matches[1];
    } 
}

/**
 * Функция добавляет просмотр к посту
 * @param mysqli $link
 * $param $post_id айди поста в котором надо прибавить просмотр
 * 
 * @return ошибку если будет
 */
function plus_view($link, $post_id) {
    $sql= "UPDATE posts SET views=views+1 WHERE id=$post_id";
    $stml = db_get_prepare_stmt($link, $sql);
    $result = mysqli_stmt_execute($stml);
    if (!$result) {
        return 'ошбика' . mysqli_error($link);
    }
}