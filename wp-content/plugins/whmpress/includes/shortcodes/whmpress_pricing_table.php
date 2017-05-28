<?php
extract( shortcode_atts( [
	'html_template' => '',
	'image'         => '',
	'id'            => '0',
	'html_class'    => 'whmpress whmpress_pricing_table',
	'html_id'       => '',
	'billingcycle'  => whmpress_get_option( "pt_billingcycle" ),
	'show_price'    => whmpress_get_option( "pt_show_price" ), //'Yes',
	'show_combo'    => whmpress_get_option( "pt_show_combo" ), //'No',
	'show_button'   => whmpress_get_option( "pt_show_button" ), //'Yes',
	"show_discount" => whmpress_get_option( "combo_show_discount" ),
	'discount_type' => whmpress_get_option( 'combo_discount_type' ),
	'currency'      => '',
	"button_text"   => whmpress_get_option( "pt_button_text" ), //"Order",
	"button_class"  => "",
], $atts ) );

$button_text = __( $button_text, "whmpress" );


if ( empty( $currency ) ) {
	if ( ! session_id() ) {
		session_start();
	}
	if ( isset( $_SESSION["currency"] ) ) {
		$currency = $_SESSION["currency"];
	}
	if ( empty( $currency ) ) {
		$currency = whmp_get_default_currency_id();
	}
}

# Checking parameters
#$html_class = !empty($atts["html_class"])?$atts["html_class"]:""; if ($html_class=="") $html_class = "whmpress whmpress_price_box";
#$html_id = !empty($atts["html_id"])?$atts["html_id"]:"";
#$id = !empty($atts["id"])?$atts["id"]:"0";
#$billingcycle = !empty($atts["billingcycle"])?$atts["billingcycle"]:whmpress_get_option("billingcycle");
#$show_price = !empty($atts["show_price"])?$atts["show_price"]:"Yes";
#$show_combo = !empty($atts["show_combo"])?$atts["show_combo"]:"No";
#$show_button = !empty($atts["show_button"])?$atts["show_button"]:"Yes";

# Getting data from MySQL
global $wpdb;
/*$Q = "SELECT `name`,`description` FROM `".whmp_get_products_table_name()."` WHERE `id`=$id";
$row = $wpdb->get_row($Q,ARRAY_A);
if (isset($row["name"])) $row["name"] = whmpress_encoding($row["name"]);*/

$row["name"] = whmpress_name_function( [ "no_wrapper" => "1", "id" => $id ] );
$description = $row["description"] = whmpress_description_function(
	[
		"id" => $id,
	]
);


# Getting price
# Getting price
$price = whmpress_price_function(
	[
		"id"                               => $id,
		"billingcycle"                     => $billingcycle,
		"currency"                         => $currency,
		"do_not_show_config_option_string" => "1",
		"return_array"                     => "1"
	]
);

# Getting description
/*$description = trim(strip_tags(whmpress_encoding($row["description"])),"\n");
$description = explode("\n",$description);
$description = "<ul>\n<li>". implode("</li><li>",$description). "</li>\n</ul>";*/

if ( strtolower( $show_combo ) == "yes" ) {
	# Getting combo
	$combo       = whmpress_order_combo_function(
		[
			"id"            => $id,
			"show_button"   => "Yes",
			"currency"      => $currency,
			"discount_type" => $discount_type,
			"button_text"   => $button_text,
			"button_class"  => $button_class,
		]
	);
	$show_button = "No";
} else {
	$combo = "";
}

if ( strtolower( $show_button ) == "yes" ) {
	# Getting button
	$button = whmpress_order_button_function( [ "id"           => $id,
	                                            "button_text"  => $button_text,
	                                            "billingcycle" => $billingcycle,
	                                            "currency"     => $currency
	] );
} else {
	$button = "";
}

# Check if template file exists in theme folder
$WHMPress = new WHMPress;

$html_template = $WHMPress->check_template_file( $html_template, "whmpress_pricing_table" );

if ( is_file( $html_template ) ) {
	$decimal_sperator = get_option( 'decimal_replacement', "." );
	$amount           = whmpress_price_function( [ "id"            => $id,
	                                               "billingcycle"  => $billingcycle,
	                                               "currency"      => $currency,
	                                               "prefix"        => "no",
	                                               "suffix"        => "no",
	                                               "show_duration" => "no"
	] );
	$totay            = explode( $decimal_sperator, strip_tags( $amount ) );
	$amount1          = $totay[0];
	$fraction         = isset( $totay[1] ) ? $totay[1] : "";
	$totay            = explode( "/", strip_tags( $price['return_string'] ) );
	$duration         = @$totay[1];
	$order_url        = whmpress_order_url_function(
		[
			"id"           => $id,
			"billingcycle" => $billingcycle,
		]
	);
	$button_text      = whmpress_encoding( $button_text );
	
	$vars = [
		"product_name"         => $row["name"],
		"product_price"        => $price['return_string'],
		"product_description"  => $description,
		"product_order_combo"  => $combo,
		"product_order_button" => $button,
		"order_button_text"    => $button_text,
		"image"                => $image,
		"prefix"               => whmp_get_currency_prefix( $currency ),
		"suffix"               => whmp_get_currency_suffix( $currency ),
		"amount"               => $amount1,
		"fraction"             => $fraction,
		"duration"             => $duration,
		"decimal"              => $decimal_sperator,
		"order_url"            => $order_url,
		"button_text"          => $button_text,
		"config_option_string" => whmpress_get_option( "config_option_string" ),
		"paytype"              => $price['paytype']
	];
	
	# Getting custom fields and adding in output
	$TemplateArray = $WHMPress->get_template_array( "whmpress_pricing_table" );
	
	foreach ( $TemplateArray as $custom_field ) {
		$vars[ $custom_field ] = isset( $atts[ $custom_field ] ) ? $atts[ $custom_field ] : "";
	}
	
	$OutputString = whmp_smarty_template( $html_template, $vars );
	
	return $OutputString;
} else {
	# Generating OutputString
	$OutputString = "<h3>" . $row["name"] . "</h3>";
	$OutputString .= $description;
	
	# Check if price is requested or not
	if ( strtolower( $show_price ) == "yes" ) {
		$OutputString .= "<h4>" . $price['return_string'] . "</h4>";
	}
	
	# Check if combo is requested or not
	if ( strtolower( $show_combo ) == "yes" ) {
		$OutputString .= $combo;
	}
	
	# Check if button is requested or not
	if ( strtolower( $show_button ) == "yes" ) {
		$OutputString .= $button;
	}
	
	# Returning output string with wrapper div
	$ID    = ! empty( $html_id ) ? "id='$html_id'" : "";
	$CLASS = ! empty( $html_class ) ? "class='$html_class'" : "";
	
	return "<div $CLASS $ID>" . $OutputString . "</div>";
}