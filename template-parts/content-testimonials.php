<div class="item">
    <div class="content">
        <p><? echo get_the_content() ?></p>
        <? edit_post_link(__('[edit]', 'upd'), '', '', $post->ID, 'edit') ?>
    </div>
    <div class="author row">
        <? if (get_the_post_thumbnail()) { ?>
            <div class="avatar">
                <img src="<? echo get_the_post_thumbnail_url() ?>" alt="">
            </div>
        <? } ?>
        <div class="personal">
            <div class="name"><? the_title() ?></div>
            <? if ($position = get_post_meta($post->ID, 'author-posotion', true)) { ?>
                <div class="position"><? echo $position ?></div>
            <? } ?>
        </div>
    </div>
</div>