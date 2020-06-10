<?php
require_once('init.php');
require_once('validation.php');
require_once('mail_settings.php');
list($unread_messages_count, $interlocutors, $profile_id) = require_once('interlocutors.php');


// проверяем залогинен ли пользователь, если нет, то перенаправляем на главную
if (!isset($_SESSION['user'])) {
    header("Location: /index.php");
}

$user_data = $_SESSION['user'];

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
    $required_fields = ['heading'];
    $rules = [
        'heading' => function () {
            return validate_lenght($_POST['heading']);
        },
        'tags' => function () {
            return check_tags($_POST['tags']);
        }
    ];
    //определяем список полей для проверки на пустое не пустое и правила для проверки полей, которые в этом нуждаются
    switch ($posts['form-type']) {  
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
                        return validate_lenght($_POST['post-text'], 50, 1000);
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
    $required_fields[] = 'tags'; //сделано специально, что бы ошибка о незаполнености тегов была в самом низу. Для удобства читаемости

    $errors = check_required_fields($required_fields); //проверка на пустое или нет

    $errors = check_rules($rules, $errors, $posts); // проверка на rules



    if ($_POST['form-type'] === 'video' && empty($errors['video-url'])) {  // если иных ошибок не найдено проверяем что ссылка ведет на youtube
        $errors['video-url'] = check_youtube_link($_POST['video-url']);
    }

    if (empty(array_filter($errors))) {
        if (!empty($_FILES['picture']['name'])) {  //определяем каким способом был загружен файл если с помощью формы то выполняем одну функцию если нет то смотрим по ссылке
            $errors['input-file'] = upload_post_picture($files);
        } else if (isset($_POST['photo-url'])) {
            $errors['photo-url'] = get_img_by_link($_POST['photo-url']);
        }
    }

    $errors = array_filter($errors);

    if (empty($errors)) { // если массив c ошибками пустой
        $user_id = $user_data['id'];
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

        mysqli_query($link, "START TRANSACTION");

        $is_r1 = mysqli_stmt_execute(db_get_prepare_stmt($link, $sql, $db_post));

        $post_id = mysqli_insert_id($link);

        $is_r2 = add_tags_to_posts($link, $tags, $post_id);

        if (!$is_r1 && !$is_r2) { // если хотя бы один запрос не выполнен откатываем.
            mysqli_query($link, "ROLLBACK");
            die('не получилось добавить пост' . mysqli_error($link));
        }
        mysqli_query($link, "COMMIT");

        $recipients = get_recipients($link, $profile_id);
       
        foreach ($recipients as $recipient) {
            $notification_content = include_template('notifications/new-post.php', [
                'author' => $user_data['login'],
                'post_id' => $post_id,
                'recipient' => $recipient,
                'profile_id' => $profile_id,
                'post_title' => $db_post['title']
            ]);
            $notification = include_template('notifications/notification-layout.php', [
                'notification_content' => $notification_content
            ]);
            $message = (new Swift_Message("Новая публикация от пользователя"))
                ->setFrom(['keks@phpdemo.ru' => 'readme'])
                ->setTo([$recipient['email'] => $recipient['name']])
                ->setBody($notification, 'text/html');
            $result = $mailer->send($message);
        }

        header("Location: post.php?post_id=" . $post_id);
    }
}

$page_parameters['name'] = get_russian_form_name($page_parameters['form-type']); //названия для формы

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
    'user_data' => $user_data,
    'active_page' => 'add',
    'unread_messages_count' => $unread_messages_count
]);

print($layout_content);
