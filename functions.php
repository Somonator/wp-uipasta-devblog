<?php
class upd_istall_theme {
	function __construct() {
		$this->custom_hooks();
		add_action('after_setup_theme', [$this, 'setup']);
		add_action('after_setup_theme', [$this, 'menus']);
		add_action('widgets_init', [$this, 'widgets']);
		add_action('after_setup_theme', [$this, 'image_sizes']);
		add_action('wp_enqueue_scripts', [$this, 'scripts']);
		add_action('after_setup_theme', [$this, 'load_lang']);
		add_action('wp_ajax_load_more', [$this, 'ajax_load_more']);
		add_action('wp_ajax_nopriv_load_more', [$this, 'ajax_load_more']);	
	}

	function custom_hooks() {
		add_filter('use_block_editor_for_post', '__return_false', 10); //disable gutenberg
	}
	
	function setup() {
		add_theme_support('title-tag');
		add_theme_support('menus');
		add_theme_support('post-thumbnails');
		add_theme_support('html5', [
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		]);
		add_theme_support('customize-selective-refresh-widgets');
	}

	function menus() {
		register_nav_menus([
			'footer-menu' => __('Menu in footer', 'upd')
		]);
	}

	function widgets(){
		register_sidebar([
			'name' => __('Left sidebar', 'upd'),
			'id' => 'left-sidebar',
			'description' => __('Widgets in left sidebar', 'upd'),
			'before_title' => '<!--', /* hide widget title */
			'after_title' => '-->',
			'before_widget' => '<div id="%1$s" class="widget block-appearance %2$s">',
			'after_widget'  => "</div>\n",
		]);
	}

	function image_sizes() {
		set_post_thumbnail_size(810, 326, true);
		add_image_size('portfolio-thumbnail', 516, 500, true);
	}

	function load_lang() {
		load_theme_textdomain('upd', get_template_directory() . '/lang');
	}	

	function scripts() {
		wp_enqueue_style('poppins-font', 'https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap');
		wp_enqueue_style('font-awesome', get_template_directory_uri() . '/src/css/font-awesome.css');
		wp_enqueue_style('main-styles', get_template_directory_uri() . '/src/css/style.css');		

		wp_enqueue_script('jquery', true);
		wp_enqueue_script('main-scripts', get_template_directory_uri() . '/src/js/scripts.js', ['jquery'], true);

		
		wp_localize_script('main-scripts', 'ajax_data', [
			'url' => admin_url('/admin-ajax.php'),
			'error_ajax' => __('Error ajax request', 'upd')			
		]);

		if ((is_front_page() && get_option('show_on_front') === 'posts') || is_archive()) {
			global $wp_query;

			wp_localize_script('main-scripts', 'load_more', [
				'url' => admin_url('/admin-ajax.php'),
				'query_vars' => serialize($wp_query->query_vars),
				'current_page' => get_query_var('paged') ? get_query_var('paged') : 1,
				'max_pages' => $wp_query->max_num_pages,
				'load_text' => __('...', 'upd'),
				'end_while' => __('End', 'upd')
			]);
		}

		if (is_singular()) {
			wp_enqueue_script('comments', get_template_directory_uri() . '/src/js/comments.js', ['jquery'], true);
			
			if (comments_open()) {
				wp_enqueue_script('comment-reply');				
			}

		}
	}

	function ajax_load_more() {
		if ($_POST['query']) {
			$args = unserialize(stripslashes($_POST['query']));
			$args['paged'] = $_POST['page'] + 1;
			$args['post_status'] = 'publish';
		
			query_posts($args);
			
			if (have_posts()) {
				while(have_posts()): the_post();
					get_template_part('template-parts/content', get_post_type());
				endwhile;
			}
		} else {
			header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
		}

		die();		
	}
}

class upd_modify_theme {
	function __construct() {
		add_filter('document_title_separator', [$this, 'change_title_separator']);
		add_filter('walker_nav_menu_start_el', [$this, 'modify_soc_menu'], 10, 4);
		add_filter('excerpt_more', [$this, 'modify_excerpt_more']);
		add_filter('navigation_markup_template', [$this, 'modify_navigation_template'], 10, 2);
	}

