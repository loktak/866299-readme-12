<?php
date_default_timezone_set("Europe/Moscow");
$is_auth = rand(0, 1);
$user_name = 'Арсений'; // укажите здесь ваше имя

require_once('functions.php');
require_once('helpers.php');

chmod('C:\OPEN-SERVER\OSPanel\userdata\php_upload', 0777);
chmod('C:\OPEN-SERVER\OSPanel\userdata\temp', 0777);

$link = database_conecting('localhost', 'root', 'root', 'readme');

$page_parameters['form-type'] = $_GET['type'] ?? 'photo';
$page_parameters['heading'] = $_POST['heading'] ?? 'default';
$files = $_FILES;

$errors = [];
$required_fields = [];
$posts = [];
$file_upload_input = "";


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $page_parameters['form-type'] = $_POST['form-type'];
    switch ($page_parameters['form-type']) {
        case 'photo':
            $required_fields = ['heading'];
            $errors = not_empty($required_fields);
            if(!empty($_FILES['picture'])) {
            $file_name = $_FILES['picture']['name'];
            $file_path = __DIR__ . '/uploads/';
            $file_url = '/uploads/' . $file_name;

            move_uploaded_file($_FILES['picture']['tmp_name'], $file_path . $file_name);
        }
            $rules = [
                'heading' => function () {
                    return validate_lenght($_POST['heading']);
                }
            ];
            break;
        case 'video':
            $required_fields = ['heading', 'video-url'];
            $errors = not_empty($required_fields);
            $rules = [
                'heading' => function () {
                    return validate_lenght($_POST['heading']);
                },
                'video-url' => function () {
                    return check_url($_POST['video-url']);
                }
            ];
            break;
        case 'text':
            $required_fields = ['heading', 'post-text'];
            $errors = not_empty($required_fields);
            $rules = [
                'heading' => function () {
                    return validate_lenght($_POST['heading']);
                }
            ];
            break;
        case 'quote':
            $required_fields = ['heading', 'cite-text', 'quote-author'];
            $errors = not_empty($required_fields);
            $rules = [
                'heading' => function () {
                    return validate_lenght($_POST['heading']);
                },
                'cite-text' => function () {
                    return validate_lenght($_POST['cite-text'], 10, 75);
                }
            ];
            break;
        case 'link':
            $required_fields = ['heading', 'post-link'];
            $errors = not_empty($required_fields);

            $rules = [
                'heading' => function () {
                    return validate_lenght($_POST['heading']);
                },
                'post-link' => function () {
                    return check_url($_POST['post-link']);
                }
            ];
    }

    foreach ($_POST as $key => $value) {
        if (empty($errors[$key])) {
            if (isset($rules[$key])) {
                $rule = $rules[$key];
                $errors[$key] = $rule();
            }
        }
    }

    if (!empty($_POST['tags'])) {
        $errors['tags'] = check_tags();
        $tags = explode(" ", $_POST['tags']);
    }

    if ($_POST['form-type'] === 'video') {
        if (empty($errors['video-url'])) {
            $errors['video-url'] = check_youtube_link($_POST['video-url']);
        }
    }
    $errors = array_filter($errors);

    if (empty($errors)) {
        $posts['title'] = htmlspecialchars($_POST['heading']);
        switch ($_POST['form-type']) {
            case 'video':
                $posts['video'] = htmlspecialchars($_POST['video-url']);
                $sql = 'INSERT INTO posts (title, video, user_id, type_id)
                VALUES (?, ?, 4, 2)';
                break;
            case 'text':
                $posts['content_text'] = htmlspecialchars($_POST['post-text']);
                $sql = 'INSERT INTO posts (title, content_text, user_id, type_id)
                VALUES (?, ?, 4, 3)';
                break;
            case 'quote':
                $posts['content_text'] = htmlspecialchars($_POST['cite-text']);
                $posts['quote_author'] = htmlspecialchars($_POST['quote-author']);
                $sql = 'INSERT INTO posts (title, content_text, quote_author, user_id, type_id)
                VALUES (?, ?, ?, 4, 4)';
                break;
            case 'link':
                $posts['link'] = htmlspecialchars($_POST['post-link']);
                $sql = 'INSERT INTO posts (title, link, user_id, type_id)
                VALUES (?, ?, 4, 5)';
                break;
        }
        if (!empty($_POST['tags'])) {
            $tag_sql = 'INSERT INTO hashtags (title) VALUES (?)';
            foreach ($tags as $tag) {
                $values['title'] = htmlspecialchars($tag);
                $tag_stml = db_get_prepare_stmt($link, $tag_sql, $values);
                $tag_result = mysqli_stmt_execute($tag_stml);
            }
        }
        $stml = db_get_prepare_stmt($link, $sql, $posts);
        $result = mysqli_stmt_execute($stml);
    
        if ($result) {
            $post_id = mysqli_insert_id($link);
    
            header("Location: post.php?post_id=" . $post_id);
        }
    }
}


switch ($page_parameters['form-type']) {
    case 'photo':
        $page_parameters['name'] = 'изображения';
        $file_upload_input = include_template('add-photo-drag-n-drop.php', []);
        break;
    case 'video':
        $page_parameters['name'] = 'видео';
        break;
    case 'text':
        $page_parameters['name'] = 'текстового поста';
        break;
    case 'link':
        $page_parameters['name'] = 'ссылки';
        break;
    case 'quote':
        $page_parameters['name'] = 'цитаты';
}

$content = include_template("add-" . $page_parameters['form-type'] . "-post.php", [
    'errors' => $errors
]);

$page_content = include_template('add-post.php', [
    'content' => $content,
    'types' => posts_categories($link),
    'page_parameters' => $page_parameters,
    'file_upload_input' => $file_upload_input,
    'errors' => $errors
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'Readme Главная',
    'is_auth' => $is_auth,
    'user_name' => $user_name
]);



$test = get_img_by_link($_POST['photo-url']);
print($layout_content);
print($test);
