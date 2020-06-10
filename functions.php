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
 * @param string $ago_text
 * 
 * @return string
 */

function time_ago($post_upload_time, $ago_text = " назад")
{
    $interval = $post_upload_time->diff(new DateTime('now'));

    $months = (int) $interval->format('%m');
    $days = (int) $interval->format('%d');
    $hours = (int) $interval->format('%H');
    $minutes = (int) $interval->format('%i');
    $years = (int) $interval->format('%Y');
    $ago = 0;

    if ($years !== 0) {
        $years = floor($years);
        $ago = $years . ' ' . plural_form($years, array('год', 'года', 'лет')) . $ago_text;
    } elseif ($months !== 0) {
        $months = floor($months);
        $ago = $months . ' ' . plural_form($months, array('месец', 'месеца', 'месецев')) . $ago_text;
    } elseif ($days > 7 && $days < 35) {
        $week = floor($days / 7);
        $ago = $week . ' ' . plural_form($week, array('неделю', 'недели', 'недель')) . $ago_text;
    } elseif ($days !== 0) {
        $ago = $days . ' ' . plural_form($days, array('день', 'дня', 'дней')) . $ago_text;
    } elseif ($hours !== 0) {
        $hours = floor($hours);
        $ago = $hours . ' ' . plural_form($hours, array('час', 'часа', 'часов')) . $ago_text;
    } elseif ($minutes !== 0) {
        $ago = $minutes . ' ' . plural_form($minutes, array('минуту', 'минуты', 'минут')) . $ago_text;
    } else {
        $ago = 'меньше минуты назад';
    }

    return $ago;
}

/** 
 *The function declines the existing ones in accordance with the numerals
 * @param int $n
 * @param array $forms
 * 
 * @return string
 */
function plural_form($n, $forms)
{
    return $n % 10 === 1 && $n % 100 !== 11 ? $forms[0] : ($n % 10 >= 2 && $n % 10 <= 4 && ($n % 100 < 10 || $n % 100 >= 20) ? $forms[1] : $forms[2]);
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
 * @return mysqli ошибку если будет
 */
function plus_view($link, $post_id)
{
    $sql = "UPDATE posts SET views=IFNULL(views, 0)+1 WHERE id=$post_id";
    $is_succsess = mysqli_stmt_execute(db_get_prepare_stmt($link, $sql));
    if (!$is_succsess) {
        return 'ошбика' . mysqli_error($link);
    }
}

/**
 * Функция проверяет заданное имя и если оно состоит из нескольких слов добавляет <br>
 * @param string $name
 * 
 * @return string $name_with_br
 */
function get_profile_name_with_br($name)
{
    return implode('<br>', explode(" ", $name));
}

/**
 * Функция возвращает дату последнего сообщения в нужном формате
 * @param Datetime $post_upload_time
 * 
 * @return string
 */
function last_message_date($post_upload_time)
{
    $interval = $post_upload_time->diff(new DateTime('now'));

    $month_name = [
        '01' => 'Янв',
        '02' => 'Фев',
        '03' => 'Мар',
        '04' => 'Апр',
        '05' => 'Мая',
        '06' => 'Июн',
        '07' => 'Июл',
        '08' => 'Авг',
        '09' => 'Cент',
        '10' => 'Окт',
        '11' => 'Нояб',
        '12' => 'Дек'
    ];

    $months = (int) $interval->format('%m');
    $days = (int) $interval->format('%d');

    $years = (int) $interval->format('%Y');

    if ($years !== 0) {
        return $post_upload_time->format('Y г');
    } elseif ($days !== 0 || $months !== 0) {
        $month = $post_upload_time->format('m');
        return $post_upload_time->format("d {$month_name[$month]}");
    }
    return  $post_upload_time->format('H:i');
}
