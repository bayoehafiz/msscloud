<?php
extract( shortcode_atts( [
	'html_template' => '',
	'type'          => whmpress_get_option( "dp_type" ),
	'years'         => whmpress_get_option( "dp_years" ),
	'tld'           => '.com',
	'currency'      => whmpress_get_option( "price_currency" ),
	"decimals"      => whmpress_get_option( "dp_decimals" ),
	"hide_decimal"  => whmpress_get_option( "dp_hide_decimal" ),
	"decimals_tag"  => whmpress_get_option( "dp_decimals_tag" ),
	"prefix"        => whmpress_get_option( "dp_prefix" ),
	"suffix"        => whmpress_get_option( "dp_suffix" ),
	"show_duration" => whmpress_get_option( "dp_show_duration" ),
	"price_tax"     => whmpress_get_option( "dp_price_tax" ),
	"html_class"    => "whmpress whmpress_domain_price",
	"html_id"       => "",
	"no_wrapper"    => "",
], $atts ) );
global $wpdb;

$tld      = "." . ltrim( $tld, "." );
$WHMPress = new WHMPress;

$AvailableTypes = [
	"domainregister",
	"domainrenew",
	"domaintransfer"
];
if ( ! in_array( $type, $AvailableTypes ) ) {
	$OutputString = __( 'Invalid type', 'whmpress' );
	
	return $OutputString;
}

/*
    msetupfee = 1 Year
    qsetupfee = 2 Years
    ssetupfee = 3 Years
    asetupfee = 4 Years
    bsetupfee = 5 Years
    monthly = 6 Years
    quarterly = 7 Years
    semiannually = 8 Years
    annually = 9 Years
    biennially = 10 Years
*/
$YearColumn = [
	"1"  => "msetupfee",
	"2"  => "qsetupfee",
	"3"  => "ssetupfee",
	"4"  => "asetupfee",
	"5"  => "bsetupfee",
	"6"  => "monthly",
	"7"  => "quarterly",
	"8"  => "semiannually",
	"9"  => "annually",
	"10" => "biennially"
];
if ( ! array_key_exists( $years, $YearColumn ) ) {
	$OutputString = __( "Invalid year", "whmpress" );
	
	return $OutputString;
}

if ( empty( $currency ) ) {
	$currency = whmp_get_currency();
}

$Q = "SELECT `{$YearColumn[$years]}` FROM `" . whmp_get_pricing_table_name() . "` pt, `" . whmp_get_domain_pricing_table_name() . "` dpt WHERE dpt.id=`relid`
AND `extension`='$tld'
AND `type`='$type' AND `currency`='$currency'";

$price = $wpdb->get_var( $Q );

if ( is_null( $price ) || $price === false ) {
	$OutputString = __( "Invalid TLD", "whmpress" );
	
	return $OutputString;
} else {
	# Calculating tax.
	$TaxEnabled = $wpdb->get_var( "SELECT `value` FROM " . whmp_get_configuration_table_name() . " WHERE `setting`='TaxEnabled'" );
	$TaxDomains = $wpdb->get_var( "SELECT `value` FROM " . whmp_get_configuration_table_name() . " WHERE `setting`='TaxDomains'" );
	
	#var_dump($TaxDomains);
	#var_dump($TaxEnabled);
	
	$tax_amount = $base_price = $price;
	if ( strtolower( $TaxEnabled ) == "on" && strtolower( $TaxDomains ) == "on" ) {
		$taxes      = whmpress_calculate_tax( $price );
		$base_price = $taxes["base_price"];
		$tax_amount = $taxes["tax_amount"];
		
		if ( $price_tax == "default" ) {
			$price_tax = "";
		}
		$price_tax = trim( strtolower( $price_tax ) );
		
		if ( $price_tax == "exclusive" ) {
			$price = $base_price;
		} elseif ( $price_tax == "inclusive" ) {
			$price = $base_price + $tax_amount;
		} elseif ( $price_tax == "tax" ) {
			$price = $tax_amount;
		}
	}
}
$simple_price = $price;

