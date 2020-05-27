<?php
require_once('init.php');

if (!isset($_SESSION['user'])) {
    header("Location: /index.php");
}

$user_data = $_SESSION['user'];

$active_page = 'popular';

$expire = strtotime("+30 days");

$path = "/popular.php";

if (!empty($_GET)) {
    foreach ($_GET as $key => $value) {
        if (isset($_GET[$key])) {
            setcookie($key, $value, $expire, $path);
            $_COOKIE[$key] = $value;
        }
    }
}

$sorting_parameters = [];

$sorting_parameters['sort_value'] = $_COOKIE['sort_value'] ?? 'views';
$sorting_parameters['sorting'] = $_COOKIE['sorting'] ?? 'DESC';
$sorting_parameters['type'] = $_COOKIE['type'] ?? 'all';

$current_page = $_COOKIE['current_page'] ?? 1;
$page_items = 6;
$posts_count = get_data($link, 'SELECT COUNT(*) as count FROM posts')[0]['count'];
$pages_count = ceil($posts_count / $page_items);
$offset = ($current_page - 1) * $page_items;

$sort_value = $sorting_parameters['sort_value'];

$sorting = $sorting_parameters['sorting'];

if ((int) $posts_count <= 9 && (int) $posts_count > 0) {
    $page_items = $posts_count;
    $offset = 0;
    $pages_count = 1;
}
$posts = popular_posts($link, $sort_value, $sorting, $page_items, $offset);

if ($sorting_parameters['type'] !== 'all') {
    $type = $sorting_parameters['type'];
    $posts_count = get_data($link, "SELECT COUNT(*) as 'count' FROM posts p JOIN content_type ct ON ct.id = p.type_id WHERE ct.icon_type = '$type'")[0]['count'];
    $pages_count = ceil($posts_count / $page_items);
    $offset = ($current_page - 1) * $page_items;
    if ((int) $posts_count <= 9 && (int) $posts_count > 0) {
        $page_items = (int) $posts_count;
        $offset = 0;
        $pages_count = 1;
    }
    $posts = popular_posts_category_sorting($link, $type, $sort_value, $sorting, $page_items, $offset);
}

$popular_posts = include_template('popular/popular-posts.php', [
    'posts' => $posts,
    'pages_count' => $pages_count,
    'current_page' => $current_page,
]);


if (empty($posts)) {
    $popular_posts = include_template('no-content.php', []);
}

$page_content = include_template('main.php', [
    'types' => posts_categories($link),
    'sorting_parameters' => $sorting_parameters,
    'popular_posts' => $popular_posts
]);



$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'Readme: Популярный контент',
    'user_data' => $user_data,
    'active_page' => $active_page,
    'unreaded_messages_count' => $unreaded_messages_count
]);

print($layout_content);
