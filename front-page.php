<?php get_header(); ?>

<main class="main-content">
    <div class="video-grid">
        <?php 
        if ( have_posts() ) : 
            while ( have_posts() ) : the_post(); 
                $post_id = get_the_ID();
                
                // Live post kina auto-detect kora
                $is_live = false;
                $stats_text = '';
                
                if ( function_exists( 'bdtube_get_video_stats' ) ) {
                    $stats_text = bdtube_get_video_stats( $post_id );
                    if ( strpos( $stats_text, 'watching now' ) !== false ) {
                        $is_live = true;
                    }
                } else {
                    $stats_text = function_exists('bdtube_get_post_views') ? bdtube_get_post_views($post_id) : '0 views';
                }
                
                ?>
                <article class="video-card">
                    <!-- Thumbnail Wrapper -->
                    <a href="<?php the_permalink(); ?>" class="thumbnail-wrapper" style="position: relative; display: block;">
                        
                        <!-- Red LIVE Badge (Shudhu live post-er jonno dekhabe) -->
                        <?php if ( $is_live ) : ?>
                            <span class="live-badge">
                                <span class="pulse-dot"></span> LIVE
                            </span>
                        <?php endif; ?>

                        <?php if ( has_post_thumbnail() ) : ?>
                            <?php the_post_thumbnail('medium_large', array('class' => 'video-thumbnail')); ?>
                        <?php else : ?>
                            <img src="https://via.placeholder.com/320x180/1f1f1f/ffffff?text=BD+TUBE" alt="<?php the_title(); ?>" class="video-thumbnail">
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
                                
                                <?php if ( ! $is_live ) : ?>
                                    <!-- Live shesh hole "Streamed X ago" dekhabe -->
                                    <span>•</span>
                                    <span>Streamed <?php echo human_time_diff( get_the_time('U'), current_time('timestamp') ) . ' ago'; ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </article>
            <?php 
            endwhile; 
        else : ?>
            <p class="no-posts">No Post Found</p>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>