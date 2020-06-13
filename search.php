<?php
require_once 'init.php';
require_once 'validation.php';
list($unread_messages_count, $interlocutors, $profile_id) = require_once 'interlocutors.php';

if (!isset($_SESSION['user'])) {
    header("Location: /index.php");
}

$user_data = $_SESSION['user'];
$page_back = $_SERVER['HTTP_REFERER'] ?? 'index.php';

$search_request = trim($_GET['search_request']);

if (empty($search_request)) {
    $page_content = include_template('no-result.php', [
        'search_request' => 'Ваш запрос пуст',
        'page_back' => $page_back,
    ]);
    $layout_content = include_template('layout.php', [
        'content' => $page_content,
        'title' => 'Readme Публикация',
        'user_data' => $user_data,
        'active_page' => 'search',

    ]);
    die($layout_content);
}

if (mb_substr($search_request, 0, 1) !== '#') {
    $posts = search_text_in_posts($link, mysqli_real_escape_string($link, $search_request));
} else {
    $hashtag = mb_substr($search_request, 1);
    $posts = search_hastags_on_posts($link, $hashtag);
}

$page_content = include_template('search-results.php', [
    'search_request' => $search_request,
    'posts' => $posts,
]);

if (empty($posts)) {
    $page_content = include_template('no-result.php', [
        'search_request' => $search_request,
        'page_back' => $page_back,
    ]);
}

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'Readme: Результаты поиска',
    'user_data' => $user_data,
    'unread_messages_count' => $unread_messages_count,
    'active_page' => 'search',
]);

print($layout_content);
