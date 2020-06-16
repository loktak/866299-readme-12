<?php
require_once('init.php');
require_once('validation.php');

list($unread_messages_count, $interlocutors, $profile_id) = require_once('interlocutors.php');

if (!isset($_SESSION['user'])) {
    header("Location: /index.php");
}

$user_data = $_SESSION['user'];

$page_parameters['type'] = $_GET['type'] ?? 'all';
$hashtags = [];
$posts = get_posts_for_feed($link, $user_data['id']);

if (!empty($_GET) && $page_parameters['type'] !== 'all') {
    $posts = get_posts_for_feed_by_category(
        $link,
        $user_data['id'],
        mysqli_real_escape_string($link, $page_parameters['type'])
    );
}

foreach ($posts as $post) {
    $hashtags[$post['id']] = get_hashtags_for_post($link, $post['id']);
}

$page_content = include_template('feed-content.php', [
    'types' => posts_categories($link),
    'page_parameters' => $page_parameters,
    'posts' => $posts,
    'hashtags' => $hashtags,
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'Readme: Моя лента',
    'user_data' => $user_data,
    'active_page' => 'feed',
    'unread_messages_count' => $unread_messages_count,
]);

print($layout_content);
