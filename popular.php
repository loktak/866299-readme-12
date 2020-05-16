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

$sort_value = $sorting_parameters['sort_value'];
$sorting = $sorting_parameters['sorting'];

$posts = popular_posts($link, $sort_value, $sorting);

if ($sorting_parameters['type'] !== 'all') {
    $posts = popular_posts_category_sorting($link, $sorting_parameters['type'], $sort_value, $sorting);
}

$page_content = include_template('main.php', [
    'posts' => $posts,
    'types' => posts_categories($link),
    'sorting_parameters' => $sorting_parameters
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'Readme: Популярный контент',
    'user_data' => $user_data,
    'active_page' => $active_page
]);

print($layout_content);