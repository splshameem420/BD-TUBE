<?php
/*
Template Name: Live TV Page
*/
get_header(); ?>

<main class="main-content">
    <h2 class="page-title" style="color: #fff; margin-bottom: 20px; font-size: 20px;">
        <span style="color: #ff0000;">●</span> Live TV & Active Streams
    </h2>

    <div class="video-grid">
        <?php 
        // সাম্প্রতিক পোস্টগুলো তুলে আনা
        $args = array(
            'post_type'      => 'post',
            'posts_per_page' => -1, // সব পোস্ট চেক করবে
            'post_status'    => 'publish'
        );

        $live_query = new WP_Query( $args );
        $live_count = 0;

        if ( $live_query->have_posts() ) : 
            while ( $live_query->have_posts() ) : $live_query->the_post(); 
                $post_id = get_the_ID();
                
                // লাইভ স্ট্যাটাস চেক
                $is_live = false;
                $stats_text = '';
                
                if ( function_exists( 'bdtube_get_video_stats' ) ) {
                    $stats_text = bdtube_get_video_stats( $post_id );
                    if ( strpos( $stats_text, 'watching now' ) !== false ) {
                        $is_live = true;
                    }
                }

                // লাইভ না হলে এই পেজে দেখাবে না
                if ( ! $is_live ) {
                    continue;
                }

                $live_count++;
                ?>
                <article class="video-card">
                    <!-- Thumbnail Wrapper -->
                    <a href="<?php the_permalink(); ?>" class="thumbnail-wrapper" style="position: relative; display: block;">
                        <span class="live-badge">
                            <span class="pulse-dot"></span> LIVE
                        </span>

                        <?php if ( has_post_thumbnail() ) : ?>
                            <?php the_post_thumbnail('medium_large', array('class' => 'video-thumbnail')); ?>
                        <?php else : ?>
                            <img src="https://via.placeholder.com/320x180/1f1f1f/ffffff?text=LIVE+TV" alt="<?php the_title(); ?>" class="video-thumbnail">
                        <?php endif; ?>
                    </a>
                    
                    <!-- Video Info Section -->
                    <div class="video-details">
                        <div class="channel-avatar">
                            <?php echo get_avatar( get_the_author_meta( 'ID' ), 36 ); ?>
                        </div>
                        <div class="video-info">
                            <h3 class="video-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h3>
                            <div class="channel-name"><?php the_author(); ?></div>
                            <div class="video-metadata">
                                <span class="realtime-views" data-post-id="<?php echo $post_id; ?>">
                                    <?php echo $stats_text; ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </article>
            <?php 
            endwhile;
            wp_reset_postdata();
        endif;

        if ( $live_count === 0 ) : ?>
            <p class="no-posts">No Live Stream Available Right Now</p>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>