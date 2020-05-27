<?php
require_once('init.php');
require_once('validation.php');

if (!isset($_SESSION['user'])) {
    header("Location: /index.php");
}

$user_data = $_SESSION['user'];

$active_page = 'messages';

$expire = strtotime("+30 days");

$path = "/messages.php";

$errors = [];

$profile_id = $user_data['id'];

$month_name = [
 '01' => 'Янв',
 '02' => 'Фев',
 '03' => 'Мар',
 '04' => 'Апр',
 '05' => 'Мая',
 '06' => 'Июн',
 '07' => 'Июл',
 '08' => 'Авг',
 '09' => 'Cент',
 '10' => 'Окт',
 '11' => 'Нояб',
 '12' => 'Дек'
];


if (!empty($_GET['receiver_id'])) { // если не пустой гет запрос, проверяем, что есть такая связь в таблице контактов, если нет, то создаем ее
    $receiver_id = $_GET['receiver_id'];
    $is_interclutor = check_interclutor($link, $profile_id, $receiver_id);
    if (!$is_interclutor) {
        $sql = "SELECT u.* FROM users u WHERE id=$receiver_id";
        if (empty(get_data($link, $sql))) { // проверяем есть ли такой юзер, если нет то сбрасываем запрос
            header("Location: /messages.php");
            die();
        }
        $sql = "INSERT INTO interlocutors (sender_id, receiver_id) VALUES ($profile_id, $receiver_id)";
        $stml = db_get_prepare_stmt($link, $sql);
        $result = mysqli_stmt_execute($stml);
        header("Location: /messages.php");
    }
    setcookie('user_id', $receiver_id, $expire, $path);
    $_COOKIE['user_id'] = $receiver_id;
}

$receiver_id = $_COOKIE['user_id'] ?? 0; // с кем диалог

if ($receiver_id !== 0) {
    $id = $receiver_id;
    $cookie = "last_dialog_$id";
    $now = new DateTime();
    $date = $now->format('Y-m-d H:i:s');
    setcookie($cookie, $date, $expire, "/");
    $_COOKIE[$cookie] = $date;
}

$messages = get_chat_messages($link, $profile_id, $receiver_id); //получаем список сообщений между авторизованным пользователем  и собеседником

if ($_SERVER['REQUEST_METHOD'] === 'POST') { // если пришел пост запрос, то проводим валидацию
    foreach ($_POST as $key => $value) {
        $message[$key] = trim($value);
    }
    $required_fields = ['message'];
    $rules = [
        'message' => function () use ($message) {
            return validate_lenght($message['message'], 2, 100);
        }
    ];
    $errors = check_required_fields($required_fields);
    $errors = check_rules($rules, $errors, $message);

    $errors = array_filter($errors);

    if (empty($errors)) { // если ошибок валидации нет, то при помощи транзакции, добавляем сообщение в сущность сообщения и меняем отправителя на авторизованного пользователя, получателя на собеседника и дату на сейчас
        $message_content = $message['message'];

        $receiver_id = $message['receiver_id'];

        $sql_for_messages = "INSERT INTO messages (content, user_id, userto_id) VALUES ('$message_content', $profile_id, $receiver_id)";

        $sql_for_interlocutors = "UPDATE interlocutors SET sender_id = $profile_id, receiver_id = $receiver_id, last_message_date = CURRENT_TIMESTAMP 
        WHERE sender_id = $profile_id AND receiver_id = $receiver_id OR sender_id = $receiver_id AND receiver_id = $profile_id";

        $stml_for_messages = db_get_prepare_stmt($link, $sql_for_messages);

        $stml_for_interlocutors = db_get_prepare_stmt($link, $sql_for_interlocutors);

        mysqli_query($link, "START TRANSACTION");

        $r1 = mysqli_stmt_execute($stml_for_messages);

        $r2 = mysqli_stmt_execute($stml_for_interlocutors);

        if (!$r1 && !$r2) { // если хотя бы один запрос не выполнен откатываем.
            mysqli_query($link, "ROLLBACK");
        }
        mysqli_query($link, "COMMIT"); // если все ок, то записываем
        header("Location: /messages.php?receiver_id=$receiver_id#message_anchor");
    }
}

$chat_content = include_template('messages/chat-messages.php', [
    'messages' => $messages,
    'user_data' => $user_data
]);

if (empty($messages)) { // если сообщений нет, выводим пустую страницу  листиком
    $chat_content = include_template('no-content.php', []);
}

$page_content = include_template('messages-content.php', [
    'interlocutors' => $interlocutors,
    'user_data' => $user_data,
    'receiver_id' => $receiver_id,
    'chat_content' => $chat_content,
    'errors' => $errors,
    'month_name' => $month_name
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'Readme: Мои сообщения',
    'user_data' => $user_data,
    'unreaded_messages_count' => $unreaded_messages_count,
    'active_page' => $active_page
]);

print($layout_content);
print_r($interlocutors);