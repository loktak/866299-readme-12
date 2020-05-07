<?php

require_once('init.php');
require_once('validation.php');


$errors = []; //объявляем массив с ошибками

//проверяем что страница загружена методом POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $registration_data = $_POST; //переносим все значения $_POST в новый массив
    $required_fields = ['email', 'login', 'password', 'password-repeat']; //определяем поля для проверки на заполненость
    //определяем список проверок для полей
    $rules =[
        'password' => function () {
            return validate_lenght($_POST['password'], 6);
        },
        'password-repeat' => function () {
            return compare_values($_POST['password-repeat'], $_POST['password']);
        },
    ];
    $errors = check_required_fields($required_fields); //проверка на пустое или нет

    $errors = check_rules($rules, $errors, $registration_data); // проверка на rules
    
    //проверяем валидность адреса почты
    if (empty($errors['email'])) {
        $errors['email'] = email_validation($link, $registration_data['email']);
    }
    
    //проверяем валидность логина
    if (empty($errors['login'])) {
        $errors['login'] = login_validation($link, $registration_data['login']);
    }

    // если других ошибок нет проверяем наличие прикрепленного файла и загружаем его
    if (empty(array_filter($errors)) && !empty($_FILES['picture']['name'])) {
        $file = $_FILES;
        $errors['input-file'] = upload_post_picture($file, '/userpics/');
    }

    $errors = array_filter($errors); //получаем окончательный массив с ошибками
    
    // если ошибок нет создаем sql запрос на добавление нового юзера. и переходим на главную страницу
    if (empty($errors)) {
        $file_name = $file['picture']['name'] ?? 'default.jpg';
        $sql = 'INSERT INTO users (email, login, password, avatar) VALUES (?, ?, ?, ?)';
        $user['email'] = $registration_data['email'];
        $user['login'] = $registration_data['login'];
        $user['password'] = password_hash($registration_data['password'], PASSWORD_DEFAULT);
        $user['avatar'] = $file_name;
        $stml = db_get_prepare_stmt($link, $sql, $user);
        $result = mysqli_stmt_execute($stml);

        if ($result) {
            header("Location: index.php");
        }
    }
}



$page_content = include_template('registration.php', [
    'errors' => $errors
]);

$layout_content = include_template('registration-layout.php', [
    'content' => $page_content,
    'title' => 'Readme: Регистрация'
]);

print($layout_content);
