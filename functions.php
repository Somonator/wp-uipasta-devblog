<?php
class upd_istall_theme {
	function __construct() {
		add_action('after_setup_theme', [$this, 'setup']);
		add_action('after_setup_theme', [$this, 'menus']);
		add_action('after_setup_theme', [$this, 'image_sizes']);
		add_action('wp_enqueue_scripts', [$this, 'scripts']);
		add_filter('use_block_editor_for_post', '__return_false', 10); //disable gutenberg
	}
	
	function setup() {
		add_theme_support('title-tag');
		add_theme_support('menus');
		add_theme_support('post-thumbnails');
		add_theme_support(
			'html5',
			[
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
				'style',
				'script',
			]
		);
		add_theme_support('customize-selective-refresh-widgets');
	}

	function menus() {
		register_nav_menus(
			[
				'main-menu' => __('Menu in sidebar', 'upd'),
				'social-menu' => __('Social menu in sidebar', 'upd'),
				'footer-menu' => __('Menu in footer', 'upd')
			]
		);
	}

	function image_sizes() {
		set_post_thumbnail_size(810, 326, true);
		add_image_size('portfolio-thumbnail', 516, 500, true);
	}

	function scripts() {
		wp_enqueue_style('poppins-font', 'https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap');
		wp_enqueue_style('font-awesome', get_template_directory_uri() . '/src/css/font-awesome.css');
		wp_enqueue_style('main-styles', get_template_directory_uri() . '/src/css/style.css');		

		wp_enqueue_script('jquery', true);
		wp_enqueue_script('main-scripts', get_template_directory_uri() . '/src/js/scripts.js', ['jquery'], true);

		if (is_archive()) {
			global $wp_query;

			wp_localize_script('main-scripts', 'ajax_data', [
				'url' => admin_url('/admin-ajax.php'),
				'query_vars' => serialize($wp_query->query_vars),
				'current_page' => get_query_var('paged') ? get_query_var('paged') : 1,
				'max_pages' => $wp_query->max_num_pages,
				'load_text' => __('...', 'upd'),
				'end_while' => __('End', 'upd'),			
				'error_ajax' => __('Error ajax request', 'upd')
			]);
		}

	}
}

class upd_customize {
	function __construct() {
		add_action('customize_register', [$this, 'my_theme_customize_register']);
	}

	function my_theme_customize_register(WP_Customize_Manager $wp_customize) {
		$transport = 'refresh';

		if ($section = 'display_options'){
			$wp_customize->add_section($section, [
				'title'     => __('Personal info', 'upd'),
				'description' => __('Customize static widget in the left sidebar ', 'upd'),		
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
				'default'            => 'Alex Parker',
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
}

class upd_modify_theme {
	function __construct() {
		add_filter('document_title_separator', [$this, 'change_title_separator']);
		add_filter('walker_nav_menu_start_el', [$this, 'modify_soc_menu'], 10, 4);
		add_filter('excerpt_more', [$this, 'modify_excerpt_more']);
	}

	function change_title_separator($sep) { 
		$sep = '|';

		return $sep;
	}

	function modify_soc_menu($item_output, $item, $depth, $args) {
		if ($args->theme_location == 'social-menu') {
			$item_output = str_replace($item->title, '<i class="fa"></i>', $item_output);
		}

		return $item_output;
	}

	function modify_excerpt_more($more) {
		$more = '...';

		return $more;
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
			wp_enqueue_style('portfolio-link-field',  get_template_directory_uri() . '/src/css/portfolio-link-field.css');

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

		echo '<div class="portfolio-link">';
		echo '<label for="portfolio-link">' . __('Enter link to project', 'upd') . '</label>';
		echo '<input type="url" id="portfolio-link" name="portfolio-link" placeholder="' . __('Enter link to the work') . '" value="' . esc_attr($value) . '">';
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
		$thumbnails = isset($_POST['portfolio-thumbnails']) ? (array) $_POST['portfolio-thumbnails'] : array();

		update_post_meta($post_id, 'portfolio-link', $link);
		update_post_meta($post_id, 'portfolio-thumbnails', $thumbnails);
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
}

class upd_ajax_requests {
	function __construct() {
		add_action('wp_ajax_load_more', [$this, 'load_more']);
		add_action('wp_ajax_nopriv_load_more', [$this, 'load_more']);
	}

	function load_more() {
		if ($_POST['query']) {
			$args = unserialize(stripslashes($_POST['query']));
			$args['paged'] = $_POST['page'] + 1;
			$args['post_status'] = 'publish';
		
			query_posts( $args );
			
			if(have_posts()) :
				while(have_posts()): the_post();
					get_template_part('template-parts/content', get_post_type());
				endwhile;
			endif;

			die();
		} else {
			header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
		}
	}
}

new upd_istall_theme();
new upd_customize();
new upd_modify_theme();
new upd_portfolio();
new upd_ajax_requests();