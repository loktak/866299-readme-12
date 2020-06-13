<?php

require_once 'init.php';

if (!isset($_SESSION['user'])) {
    header("Location: /index.php");
}

$user_data = $_SESSION['user'];

$page_back = $_SERVER['HTTP_REFERER'] ?? 'index.php';

if (empty($_GET['post_id'])) {
    header("Location: $page_back");
    die();
}

$post_id = (int)$_GET['post_id'];
$post_info = get_post_by_id($link, $post_id);
if (empty($post_info)) {
    header("Location: $page_back");
    die();
}

if ($post_info['user_id'] === $user_data['id']) { //если пост и так пренадлежит пользователю, то просто показываем ему его пост. так как в тз написано, что репосты чужих постов.
    $path = '/post.php?post_id='.$post_info['id'];
    header("Location: $path");
    die();
}

$hashtags = get_hashtags_for_post($link, $post_id);
$repost_info['title'] = $post_info['title'];
$user_id = $user_data['id'];
$type_id = (int)$post_info['type_id'];
$original_author_id = $post_info['user_id'];
$original_id = $post_info['id'];

if (!empty($post_info['original_id'])) { // делает так что при репосте репоста репостится оригинал
    $original_id = $post_info['original_id'];
}

switch ($type_id) {
    case PHOTO:
        $column = 'img';
        $repost_info['img'] = $post_info['img'];
        break;
    case VIDEO:
        $column = 'video';
        $repost_info['video'] = $post_info['video'];
        break;
    case TEXT:
        $column = 'content_text';
        $repost_info['content_text'] = $post_info['content_text'];
        break;
    case QUOTE:
        $column = 'content_text, quote_author';
        $repost_info['content_text'] = $post_info['content_text'];
        $repost_info['quote_author'] = $post_info['quote_author'];
        break;
    case LINK:
        $column = 'link';
        $repost_info['link'] = $post_info['link'];
        break;
}

$sql = "INSERT INTO posts (title, $column, user_id, type_id, original_author_id, original_id)
VALUES (?, ?, $user_id, $type_id, $original_author_id, $original_id)";
if ($type_id === QUOTE) {
    $sql = "INSERT INTO posts (title, $column, user_id, type_id, original_author_id, original_id)
    VALUES (?, ?, ?, $user_id, $type_id, $original_author_id, $original_id)";
}

mysqli_query($link, "START TRANSACTION");

$is_r1 = mysqli_stmt_execute(db_get_prepare_stmt($link, $sql, $repost_info));

$post_id = mysqli_insert_id($link);

$sql = "INSERT INTO hashtags_posts (tag_id, post_id) VALUES";


if (!empty($hashtags)) {
    foreach ($hashtags as $hashtag) {
        $sql .= ' ('.$hashtag['id'].", $post_id),";
    }
    $is_r2 = mysqli_stmt_execute(db_get_prepare_stmt($link, substr($sql, 0, -1)));
} else {
    $is_r2 = true;
}

if (!$is_r1 && !$is_r2) { // если хотя бы один запрос не выполнен откатываем.
    mysqli_query($link, "ROLLBACK");

    return print('не получилось сделать репост'.mysqli_error($link));
    die();
}

mysqli_query($link, "COMMIT"); // если все ок, то записываем
header("Location: /profile.php?user_id=$user_id&active_tab=posts");
