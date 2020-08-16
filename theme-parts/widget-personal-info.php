<?
$pi_enable = get_theme_mod('upd_pi_enable');
$thumbnail = get_theme_mod('upd_pi_thumb');
$name = get_theme_mod('upd_pi_name');
$position = get_theme_mod('upd_pi_position');

if ($pi_enable) { ?>
    <div id="personal-info" class="widget-info block-appearance">
        <div class="thumbnail">
            <? if(has_nav_menu('main-menu')) { ?>
                <a href="#" id="trigger-main-menu" class="trigger-main-menu center">
                    <i class="fa fa-bars" aria-hidden="true"></i>
                </a>
            <?
            }

            if ($thumbnail) {
            ?>
                <img src="<? echo $thumbnail ?>" alt="">
            <? } ?>  
        </div>

        <? wp_nav_menu([ 
            'container' => 'nav',
            'container_id' => 'main-menu',
            'container_class' => 'main-menu',
            'menu_id' => false,
            'menu_class' => 'menu',
            'theme_location'  => 'main-menu',
            'fallback_cb' => '__return_empty_string'
        ]) ?>

        <div class="info">
            <? if ($name) { ?>
                <div class="name"><? echo $name ?></div>
            <?
            }

            if ($position) { ?>
                <div class="position"><? echo $position ?></div>
            <? } ?>
        </div>

        <? wp_nav_menu([ 
            'container' => 'nav',
            'container_class' => 'social-menu',
            'menu_class' => 'menu',
            'theme_location'  => 'social-menu',
            'fallback_cb' => '__return_empty_string'
        ]) ?>
    </div>
<? } ?>