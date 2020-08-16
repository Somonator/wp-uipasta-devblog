<? 
if (!get_theme_mod('upd_pi_enable') && !is_active_sidebar('left-sidebar')) {
    return;
}
?>

<aside class="sidebar">
    <? 
    get_template_part('theme-parts/widget-personal-info');
    dynamic_sidebar('left-sidebar');
    ?>
</aside>