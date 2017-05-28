<?php

// Just disply an array or object in human friendly manners
function whmp_show_array( $ar, $show = false ) {
	echo "<pre>";
	print_r( $ar, $show );
	echo "</pre>";
}

function whmp_get_domain_extension( $domain ) {
	$domain = str_replace( "\n", "", $domain );
	$domain = str_replace( chr( 10 ), "", $domain );
	
	return trim( ltrim( strstr( $domain, '.' ), "." ) );
}

function whmp_get_announcements_table_name() {
	global $wpdb;
	
	return $wpdb->prefix . "whmpress_announcements";
}

function whmp_get_productconfigoptions_table_name() {
	global $wpdb;
	
	return $wpdb->prefix . "whmpress_productconfigoptions";
}

function whmp_get_productconfiglinks_table_name() {
	global $wpdb;
	
	return $wpdb->prefix . "whmpress_productconfiglinks";
}

function whmp_get_productconfigoptionssub_table_name() {
	global $wpdb;
	
	return $wpdb->prefix . "whmpress_productconfigoptionssub";
}

function whmp_get_clientgroups_table_name() {
	global $wpdb;
	
	return $wpdb->prefix . "whmpress_clientgroups";
}

function whmp_is_table_exists( $table_name ) {
	global $wpdb;
	
	return ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) == $table_name );
}

function get_mysql_table_name( $table = "" ) {
	$table = strtolower( trim( $table ) );
	if ( substr( $table, 0, 3 ) == "tbl" ) {
		$table = substr( $table, 3 );
	}
	global $wpdb;
	
	return $wpdb->prefix . "whmpress_" . $table;
}

function whmp_get_tax_table_name() {
	global $wpdb;
	
	return $wpdb->prefix . "whmpress_tax";
}

function whmp_get_pricing_table_name() {
	global $wpdb;
	
	return $wpdb->prefix . "whmpress_pricing";
}

function whmp_get_domainpricing_table_name() {
	global $wpdb;
	
	return $wpdb->prefix . "whmpress_domainpricing";
}

function whmp_get_domain_pricing_table_name() {
	global $wpdb;
	
	return $wpdb->prefix . "whmpress_domainpricing";
}

function whmp_get_products_table_name() {
	global $wpdb;
	
	return $wpdb->prefix . "whmpress_products";
}

function whmp_get_productgroups_table_name() {
	global $wpdb;
	
	return $wpdb->prefix . "whmpress_productgroups";
}

function whmp_get_product_group_table_name() {
	global $wpdb;
	
	return $wpdb->prefix . "whmpress_productgroups";
}

function whmp_get_countries_table_name() {
	global $wpdb;
	
	return $wpdb->prefix . "whmpress_countries";
}

function whmp_get_currencies_table_name() {
	global $wpdb;
	
	return $wpdb->prefix . "whmpress_currencies";
}

function whmp_get_configuration_table_name() {
	global $wpdb;
	
	return $wpdb->prefix . "whmpress_configuration";
}

function whmp_get_group_table_name() {
	global $wpdb;
	
	return $wpdb->prefix . "whmpress_groups";
}

function whmp_get_group_detail_table_name() {
	global $wpdb;
	
	return $wpdb->prefix . "whmpress_groups_details";
}

function whmp_get_logs_table_name() {
	global $wpdb;
	
	return $wpdb->prefix . "whmpress_search_logs";
}

function whmp_get_default_currency_code() {
	$WHMPress = new WHMpress();
	if ( ! $WHMPress->WHMpress_synced() ) {
		return '';
	}
	
	$currency = get_option( "whmpress_default_currency" );
	
	global $wpdb;
	if ( ! empty( $currency ) ) {
		$Q = "SELECT `code` FROM `" . whmp_get_currencies_table_name() . "` WHERE `id`='$currency'";
	} else {
		$Q = "SELECT `code` FROM `" . whmp_get_currencies_table_name() . "` WHERE `default`='1'";
	}
	
	return $wpdb->get_var( $Q );
}

