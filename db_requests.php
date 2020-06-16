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
 * Функция вызывает список постов сортированных по популярности (по умолчанию)
 * с указанием автора поста на главную страницу
 * @param mysqli $link
 * @param string $sort_value Выбор по какому параметру сортировать
 * @param string $sorting сортировка по возрастанию или убыванию
 * @return array двумерный массив данных
 */
function popular_posts($link, $page_items, $offset, $sort_value = 'views', $sorting = ' DESC')
{
    $sql = "SELECT p.*, ct.icon_type, u.avatar, u.login AS author_login, IFNULL(l.likes, 0) AS likes, 
    IFNULL(com.comments, 0) AS comments_value
    FROM posts p
    JOIN users u ON p.user_id = u.id
    JOIN content_type ct ON p.type_id = ct.id
    LEFT JOIN (SELECT l.post_id, COUNT(*) AS likes FROM likes l GROUP BY l.post_id) AS l ON l.post_id = p.id
    LEFT JOIN (SELECT com.post_id, COUNT(*) AS comments FROM comments com GROUP BY com.post_id) AS com 
    ON com.post_id = p.id
    ORDER BY $sort_value $sorting
    LIMIT $page_items OFFSET $offset
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
function popular_posts_category_sorting($link, $type, $page_items, $offset, $sort_value = 'views', $sorting = ' DESC')
{
    $sql = "SELECT p.*, ct.icon_type, u.avatar, u.login AS author_login, IFNULL(l.likes, 0) AS likes, 
    IFNULL(com.comments, 0) AS comments_value
    FROM posts p
    JOIN users u ON p.user_id = u.id
    JOIN content_type ct ON p.type_id = ct.id
    LEFT JOIN (SELECT l.post_id, COUNT(*) AS likes FROM likes l GROUP BY l.post_id) AS l ON l.post_id = p.id
    LEFT JOIN (SELECT com.post_id, COUNT(*) AS comments FROM comments com GROUP BY com.post_id) AS com 
    ON com.post_id = p.id
    WHERE ct.icon_type = '$type'
    ORDER BY $sort_value $sorting
    LIMIT $page_items OFFSET $offset";

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
 * @param int $post_id id поста
 * @param int $profile_id id авторизованного пользователя
 *
 * @return array двумерный массив данных
 */
function get_post_info($link, $post_id, $profile_id)
{
    $sql = "SELECT p.*, ct.icon_type, u.avatar, u.login AS author_login, u.registration_date,
    IFNULL((SELECT COUNT(*) FROM likes l WHERE l.post_id = p.id), 0) AS likes,
    IFNULL((SELECT COUNT(*) FROM comments com WHERE com.post_id = p.id), 0) AS comments_count,
    IFNULL((SELECT COUNT(*) FROM subscriptions sub WHERE sub.userto_id = p.user_id), 0) AS subscribers,
    IFNULL((SELECT COUNT(*) FROM posts post WHERE post.original_id = p.id), 0) AS reposts,
    post.post_date AS original_date, us.login AS original_author_name, us.avatar AS original_author_avatar,
    IFNULL ((SELECT COUNT(*) FROM subscriptions sub WHERE sub.userto_id = p.user_id AND sub.user_id = $profile_id ), 0) 
    AS is_subscribed,
    IFNULL((SELECT COUNT(*) FROM posts post WHERE post.user_id = p.user_id), 0) AS user_posts
    FROM posts p
    JOIN users u ON p.user_id = u.id
    JOIN content_type ct ON p.type_id = ct.id
    LEFT JOIN users us ON us.id = p.original_author_id
    LEFT JOIN posts post ON post.id = p.original_id
   WHERE p.id = $post_id";
    $result = get_data($link, $sql);

    return empty($result) ? null : $result;
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
    ORDER BY comment_date DESC";

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
 * Функция принемает массив с тегами и добавляет их в базу данных.
 * Если такой тег уже есть то выдает существующий id. функция возвращает массив с id тегов
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
        $search_result = get_data($link, $search_sql)[0] ?? null;
        if (!empty($search_result)) {
            $tags_id[] = $search_result['id'];
        } else {
            $values['title'] = $tag;
            $result = mysqli_stmt_execute(db_get_prepare_stmt($link, $tag_sql, $values));
            if (!$result) {
                return 'не удалось добавить теги' . mysqli_error($link);
            }
            $tags_id[] = mysqli_insert_id($link);
        }
    }

    return $tags_id;
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
    $sql = "INSERT INTO hashtags_posts (tag_id, post_id) VALUES";
    foreach ($tags_id as $tag_id) {
        $sql .= " ($tag_id, $post_id),";
    }
    $result = mysqli_stmt_execute(mysqli_prepare($link, substr($sql, 0, -1)));
    if (!$result) {
        return 'ошбика' . mysqli_error($link);
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

    return empty($result) ? null : $result[0];
}


/**
 * Функция получает список постов в которых есть поисковой запрос и сортирует их по релевантности репосты игнорируются
 * @param mysqli $link
 * @param string $text сам запрос
 *
 * @return array массив с данными о пользователе
 */
function search_text_in_posts($link, $text)
{
    $sql = "SELECT DISTINCT p.*, u.login AS author, u.avatar, ct.icon_type AS type,
    MATCH(p.title, p.content_text, p.quote_author) AGAINST('$text*') as score,
    IFNULL ((SELECT COUNT(*) FROM likes l WHERE l.post_id = p.id), 0) AS likes,
    IFNULL ((SELECT COUNT(*) FROM comments com WHERE com.post_id = p.id), 0) AS comments
    FROM posts p
    JOIN users u ON u.id = p.user_id
    JOIN content_type ct ON ct.id = p.type_id
    WHERE MATCH(p.title, p.content_text, p.quote_author)
    AGAINST ('$text*' IN BOOLEAN MODE) AND p.original_id is NULL
    ORDER BY score DESC";

    return get_data($link, $sql);
}

/**
 * Функция получает список постов у которых есть определенный хэштег
 * и сортирует их по дате добавления репосты игнорируются
 * @param mysqli $link
 * @param string $hashtag хэштег
 *
 * @return array массив с данными о пользователе
 */
function search_hastags_on_posts($link, $hashtag)
{
    $sql = "SELECT DISTINCT p.*, u.login AS author, u.avatar, ct.icon_type AS type,
    IFNULL ((SELECT COUNT(*) FROM likes l WHERE l.post_id = p.id), 0) AS likes,
    IFNULL ((SELECT COUNT(*) FROM comments com WHERE com.post_id = p.id), 0) AS comments
    FROM posts p
    JOIN users u ON u.id = p.user_id
    JOIN content_type ct ON ct.id = p.type_id
    JOIN hashtags_posts hp ON hp.post_id = p.id
    JOIN hashtags h ON h.id = hp.tag_id
    WHERE h.title LIKE '%$hashtag%' AND p.original_id is NULL
    ORDER BY p.post_date DESC";

    return get_data($link, $sql);
}

/**
 * Функция получает список постов одного автора и сортирует их по дате добавления
 * @param mysqli $link
 * @param int $author_id id автора
 *
 * @return array массив с данными о пользователе
 */
function get_posts_by_author_id($link, $author_id)
{
    $sql = "SELECT DISTINCT p.*, u.login AS author_login, ct.icon_type AS type, u.avatar,
    IFNULL((SELECT COUNT(*) FROM likes l WHERE l.post_id = p.id), 0) AS likes,
    IFNULL((SELECT COUNT(*) FROM comments com WHERE com.post_id = p.id), 0) AS comments_count,
    IFNULL((SELECT COUNT(*) FROM posts post WHERE post.original_id = p.id), 0) AS reposts,
    post.post_date AS original_date, us.login AS original_author_name, us.avatar AS original_author_avatar
    FROM posts p
    JOIN users u ON u.id = p.user_id
    JOIN content_type ct ON ct.id = p.type_id
    LEFT JOIN users us ON us.id = p.original_author_id
    LEFT JOIN posts post ON post.id = p.original_id
    WHERE p.user_id = $author_id
    ORDER BY p.post_date DESC";

    return get_data($link, $sql);
}

/**
 * Функция получает информацию о пользователе и проверяет подписан ли залогиненный пользователь на него
 * @param mysqli $link
 * @param int $profile_id id пользователя
 * @param int $user_id id залогиненого пользователя
 *
 * @return array массив с данными о пользователе
 */
function get_profile_data($link, $profile_id, $user_id)
{
    $sql = "SELECT DISTINCT u.id, u.login, u.avatar, IFNULL(COUNT(p.user_id), 0) AS user_posts,
    u.registration_date,
    IFNULL((SELEct COUNT(*) FROM subscriptions sub WHERE sub.userto_id = u.id), 0) AS user_subs,
    IFNULL((SELEct COUNT(*) FROM subscriptions subs WHERE subs.userto_id = u.id AND subs.user_id = $user_id), 0) 
    AS is_subscribed
    FROM users u
    JOIN posts p ON p.user_id = u.id
    WHERE u.id = $profile_id";

    return get_data($link, $sql)[0];
}


/**
 * Функция получает список хэштегов для поста по id
 * @param mysqli $link
 * @param int $post_id id поста
 *
 * @return array массив с данными о пользователе
 */
function get_hashtags_for_post($link, $post_id)
{
    $sql = "SELECT h.id, h.title
    FROM hashtags h
    JOIN hashtags_posts hp ON hp.tag_id = h.id
    WHERE hp.post_id = $post_id";

    return get_data($link, $sql);
}

/**
 * Функция получает список тех кто поставил лайки определенному пользователю
 * @param mysqli $link
 * @param int $user_id id пользователя
 *
 * @return array массив с данными о пользователе
 */
function get_user_likes($link, $user_id)
{
    $sql = "SELECT p.id, p.img, p.video, l.like_date, u.login, u.avatar, l.user_id, ct.icon_type AS 'type'
    FROM posts p
    JOIN likes l ON l.post_id = p.id
    JOIN users u ON u.id = l.user_id
    JOIN content_type ct ON ct.id = p.type_id
    WHERE p.user_id = $user_id
    ORDER BY l.like_date DESC";

    return get_data($link, $sql);
}

/**
 * Функция получает список тех кто подписан на пользователя и проверяет подписан ли на них активный пользователь
 * @param mysqli $link
 * @param int $profile_id пользователя
 * @param int $user_id id авторизованного пользователя
 *
 * @return array массив с данными о пользователе
 */
function get_subscribers($link, $profile_id, $user_id)
{
    $sql = "SELECT DISTINCT u.id, u.login, u.avatar, u.registration_date AS 'date',
    IFNULL(COUNT(subs.user_id), 0) AS subscribers,
    IFNULL((SELECT COUNT(*) FROM posts p WHERE p.user_id = u.id), 0) AS posts,
    IFNULL((SELECT COUNT(*) FROM subscriptions s WHERE s.userto_id = u.id AND s.user_id = $user_id),0) AS is_subscribed
    FROM users u
    JOIN subscriptions sub ON sub.user_id = u.id
    LEFT JOIN subscriptions subs ON subs.userto_id = u.id
    WHERE sub.userto_id = $profile_id
    GROUP BY u.id
    ORDER BY u.id DESC";

    return get_data($link, $sql);
}


/**
 * Функция получает список сообщений между двумя пользователями
 * @param mysqli $link
 * @param int $user_one id первого пользователя
 * @param int $user_two id второго пользователя
 *
 * @return array массив с данными о пользователе
 */
function get_chat_messages($link, $user_one, $user_two)
{
    $sql = "SELECT m.message_date AS 'date', m.content, u.avatar AS sender_avatar, 
    u.login AS sender_name, m.user_id AS sender_id
    FROM messages m
    JOIN users u ON u.id = m.user_id
    WHERE m.userto_id = $user_one AND m.user_id = $user_two OR m.userto_id = $user_two AND m.user_id = $user_one
    ORDER BY message_date ASC";

    return get_data($link, $sql);
}

/**
 * Вызывает список юзеров с кем у пользователя есть чат
 * @param mysqli $link
 * @param string $sql запрос в базу данных
 *
 * @return BOOLEAN
 */
function is_exist($link, $sql)
{
    $result = mysqli_query($link, $sql);

    if (!$result) {
        die('Ошибка MySQL: ' . mysqli_error($link));
    }

    return mysqli_num_rows($result) > 0;
}

/**
 * Вызывает список юзеров с кем у пользователя есть чат
 * @param mysqli $link
 * @param int $user_one id первого пользователя
 * @param int $user_two id второго пользователя
 *
 * @return BOOLEAN
 */
function is_interlocutor_exist($link, $user_one, $user_two)
{
    $sql = "SELECT i.*
    FROM interlocutors i
    WHERE i.sender_id = $user_one AND i.receiver_id = $user_two  OR i.sender_id = $user_two 
    AND i.receiver_id = $user_one";

    return is_exist($link, $sql);
}

/**
 * Вызывает список юзеров с кем у пользователя есть чат
 * @param mysqli $link
 * @param int $profile_id id первого пользователя
 *
 * @return array массив c собеседниками
 */
function get_interclutors($link, $profile_id)
{
    $sql = "SELECT i.*, u.login AS sender_name, us.login AS receiver_name, u.avatar AS sender_avatar, 
    us.avatar AS receiver_avatar, (SELECT m.content FROM messages m
    WHERE (m.user_id = i.sender_id AND m.userto_id = i.receiver_id OR m.user_id = i.receiver_id 
    AND m.userto_id = i.sender_id) AND m.message_date = i.last_message_date ) AS last_message
    FROM interlocutors i
    JOIN users u ON u.id = i.sender_id
    LEFT JOIN users us ON us.id = i.receiver_id
    WHERE i.sender_id = $profile_id OR i.receiver_id = $profile_id";

    return get_data($link, $sql);
}

/**
 * Проверяет ставил ли юзер лайк посту
 * @param mysqli $link
 * @param int $post_id id id поста
 * @param int $user_id id юзера
 *
 * @return BOOLEAN
 */
function is_exists_like($link, $post_id, $user_id)
{
    $sql = "SELECT l.* FROM likes l WHERE l.post_id = $post_id AND l.user_id = $user_id";

    return is_exist($link, $sql);
}

/**
 * Проверяет подписывался ли авторизованный пользователь на юзера
 * @param mysqli $link
 * @param int $subscriber_id id на кто подписывается
 * @param int $user_id id на кого подписываются
 *
 * @return BOOLEAN
 */
function is_exists_subscription($link, $subscriber_id, $user_id)
{
    $sql = "SELECT sub.* FROM subscriptions sub WHERE sub.user_id = $subscriber_id AND sub.userto_id = $user_id";

    return is_exist($link, $sql);
}

/**
 * Проверяет существование юзера в БД
 * @param mysqli $link
 * @param int $user_id id юзера
 *
 * @return BOOLEAN
 */
function is_exists_user($link, $user_id)
{
    $sql = "SELECT u.* FROM users u WHERE u.id = $user_id";

    return is_exist($link, $sql);
}

/**
 * Проверяет существование поста в БД
 * @param mysqli $link
 * @param int $post_id id юзера
 *
 * @return BOOLEAN
 */
function is_exists_post($link, $post_id)
{
    $sql = "SELECT p.* FROM posts p WHERE p.id = $post_id";

    return is_exist($link, $sql);
}


/**
 * Функция добавляет комментарий к посту
 * @param mysqli $link
 * @param array $comment_data данные коментария
 * @param int $profile_id id авторизованого пользователя
 *
 * @return BOOLEAN
 */
function comment_to_db($link, $comment_data, $profile_id)
{
    $sql = "INSERT INTO comments (content, user_id, post_id) VALUES (?, ?, ?)";
    $comment_data = [
        'content' => $comment_data['comment'],
        'user_id' => (int) $profile_id,
        'post_id' => (int) $comment_data['post_id'],
    ];

    return mysqli_stmt_execute(db_get_prepare_stmt($link, $sql, $comment_data));
}


/**
 * Функция получает список имен и адресов электронной почты для отправки уведомлений
 * @param mysqli $link
 * @param int $profile_id
 * @param string $page
 *
 * @return array массив c собеседниками
 */
function get_recipients($link, $profile_id, $page = 'add')
{
    $sql = "SELECT u.login AS name, u.email 
    FROM users u JOIN subscriptions sub ON u.id = sub.user_id 
    WHERE sub.userto_id = $profile_id";
    if ($page === 'subscription') {
        $sql = "SELECT u.login AS name, u.email FROM users u WHERE u.id = $profile_id";
    }
    return get_data($link, $sql);
}
