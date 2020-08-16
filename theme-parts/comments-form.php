<?
global $post_id;

$user = wp_get_current_user();
$user_identity = $user->exists() ? $user->display_name : '';
$req = get_option('require_name_email');
$html_req = ($req ? " required" : '');
$commenter = wp_get_current_commenter();
$consent = empty($commenter['comment_author_email']) ? '' : ' checked';
$is_show_avatar = get_option('show_avatars');
$args = [
    'comment_notes_before' => '',
    'must_log_in' => '<div class="must-log-in">' . 
        '<a href="' . wp_login_url(apply_filters('the_permalink', get_permalink($post_id ))) . '">' . __('Login', 'upd') . '</a>' .
    '</div>',
    'logged_in_as' => '<div class="logged-in-as">' . 
        sprintf(__('Logged in as <a href="%1$s">%2$s</a>', 'upd'), get_edit_user_link(), $user_identity) .
        '<span>|</span>'  .
        sprintf(__('<a href="%1$s">Log out?</a>', 'upd'), wp_logout_url(apply_filters('the_permalink', get_permalink($post_id)))) .
    '</div>',    
    'fields' => [
        'author' => '<div data-move="true" class="comment-form-author field input-has-icon">
            <i class="fa fa-user" aria-hidden="true"></i>   
            <input id="author" type="text" name="author" placeholder="' .__('Name', 'upd')  .'"  value="' . esc_attr($commenter['comment_author']) . '" size="30"' . $html_req . '>
        </div>',
        'email'  => '<div data-move="true" class="comment-form-email field input-has-icon">
            <i class="fa fa-envelope-o" aria-hidden="true"></i>
            <input id="email" type="email" name="email" placeholder="' .__('Email', 'upd')  .'" value="' . esc_attr($commenter['comment_author_email']) . '" size="30" aria-describedby="email-notes"' . $html_req  . '>
        </div>',
		'url'    => '',
		'cookies' => '<div data-move="true" class="comment-form-cookies-consent field">'.
            sprintf('<input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" value="yes"%s />', $consent ) .'
            <label for="wp-comment-cookies-consent">'. __('Remember me', 'upd') .'</label>
		</div>',        
    ],
    'comment_field' => '<div class="form-inner row">
        <div class="avatar">
            <img src="' . get_avatar_url(get_the_author_meta('ID')) . '" class="" alt="">
        </div>
        <div class="comment-fields">
		    <textarea id="comment" class="field" name="comment" placeholder="' . __('Join the discussion', 'upd')  . '" aria-required="true" required></textarea>        
        </div>
	</div>',
    'title_reply' => '',
    'title_reply_before' => false,
    'title_reply_after' => false,
    'cancel_reply_before'  => '<div class="cancel-reply">',
	'cancel_reply_after'   => '</div>',
];

if (!$is_show_avatar) {
   $args['comment_field'] = '<div class="form-inner row">
        <div class="comment-fields">
            <textarea id="comment" class="field" name="comment" placeholder="' . __('Join the discussion', 'upd')  . '" aria-required="true" required></textarea>        
        </div>
    </div>'; 
}

comment_form($args)
?>