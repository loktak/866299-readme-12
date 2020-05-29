<?php
require_once('init.php');
require_once('validation.php');

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

$user_id = (int) $_GET['user_id'];

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
    header("Location: $page_back");
    die();
}
