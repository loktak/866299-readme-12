<?php

require_once('init.php');

if (!isset($_SESSION['user'])) {
    header("Location: /index.php");
}

$user_data = $_SESSION['user'];

$page_back = $_SERVER['HTTP_REFERER'] ?? 'index.php';

if (empty($_GET['post_id'])) {
  header("Location: $page_back");
  die();
}

$post_id = $_GET['post_id'];
$post_info = get_post_by_id($link, $post_id);
  if (empty($post_info)) {
    header("Location: $page_back");
}


$title = $post_info['title'];
$user_id = $user_data['id'];
$type_id = $post_info['type_id'];
$original_author_id = $post_info['user_id'];
$original_id = $post_info['id'];

switch ($type_id) {
    case '1' :
        $column = 'img';
        $repost_info['img'] = $post_info['img'];
    break;
    case '2' :
        $column = 'video';
        $repost_info['video'] = $post_info['video'];
    break;
    case '3' :
        $column = 'content_text';
        $repost_info['content_text'] = $post_info['content_text'];
    break;
    case '4' :
        $column = 'content_text, quote_author';
        $repost_info['content_text'] = $post_info['content_text'];
        $repost_info['quote_author'] = $post_info['quote_author'];
    break;
    case '5' :
        $column = 'link';
        $repost_info['link'] = $post_info['link'];
    break;
}

$sql = "INSERT INTO posts (title, $column, user_id, type_id, original_author_id, original_id) 
VALUES ('$title', ?, $user_id, $type_id, $original_author_id, $original_id)";
if ($post_info['type_id'] === 4) {
    $sql = "INSERT INTO posts (title, $column, user_id, type_id, original_author_id, original_id) 
    VALUES ('$title', ?, ?, $user_id, $type_id, $original_author_id, $original_id)";
}

$stml = db_get_prepare_stmt($link, $sql, $repost_info);
$result = mysqli_stmt_execute($stml);
if (!$result) {
 return print('не получилось сделать репост'. mysqli_error($link));
} else {
    header("Location: $page_back");
}

