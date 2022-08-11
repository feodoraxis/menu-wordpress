<?php
if ( !defined('ABSPATH') ) {
    die();
}

add_action( 'wp_enqueue_scripts', 'enqueue_scripts' );
function enqueue_scripts() {
    wp_enqueue_style( 'bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css', [], wp_get_theme()->get( 'Version' ) );
    wp_enqueue_style( 'font-awesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css', [], wp_get_theme()->get( 'Version' ) );

    wp_enqueue_script( 'jquery' );
    wp_enqueue_script(
        'main',
        get_template_directory_uri() . '/assets/js/main.js',
        [ 'jquery' ],
        wp_get_theme()->get( 'Version' ),
        true
    );

    wp_enqueue_script(
        'bootstrap',
        'https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js',
        [],
        wp_get_theme()->get( 'Version' ),
        true
    );
}
