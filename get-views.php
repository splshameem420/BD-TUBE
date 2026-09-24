<?php
$wp_load_path = dirname(__DIR__, 3) . '/wp-load.php';

if (!file_exists($wp_load_path)) {
    $wp_load_path = dirname(__DIR__, 4) . '/wp-load.php';
}

if (file_exists($wp_load_path)) {
    define('WP_USE_THEMES', false);
    require_once($wp_load_path);
} else {
    header('Content-Type: application/json');
    echo json_encode(['views' => '0 views', 'error' => 'wp-load.php not found']);
    exit;
}

$post_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

header('Content-Type: application/json');

if ($post_id > 0 && function_exists('bdtube_get_video_stats')) {
    $views_text = bdtube_get_video_stats($post_id);
    echo json_encode(['views' => $views_text]);
} else {
    echo json_encode(['views' => '0 views']);
}
exit;