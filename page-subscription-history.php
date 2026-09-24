<style>
    .history-page-container {
    display: flex;
    max-width: 1280px;
    margin: 20px auto;
    padding: 0 24px;
    gap: 40px;
    color: #f1f1f1;
}

.history-main-content {
    flex: 1;
}

.page-title {
    font-size: 24px;
    font-weight: 700;
    margin-bottom: 24px;
}

/* History Video Card Row Layout */
.history-item-card {
    display: flex;
    gap: 16px;
    margin-bottom: 16px;
    position: relative;
}

.history-thumbnail {
    width: 240px;
    height: 135px;
    flex-shrink: 0;
    border-radius: 8px;
    overflow: hidden;
    background: #1f1f1f;
}

.history-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.history-details {
    flex: 1;
}

.video-title a {
    color: #f1f1f1;
    text-decoration: none;
    font-size: 18px;
    font-weight: 500;
    line-height: 1.4;
}

.video-meta {
    color: #aaa;
    font-size: 13px;
    margin: 8px 0;
}

.video-description {
    color: #aaa;
    font-size: 13px;
    line-height: 1.4;
}

.remove-history-btn {
    background: none;
    border: none;
    color: #aaa;
    cursor: pointer;
    padding: 8px;
    border-radius: 50%;
    height: fit-content;
}

.remove-history-btn:hover {
    background: #272727;
    color: #fff;
}

/* History Sidebar Right */
.history-sidebar {
    width: 320px;
    flex-shrink: 0;
}

.search-history-box {
    display: flex;
    align-items: center;
    gap: 10px;
    border-bottom: 1px solid #aaa;
    padding-bottom: 8px;
    margin-bottom: 24px;
}

.search-history-box input {
    background: transparent;
    border: none;
    color: #fff;
    outline: none;
    width: 100%;
}

.control-btn {
    display: flex;
    align-items: center;
    gap: 12px;
    background: none;
    border: none;
    color: #f1f1f1;
    width: 100%;
    padding: 10px 0;
    cursor: pointer;
    font-size: 14px;
}

.control-btn:hover {
    color: #3ea6ff;
}

/* Signed out State */
.signed-out-content {
    text-align: center;
    padding: 80px 0;
    width: 100%;
}
</style>

<?php
/**
 * Template Name: Subscription History Page
 * Description: BD TUBE - Watch History Page (/history)
 */

get_header();

// ইউজার লগইন না থাকলে সাইন ইন স্টেট দেখাবে
if ( ! is_user_logged_in() ) : ?>
    <div class="history-page-container signed-out">
        <div class="signed-out-content">
            <span class="material-icons size-large">history</span>
            <h2>আপনার দেখা ভিডিওগুলোর হিসাব রাখুন</h2>
            <p>সাইন ইন না থাকলে ওয়াচ হিস্ট্রি দেখা সম্ভব নয়।</p>
            <a href="<?php echo esc_url( wp_login_url( home_url( '/history' ) ) ); ?>" class="btn-signin">
                <span class="material-icons">account_circle</span> সাইন ইন
            </a>
        </div>
    </div>
<?php 
else : 
    $user_id     = get_current_user_id();
    $history_ids = get_user_meta( $user_id, 'watch_history', true );
?>

<div class="history-page-container">
    <div class="history-main-content">
        <h1 class="page-title">Watch history</h1>

        <div class="history-video-list">
            <?php
            if ( ! empty( $history_ids ) && is_array( $history_ids ) ) :
                $args = array(
                    'post_type'      => 'post',
                    'post__in'       => $history_ids,
                    'orderby'        => 'post__in',
                    'posts_per_page' => 20
                );
                $history_query = new WP_Query( $args );

                if ( $history_query->have_posts() ) :
                    while ( $history_query->have_posts() ) : $history_query->the_post();
                        ?>
                        <div class="history-item-card">
                            <div class="history-thumbnail">
                                <a href="<?php the_permalink(); ?>">
                                    <?php if ( has_post_thumbnail() ) : ?>
                                        <?php the_post_thumbnail( 'medium' ); ?>
                                    <?php else : ?>
                                        <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/default-thumb.jpg' ); ?>" alt="<?php the_title(); ?>">
                                    <?php endif; ?>
                                </a>
                            </div>
                            <div class="history-details">
                                <h3 class="video-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h3>
                                <div class="video-meta">
                                    <span class="channel-name"><?php the_author(); ?></span> • 
                                    <span class="views-count"><?php echo get_post_meta( get_the_ID(), 'post_views_count', true ) ?: '0'; ?> views</span>
                                </div>
                                <p class="video-description">
                                    <?php echo wp_trim_words( get_the_excerpt(), 20, '...' ); ?>
                                </p>
                            </div>
                            <button class="remove-history-btn" data-video-id="<?php the_ID(); ?>" title="Remove from watch history">
                                <span class="material-icons">close</span>
                            </button>
                        </div>
                        <?php
                    endwhile;
                    wp_reset_postdata();
                else :
                    echo '<p class="empty-history">কোনো ভিডিও পাওয়া যায়নি।</p>';
                endif;
            else : ?>
                <div class="empty-history-state">
                    <p>এই তালিকায় কোনো ভিডিও নেই।</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- সাইডবার কন্ট্রোলস (YouTube Style Sidebar) -->
    <div class="history-sidebar">
        <div class="search-history-box">
            <span class="material-icons">search</span>
            <input type="text" id="search-history-input" placeholder="Search watch history">
        </div>

        <div class="history-controls">
            <button id="clear-all-history" class="control-btn">
                <span class="material-icons">delete_outline</span>
                <span>Clear all watch history</span>
            </button>
            <button id="pause-history" class="control-btn">
                <span class="material-icons">pause_circle_outline</span>
                <span>Pause watch history</span>
            </button>
        </div>
    </div>
</div>

<?php 
endif; 
get_footer();