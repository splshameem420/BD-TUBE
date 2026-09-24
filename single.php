<?php 
get_header(); 

if ( have_posts() ) : 
    while ( have_posts() ) : the_post(); 
        $post_id = get_the_ID();
        $is_live = get_post_meta($post_id, 'is_live_stream', true);
        
        if ($is_live !== 'yes') {
            bdtube_set_post_views($post_id); 
        }
        ?>

        <main class="single-video-container" style="padding: 20px; color: #fff;">
            <h1><?php the_title(); ?></h1>

            <div class="video-player-box" style="margin: 20px 0;">
                <?php the_content(); ?>
            </div>

            <div class="video-info">
                <p>Channel: <?php the_author(); ?></p>
                <p>
                    <span class="realtime-views" data-post-id="<?php echo $post_id; ?>">
                        <?php echo bdtube_get_video_stats($post_id); ?>
                    </span>
                </p>
            </div>
        </main>

        <?php if ($is_live === 'yes') : ?>
            <script>
            function sendLiveHeartbeat() {
                fetch('<?php echo get_template_directory_uri(); ?>/live-ping.php?id=<?php echo $post_id; ?>');
            }
            setInterval(sendLiveHeartbeat, 10000);
            sendLiveHeartbeat(); 
            </script>
        <?php endif; ?>

        <?php 
    endwhile; 
endif; 


get_footer(); 
?>