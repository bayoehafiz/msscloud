<?php
include_once ("../../../../wp-load.php");

# If admin user is not logged in then redirect back.
if (!current_user_can( 'manage_options' )) {
    wp_redirect(get_admin_url()."admin.php?page=whmp_client_area&settings-updated=false");
    exit;
}

/*
 * Only added field names in this array will apply in ini settings.
 */
$allowed_settings = [
	"remove_whmcs_logo",
	"remove_whmcs_menu",
	"remove_copyright",
	"remove_breadcrumb",
	"remove_powered_by",
	"whmp_hide_currency_select",
	"whmp_hide_client_ip",
	"whmpca_custom_css",
	"whmp_show_admin_notice1",
	"jquery_source",
	"whmp_use_permalinks",
	"use_whmcs_css_files",
	"load_dropdown",
	"exclude_js_files",
	"whmp_config_data",
	"curl_timeout_whmp",
	"cache_enabled_whmp",
	"whmcs_load_sytle_orders"
];

if ( isset( $_POST["import_settings"] ) && isset( $_POST["file"] ) && is_file( $_POST["file"] ) ) {

	if ( $_POST["import_settings"] == "" ) {
		$settings = parse_ini_file( $_POST["file"] );
	} else {
		$settings = parse_ini_file( $_POST["file"], true );
		if ( isset( $settings[ $_POST["import_settings"] ] ) ) {
			$settings = $settings[ $_POST["import_settings"] ];
		} else {
			$settings = array();
		}
	}

	if ( ! is_array( $settings ) ) {
		wp_redirect( get_admin_url() . "admin.php?page=whmp_client_area&settings-updated=false" );
		exit;
	}

	foreach ( $settings as $k => $v ) {
		if ( in_array( $k, $allowed_settings ) ) {
			update_option( $k, $v );
		}
	}

	# Redirect back with true message.

	wp_redirect( get_admin_url() . "admin.php?page=whmp_client_area&settings-updated=true" );
	exit;
}

# If no valid form data submitted then redirect back with false message.

wp_redirect( get_admin_url() . "admin.php?page=whmp_client_area&settings-updated=false" );
exit;