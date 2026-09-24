<?php
/*
Template Name: Live Video Template
Template Post Type: post
*/

get_header(); 

if ( have_posts() ) : 
    while ( have_posts() ) : the_post(); 
        $post_id = get_the_ID();
        $video_url = get_post_meta($post_id, 'video_url', true);
        ?>

        <!-- HLS.js Library for .m3u8 Streaming -->
        <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>

        <main class="single-live-container" style="padding: 20px; color: #fff; max-width: 1100px; margin: 0 auto;">
            
            <!-- Live Stream Title & Badge -->
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 15px;">
                <span style="background: #ff0000; color: #fff; padding: 4px 10px; font-weight: bold; border-radius: 4px; font-size: 14px; text-transform: uppercase; letter-spacing: 1px;">
                    ● LIVE
                </span>
                <h1 style="margin: 0; font-size: 24px;"><?php the_title(); ?></h1>
            </div>

            <!-- Video Player Box -->
            <div class="live-player-box" style="margin-bottom: 20px; background: #000; border-radius: 8px; overflow: hidden; border: 1px solid #333;">
                <?php if ( ! empty( $video_url ) ) : ?>
                    <video id="bdtube-live-player" controls autoplay style="width: 100%; height: auto; max-height: 550px; display: block;"></video>
                    
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            var video = document.getElementById('bdtube-live-player');
                            var videoSrc = '<?php echo esc_url( $video_url ); ?>';

                            if (Hls.isSupported()) {
                                var hls = new Hls();
                                hls.loadSource(videoSrc);
                                hls.attachMedia(video);
                            } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
                                video.src = videoSrc;
                            }
                        });
                    </script>
                <?php else : ?>
                    <div style="padding: 20px;">
                        <?php the_content(); ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Live Stats & Details -->
            <div class="live-info-bar" style="display: flex; justify-content: space-between; align-items: center; background: #181818; padding: 15px 20px; border-radius: 8px;">
                <div>
                    <p style="margin: 0; color: #aaa; font-size: 14px;">Channel</p>
                    <strong style="font-size: 16px;"><?php the_author(); ?></strong>
                </div>

                <div>
                    <span class="realtime-views" data-post-id="<?php echo $post_id; ?>" style="background: #282828; color: #ff4e4e; font-weight: bold; padding: 8px 16px; border-radius: 20px; font-size: 14px; border: 1px solid #333;">
                        <?php 
                        if ( function_exists( 'bdtube_get_video_stats' ) ) {
                            echo bdtube_get_video_stats( $post_id );
                        } else {
                            echo '1 watching now';
                        }
                        ?>
                    </span>
                </div>
            </div>

        </main>

        <!-- Live Heartbeat Ping Script -->
        <!-- Live Heartbeat Ping Script -->
        <script>
        function sendLiveHeartbeat() {
            fetch('<?php echo get_template_directory_uri(); ?>/live-ping.php?id=<?php echo $post_id; ?>');
        }

        // প্রতি ৫ সেকেন্ড পর পর পিং পাঠাবে
        var heartbeatInterval = setInterval(sendLiveHeartbeat, 5000);
        sendLiveHeartbeat();

        // ইউজার ট্যাব/ব্রাউজার বন্ধ করলে সেশন মুছে ফেলার নোটিশ পাঠানো
        window.addEventListener('pagehide', function() {
            var leaveUrl = '<?php echo get_template_directory_uri(); ?>/live-ping.php?id=<?php echo $post_id; ?>&action=leave';
            navigator.sendBeacon(leaveUrl);
        });
        </script>

        <?php 
    endwhile; 
endif; 

get_footer(); 
?>