<?php
/*
Template Name: Subscriptions Feed
*/
get_header(); 
?>

<main class="main-content" style="padding: 20px; background: #0f0f0f; min-height: 100vh;">
    <div class="subscriptions-feed-container" style="max-width: 1280px; margin: 0 auto;">
        
        <h2 style="color: #fff; margin-bottom: 20px; font-size: 22px; border-bottom: 1px solid #333; padding-bottom: 10px;">
            Latest from your subscriptions
        </h2>

        <?php if ( is_user_logged_in() ) : 
            $user_id = get_current_user_id();
            $subscribed_channels = get_user_meta( $user_id, 'subscribed_channels', true );

            if ( ! empty( $subscribed_channels ) && is_array( $subscribed_channels ) ) :

                // সাবস্ক্রাইব করা চ্যানেলগুলোর পোস্ট কুয়েরি করা
                $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
                $args  = array(
                    'post_type'      => 'post',
                    'author__in'     => $subscribed_channels,
                    'posts_per_page' => 12,
                    'paged'          => $paged,
                    'post_status'    => 'publish'
                );

                $sub_query = new WP_Query( $args );

                if ( $sub_query->have_posts() ) : ?>
                    <div class="video-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px;">
                        <?php while ( $sub_query->have_posts() ) : $sub_query->the_post(); 
                            $post_id   = get_the_ID();
                            $author_id = get_the_author_meta('ID');
                            
                            // লাইভ স্ট্যাটাস বা দেখা হওয়ার কাউন্ট
                            $stats_text = function_exists('bdtube_get_video_stats') ? bdtube_get_video_stats($post_id) : '';
                            $is_live   = ( strpos($stats_text, 'watching now') !== false );
                            ?>
                            <article class="video-card">
                                <a href="<?php the_permalink(); ?>" class="thumbnail-wrapper" style="position: relative; display: block;">
                                    <?php if ( $is_live ) : ?>
                                        <span class="live-badge" style="position: absolute; top: 10px; right: 10px; background: #ff0000; color: #fff; padding: 2px 6px; border-radius: 4px; font-size: 11px; font-weight: bold;">LIVE</span>
                                    <?php endif; ?>

                                    <?php if ( has_post_thumbnail() ) : ?>
                                        <?php the_post_thumbnail('medium', array('style' => 'width:100%; height:auto; border-radius:8px;')); ?>
                                    <?php else : ?>
                                        <img src="https://via.placeholder.com/320x180/1f1f1f/ffffff?text=BD+TUBE" style="width:100%; border-radius:8px;" alt="<?php the_title(); ?>">
                                    <?php endif; ?>
                                </a>

                                <div class="video-details" style="display: flex; gap: 12px; margin-top: 10px;">
                                    <div class="channel-avatar">
                                        <?php echo get_avatar( $author_id, 36, '', '', array('style' => 'border-radius: 50%;') ); ?>
                                    </div>
                                    <div class="video-info">
                                        <h3 class="video-title" style="font-size: 14px; margin: 0 0 5px 0; line-height: 1.4;">
                                            <a href="<?php the_permalink(); ?>" style="color: #fff; text-decoration: none;"><?php the_title(); ?></a>
                                        </h3>
                                        <div class="channel-name" style="font-size: 12px; color: #aaa;">
                                            <?php the_author(); ?>
                                        </div>
                                        <div class="video-metadata" style="font-size: 12px; color: #aaa; margin-top: 2px;">
                                            <span><?php echo $stats_text; ?></span> • <span><?php echo human_time_diff( get_the_time('U'), current_time('timestamp') ) . ' ago'; ?></span>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        <?php endwhile; wp_reset_postdata(); ?>
                    </div>
                <?php else : ?>
                    <p style="color: #aaa; text-align: center; margin-top: 40px;">No recent videos found from your subscribed channels.</p>
                <?php endif; ?>

            <?php else : ?>
                <div style="text-align: center; color: #aaa; margin-top: 50px;">
                    <h3>Your Subscriptions Feed is empty</h3>
                    <p>Subscribe to channels to see their latest videos here.</p>
                </div>
            <?php endif; ?>

        <?php else : ?>
            <div style="text-align: center; color: #fff; margin-top: 60px;">
                <h3>Don't miss new videos</h3>
                <p style="color: #aaa;">Sign in to see updates from your favorite channels</p>
                <a href="<?php echo esc_url( wp_login_url( home_url('/subscriptions/') ) ); ?>" style="display: inline-block; background: #065fd4; color: #fff; padding: 10px 20px; border-radius: 20px; text-decoration: none; font-weight: bold; margin-top: 15px;">Sign In</a>
            </div>
        <?php endif; ?>

    </div>
</main>

<?php get_footer(); ?>