if ( ! function_exists( 'whmp_get_default_currency_id' ) ) {
	function whmp_get_default_currency_id() {
		global $WHMPress;
		if ( ! $WHMPress ) {
			$WHMPress = new WHMpress();
		}
		if ( ! $WHMPress->WHMpress_synced() ) {
			return '';
		}
		
		$currency = get_option( "whmpress_default_currency" );
		if ( ! empty( $currency ) && is_numeric( $currency ) ) {
			return $currency;
		}
		
		global $wpdb;
		$Q = "SELECT `id` FROM `" . whmp_get_currencies_table_name() . "` WHERE `default`='1'";
		
		return $wpdb->get_var( $Q );
	}
}

function whmp_get_default_currency_prefix() {
	$WHMPress = new WHMpress();
	if ( ! $WHMPress->WHMpress_synced() ) {
		return '';
	}
	
	$currency = get_option( "whmpress_default_currency" );
	
	global $wpdb;
	if ( ! empty( $currency ) ) {
		$Q = "SELECT `prefix` FROM `" . whmp_get_currencies_table_name() . "` WHERE `id`='$currency'";
	} else {
		$Q = "SELECT `prefix` FROM `" . whmp_get_currencies_table_name() . "` WHERE `default`='1'";
	}
	
	return $wpdb->get_var( $Q );
}

function whmp_get_default_currency_suffix() {
	$WHMPress = new WHMpress();
	if ( ! $WHMPress->WHMpress_synced() ) {
		return '';
	}
	
	$currency = get_option( "whmpress_default_currency" );
	
	global $wpdb;
	if ( ! empty( $currency ) ) {
		$Q = "SELECT `suffix` FROM `" . whmp_get_currencies_table_name() . "` WHERE `id`='$currency'";
	} else {
		$Q = "SELECT `suffix` FROM `" . whmp_get_currencies_table_name() . "` WHERE `default`='1'";
	}
	
	return $wpdb->get_var( $Q );
}

if ( ! function_exists( 'whmp_get_currency' ) ) {
	function whmp_get_currency_code( $id = "" ) {
		$WHMPress = new WHMpress();
		if ( ! $WHMPress->WHMpress_synced() ) {
			return '';
		}
		if ( $id == "" ) {
			return whmp_get_default_currency_code();
		}
		global $wpdb;
		$Q             = "SELECT `code` FROM `" . whmp_get_currencies_table_name() . "` WHERE `id`='$id'";
		$currency_code = $wpdb->get_var( $Q );
		if ( empty( $currency_code ) ) {
			$currency_code = whmp_get_default_currency_code();
		}
		$WHMP  = new WHMPress();
		$alter = get_option( "whmpress_currencies_" . trim( $currency_code ) . "_code_" . $WHMP->get_current_language() );
		if ( empty( $alter ) ) {
			return $currency_code;
		} else {
			return $alter;
		}
	}
}

function whmp_get_currency_prefix( $id = "" ) {
	$WHMPress = new WHMpress();
	if ( ! $WHMPress->WHMpress_synced() ) {
		return '';
	}
	if ( $id == "" ) {
		$currency_prefix = whmp_get_default_currency_prefix();
	} else {
		global $wpdb;
		$Q               = "SELECT `prefix` FROM `" . whmp_get_currencies_table_name() . "` WHERE `id`='$id'";
		$currency_prefix = $wpdb->get_var( $Q );
		if ( empty( $currency_prefix ) ) {
			$currency_prefix = whmp_get_default_currency_prefix();
		}
	}
	
	$alter = get_option( "whmpress_currencies_" . trim( $currency_prefix ) . "_prefix_" . $WHMPress->get_current_language() );
	if ( empty( $alter ) ) {
		return $currency_prefix;
	} else {
		return $alter;
	}
}

function whmp_get_currency_suffix( $id = "" ) {
	$WHMPress = new WHMpress();
	if ( ! $WHMPress->WHMpress_synced() ) {
		return '';
	}
	if ( $id == "" ) {
		$currency_suffix = whmp_get_default_currency_suffix();;
	} else {
		global $wpdb;
		$Q               = "SELECT `suffix` FROM `" . whmp_get_currencies_table_name() . "` WHERE `id`='$id'";
		$currency_suffix = $wpdb->get_var( $Q );
		if ( $currency_suffix == "" ) {
			$currency_suffix = whmp_get_default_currency_suffix();
		}
	}
	
	$alter = get_option( "whmpress_currencies_" . trim( $currency_suffix ) . "_suffix_" . $WHMPress->get_current_language() );
	if ( empty( $alter ) ) {
		return $currency_suffix;
	} else {
		return $alter;
	}
}

