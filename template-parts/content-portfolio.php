<?
$thumbnails = get_post_meta($post->ID, 'portfolio-thumbnails', true);
$link = get_post_meta($post->ID, 'portfolio-link', true);
?>

<article class="work row">
	<div class="thumbnails">
		<?
		if ($thumbnails && is_array($thumbnails)) {
			if (count($thumbnails) == 1) { ?>
				<img src="<? echo wp_get_attachment_image_url($thumbnails[0], 'portfolio-thumbnail') ?>" alt="">
			<? } else { ?>
				<div class="portfolio-thumbnails owl-portfolio-thumbnails">
					<? foreach($thumbnails as $id) { ?>
						<img src="<? echo wp_get_attachment_image_url($id, 'portfolio-thumbnail') ?>" alt="">
					<? } ?>
				</div>
			<?
			}
		}
		?>
	</div>
	
	<div class="info">
		<table>
			<tr>
				<? if ($link) { ?>
					<th><? _e('Url', 'upd') ?></th>
					<td><a href="<? echo $link ?>" target="__blank"><? the_title() ?></a></td>
				<? } else { ?>
					<th><? _e('Title', 'upd') ?></th>
					<td><? the_title() ?></td>
				<? } ?>

			</tr>
			<tr>
				<th><? _e('About Project', 'upd') ?></th>
				<td><? the_content() . ' ' . edit_post_link(__('[edit]', 'upd'), '', '', $post->ID, 'edit') ?></td>
			</tr>
		</table>
	</div>
</article>