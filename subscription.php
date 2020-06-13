<?php
require_once 'init.php';
require_once 'validation.php';
require_once 'mail_settings.php';

if (!isset($_SESSION['user'])) {
    header("Location: /index.php");
}

$user_data = $_SESSION['user'];

$page_back = $_SERVER['HTTP_REFERER'] ?? 'index.php';

if (empty($_GET['user_id']) || $user_data['id'] === $_GET['user_id']) {
    header("Location: $page_back");
    die();
}

$subscriber_id = $user_data['id'];

$user_id = (int)$_GET['user_id'];

$is_user = is_exists_user($link, $user_id); //проверка на существование такого юзера

if (!$is_user) {
    header("Location: $page_back");
    die();
}

$is_subscription = is_exists_subscription($link, $subscriber_id, $user_id);

$sql = "DELETE sub.* FROM subscriptions sub WHERE sub.user_id = $subscriber_id AND sub.userto_id = $user_id";

if (!$is_subscription) {
    $sql = "INSERT INTO subscriptions (user_id, userto_id) VALUES ($subscriber_id, $user_id)";
}

$is_succsess = mysqli_stmt_execute(db_get_prepare_stmt($link, $sql));

if ($is_succsess) {
    if (!$is_subscription) {
        $recipient = current(get_recipients($link, $user_id, 'subscription'));
        $notification_content = include_template('notifications/new_subscriber.php', [
            'subscriber' => $user_data['login'],
            'recipient' => $recipient,
            'subscriber_id' => $subscriber_id,
        ]);
        $notification = include_template('notifications/notification-layout.php', [
            'notification_content' => $notification_content,
        ]);
        $message = (new Swift_Message("У вас новый подписчик"))
            ->setFrom(['keks@phpdemo.ru' => 'readme'])
            ->setTo([$recipient['email'] => $recipient['name']])
            ->setBody($notification, 'text/html');
        $result = $mailer->send($message);
    }

    header("Location: $page_back");
    die();
}
