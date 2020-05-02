<?php
require_once('init.php');

if (isset($_GET['post_id'])) {
    $post_info = get_post_info($link, $_GET['post_id'])[0];
    if (!empty($post_info)) {
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
        $page_content = include_template('post-layout.php', [
            'post_content' => $post_content,
            'post_info' => $post_info,
            'user_posts' => $user_posts
        ]);
    } else {
        $page_content = include_template('post/post404.php', []);
    }
} else {
    $page_content = include_template('post/post404.php', []);
}

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'Readme Публикация',
    'is_auth' => $is_auth,
    'user_name' => $user_name
]);

print($layout_content);