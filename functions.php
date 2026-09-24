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


function bd_tube_main_styles() {
    wp_enqueue_style('main-style', get_stylesheet_uri());
}
add_action('wp_enqueue_scripts', 'bd_tube_main_styles');

function bd_tube_header() { 
    // Header CSS
    wp_enqueue_style( 'bd-tube-header', get_template_directory_uri() . '/header/header.css', array(), null );
    
    // Mobile CSS
    wp_enqueue_style( 'bd-tube-header-mobile', get_template_directory_uri() . '/header/mobile.css', array( 'bd-tube-header' ), null, 'only screen and (max-width: 768px)' );
    
    // Header JS
    wp_enqueue_script( 'bd-tube-header', get_template_directory_uri() . '/header/header.js', array(), null, true );
}
add_action( 'wp_enqueue_scripts', 'bd_tube_header' );

function bd_tube_homepage() { 
    // Homepage CSS
    wp_enqueue_style( 'bd-tube-homepage', get_template_directory_uri() . '/Homepage/Homepage.css', array(), null );
}
add_action( 'wp_enqueue_scripts', 'bd_tube_homepage' );

// Views Counter Function

function bdtube_set_post_views($postID) {
    $count_key = 'post_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if($count == ''){
        $count = 0;
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
    } else {
        $count++;
        update_post_meta($postID, $count_key, $count);
    }
}

function bdtube_get_post_views($postID) {
    $count_key = 'post_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if($count == '' || $count == 0){
        return "0 views";
    }
    
    // ১০০০ হলে K এবং ১০০০০০০ হলে M দেখাবে
    if ($count >= 1000000) {
        $count = round($count / 1000000, 1) . 'M';
    } elseif ($count >= 1000) {
        $count = round($count / 1000, 1) . 'K';
    }
    
    return $count . ' views';
}

// অটোমেটিক লাইভ ডিটেক্টর ও ভিউ ফাংশন
if ( ! function_exists( 'bdtube_get_video_stats' ) ) {
    function bdtube_get_video_stats($post_id) {
        $post = get_post($post_id);
        $is_live = false;

        $meta_live = get_post_meta($post_id, 'is_live_stream', true);
        if ($meta_live === 'yes') {
            $is_live = true;
        }

        if (!$is_live && has_category(array('live', 'live-stream', 'লাইভ'), $post_id)) {
            $is_live = true;
        }

        if (!$is_live && strpos($post->post_content, '.m3u8') !== false) {
            $is_live = true;
        }

        if (!$is_live && strpos($post->post_content, '.ts') !== false) {
            $is_live = true;
        }

        if (!$is_live && strpos($post->post_content, '.mpd') !== false) {
            $is_live = true;
        }

        if ($is_live) {
            $watching = get_post_meta($post_id, 'live_watching_count', true);
            $watching = !empty($watching) ? intval($watching) : 0;
            
            if ($watching >= 1000000) {
                $formatted = round($watching / 1000000, 1) . 'M';
            } elseif ($watching >= 1000) {
                $formatted = round($watching / 1000, 1) . 'K';
            } else {
                $formatted = $watching;
            }
            
            return $formatted . ' watching now';
        } else {
            return bdtube_get_post_views($post_id);
        }
    }
}

function bdtube_auto_select_live_template( $single_template ) {
    global $post;

    if ( $post && function_exists( 'bdtube_get_video_stats' ) ) {
        $stats_text = bdtube_get_video_stats( $post->ID );
        if ( strpos( $stats_text, 'watching now' ) !== false ) {
            $live_template = locate_template( 'single-live.php' );
            if ( $live_template ) {
                return $live_template;
            }
        }
    }

    return $single_template;
}
add_filter( 'single_template', 'bdtube_auto_select_live_template' );


function bdtube_reset_post_time_on_live_end( $post_id, $post, $update ) {
    if ( !$update || wp_is_post_revision( $post_id ) ) {
        return;
    }

    $is_live = false;
    $meta_live = get_post_meta($post_id, 'is_live_stream', true);
    if ($meta_live === 'yes') {
        $is_live = true;
    }
    if (!$is_live && preg_match('/\blive\b/i', $post->post_title)) {
        $is_live = true;
    }

    $was_live = get_post_meta($post_id, '_was_previously_live', true);

    if ($is_live) {
        update_post_meta($post_id, '_was_previously_live', 'yes');
    } elseif ($was_live === 'yes' && !$is_live) {
        remove_action( 'save_post', 'bdtube_reset_post_time_on_live_end', 10 );
        
        wp_update_post( array(
            'ID'            => $post_id,
            'post_date'     => current_time('mysql'),
            'post_date_gmt' => current_time('mysql', 1)
        ) );
        
        delete_post_meta($post_id, '_was_previously_live');
        
        add_action( 'save_post', 'bdtube_reset_post_time_on_live_end', 10, 3 );
    }
}
add_action( 'save_post', 'bdtube_reset_post_time_on_live_end', 10, 3 );

