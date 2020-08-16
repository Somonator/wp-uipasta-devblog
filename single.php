<? 
get_header();

$args = [
	'post_type' => get_post_type(),
	'orderby' => 'rand',	
	'posts_per_page' => 3,
	'post__not_in' => [get_the_id()]
];

$read_also = new WP_Query($args);

while (have_posts()): the_post();
	?>
	<article id="article-<? the_ID() ?>" <? post_class('post') ?>>
		<? if (get_the_post_thumbnail()) { ?>
			<div class="thumbnail">
				<? the_post_thumbnail() ?>
			</div>
		<? } ?>

		<h1 class="title"><? the_title() ?></h1>
		
		<? upd_template_parts::get_post_meta() ?>
		<? the_content(); ?>
	</article>
<? endwhile ?>

<div class="author-info row">
	<div class="avatar">
		<img src="<? echo get_avatar_url(get_the_author_meta('ID')) ?>" alt="">
	</div>
	<div class="info">
		<div class="by"><? echo __('Article by', 'upd') . ' ' . get_the_author_link() ?></div>
		<p><? echo get_the_author_meta('user_description') ?></p>
	</div>
</div>

<? if ($read_also->have_posts()) { ?>
	<div class="read-also">
		<div class="title"><? _e('You may also like', 'upd') ?></div>
		<div class="posts row">
			<? while($read_also->have_posts()): $read_also->the_post(); ?>
				<div class="ra-post">
					<a href="<? the_permalink() ?>" class="link"><? the_title() ?></a>
				</div>
			<?
			endwhile; 
			wp_reset_query();
			?>
		</div>
	</div>
<? }

if (comments_open() || get_comments_number()) {
	comments_template();
}

get_footer()
?>