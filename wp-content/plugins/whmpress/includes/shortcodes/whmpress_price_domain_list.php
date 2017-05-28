<?php
/**
 * Displays customized price list only for domain
 *
 * List of parameters
 * table_id = HTML id for HTML table.
 * html_id = HTML id for wrapper div of table.
 * html_class = HTML class for wrapper div of table.
 * currency = Set currency for prices, Leave this parameter for default currency.
 * show_tlds = provide comma seperated tlds e.g. .com,.net,.org or leave it blank for all tlds
 * show_tlds_wildcard = provide tld search as wildcard, e.g. pk for all .pk domains or co for all com and .co domains
 * decimals = Decimals for price, default 2
 * cols = Number of columns for result in, default 1
 * show_renewel = Show domain renewal price, Yes or No
 * show_transfer = Show domain transfer price, Yes or No
 * titles = Comma separated titles for column titles
 * show_disabled = Show disabled domains yes or no.. (Disabled in WHMCS)
 */

$atts = shortcode_atts( [
	'html_template'      => '',
	'table_id'           => 'prices_table2',
	'html_id'            => '',
	'html_class'         => 'whmpress_domain_price_list whmpress simple-01',
	'currency'           => '',
	'show_tlds'          => '',
	'show_tlds_wildcard' => '',
	'decimals'           => whmpress_get_option( 'pmd_decimals' ),
	//'cols' => '1',
	'show_renewel'       => whmpress_get_option( 'pmd_show_renewel' ),
	'show_transfer'      => whmpress_get_option( 'pmd_show_transfer' ),
	'titles'             => '',
	"show_disabled"      => whmpress_get_option( 'pmd_show_disabled' ),
	'pricing_slab'       => "0",
	'combine_extension'  => '',                          # Price, PriceCC
	'data_table'         => '0',
	'num_of_rows'        => whmpress_get_option( 'pmd_num_of_rows' ),
	'replace_empty'      => '-',
	//'replace_zero' => '0'
], $atts );
extract( $atts );

$currency = whmp_get_currency( $currency );

$combine_extension = strtolower( trim( $combine_extension ) );
if ( $table_id == "" ) {
	$table_id = uniqid();
}

# Getting WordPress DB object
global $wpdb;

# Getting symbol type
$symbol_type = strtolower( whmpress_get_option( 'default_currency_symbol' ) );

# Getting data from database
$c = "";