	function change_title_separator($sep) { 
		$sep = '|';

		return $sep;
	}

	function modify_soc_menu($item_output, $item, $depth, $args) {
		if ($args->theme_location == 'social-menu') {
			$item_output = str_replace($item->title, '<i class="fa"></i>', $item_output); /* replace text menu items on fa icons */
		}

		return $item_output;
	}

	function modify_excerpt_more($more) {
		$more = '...';

		return $more;
	}

	function modify_navigation_template($template, $class) {
		return '
			<nav class="navigation %1$s" role="navigation">
				<div class="nav-links">%3$s</div>
			</nav>    
		';
	}
}

class upd_template_parts {
	public static function get_post_meta() {
		$meta = [];

		$k = 0;
		$meta[$k] = get_the_date('F j, Y');

		if (has_category()) {
			$k = 1;
			$meta[$k] = get_the_category_list(', ');
		}

		if (has_tag()) {
			$k = 2;
			$meta[$k] = __('tags:', 'upd') . ' ';
			$meta[$k] .= get_the_tag_list('', ', ', '');
		}

		$k = 3;
		$meta[$k] = __('by', 'upd') . ' ';
		$meta[$k] .= get_the_author_link();

		$meta = count($meta) > 0 ? '<div class="meta">' . implode(' / ', $meta) . '</div>': '';

		echo $meta;
	}

	public static function get_comment_hrd($comment_id = 0) {
		/* hrd - human readable date */
		return sprintf(__('%s ago', 'upd'), human_time_diff(get_comment_date('U', $comment_id), current_time('timestamp')));
	}

	public static function comment_view($comment, $args, $depth) {
		$classes = ' ' . comment_class((empty($args['has_children']) ? '' : 'parent') . ' ' . 'comment-wrap', null, null, false);
		$is_show_avatar = get_option('show_avatars');		
		$is_author = get_the_author_meta('ID') == $comment->user_id;
		?>
	
		<div <? echo $classes; ?> id="comment-<?php comment_ID() ?>">
			<div id="div-comment-<?php comment_ID() ?>" class="comment row">
				<div class="avatar <? echo $is_author ? 'author' : ''?>">
					<?
					if ($is_show_avatar && $args['avatar_size'] != 0) {
						echo get_avatar($comment, $args['avatar_size']);
					}

					if ($is_author) { ?>
						<div class="author-label"><? _e('Author', 'upd') ?></div>
					<? } ?>
				</div>
	
				<div class="comment-body">
					<div class="comment-meta">
						<div class="name"><? echo get_comment_author_link() ?></div>
	
						<div class="date">
							<i class="fa fa-clock-o" aria-hidden="true"></i>
							<? echo upd_template_parts::get_comment_hrd() ?>
						</div>

						<? if ($comment->comment_approved == '0') { ?>
							<div class="awaiting-moderation">
								<i class="fa fa-info-circle" aria-hidden="true"></i>
								<? _e('Awaiting for approval', 'upd'); ?>
							</div>
						<? }
	
						edit_comment_link(__('[edit]', 'upd'), '  ', '' ) ?>
					</div>

					<? if ($comment->comment_parent) { ?>
						<div class="reply-to">
							<i class="fa fa-comments-o" aria-hidden="true"></i>
							<? _e('Reply to', 'upd') ?>
							<a href="#comment-<? echo $comment->comment_parent ?>"><? echo get_comment($comment->comment_parent)->comment_author ?></a>
						</div>
					<? } ?>					
	
					<? comment_text() ?>
	
					<div class="reply">
						<?
						comment_reply_link(array_merge($args, [
							'depth' => $depth,
							'max_depth' => $args['max_depth'],
							'reply_text' => '<i class="fa fa-reply" aria-hidden="true"></i>' . __('Reply', 'upd')
						]))
						?>
					</div>			
				</div>
			</div>
		<?
		
		/* last div closed in comment_view_end */
	}

