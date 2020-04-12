<?php
date_default_timezone_set("Europe/Moscow");

$is_auth = rand(0, 1);
$user_name = 'Арсений'; // укажите здесь ваше имя

require_once('functions.php');
require_once('data.php');
require_once('variables.php');
require_once('helpers.php');

$page_content = include_template(
    'main.php',
    ['posts' => $posts,]
);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'Readme Главная',
    'is_auth' => $is_auth,
    'user_name' => $user_name,

]);

print($layout_content);
