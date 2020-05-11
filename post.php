<?php
require_once('init.php');
require_once('validation.php');

if (!isset($_SESSION['user'])) {
    header("Location: /");
}

$user_data = $_SESSION['user'];

if (empty($_GET['post_id']) || empty(get_post_info($link, $_GET['post_id'])[0])) { // если нет гет запроса или нет такого поста показываем страницу 404
    $page_content = include_template('post/post404.php', []);
    $layout_content = include_template('layout.php', [
        'content' => $page_content,
        'title' => 'Readme Публикация не найдена',
        'user_data' => $user_data
    ]);
    die(print($layout_content));
}

$errors = [];
$show_comments = $_GET['show_comments'] ?? NULL;

$post_info = get_post_info($link, $_GET['post_id'])[0]; // ищем пост в БД

$user_posts = count(get_user_posts_count($link, ($post_info['user_id'])));

switch ($post_info['icon_type']) {
    case 'link':
        $post_content = include_template('post/post-link.php', [
            'post_info' => $post_info
        ]);
        break;
    case 'quote':
        $post_content = include_template('post/post-quote.php', [
            'post_info' => $post_info
        ]);
        break;
    case 'video':
        $post_content = include_template('post/post-video.php', [
            'post_info' => $post_info
        ]);
        break;
    case 'photo':
        $post_content = include_template('post/post-photo.php', [
            'post_info' => $post_info
        ]);
        break;
    case 'text':
        $post_content = include_template('post/post-text.php', [
            'post_info' => $post_info
        ]);
}

$comments = get_post_comments($link, $post_info['id']);
$comments_count = count($comments);
$hidded_comments_count = null;
if ($comments_count > 2 && $show_comments !== 'all') {
    $cutted_comments = [
        '0' => $comments[0],
        '1' => $comments[1]
    ];
    $comments = $cutted_comments;
    $hidded_comments_count = $comments_count - 2;    
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST as $key => $value) {
        $comment[$key] = trim($value);
    }
    $required_fields = ['comment'];
    $rules = [
        'comment' => function () use ($comment) {
            return validate_lenght($comment['comment'], 4, 100);
        }
    ];
    $errors = check_required_fields($required_fields);
    $errors = check_rules($rules, $errors, $comment);

    $errors = array_filter($errors);

    if (empty($errors)) {
        $sql = "INSERT INTO comments (content, user_id, post_id) VALUES (?, ?, ?)";
        $comment_data = [
            'content' => $comment['comment'],
            'user_id' => $user_data['id'],
            'post_id' => $comment['post_id']
        ];
        $stml = db_get_prepare_stmt($link, $sql, $comment_data);
        $result = mysqli_stmt_execute($stml);
        if ($result) {
            $id = $comment['post_id'];
            header("Location: post.php?post_id=$id"); //пока нет страницы с профилем пользователя возвращаю на эту же
        }
        $errors['comment'] = 'не удалось добавить комментарий' . mysqli_error($link);
    }
}
$page_content = include_template('post-layout.php', [
    'post_content' => $post_content,
    'post_info' => $post_info,
    'user_posts' => $user_posts,
    'user_data' => $user_data,
    'errors' => $errors,
    'comments' => $comments,
    'hidded_comments_count' => $hidded_comments_count
]);


$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'Readme Публикация',
    'user_data' => $user_data
]);

print($layout_content);