	public static function comment_view_end($comment, $args, $depth) {
		echo '</div>';
	}
}

class widget_personal_info {
	function __construct() {
		add_action('customize_register', [$this, 'customize_widget']);
		add_action('after_setup_theme', [$this, 'menus']);
	}

	function customize_widget(WP_Customize_Manager $wp_customize) {
		$transport = 'refresh';

		if ($section = 'display_options'){
			$wp_customize->add_section($section, [
				'title'     => __('Personal info', 'upd'),
				'description' => __('Customize static widget in the left sidebar', 'upd'),		
				'priority'  => 200
			]);


			$setting = 'upd_pi_enable';

			$wp_customize->add_setting($setting, [
				'default'            => true,
				'sanitize_callback'  => 'sanitize_text_field',
				'transport'          => $transport
			]);

			$wp_customize->add_control($setting, [
				'section'  => $section,
				'label'    => __('Enable widget', 'upd'),
				'type'     => 'checkbox'
			]);


			$setting = 'upd_pi_thumb';

			$wp_customize->add_setting($setting, [
				'default'      => get_template_directory_uri() . '/src/images/placeholder-270x229.png',
				'transport'    => $transport
			]);

			$wp_customize->add_control(
				new WP_Customize_Image_Control($wp_customize, $setting, [
					'section'  => $section,
					'label'    => __('Thumbnail', 'upd')
				])
			);

			
			$setting = 'upd_pi_name';

			$wp_customize->add_setting($setting, [
				'default'            => __('Alex Parker', 'upd'),
				'sanitize_callback'  => 'sanitize_text_field',
				'transport'          => $transport
			]);

			$wp_customize->add_control($setting, [
				'section'  => $section,
				'label'    => __('Yout name', 'upd'),
				'type'     => 'text'
			]);


			$setting = 'upd_pi_position';

			$wp_customize->add_setting($setting, [
				'default'            => __('Web deleloper', 'upd'),
				'sanitize_callback'  => 'sanitize_text_field',
				'transport'          => $transport
			]);

			$wp_customize->add_control($setting, [
				'section'  => $section,
				'label'    => __('Yout position', 'upd'),
				'type'     => 'text'
			]);
		}
	}

	function menus() {
		register_nav_menus([
			'main-menu' => __('Menu in sidebar', 'upd'),
			'social-menu' => __('Social menu in sidebar', 'upd')
		]);
	}
}

class upd_portfolio {
	function __construct() {
		add_action('init', [$this, 'register_portfolio_posts']);
		add_action('template_redirect', [$this, 'disable_single'], 99);
		add_action('wp_enqueue_scripts', [$this, 'scripts']);
		add_action('admin_enqueue_scripts', [$this, 'admin_scripts']);		
		add_action('add_meta_boxes', [$this, 'add_meta_box']);		
		add_action('save_post', [$this, 'save_meta_box']);
	}

	function register_portfolio_posts() {
		$labels = [
			'name' => __('Portfolio', 'upd'),
			'all_items' => __('All works', 'upd'),
			'singular_name' => __('Works', 'upd'),
			'add_new' => __('Add work', 'upd'),
			'add_new_item' => __('Add new work', 'upd'),
			'edit_item' => __('Edit', 'upd'),
			'new_item' => __('New work', 'upd'),
			'view_item' => __('View', 'upd'),
			'search_items' => __('Find', 'upd'),
			'not_found' => __('Nothing found', 'upd'),
			'not_found_in_trash' => __('There is nothing in the basket', 'upd'),
			'parent_item_colon' => '',
			'menu_name' => __('Portfolio', 'upd')
		];

		$args = [
			'labels' => $labels,
			'public' => true,
			'publicly_queryable' => true,
			'exclude_from_search' => true,
			'show_in_rest' => false,
			'show_in_menu' => true,
			'show_in_admin_bar' => true,
			'has_archive' => true,
			'hierarchical' => false,
			'rewrite' => true,
			'capability_type' => 'post',
			'menu_position' => null,
			'menu_icon' => 'dashicons-format-aside',
			'supports' => ['title','editor']
		];

		register_post_type('portfolio', $args);
	}

