<style>
    /* ==========================================
   BD TUBE - Watch Later Page Styles (/for/watched)
   ========================================== */

.watched-page-container {
    max-width: 1280px;
    margin: 20px auto;
    padding: 0 24px;
    color: #f1f1f1;
    font-family: 'Roboto', 'Arial', sans-serif;
}

.watched-main-content .page-title {
    font-size: 24px;
    font-weight: 700;
    margin-bottom: 24px;
    color: #f1f1f1;
}

/* Video Item Card Row Layout */
.watched-item-card {
    display: flex;
    gap: 16px;
    margin-bottom: 16px;
    position: relative;
    align-items: flex-start;
    padding-right: 40px;
}

.watched-thumbnail {
    width: 240px;
    height: 135px;
    flex-shrink: 0;
    border-radius: 8px;
    overflow: hidden;
    background: #1f1f1f;
}

.watched-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.watched-details {
    flex: 1;
}

.watched-item-card .video-title a {
    color: #f1f1f1;
    text-decoration: none;
    font-size: 18px;
    font-weight: 500;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.watched-item-card .video-title a:hover {
    color: #3ea6ff;
}

.watched-item-card .video-meta {
    color: #aaa;
    font-size: 13px;
    margin: 6px 0;
}

.watched-item-card .video-description {
    color: #aaa;
    font-size: 13px;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Remove Button */
.remove-watched-btn {
    position: absolute;
    right: 0;
    top: 0;
    background: none;
    border: none;
    color: #aaa;
    cursor: pointer;
    padding: 8px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s ease, color 0.2s ease;
}

.remove-watched-btn:hover {
    background: #272727;
    color: #fff;
}

/* Empty State & Signed Out Styles */
.empty-watched-state,
.empty-watched {
    padding: 40px 0;
    color: #aaa;
    font-size: 15px;
}

.watched-page-container.signed-out {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 60vh;
}

.signed-out-content {
    text-align: center;
    max-width: 400px;
}

.signed-out-content .size-large {
    font-size: 96px;
    color: #909090;
    margin-bottom: 12px;
}

.signed-out-content h2 {
    font-size: 20px;
    font-weight: 600;
    margin-bottom: 8px;
}

.signed-out-content p {
    color: #aaa;
    font-size: 14px;
    margin-bottom: 20px;
}
</style>

<?php
/**
 * Template Name: Subscription Watched Page
 * Description: BD TUBE - Watch Later Page (/for/watched)
 */

get_header();

// ইউজার লগইন না থাকলে সাইন ইন স্টেট দেখাবে
if ( ! is_user_logged_in() ) : ?>
    <div class="watched-page-container signed-out">
        <div class="signed-out-content">
            <span class="material-icons size-large">watch_later</span>
            <h2>আপনার সংরক্ষিত ভিডিওগুলো দেখুন</h2>
            <p>ওয়াচ লেটার বা সংরক্ষিত ভিডিও দেখতে সাইন ইন করুন।</p>
            <a href="<?php echo esc_url( wp_login_url( home_url( '/for/watched' ) ) ); ?>" class="btn-signin">
                <span class="material-icons">account_circle</span> সাইন ইন
            </a>
        </div>
    </div>
<?php 
else : 
    $user_id     = get_current_user_id();
    $watched_ids = get_user_meta( $user_id, 'watch_later', true );
?>

<div class="watched-page-container">
    <div class="watched-main-content">
        <h1 class="page-title">Watch Later</h1>

        <div class="watched-video-list">
            <?php
            if ( ! empty( $watched_ids ) && is_array( $watched_ids ) ) :
                $args = array(
                    'post_type'      => 'post',
                    'post__in'       => $watched_ids,
                    'orderby'        => 'post__in',
                    'posts_per_page' => 20
                );
                $watched_query = new WP_Query( $args );

                if ( $watched_query->have_posts() ) :
                    while ( $watched_query->have_posts() ) : $watched_query->the_post();
                        ?>
                        <div class="watched-item-card" id="watched-item-<?php the_ID(); ?>">
                            <div class="watched-thumbnail">
                                <a href="<?php the_permalink(); ?>">
                                    <?php if ( has_post_thumbnail() ) : ?>
                                        <?php the_post_thumbnail( 'medium' ); ?>
                                    <?php else : ?>
                                        <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/default-thumb.jpg' ); ?>" alt="<?php the_title(); ?>">
                                    <?php endif; ?>
                                </a>
                            </div>
                            <div class="watched-details">
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
                            <button class="remove-watched-btn" data-video-id="<?php the_ID(); ?>" title="Remove from list">
                                <span class="material-icons">close</span>
                            </button>
                        </div>
                        <?php
                    endwhile;
                    wp_reset_postdata();
                else :
                    echo '<p class="empty-watched">এই তালিকায় কোনো ভিডিও পাওয়া যায়নি।</p>';
                endif;
            else : ?>
                <div class="empty-watched-state">
                    <p>আপনার "Watch Later" তালিকায় কোনো ভিডিও সংরক্ষণ করা হয়নি।</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php 
endif; 
get_footer();