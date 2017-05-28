<?php
add_action('admin_menu', 'whmpca_add_pages');
function whmpca_add_pages() {
    add_menu_page('WHMCS Client Area', 'WHMCS Client Area', 'manage_options', 'whmp_client_area', 'whmp_client_area', WHMP_CA_URL . "/admin/images/whitelogo-16.png" ,'81.69850');
}
function whmp_client_area() {
    require_once (WHMP_CA_PATH . '/admin/client_area.php');
}

add_action( 'wp_ajax_remove_cache_whmp', 'remove_cache_whmp' );
function remove_cache_whmp() {
    $cache_path = WHMP_CA_PATH."/cache/*";
    $files = glob($cache_path);
    foreach($files as $file) {
        if (!@unlink($file)) {
            echo __("Error occured while deleteing cached files",'whmpress');
            exit;
        }
    }
    $files = glob($cache_path);
    echo "OK".count($files);
    wp_die();
}