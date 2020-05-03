<?php
require_once('init.php');
require_once('validation.php');

$page_parameters['form-type'] = $_GET['type'] ?? 'photo';
$page_parameters['heading'] = $_POST['heading'] ?? 'default';
$files = $_FILES;

$errors = [];
$required_fields = [];
$posts = [];

// Проверяем что страница загружена методом POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $page_parameters['form-type'] = $_POST['form-type'];
    $posts = $_POST;
    $required_fields = ['heading', 'tags'];
    $rules = [
        'heading' => function () {
            return validate_lenght($_POST['heading']);
        },
        'tags' => function () {
            return check_tags($_POST['tags']);
        }
    ];
    switch ($posts['form-type']) {  //определяем список полей для проверки на пустое не пустое и правила для проверки полей, которые в этом нуждаются
        case 'photo':
            if (empty($_FILES['picture']['name'])) {
                $required_fields[] = 'photo-url';
                $rules = array_merge(
                    $rules,
                    [
                        'photo-url' => function () {
                            return check_url($_POST['photo-url']);
                        }
                    ]
                );
            }
            break;
        case 'video':
            $required_fields[] = 'video-url';
            $rules = array_merge(
                $rules,
                [
                    'video-url' => function () {
                        return check_url($_POST['video-url']);
                    }
                ]
            );
            break;
        case 'text':
            $required_fields[] = 'post-text';
            $rules = array_merge(
                $rules,
                [
                    'post-text' => function () {
                        return validate_lenght($_POST['post-text'], 50, 600);
                    }
                ]
            );
            break;
        case 'quote':
            $required_fields = array_merge($required_fields, ['cite-text', 'quote-author']);
            $rules = array_merge(
                $rules,
                [
                    'cite-text' => function () {
                        return validate_lenght($_POST['cite-text'], 10, 75);
                    }
                ]
            );
            break;
        case 'link':
            $required_fields[] = 'post-link';
            $rules = array_merge(
                $rules,
                [
                    'post-link' => function () {
                        return check_url($_POST['post-link']);
                    }
                ]
            );
    }

    $errors = check_required_fields($required_fields); //проверка на пустое или нет

    $errors = check_rules($rules, $errors, $posts); // проверка на rules

    if (!empty($_FILES['picture']['name'])) {  //определяем каким способом был загружен файл если с помощью формы то выполняем одну функцию если нет то смотрим по ссылке
        $errors['input-file'] = upload_post_picture($files);
    } else if (isset($_POST['photo-url']) && empty($errors['photo-url'])) {
        $errors['photo-url'] = get_img_by_link($_POST['photo-url']);
    }

    if ($_POST['form-type'] === 'video' && empty($errors['video-url'])) {  // если иных ошибок не найдено проверяем что ссылка ведет на youtube
        $errors['video-url'] = check_youtube_link($_POST['video-url']);
    }

    $errors = array_filter($errors); // выводим массив с ошибками

    if (empty($errors)) { // если массив c ошибками пустой
        $user_id = 4;
        $db_post['title'] = $_POST['heading'];
        switch ($posts['form-type']) {
            case 'photo':
                $column = 'img';
                $db_post['img'] = get_file_path($posts['photo-url'], $files['picture']['name']);
                $type_id = 1;
                break;
            case 'video':
                $column = 'video';
                $db_post['video'] = $_POST['video-url'];
                $type_id = 2;
                break;
            case 'text':
                $column = 'content_text';
                $db_post['content_text'] = $_POST['post-text'];
                $type_id = 3;
                break;
            case 'quote':
                $column = 'content_text, quote_author';
                $db_post['content_text'] = $_POST['cite-text'];
                $db_post['quote_author'] = $_POST['quote-author'];
                $type_id = 4;
                break;
            case 'link':
                $column = 'link';
                $db_post['link'] = $_POST['post-link'];
                $type_id = 5;
                break;
        }


        $sql = "INSERT INTO posts (title, $column, user_id, type_id) VALUES (?, ?, $user_id, $type_id)";
        if ($posts['form-type'] === 'quote') {
            $sql = "INSERT INTO posts (title, $column, user_id, type_id) VALUES (?, ?, ?, $user_id, $type_id)";
        }

        $tags = tags_to_array($posts['tags']); //делаем из строки с тегами массив без повторяющихся тегов

        $stml = db_get_prepare_stmt($link, $sql, $db_post);

        $post_id = add_post_to_db($link, $stml);

        $result = add_tags_to_posts($link, $tags, $post_id);
    
        if ($result) {
            header("Location: post.php?post_id=" . $post_id);
        } 
    }
}

$page_parameters['name'] = get_russian_form_name($page_parameters['form-type']); //названия для формы формы


$content = include_template("add-post/add-" . $page_parameters['form-type'] . "-post.php", [
    'errors' => $errors
]);

$page_content = include_template('add-post.php', [
    'content' => $content,
    'types' => posts_categories($link),
    'page_parameters' => $page_parameters,
    'errors' => $errors
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'Readme: Добавить пост',
    'is_auth' => $is_auth,
    'user_name' => $user_name
]);

print($layout_content);
