<? 
get_header();

while (have_posts()): the_post();
	?>
	<article id="page-<? the_ID(); ?>" <? post_class('post') ?>>
		<? if (get_the_post_thumbnail()) { ?>
			<div class="thumbnail">
				<? the_post_thumbnail() ?>
			</div>
		<? } ?>

		<h1 class="title"><? the_title() ?></h1>

		<? the_content(); ?>
	</article>
	<?
endwhile;

get_footer();
?>