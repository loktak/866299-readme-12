<?php

require_once 'init.php';
require_once 'validation.php';

$errors = []; //объявляем массив с ошибками

//проверяем что страница загружена методом POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST as $key => $value) {
        $registration_data[$key] = trim($value);
    }
    $required_fields = ['email', 'login', 'password', 'password-repeat']; //определяем поля для проверки на заполненость
    //определяем список проверок для полей
    $rules = [
        'email' => function () use (&$link, $registration_data) {
            return email_validation($link, $registration_data['email']);
        },
        'login' => function () use (&$link, $registration_data) {
            return login_validation($link, $registration_data['login']);
        },
        'password' => function () {
            return validate_lenght($_POST['password'], 6);
        },
        'password-repeat' => function () {
            return compare_values($_POST['password-repeat'], $_POST['password']);
        },
    ];
    $errors = check_required_fields($required_fields); //проверка на пустое или нет

    $errors = check_rules($rules, $errors, $registration_data); // проверка на rules

    // если других ошибок нет проверяем наличие прикрепленного файла и загружаем его
    if (empty(array_filter($errors)) && !empty($_FILES['picture']['name'])) {
        $file = $_FILES;
        $errors['input-file'] = upload_post_picture($file, '/userpics/');
    }

    $errors = array_filter($errors); //получаем окончательный массив с ошибками

    // если ошибок нет создаем sql запрос на добавление нового юзера. и переходим на главную страницу
    if (empty($errors)) {
        $file_name = $file['picture']['name'] ?? 'userpic.jpg';
        $sql = 'INSERT INTO users (email, login, password, avatar) VALUES (?, ?, ?, ?)';
        $user = [
            'email' => $registration_data['email'],
            'login' => $registration_data['login'],
            'password' => password_hash($registration_data['password'], PASSWORD_DEFAULT),
            'avatar' => $file_name,
        ];

        $is_success = mysqli_stmt_execute(db_get_prepare_stmt($link, $sql, $user));

        if ($is_success) {
            header("Location: index.php");
        }

        $errors['input-file'] = 'не удалось зарегистрировать нового пользователя'.mysqli_error($link);
    }
}

$page_content = include_template('registration.php', [
    'errors' => $errors,
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'Readme: Регистрация',
    'is_auth' => 0,
]);

print($layout_content);
