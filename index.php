<?php
date_default_timezone_set("Europe/Moscow");

$is_auth = rand(0, 1);
$user_name = 'Арсений'; // укажите здесь ваше имя

require_once('functions.php');
require_once('helpers.php');

$link = database_conecting ('localhost', 'root', 'root', 'readme');

if (!isset($_GET['sort_value'])) {
    $sort_value = 'views';
    $sorting = 'DESC';
}
else {
    $sort_value = $_GET['sort_value'];
    $sorting = $_GET['sorting'];
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
    'types' => posts_categories($link)
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'Readme Главная',
    'is_auth' => $is_auth,
    'user_name' => $user_name
]);

print($layout_content);
