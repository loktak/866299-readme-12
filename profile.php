<?php
require_once('init.php');
require_once('validation.php');
require_once('interlocutors.php');

if (!isset($_SESSION['user'])) {
    header("Location: /index.php");
}

$user_data = $_SESSION['user'];
$active_page = 'profile.php';

$errors = [];
$hashtags = [];


if (isset($_GET['user_id'])) { // проверяем есть ли такой юзер, если нет то сбрасываем запрос
    $id = (int) $_GET['user_id'];
    $is_user = is_exists_user($link, $id);
    if (!$is_user) {
        header("Location: /profile.php");
        die();
    }
}

if (!empty($_GET)) {
    foreach ($_GET as $key => $value) {
        if (isset($_GET[$key]) && ($key === 'user_id' || $key === 'active_tab')) {
            setcookie($key, $value, strtotime("+30 days"), '/profile.php');
            $_COOKIE[$key] = $value;
        }
    }
}

$active_tab = mysqli_real_escape_string($link, $_COOKIE['active_tab']) ?? 'posts';

$profile_id = (int)$_COOKIE['user_id'] ?? $user_data['id'];

$profile_info = get_profile_data($link, $profile_id, $user_data['id']);

$comments_for_id = $_COOKIE['comments_for'] ?? 0;
$show_comments = $_GET['show_comments'] ?? null;

if ($active_tab === 'posts') {

    $posts = get_posts_by_author_id($link, $profile_id);

    $comments[$comments_for_id] = get_post_comments($link, $comments_for_id);
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
        $sql = "INSERT INTO comments (content, user_id, post_id) VALUES (?, ?, ?)";
        $comment_data = [
            'content' => $comment['comment'],
            'user_id' => $user_data['id'],
            'post_id' => $comment['post_id'],
        ];
        $stml = db_get_prepare_stmt($link, $sql, $comment_data);
        $result = mysqli_stmt_execute($stml);
        if ($result) {
            $id = $comment['author_id'];
            header("Location: profile.php?user_id=$id");
        }
        $errors['comment'] = 'не удалось добавить комментарий' . mysqli_error($link);
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
    'active_page' => $active_page,
    'unreaded_messages_count' => $unreaded_messages_count,
]);

print($layout_content);
