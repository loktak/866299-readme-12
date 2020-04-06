<?php
require_once('functions.php');
require_once('data.php');

$page_content = include_template('main.php', ['posts' => $posts]);

$layout_content = include_template('layout.php', [
	'content' => $page_content,
	'title' => 'Readme Главная'
]);

print($layout_content);


