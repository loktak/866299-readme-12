<?php
$is_auth = rand(0, 1);
$user_name = 'Арсений';

date_default_timezone_set("Europe/Moscow");

require_once('functions.php');
require_once('helpers.php');

$link = database_conecting('localhost', 'root', 'root', 'readme');

if (isset($_GET['post_id'])) {
    $post_info = get_post_info($link, $_GET['post_id'])[0];
    if (!empty($post_info)) {
        $user_posts = count(get_user_posts_count($link, ($post_info['user_id'])));
        switch ($post_info['icon_type']) {
            case 'link':
                $post_content = include_template('post-link.php', [
                    'post_info' => $post_info
                ]);
                break;
            case 'quote':
                $post_content = include_template('post-quote.php', [
                    'post_info' => $post_info
                ]);
                break;
            case 'video':
                $post_content = include_template('post-video.php', [
                    'post_info' => $post_info
                ]);
                break;
            case 'photo':
                $post_content = include_template('post-photo.php', [
                    'post_info' => $post_info
                ]);
                break;
            case 'text':
                $post_content = include_template('post-text.php', [
                    'post_info' => $post_info
                ]);
        }
        $page_content = include_template('post-layout.php', [
            'post_content' => $post_content,
            'post_info' => $post_info,
            'user_posts' => $user_posts
        ]);
    } else {
        $page_content = include_template('post404.php', []);
    }
} else {
    $page_content = include_template('post404.php', []);
}

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'Readme Публикация',
    'is_auth' => $is_auth,
    'user_name' => $user_name
]);

print($layout_content);