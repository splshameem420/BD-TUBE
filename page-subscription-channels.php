<?php
/*
Template Name: Subscription Channels List
*/
get_header(); 
?>

<main class="main-content" style="padding: 20px; background: #0f0f0f; min-height: 100vh;">
    <div class="channels-container" style="max-width: 900px; margin: 0 auto;">
        
        <h2 style="color: #fff; margin-bottom: 20px; font-size: 22px; border-bottom: 1px solid #333; padding-bottom: 10px;">
            All Subscribed Channels
        </h2>

        <?php if ( is_user_logged_in() ) : 
            $user_id = get_current_user_id();
            $subscribed_channels = get_user_meta( $user_id, 'subscribed_channels', true );

            if ( ! empty( $subscribed_channels ) && is_array( $subscribed_channels ) ) : ?>
                <div class="channels-list" style="display: flex; flex-direction: column; gap: 15px;">
                    <?php foreach ( $subscribed_channels as $channel_id ) :
                        $user_info = get_userdata( $channel_id );
                        if ( ! $user_info ) continue;

                        $channel_url = get_author_posts_url( $channel_id );
                        $video_count = count_user_posts( $channel_id );
                        ?>
                        <div class="channel-card" style="display: flex; align-items: center; justify-content: space-between; background: #181818; padding: 15px 20px; border-radius: 12px;">
                            
                            <a href="<?php echo esc_url( $channel_url ); ?>" style="display: flex; align-items: center; gap: 15px; text-decoration: none; color: #fff;">
                                <?php echo get_avatar( $channel_id, 56, '', '', array('style' => 'border-radius: 50%;') ); ?>
                                <div>
                                    <h3 style="margin: 0; font-size: 16px; color: #fff;"><?php echo esc_html( $user_info->display_name ); ?></h3>
                                    <p style="margin: 4px 0 0 0; font-size: 13px; color: #aaa;"><?php echo $video_count; ?> videos</p>
                                </div>
                            </a>

                            <button class="sub-btn subscribed" data-author="<?php echo esc_attr( $channel_id ); ?>" style="background: #333; color: #fff; border: none; padding: 8px 18px; border-radius: 18px; font-weight: bold; cursor: pointer;">
                                Subscribed
                            </button>

                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else : ?>
                <div style="text-align: center; color: #aaa; margin-top: 50px;">
                    <p>আপনি এখনো কোনো চ্যানেল সাবস্ক্রাইব করেননি।</p>
                </div>
            <?php endif; ?>

        <?php else : ?>
            <div style="text-align: center; color: #fff; margin-top: 50px;">
                <p style="color: #aaa;">সাবস্ক্রাইব করা চ্যানেলের তালিকা দেখতে সাইন ইন করুন।</p>
                <a href="<?php echo esc_url( wp_login_url( home_url('/subscriptions/channels/') ) ); ?>" style="display: inline-block; background: #cc0000; color: #fff; padding: 10px 20px; border-radius: 20px; text-decoration: none; font-weight: bold; margin-top: 10px;">Sign In</a>
            </div>
        <?php endif; ?>

    </div>
</main>

<?php get_footer(); ?>