<?php
require_once('init.php');

if (!isset($_SESSION['user'])) {
    header("Location: /");
  }

$user_data = $_SESSION['user'];

$sorting_parameters = [];

$sorting_parameters['sort_value'] = $_GET['sort_value'] ?? 'views';
$sorting_parameters['sorting'] = $_GET['sorting'] ?? 'DESC';
$sorting_parameters['type'] = $_GET['type'] ?? 'all';

$sort_value = $sorting_parameters['sort_value'];
$sorting = $sorting_parameters['sorting'];

$posts = popular_posts($link, $sort_value, $sorting);

if (isset($_GET['type'])) {
    if ($_GET['type'] === 'all') {
        $posts = popular_posts($link, $sort_value, $sorting);
    }
    else {
        $posts = popular_posts_category_sorting($link, $sorting_parameters['type'], $sort_value, $sorting);
    }
}

$page_content = include_template('main.php',[
    'posts' => $posts,
    'types' => posts_categories($link),
    'sorting_parameters' => $sorting_parameters
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'Readme: Популярный контент',
    'user_data' => $user_data
]);

print($layout_content);