<?php
require_once('init.php');
require_once('interlocutors.php');

if (!isset($_SESSION['user'])) {
    header("Location: /index.php");
}

$user_data = $_SESSION['user'];

$active_page = 'popular';

if (!empty($_GET)) {
    foreach ($_GET as $key => $value) {
        if (isset($_GET[$key]) && ($key === 'type' || $key === 'sorting' || $key === 'sort_value' || $key === 'current_page')) {
            setcookie($key, $value, strtotime("+30 days"), '/popular.php');
            $_COOKIE[$key] = $value;
        }
    }
}

$sorting_parameters = [];
// защищяемся от инъекций
$sorting_parameters['sort_value'] = mysqli_real_escape_string($link, $_COOKIE['sort_value']) ?? 'views';
$sorting_parameters['sorting'] =  mysqli_real_escape_string($link, $_COOKIE['sorting']) ?? 'DESC';
$sorting_parameters['type'] = mysqli_real_escape_string($link, $_COOKIE['type']) ?? 'all';

$current_page = (int) $_COOKIE['current_page'] ?? 1;

$page_items = 6;

if ($sorting_parameters['type'] !== 'all') {
    $posts_count = current(get_data($link, "SELECT COUNT(*) as 'count' FROM posts p JOIN content_type ct ON ct.id = p.type_id WHERE ct.icon_type = '" . $sorting_parameters['type'] . "'"))['count'];
} else {
    $posts_count = current(get_data($link, 'SELECT COUNT(*) as count FROM posts'))['count'];
}

$pages_count = ceil($posts_count / $page_items);

$offset = ($current_page - 1) * $page_items;

if ((int) $posts_count <= 9 && (int) $posts_count > 0) {
    $offset = 0;
    $pages_count = 1;
    $page_items = 9;
}

if ($sorting_parameters['type'] !== 'all') {
    $posts = popular_posts_category_sorting($link, $sorting_parameters['type'], $sorting_parameters['sort_value'], $sorting_parameters['sorting'], $page_items, $offset);
} else {
    $posts = popular_posts($link, $sorting_parameters['sort_value'], $sorting_parameters['sorting'], $page_items, $offset);
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
