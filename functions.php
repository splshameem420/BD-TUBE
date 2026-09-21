<?php
function bd_tube_main() { 
    // WordPress Content Support & Menus
    add_theme_support( 'title-tag' ); 
    add_theme_support( 'custom-logo' ); 
    add_theme_support( 'post-thumbnails' );
    
    register_nav_menus( array(
        'primary' => __( 'Primary Menu', 'bd_tube' ),
    ) );
}
add_action( 'after_setup_theme', 'bd_tube_main' );


function bdtube_register_menus() {
    register_nav_menus( array(
        'drawer-footer-menu' => __( 'Drawer Footer Menu', 'bdtube' ),
    ) );
}
add_action( 'init', 'bdtube_register_menus' );


function bd_tube_enqueue_styles() {
    wp_enqueue_style('main-style', get_stylesheet_uri());
}
add_action('wp_enqueue_scripts', 'bd_tube_enqueue_styles');

function bd_tube_header() { 
    // Header CSS
    wp_enqueue_style( 'bd-tube-header', get_template_directory_uri() . '/header/header.css', array(), null );
    
    // Mobile CSS
    wp_enqueue_style( 'bd-tube-header-mobile', get_template_directory_uri() . '/header/mobile.css', array( 'bd-tube-header' ), null, 'only screen and (max-width: 768px)' );
    
    // Header JS
    wp_enqueue_script( 'bd-tube-header', get_template_directory_uri() . '/header/header.js', array(), null, true );
}
add_action( 'wp_enqueue_scripts', 'bd_tube_header' );