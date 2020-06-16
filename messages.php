<?php
require_once 'init.php';
require_once 'validation.php';
list($unread_messages_count, $interlocutors, $profile_id) = require_once 'interlocutors.php';

if (!isset($_SESSION['user'])) {
    header("Location: /index.php");
}

$user_data = $_SESSION['user'];

$errors = [];

$profile_id = $user_data['id'];

if (!empty($_GET['receiver_id'])) {
    $receiver_id = (int) $_GET['receiver_id'];
    if (!is_interlocutor_exist($link, $profile_id, $receiver_id)) {
        $is_user = is_exists_user($link, $receiver_id);
        if (!$is_user) { // проверяем есть ли такой юзер, если нет то сбрасываем запрос
            header("Location: /messages.php");
            die();
        }
        $sql = "INSERT INTO interlocutors (sender_id, receiver_id) VALUES ($profile_id, $receiver_id)";
        $is_result = mysqli_stmt_execute(db_get_prepare_stmt($link, $sql));
        header("Location: /messages.php");
    }
    setcookie('user_id', $receiver_id, strtotime("+30 days"), '/messages.php');
    $_COOKIE['user_id'] = $receiver_id;
}

$receiver_id = $_COOKIE['user_id'] ?? 0; // с кем диалог

if ($receiver_id !== 0) {
    $cookie = "last_dialog_$receiver_id";
    $date = (new DateTime())->format('Y-m-d H:i:s');
    setcookie($cookie, $date, strtotime("+30 days"), "/");
    $_COOKIE[$cookie] = $date;
}

$messages = get_chat_messages(
    $link,
    $profile_id,
    $receiver_id
); //получаем список сообщений между авторизованным пользователем  и собеседником

if ($_SERVER['REQUEST_METHOD'] === 'POST') { // если пришел пост запрос, то проводим валидацию
    $message = [];
    foreach ($_POST as $key => $value) {
        $message[$key] = trim($value);
    }
    $required_fields = ['message'];
    $rules = [
        'message' => function () use ($message) {
            return validate_lenght($message['message'], 2, 100);
        },
    ];
    $errors = check_required_fields($required_fields);
    $errors = check_rules($rules, $errors, $message);

    $errors = array_filter($errors);

    if (empty($errors)) {
        $message_content = mysqli_real_escape_string($link, $message['message']);

        $receiver_id = (int) $message['receiver_id'];

        $sql_for_messages = "INSERT INTO messages (content, user_id, userto_id) 
        VALUES ('$message_content', $profile_id, $receiver_id)";

        $sql_for_interlocutors = "UPDATE interlocutors 
        SET sender_id = $profile_id, receiver_id = $receiver_id, last_message_date = CURRENT_TIMESTAMP
        WHERE sender_id = $profile_id AND receiver_id = $receiver_id OR sender_id = $receiver_id 
        AND receiver_id = $profile_id";

        mysqli_query($link, "START TRANSACTION");

        $r1 = mysqli_stmt_execute(db_get_prepare_stmt($link, $sql_for_messages));

        $r2 = mysqli_stmt_execute(db_get_prepare_stmt($link, $sql_for_interlocutors));

        if (!$r1 && !$r2) { // если хотя бы один запрос не выполнен откатываем.
            mysqli_query($link, "ROLLBACK");
        }
        mysqli_query($link, "COMMIT"); // если все ок, то записываем
        header("Location: /messages.php?receiver_id=$receiver_id#message_anchor");
    }
}

if (empty($messages)) { // если сообщений нет, выводим пустую страницу  листиком
    $chat_content = include_template('no-content.php', []);
} else {
    $chat_content = include_template('messages/chat-messages.php', [
        'messages' => $messages,
        'user_data' => $user_data,
    ]);
}

$page_content = include_template('messages-content.php', [
    'interlocutors' => $interlocutors,
    'user_data' => $user_data,
    'receiver_id' => $receiver_id,
    'chat_content' => $chat_content,
    'errors' => $errors,
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'Readme: Мои сообщения',
    'user_data' => $user_data,
    'unread_messages_count' => $unread_messages_count,
    'active_page' => 'messages',
]);

print($layout_content);
