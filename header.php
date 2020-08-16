<?
$head_tag = is_front_page() ? 'h1' : 'div';
?>

<!doctype html>
<html <? language_attributes() ?>>
<head>
	<meta charset="<? bloginfo('charset') ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<? wp_head() ?>
</head>

<body <? body_class('overflow-hidden') ?>> <? wp_body_open() ?>
    <div id="preloader" class="preloader">
        <div class="rounder"></div>
    </div>

    <div class="wrap row container">
        <? get_sidebar() ?>

        <main class="main">
            <div class="page-wrap block-appearance">
                <div class="page-heading">
                    <?
                    printf('<%1$s class="page-title">%2$s</%1$s>', $head_tag, get_bloginfo('name'));

                    if (is_single() && comments_open()) { ?>
                        <a href="#comments" class="link">
                            <i class="fa fa-comments" aria-hidden="true"></i>
                        </a>
                    <? } else if (!is_front_page()) { ?>
                        <a href="<? echo home_url() ?>" class="link">
                            <i class="fa fa-home" aria-hidden="true"></i>
                        </a>
                    <? } ?>
                </div>

                <div class="page-content">
