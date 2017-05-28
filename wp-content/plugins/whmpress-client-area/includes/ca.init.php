<?php

# Activating is_plugin_active() function by WordPress
require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

# Managing list of shortcodes
## Line 2 change for Abdul Waheed
$whmpca_shortcodes_list = [
	"whmpress_client_area"           => "whmpress_client_area_function",
	"whmpress_whmcs_page"            => "whmpress_whmcs_page",
	"whmpress_whmcs_if_loggedin"     => "whmpress_whmcs_if_loggedin",
	"whmpress_whmcs_if_not_loggedin" => "whmpress_whmcs_if_not_loggedin",
	"whmpress_whmcs_cart"            => "whmpress_whmcs_cart",
	"whmpress_whmcs_info"            => "whmpress_whmcs_info",

	"wca_client_area"           => "whmpress_client_area_function",
	"wca_whmcs_page"            => "whmpress_whmcs_page",
	"wca_whmcs_if_loggedin"     => "whmpress_whmcs_if_loggedin",
	"wca_whmcs_if_not_loggedin" => "whmpress_whmcs_if_not_loggedin",
	"wca_whmcs_cart"            => "whmpress_whmcs_cart",                       // Template Supported
	"wca_whmcs_info"            => "whmpress_whmcs_info",
	"wca_login_form"            => "wca_login_form",                            // Template Supported
	/*"wca_login_form_modal"    => "wca_login_form_modal",
	"wca_login_popup"           => "wca_login_popup",*/
	"wca_whmcs_menu"            => "wca_whmcs_menu",                            // Template Supported
];


if ( ! function_exists( 'http_parse_headers' ) ) {
	function http_parse_headers( $raw_headers ) {
		$headers = [];
		$key     = ''; // [+]

		foreach ( explode( "\n", $raw_headers ) as $i => $h ) {
			$h = explode( ':', $h, 2 );

			if ( isset( $h[1] ) ) {
				if ( ! isset( $headers[ $h[0] ] ) ) {
					$headers[ $h[0] ] = trim( $h[1] );
				} elseif ( is_array( $headers[ $h[0] ] ) ) {
					// $tmp = array_merge($headers[$h[0]], array(trim($h[1]))); // [-]
					// $headers[$h[0]] = $tmp; // [-]
					$headers[ $h[0] ] = array_merge( $headers[ $h[0] ], array( trim( $h[1] ) ) ); // [+]
				} else {
					// $tmp = array_merge(array($headers[$h[0]]), array(trim($h[1]))); // [-]
					// $headers[$h[0]] = $tmp; // [-]
					$headers[ $h[0] ] = array_merge( array( $headers[ $h[0] ] ), array( trim( $h[1] ) ) ); // [+]
				}

				$key = $h[0]; // [+]
			} else // [+]
			{ // [+]
				if ( substr( $h[0], 0, 1 ) == "\t" ) // [+]
				{
					$headers[ $key ] .= "\r\n\t" . trim( $h[0] );
				} // [+]
				elseif ( ! $key ) // [+]
				{
					$headers[0] = trim( $h[0] );
				}
				trim( $h[0] ); // [+]
			} // [+]
		}

		return $headers;
	}
}

if ( ! function_exists( 'curl_file_create' ) ) {
	function curl_file_create( $filename, $mimetype = '', $postname = '' ) {
		return "@$filename;filename=" . ( $postname ?: basename( $filename ) ) . ( $mimetype ? ";type=$mimetype" : '' );
	}
}

if ( ! function_exists( 'wp_current_url' ) ) {
	function wp_current_url( $include_query_string = false ) {
		$s                  = $_SERVER;
		$use_forwarded_host = false;

		$ssl      = ( ! empty( $s['HTTPS'] ) && $s['HTTPS'] == 'on' ) ? true : false;
		$sp       = strtolower( $s['SERVER_PROTOCOL'] );
		$protocol = substr( $sp, 0, strpos( $sp, '/' ) ) . ( ( $ssl ) ? 's' : '' );
		$port     = $s['SERVER_PORT'];
		$port     = ( ( ! $ssl && $port == '80' ) || ( $ssl && $port == '443' ) ) ? '' : ':' . $port;
		$host     = ( $use_forwarded_host && isset( $s['HTTP_X_FORWARDED_HOST'] ) ) ? $s['HTTP_X_FORWARDED_HOST'] : ( isset( $s['HTTP_HOST'] ) ? $s['HTTP_HOST'] : null );
		$host     = isset( $host ) ? $host : $s['SERVER_NAME'] . $port;

		return $protocol . '://' . $host . $s['REQUEST_URI'];
	}
}