// Shorts Function
function bdtube_shorts_rewrite_rules() {
    add_rewrite_rule(
        '^shorts/([^/]+)/?$',
        'index.php?name=$matches[1]&is_shorts_feed=1',
        'top'
    );
}
add_action('init', 'bdtube_shorts_rewrite_rules');

function bdtube_register_shorts_query_var($vars) {
    $vars[] = 'is_shorts_feed';
    return $vars;
}
add_filter('query_vars', 'bdtube_register_shorts_query_var');

function bdtube_is_short($post_id) {
    $post = get_post($post_id);
    if (!$post) return false;

    // Custom Field, Category বা Title-এ #shorts থাকলে Short ধরা হবে
    $meta_short = get_post_meta($post_id, 'is_short_video', true);
    if ($meta_short === 'yes') return true;

    if (has_category(array('shorts', 'short'), $post_id)) return true;

    if (preg_match('/#shorts\b/i', $post->post_title)) return true;

    return false;
}

// Subscription Feed Function
function bdtube_subscriptions_rewrite_rules() {
    add_rewrite_rule(
        '^for/subscriptions/?$',
        'index.php?is_subscriptions_page=1',
        'top'
    );
    
    add_rewrite_rule(
        '^for/channels/?$',
        'index.php?is_sub_channels_page=1',
        'top'
    );
    
    add_rewrite_rule(
        '^for/you/?$',
        'index.php?is_sub_you_page=1',
        'top'
    );

    add_rewrite_rule(
        '^for/history/?$',
        'index.php?is_sub_history_page=1',
        'top'
    );

    add_rewrite_rule(
        '^for/playlists/?$',
        'index.php?is_sub_playlist_page=1',
        'top'
    );

    add_rewrite_rule(
        '^for/watched/?$',
        'index.php?is_sub_watched_page=1',
        'top'
    );
}
add_action( 'init', 'bdtube_subscriptions_rewrite_rules' );

function bdtube_register_subscriptions_query_var( $vars ) {
    $vars[] = 'is_subscriptions_page';
    $vars[] = 'is_sub_channels_page';
    $vars[] = 'is_sub_you_page';
    $vars[] = 'is_sub_history_page';
    $vars[] = 'is_sub_playlist_page';
    $vars[] = 'is_sub_watched_page';
    return $vars;
}
add_filter( 'query_vars', 'bdtube_register_subscriptions_query_var' );

function bdtube_subscriptions_template_include( $template ) {
    if ( get_query_var( 'is_subscriptions_page' ) ) {
        $new_template = locate_template( array( 'page-subscriptions.php' ) );
        if ( ! empty( $new_template ) ) {
            return $new_template;
        }
    }
    
    if ( get_query_var( 'is_sub_channels_page' ) ) {
        $new_template = locate_template( array( 'page-subscription-channels.php' ) );
        if ( ! empty( $new_template ) ) {
            return $new_template;
        }
    }

    if ( get_query_var( 'is_sub_you_page' ) ) {
        $new_template = locate_template( array( 'page-subscription-you.php' ) );
        if ( ! empty( $new_template ) ) {
            return $new_template;
        }
    }

    // এখানে ভুলটি ঠিক করা হয়েছে: is_sub_history_page দেওয়া হয়েছে
    if ( get_query_var( 'is_sub_history_page' ) ) {
        $new_template = locate_template( array( 'page-subscription-history.php' ) );
        if ( ! empty( $new_template ) ) {
            return $new_template;
        }
    }

    if ( get_query_var( 'is_sub_playlist_page' ) ) {
        $new_template = locate_template( array( 'page-subscription-playlist.php' ) );
        if ( ! empty( $new_template ) ) {
            return $new_template;
        }
    }

    if ( get_query_var( 'is_sub_watched_page' ) ) {
        $new_template = locate_template( array( 'page-subscription-watched.php' ) );
        if ( ! empty( $new_template ) ) {
            return $new_template;
        }
    }

    return $template;
}
add_filter( 'template_include', 'bdtube_subscriptions_template_include' );