<?php
/*
Template Name: YouTube Shorts Page
*/
get_header(); ?>

<style>

html, body {
    overflow-x: hidden;
    margin: 0;
    padding: 0;
}
/* YouTube Shorts Layout Styles */
.shorts-container {
    height: calc(100vh - 56px); /* হেডার হাইট অনুযায়ী অ্যাডজাস্ট করুন */
    overflow-y: scroll;
    scroll-snap-type: y mandatory;
    background: #0f0f0f;
    display: flex;
    flex-direction: column;
    align-items: center;
    /* স্ট্যান্ডার্ড স্ক্রোলবার লুকানো বা চিকন করা */
    scrollbar-width: thin;
    scrollbar-color: #333 #0f0f0f;
}
.shorts-card {
    scroll-snap-align: start;
    min-height: calc(100vh - 80px);
    width: 100%;
    max-width: 420px;
    position: relative;
    margin: 10px 0;
    background: #000;
    border-radius: 12px;
    overflow: hidden;
    display: flex;
    justify-content: center;
    align-items: center;
}
.shorts-video-wrapper {
    width: 100%;
    height: 100%;
    position: relative;
}
.shorts-video-wrapper video, 
.shorts-video-wrapper iframe {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.shorts-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 20px 15px;
    background: linear-gradient(transparent, rgba(0, 0, 0, 0.8));
    color: #fff;
    display: flex;
    flex-direction: column;
    gap: 8px;
    z-index: 2;
}
.shorts-author {
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: bold;
    font-size: 14px;
}
.shorts-title {
    font-size: 14px;
    line-height: 1.3;
    margin: 0;
}
.shorts-actions {
    position: absolute;
    right: 12px;
    bottom: 30px;
    display: flex;
    flex-direction: column;
    gap: 20px;
    align-items: center;
    z-index: 3;
}
.action-btn {
    background: rgba(255, 255, 255, 0.15);
    border: none;
    color: #fff;
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    backdrop-filter: blur(5px);
}
.action-btn span.material-icons {
    font-size: 24px;
}
.action-btn small {
    font-size: 10px;
    margin-top: 2px;
}
</style>

<div class="shorts-container">
    <?php
    $args = array(
        'post_type'      => 'post',
        'posts_per_page' => 15,
        'post_status'    => 'publish'
    );

    $shorts_query = new WP_Query($args);
    $found_shorts = 0;

    if ($shorts_query->have_posts()) :
        while ($shorts_query->have_posts()) : $shorts_query->the_post();
            $post_id = get_the_ID();

            // কাস্টম চেকার দিয়ে শর্টস পোস্ট কিনা দেখা
            if (!bdtube_is_short($post_id)) continue;

            $found_shorts++;
            $video_url = get_post_meta($post_id, 'video_url', true); // কাস্টম ফিল্ডের ভিডিও লিংক
            ?>
            <div class="shorts-card">
                <div class="shorts-video-wrapper">
                    <?php if (!empty($video_url)) : ?>
                        <video src="<?php echo esc_url($video_url); ?>" loop autoplay muted playsinline></video>
                    <?php else : ?>
                        <?php the_content(); ?>
                    <?php endif; ?>
                </div>

                <!-- Floating Right Action Buttons -->
                <div class="shorts-actions">
                    <button class="action-btn">
                        <span class="material-icons">thumb_up</span>
                        <small>Like</small>
                    </button>
                    <button class="action-btn">
                        <span class="material-icons">chat</span>
                        <small>Comment</small>
                    </button>
                    <button class="action-btn">
                        <span class="material-icons">share</span>
                        <small>Share</small>
                    </button>
                </div>

                <!-- Bottom Overlay Info -->
                <div class="shorts-overlay">
                    <div class="shorts-author">
                        <?php echo get_avatar(get_the_author_meta('ID'), 32, '', '', array('style' => 'border-radius:50%;')); ?>
                        <span>@<?php the_author(); ?></span>
                    </div>
                    <p class="shorts-title"><?php the_title(); ?></p>
                </div>
            </div>
            <?php
        endwhile;
        wp_reset_postdata();
    endif;

    if ($found_shorts === 0) : ?>
        <div style="color: #fff; padding: 50px; text-align: center;">
            <h3>No Shorts Available</h3>
            <p>Publish posts with category 'Shorts' or add #shorts in title.</p>
        </div>
    <?php endif; ?>
</div>

<?php get_footer(); ?>