<?
if (post_password_required()) {
    return;
}
?>
 
<div id="comments" class="comments-area">
    <div class="title"><? _e('Discuss about post', 'upd') ?></div>
    <?
    if (comments_open()) {
        get_template_part('theme-parts/comments-form');
    }
 
    if (have_comments()) { ?>
        <div class="comment-list">
            <div class="comments-title"><? printf(_nx('%1$s comment', '%1$s comments', get_comments_number(), 'upd'), number_format_i18n(get_comments_number())) ?></div>

            <?
            wp_list_comments([
                'style' => 'div',
                'callback' => 'upd_template_parts::comment_view',
                'end-callback' => 'upd_template_parts::comment_view_end',
                'short_ping' => true,
                'format' => 'html5',
                'avatar_size' => 74,
            ]);
            ?>
        </div>

        <?
        if (get_comment_pages_count() > 1 && get_option('page_comments')) {
            the_comments_pagination();
        }
    }
    ?>
</div>