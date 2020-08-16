<? 
$sf_enable = get_theme_mod('upd_sf_enable');

if ($sf_enable) {
?>
    <div class="subscribe">
        <form id="subscribe-form" action="#" class="subscribe-form">
            <input type="email" placeholder="<? _e('Email Address', 'upd') ?>" name="email" class="email">
            <input type="submit" class="submit" value="<? _e('Submit', 'upd') ?>">
        </form>

        <p><? _e('Subscribe to my weekly newsletter', 'upd') ?></p>

        <p id="subscribe-notice" style="display: none;"></p>
    </div>
<? } ?>