function whmp_get_installation_url() {
	global $wpdb;
	$whmcs_url = esc_attr( get_option( "whmcs_url" ) );
	if ( $whmcs_url == "" ) {
		$Q    = "SELECT `value` FROM `" . whmp_get_configuration_table_name() . "` WHERE `setting`='SystemURL' OR `setting`='SystemSSLURL' ORDER BY `setting`";
		$urls = $wpdb->get_results( $Q );
		foreach ( $urls as $url ) {
			if ( $url->value <> "" ) {
				return rtrim( $url->value, "/" ) . "/";
			}
		}
		
		return "";
	} else {
		return $whmcs_url;
	}
}

if ( ! function_exists( 'whmp_get_currency' ) ) {
	function whmp_get_currency( $curency_id = "0" ) {
		$curency_id = (int) $curency_id;
		if ( empty( $curency_id ) ) {
			if ( ! session_id() ) {
				session_start();
			}
			if ( isset( $_SESSION["currency"] ) && ! empty( $_SESSION["currency"] ) ) {
				return $_SESSION["currency"];
			}
			
			return whmp_get_default_currency_id();
		} else {
			return $curency_id;
		}
	}
}

function whmpress_draw_combo( $dataArray, $selected = "", $name = "" ) {
	$WHMPress = new WHMpress();
	if ( ! $WHMPress->WHMpress_synced() ) {
		return '';
	}
	$OutputString = "<select name='$name'>\n";
	if ( whmpress_is_assoc_array( $dataArray ) ) {
		foreach ( $dataArray as $key => $val ) {
			$S = $key == $selected ? "selected=selected" : "";
			$OutputString .= "<option $S value=\"$key\">{$val}</option>\n";
		}
	} else {
		foreach ( $dataArray as $val ) {
			$S = $val == $selected ? "selected=selected" : "";
			$OutputString .= "<option $S>{$val}</option>\n";
		}
	}
	$OutputString .= "</select>\n";
	
	return $OutputString;
}

function whmpress_draw_combo_multiple( $dataArray, $selected = [], $name ) {
	$WHMPress = new WHMpress();
	if ( ! $WHMPress->WHMpress_synced() ) {
		return '';
	}
	$OutputString = "<select name='{$name}[]' multiple='multiple'>\n";
	if ( ! is_array( $selected ) ) {
		$selected = explode( ",", $selected );
	}
	$selected = array_map( 'trim', $selected );
	if ( whmpress_is_assoc_array( $dataArray ) ) {
		foreach ( $dataArray as $key => $val ) {
			$S = in_array( $key, $selected ) ? "selected=selected" : "";
			$OutputString .= "<option $S value=\"$key\">{$val}</option>\n";
		}
	} else {
		foreach ( $dataArray as $val ) {
			$S = in_array( $key, $selected ) ? "selected=selected" : "";
			$OutputString .= "<option $S>{$val}</option>\n";
		}
	}
	$OutputString .= "</select>\n";
	
	return $OutputString;
}

function whmpress_is_assoc_array( $arr ) {
	return array_keys( $arr ) !== range( 0, count( $arr ) - 1 );
}

