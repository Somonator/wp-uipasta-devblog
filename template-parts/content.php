<article id="article-<? the_ID() ?>" <? post_class('post') ?>>
	<? if (get_the_post_thumbnail()) { ?>
		<div class="thumbnail">
			<? the_post_thumbnail() ?>
		</div>
	<? } ?>

	<a href="<? the_permalink() ?>">
		<h2 class="title"><? the_title() ?></h2>
	</a>

	<? upd_template_parts::get_post_meta() ?>
	<? the_excerpt() ?>
	
	<a href="<? the_permalink() ?>" class="read-more"><? _e('Read more', 'upd') ?></a>
</article>