	function disable_single() {
		if (is_singular('portfolio')) {
			wp_redirect(get_post_type_archive_link('portfolio'));
			die();
	  }
	}

	function scripts() {
		if (is_post_type_archive('portfolio')) {
			wp_enqueue_style('owl-carousel', get_template_directory_uri() . '/src/css/owl-carousel.css');
			wp_enqueue_style('owl-owl-portfolio-thumbnails-theme', get_template_directory_uri() . '/src/css/owl-portfolio-thumbnails.css');
			wp_enqueue_script('owl-carousel', get_template_directory_uri() . '/src/js/owl-carousel.js', true);

			wp_enqueue_script('portfolio-scripts', get_template_directory_uri() . '/src/js/portfolio-scripts.js', ['jquery', 'owl-carousel'], true);
		}
	}

	function admin_scripts() {
		if (get_current_screen()->post_type == 'portfolio' && get_current_screen()->base == 'post') {
			wp_enqueue_style('default-text-field',  get_template_directory_uri() . '/src/css/default-text-field.css');

			wp_enqueue_media();
			wp_enqueue_style('multiple-thumbnails-field',  get_template_directory_uri() . '/src/css/multiple-thumbnails-field.css');
			wp_enqueue_script('multiple-thumbnails-field', get_template_directory_uri() . '/src/js/multiple-thumbnails-field.js', ['jquery'], null, true);
			wp_localize_script('multiple-thumbnails-field', 'mf_text', [
				'title_popup' => __('Select image', 'upd'),
				'btn_select_popup' => __('Select', 'upd')
			]);

			add_action('admin_footer', function() { /* disable single view elements */
				echo '<script>jQuery("#edit-slug-box, #wp-admin-bar-view, #post-preview, #misc-publishing-actions, #minor-publishing-actions").hide();</script>';
			});
		}
	}

	function add_meta_box($post_type) {
		if ($post_type == 'portfolio') {
			add_meta_box('portfolio-link',	__('Link', 'upd'), [$this, 'render_meta_box_link'], $post_type, 'advanced', 'high');			
			add_meta_box('portfolio-info',	__('Thumbnails', 'upd'), [$this, 'render_meta_box_thumbnails'], $post_type, 'advanced', 'high');
		}
	}

	function render_meta_box_link($post) {
		$value = get_post_meta($post->ID, 'portfolio-link', true);

		echo '<div class="portfolio-link default-text-field">';
		echo '<label for="portfolio-link">' . __('Link to work', 'upd') . '</label>';
		echo '<input type="url" id="portfolio-link" name="portfolio-link" placeholder="' . __('Enter link to the work', 'upd') . '" value="' . esc_attr($value) . '">';
		echo '</div>';
	}

	function render_meta_box_thumbnails($post) {
		$values = get_post_meta($post->ID, 'portfolio-thumbnails', true);

		echo '<div id="multiple-thumbnails" class="multiple-thumbnails">';
		echo '<label>' . __('Choose thumbnails', 'upd') . '</label>';
		echo '<div class="thumbnails">';

		if (!empty($values)) {
			foreach ($values as $id) {
				echo '<div class="item">';
				echo '<input type="hidden" name="portfolio-thumbnails[]" value="' . $id . '">';
				echo '<div class="remove"><span class="dashicons dashicons-no-alt"></span></div>';
				echo '<img src="' . wp_get_attachment_image_url($id, 'portfolio-thumbnail') . '" alt="">';
				echo '</div>';
			}
		}

		echo '</div>';
		echo '<button class="add-thumbnails button">' . __('Add thumbnails', 'upd') . '</button>';
		echo '</div>';
	}

	function save_meta_box($post_id) {
		$link = isset($_POST['portfolio-link']) ? sanitize_text_field($_POST['portfolio-link']) : '';
		$thumbnails = isset($_POST['portfolio-thumbnails']) ? (array) $_POST['portfolio-thumbnails'] : [];

		update_post_meta($post_id, 'portfolio-link', $link);
		update_post_meta($post_id, 'portfolio-thumbnails', $thumbnails);
	}
}

