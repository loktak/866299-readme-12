<?php
date_default_timezone_set("Europe/Moscow");
$is_auth = rand(0, 1);
$user_name = 'Арсений'; // укажите здесь ваше имя

require_once('functions.php');
require_once('helpers.php');

$sorting_paramters = array();
$link = database_conecting ('localhost', 'root', 'root', 'readme');
$sorting_paramters['sort_value'] = $_GET['sort_value'] ?? 'views';
$sorting_paramters['sorting'] = $_GET['sorting'] ?? 'DESC';
$sorting_paramters['type'] = $_GET['type'] ?? 'all';

if (!isset($_GET['sort_value'])) {
    $sort_value = 'views';
    $sorting = 'DESC';
}
else {
    $sort_value = $sorting_paramters['sort_value'];
    $sorting = $sorting_paramters['sorting'];
}

$posts = popular_posts($link, $sort_value, $sorting);

if (isset($_GET['type'])) {
    if ($_GET['type'] === 'all') {
        $posts = popular_posts($link, $sort_value, $sorting);
    }
    else {
        $posts = popular_posts_category_sorting($link, $_GET['type'], $sort_value, $sorting);
    }
}

$page_content = include_template('main.php',[
    'posts' => $posts,
    'types' => posts_categories($link),
    'sorting_paramters' => $sorting_paramters
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'Readme Главная',
    'is_auth' => $is_auth,
    'user_name' => $user_name
]);

print($layout_content);