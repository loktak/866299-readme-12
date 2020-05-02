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
    switch ($posts['form-type']) {  //определяем список полей для проверки на пустое не пустое и правила для проверки полей, которые в этом нуждаются
        case 'photo':
            if (empty($_FILES['picture']['name'])) {
                $required_fields = ['heading', 'photo-url', 'tags'];
                $rules = [
                    'heading' => function () {
                        return validate_lenght($_POST['heading']);
                    },
                    'photo-url' => function () {
                        return check_url($_POST['photo-url']);
                    },
                    'tags' => function () {
                        return check_tags($_POST['tags']);
                    }
                ];
            } else {
                $required_fields = ['heading', 'tags'];
                $rules = [
                    'heading' => function () {
                        return validate_lenght($_POST['heading']);
                    },
                    'tags' => function () {
                        return check_tags($_POST['tags']);
                    }
                ];
            }
            break;
        case 'video':
            $required_fields = ['heading', 'video-url', 'tags'];
            $rules = [
                'heading' => function () {
                    return validate_lenght($_POST['heading']);
                },
                'video-url' => function () {
                    return check_url($_POST['video-url']);
                },
                'tags' => function () {
                    return check_tags($_POST['tags']);
                }
            ];
            break;
        case 'text':
            $required_fields = ['heading', 'post-text', 'tags'];
            $rules = [
                'heading' => function () {
                    return validate_lenght($_POST['heading']);
                },
                'tags' => function () {
                    return check_tags($_POST['tags']);
                }
            ];
            break;
        case 'quote':
            $required_fields = ['heading', 'cite-text', 'quote-author', 'tags'];
            $rules = [
                'heading' => function () {
                    return validate_lenght($_POST['heading']);
                },
                'cite-text' => function () {
                    return validate_lenght($_POST['cite-text'], 10, 75);
                },
                'tags' => function () {
                    return check_tags($_POST['tags']);
                }
            ];
            break;
        case 'link':
            $required_fields = ['heading', 'post-link', 'tags'];
            $rules = [
                'heading' => function () {
                    return validate_lenght($_POST['heading']);
                },
                'post-link' => function () {
                    return check_url($_POST['post-link']);
                },
                'tags' => function () {
                    return check_tags($_POST['tags']);
                }
            ];
    }

    $errors = not_empty($required_fields); //проверка на пустое или нет

    $errors = check_rules($rules, $errors); // проверка на rules

    if (!empty($_FILES['picture']['name'])) {  //определяем каким способом был загружен файл если с помощью формы то выполняем одну функцию если нет то смотрим по ссылке
        $errors['input-file'] = upload_post_picture($files);
    } else {
        if (isset($_POST['photo-url']) && empty($errors['photo-url'])) {
            $errors['photo-url'] = get_img_by_link($_POST['photo-url']);
        }   
    }

    if ($_POST['form-type'] === 'video') {  // если иных ошибок не найдено проверяем что ссылка ведет на youtube
        if (empty($errors['video-url'])) {
            $errors['video-url'] = check_youtube_link($_POST['video-url']);
        }
    }

    $errors = array_filter($errors); // выводим массив с ошибками

    if (empty($errors)) { // если массив c ошибками пустой
        $db_post['title'] = htmlspecialchars($_POST['heading']);
        switch ($posts['form-type']) {
            case 'photo':
                $db_post['img'] = get_file_path($posts['photo-url'], $files['picture']['name']);
                $sql = 'INSERT INTO posts (title, img, user_id, type_id)
                VALUES (?, ?, 4, 1)';
                break;
            case 'video':
                $db_post['video'] = htmlspecialchars($_POST['video-url']);
                $sql = 'INSERT INTO posts (title, video, user_id, type_id)
                VALUES (?, ?, 4, 2)';
                break;
            case 'text':
                $db_post['content_text'] = htmlspecialchars($_POST['post-text']);
                $sql = 'INSERT INTO posts (title, content_text, user_id, type_id)
                VALUES (?, ?, 4, 3)';
                break;
            case 'quote':
                $db_post['content_text'] = htmlspecialchars($_POST['cite-text']);
                $db_post['quote_author'] = htmlspecialchars($_POST['quote-author']);
                $sql = 'INSERT INTO posts (title, content_text, quote_author, user_id, type_id)
                VALUES (?, ?, ?, 4, 4)';
                break;
            case 'link':
                $db_post['link'] = htmlspecialchars($_POST['post-link']);
                $sql = 'INSERT INTO posts (title, link, user_id, type_id)
                VALUES (?, ?, 4, 5)';
                break;
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
print_r($stml);