function whmpress_get_option( $key_name, $default = "" ) {
	$DefaultValues = [
		"whmp_custom_css"         => "default.css",
		"decimals"                => "0",
		"billingcycle"            => "annually",
		"hide_decimal"            => "No",
		"decimals_tag"            => "",
		"prefix"                  => "",
		"suffix"                  => "",
		"show_duration"           => "Yes",
		"show_duration_as"        => "",
		"duration_type"           => "long",
		"combo_billingcycles"     => "",
		"combo_decimals"          => "0",
		"combo_show_button"       => "Yes",
		//"combo_rows" => "1",
		"combo_button_text"       => "Order Now",
		"combo_show_discount"     => "Yes",
		"combo_discount_type"     => "yearly",
		"combo_prefix"            => "Yes",
		"combo_suffix"            => "No",
		"default_currency_symbol" => "prefix",
		
		"domain_available_message"             => "Domain is available",
		"domain_not_available_message"         => "Domain is not available",
		"domain_recommended_list"              => "Recommended domains list",
		"ongoing_domain_available_message"     => "[domain-name] is available",
		"ongoing_domain_not_available_message" => "[domain-name] is not available",
		"register_domain_button_text"          => "Select",
		"load_more_button_text"                => "Load more",
		
		"curl_timeout_whmp"     => "20",
		"cache_enabled_whmp"    => "0",
		"configureable_options" => "0",
		"price_tax"             => "",
		"price_currency"        => "0",
		"price_type"            => "price",
		"convert_monthly"       => "no",
		"config_option_string"  => "Starting from",
		
		"jquery_source"               => "wordpres",
		
		# Domain Price
		"dp_type"                     => "domainregister",
		"dp_years"                    => "1",
		"dp_decimals"                 => "1",
		"dp_hide_decimal"             => "no",
		"dp_decimals_tag"             => "",
		"dp_prefix"                   => "Yes",
		"dp_suffix"                   => "No",
		"dp_show_duration"            => "Yes",
		"dp_price_tax"                => "",
		
		# Price Matrix
		"pm_decimals"                 => "0",
		"pm_show_hidden"              => "No",
		"pm_replace_zero"             => "x",
		"pm_replace_empty"            => "-",
		//"pm_type" => "product",
		"pm_hide_search"              => "No",
		"pm_search_label"             => "Search:",
		"pm_search_placeholder"       => "Search",
		
		# Price Matrix Domain
		"pmd_decimals"                => "0",
		"pmd_show_renewel"            => "Yes",
		"pmd_show_transfer"           => "Yes",
		"pmd_hide_search"             => "No",
		"pmd_search_label"            => "Search",
		"pmd_search_placeholder"      => "Type Extension to search a domain",
		"pmd_show_disabled"           => "No",
		"pmd_num_of_rows"             => "10",
		
		# Order Button
		"ob_button_text"              => "Order",
		"ob_billingcycle"             => "annually",
		
		# Pricing Table
		"pt_billingcycle"             => "annually",
		"pt_show_price"               => "Yes",
		"pt_show_combo"               => "No",
		"pt_show_button"              => "Yes",
		"pt_button_text"              => "Order",
		
		# Domain Search
		"ds_show_combo"               => "No",
		"ds_placeholder"              => "Search",
		"ds_button_text"              => "Search",
		
		# Domain Search Ajax
		"dsa_placeholder"             => "Search",
		"dsa_button_text"             => "Search",
		"dsa_whois_link"              => "Yes",
		"dsa_www_link"                => "Yes",
		"dsa_transfer_link"           => "Yes",
		"dsa_disable_domain_spinning" => "0",
		"dsa_order_landing_page"      => "0",
		"dsa_show_price"              => "1",
		"dsa_show_years"              => "1",
		"dsa_search_extensions"       => "1",
		
		# Domain Search Ajaz Result
		"dsar_whois_link"             => "Yes",
		"dsar_www_link"               => "Yes",
		"dsar_show_price"             => "1",
		"dsar_show_years"             => "1",
		
		# Domain Search Bulk
		"dsb_placeholder"             => "",
		"dsb_button_text"             => "Search",
		
		# Domain WhoIS
		"dw_placeholder"              => "",
		"dw_button_text"              => "Get WhoIs",
		
		# Order Link
		"ol_link_text"                => "Link Text",
		
		# Description
		"dsc_description"             => "ul",
		
		"whmp_follow_lang" => "yes",
	];
	
	if ( $default == "" ) {
		if ( isset( $DefaultValues[ $key_name ] ) ) {
			$default = $DefaultValues[ $key_name ];
		}
	}
	
	$old_key_name = $key_name;
	$key_name     = whmpress_process_key_name( $key_name );
	
	$value = get_option( $key_name, __( $default, "whmpress" ) );
	if ( $value == "" ) {
		if ( isset( $DefaultValues[ $old_key_name ] ) ) {
			$value = $DefaultValues[ $old_key_name ];
		}
	}
	
	if ( $key_name == "whois_db" && trim( $value ) == "" ) {
		$value = whmp_read_local_file( WHMP_PLUGIN_DIR . "/includes/whoisdb" );
	}
	
	if ( is_array( $value ) ) {
		return array_map( 'trim', $value );
	} else {
		return trim( $value );
	}
}

