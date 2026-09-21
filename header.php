<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Google Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

    <?php
    if (function_exists('wp_body_open')) {
        wp_body_open();
    }

    // লগইন করার পর যে পেজে ফিরে আসবে (get_permalink() হোম/আর্কাইভ পেজে কাজ করে না)
    global $wp;
    $current_url = home_url(add_query_arg(array(), $wp->request));
    $site_name = get_bloginfo('name');
    $is_home = is_front_page() || is_home();
    ?>

    <header class="site-header">

        <!-- ১. বাম পাশের অংশ (মেনু বাটন ও লোগো) -->
        <div class="header-left">
            <button type="button" class="icon-btn" id="menu-btn" title="Menu" aria-label="Toggle navigation"
                aria-expanded="false" aria-controls="full-drawer">
                <span class="material-icons" aria-hidden="true">menu</span>
            </button>

            <?php if (has_custom_logo()): ?>
                <div class="logo custom-logo-wrapper">
                    <?php the_custom_logo(); ?>
                </div>
            <?php else: ?>
                <a href="<?php echo esc_url(home_url('/')); ?>" class="logo">
                    <span class="material-icons logo-icon" aria-hidden="true">play_circle_filled</span>
                    <span class="logo-text"><?php echo esc_html($site_name); ?></span>
                </a>
            <?php endif; ?>
        </div>

        <!-- ২. মাঝের অংশ (সার্চ ও মাইক) -->
        <div class="header-center">
            <form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
                <div class="search-input-wrapper">
                    <input type="text" placeholder="Search" value="<?php echo get_search_query(); ?>" name="s"
                        id="search-input" autocomplete="off" aria-label="Search">
                    <button type="button" class="clear-btn" id="clear-search-btn" title="Clear search"
                        aria-label="Clear search" <?php echo get_search_query() ? '' : ' hidden'; ?>>
                        <span class="material-icons" aria-hidden="true">close</span>
                    </button>
                    <div class="search-suggestions" id="search-suggestions" hidden></div>
                </div>

                <button type="submit" class="search-btn" title="Search" aria-label="Search">
                    <span class="material-icons" aria-hidden="true">search</span>
                </button>
            </form>

            <button type="button" class="icon-btn mic-btn" id="mic-btn" title="Search with your voice"
                aria-label="Search with your voice" aria-pressed="false">
                <span class="material-icons" aria-hidden="true">mic</span>
            </button>
        </div>

        <!-- ৩. ডান পাশের অংশ (Create, Notification, Profile) -->
        <div class="header-right">

            <!-- Create Dropdown -->
            <div class="create-dropdown-wrapper">
                <button type="button" class="create-btn" id="create-btn" title="Create" aria-haspopup="true"
                    aria-expanded="false" aria-controls="create-menu">
                    <span class="material-icons" aria-hidden="true">add</span>
                    <span class="btn-text">Create</span>
                </button>

                <div class="create-menu" id="create-menu" hidden>
                    <a href="<?php echo esc_url(home_url('/upload-video')); ?>" class="create-item">
                        <span class="material-icons" aria-hidden="true">slideshow</span>
                        <span>Upload video</span>
                    </a>

                    <a href="<?php echo esc_url(home_url('/go-live')); ?>" class="create-item">
                        <span class="material-icons" aria-hidden="true">podcasts</span>
                        <span>Go live</span>
                    </a>

                    <a href="<?php echo esc_url(home_url('/create-post')); ?>" class="create-item">
                        <span class="material-icons" aria-hidden="true">edit_note</span>
                        <span>Create post</span>
                    </a>
                </div>
            </div>

            <!-- Notification Dropdown -->
            <div class="notification-dropdown-wrapper">
                <button type="button" class="icon-btn notification-btn" id="notification-btn" title="Notifications"
                    aria-label="Notifications" aria-haspopup="true" aria-expanded="false"
                    aria-controls="notification-menu">
                    <span class="material-icons" aria-hidden="true">notifications</span>
                    <!-- নমুনা সংখ্যা: পরে আসল আনরিড কাউন্ট দিয়ে বদলাবেন -->
                    <span class="notification-badge">3</span>
                </button>

                <div class="notification-menu" id="notification-menu" hidden>
                    <div class="notification-header">
                        <h3>Notifications</h3>
                    </div>

                    <div class="notification-list">
                        <!-- নমুনা নোটিফিকেশন -->
                        <a href="#" class="notification-item unread">
                            <div class="notification-avatar">
                                <span class="material-icons" aria-hidden="true">play_circle</span>
                            </div>
                            <div class="notification-content">
                                <p class="notification-text">New video uploaded on <?php echo esc_html($site_name); ?>
                                </p>
                                <span class="notification-time">2 hours ago</span>
                            </div>
                        </a>

                        <a href="#" class="notification-item">
                            <div class="notification-avatar">
                                <span class="material-icons" aria-hidden="true">campaign</span>
                            </div>
                            <div class="notification-content">
                                <p class="notification-text">Welcome to <?php echo esc_html($site_name); ?> platform!
                                </p>
                                <span class="notification-time">1 day ago</span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- User Profile / Sign In -->
            <div class="user-profile-wrapper">
                <?php if (is_user_logged_in()):
                    $current_user = wp_get_current_user();
                    $avatar_url = get_avatar_url($current_user->ID, array('size' => 64));
                    ?>
                    <button type="button" class="profile-avatar-btn" id="profile-avatar-btn" title="Account"
                        aria-label="Account menu" aria-haspopup="true" aria-expanded="false" aria-controls="profile-menu">
                        <img src="<?php echo esc_url($avatar_url); ?>" alt="">
                    </button>

                    <div class="profile-menu" id="profile-menu" hidden>
                        <div class="profile-menu-header">
                            <img src="<?php echo esc_url($avatar_url); ?>" alt="" class="menu-avatar">
                            <div class="user-details">
                                <span class="user-name"><?php echo esc_html($current_user->display_name); ?></span>
                                <span class="user-handle">@<?php echo esc_html($current_user->user_login); ?></span>
                                <a href="<?php echo esc_url(home_url('/channel')); ?>" class="view-channel-link">View
                                    your channel</a>
                            </div>
                        </div>

                        <hr class="menu-divider">

                        <div class="profile-menu-list">
                            <?php if (current_user_can('edit_posts')): ?>
                                <a href="<?php echo esc_url(admin_url()); ?>" class="profile-menu-item">
                                    <span class="material-icons" aria-hidden="true">dashboard</span>
                                    <span>Studio / Admin</span>
                                </a>
                            <?php endif; ?>
                            <a href="#" class="profile-menu-item">
                                <span class="material-icons" aria-hidden="true">switch_account</span>
                                <span>Switch account</span>
                            </a>
                            <a href="<?php echo esc_url(wp_logout_url(home_url('/'))); ?>" class="profile-menu-item">
                                <span class="material-icons" aria-hidden="true">logout</span>
                                <span>Sign out</span>
                            </a>

                            <hr class="menu-divider">

                            <a href="#" class="profile-menu-item">
                                <span class="material-icons" aria-hidden="true">dark_mode</span>
                                <span>Appearance: Dark</span>
                            </a>
                            <a href="<?php echo esc_url(admin_url('profile.php')); ?>" class="profile-menu-item">
                                <span class="material-icons" aria-hidden="true">settings</span>
                                <span>Settings</span>
                            </a>
                        </div>
                    </div>

                <?php else: ?>
                    <!-- Guest: Sign In Button -->
                    <a href="<?php echo esc_url(wp_login_url($current_url)); ?>" class="sign-in-btn">
                        <span class="material-icons" aria-hidden="true">account_circle</span>
                        <span>Sign in</span>
                    </a>
                <?php endif; ?>
            </div>

        </div><!-- /.header-right -->

        <!-- ৪. Mini Sidebar (ডিফল্ট অবস্থায় বামপাশে ছোট হয়ে থাকবে) -->
        <nav class="mini-sidebar" id="mini-sidebar" aria-label="Quick navigation">
            <a href="<?php echo esc_url(home_url('/')); ?>"
                class="mini-item<?php echo $is_home ? ' active' : ''; ?>" <?php echo $is_home ? ' aria-current="page"' : ''; ?>>
                <span class="material-icons" aria-hidden="true">home</span>
                <span class="mini-label">Home</span>
            </a>
            <a href="#" class="mini-item">
                <span class="material-icons" aria-hidden="true">bolt</span>
                <span class="mini-label">Shorts</span>
            </a>
            <a href="#" class="mini-item">
                <span class="material-icons" aria-hidden="true">subscriptions</span>
                <span class="mini-label">Subscriptions</span>
            </a>
            <a href="#" class="mini-item">
                <span class="material-icons" aria-hidden="true">account_circle</span>
                <span class="mini-label">You</span>
            </a>
        </nav>

        <!-- ৫. Drawer Overlay (ব্যাকগ্রাউন্ড ডার্ক করার জন্য) -->
        <div class="drawer-overlay" id="drawer-overlay"></div>

        <!-- ৬. Full Menu Drawer -->
        <aside class="full-drawer" id="full-drawer" aria-label="Main menu">
            <div class="drawer-header">
                <button type="button" class="icon-btn" id="drawer-close-btn" title="Close" aria-label="Close menu">
                    <span class="material-icons" aria-hidden="true">menu</span>
                </button>
                <?php if (has_custom_logo()): ?>
                    <div class="logo custom-logo-wrapper">
                        <?php the_custom_logo(); ?>
                    </div>
                <?php else: ?>
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="logo">
                        <span class="material-icons logo-icon" aria-hidden="true">play_circle_filled</span>
                        <span class="logo-text"><?php echo esc_html($site_name); ?></span>
                    </a>
                <?php endif; ?>
            </div>

            <div class="drawer-scroll-content">
                <!-- Main Links -->
                <div class="drawer-section">
                    <a href="<?php echo esc_url(home_url('/')); ?>"
                        class="drawer-item<?php echo $is_home ? ' active' : ''; ?>" <?php echo $is_home ? ' aria-current="page"' : ''; ?>>
                        <span class="material-icons" aria-hidden="true">home</span>
                        <span>Home</span>
                    </a>
                    <a href="#" class="drawer-item">
                        <span class="material-icons" aria-hidden="true">bolt</span>
                        <span>Shorts</span>
                    </a>
                </div>

                <hr class="drawer-divider">

                <!-- Subscriptions (নমুনা চ্যানেল; via.placeholder.com বন্ধ হয়ে গেছে, তাই অক্ষরের গোল আইকন) -->
                <div class="drawer-section">
                    <div class="section-title">
                        <span>Subscriptions</span>
                        <span class="material-icons chevron" aria-hidden="true">chevron_right</span>
                    </div>

                    <a href="#" class="drawer-item sub-channel">
                        <span class="sub-avatar" aria-hidden="true">J</span>
                        <span class="sub-name">Jamuna TV</span>
                        <span class="live-dot" aria-hidden="true">((•))</span>
                    </a>
                    <a href="#" class="drawer-item sub-channel">
                        <span class="sub-avatar" aria-hidden="true">A</span>
                        <span class="sub-name">ATN Bangla News</span>
                        <span class="unread-dot" aria-hidden="true">•</span>
                    </a>
                    <a href="#" class="drawer-item">
                        <span class="material-icons" aria-hidden="true">expand_more</span>
                        <span>Show more</span>
                    </a>
                </div>

                <hr class="drawer-divider">

                <!-- You Section -->
                <div class="drawer-section">
                    <div class="section-title">
                        <span>You</span>
                        <span class="material-icons chevron" aria-hidden="true">chevron_right</span>
                    </div>
                    <a href="#" class="drawer-item">
                        <span class="material-icons" aria-hidden="true">account_box</span>
                        <span>Your channel</span>
                    </a>
                    <a href="#" class="drawer-item">
                        <span class="material-icons" aria-hidden="true">history</span>
                        <span>History</span>
                    </a>
                    <a href="#" class="drawer-item">
                        <span class="material-icons" aria-hidden="true">playlist_play</span>
                        <span>Playlists</span>
                    </a>
                    <a href="#" class="drawer-item">
                        <span class="material-icons" aria-hidden="true">watch_later</span>
                        <span>Watch Later</span>
                    </a>
                    <a href="#" class="drawer-item">
                        <span class="material-icons" aria-hidden="true">thumb_up_off_alt</span>
                        <span>Liked videos</span>
                    </a>
                    <a href="#" class="drawer-item">
                        <span class="material-icons" aria-hidden="true">download</span>
                        <span>Downloads</span>
                    </a>
                </div>

                <hr class="drawer-divider">
                <!-- More for Section -->
                <div class="drawer-section">
                    <div class="section-title">
                        <span>More from BDTUBE</span>
                    </div>
                    <a href="#" class="drawer-item">
                        <span class="material-icons" aria-hidden="true">workspace_premium</span>
                        <span>BDTUBE PREMIUM</span>
                    </a>
                    <a href="#" class="drawer-item">
                        <span class="material-icons" aria-hidden="true">music_note</span>
                        <span>BDTUBE MUSIC</span>
                    </a>
                    <a href="#" class="drawer-item">
                        <span class="material-icons" aria-hidden="true">child_care</span>
                        <span>BDTUBE KIDS</span>
                    </a>
                </div>

                <hr class="drawer-divider">
                <!-- Explore Section -->
                <div class="drawer-section">
                    <div class="section-title">
                        <span>Explore</span>
                    </div>
                    <a href="#" class="drawer-item">
                        <span class="material-icons" aria-hidden="true">music_note</span>
                        <span>Music</span>
                    </a>
                    <a href="#" class="drawer-item">
                        <span class="material-icons" aria-hidden="true">sports_esports</span>
                        <span>Gaming</span>
                    </a>
                    <a href="#" class="drawer-item">
                        <span class="material-icons" aria-hidden="true">sports_soccer</span>
                        <span>Sports</span>
                    </a>
                    <a href="#" class="drawer-item">
                        <span class="material-icons" aria-hidden="true">workspace_premium</span>
                        <span>Get Premium</span>
                    </a>
                    <a href="#" class="drawer-item">
                        <span class="material-icons" aria-hidden="true">newspaper</span>
                        <span>News</span>
                    </a>
                </div>

                <hr class="drawer-divider">
                <!-- Report Section -->
                <div class="drawer-section">
                    <div class="section-title">
                        <span>Report</span>
                    </div>
                    <a href="#" class="drawer-item">
                        <span class="material-icons" aria-hidden="true">bug_report</span>
                        <span>Report a problem</span>
                    </a>
                    <a href="#" class="drawer-item">
                        <span class="material-icons" aria-hidden="true">feedback</span>
                        <span>Send feedback</span>
                    </a>
                    <a href="#" class="drawer-item">
                        <span class="material-icons" aria-hidden="true">help_outline</span>
                        <span>Help</span>
                    </a>
                </div>

                <hr class="drawer-divider">
                <!-- Report History Section -->
                <div class="drawer-section">
                    <div class="section-title">
                        <span>Report History</span>
                    </div>
                    <a href="#" class="drawer-item">
                        <span class="material-icons" aria-hidden="true">history</span>
                        <span>View report history</span>
                    </a>
                </div>

                <hr class="drawer-divider">
                <!-- Footer Section -->
                <div class="drawer-section">
                    <div class="footer-links">
                        <?php
                        if (has_nav_menu('drawer-footer-menu')) {
                            wp_nav_menu(array(
                                'theme_location' => 'drawer-footer-menu',
                                'container' => false,
                                'depth' => 1,
                            ));
                        } else {
                            // মেনু না বানানো থাকলে এগুলো দেখাবে
                            echo '<a href="#">About</a>';
                            echo '<a href="#">Contact</a>';
                            echo '<a href="#">Privacy</a>';
                            echo '<a href="#">Terms</a>';
                        }
                        ?>
                    </div>
                    <div class="footer-copy">
                        &copy; <?php echo date('Y'); ?> <?php echo esc_html(get_bloginfo('name')); ?>. All rights
                        reserved.
                    </div>
                </div>
            </div>
        </aside>

    </header>