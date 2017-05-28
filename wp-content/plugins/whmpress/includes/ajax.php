<?php
global $wpdb;

global $WHMPress;
if ( ! $WHMPress ) {
	$WHMPress = new WHMpress();
}

if ( isset( $_GET["set_default_currency"] ) ) {
	$Wp_load = realpath( dirname( __FILE__ ) . '/../../../../wp-load.php' );
	if ( ! is_file( $Wp_load ) ) {
		die( "WordPress library not found." );
	}
	require_once( $Wp_load );
	
	$new_curr = isset( $_POST["new_curr"] ) ? $_POST["new_curr"] : "";
	update_option( "whmpress_default_currency", $new_curr );
	echo "OK";
	exit;
}

if ( isset( $_GET["setCurrency"] ) ) {
	$Wp_load = realpath( dirname( __FILE__ ) . '/../../../../wp-load.php' );
	if ( ! is_file( $Wp_load ) ) {
		die( "WordPress library not found." );
	}
	if ( ! session_id() ) {
		session_start();
	}
	$_SESSION["currency"] = $_POST["curency"];
	echo "OK";
	exit;
}

if ( isset( $_REQUEST["show_price"] ) ) {
	$show_price = $_REQUEST["show_price"];
} else {
	$show_price = "1";
}
if ( isset( $_REQUEST["show_years"] ) ) {
	$show_years = $_REQUEST["show_years"];
} else {
	$show_years = "1";
}
if ( isset( $_REQUEST["enable_transfer_link"] ) ) {
	$enable_transfer_link = $_REQUEST["enable_transfer_link"];
} else {
	$enable_transfer_link = "No";
}