function whmpress_process_key_name( $key_name ) {
	global $WHMP_Settings;
	$WHMP   = new WHMPress();
	$lang   = $WHMP->get_current_language();
	$extend = empty( $lang ) ? "" : "_" . $lang;
	
	if ( in_array( $key_name, $WHMP_Settings ) ) {
		$key_name .= $extend;
	}
	
	return $key_name;
}

/**
 * @param array $data
 * @param bool $show_full_result
 *
 * @return string
 *
 * Sync data from WHMCS into WHMPress
 */
function whmp_fetch_data( $data = [], $show_full_result = true ) {
	// Connecting to WHMCS db    for fetching data.
	if ( ! isset( $data["db_server"] ) ) {
		$data["db_server"] = get_option( "db_server" );
	}
	if ( ! isset( $data["db_user"] ) ) {
		$data["db_user"] = get_option( "db_user" );
	}
	if ( ! isset( $data["db_pass"] ) ) {
		$data["db_pass"] = get_option( "db_pass" );
	}
	if ( ! isset( $data["db_name"] ) ) {
		$data["db_name"] = get_option( "db_name" );
	}
	
	if ( get_option( "whmp_save_pwd" ) <> "1" ) {
		update_option( "db_pass", "" );
	}
	
	if ( ! function_exists( 'mysqli_connect' ) ) {
		return "<div class='error'><p style='color:#ff0000;font-weight:bold'>MySQLi not installed on your server, WHMpress required MySQLi</p></div>";
	}
	$conn = new mysqli( $data["db_server"], $data["db_user"], $data["db_pass"], $data["db_name"] );
	
	if ( $conn->connect_error ) {
		if ( $show_full_result ) {
			return "<h1>Unable to connect with WHMCS server: " . $conn->connect_error . "</h1>";
		} else {
			return "Unable to connect with WHMCS server: \n" . $conn->connect_error;
		}
	}
	
	if ( ! $conn->set_charset( "utf8" ) ) {
		return "<div class='error'><p style='color:#ff0000;font-weight:bold'>Error loading character set utf8: " . $conn->error . "</p></div>";
	}
	
	// Getting list of WHMPress decided tables
	global $Tables;
	
	global $wpdb;
	$Out             = "";
	$charset_collate = $wpdb->get_charset_collate();
	foreach ( $Tables as $table => $newTable ) {
		/**
		 * Check if MySQL table exists
		 * Added in 2.4.2
		 */
		$Q        = "SELECT * FROM information_schema.tables WHERE table_schema = '{$data["db_name"]}' AND table_name = '{$table}' LIMIT 1;";
		$is_table = $conn->query( $Q );
		if ( $is_table->num_rows == "0" ) {
			$Out .= "<span style='color:#CC0000;'>Table <b>$table</b> doesn't exists in database <b>{$data["db_name"]}</b>. Please ask your administrator.</span><br />";
		} else {
			$newTableName = $wpdb->prefix . "whmpress_" . $newTable;
			
			$Q      = "SHOW CREATE TABLE `" . $table . "`";
			$result = $conn->query( $Q );
			if ( ! $result ) {
				if ( $show_full_result ) {
					return "<h1>Can't get data from table " . $table . "</h1>";
				} else {
					return "Can't get data from table " . $table;
				}
			}
			
			$row       = $result->fetch_assoc();
			$newTableQ = $row["Create Table"];
			$newTableQ = substr( $newTableQ, 0, strrpos( $newTableQ, ")" ) + 2 );
			$newTableQ .= $charset_collate;
			
			$result = $conn->query( "SELECT * FROM `$table`" );
			
			$newTableQ = str_replace( "`$table`", "`" . $newTableName . "`", $newTableQ );
			
			$Q = "DROP TABLE IF EXISTS `$newTableName`";
			$wpdb->query( $Q );
			$wpdb->query( $newTableQ );
			$wpdb->query( "TRUNCATE `$newTableName`" );
			$s = 0;
			$f = 0;
			while ( $row = $result->fetch_assoc() ) {
				$response = $wpdb->insert( $newTableName, $row );
				if ( $response === false ) {
					$f ++;
				} else {
					$s ++;
				}
			}
			$Out .= "<b>Caching $newTable:</b> <i>Successfully cached:</i> $s, <i>Failed:</i> $f<br />";
		}
	}
	update_option( 'sync_time', date( "F, d Y - H:i" ) );
	if ( $show_full_result ) {
		return $Out;
	} else {
		update_option( 'sync_time', date( "F, d Y - H:i" ) );
		
		return "OK";
	}
}


