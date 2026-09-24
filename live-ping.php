<?php
// WordPress Core Load
define('WP_USE_THEMES', false);
require_once('../../../wp-load.php');

if (isset($_GET['id'])) {
    $post_id = intval($_GET['id']);
    
    if ($post_id > 0) {
        $temp_dir = sys_get_temp_dir() . '/bdtube_live_sessions/';
        if (!file_exists($temp_dir)) {
            mkdir($temp_dir, 0777, true);
        }

        $user_ip = $_SERVER['REMOTE_ADDR'];
        $session_file = $temp_dir . 'session_' . $post_id . '_' . md5($user_ip);

        if (isset($_GET['action']) && $_GET['action'] === 'leave') {
            if (file_exists($session_file)) {
                @unlink($session_file);
            }
        } else {
            file_put_contents($session_file, time());
        }

        $files = glob($temp_dir . 'session_' . $post_id . '_*');
        $current_time = time();
        $active_count = 0;

        if ($files) {
            foreach ($files as $file) {
                if (file_exists($file)) {
                    $last_active = intval(file_get_contents($file));
                    
                    if (($current_time - $last_active) > 10) {
                        @unlink($file);
                    } else {
                        $active_count++;
                    }
                }
            }
        }

        update_post_meta($post_id, 'live_watching_count', $active_count);
        
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'watching' => $active_count]);
        exit;
    }
}

header('Content-Type: application/json');
echo json_encode(['status' => 'error']);