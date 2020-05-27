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

$user_id = $_GET['user_id'];

$sql = "SELECT sub.* FROM subscriptions sub WHERE sub.user_id = $subscriber_id AND sub.userto_id = $user_id";

$check_subscription = get_data($link, $sql)[0] ?? NULL;

if (empty($check_subscription)) {
    $sql = "INSERT INTO subscriptions (user_id, userto_id) VALUES ($subscriber_id, $user_id)";
    
    $stml = db_get_prepare_stmt($link, $sql);
    
    $result = mysqli_stmt_execute($stml);
    
    if ($result) {
        header("Location: $page_back");
        die();
    }
}

$subsctibe_id = $check_subscription['id'];

$sql = "DELETE sub.* FROM subscriptions sub WHERE sub.id = $subsctibe_id";

$stml = db_get_prepare_stmt($link, $sql);

$result = mysqli_stmt_execute($stml);

if ($result) {
    header("Location: $page_back");
    die();
}