if ( $num_of_rows == "Default" ) {
	$num_of_rows = whmpress_get_option( 'pmd_num_of_rows' );
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

$Q = "SELECT ";
if ( $combine_extension == "price" || $combine_extension == "pricecc" ) {
	$Q .= "GROUP_CONCAT(`extension` SEPARATOR ', ') extension";
} else {
	$Q .= "`extension`";
}
$Q .= ", `msetupfee`, `qsetupfee` FROM `" . whmp_get_pricing_table_name() . "` pt, `" . whmp_get_domain_pricing_table_name() . "` dpt WHERE dpt.id=`relid` AND
    `type`='[[[type]]]' AND `currency`=" . $currency;
if ( strtolower( $show_disabled ) == "no" || $show_disabled == "0" || $show_disabled == false ) {
	$Q .= " AND (`msetupfee`>0 OR `qsetupfee`>0)";
}

if ( whmp_is_table_exists( whmp_get_clientgroups_table_name() ) && ! empty( $pricing_slab ) ) {
	$Q .= " AND `tsetupfee` IN (SELECT `id` tsetupfee FROM `" . whmp_get_clientgroups_table_name() . "` WHERE id='$pricing_slab' OR `groupname`='$pricing_slab')";
} else {
	$Q .= " AND `tsetupfee`='0'";
}

if ( trim( $show_tlds ) <> "" ) {
	$show_tlds = explode( ",", $show_tlds );
	$show_tlds = "'" . implode( "','", $show_tlds ) . "'";
	$Q .= " AND `extension` IN (" . $show_tlds . ")";
} else if ( trim( $show_tlds_wildcard ) <> "" ) {
	$Q .= " AND `extension` LIKE '%" . $show_tlds_wildcard . "%'";
}

if ( $combine_extension == "pricecc" ) {
	$Q .= " AND `extension` IN (SELECT `extension` FROM `" . whmp_get_domain_pricing_table_name() . "` WHERE LENGTH(`extension`)-LENGTH(REPLACE(`extension`,'.',''))='2')";
}

if ( $combine_extension == "price" || $combine_extension == "pricecc" ) {
	$Q .= " GROUP BY `msetupfee`, `qsetupfee`";
}

$Q .= " ORDER BY `order`";

$type  = "domainregister";
$rows  = $wpdb->get_results( str_replace( "[[[type]]]", $type, $Q ), ARRAY_A );
$type  = "domainrenew";
$rows2 = $wpdb->get_results( str_replace( "[[[type]]]", $type, $Q ), ARRAY_A );
$type  = "domaintransfer";
$rows3 = $wpdb->get_results( str_replace( "[[[type]]]", $type, $Q ), ARRAY_A );

# Generating output string
$str = "";

if ( $data_table == '1' || strtolower( $data_table ) == 'yes' ) {
	$str .= "\n<script>
        jQuery(function(){
            jQuery('table#{$table_id}').DataTable({
                \"iDisplayLength\": $num_of_rows
            });
        });
        </script>\n";
}

$str .= "<ul";
$str .= " id=\"" . $table_id . "\"";
$str .= ">\n";

# Getting decimal seperator from settings
$decimal_sperator = get_option( 'decimal_replacement', "." );

$smarty_array = [];

foreach ( $rows as $index => $domain ) {
	if ( $domain['msetupfee'] == "-1" && $domain['qsetupfee'] > 0 ) {
		$field = "qsetupfee";
	} elseif ( $domain['msetupfee'] > 0 ) {
		$field = "msetupfee";
	} else {
		$field = "";
	}
	
	$data = [];
	
	$data['domain'] = $domain['extension'];
	$str .= "
        <span class=\"domain_tld\">
				<span class=\"domain_tld_title price_title\">TLD Text:</span>
            <span class=\"domain_tld_value price_value\">{$domain['extension']}</span>
        </span>";
	
	if ( $field == "" ) {
		$v = $replace_empty;
	} else {
		if ( $domain[ $field ] == "-1" ) {
			$v = $replace_empty;
		} else {
			$decimals = (int) $decimals;
			$v        = str_replace( ".", $decimal_sperator, number_format( $domain[ $field ], $decimals ) );
		}
	}
	
	if ( $field == "qsetupfee" ) {
		$_years = 2;
	} elseif ( $field == "msetupfee" ) {
		$_years = 1;
	} else {
		$_years = $replace_empty;
	}
	if ( $v <> $replace_empty ) {
		$v = whmpress_domain_price_function(
			[
				"years"         => $_years,
				"tld"           => $domain['extension'],
				"html_class"    => "",
				"html_id"       => "",
				"prefix"        => "no",
				"suffix"        => "no",
				"show_duration" => "no",
				"type"          => "domainregister",
				"no_wrapper"    => "1",
				"decimals"      => $decimals
			]
		);
		
		if ( $symbol_type == "prefix" ) {
			$v = whmp_get_currency_prefix( $currency ) . $v;
		} elseif ( $symbol_type == "suffix" ) {
			$v = $v . whmp_get_currency_suffix( $currency );
		} elseif ( $symbol_type == "code" ) {
			$v = whmp_get_currency_code( $currency ) . " $v";
		}
	}
	if ( ! isset( $titles[1] ) ) {
		$titles[1] = "";
	}
	if ( ! isset( $FixTitles[1] ) ) {
		$FixTitles[1] = "";
	}
	if ( $titles[1] <> "" ) {
		$title = $titles[1];
	} else {
		$title = $FixTitles[1];
	}
	
	$data['register'] = $v;
	//$str .= "<td data-content=\"{$title}\">$v</td>";
	$str .= "
    $v
    <span class=\"registration_price\">
        <span class=\"registration_price_title price_title\">Registration Price:</span>
        <span class=\"registration_price_value price_value\">
            <span class=\"price_unit\">$</span>
            <span class=\"price_amount\">9</span>
            <span class=\"price_decimal\">.</span>
            <span class=\"price_fraction\">98</span>
        </span>
    </span>
    ";
	
	
	if ( ! isset( $titles[2] ) ) {
		$titles[2] = "";
	}
	if ( ! isset( $FixTitles[2] ) ) {
		$FixTitles[2] = "";
	}
	if ( $titles[2] <> "" ) {
		$title = $titles[2];
	} else {
		$title = $FixTitles[2];
	}
	if ( $field == "qsetupfee" ) {
		$data['years'] = 2;
		$str .= "<td data-content=\"{$title}\">2</td>";
	} elseif ( $field == "msetupfee" ) {
		$data['years'] = 1;
		$str .= "<td data-content=\"{$title}\">1</td>";
	} else {
		$data['years'] = $replace_empty;
		$str .= "<td data-content=\"{$title}\">-</td>";
	}
	
	if ( $show_renewel == "1" || strtolower( $show_renewel ) == "yes" || $show_renewel === true ) {
		if ( $combine_extension == "price" || $combine_extension == "pricecc" ) {
			$v = $replace_empty;
		} else if ( $field == "" ) {
			$v = $replace_empty;
		} else {
			$v = $replace_empty;
			foreach ( $rows2 as $r2 ) {
				if ( $r2["extension"] == $domain["extension"] ) {
					$v = $domain[ $field ];
				}
			}
			if ( $v == "-1.00" || $v == - 1 ) {
				$v = $replace_empty;
			}
		}
		
		if ( $v <> $replace_empty ) {
			$v = whmpress_domain_price_function(
				[
					"years"         => $data["years"],
					"tld"           => $domain['extension'],
					"html_class"    => "",
					"html_id"       => "",
					"prefix"        => "no",
					"suffix"        => "no",
					"show_duration" => "no",
					"type"          => "domainrenew",
					"no_wrapper"    => "1",
					"decimals"      => $decimals
				]
			);
			if ( $symbol_type == "prefix" ) {
				$v = whmp_get_currency_prefix( $currency ) . $v;
			} elseif ( $symbol_type == "suffix" ) {
				$v = $v . whmp_get_currency_suffix( $currency );
			} elseif ( $symbol_type == "code" ) {
				$v = whmp_get_currency_code( $currency ) . " $v";
			}
		}
		if ( ! isset( $titles[3] ) ) {
			$titles[3] = "";
		}
		if ( ! isset( $FixTitles[3] ) ) {
			$FixTitles[3] = "";
		}
		if ( $titles[3] <> "" ) {
			$title = $titles[3];
		} else {
			$title = $FixTitles[3];
		}
		
		$data['renewal'] = $v;
		$str .= "<td data-content=\"{$title}\">$v</td>";
	} else {
		$data['renewal'] = "";
	}
	
	if ( $show_transfer == "1" || strtolower( $show_transfer ) == "yes" || $show_transfer === true ) {
		if ( $combine_extension == "price" || $combine_extension == "pricecc" ) {
			$v = $replace_empty;
		} else if ( $field == "" ) {
			$v = $replace_empty;
		} else {
			$v = $replace_empty;
			foreach ( $rows3 as $r2 ) {
				if ( $r2["extension"] == $domain["extension"] ) {
					$v = $domain[ $field ];
				}
			}
			if ( $v == "-1.00" || $v == - 1 ) {
				$v = $replace_empty;
			}
		}
		
		if ( $v <> $replace_empty ) {
			$v = whmpress_domain_price_function(
				[
					"years"         => $data["years"],
					"tld"           => $domain['extension'],
					"html_class"    => "",
					"html_id"       => "",
					"prefix"        => "no",
					"suffix"        => "no",
					"show_duration" => "no",
					"type"          => "domaintransfer",
					"no_wrapper"    => "1",
					"decimals"      => $decimals
				]
			);
			
			if ( $symbol_type == "prefix" ) {
				$v = whmp_get_currency_prefix( $currency ) . $v;
			} elseif ( $symbol_type == "suffix" ) {
				$v = $v . whmp_get_currency_suffix( $currency );
			} elseif ( $symbol_type == "code" ) {
				$v = whmp_get_currency_code( $currency ) . " $v";
			}
		}
		if ( ! isset( $titles[4] ) ) {
			$titles[4] = "";
		}
		if ( ! isset( $FixTitles[4] ) ) {
			$FixTitles[4] = "";
		}
		if ( $titles[4] <> "" ) {
			$title = $titles[4];
		} else {
			$title = $FixTitles[4];
		}
		
		$data['transfer'] = $v;
		$str .= "<td data-content=\"{$title}\">$v</td>";
	} else {
		$data['transfer'] = "";
	}
	//if (($index+1) % $cols==0) $str .= "</tr>\n<tr>";
	$str .= "</tr>\n<tr>";
	$smarty_array[] = $data;
}

# Removing extra <tr> at the end of string.
$str .= "</ul>";

global $WHMPress;
if ( ! $WHMPress ) {
	$WHMPress = new WHMPress();
}

$html_template = $WHMPress->check_template_file( $html_template, "whmpress_price_domain_list" );

if ( is_file( $html_template ) ) {
	$OutputString = $WHMPress->read_local_file( $html_template );
	
	$vars = [
		"params"            => $atts,
		"price_domain_list" => $str,
		"data"              => $smarty_array,
	];
	
	# Getting custom fields and adding in output
	$TemplateArray = $WHMPress->get_template_array( "whmpress_price_matrix_domain" );
	foreach ( $TemplateArray as $custom_field ) {
		$vars[ $custom_field ] = isset( $atts[ $custom_field ] ) ? $atts[ $custom_field ] : "";
	}
	
	$OutputString = whmp_smarty_template( $html_template, $vars );
	
	return $OutputString;
} else {
	# Returning output string including output wrapper div.
	$ID    = ! empty( $html_id ) ? "id='$html_id'" : "";
	$CLASS = ! empty( $html_class ) ? "class='$html_class'" : "";
	
	return "<div $ID $CLASS>" . $str . "</div>";
}