<?php
require_once 'init.php';
require_once 'validation.php';

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

$post_id = (int)$_GET['post_id']; //защита от иньекций

$is_post = is_exists_post($link, $post_id); //проверка на существование такого поста

if (!$is_post) {
    header("Location: $page_back");
    die();
}

$is_exists = is_exists_like($link, $post_id, $user_id);

$sql = "DELETE l.* FROM likes l WHERE l.post_id = $post_id AND l.user_id = $user_id";

if (!$is_exists) {
    $sql = "INSERT INTO likes (user_id, post_id) VALUES ($user_id, $post_id)";
}

$is_succsess = mysqli_stmt_execute(db_get_prepare_stmt($link, $sql));

if ($is_succsess) {
    header("Location: $page_back");
    die();
}
