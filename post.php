<?php
$is_auth = rand(0, 1);
$user_name = 'Арсений';

date_default_timezone_set("Europe/Moscow");

require_once('functions.php');
require_once('helpers.php');

$link = database_conecting('localhost', 'root', 'root', 'readme');

if (isset($_GET['post_id'])) {
    list($post_info) = get_post_info($link, $_GET['post_id']);
    if (isset($post_info['id'])) {
        if (($post_info['icon_type'] === 'link')) {
            $post_content = include_template('post-link.php', [
                'post_info' => $post_info
            ]);
        } elseif (($post_info['icon_type'] === 'quote')) {
            $post_content = include_template('post-quote.php', [
                'post_info' => $post_info
            ]);
        } elseif (($post_info['icon_type'] === 'video')) {
            $post_content = include_template('post-video.php', [
                'post_info' => $post_info
            ]);
        } elseif (($post_info['icon_type'] === 'photo')) {
            $post_content = include_template('post-photo.php', [
                'post_info' => $post_info
            ]);
        } elseif (($post_info['icon_type'] === 'text')) {
            $post_content = include_template('post-text.php', [
                'post_info' => $post_info
            ]);
        } 
        list($user_posts) = get_user_posts_count($link, $post_info['user_id']);
        $page_content = include_template('post-layout.php', [
            'post_content' => $post_content,
            'post_info' => $post_info,
            'user_posts' => $user_posts
        ]);
    }
    else {
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
print($post_info['post_date']);