class upd_testimonials {
	function __construct() {
		add_action('init', [$this, 'register_portfolio_posts']);
		add_action('admin_enqueue_scripts', [$this, 'admin_scripts']);
		add_filter('enter_title_here', [$this, 'change_placeholder_title'], 10, 2);
		add_action('add_meta_boxes', [$this, 'add_meta_box']);		
		add_action('save_post', [$this, 'save_meta_box']);
		add_shortcode('testimonials', [$this, 'testimonials_view']);
	}

	function register_portfolio_posts() {
		$labels = [
			'name' => __('Testimonials', 'upd'),
			'all_items' => __('All testimonial', 'upd'),
			'singular_name' => __('Testimonials', 'upd'),
			'add_new' => __('Add testimonial', 'upd'),
			'add_new_item' => __('Add new testimonial', 'upd'),
			'edit_item' => __('Edit', 'upd'),
			'new_item' => __('New testimonial', 'upd'),
			'view_item' => __('View', 'upd'),
			'search_items' => __('Find', 'upd'),
			'not_found' => __('Nothing found', 'upd'),
			'not_found_in_trash' => __('There is nothing in the basket', 'upd'),
			'parent_item_colon' => '',
			'menu_name' => __('Testimonials', 'upd')
		];

		$args = [
			'labels' => $labels,
			'public' => true,
			'publicly_queryable' => false,
			'exclude_from_search' => true,
			'show_in_rest' => false,
			'show_in_menu' => true,
			'show_in_admin_bar' => true,
			'show_in_nav_menus' => false,
			'has_archive' => false,
			'hierarchical' => false,
			'rewrite' => false,
			'capability_type' => 'post',
			'menu_position' => null,
			'menu_icon' => 'dashicons-format-status',
			'supports' => ['title','editor', 'thumbnail']
		];

		register_post_type('testimonials', $args);
	}

	function admin_scripts() {
		if (get_current_screen()->post_type == 'testimonials' && get_current_screen()->base == 'post') {
			wp_enqueue_style('default-text-field',  get_template_directory_uri() . '/src/css/default-text-field.css');
		}
	}

	function change_placeholder_title($title) {
		global $post_type;

		if ($post_type == 'testimonials') {
			return __('Author name', 'upd');
		}

		return $title;
	}

	function add_meta_box($post_type) {
		if ($post_type == 'testimonials') {
			add_meta_box('author-posotion',	__('Position', 'upd'), [$this, 'render_meta_box_position'], $post_type, 'advanced', 'high');			
		}
	}

	function render_meta_box_position($post) {
		$value = get_post_meta($post->ID, 'author-posotion', true);

		echo '<div class="author-posotion default-text-field">';
		echo '<label for="author-posotion">' . __('Enter the position of author testimonial', 'upd') . '</label>';
		echo '<input type="text" id="author-posotion" name="author-posotion" placeholder="' . __('Position of author', 'upd') . '" value="' . esc_attr($value) . '">';
		echo '</div>';
	}

	function save_meta_box($post_id) {
		$link = isset($_POST['author-posotion']) ? sanitize_text_field($_POST['author-posotion']) : '';

		update_post_meta($post_id, 'author-posotion', $link);
	}

	function testimonials_view($atts) {
		global $post;

		$atts = shortcode_atts([
			'count' => 4,
			'random' => false
		], $atts);

		$args = [
			'post_type' => 'testimonials',
			'posts_per_page' => $atts['count']
		];

		if ($atts['random']) {
			$args['orderby'] = 'rand';
			$args['order'] = 'ASC';
		}

		$output = '';		
		$testimonials = get_posts($args);

		if ($testimonials) {
			foreach($testimonials as $post) {
				setup_postdata($post);

				ob_start();
				get_template_part('template-parts/content', get_post_type());
				$output .= ob_get_contents();
				ob_end_clean();
			}

			wp_reset_postdata();
		}

		return '<div class="testimonials row">' . $output . '</div>';
	}
}