/**
 * Check whether file editing is allowed for the .htaccess and robots.txt files
 *
 * @internal current_user_can() checks internally whether a user is on wp-ms and adjusts accordingly.
 *
 * @return bool
 */
function whmp_allow_system_file_edit() {
	$allowed = true;
	
	if ( current_user_can( 'edit_files' ) === false ) {
		$allowed = false;
	}
	
	/**
	 * Filter: 'whmp_allow_system_file_edit' - Allow developers to change whether the editing of
	 * .htaccess and robots.txt is allowed
	 *
	 * @api bool $allowed Whether file editing is allowed
	 */
	
	return apply_filters( 'whmp_allow_system_file_edit', $allowed );
}

/**
 * Check if string is a valid utf8 or not
 *
 */
if ( ! function_exists( 'is_utf8' ) ) {
	function is_utf8( $string ) {
		return ( mb_detect_encoding( $string, 'UTF-8', true ) == 'UTF-8' );
	}
}

function whmp_get_service_types() {
	$WHMPress = new WHMpress();
	if ( ! $WHMPress->WHMpress_synced() ) {
		return [];
	}
	global $wpdb;
	$Q            = "SELECT DISTINCT `type` FROM `" . whmp_get_products_table_name() . "` WHERE `type`<>''";
	$rows         = $wpdb->get_results( $Q, ARRAY_A );
	$realNames    = [
		"hostingaccount",
		"reselleraccount",
		"server",
		"other",
	];
	$changedNames = [
		"Hosting Plans",
		"Reseller Plans",
		"VPS/Servers",
		"Other",
	];
	$Out          = [];
	foreach ( $rows as $row ) {
		$Out[ $row["type"] ] = str_replace( $realNames, $changedNames, $row["type"] );
	}
	
	return $Out;
}

function whmp_get_type_groups( $type ) {
	$Q = "SELECT DISTINCT grps.`id`,grps.`name`,grps.`hidden` FROM `" . whmp_get_product_group_table_name() . "` grps, `" . whmp_get_products_table_name() . "` prds WHERE 
    prds.type='$type' AND prds.gid=grps.id ORDER BY grps.`order`";
	global $wpdb;
	
	return $wpdb->get_results( $Q, ARRAY_A );
}

function whmp_get_products_by_group( $group ) {
	$WHMPress = new WHMpress();
	if ( ! $WHMPress->WHMpress_synced() ) {
		return [];
	}
	$Q = "SELECT * FROM `" . whmp_get_products_table_name() . "` WHERE 1";
	if ( is_numeric( $group ) ) {
		$group = (int) $group;
		$Q .= " AND `gid`='$group'";
	} else {
		$Q .= " AND `gid` IN (SELECT `id` gid FROM `" . whmp_get_product_group_table_name() . "` WHERE `name`='$group')";
	}
	global $wpdb;
	
	return $wpdb->get_results( $Q, ARRAY_A );
}