switch ( $_POST["do"] ) {
	case "getDomainData":
		if ( ! isset( $_POST["domain"] ) && isset( $_POST["search_domain"] ) ) {
			$_POST["domain"] = $_POST["search_domain"];
		}
		
		if ( @$_POST["domain"] == "" ) {
			echo "<div class='whmp-domain-required'>" . __( "Domain name required.", "whmpress" ) . "</div>";
			exit;
		}
		if ( ! $WHMPress->is_valid_domain_name( @$_POST["domain"] ) ) {
			echo "<div class='whmp-not-valid-name'>" . __( "Domain <span>" . $_POST["domain"] . "</span> is not a valid domain name.", "whmpress" ) . "</div>";
			exit;
		}
		
		include_once( WHMP_PLUGIN_DIR . "/includes/whois.class.php" );
		$html_template = $WHMPress->whmp_get_template_directory() . "/whmpress/ajax/first.html";
		if ( ! is_file( $html_template ) ) {
			$html_template = WHMP_PLUGIN_DIR . "/templates/ajax/first.html";
		}
		
		$_insert_data = [
			"search_term" => $_REQUEST["domain"],
			"search_ip"   => $WHMPress->ip_address(),
			"search_time" => current_time( 'mysql' ),
		];
		
		$whois = new Whois();
		
		if ( isset( $_REQUEST["domain"] ) ) {
			$domain = $_REQUEST["domain"];
		} else if ( isset( $_REQUEST["search_domain"] ) ) {
			$domain = $_REQUEST["search_domain"];
		} else {
			$domain = "";
		}
		
		$searchonly = isset( $_POST["searchonly"] ) ? $_POST["searchonly"] : "*";
		if ( isset( $_POST['extensions'] ) && ! empty( $_POST['extensions'] ) ) {
			$searchonly = $_POST['extensions'];
		}
		
		if ( empty( $searchonly ) ) {
			$searchonly = "*";
		}
		/*var_dump($_POST['extensions']);
		var_dump(empty($_POST['extensions']));
		show_array($_POST);
		var_dump($searchonly);
		die;*/
		$domain = ltrim( $domain, '//' );
		
		if ( substr( strtolower( $domain ), 0, 7 ) == "http://" ) {
			$domain = substr( $domain, 7 );
		}
		if ( substr( strtolower( $domain ), 0, 8 ) == "https://" ) {
			$domain = substr( $domain, 8 );
		}
		$domain = "http://" . $domain;
		$domain = parse_url( $domain );
		if ( strtolower( substr( $domain["host"], 0, 4 ) ) == "www." ) {
			$domain["host"] = substr( $domain["host"], 4 );
		}
		if ( strtolower( substr( $domain["host"], 0, 3 ) ) == "ww." ) {
			$domain["host"] = substr( $domain["host"], 3 );
		}
		
		$domain["extension"] = whmp_get_domain_extension( $domain["host"] );
		if ( ! isset( $domain["host"] ) ) {
			$domain["host"] = "";
		}
		if ( $domain["extension"] == "" ) {
			$extensions = whmpress_get_option( 'whois_db' );
			$ext        = explode( "|", $extensions );
			$ext        = $ext[0];
			$domain["host"] .= $ext;
			$domain["extension"] = ltrim( $ext, "." );
		}
		
		$_REQUEST["params"]["www_text"]      = __( "WWW", "whmpress" );
		$_REQUEST["params"]["whois_text"]    = __( "WHOIS", "whmpress" );
		$_REQUEST["params"]["transfer_text"] = __( "Transfer", "whmpress" );
		if ( isset( $_REQUEST["params"] ) ) {
			$smarty_array["params"] = $_REQUEST["params"];
		}
		//if (!is_array($smarty_array["params"])) $smarty_array["params"] = json_decode($smarty_array["params"], true);
		
		$smarty_array["domain"]    = $domain["host"];
		$smarty_array["extension"] = $domain["extension"];
		$result                    = $whois->whoislookup( $domain["host"], $domain["extension"] );
		$register_text             = whmpress_get_option( 'register_domain_button_text', __( "Select", "whmpress" ) );
		
		$HTML       = "";
		$onlydomain = str_replace( "." . $domain["extension"], "", $domain["host"] );
		
		### Getting price
		$Q = "SELECT d.id, d.extension 'tld', t.type, c.code, c.suffix, c.prefix, t.msetupfee, t.qsetupfee
            FROM `" . whmp_get_domain_pricing_table_name() . "` AS d
            INNER JOIN `" . whmp_get_pricing_table_name() . "` AS t ON t.relid = d.id
            INNER JOIN `" . whmp_get_currencies_table_name() . "` AS c ON c.id = t.currency
            WHERE t.type
            IN (
            'domainregister'
            ) AND d.extension IN ('.{$domain["extension"]}')
            AND c.code='" . whmp_get_currency_code( whmp_get_currency() ) . "' 
            ORDER BY d.id ASC 
            LIMIT 0 , 30";
		
		$price = $wpdb->get_row( $Q, ARRAY_A );
		
		if ( isset( $price["msetupfee"] ) ) {
			# if tld found in DB then calculate price.
			if ( $price["msetupfee"] > 0 ) {
				$year = "1 " . __( "Year", "whmpress" );
				$_y   = "1";
			} else if ( $price["qsetupfee"] > 0 ) {
				$year = "2 " . __( "Years", "whmpress" );
				$_y   = "2";
			} else {
				$year = "";
				$_y   = "";
			}
			
			$pricef = whmpress_domain_price_function(
				[
					"years"         => $_y,
					"tld"           => $domain["extension"],
					"html_class"    => "",
					"html_id"       => "",
					"prefix"        => "no",
					"suffix"        => "no",
					"show_duration" => "no",
					"type"          => "domainregister",
					"no_wrapper"    => "1",
				]
			);
			
			//$pricef = ($price["msetupfee"]>"0"?$price["msetupfee"]:$price["qsetupfee"]);
			if ( ( get_option( 'default_currency_symbol', "prefix" ) == "code" || get_option( 'default_currency_symbol', "prefix" ) == "prefix" ) && isset( $price[ get_option( 'default_currency_symbol', "prefix" ) ] ) ) {
				$pricef = $price[ get_option( 'default_currency_symbol', "prefix" ) ] . $pricef;
			} else if ( get_option( 'default_currency_symbol' ) == "suffix" && isset( $price[ get_option( 'default_currency_symbol' ) ] ) ) {
				$pricef = $pricef . $price[ get_option( 'default_currency_symbol' ) ];
			}
		} else {
			$pricef = "";
			$year   = "";
		}
		
		$smarty_array["price"]    = $pricef;
		$smarty_array["duration"] = $year;
		
		if ( $result ) {
			$smarty_array["available"]        = "1";
			$_insert_data["domain_available"] = "1";
			$Class                            = "found";
			$Dom                              = str_replace( $domain["extension"], $domain["extension"], $domain["host"] );
			$Message                          = whmpress_get_option( "domain_available_message" );
			if ( $Message == "" ) {
				$Message = __( "[domain-name] is available", "whmpress" );
			}
			$Message                 = str_replace( "[domain-name]", $Dom, $Message );
			$smarty_array["message"] = $Message;
			
			if ( $register_text == "" ) {
				$register_text = __( "Select", "whmpress" );
			}
			
			## Generating register domain url
			if ( is_plugin_active( 'WHMpress_Client_Area/client-area.php' ) ) {
				$WHMPress_Client_Area = new WHMPress_Client_Area;
				if ( $WHMPress_Client_Area->is_permalink() ) {
					$_url = $WHMPress->get_whmcs_url( "order" ) . "/a/add/domain/register/sld/{$onlydomain}/tld/.{$domain["extension"]}/";
				} else {
					$_url = $WHMPress->get_whmcs_url( "order" ) . "a=add&domain=register&sld={$onlydomain}&tld=.{$domain["extension"]}";
				}
			} else {
				$_url = $WHMPress->get_whmcs_url( "order" ) . "a=add&domain=register&sld={$onlydomain}&tld=.{$domain["extension"]}";
			}
			
			$order_landing_page                 = isset( $_REQUEST["order_landing_page"] ) ? $_REQUEST["order_landing_page"] : "";
			$smarty_array["order_landing_page"] = $order_landing_page;
			$domain_form_name                   = str_replace( ".", "_", $domain["extension"] );
			if ( $order_landing_page == "1" ) {
				$hidden_form = "<form name='whmpress_domain_form_{$domain_form_name}' style='display:none' method='post' action='$_url'>
                    <input type='submit' id='whmpress_domain_form_{$domain_form_name}'>
                    <input type='hidden' name='domainsregperiod[{$domain["host"]}]' value='1'>
                    <input type='hidden' name='domains[]' value='{$domain["host"]}'>
                    </form>\n";
				$HTML .= $hidden_form;
				$button_action                 = "onclick=\"jQuery('#whmpress_domain_form_{$domain_form_name}').click();\"";
				$href                          = "javascript:;";
				$smarty_array["order_url"]     = $href;
				$smarty_array["button_action"] = $button_action;
				$smarty_array["hidden_form1"]  = $smarty_array["hidden_form"] = $hidden_form;
			} else {
				$button_action                 = "";
				$href                          = "$_url";
				$smarty_array["order_url"]     = $_url;
				$smarty_array["button_action"] = "";
				$smarty_array["hidden_form1"]  = $smarty_array["hidden_form"] = "";
			}
			
			$HTML .= "
            <div class=\"found-title\">
              <div class=\"domain-name\">$Message</div>";
			
			if ( $show_price == "1" || strtoupper( $show_price ) == "YES" ) {
				$HTML .= "<div class=\"rate\">$pricef</div>";
			}
			
			if ( ( $show_years == "1" || strtoupper( $show_years ) == "YES" ) && $pricef <> "" ) {
				$HTML .= "<div class=\"year\">$year</div>";
			}
			
			$HTML .= "<div class=\"select-box\">
                <a $button_action class=\"buy-button\" href='$href'>$register_text</a>
              </div>
              <div style=\"clear:both\"></div>
            </div>";
			$smarty_array["whois_link"] = "";
		} else {
			$smarty_array["available"]          = "0";
			$smarty_array["order_url"]          = "";
			$smarty_array["order_landing_page"] = "-1";
			
			$_insert_data["domain_available"] = "0";
			$Dom                              = str_replace( $domain["extension"], $domain["extension"], $domain["host"] );
			$Message                          = whmpress_get_option( "domain_not_available_message" );
			if ( $Message == "" ) {
				$Message = __( "[domain-name] is not available", "whmpress" );
			}
			$Message                 = str_replace( "[domain-name]", "<b>" . $Dom . "</b>", $Message );
			$smarty_array["message"] = $Message;
			if ( isset( $_POST["www_link"] ) && strtolower( $_POST["www_link"] ) == "yes" ) {
				$www_link = "<a class='www-button' href='http://{$Dom}' target='_blank'>" . __( "WWW", "whmpress" ) . "</a>";
			} else {
				$www_link = "";
			}
			
			## Generating transfer domain link if requested.
			if ( $enable_transfer_link == "1" || strtolower( $enable_transfer_link ) == "yes" || $enable_transfer_link === true ) {
				if ( is_plugin_active( 'WHMpress_Client_Area/client-area.php' ) ) {
					$WHMPress_Client_Area = new WHMPress_Client_Area;
					if ( $WHMPress_Client_Area->is_permalink() ) {
						$_url = $WHMPress->get_whmcs_url( "order" ) . "/a/add/domain/transfer/sld/{$onlydomain}/tld/.{$domain["extension"]}/";
					} else {
						$_url = $WHMPress->get_whmcs_url( "order" ) . "a=add&domain=transfer&sld={$onlydomain}&tld=.{$domain["extension"]}";
					}
				} else {
					$_url = $WHMPress->get_whmcs_url( "order" ) . "a=add&domain=transfer&sld={$onlydomain}&tld=.{$domain["extension"]}";
				}
				
				$order_landing_page                 = isset( $_REQUEST["order_landing_page"] ) ? $_REQUEST["order_landing_page"] : "";
				$smarty_array["order_landing_page"] = $order_landing_page;
				$domain_form_name                   = str_replace( ".", "_", $domain["extension"] );
				if ( $order_landing_page == "1" ) {
					$hidden_form = "<form name='whmpress_domain_form_{$domain_form_name}' style='display:none' method='post' action='$_url'>
                    <input type='submit' id='whmpress_domain_form_{$domain_form_name}'>
                    <input type='hidden' name='domainsregperiod[{$domain["host"]}]' value='1'>
                    <input type='hidden' name='domains[]' value='{$domain["host"]}'>
                    </form>\n";
					$HTML .= $hidden_form;
					$button_action                 = "onclick=\"jQuery('#whmpress_domain_form_{$domain_form_name}').click();\"";
					$href                          = "javascript:;";
					$smarty_array["order_url"]     = $href;
					$smarty_array["button_action"] = $button_action;
					$smarty_array["hidden_form1"]  = $smarty_array["hidden_form"] = $hidden_form;
				} else {
					$button_action                 = "";
					$href                          = "$_url";
					$smarty_array["order_url"]     = $_url;
					$smarty_array["button_action"] = "";
					$smarty_array["hidden_form1"]  = $smarty_array["hidden_form"] = "";
				}
				
				$transfer_link = "<a class='www-button' href='$_url'>" . __( "Transfer", "whmpress" ) . "</a>";
			} else {
				$transfer_link = "";
			}
			
			if ( isset( $_POST["whois_link"] ) && strtolower( $_POST["whois_link"] ) == "yes" ) {
				$whois_link                 = "<a class='whois-button' href='javascript:;' onclick='window.open(\"" . WHMP_PLUGIN_URL . "/whois.php?domain={$Dom}\",\"whmpwin\",\"width=600,height=600,toolbar=no,location=no,directories=no,status=no,menubar=no,resizable=0\")'>" . __( "WHOIS", "whmpress" ) . "</a>";
				$smarty_array["whois_link"] = WHMP_PLUGIN_URL . "/whois.php?domain={$Dom}";
			} else {
				$whois_link                 = "";
				$smarty_array["whois_link"] = "";
			}
			
			$HTML .= "
            <div class=\"not-found-title\">
              <div class=\"domain-name\">$Message</div>";
			if ( $show_price == "1" || strtoupper( $show_price ) == "YES" ) {
				$HTML .= "<div class=\"rate\"></div>";
			}
			
			if ( $show_years == "1" || strtoupper( $show_years ) == "YES" ) {
				$HTML .= "<div class=\"year\"></div>";
			}
			
			$HTML .= "<div class=\"select-box\">
                $www_link
                $whois_link
                $transfer_link
              </div>
              <div style='clear:both'></div>
            </div>";
		}
		
		// Record search logs if "Enable logs for searches" is enabled.
		if ( get_option( 'enable_logs' ) == "1" && $_insert_data["search_term"] <> "" ) {
			$wpdb->insert( whmp_get_logs_table_name(), $_insert_data );
		}
		
		$disable_domain_spinning = isset( $_REQUEST["disable_domain_spinning"] ) ? $_REQUEST["disable_domain_spinning"] : "0";
		if ( $disable_domain_spinning == "1" || strtolower( $disable_domain_spinning ) == "yes" ) {
			echo $HTML;
			break;
		}
		
		if ( isset( $_REQUEST["skip_extra"] ) && $_REQUEST["skip_extra"] == "1" ) {
			global $wpdb;
			$extensions = whmpress_get_option( 'whois_db' );
			$extensions = explode( "\n", $extensions );
			foreach ( $extensions as $k => $ext ) {
				if ( trim( $ext ) == "" ) {
					unset( $extensions[ $k ] );
				}
			}
			
			foreach ( $extensions as $y => &$ext ) {
				if ( trim( $ext <> "" ) ) {
					$E = explode( "|", $ext );
					if ( ! isset( $E[0] ) ) {
						$E[0] = "";
					}
					$ext = $E[0];
					if ( $wpdb->get_var( "SELECT COUNT(*) FROM `" . whmp_get_domain_pricing_table_name() . "` WHERE `extension`='$ext'" ) == 0 ) {
						unset( $extensions[ $y ] );
					}
				}
			}
			$PageSize   = get_option( 'no_of_domains_to_show', '10' );
			$extensions = array_slice( $extensions, 0, $PageSize );
			$extensions = array_values( $extensions );
		} elseif ( is_string( $searchonly ) && $searchonly == "*" ) {
			$extensions = get_option( "tld_order" );
			if ( trim( $extensions ) == "" ) {
				$extensions = whmpress_get_option( 'whois_db' );
				$extensions = explode( "\n", $extensions );
				foreach ( $extensions as $k => $ext ) {
					if ( trim( $ext ) == "" ) {
						unset( $extensions[ $k ] );
					}
				}
				
				$PageSize   = get_option( 'no_of_domains_to_show', '10' );
				$extensions = array_slice( $extensions, 0, $PageSize );
				foreach ( $extensions as &$ext ) {
					$E = explode( "|", $ext );
					if ( ! isset( $E[0] ) ) {
						$E[0] = "";
					}
					$ext = $E[0];
				}
				
			} else {
				$extensions = str_replace( " ", "", $extensions );
				$extensions = explode( ",", $extensions );
			}
		} else {
			$extensions = $searchonly;
			if ( ! is_array( $searchonly ) && is_string( $searchonly ) ) {
				$extensions = str_replace( " ", "", $extensions );
				$extensions = explode( ",", $extensions );
			}
		}
		
		$smarty_array["order_button_text"] = $register_text;
		$Message                           = whmpress_get_option( "domain_recommended_list" );
		if ( $Message == "" ) {
			$Message = __( "Recommended domains list", "whmpress" );
		}
		$Message                                  = str_replace( "[domain-name]", $domain["host"], $Message );
		$smarty_array["recommended_domains_text"] = $Message;
		$HTML .= "<div class='recommended'>$Message</div>";
		$onlydomain     = str_replace( "." . $domain["extension"], "", $domain["host"] );
		$smarty_domains = [];
		$HTML .= "<div class='result-div'>";
		
		foreach ( $extensions as $x => $ext ) {
			$ext = ltrim( $ext, "." );
			
			if ( $ext <> $domain["extension"] ) {
				$smarty_domain              = [];
				$smarty_domain["extension"] = $ext;
				
				$newDomain = $onlydomain . "." . $ext;
				
				$smarty_domain["domain"] = $newDomain;
				
				$result = $whois->whoislookup( $newDomain, $ext );
				
				$Q = "SELECT d.id, d.extension 'tld', t.type, c.code, c.suffix, c.prefix, t.msetupfee, t.qsetupfee
                    FROM `" . whmp_get_domain_pricing_table_name() . "` AS d
                    INNER JOIN `" . whmp_get_pricing_table_name() . "` AS t ON t.relid = d.id
                    INNER JOIN `" . whmp_get_currencies_table_name() . "` AS c ON c.id = t.currency
                    WHERE t.type
                    IN (
                    'domainregister'
                    ) AND d.extension IN ('.{$ext}')
                    AND c.code='" . whmp_get_currency_code( whmp_get_currency() ) . "' 
                    ORDER BY d.id ASC 
                    LIMIT 0 , 30";
				
				$price = $wpdb->get_row( $Q, ARRAY_A );
				
				if ( isset( $price["msetupfee"] ) ) {
					if ( $price["msetupfee"] > 0 ) {
						$year = "(1 " . __( "Year", "whmpress" ) . ")";
						$_y   = "1";
					} else if ( $price["qsetupfee"] > 0 ) {
						$year = "(2 " . __( "Years", "whmpress" ) . ")";
						$_y   = "1";
					} else {
						$year = "";
						$_y   = "";
					}
					
					$pricef = whmpress_domain_price_function(
						[
							"years"         => $_y,
							"tld"           => $ext,
							"html_class"    => "",
							"html_id"       => "",
							"prefix"        => "no",
							"suffix"        => "no",
							"show_duration" => "no",
							"type"          => "domainregister",
							"no_wrapper"    => "1",
						]
					);
					if ( ( get_option( 'default_currency_symbol', "prefix" ) == "code" || get_option( 'default_currency_symbol', "prefix" ) == "prefix" ) && isset( $price[ get_option( 'default_currency_symbol', "prefix" ) ] ) ) {
						$pricef = $price[ get_option( 'default_currency_symbol', "prefix" ) ] . $pricef;
					} else if ( get_option( 'default_currency_symbol' ) == "suffix" && isset( $price[ get_option( 'default_currency_symbol' ) ] ) ) {
						$pricef = $pricef . $price[ get_option( 'default_currency_symbol' ) ];
					}
				} else {
					$pricef = "";
					$year   = "";
				}
				
				$smarty_domain["price"]    = $pricef;
				$smarty_domain["duration"] = $year;
				
				if ( $result ) {
					$smarty_domain["available"] = "1";
					$Dom                        = $newDomain;
					$Message                    = whmpress_get_option( 'ongoing_domain_available_message' );
					if ( $Message == "" ) {
						$Message = __( "[domain-name] is available", "whmpress" );
					}
					$Message                  = __( $Message, "whmpress" );
					$Message                  = str_replace( "[domain-name]", $Dom, $Message );
					$smarty_domain["message"] = $Message;
					
					$register_text = whmpress_get_option( 'register_domain_button_text' );
					if ( $register_text == "" ) {
						$register_text = __( "Select", "whmpress" );
					}
					
					## Generating domain registration link
					if ( is_plugin_active( 'WHMpress_Client_Area/client-area.php' ) ) {
						$WHMPress_Client_Area = new WHMPress_Client_Area;
						if ( $WHMPress_Client_Area->is_permalink() ) {
							$_url = $WHMPress->get_whmcs_url( "order" ) . "/a/add/domain/register/sld/{$onlydomain}/tld/.{$ext}/";
						} else {
							$_url = $WHMPress->get_whmcs_url( "order" ) . "a=add&domain=register&sld={$onlydomain}&tld=.{$ext}";
						}
					} else {
						$_url = $WHMPress->get_whmcs_url( "order" ) . "a=add&domain=register&sld={$onlydomain}&tld=.{$ext}";
					}
					
					$order_landing_page                  = isset( $_REQUEST["order_landing_page"] ) ? $_REQUEST["order_landing_page"] : "";
					$smarty_domain["order_landing_page"] = $order_landing_page;
					$domain_form_name                    = str_replace( ".", "_", $ext );
					if ( $order_landing_page == "1" ) {
						$hidden_form = "<form name='whmpress_domain_form_{$domain_form_name}' style='display:none' method='post' action='$_url'>
                            <input type='submit' id='whmpress_domain_form_{$domain_form_name}'>
                            <input type='hidden' name='domainsregperiod[{$Dom}]' value='1'>
                            <input type='hidden' name='domains[]' value='{$Dom}'>
                            </form>\n";
						$HTML .= $hidden_form;
						$button_action                  = "onclick=\"jQuery('#whmpress_domain_form_{$domain_form_name}').click();\"";
						$href                           = "javascript:;";
						$smarty_domain["order_url"]     = $href;
						$smarty_domain["button_action"] = $button_action;
						$smarty_domain["hidden_form"]   = $hidden_form;
					} else {
						$button_action                  = "";
						$href                           = "$_url";
						$smarty_domain["order_url"]     = $href;
						$smarty_domain["button_action"] = "";
						$smarty_domain["hidden_form"]   = "";
					}
					
					if ( ! empty( $price["msetupfee"] ) ) {
						$button                             = "<a $button_action href='$href' class='buy-button'>$register_text</a>";
						$smarty_domain["order_button_text"] = $register_text;
					} else {
						$smarty_domain["order_button_text"] = $button = "";
						
					}
					
					$HTML .= "
                    <div class='found-div'>
                        <div class=\"domain-name\">$Message</div>";
					
					if ( $show_price == "1" || strtoupper( $show_price ) == "YES" ) {
						$HTML .= "<div class=\"rate\">$pricef</div>";
					}
					
					if ( ( $show_years == "1" || strtoupper( $show_years ) == "YES" ) && $pricef <> "" ) {
						$HTML .= "<div class=\"year\">$year</div>";
					}
					
					$HTML .= "<div class=\"select-box\">
                            $button
                        </div>
                        <div style=\"clear:both\"></div>
                    </div>\n";
					$smarty_domain["whois_link"] = "";
				} else {
					$smarty_domain["order_url"]          = "2";
					$smarty_domain["available"]          = "0";
					$smarty_domain["order_landing_page"] = "-1";
					$smarty_domain["order_button_text"]  = "";
					
					$Dom = $newDomain;
					
					if ( isset( $_POST["www_link"] ) && strtolower( $_POST["www_link"] ) == "yes" ) {
						$www_link = "<a class=\"www-button\" href='http://{$Dom}' target='_blank'>" . __( "WWW", "whmpress" ) . "</a>";
					} else {
						$www_link = "";
					}
					
					if ( $enable_transfer_link == "1" || strtolower( $enable_transfer_link ) == "yes" || $enable_transfer_link === true ) {
						if ( is_plugin_active( 'WHMpress_Client_Area/client-area.php' ) ) {
							$WHMPress_Client_Area = new WHMPress_Client_Area;
							if ( $WHMPress_Client_Area->is_permalink() ) {
								$_url = $WHMPress->get_whmcs_url( "order" ) . "/a/add/domain/transfer/sld/{$onlydomain}/tld/.{$domain["extension"]}/";
							} else {
								$_url = $WHMPress->get_whmcs_url( "order" ) . "a=add&domain=transfer&sld={$onlydomain}&tld=.{$domain["extension"]}";
							}
						} else {
							$_url = $WHMPress->get_whmcs_url( "order" ) . "a=add&domain=transfer&sld={$onlydomain}&tld=.{$domain["extension"]}";
						}
						
						$order_landing_page                  = isset( $_REQUEST["order_landing_page"] ) ? $_REQUEST["order_landing_page"] : "";
						$smarty_domain["order_landing_page"] = $order_landing_page;
						$domain_form_name                    = str_replace( ".", "_", $domain["extension"] );
						if ( $order_landing_page == "1" ) {
							$hidden_form = "<form name='whmpress_domain_form_{$domain_form_name}' style='display:none' method='post' action='$_url'>
                    <input type='submit' id='whmpress_domain_form_{$domain_form_name}'>
                    <input type='hidden' name='domainsregperiod[{$domain["host"]}]' value='1'>
                    <input type='hidden' name='domains[]' value='{$domain["host"]}'>
                    </form>\n";
							$HTML .= $hidden_form;
							$button_action                 = "onclick=\"jQuery('#whmpress_domain_form_{$domain_form_name}').click();\"";
							$href                          = "javascript:;";
							$smarty_array["order_url"]     = $href;
							$smarty_array["button_action"] = $button_action;
							$smarty_array["hidden_form"]   = $hidden_form;
						} else {
							$button_action                  = "";
							$href                           = "$_url";
							$smarty_domain["order_url"]     = $_url;
							$smarty_domain["button_action"] = "";
							$smarty_domain["hidden_form"]   = "";
						}
						
						$transfer_link = "<a class='www-button' href='$_url'>" . __( "Transfer", "whmpress" ) . "</a>";
					} else {
						$transfer_link = "";
					}
					
					if ( isset( $_POST["whois_link"] ) && strtolower( $_POST["whois_link"] ) == "yes" ) {
						$whois_link                  = "<a class=\"whois-button\" href='javascript:;' onclick='window.open(\"" . WHMP_PLUGIN_URL . "/whois.php?domain={$Dom}\",\"whmpwin\",\"width=600,height=600,toolbar=no,location=no,directories=no,status=no,menubar=no,resizable=0\")'>" . __( "WHOIS", "whmpress" ) . "</a>";
						$smarty_domain["whois_link"] = WHMP_PLUGIN_URL . "/whois.php?domain={$Dom}";
					} else {
						$whois_link                  = "";
						$smarty_domain["whois_link"] = $whois_link;
					}
					
					## Generating domain transfter link
					if ( $enable_transfer_link == "1" || strtolower( $enable_transfer_link ) == "yes" || $enable_transfer_link === true ) {
						if ( is_plugin_active( 'WHMpress_Client_Area/client-area.php' ) ) {
							$WHMPress_Client_Area = new WHMPress_Client_Area;
							if ( $WHMPress_Client_Area->is_permalink() ) {
								$_url = $WHMPress->get_whmcs_url( "order" ) . "/a/add/domain/transfer/sld/{$onlydomain}/tld/.{$ext}/";
							} else {
								$_url = $WHMPress->get_whmcs_url( "order" ) . "a=add&domain=transfer&sld={$onlydomain}&tld=.{$ext}";
							}
						} else {
							$_url = $WHMPress->get_whmcs_url( "order" ) . "a=add&domain=transfer&sld={$onlydomain}&tld=.{$ext}";
						}
						
						$order_landing_page                  = isset( $_REQUEST["order_landing_page"] ) ? $_REQUEST["order_landing_page"] : "";
						$smarty_domain["order_landing_page"] = $order_landing_page;
						$domain_form_name                    = str_replace( ".", "_", $ext );
						if ( $order_landing_page == "1" ) {
							$hidden_form = "<form name='whmpress_domain_form_{$domain_form_name}' style='display:none' method='post' action='$_url'>
                                <input type='submit' id='whmpress_domain_form_{$domain_form_name}'>
                                <input type='hidden' name='domainsregperiod[{$Dom}]' value='1'>
                                <input type='hidden' name='domains[]' value='{$Dom}'>
                                </form>\n";
							$HTML .= $hidden_form;
							$button_action                  = "onclick=\"jQuery('#whmpress_domain_form_{$domain_form_name}').click();\"";
							$href                           = "javascript:;";
							$smarty_domain["order_url"]     = $href;
							$smarty_domain["button_action"] = $button_action;
							$smarty_domain["hidden_form"]   = $hidden_form;
						} else {
							$button_action                  = "";
							$href                           = "$_url";
							$smarty_domain["order_url"]     = $href;
							$smarty_domain["button_action"] = "";
							$smarty_domain["hidden_form"]   = "";
						}
						$transfer_link = "<a class='www-button' href='$_url'>" . __( "Transfer", "whmpress" ) . "</a>";
					} else {
						$transfer_link = "";
					}
					
					$Message = whmpress_get_option( 'ongoing_domain_not_available_message' );
					if ( $Message == "" ) {
						$Message = __( "[domain-name] is not available", "whmpress" );
					}
					$Message                  = str_replace( "[domain-name]", $Dom, $Message );
					$smarty_domain["message"] = $Message;
					
					$HTML .= "<div class='not-found-div'>";
					$HTML .= '<div class="domain-name">' . $Message . '</div>';
					
					if ( $show_price == "1" || strtoupper( $show_price ) == "YES" ) {
						$HTML .= "<div class=\"rate\"></div>";
					}
					
					if ( $show_years == "1" || strtoupper( $show_years ) == "YES" ) {
						$HTML .= "<div class=\"year\"></div>";
					}
					
					$HTML .= "<div class=\"select-box\">
                        $www_link
                        $whois_link
                        $transfer_link
                    </div>";
					$HTML .= "<div style=\"clear:both\"></div>
                    </div>\n";
				}
				$smarty_domains[] = $smarty_domain;
			}
		}
		
		$load_more = whmpress_get_option( 'load_more_button_text' );
		if ( $load_more == "" ) {
			$load_more = __( "Load More", "whmpress" );
		}
		if ( is_string( $searchonly ) && $searchonly == "*" ) {
			$HTML .= "<div style='clear:both'></div>";
			$smarty_load_more = "<div id='load-more-div' class='load-more-div'><button type='button'>$load_more</button></div>";
			$HTML .= $smarty_load_more;
		} else {
			$smarty_load_more = "";
		}
		
		$smarty_load_more .= "
            <script>
                function load_more() {
                    jQuery(\"#load-more-div\").remove();
                    jQuery(\".result-div\").append('<div id=\"waiting_div\" style=\"font-size:30px;text-align: center;\"><i class=\"fa fa-spinner fa-spin whmp_domain_search_ajax_results_spinner\"></i></div>');
                    whmp_page++;
                    jQuery.post(WHMPAjax.ajaxurl, {'domain':jQuery('#domain_{$ajax_id}').val(),'action':'whmpress_action','do':'loadWhoisPage','skip_extra':'0','page':whmp_page,'searchonly':'{$searchonly}','lang':''}, function(data){
                        jQuery(\"#waiting_div\").remove();
                        jQuery(\".result-div\").append(data);
                    });
                };
            </script>
            ";
		
		if ( is_file( $html_template ) ) {
			$vars = [
				"data"      => $smarty_array,
				"domains"   => $smarty_domains,
				"load_more" => $smarty_load_more
			];
			
			$OutputString = whmp_smarty_template( $html_template, $vars );
			echo $OutputString;
		} else {
			$HTML .= "</div>";
			echo $HTML;
		}
		break;
	case "loadWhoisPage":
		include_once( WHMP_PLUGIN_DIR . "/includes/whois.class.php" );
		
		$html_template = $WHMPress->whmp_get_template_directory() . "/whmpress/ajax/more.html";
		if ( ! is_file( $html_template ) ) {
			$html_template = WHMP_PLUGIN_DIR . "/templates/ajax/more.html";
		}
		
		$whois  = new Whois;
		$HTML   = "";
		$domain = $_REQUEST["domain"];
		$domain = ltrim( $domain, '//' );
		if ( substr( strtolower( $domain ), 0, 7 ) == "http://" ) {
			$domain = substr( $domain, 7 );
		}
		if ( substr( strtolower( $domain ), 0, 8 ) == "https://" ) {
			$domain = substr( $domain, 8 );
		}
		$domain = "http://" . $domain;
		$domain = parse_url( $domain );
		if ( strtolower( substr( $domain["host"], 0, 4 ) ) == "www." ) {
			$domain["host"] = substr( $domain["host"], 4 );
		}
		if ( strtolower( substr( $domain["host"], 0, 3 ) ) == "ww." ) {
			$domain["host"] = substr( $domain["host"], 3 );
		}
		$domain["extension"] = whmp_get_domain_extension( $domain["host"] );
		#echo "<pre>"; print_r($domain); echo "</pre>";
		if ( ! isset( $domain["host"] ) ) {
			$domain["host"] = "";
		}
		if ( $domain["extension"] == $domain["host"] ) {
			$domain["extension"] = "com";
			$domain["host"] .= ".com";
		}

		$onlydomain = str_replace( "." . $domain["extension"], "", $domain["host"] );
		$WhoIS      = whmpress_get_option( "whois_db" );
		$WhoIS      = explode( "\n", $WhoIS );
		foreach ( $WhoIS as $k => $ext ) {
			if ( trim( $ext ) == "" ) {
				unset( $WhoIS[ $k ] );
			}
		}
		
		# Removing domains from WhoIs DB if not found in your WHMCS data.
		# If extended domain ajax call then it will true
		global $wpdb;
		
		if ( isset( $_REQUEST["skip_extra"] ) && $_REQUEST["skip_extra"] == "1" ) {
			foreach ( $WhoIS as $x => $line ) {
				$ar = explode( "|", $line );
				if ( ! isset( $ar[0] ) ) {
					$ar[0] = "";
				}
				$ext = $ar[0];
				if ( $wpdb->get_var( "SELECT COUNT(*) FROM `" . whmp_get_domain_pricing_table_name() . "` WHERE `extension`='$ext'" ) == 0 ) {
					#echo "SELECT COUNT(*) FROM `".whmp_get_domain_pricing_table_name()."` WHERE `extension`='$ext'"; die;
					unset( $WhoIS[ $x ] );
				}
			}
			$WhoIS = array_values( $WhoIS );
		}
		
		/*if (isset($_REQUEST["skip_extra"])) {
			global $wpdb;
			$extensions = whmpress_get_option('whois_db');
			$extensions = explode("\n",$extensions);
			foreach($extensions as $k=>$ext) {
				if (trim($ext)=="") unset($extensions[$k]);
			}
			
			foreach($extensions as $y=>&$ext) {
				$E = explode("|",$ext);
				if (!isset($E[0])) $E[0] = "";
				$ext = $E[0];
				if ($wpdb->get_var("SELECT COUNT(*) FROM `".whmp_get_domain_pricing_table_name()."` WHERE `extension`='$ext'")==0) {
					unset($extensions[$y]);
				}
			}
			$PageSize = get_option('no_of_domains_to_show','10');
			$extensions = array_slice($extensions, 0, $PageSize);
			$extensions = array_values($extensions);
		} elseif ($searchonly == "*") {            
			$extensions = get_option("tld_order");
			if (trim($extensions)=="") {
				$extensions = whmpress_get_option('whois_db');
				$extensions = explode("\n",$extensions);
				foreach($extensions as $k=>$ext) {
					if (trim($ext)=="") unset($extensions[$k]);
				}
				$PageSize = get_option('no_of_domains_to_show','10');
				$extensions = array_slice($extensions, 0, $PageSize);
				foreach($extensions as &$ext) {
					$E = explode("|",$ext);
					if (!isset($E[0])) $E[0] = "";
					$ext = $E[0];
				}
			} else {
				$extensions = str_replace(" ", "", $extensions);
				$extensions = explode(",", $extensions);
			}
		} else {
			$extensions = $searchonly;
			$extensions = str_replace(" ", "", $extensions);
			$extensions = explode(",", $extensions);
		}*/
		
		# Removing domains from WhoIs DB who are in backend settings
		$extensions = get_option( "tld_order" );
		$extensions = str_replace( " ", "", $extensions );
		$extensions = explode( ",", $extensions );
		
		if ( count( $extensions ) > 0 ) {
			foreach ( $WhoIS as $x => $line ) {
				$ar = explode( "|", $line );
				if ( ! isset( $ar[0] ) ) {
					$ar[0] = "";
				}
				$ext = $ar[0];
				if ( in_array( $ext, $extensions ) ) {
					unset( $WhoIS[ $x ] );
				}
			}
			$WhoIS = array_values( $WhoIS );
		}
		
		# creating pagination
		$PageSize = get_option( 'no_of_domains_to_show', '10' );
		#echo "Page: ".$_REQUEST["page"]." ";
		$start = ( $_REQUEST["page"] - 1 ) * ( $PageSize );
		#echo "Start: ".$start." PageSize: $PageSize";
		$WhoIS          = array_slice( $WhoIS, $start, $PageSize );

		$smarty_domains = [];
		foreach ( $WhoIS as $line ) {
			$ar = explode( "|", $line );
			if ( ! isset( $ar[0] ) ) {
				$ar[0] = "";
			}
			$ext = ltrim( $ar[0], "." );
			if ( $ext <> $domain["extension"] ) {
				$smarty_domain              = [];
				$smarty_domain["extension"] = $ext;
				
				$newDomain               = $onlydomain . "." . $ext;
				$smarty_domain["domain"] = $newDomain;
				//echo $newDomain ."=". $ext."<br />";
				
				if ( is_plugin_active( 'WHMpress_Client_Area/client-area.php' ) ) {
					$WHMPress_Client_Area = new WHMPress_Client_Area;
					if ( $WHMPress_Client_Area->is_permalink() ) {
						$_url = $WHMPress->get_whmcs_url( "order" ) . "/a/add/domain/register/sld/{$onlydomain}/tld/.{$domain["extension"]}/";
					} else {
						$_url = $WHMPress->get_whmcs_url( "order" ) . "a=add&domain=register&sld={$onlydomain}&tld=.{$domain["extension"]}";
					}
				} else {
					$_url = $WHMPress->get_whmcs_url( "order" ) . "a=add&domain=register&sld={$onlydomain}&tld=.{$domain["extension"]}";
				}
				
				$result = $whois->whoislookup( $newDomain, $ext );
				
				## Getting price.
				$Q = "SELECT d.id, d.extension 'tld', t.type, c.code, c.suffix, c.prefix, t.msetupfee, t.qsetupfee
                    FROM `" . whmp_get_domain_pricing_table_name() . "` AS d
                    INNER JOIN `" . whmp_get_pricing_table_name() . "` AS t ON t.relid = d.id
                    INNER JOIN `" . whmp_get_currencies_table_name() . "` AS c ON c.id = t.currency
                    WHERE t.type
                    IN (
                    'domainregister'
                    ) AND d.extension IN ('.{$ext}')
                    AND c.code='" . whmp_get_currency_code( whmp_get_currency() ) . "' 
                    ORDER BY d.id ASC 
                    LIMIT 0 , 30";
				
				$price = $wpdb->get_row( $Q, ARRAY_A );
				
				if ( isset( $price["msetupfee"] ) ) {
					if ( $price["msetupfee"] > 0 ) {
						$year = "(1 " . __( "Year", "whmpress" ) . ")";
						$_y   = "1";
					} else if ( $price["qsetupfee"] > 0 ) {
						$year = "(2 " . __( "Years", "whmpress" ) . ")";
						$_y   = "2";
					} else {
						$year = $_y = "";
					}
					
					$pricef = whmpress_domain_price_function(
						[
							"years"         => $_y,
							"tld"           => $ext,
							"html_class"    => "",
							"html_id"       => "",
							"prefix"        => "no",
							"suffix"        => "no",
							"show_duration" => "no",
							"type"          => "domainregister",
							"no_wrapper"    => "1",
						]
					);
					
					//$pricef = ($price["msetupfee"]>"0"?$price["msetupfee"]:$price["qsetupfee"]);
					
					if ( ( get_option( 'default_currency_symbol', "prefix" ) == "code" || get_option( 'default_currency_symbol', "prefix" ) == "prefix" ) && isset( $price[ get_option( 'default_currency_symbol', "prefix" ) ] ) ) {
						$pricef = $price[ get_option( 'default_currency_symbol', "prefix" ) ] . $pricef;
					} else if ( get_option( 'default_currency_symbol' ) == "suffix" && isset( $price[ get_option( 'default_currency_symbol' ) ] ) ) {
						$pricef = $pricef . $price[ get_option( 'default_currency_symbol' ) ];
					}
				} else {
					$pricef = "";
					$year   = "";
				}
				
				$smarty_domain["price"]    = $pricef;
				$smarty_domain["duration"] = $year;
				
				if ( $result ) {
					$smarty_domain["available"] = "1";
					$Dom                        = $newDomain;
					$Message                    = whmpress_get_option( 'ongoing_domain_available_message' );
					if ( $Message == "" ) {
						$Message = __( "[domain-name] is available", "whmpress" );
					}
					$Message                  = str_replace( "[domain-name]", $Dom, $Message );
					$smarty_domain["message"] = $Message;
					
					$register_text = whmpress_get_option( 'register_domain_button_text' );
					if ( $register_text == "" ) {
						$register_text = __( "Select", "whmpress" );
					}
					
					if ( is_plugin_active( 'WHMpress_Client_Area/client-area.php' ) ) {
						$WHMPress_Client_Area = new WHMPress_Client_Area;
						if ( $WHMPress_Client_Area->is_permalink() ) {
							$_url = $WHMPress->get_whmcs_url( "order" ) . "/a/add/domain/register/sld/{$onlydomain}/tld/.{$ext}/";
						} else {
							$_url = $WHMPress->get_whmcs_url( "order" ) . "a=add&domain=register&sld={$onlydomain}&tld=.{$ext}";
						}
					} else {
						$_url = $WHMPress->get_whmcs_url( "order" ) . "a=add&domain=register&sld={$onlydomain}&tld=.{$ext}";
					}
					
					if ( empty( $price["msetupfee"] ) ) {
						$button = "";
					} else {
						$button = "<a class='buy-button' href='$_url'>$register_text</a>";
					}
					
					$domain_form_name = str_replace( ".", "_", $ext );
					
					$order_landing_page                  = isset( $_REQUEST["order_landing_page"] ) ? $_REQUEST["order_landing_page"] : "";
					$smarty_domain["order_landing_page"] = $order_landing_page;
					if ( $order_landing_page == "1" ) {
						$hidden_form = "<form name='whmpress_domain_form_{$ext}' style='display:none' method='post' action='$_url'>
                            <input type='submit' id='whmpress_domain_form_{$domain_form_name}'>
                            <input type='hidden' name='domainsregperiod[{$domain["host"]}]' value='1'>
                            <input type='hidden' name='domains[]' value='{$domain["host"]}'>
                            </form>\n";
						$HTML .= $hidden_form;
						$button_action                  = "onclick=\"jQuery('#whmpress_domain_form_{$domain_form_name}').click();\"";
						$href                           = "javascript:;";
						$smarty_domain["order_url"]     = $href;
						$smarty_domain["button_action"] = $button_action;
						$smarty_domain["hidden_form"]   = $hidden_form;
					} else {
						$button_action                = "";
						$href                         = "$_url";
						$smarty_domain["order_url"]   = $_url;
						$smarty_domain["hidden_form"] = "";
					}
					
					if ( ! empty( $price["msetupfee"] ) ) {
						$button                             = "<a $button_action href='$href' class='buy-button'>$register_text</a>";
						$smarty_domain["order_button_text"] = $register_text;
					} else {
						$smarty_domain["order_button_text"] = $button = "";
						
					}
					
					$HTML .= "
                    <div class='found-div'>
                        <div class=\"domain-name\">$Message</div>";
					
					if ( $show_price == "1" || strtoupper( $show_price ) == "YES" ) {
						$HTML .= "<div class=\"rate\">$pricef</div>";
					}
					
					if ( ( $show_years == "1" || strtoupper( $show_years ) == "YES" ) && $pricef <> "" ) {
						$HTML .= "<div class=\"year\">$year</div>";
					}
					
					$HTML .= "<div class=\"select-box\">
                            $button
                        </div>
                        <div style=\"clear:both\"></div>
                    </div>\n";
					$smarty_domain["whois_link"] = "";
				} else {
					$smarty_domain["order_url"]          = "";
					$smarty_domain["available"]          = "0";
					$smarty_domain["order_landing_page"] = "-1";
					$smarty_domain["order_button_text"]  = "";
					$Dom                                 = $newDomain;
					$Message                             = whmpress_get_option( 'ongoing_domain_not_available_message' );
					if ( $Message == "" ) {
						$Message = __( "[domain-name] is registered", "whmpress" );
					}
					$Message                  = str_replace( "[domain-name]", $Dom, $Message );
					$smarty_domain["message"] = $Message;
					
					if ( isset( $_POST["www_link"] ) && strtolower( $_POST["www_link"] ) == "yes" ) {
						$www_link                    = "<a class='www-button' href='http://{$Dom}' target='_blank'>" . __( "WWW", "whmpress" ) . "</a>";
						$smarty_domain["whois_link"] = WHMP_PLUGIN_URL . "/whois.php?domain={$Dom}";
					} else {
						$www_link                    = "";
						$smarty_domain["whois_link"] = $whois_link;
					}
					$order_landing_page   = $_POST['params']['order_landing_page'];
					$enable_transfer_link = $_POST['params']['enable_transfer_link'];
					//echo $order_landing_page." = ".$enable_transfer_link."<br>";
					if ( $enable_transfer_link == "1" || strtolower( $enable_transfer_link ) == "yes" || $enable_transfer_link === true ) {
						if ( is_plugin_active( 'WHMpress_Client_Area/client-area.php' ) ) {
							$WHMPress_Client_Area = new WHMPress_Client_Area;
							if ( $WHMPress_Client_Area->is_permalink() ) {
								$_url = $WHMPress->get_whmcs_url( "order" ) . "/a/add/domain/transfer/sld/{$onlydomain}/tld/.{$ext}/";
							} else {
								$_url = $WHMPress->get_whmcs_url( "order" ) . "a=add&domain=transfer&sld={$onlydomain}&tld=.{$ext}";
							}
						} else {
							$_url = $WHMPress->get_whmcs_url( "order" ) . "a=add&domain=transfer&sld={$onlydomain}&tld=.{$ext}";
						}
						
						$order_landing_page                  = isset( $_REQUEST["order_landing_page"] ) ? $_REQUEST["order_landing_page"] : "";
						$smarty_domain["order_landing_page"] = $order_landing_page;
						$domain_form_name                    = str_replace( ".", "_", $domain["extension"] );
						if ( $order_landing_page == "1" ) {
							$hidden_form = "<form name='whmpress_domain_form_{$domain_form_name}' style='display:none' method='post' action='$_url'>
                            <input type='submit' id='whmpress_domain_form_{$domain_form_name}'>
                            <input type='hidden' name='domainsregperiod[{$domain["host"]}]' value='1'>
                            <input type='hidden' name='domains[]' value='{$domain["host"]}'>
                            </form>\n";
							$HTML .= $hidden_form;
							$button_action                  = "onclick=\"jQuery('#whmpress_domain_form_{$domain_form_name}').click();\"";
							$_url                           = $href = "javascript:;";
							$smarty_domain["order_url"]     = $href;
							$smarty_domain["button_action"] = $button_action;
							$smarty_domain["hidden_form"]   = $hidden_form;
						} else {
							$button_action                  = "";
							$href                           = "$_url";
							$smarty_domain["order_url"]     = $_url;
							$smarty_domain["button_action"] = "";
							$smarty_domain["hidden_form"]   = "";
						}
						
						$transfer_link = "<a class='www-button' href='$_url'>" . __( "Transfer", "whmpress" ) . "</a>";
					} else {
						$transfer_link = "";
					}
					
					if ( isset( $_POST["whois_link"] ) && strtolower( $_POST["whois_link"] ) == "yes" ) {
						$whois_link = "<a class='whois-button' href='javascript:;' onclick='window.open(\"" . WHMP_PLUGIN_URL . "/whois.php?domain={$Dom}\",\"whmpwin\",\"width=600,height=600,toolbar=no,location=no,directories=no,status=no,menubar=no,resizable=0\")'>" . __( "WHOIS", "whmpress" ) . "</a>";
					} else {
						$whois_link = "";
					}
					
					$HTML .= "<div class='not-found-div'>";
					$HTML .= '<div class="domain-name">' . $Message . '</div>';
					$HTML .= "
                    <div class=\"rate\"></div>
                    <div class=\"year\"></div>
                    <div class=\"select-box\">
                        $www_link
                        $whois_link
                    </div>";
					$HTML .= "<div style=\"clear:both\"></div>
                    </div>\n";
				}
				$smarty_domains[] = $smarty_domain;
			}
		}
		$load_more = whmpress_get_option( 'load_more_button_text' );
		if ( $load_more == "" ) {
			$load_more = __( "Load More", "whmpress" );
		}
		if ( sizeof( $WhoIS ) >= $PageSize ) {
			$HTML .= "<div class='load-more-div' id='load-more-div'><button type='button'>$load_more</button></div>";
			$smarty_load_more = "<div Class='load-more-div' id='load-more-div'><button type='button'>$load_more</button></div>";
		} else {
			$smarty_load_more = "";
		}
		
		/*$smarty_load_more .= "
			<script>
				function load_more() {
					jQuery(\"#load-more-div\").remove();
					jQuery(\".result-div\").append('<div id=\"waiting_div\" style=\"font-size:30px;text-align: center;\"><i class=\"fa fa-spinner fa-spin whmp_domain_search_ajax_results_spinner\"></i></div>');
					whmp_page++;
					jQuery.post(WHMPAjax.ajaxurl, {'domain':jQuery('#domain_{$ajax_id}').val(),'action':'whmpress_action','do':'loadWhoisPage','skip_extra':'0','page':whmp_page,'searchonly':'{$searchonly}','lang':''}, function(data){
						jQuery(\"#waiting_div\").remove();
						jQuery(\".result-div\").append(data);
					});
				};
			</script>
			";*/
		
		if ( is_file( $html_template ) ) {
			$vars = [
				"domains"   => $smarty_domains,
				"load_more" => $smarty_load_more
			];
			
			$_REQUEST["params"]["www_text"]      = __( "WWW", "whmpress" );
			$_REQUEST["params"]["whois_text"]    = __( "WHOIS", "whmpress" );
			$_REQUEST["params"]["transfer_text"] = __( "Transfer", "whmpress" );
			if ( isset( $_REQUEST["params"] ) ) {
				$vars["params"] = $_REQUEST["params"];
			}
			if ( ! is_array( $vars["params"] ) ) {
				$vars["params"] = json_decode( $vars["params"], true );
			}
			
			$OutputString = whmp_smarty_template( $html_template, $vars );
			echo $OutputString;
		} else {
			echo $HTML;
		}
		break;
}
exit;
wp_die(); // this is required to return a proper result