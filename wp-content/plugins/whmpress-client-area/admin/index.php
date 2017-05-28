<?php

if (get_option("whmp_show_admin_notice1") == "1") {
    function whmp_show_admin_notice1()
    {
        update_option("whmp_show_admin_notice1", "0");
        $url = get_option("client_area_page_url");
        if (is_numeric($url) && get_post_status($url) !== false) {
            $url = get_page_link($url);
        }
        ?>
        <div class="updated">
            <p><?php _e("Your \"<b>WHMCS Client Area</b>\" page is created, click <a href='$url'>here</a> to visit <b>Client Area</b>", 'whmpress'); ?></p>
        </div>
        <?php
    }
    //add_action( 'admin_notices', 'whmp_show_admin_notice1' );
}