class upd_mail_subcribe {
	function __construct() {
		add_action('customize_register', [$this, 'customize_register']);		
		add_action('wp_ajax_subscribe_email', [$this, 'subscribe_email']);
		add_action('wp_ajax_nopriv_subscribe_email', [$this, 'subscribe_email']);
	}

	function customize_register(WP_Customize_Manager $wp_customize) {
		$transport = 'refresh';

		if ($section = 'suncribe_from') {
			$wp_customize->add_section($section, [
				'title'     => __('Subscribe form', 'upd'),
				'description' => __('Options subscribe form', 'upd'),		
				'priority'  => 200
			]);


			$setting = 'upd_sf_enable';

			$wp_customize->add_setting($setting, [
				'default'            => true,
				'sanitize_callback'  => 'sanitize_text_field',
				'transport'          => $transport
			]);

			$wp_customize->add_control($setting, [
				'section'  => $section,
				'label'    => __('Enable form', 'upd'),
				'type'     => 'checkbox'
			]);


			$setting = 'upd_sf_mailchimp_api_key';

			$wp_customize->add_setting($setting, [
				'default'            => '',
				'sanitize_callback'  => 'sanitize_text_field',
				'transport'          => $transport
			]);

			$wp_customize->add_control($setting, [
				'section'  => $section,
				'label'    => __('Mailchimp api key', 'upd'),
				'type'     => 'text'
			]);


			$setting = 'upd_sf_mailchimp_list_id';

			$wp_customize->add_setting($setting, [
				'default'            => '',
				'sanitize_callback'  => 'sanitize_text_field',
				'transport'          => $transport
			]);

			$wp_customize->add_control($setting, [
				'section'  => $section,
				'label'    => __('Mailchimp list id', 'upd'),
				'type'     => 'text'
			]);
		}
	}

	function subscribe_email() {
		if ($_POST['email']) {
			$result = $this->subcribe_mailchimp($_POST);
			$result = json_decode($result);

			if ($result->status == 400) {
				echo $result->detail;
			} else if ($result->status == 'subscribed') {
				echo __('Thank you. You have subscribed successfully', 'upd');
			}				
		} else {
			header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
		}
		
		die();
	}

	function check_data_mailchimp() {
		$this->api_key = get_theme_mod('upd_sf_mailchimp_api_key');
		$this->list_id = get_theme_mod('upd_sf_mailchimp_list_id');

		if (empty($this->api_key) || empty($this->list_id)) {
			echo __('Mailchimp api key or list id is empty', 'upd');
			die();
		}
	}

	function subcribe_mailchimp($data) {
		$this->check_data_mailchimp();

		$memberId = md5(strtolower($data['email']));
		$dataCenter = substr($this->api_key,strpos($this->api_key,'-')+1);
		$url = 'https://' . $dataCenter . '.api.mailchimp.com/3.0/lists/' . $this->list_id . '/members/' . $memberId;
	
		$json = json_encode([
			'email_address' => $data['email'],
			'status'        => 'subscribed', // 'subscribed','unsubscribed','cleaned','pending'
			'merge_fields'  => [
				'FNAME'     => '',
				'LNAME'     => ''
			]
		]);
	
		$ch = curl_init($url);
	
		curl_setopt($ch, CURLOPT_USERPWD, 'user:' . $this->api_key);
		curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json);                                                                                                                 
	
		$result = curl_exec($ch);
		//$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		curl_close($ch);
	
		return $result;
	}
}


/**
 * Intall supports, widget, menus, lang, scripts, ajax requests.
 */
new upd_istall_theme();

/**
 * Modify different elements theme.
 */
new upd_modify_theme();

/**
 * Static widget personal info component.
 */
new widget_personal_info();

/**
 * Portfolio component.
 */
new upd_portfolio();

/**
 * Testimonials component.
 */
new upd_testimonials();

/**
 * Mail subscribe component.
 */
new upd_mail_subcribe();