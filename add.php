<?php
require_once('init.php');



$page_parameters['form-type'] = $_GET['type'] ?? 'photo';
$page_parameters['heading'] = $_POST['heading'] ?? 'default';
$files = $_FILES;

$errors = [];
$required_fields = [];
$posts = [];
$file_upload_input = "";

// Проверяем что страница загружена методом POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $page_parameters['form-type'] = $_POST['form-type'];
    $posts = $_POST;
    switch ($posts['form-type']) {  //определяем список полей для проверки на пустое не пустое и правила для проверки полей, которые в этом нуждаются
        case 'photo':
            if (empty($_FILES['picture']['name'])) {
                $required_fields = ['heading', 'photo-url'];
                $rules = [
                    'heading' => function () {
                        return validate_lenght($_POST['heading']);
                    },
                    'photo-url' => function () {
                        return check_url($_POST['photo-url']);
                    }
                ];
            } else {
                $required_fields = ['heading'];
                $rules = [
                    'heading' => function () {
                        return validate_lenght($_POST['heading']);
                    }
                ];
            }
            break;
        case 'video':
            $required_fields = ['heading', 'video-url'];
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
            $rules = [
                'heading' => function () {
                    return validate_lenght($_POST['heading']);
                }
            ];
            break;
        case 'quote':
            $required_fields = ['heading', 'cite-text', 'quote-author'];
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
            $rules = [
                'heading' => function () {
                    return validate_lenght($_POST['heading']);
                },
                'post-link' => function () {
                    return check_url($_POST['post-link']);
                }
            ];
    }
    
    $errors = not_empty($required_fields); //проверка на пустое или нет
    
    $errors = check_rules($rules, $errors); // проверка на rules

    if (!empty($_FILES['picture']['name']))  {  //определяем каким способом был загружен файл если с помощью формы то выполняем одну функцию если нет то смотрим по ссылке
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

    if (!empty($_POST['tags'])) { //если поле тэги не пустое, проверяем теги согласно тз
        $errors['tags'] = check_tags($posts['tags']);
    }
    
    $errors = array_filter($errors); // выводим массив с ошибками

    if (empty($errors)) { // если массив c ошибками пустой
        $db_post['title'] = htmlspecialchars($_POST['heading']);
        switch ($posts['form-type']) {
            case 'photo':
                $db_post['img'] = get_file_path($_POST['photo-url'], $files); 
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


        $stml = db_get_prepare_stmt($link, $sql, $db_post);
        
        $post_id = add_post_to_db($link, $posts['tags'], $stml);
        
        if ($post_id !== 'Что-то пошло не так') {

            header("Location: post.php?post_id=" . $post_id);
        } else {
            $errors['sql'] = 'Ошибка загрузки поста';
        }
    }
}

$page_parameters['name'] = set_russian_form_name($page_parameters['form-type']); //названия для формы формы

if ($page_parameters['form-type'] === 'photo') {
    $file_upload_input = include_template('add-post/add-photo-drag-n-drop.php', []);
}

$content = include_template("add-post/add-" . $page_parameters['form-type'] . "-post.php", [
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
    'title' => 'Readme: Добавить пост',
    'is_auth' => $is_auth,
    'user_name' => $user_name
]);

print($layout_content);
