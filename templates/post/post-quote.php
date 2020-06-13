<div class="post-details__image-wrapper post-quote">
    <div class="post__main">
        <blockquote>
            <p>
                <?= anti_xss($post_info['content_text']) ?>
            </p>
            <cite><?= anti_xss($post_info['quote_author']) ?></cite>
        </blockquote>
    </div>
</div>
