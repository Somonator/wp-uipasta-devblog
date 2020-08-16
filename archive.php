<? 
get_header();

$is_pagination = $wp_query->max_num_pages > 1;

the_archive_title('<h1 class="archive-title">', '</h1>');

if (have_posts()) :
?>
	<div class="blog-posts">
		<?
		while (have_posts()) : the_post();
			get_template_part('template-parts/content', get_post_type());
		endwhile;
		?>
	</div>

	<div id="load-more" class="<? echo $is_pagination ? 'load-more' : 'load-more end' ?>"><? echo $is_pagination ? __('Load', 'upd') : __('End', 'upd') ?></div>
<?
else :
	get_template_part('template-parts/content', 'none');
endif;

get_footer();
?>