if ( strtolower( $hide_decimal ) <> "yes" ) {
	if ( get_option( "show_trailing_zeros" ) == "yes" ) {
		$CurrencyFormatFunction = "number_format";
	} else {
		$CurrencyFormatFunction = "round";
	}
	
	$price = $CurrencyFormatFunction( $price, $decimals );
}

// Removing decimal symbol
if ( strtolower( $hide_decimal ) == "yes" ) {
	$price = str_replace( ".", "", $price );
}

if ( $decimals_tag <> "" && strtolower( $hide_decimal ) <> "yes" ) {
	$parts = explode( ".", $price );
	if ( $decimals > 0 ) {
		$parts[1] = "<{$decimals_tag}>." . ( @$parts[1] ) . "</{$decimals_tag}>";
	} else {
		$parts[1] = "";
	}
	$price = @$parts[0] . @$parts[1];
	$price = rtrim( $price, "." );
}

if ( strtolower( $prefix ) <> "no" ) {
	$prefix_symbol = whmp_get_currency_prefix( $currency );
	if ( strtolower( $prefix ) <> "yes" ) {
		$prefix_symbol = "<{$prefix}>" . $prefix_symbol . "</{$prefix}>";
	}
} else {
	$prefix_symbol = "";
}

if ( strtolower( $suffix ) <> "no" ) {
	$suffix_symbol = whmp_get_currency_suffix( $currency );
	if ( strtolower( $suffix ) <> "yes" ) {
		$suffix_symbol = "<{$suffix}>" . $suffix_symbol . "</{$suffix}>";
	}
} else {
	$suffix_symbol = "";
}

if ( strtolower( $show_duration ) <> "no" ) {
	if ( strtolower( $show_duration ) == "yes" ) {
		$duration = "/" . $years . __( " Years", "whmpress" );
	} else {
		$duration = "<$show_duration>/";
		if ( $years == "1" ) {
			$duration .= __( "Year", "whmpress" );
		} else {
			$duration .= "$years " . __( "Years", "whmpress" );
		}
		$duration .= "</$show_duration>";
	}
} else {
	$duration = "";
}

$html_template = $WHMPress->check_template_file( $html_template, "whmpress_domain_price" );
if ( is_file( $html_template ) ) {
	$decimal_sperator = get_option( 'decimal_replacement', "." );
	$totay            = explode( $decimal_sperator, strip_tags( $simple_price ) );
	$amount1          = $totay[0];
	$fraction         = isset( $totay[1] ) ? $totay[1] : "";
	$totay            = explode( "/", strip_tags( $price ) );
	$duration         = @$totay[1];
	
	if ( $years == "1" ) {
		$duration .= __( 'Year', 'whmpress' );
	} else {
		$duration .= $years . __( 'Years', 'whmpress' );
	}
	
	$vars                 = $atts;
	$vars["domain_price"] = $prefix_symbol . $price . $suffix_symbol . $duration;
	$vars["prefix"]       = whmp_get_currency_prefix( $currency );
	$vars["suffix"]       = whmp_get_currency_suffix( $currency );
	$vars["amount"]       = $amount1;
	$vars["fraction"]     = $fraction;
	$vars["decimal"]      = $decimal_sperator;
	$vars["duration"]     = $duration;
	
	# Getting custom fields and adding in output
	$TemplateArray = $WHMPress->get_template_array( "whmpress_domain_price" );
	foreach ( $TemplateArray as $custom_field ) {
		$vars[ $custom_field ] = isset( $atts[ $custom_field ] ) ? $atts[ $custom_field ] : "";
	}
	
	$OutputString = whmp_smarty_template( $html_template, $vars );
} else {
	if ( $no_wrapper == "1" ) {
		$OutputString = $prefix_symbol . $price . $suffix_symbol . $duration;
	} else {
		$OutputString = "<span class='$html_class' id='$html_id'>";
		$OutputString .= $prefix_symbol . $price . $suffix_symbol . $duration;
		$OutputString .= "</span>";
	}
}
return $OutputString;