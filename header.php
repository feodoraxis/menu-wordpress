<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="format-detection" content="telephone=no">
    <meta name="tagline" content="https://feodoraxis.ru/">
    <meta name="theme-color" content="#FFF">
    <?php wp_head(); ?>
</head>
<body>

    <div class="container">

        <nav class="navbar navbar-expand-lg bg-light">
            <div class="container-fluid">
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <?php
                    wp_nav_menu( [
                        'theme_location' => 'primary',
                        'container'      => false,
                        'menu_class'     => 'navbar-nav me-auto mb-2 mb-lg-0',
                        'walker'         => new Feodoraxis_Walker_Nav_Menu(),
                    ] );
                    ?>
                </div>
            </div>
        </nav>

    </div>