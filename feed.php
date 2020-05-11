<?php

require_once('init.php');
require_once('validation.php');

if (!isset($_SESSION['user'])) {
  header("Location: /");
}

$user_data = $_SESSION['user'];

$page_parameters['type'] = $_GET['type'] ?? 'all';

$posts = get_posts_for_feed($link, $user_data['id']);

if (!empty($_GET) && $page_parameters['type'] !== 'all') {
  $posts = get_posts_for_feed_by_category($link, $user_data['id'], $page_parameters['type']);
} else {
  $posts = get_posts_for_feed($link, $user_data['id']);
}

$site = file_get_contents('https://www.htmlacademy.ru');
if (preg_match('/<title>([^<]*)<\/title>/',$site,$matches) == 1)
{
  $test = $matches[1];
}
else
{
  $test = "нет титла";
}

$page_content = include_template('feed-content.php', [
  'types' => posts_categories($link),
  'page_parameters' => $page_parameters,
  'posts' => $posts,
  'test' => $test
]);

$layout_content = include_template('layout.php', [
  'content' => $page_content,
  'title' => 'Readme: Моя лента',
  'user_data' => $user_data
]);

print($layout_content);