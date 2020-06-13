<?php
require_once('init.php');
require_once('validation.php');

if (isset($_SESSION['user'])) {
    header("Location: /feed.php");
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST as $key => $value) {
        $form[$key] = trim($value);
    }

    $required_fields = ['email', 'password'];

    $rules = [
        'email' => function () {
            return check_email($_POST['email']);
        },
        'password' => function () {
            return validate_lenght($_POST['password'], 6);
        },
    ];

    $errors = check_required_fields($required_fields); //проверка на пустое или нет
    $errors = check_rules($rules, $errors, $form);

    $errors = array_filter($errors);

    if (empty($errors)) {
        $user_data = get_user_data_by_email($link, mysqli_real_escape_string($link, $form['email']));
        if (empty($user_data)) {
            $errors['email'] = "Вы ввели неверный email/пароль";
        }

        if (empty($errors) && password_verify($form['password'], $user_data['password'])) {
            $_SESSION['user'] = $user_data;
            header("Location: feed.php");
        } else {
            $errors['password'] = 'Вы ввели неверный email/пароль';
        }
    }
}


$layout_content = include_template('index.php', [
    'errors' => $errors,
]);

print($layout_content);
