<?php
require_once('init.php');
require_once('validation.php');

if (!isset($_SESSION['user'])) {
    header("Location: /index.php");
}

$user_data = $_SESSION['user'];

$page_back = $_SERVER['HTTP_REFERER'] ?? 'index.php';

if (empty($_GET['post_id'])) {
    header("Location: $page_back");
    die();
}

$user_id = $user_data['id'];

$post_id = $_GET['post_id'];

$sql = "SELECT l.* FROM likes l WHERE l.post_id = $post_id AND l.user_id = $user_id";

$check_like = get_data($link, $sql)[0] ?? NULL;

if (empty($check_like)) {
    $sql = "INSERT INTO likes (user_id, post_id) VALUES ($user_id, $post_id)";
    
    $stml = db_get_prepare_stmt($link, $sql);
    
    $result = mysqli_stmt_execute($stml);
    
    if ($result) {
        header("Location: $page_back");
        die();
    }
}

$like_id = $check_like['id'];

$sql = "DELETE l.*FROM likes l WHERE l.id = $like_id";

$stml = db_get_prepare_stmt($link, $sql);

$result = mysqli_stmt_execute($stml);

if ($result) {
    header("Location: $page_back");
    die();
}
