<?php
/**
 * Displays order button
 *
 * List of parameters
 * button_text = Button text
 * html_class = HTML class for button
 * id = WHMCS product ID from mysql table
 * billingcycle = Billing cycle e.g. annually
 * html_id = HTML id for button
 * currency = Currency ID
 */

extract( shortcode_atts( [
	'html_template' => '',
	'image'         => '',
	'button_text'   => whmpress_get_option( 'ob_button_text' ),
	'html_class'    => 'whmpress whmpress_order_button',
	'id'            => '0',
	'billingcycle'  => whmpress_get_option( 'ob_billingcycle' ),
	'html_id'       => '',
	'currency'      => '0',
	"params"        => '',
], $atts ) );
if ( empty( $currency ) ) {
	$currency = whmp_get_currency( $currency );
}
$value = $button_text;
$class = $html_class;

$Q = "SELECT `paytype` FROM `" . get_mysql_table_name( "tblproducts" ) . "` WHERE `id`='$id'";
global $wpdb;
$paytype = $wpdb->get_var( $Q );

$WHMPress = new WHMpress();

# Generating URL.
if ( is_plugin_active( 'WHMpress_Client_Area/client-area.php' ) ) {
	global $WHMPress_Client_Area;
	if ( empty( $WHMPress_Client_Area ) ) {
		$WHMPress_Client_Area = new WHMPress_Client_Area;
	}
	if ( $WHMPress_Client_Area->is_permalink() ) {
		if ( $paytype == "onetime" ) {
			$url = rtrim( $WHMPress->get_whmcs_url( "order" ), "/" ) . "/pid/{$id}/a/add/currency/{$currency}/";
		} else {
			$url = rtrim( $WHMPress->get_whmcs_url( "order" ), "/" ) . "/pid/{$id}/a/add/currency/{$currency}/billingcycle/{$billingcycle}/";
		}
	} else {
		if ( $paytype == "onetime" ) {
			$url = $WHMPress->get_whmcs_url( "order" ) . "pid={$id}&a=add&currency={$currency}";
		} else {
			$url = $WHMPress->get_whmcs_url( "order" ) . "pid={$id}&a=add&currency={$currency}&billingcycle={$billingcycle}";
		}
	}
} else {
	if ( $paytype == "onetime" ) {
		$url = $WHMPress->get_whmcs_url( "order" ) . "pid={$id}&a=add&currency={$currency}";
	} else {
		$url = $WHMPress->get_whmcs_url( "order" ) . "pid={$id}&a=add&currency={$currency}&billingcycle={$billingcycle}";
	}
}

if ( $params <> "" ) {
	$url .= "&" . $params;
}

$parsed_url = parse_url( $url );
if ( isset( $parsed_url["query"] ) ) {
	
}

# Generating output string.
$WHMPress = new WHMPress;

$html_template = $WHMPress->check_template_file( $html_template, "whmpress_order_button" );

if ( is_file( $html_template ) ) {
	$vars = [
		"product_order_button" => "<button type='button' id='$html_id' class=\"{$class}\" onclick=\"window.location.href='{$url}'\">{$value}</button>",
		"button_text"          => $button_text,
		"order_button_text"    => $button_text,
		"url"                  => $url,
		"product_order_url"    => $url,
	];
	
	# Getting custom fields and adding in output
	$TemplateArray = $WHMPress->get_template_array( "whmpress_order_button" );
	foreach ( $TemplateArray as $custom_field ) {
		$vars[ $custom_field ] = isset( $atts[ $custom_field ] ) ? $atts[ $custom_field ] : "";
	}
	
	$OutputString = whmp_smarty_template( $html_template, $vars );
	
	return $OutputString;
} else {
	$str = "<button type='button' id='$html_id' class=\"{$class}\" onclick=\"window.location.href='{$url}'\">{$value}</button>";
	
	# Returning output string
	return $str;
}