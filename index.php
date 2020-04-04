<?php

$posts = [];

function include_template($name, $data) {
    $name = 'templates/' . $name;
    $result = '';

    if (!file_exists($name)) {
        return $result;
    }

    ob_start();
    extract($data);
    require $name;

    $result = ob_get_clean();

    return $result;
}

$page_content = include_template('main.php', ['posts' => $posts]);

$layout_content = include_template('layout.php', [
	'content' => $page_content,
	'title' => 'Readme Главная'
]);

print($layout_content);

?>