function whmp_get_domain_extension_price( $ext, $currency = "" ) {
	global $wpdb;
	$ext = "." . ltrim( $ext, "." );
	if ( $currency == "" ) {
		$currency = whmp_get_currency_code();
	}
	$Q = "SELECT d.id, d.extension 'tld', t.type, c.code, c.suffix, c.prefix, t.msetupfee, t.qsetupfee
    FROM `" . whmp_get_domain_pricing_table_name() . "` AS d
    INNER JOIN `" . whmp_get_pricing_table_name() . "` AS t ON t.relid = d.id
    INNER JOIN `" . whmp_get_currencies_table_name() . "` AS c ON c.id = t.currency
    WHERE t.type
    IN (
    'domainregister'
    ) AND d.extension IN ('{$ext}')
    AND c.code='$currency' 
    ORDER BY d.id ASC 
    LIMIT 0 , 30";
	
	return $wpdb->get_row( $Q, ARRAY_A );
}

function whmp_get_products( $add = false ) {
	$WHMPress = new WHMpress();
	if ( ! $WHMPress->WHMpress_synced() ) {
		return [];
	}
	global $wpdb;
	#$Q = "SELECT `id`, `name`, `type` FROM `".whmp_get_products_table_name()."` WHERE `type`<>'' ORDER BY `type`";
	#$rows = $wpdb->get_results($Q,ARRAY_A);
	
	$services = whmp_get_service_types();
	$groups   = $wpdb->get_results( "SELECT `id`,`name`,`hidden` FROM `" . whmp_get_product_group_table_name() . "` ORDER BY `order`", ARRAY_A );
	$Out      = [];
	foreach ( $services as $key => $service ) {
		foreach ( $groups as $group ) {
			$rows = $wpdb->get_results( "SELECT `id`, `name`,`description`,`hidden` FROM `" . whmp_get_products_table_name() . "` WHERE `gid`='{$group["id"]}' AND `type`='{$key}' ORDER BY `name`" );
			foreach ( $rows as $row ) {
				$Out[ $key . " >> " . whmpress_encoding( $row->name ) . " (" . $row->id . ")" ] = $row->id;
			}
		}
	}
	
	return $Out;
}

function whmp_get_slabs( $add = false ) {
	$Out      = [ "Default" => "0" ];
	$WHMPress = new WHMpress();
	if ( ! $WHMPress->WHMpress_synced() ) {
		return $Out;
	}
	if ( ! whmp_is_table_exists( whmp_get_clientgroups_table_name() ) ) {
		return $Out;
	}
	global $wpdb;
	$Q    = "SELECT `id`, `groupname` FROM `" . whmp_get_clientgroups_table_name() . "` ORDER BY `groupname`";
	$rows = $wpdb->get_results( $Q );
	
	foreach ( $rows as $row ) {
		$Out[ $row->groupname ] = $row->id;
	}
	
	return $Out;
}

function whmp_smarty_template( $filename, $vars ) {
	if ( ! class_exists( 'Smarty' ) ) {
		require_once WHMP_PLUGIN_PATH . "/includes/smarty/libs/Smarty.class.php";
	}
	$smarty = new Smarty();
	$smarty->setTemplateDir( dirname( $filename ) );
	$smarty->setCompileDir( WHMP_PLUGIN_PATH . '/includes/smarty/data/templates_c/' );
	$smarty->setCacheDir( WHMP_PLUGIN_PATH . '/includes/smarty/data/cache/' );
	$smarty->setConfigDir( WHMP_PLUGIN_PATH . '/includes/smarty/data/configs/' );;
	
	#$smarty->left_delimiter = "{{";
	#$smarty->right_delimiter = "}}";
	
	foreach ( $vars as $key => $val ) {
		if ( substr( $key, - 6 ) == "_image" && is_numeric( $val ) ) {
			$img = wp_get_attachment_image_src( $val );
			$val = $img[0];
			
		}
		$smarty->assign( $key, $val );
	}
	
	#$smarty->debugging = true;
	return $smarty->fetch( basename( $filename ) );
}

function process_price( $price, $decimal_sperator = "." ) {
	$price           = strip_tags( $price );
	$totay           = explode( $decimal_sperator, $price );
	$out["amount"]   = $totay[0];
	$out["fraction"] = isset( $totay[1] ) ? $totay[1] : "";
	
	return $out;
}

