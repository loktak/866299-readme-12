<?php

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
    IFNULL((SELECT COUNT(*) FROM comments com WHERE com.post_id = p.id), 0) AS comments_count,
    IFNULL((SELECT COUNT(*) FROM posts post WHERE post.original_id = p.id), 0) AS reposts
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
    IFNULL((SELECT COUNT(*) FROM comments com WHERE com.post_id = p.id), 0) AS comments_count,
    IFNULL((SELECT COUNT(*) FROM posts post WHERE post.original_id = p.id), 0) AS reposts
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
    IFNULL((SELECT COUNT(*) FROM subscriptions sub WHERE sub.userto_id = p.user_id), 0) AS subscribers,
    IFNULL((SELECT COUNT(*) FROM posts post WHERE post.original_id = p.id), 0) AS reposts
    FROM posts p
   JOIN users u ON p.user_id = u.id
   JOIN content_type ct ON p.type_id = ct.id
   WHERE p.id = $post_id";
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
 * Функция вызывает данные поста по id поста.
 * @param mysqli $link
 * @param string $post_id id пользователя
 * 
 * @return array двумерный массив данных
 */
function get_post_by_id($link, $post_id)
{
    $sql = "SELECT p.* FROM posts p WHERE id = $post_id";
    return get_data($link, $sql)[0];
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
 * Функция получает список постов в которых есть поисковой запрос и сортирует их по релевантности репосты игнорируются
 * @param mysqli $link
 * @param string $text сам запрос
 * 
 * @return array массив с данными о пользователе
 */
function search_text_in_posts($link, $text) {
    $sql = "SELECT DISTINCT p.*, u.login AS author, u.avatar, ct.icon_type AS type,
    MATCH(p.title, p.content_text, p.quote_author) AGAINST('$text') as score,
    IFNULL ((SELECT COUNT(*) FROM likes l WHERE l.post_id = p.id), 0) AS likes,
    IFNULL ((SELECT COUNT(*) FROM comments com WHERE com.post_id = p.id), 0) AS comments
    FROM posts p
    JOIN users u ON u.id = p.user_id
    JOIN content_type ct ON ct.id = p.type_id 
    WHERE MATCH(p.title, p.content_text, p.quote_author) 
    AGAINST ('$text' IN BOOLEAN MODE) AND p.original_id is NULL
    ORDER BY score DESC";

    return get_data($link, $sql);
}

/**
 * Функция получает список постов у которых есть определенный хэштег и сортирует их по дате добавления репосты игнорируются
 * @param mysqli $link
 * @param string $hashtag хэштег
 * 
 * @return array массив с данными о пользователе
 */
function search_hastags_on_posts($link, $hashtag) {
    $sql = "SELECT DISTINCT p.*, u.login AS author, u.avatar, ct.icon_type AS type,
    IFNULL ((SELECT COUNT(*) FROM likes l WHERE l.post_id = p.id), 0) AS likes,
    IFNULL ((SELECT COUNT(*) FROM comments com WHERE com.post_id = p.id), 0) AS comments
    FROM posts p
    JOIN users u ON u.id = p.user_id
    JOIN content_type ct ON ct.id = p.type_id 
    JOIN hashtags_posts hp ON hp.post_id = p.id
    JOIN hashtags h ON h.id = hp.tag_id
    WHERE h.title LIKE '%$hashtag%' 
    ORDER BY p.post_date DESC";

    return get_data($link, $sql);
}