
<style>

.playlist-page-container {
    max-width: 1280px;
    margin: 20px auto;
    padding: 0 24px;
    color: #f1f1f1;
    font-family: 'Roboto', 'Arial', sans-serif;
}

.playlist-page-container .page-title {
    font-size: 24px;
    font-weight: 700;
    margin-bottom: 24px;
    color: #f1f1f1;
}

/* Grid Layout for Playlists */
.playlist-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 20px;
}

/* Playlist Card */
.playlist-card {
    display: flex;
    flex-direction: column;
    background: transparent;
    border-radius: 12px;
    transition: transform 0.2s ease;
}

.playlist-card:hover {
    transform: translateY(-2px);
}

/* Thumbnail Overlay & Box */
.playlist-thumbnail {
    position: relative;
    width: 100%;
    aspect-ratio: 16 / 9;
    background: #1f1f1f;
    border-radius: 12px;
    overflow: hidden;
}

.playlist-thumbnail a {
    display: block;
    width: 100%;
    height: 100%;
}

.thumb-overlay {
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: linear-gradient(180deg, rgba(0, 0, 0, 0.2) 0%, rgba(0, 0, 0, 0.6) 100%), #272727;
    color: #ffffff;
    transition: background 0.2s ease;
}

.playlist-card:hover .thumb-overlay {
    background: linear-gradient(180deg, rgba(0, 0, 0, 0.4) 0%, rgba(0, 0, 0, 0.8) 100%), #3f3f3f;
}

.thumb-overlay .material-icons {
    font-size: 42px;
    color: #f1f1f1;
}

.video-count {
    font-size: 12px;
    margin-top: 6px;
    background: rgba(0, 0, 0, 0.75);
    padding: 3px 8px;
    border-radius: 4px;
    font-weight: 500;
}

/* Playlist Info Meta */
.playlist-info {
    padding: 12px 0 4px 0;
}

.playlist-title {
    margin: 0;
    font-size: 15px;
    font-weight: 600;
    line-height: 1.3;
}

.playlist-title a {
    color: #f1f1f1;
    text-decoration: none;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.playlist-title a:hover {
    color: #3ea6ff;
}

.playlist-meta {
    color: #aaa;
    font-size: 12px;
    margin: 4px 0 8px 0;
}

.view-full-playlist {
    color: #aaa;
    text-decoration: none;
    font-size: 12px;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    transition: color 0.2s ease;
}

.view-full-playlist:hover {
    color: #ffffff;
}

/* Signed-out State Wrapper */
.playlist-page-container.signed-out {
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

.btn-signin {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    border: 1px solid #3ea6ff;
    color: #3ea6ff;
    padding: 8px 16px;
    border-radius: 18px;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    transition: background 0.2s ease;
}

.btn-signin:hover {
    background: rgba(62, 166, 255, 0.15);
}
</style>

<?php
/**
 * Template Name: Subscription Playlist Page
 * Description: BD TUBE - User Playlists Page (/for/playlists)
 */

get_header();

// ইউজার লগইন না থাকলে সাইন ইন স্টেট দেখাবে
if ( ! is_user_logged_in() ) : ?>
    <div class="playlist-page-container signed-out">
        <div class="signed-out-content">
            <span class="material-icons size-large">playlist_play</span>
            <h2>আপনার পছন্দের প্লেলিস্টগুলো এখানে সংরক্ষণ করুন</h2>
            <p>প্লেলিস্ট তৈরি করতে বা দেখতে সাইন ইন করুন।</p>
            <a href="<?php echo esc_url( wp_login_url( home_url( '/for/playlist' ) ) ); ?>" class="btn-signin">
                <span class="material-icons">account_circle</span> সাইন ইন
            </a>
        </div>
    </div>
<?php 
else : 
    $user_id = get_current_user_id();
    
    // কাস্টম প্লেলিস্ট ট্যাক্সোনমি বা টার্মস লোড করা (যদি থাকে)
    $playlists = get_terms( array(
        'taxonomy'   => 'playlist',
        'hide_empty' => false,
    ) );
?>

<div class="playlist-page-container">
    <h1 class="page-title">Playlists</h1>

    <div class="playlist-grid">
        
        <!-- ১. সিস্টেম প্লেলিস্ট: Liked Videos -->
        <div class="playlist-card">
            <div class="playlist-thumbnail">
                <a href="<?php echo esc_url( home_url( '/playlist?list=LL' ) ); ?>">
                    <div class="thumb-overlay">
                        <span class="material-icons">thumb_up</span>
                    </div>
                </a>
            </div>
            <div class="playlist-info">
                <h3 class="playlist-title">
                    <a href="<?php echo esc_url( home_url( '/playlist?list=LL' ) ); ?>">পছন্দ করা ভিডিও (Liked)</a>
                </h3>
                <p class="playlist-meta">অটো জেনারেটেড প্লেলিস্ট</p>
                <a href="<?php echo esc_url( home_url( '/playlist?list=LL' ) ); ?>" class="view-full-playlist">সম্পূর্ণ প্লেলিস্ট দেখুন</a>
            </div>
        </div>

        <!-- ২. সিস্টেম প্লেলিস্ট: Watch Later -->
        <div class="playlist-card">
            <div class="playlist-thumbnail">
                <a href="<?php echo esc_url( home_url( '/playlist?list=WL' ) ); ?>">
                    <div class="thumb-overlay">
                        <span class="material-icons">watch_later</span>
                    </div>
                </a>
            </div>
            <div class="playlist-info">
                <h3 class="playlist-title">
                    <a href="<?php echo esc_url( home_url( '/playlist?list=WL' ) ); ?>">পরে দেখুন (Watch Later)</a>
                </h3>
                <p class="playlist-meta">অটো জেনারেটেড প্লেলিস্ট</p>
                <a href="<?php echo esc_url( home_url( '/playlist?list=WL' ) ); ?>" class="view-full-playlist">সম্পূর্ণ প্লেলিস্ট দেখুন</a>
            </div>
        </div>

        <!-- ৩. ইউজারের তৈরি করা কাস্টম প্লেলিস্টসমূহ (যদি থাকে) -->
        <?php if ( ! empty( $playlists ) && ! is_wp_error( $playlists ) ) : ?>
            <?php foreach ( $playlists as $playlist ) : 
                $playlist_link = get_term_link( $playlist );
            ?>
                <div class="playlist-card">
                    <div class="playlist-thumbnail">
                        <a href="<?php echo esc_url( $playlist_link ); ?>">
                            <div class="thumb-overlay">
                                <span class="material-icons">playlist_play</span>
                                <span class="video-count"><?php echo esc_html( $playlist->count ); ?> টি ভিডিও</span>
                            </div>
                        </a>
                    </div>
                    <div class="playlist-info">
                        <h3 class="playlist-title">
                            <a href="<?php echo esc_url( $playlist_link ); ?>"><?php echo esc_html( $playlist->name ); ?></a>
                        </h3>
                        <p class="playlist-meta">কাস্টম প্লেলিস্ট</p>
                        <a href="<?php echo esc_url( $playlist_link ); ?>" class="view-full-playlist">সম্পূর্ণ প্লেলিস্ট দেখুন</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>
</div>

<?php 
endif; 
get_footer();