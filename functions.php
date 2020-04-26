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
