<?php
require_once 'init.php';
require_once 'validation.php';
list($unread_messages_count, $interlocutors, $profile_id) = require_once 'interlocutors.php';

if (!isset($_SESSION['user'])) {
    header("Location: /index.php");
}

$user_data = $_SESSION['user'];

$errors = [];
$hashtags = [];

if (isset($_GET['user_id'])) { // проверяем есть ли такой юзер, если нет то сбрасываем запрос
    $id = (int)$_GET['user_id'];
    $is_user = is_exists_user($link, $id);
    if (!$is_user) {
        header("Location: /profile.php");
        die();
    }
}

if (!empty($_GET)) {
    foreach ($_GET as $key => $value) {
        if (isset($_GET[$key]) && in_array($key, ['user_id', 'active_tab', 'comments_for'])) {
            setcookie($key, $value, strtotime("+30 days"), '/profile.php');
            $_COOKIE[$key] = $value;
        }
    }
}

$active_tab = $_COOKIE['active_tab'] ?? 'posts';

$profile_id = $_COOKIE['user_id'] ?? $user_data['id'];

$profile_info = get_profile_data($link, (int)$profile_id, $user_data['id']);

$comments_for_id = $_COOKIE['comments_for'] ?? 0;
$show_comments = $_GET['show_comments'] ?? null;

if ($active_tab === 'posts') {
    $posts = get_posts_by_author_id($link, $profile_id);

    $comments[$comments_for_id] = get_post_comments($link, (int)$comments_for_id);
    $comments_count = count($comments[$comments_for_id]);
    $hidded_comments_count = null;
    if ($comments_count > 2 && $show_comments !== 'all') {
        $cutted_comments = [
            '0' => $comments[$comments_for_id][0],
            '1' => $comments[$comments_for_id][1],
        ];
        $comments[$comments_for_id] = $cutted_comments;
        $hidded_comments_count = $comments_count - 2;
    }

    foreach ($posts as $post) {
        $hashtags[$post['id']] = get_hashtags_for_post($link, $post['id']);
    }
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

if ($active_tab === 'posts' || $_SERVER['REQUEST_METHOD'] === 'POST') {
    $profile_tab = include_template('profile/profile-posts.php', [
        'posts' => $posts,
        'hashtags' => $hashtags,
        'comments' => $comments,
        'show_comments' => $show_comments,
        'hidded_comments_count' => $hidded_comments_count,
        'comments_for_id' => $comments_for_id,
        'errors' => $errors,
        'user_data' => $user_data,
    ]);
    if (empty($posts)) {
        $profile_tab = include_template('no-content.php', []);
    }
}

if ($active_tab === 'likes') {
    $likes = get_user_likes($link, $profile_id);
    $profile_tab = include_template('profile/profile-likes.php', [
        'likes' => $likes,
    ]);
    if (empty($likes)) {
        $profile_tab = include_template('no-content.php', []);
    }
}

if ($active_tab === 'subscriptions') {
    $subsribers = get_subscribers($link, $profile_id, $user_data['id']);
    $profile_tab = include_template('profile/profile-subscriptions.php', [
        'subsribers' => $subsribers,
        'user_data' => $user_data,
    ]);
    if (empty($subsribers)) {
        $profile_tab = include_template('no-content.php', []);
    }
}

$page_content = include_template('profile-template.php', [
    'profile_tab' => $profile_tab,
    'profile_info' => $profile_info,
    'user_data' => $user_data,
    'active_tab' => $active_tab,
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'Readme: Профиль пользователя',
    'user_data' => $user_data,
    'active_page' => 'profile',
    'unread_messages_count' => $unread_messages_count,
]);

print($layout_content);