function whmp_read_local_file( $filepath ) {
	if ( ! is_file( $filepath ) ) {
		return false;
	}
	global $wp_filesystem;
	if ( empty( $wp_filesystem ) ) {
		require_once( ABSPATH . '/wp-admin/includes/file.php' );
		WP_Filesystem();
	}
	
	$content = $wp_filesystem->get_contents( $filepath );
	if ( empty( $content ) ) {
		$content = file_get_contents( $filepath );
	}
	
	return $content;
}

function whmpress_calculate_tax( $price ) {
	global $wpdb;
	$tax_amount    = $base_price = 0;
	$TaxType       = $wpdb->get_var( "SELECT `value` FROM " . whmp_get_configuration_table_name() . " WHERE `setting`='TaxType'" );
	$TaxL2Compound = $wpdb->get_var( "SELECT `value` FROM " . whmp_get_configuration_table_name() . " WHERE `setting`='TaxL2Compound'" );
	
	$level1_rate = $wpdb->get_var( "SELECT `taxrate` FROM `" . whmp_get_tax_table_name() . "` WHERE `level`='1' AND `country`='' ORDER BY `id`" );
	$level2_rate = $wpdb->get_var( "SELECT `taxrate` FROM `" . whmp_get_tax_table_name() . "` WHERE `level`='2' AND `country`='' ORDER BY `id`" );
	
	if ( $TaxType == "Exclusive" ) {
		$tax_amount = $price * ( $level1_rate / 100 );
		$base_price = $price;
	} elseif ( $TaxType == "Inclusive" ) {
		$tax_amount = ( $price / ( 100 + $level1_rate ) ) * $level1_rate;
		$base_price = $price - $tax_amount;
	}
	if ( ! empty( $level2_rate ) ) {
		if ( strtolower( $TaxL2Compound ) == "on" ) {
			$price2 = $tax_amount + $base_price;
		} else {
			$price2 = $base_price;
		}
		
		$tax2_amount = 0;
		if ( $TaxType == "Exclusive" ) {
			$tax2_amount = $price2 * ( $level2_rate / 100 );
		} elseif ( $TaxType == "Inclusive" ) {
			$tax2_amount = ( $price2 / ( 100 + $level2_rate ) ) * $level2_rate;
		}
		$tax_amount += $tax2_amount;
	}
	if ( $TaxType == "Inclusive" ) {
		$base_price = $price - $tax_amount;
	}
	
	return [ "original_price" => $price, "tax_amount" => $tax_amount, "base_price" => $base_price ];
}

function whmpress_encoding( $string ) {
	if ( whmpress_get_option( 'whmpress_utf_encode_decode' ) == "utf_encode" ) {
		return utf8_encode( $string );
	} elseif ( whmpress_get_option( 'whmpress_utf_encode_decode' ) == "utf_decode" ) {
		return utf8_decode( $string );
	} else {
		return $string;
	}
	
	/*if (preg_match('!!u', $string)) {
		return utf8_decode($string);
	} else {
		return $string;
	}*/
	
	/*if (!function_exists("mb_check_encoding")) return $string;

	if (mb_check_encoding($string, mb_internal_encoding())) return utf8_decode($string);
	return $string;*/
}

function whmpress_json_encode( $arr ) {
	//convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
	array_walk_recursive( $arr, function ( &$item, $key ) {
		if ( is_string( $item ) ) {
			//$item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
			$item = utf8_encode( $item );
		}
	} );
	
	//return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
	return json_encode( $arr );
}

if ( ! function_exists( 'count_folders' ) ) {
	function count_folders( $path ) {
		$path = rtrim( $path, "/" );
		
		return count( glob( "$path/*", GLOB_ONLYDIR ) );
	}
}

if ( ! function_exists( 'show_array' ) ) {
	function show_array( $ar ) {
		if ( is_array( $ar ) || is_object( $ar ) ) {
			echo "<pre>";
			print_r( $ar );
			echo "</pre>";
		} elseif ( is_bool( $ar ) ) {
			if ( $ar ) {
				return "TRUE";
			} else {
				return "FALSE";
			}
		} else {
			print_r( $ar );
		}
	}
}