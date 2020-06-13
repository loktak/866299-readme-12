<?php
require_once 'init.php';
require_once 'validation.php';
list($unread_messages_count, $interlocutors) = require_once 'interlocutors.php';

if (!isset($_SESSION['user'])) {
    header("Location: /index.php");
}

$user_data = $_SESSION['user'];

$errors = [];
$show_comments = $_GET['show_comments'] ?? null;

if (isset($_GET['post_id'])) {
    setcookie('post_id', $_GET['post_id'], strtotime("+30 days"), '/post.php');
    $_COOKIE['post_id'] = $_GET['post_id'];
}

$post_id = (int)$_COOKIE['post_id'] ?? null; //защита от инъекций

if ($post_id === null || empty(get_post_info($link, $post_id,
        $profile_id))) { // если нет гет запроса или нет такого поста показываем страницу 404
    $page_content = include_template('post/post404.php', []);
    $layout_content = include_template('layout.php', [
        'content' => $page_content,
        'title' => 'Readme Публикация не найдена',
        'user_data' => $user_data,
        'unread_messages_count' => $unread_messages_count,
    ]);
    die(print($layout_content));
}

$post_info = current(get_post_info($link, $post_id, $profile_id)); // ищем пост в БД

plus_view($link, $post_info['id']); //добавляем просмотр

switch ($post_info['icon_type']) {
    case 'link':
        $post_content = include_template('post/post-link.php', [
            'post_info' => $post_info,
        ]);
        break;
    case 'quote':
        $post_content = include_template('post/post-quote.php', [
            'post_info' => $post_info,
        ]);
        break;
    case 'video':
        $post_content = include_template('post/post-video.php', [
            'post_info' => $post_info,
        ]);
        break;
    case 'photo':
        $post_content = include_template('post/post-photo.php', [
            'post_info' => $post_info,
        ]);
        break;
    case 'text':
        $post_content = include_template('post/post-text.php', [
            'post_info' => $post_info,
        ]);
}

$comments = get_post_comments($link, $post_info['id']);
$comments_count = count($comments);
$hidden_comments_count = null;
if ($comments_count > 2 && $show_comments !== 'all') {
    $cutted_comments = [
        '0' => $comments[0],
        '1' => $comments[1],
    ];
    $comments = $cutted_comments;
    $hidden_comments_count = $comments_count - 2;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST as $key => $value) {
        $comment[$key] = trim($value);
    }
    $required_fields = ['comment'];
    $rules = [
        'comment' => function () use ($comment) {
            return validate_lenght($comment['comment'], 4, 100);
        },
    ];
    $errors = check_required_fields($required_fields);
    $errors = check_rules($rules, $errors, $comment);

    $errors = array_filter($errors);

    if (empty($errors)) {
        $is_success = comment_to_db($link, $comment, $profile_id);
        if ($is_success) {
            header("Location: profile.php?user_id={$comment['author_id']}");
            die();
        }
        $errors['comment'] = 'не удалось добавить комментарий'.mysqli_error($link);
    }
}

$page_content = include_template('post-layout.php', [
    'post_content' => $post_content,
    'post_info' => $post_info,
    'user_data' => $user_data,
    'errors' => $errors,
    'comments' => $comments,
    'hidden_comments_count' => $hidden_comments_count,
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'Readme Публикация',
    'user_data' => $user_data,
    'unread_messages_count' => $unread_messages_count,
    'active_page' => 'post',
]);

print($layout_content);
