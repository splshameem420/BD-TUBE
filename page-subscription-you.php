<style>
    .you-page-container {
    max-width: 1200px;
    margin: 20px auto;
    padding: 0 20px;
    color: #fff;
}

/* User Profile Header */
.user-profile-header {
    display: flex;
    align-items: center;
    gap: 24px;
    padding: 20px 0;
}

.profile-avatar img {
    width: 110px;
    height: 110px;
    border-radius: 50%;
    object-fit: cover;
}

.user-name {
    font-size: 28px;
    font-weight: bold;
    margin: 0 0 5px 0;
}

.user-handle {
    color: #aaa;
    margin: 0 0 15px 0;
    font-size: 14px;
}

.btn-secondary {
    background: #272727;
    color: #fff;
    padding: 8px 16px;
    border-radius: 18px;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
}

.btn-secondary:hover {
    background: #3f3f3f;
}

.section-divider {
    border: 0;
    height: 1px;
    background: #333;
    margin: 25px 0;
}

/* Sections */
.you-section {
    margin-bottom: 35px;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.section-header h2 {
    font-size: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 0;
}

.see-all-link {
    color: #3ea6ff;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
}

.empty-text {
    color: #aaa;
    font-size: 14px;
}

/* Signed Out State */
.signed-out-content {
    text-align: center;
    padding: 80px 20px;
}

.signed-out-content .size-large {
    font-size: 100px;
    color: #aaa;
}

.btn-signin {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    border: 1px solid #3ea6ff;
    color: #3ea6ff;
    padding: 8px 16px;
    border-radius: 18px;
    text-decoration: none;
    margin-top: 15px;
}
</style>

<?php
/**
 * Template Name: Subscription You Page
 * Description: BD TUBE - User Profile and History Page (/for/you)
 */

get_header();


// ইউজার লগইন না থাকলে সাইন ইন স্টেট দেখাবে
if ( ! is_user_logged_in() ) : ?>
    <div class="you-page-container signed-out">
        <div class="signed-out-content">
            <span class="material-icons size-large">account_circle</span>
            <h2>আপনার নিজস্ব ভিডিও এবং প্লেলিস্ট এখানে দেখুন</h2>
            <p>হিস্ট্রি, লাইক করা ভিডিও এবং ওয়াচ লেটার দেখতে সাইন ইন করুন।</p>
            <a href="<?php echo esc_url( wp_login_url( home_url( '/for/you' ) ) ); ?>" class="btn-signin">
                <span class="material-icons">account_circle</span> সাইন ইন
            </a>
        </div>
    </div>
<?php 
else : 
    $current_user = wp_get_current_user();
    $user_id      = $current_user->ID;
    $avatar_url   = get_avatar_url( $user_id, array( 'size' => 120 ) );
?>

<div class="you-page-container">
    
    <!-- ১. প্রোফাইল হেডার -->
    <div class="user-profile-header">
        <div class="profile-avatar">
            <img src="<?php echo esc_url( $avatar_url ); ?>" alt="<?php echo esc_attr( $current_user->display_name ); ?>">
        </div>
        <div class="profile-details">
            <h1 class="user-name"><?php echo esc_html( $current_user->display_name ); ?></h1>
            <p class="user-handle">@<?php echo esc_html( $current_user->user_login ); ?></p>
            <div class="profile-actions">
                <a href="<?php echo esc_url( get_edit_profile_url( $user_id ) ); ?>" class="btn-secondary">প্রোফাইল এডিট করুন</a>
            </div>
        </div>
    </div>

    <hr class="section-divider">

    <!-- ২. ওয়াচ হিস্ট্রি (History) -->
    <section class="you-section">
        <div class="section-header">
            <h2><span class="material-icons">history</span> হিস্ট্রি</h2>
            <a href="<?php echo esc_url( home_url( '/history' ) ); ?>" class="see-all-link">সব দেখুন</a>
        </div>
        <div class="video-grid">
            <?php
            $history_ids = get_user_meta( $user_id, 'watch_history', true );

            if ( ! empty( $history_ids ) && is_array( $history_ids ) ) :
                $args = array(
                    'post_type'      => 'post',
                    'post__in'       => array_slice( $history_ids, 0, 4 ),
                    'orderby'        => 'post__in',
                    'posts_per_page' => 4
                );
                $history_query = new WP_Query( $args );

                if ( $history_query->have_posts() ) :
                    while ( $history_query->have_posts() ) : $history_query->the_post();
                        get_template_part( 'template-parts/content', 'video-card' );
                    endwhile;
                    wp_reset_postdata();
                endif;
            else : ?>
                <p class="empty-text">এখনো কোনো ভিডিও দেখা হয়নি।</p>
            <?php endif; ?>
        </div>
    </section>

    <!-- ৩. ওয়াচ লেটার (Watch Later) -->
    <section class="you-section">
        <div class="section-header">
            <h2><span class="material-icons">watch_later</span> পরে দেখুন (Watch Later)</h2>
            <a href="<?php echo esc_url( home_url( '/playlist?list=WL' ) ); ?>" class="see-all-link">সব দেখুন</a>
        </div>
        <div class="video-grid">
            <?php
            $watch_later_ids = get_user_meta( $user_id, 'watch_later', true );

            if ( ! empty( $watch_later_ids ) && is_array( $watch_later_ids ) ) :
                $args_wl = array(
                    'post_type'      => 'post',
                    'post__in'       => array_slice( $watch_later_ids, 0, 4 ),
                    'orderby'        => 'post__in',
                    'posts_per_page' => 4
                );
                $wl_query = new WP_Query( $args_wl );

                if ( $wl_query->have_posts() ) :
                    while ( $wl_query->have_posts() ) : $wl_query->the_post();
                        get_template_part( 'template-parts/content', 'video-card' );
                    endwhile;
                    wp_reset_postdata();
                endif;
            else : ?>
                <p class="empty-text">"পরে দেখুন" তালিকায় কোনো ভিডিও নেই।</p>
            <?php endif; ?>
        </div>
    </section>

    <!-- ৪. লাইক করা ভিডিও (Liked Videos) -->
    <section class="you-section">
        <div class="section-header">
            <h2><span class="material-icons">thumb_up</span> পছন্দ করা ভিডিও (Liked)</h2>
            <a href="<?php echo esc_url( home_url( '/playlist?list=LL' ) ); ?>" class="see-all-link">সব দেখুন</a>
        </div>
        <div class="video-grid">
            <?php
            $liked_video_ids = get_user_meta( $user_id, 'liked_videos', true );

            if ( ! empty( $liked_video_ids ) && is_array( $liked_video_ids ) ) :
                $args_liked = array(
                    'post_type'      => 'post',
                    'post__in'       => array_slice( $liked_video_ids, 0, 4 ),
                    'orderby'        => 'post__in',
                    'posts_per_page' => 4
                );
                $liked_query = new WP_Query( $args_liked );

                if ( $liked_query->have_posts() ) :
                    while ( $liked_query->have_posts() ) : $liked_query->the_post();
                        get_template_part( 'template-parts/content', 'video-card' );
                    endwhile;
                    wp_reset_postdata();
                endif;
            else : ?>
                <p class="empty-text">কোনো ভিডিও পছন্দ করা হয়নি।</p>
            <?php endif; ?>
        </div>
    </section>

</div>

<?php 
endif; 
get_footer();