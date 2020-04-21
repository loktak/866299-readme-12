<?php
date_default_timezone_set("Europe/Moscow");

$is_auth = rand(0, 1);
$user_name = 'Арсений'; // укажите здесь ваше имя

require_once('functions.php');
require_once('helpers.php');

$link = database_conecting ('localhost', 'root', 'root', 'readme');

$page_content = include_template('main.php',[
    'posts' => popular_posts($link),
    'types' => posts_categories($link)
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'Readme Главная',
    'is_auth' => $is_auth,
    'user_name' => $user_name
]);

$types = posts_categories($link);

print($layout_content);