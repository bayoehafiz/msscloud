<?php
if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
	$the_lang = ICL_LANGUAGE_CODE;
} else {
	$the_lang = get_locale();
}

// All shortcodes and their functions are defined in array to recognise
$whmp_shortcodes_list = [
	"whmpress_price"                => "whmpress_price_function",
	"whmpress_domain_price"         => "whmpress_domain_price_function",
	"whmpress_price_table"          => "whmpress_price_table_function",
	"whmpress_price_matrix"         => "whmpress_price_table_function",
	"whmpress_price_table_domain"   => "whmpress_price_table_domain_function",
	"whmpress_price_matrix_domain"  => "whmpress_price_table_domain_function",
	"whmpress_price_domain_list"    => "whmpress_price_domain_list_function",
	"whmpress_order_combo"          => "whmpress_order_combo_function",
	"whmpress_order_button"         => "whmpress_order_button_function",
	"whmpress_order_link"           => "whmpress_order_link_function",
	"whmpress_order_url"            => "whmpress_order_url_function",
	"whmpress_currency"             => "whmpress_currency_function",
	"whmpress_currency_combo"       => "whmpress_currency_combo_function",
	"whmpress_currency_combo_fancy" => "whmpress_currency_combo_fancy_function",
	"whmpress_name"                 => "whmpress_name_function",
	"whmpress_description"          => "whmpress_description_function",
	"whmpress_cdescription"         => "whmpress_cdescription_function",
	"whmpress_price_box"            => "whmpress_price_box_function",
	"whmpress_pricing_table"        => "whmpress_pricing_table_function",
	"whmpress_domain_search"        => "whmpress_domain_search_function",
	"whmpress_domain_search_ajax"   => "whmpress_domain_search_ajax_function",
	"whmpress_domain_search_ajax2"  => "whmpress_domain_search_ajax2_function",
	//"whmpress_domain_search_ajax_results" => "whmpress_domain_search_ajax_results_function",
	/*"whmpress_domain_search_extended_ajax" => "whmpress_domain_search_extended_ajax_function",
    "whmpress_domain_search_extended_ajax_results" => "whmpress_domain_search_extended_ajax_results_function",*/
	"whmpress_domain_search_bulk"   => "whmpress_domain_search_bulk_function",
	"whmpress_domain_whois"         => "whmpress_whois_function",
	
	"whmpress_login_form"    => "whmpress_login_form_function",
	"whmpress_url"           => "whmpress_url_function",
	"whmpress_announcements" => "whmpress_announcements_function",
	
	#"whmpress_geek" => "whmpress_geek_function",
	#"whmpress_cyearly" => "whmp_shortcode_yearly",
	#"whmpress_cmonthly" => "whmp_shortcode_cmonthly",
];


## Do not use these shortcodes in VC integration, Text editor and this shortcode will also disabled.
$donot_use = [
	"whmpress_price_table",
	"whmpress_price_table_domain",
	"whmpress_currency_combo_fancy",
	"whmpress_cdescription",
	"whmpress_price_domain_list"
];

## These shortcodes will work in background but will not include in VC integration and Text editor.
$donot_include_editors = [
	"whmpress_domain_search_ajax2",
];

// Adding all shortcodes mentioned in array above
foreach ( $whmp_shortcodes_list as $shortcode => $func ) {
	add_shortcode( $shortcode, $func );
}

function whmpress_announcements_function( $atts, $content = null ) {
	extract( shortcode_atts( [
		'count' => '3',
		'words' => '25',
	], $atts ) );
	
	if ( ! is_numeric( $count ) ) {
		$count = "3";
	}
	if ( $count < 1 ) {
		$count = "3";
	}
	
	if ( ! is_numeric( $words ) ) {
		$words = "25";
	}
	if ( $words < 1 ) {
		$words = "25";
	}
	
	global $wpdb;
	$table_name = $wpdb->prefix . "whmpress_announcements";
	$Q          = "SELECT * FROM `{$table_name}` WHERE (published='1' OR published='on') ORDER BY date DESC LIMIT 0,{$count}";
	$rows       = $wpdb->get_results( $Q );
	
	if ( is_plugin_active( 'WHMpress_Client_Area/client-area.php' ) ) {
		global $WHMPress_Client_Area;
		if ( empty( $WHMPress_Client_Area ) ) {
			$WHMPress_Client_Area = new WHMPress_Client_Area;
		}
		$blog_url = get_option( "client_area_page_url" );
		if ( is_numeric( $blog_url ) ) {
			$blog_url = get_page_link( $blog_url );
		}
		if ( $WHMPress_Client_Area->is_permalink() ) {
			$url = rtrim( $blog_url, "/" ) . "/whmpca/announcements/id/{{id}}/";
		} else {
			$url = $blog_url . "?whmpca=announcements&id={{id}}";
		}
	} else {
		$url = rtrim( get_option( "whmcs_url" ), "/" ) . "/announcements.php?id={{id}}";
	}
	
	$return_string = "";
	foreach ( $rows as $row ) {
		$row->announcement = explode( " ", $row->announcement );
		array_splice( $row->announcement, $words );
		$row->announcement = implode( " ", $row->announcement );
		
		$url = str_replace( "{{id}}", $row->id, $url );
		
		$return_string .= "<div class=\"whmpress_announcements\">
          <span class=\"announcement-date\">" . mysql2date( get_option( 'date_format' ), $row->date ) . " - </span>
          <a class=\"announcement-id\" href=\"{$url}\"><b>{$row->title}</b></a>
          <p class=\"announcement-summary\">" . $row->announcement . "</p>
        </div>\n";
	}
	
	return $return_string;
}

function whmpress_domain_price_function( $atts, $content = null ) {
	$OutputString = include( WHMP_PLUGIN_DIR . "/includes/shortcodes/whmpress_domain_price.php" );
	
	return $OutputString;
}

function whmpress_url_function( $atts, $content = null ) {
	extract( shortcode_atts( [
		'type' => 'client_area'
	], $atts ) );
	
	
	if ( $type == "" ) {
		$type = "client_area";
	}
	$html = include( WHMP_PLUGIN_DIR . "/includes/shortcodes/whmpress_url.php" );
	
	return $html;
}

function whmpress_login_form_function( $atts, $content = null ) {
	/**
	 * Return WHMCS registration form
	 *
	 * List of parameters
	 * html_class = HTML class for container
	 * html_id = HTML id for container
	 * button_class = HTML class for button
	 * button_text = Button text
	 */
	
	extract( shortcode_atts( [
		'html_template' => '',
		'image'         => '',
		'button_class'  => '',
		'button_text'   => "Login",
		'html_id'       => '',
		'html_class'    => 'whmpress whmpress_login_form',
	], $atts ) );
	
	# Checking parameters
	#$button_class = isset($atts["button_class"])?$atts["button_class"]:"";
	#$html_class = isset($atts["html_class"])?$atts["html_class"]:""; if ($html_class=="") $html_class = "whmpress whmpress_login_form";
	#$html_id = isset($atts["html_id"])?$atts["html_id"]:"";
	#$button_text = !empty($atts["button_text"])?$atts["button_text"]:"Login";
	
	# Generating output form
	$WHMPress = new WHMpress();
	
	$html_template = $WHMPress->check_template_file( $html_template, "whmpress_login_form" );
	
	if ( is_file( $html_template ) ) {
		$vars = [
			"action_url"     => $WHMPress->get_whmcs_url( "loginurl" ),
			"username_field" => "<label>Username:</lable> <input type='text' name='username' >",
			"password_field" => "<label>Password:</lable> <input type='password' name='password' >",
			"button"         => "<input type='submit' value='" . __( $button_text, "whmpress" ) . "'>",
			"button_text"    => __( $button_text, "whmpress" ),
		];
		
		# Getting custom fields and adding in output
		$TemplateArray = $WHMPress->get_template_array( "whmpress_login_form" );
		foreach ( $TemplateArray as $custom_field ) {
			$vars[ $custom_field ] = isset( $atts[ $custom_field ] ) ? $atts[ $custom_field ] : "";
		}
		
		$OutputString = whmp_smarty_template( $html_template, $vars );
		
		return $OutputString;
	} else {
		$str = '<form method="post" action="' . $WHMPress->get_whmcs_url( "loginurl" ) . '">' . "\n";
		$str .= '<div><label>' . __( "Email address", 'whmpress' ) . '</label><input placeholder="' . __( "Email address", 'whmpress' ) . '" type="text" name="username" /></div>' . "\n";
		$str .= '<div><label>' . __( "Password", 'whmpress' ) . '</label><input placeholder="' . __( "Password", 'whmpress' ) . '" type="password" name="password" /></div>' . "\n";
		$str .= '<button class="search_btn ' . $button_class . '">' . __( $button_text, "whmpress" ) . '</button>' . "\n";
		$str .= "</form>\n";
		
		$ID    = ! empty( $html_id ) ? "id='$html_id'" : "";
		$CLASS = ! empty( $html_class ) ? "class='$html_class'" : "";
		
		return "<div $CLASS $ID>" . $str . "</div>";
	}
}

function whmpress_whois_function( $atts ) {
	/**
	 * Displays a form for getting whois info of multiple line provided domains
	 *
	 * List of parameters
	 * text_class = HTML class for input search text box
	 * button_class = HTML class for submit button of form
	 * html_class = HTML class for wrapper
	 * html_id = HTML id for wrapper
	 * placeholder = placeholder text for input search textbox
	 * button_text = Search button text
	 * result_text = HTML class for output result
	 */
	
	extract( shortcode_atts( [
		'html_template'     => '',
		'image'             => '',
		'text_class'        => '',
		'button_class'      => '',
		'html_class'        => 'whmpress whmpress_domain_whois',
		'html_id'           => '',
		'placeholder'       => whmpress_get_option( 'dw_placeholder' ),
		'button_text'       => whmpress_get_option( 'dw_button_text' ), //'Get Whois',
		'result_text_class' => ''
	], $atts ) );
	
	# Generating output result
	if ( isset( $_POST["whois_domain"] ) ) {
		include_once( WHMP_PLUGIN_DIR . "/includes/whois.class.php" );
		$whois = new Whois;
		
		$tld = strstr( $_POST["whois_domain"], '.' );
		
		$result = "<div class='whmpress_whois_results'><pre class='$result_text_class'>" . $whois->whoislookup( $_POST["whois_domain"], $tld, true ) . "</pre></div>";
	} else {
		$result = __( "No domain selected", "whmpress" );
	}
	
	$WHMPress = new WHMPress;
	
	$html_template = $WHMPress->check_template_file( $html_template, "whmpress_domain_whois" );
	
	if ( is_file( $html_template ) ) {
		$whois_domain = isset( $_POST["whois_domain"] ) ? $_POST["whois_domain"] : "";
		$vars         = [
			"search_text_box" => '<input required="required" class="' . $text_class . '" placeholder="' . __( $placeholder, "whmpress" ) . '" name="whois_domain" value="' . $whois_domain . '">',
			"search_button"   => '<button class="search_btn ' . $button_class . '">' . __( $button_text, "whmpress" ) . '</button>',
			"whois_output"    => $result
		];
		
		# Getting custom fields and adding in output
		$TemplateArray = $WHMPress->get_template_array( "whmpress_domain_whois" );
		foreach ( $TemplateArray as $custom_field ) {
			$vars[ $custom_field ] = isset( $atts[ $custom_field ] ) ? $atts[ $custom_field ] : "";
		}
		
		$OutputString = whmp_smarty_template( $html_template, $vars );
		
		return $OutputString;
	} else {
		$str          = '<form method="post">' . "\n";
		$whois_domain = isset( $_POST["whois_domain"] ) ? $_POST["whois_domain"] : "";
		$str .= '<input required="required" class="' . $text_class . '" placeholder="' . __( $placeholder, "whmpress" ) . '" name="whois_domain" value="' . $whois_domain . '">' . "\n";
		$str .= '<button class="search_btn ' . $button_class . '">' . $button_text . '</button>' . "\n";
		$str .= "</form>\n";
		
		$ID    = ! empty( $html_id ) ? "id='$html_id'" : "";
		$CLASS = ! empty( $html_class ) ? "class='$html_class'" : "";
		
		return "<div $CLASS $ID>" . $str . $result . "</div>";
	}
}

function whmpress_domain_search_bulk_function( $atts ) {
	$OutputString = include( WHMP_PLUGIN_DIR . "/includes/shortcodes/whmpress_domain_search_bulk.php" );
	
	return $OutputString;
}


function whmpress_domain_search_extended_ajax_function( $atts ) {
	/**
	 * Displays a form for getting whois info of domains
	 *
	 * List of parameters
	 * action = Action url for form. Form will submit on this url
	 * text_class = HTML class for input search text box
	 * button_class = HTML class for submit button of form
	 * html_class = HTML class for wrapper
	 * html_id = HTML id for wrapper
	 * placeholder = placeholder text for input search textbox
	 * button_text = Search button text
	 */
	
	extract( shortcode_atts( [
		'html_template'           => '',
		'image'                   => '',
		'text_class'              => '',
		'button_class'            => '',
		'action'                  => '',
		'html_class'              => 'whmpress whmpress_domain_search_ajax',
		'html_id'                 => '',
		'placeholder'             => 'Search',
		'button_text'             => 'Search',
		"whois_link"              => "yes",
		"www_link"                => "yes",
		"disable_domain_spinning" => whmpress_get_option( "dsa_disable_domain_spinning" ),
		"order_landing_page"      => whmpress_get_option( "dsa_order_landing_page" ),
		"show_price"              => whmpress_get_option( "dsa_show_price" ),
		"show_years"              => whmpress_get_option( "dsa_show_years" ),
	], $atts ) );
	
	# Generating output form
	$WHMPress = new WHMPress;
	
	if ( strtoupper( $order_landing_page ) == "YES" || $order_landing_page == "1" ) {
		$order_landing_page = "1";
	} else {
		$order_landing_page = "0";
	}
	if ( strtoupper( $disable_domain_spinning ) == "YES" || $disable_domain_spinning == "1" ) {
		$disable_domain_spinning = "1";
	} else {
		$disable_domain_spinning = "0";
	}
	if ( strtoupper( $show_price ) == "NO" || $show_price == "0" ) {
		$show_price = "0";
	} else {
		$show_price = "1";
	}
	if ( strtoupper( $show_years ) == "NO" || $show_years == "0" ) {
		$show_years = "0";
	} else {
		$show_years = "1";
	}
	
	$html_template = $WHMPress->check_template_file( $html_template, "whmpress_domain_search_extended_ajax" );
	
	if ( is_file( $html_template ) ) {
		$search_domain = isset( $_GET["search_domain"] ) ? $_GET["search_domain"] : "";
		$vars          = [
			"search_text_box" => '<input required="required" class="' . $text_class . '" placeholder="' . __( $placeholder, "whmpress" ) . '" value="' . $search_domain . '" type="search" name="search_domain" />',
			"search_button"   => '<button class="search_btn ' . $button_class . '">' . __( $button_text, "whmpress" ) . '</button>',
			"action"          => $action
		];
		
		# Getting custom fields and adding in output
		$TemplateArray = $WHMPress->get_template_array( "whmpress_domain_search_extended_ajax" );
		foreach ( $TemplateArray as $custom_field ) {
			$vars[ $custom_field ] = isset( $atts[ $custom_field ] ) ? $atts[ $custom_field ] : "";
		}
		
		$OutputString = whmp_smarty_template( $html_template, $vars );
		
		return $OutputString;
	} else {
		$ajaxID = uniqid( "ajaxForm" );
		$ACTION = empty( $action ) ? "" : "action='$action'";
		$str    = "<form $ACTION method='get' id='form{$ajaxID}'";
		if ( $action == "" ) {
			$str .= " onsubmit='return Search{$ajaxID}(this);'";
		}
		$str .= '>' . "\n";
		if ( $action == "" ) {
			foreach ( $_GET as $k => $v ) {
				if ( $k <> "search_domain" ) {
					$str .= "<input type='hidden' name='$k' value=\"$v\" />\n";
				}
			}
		}
		$str .= '<input type="hidden" name="skip_extra" value="0" />';
		$search_domain = isset( $_GET["search_domain"] ) ? $_GET["search_domain"] : "";
		$str .= '<input required="required" class="' . $text_class . '" placeholder="' . __( $placeholder, "whmpress" ) . '" value="' . $search_domain . '" type="search" id="search_box" name="search_domain" />' . "\n";
		$str .= '<button class="search_btn ' . $button_class . '">' . __( $button_text, "whmpress" ) . '</button>' . "\n";
		$str .= "<div class='clear:both'></div>";
		$str .= "</form>\n";
		
		$str .= "<script>
            jQuery(function(){
                /*jQuery.post(WHMPAjax.ajaxurl, {'domain':jQuery('#search_box').val(),'action':'whmpress_action','do':'getDomainData','skip_extra':'0','searchonly':'{$searchonly}','lang':'" . $the_lang . "'}, function(data){
                    jQuery(\"#search_result_div\").html(data);
                });*/
                jQuery(document).on('click', \"#load-more-div button\", function () {
                    jQuery(\"#load-more-div\").remove();
                    //jQuery(\"#search_result_div\").append('<div id=\"waiting_div\" style=\"font-size:30px;text-align: center;\"><i class=\"icon-spinner fa-spin\"></i></div>');
                    jQuery(\".result-div\").append('<div id=\"waiting_div\" style=\"font-size:30px;text-align: center;\"><i class=\"fa fa-spinner fa-spin whmp_domain_search_ajax_results_spinner\"></i></div>');
                    whmp_page++;
                    jQuery.post(WHMPAjax.ajaxurl, {'domain':jQuery('#search_box').val(),'action':'whmpress_action','do':'loadWhoisPage','skip_extra':'0','page':whmp_page,'searchonly':'{$searchonly}','lang':'" . $the_lang . "'}, function(data){
                        jQuery(\"#waiting_div\").remove();
                        jQuery(\".result-div\").append(data);
                    });
                });
            });
            </script>\n";
		
		if ( $action == "" ) {
			$str .= "<div id='$ajaxID'>";
			$str .= whmpress_domain_search_extended_ajax_results_function( [
				"searchonly"              => "*",
				'html_class'              => 'whmpress whmpress_domain_search_ajax_results',
				'html_id'                 => "{$ajaxID}2",
				'whois_link'              => $whois_link,
				"www_link"                => $www_link,
				"disable_domain_spinning" => $disable_domain_spinning,
			] );
			$str .= "</div>";
			$str .= "<script>
            function Search{$ajaxID}(form) {
                whmp_page=1;
                jQuery('#{$ajaxID}2').html('<div class=\"whmpress_loading_div\"><i class=\"fa fa-spinner fa-spin whmp_domain_search_ajax_results_spinner\"></i> Loading .....</div>');
                jQuery.post(WHMPAjax.ajaxurl,{'show_price':'$show_price','show_years':'$show_years','order_landing_page':'$order_landing_page','disable_domain_spinning':'$disable_domain_spinning','domain':jQuery('#form{$ajaxID} input[type=search]').val(),'action':'whmpress_action','do':'getDomainData','www_link':'$www_link','whois_link':'$whois_link','searchonly':'*','skip_extra':'0','page':'1','lang':'" . $the_lang . "'},function(data){
                    jQuery('#{$ajaxID}2').html(data);
                });
                return false;
            }
            </script>";
		}
		
		# Returning output form
		$ID    = ! empty( $html_id ) ? "id='$html_id'" : "";
		$CLASS = ! empty( $html_class ) ? "class='$html_class'" : "";
		
		return "<div $CLASS $ID>" . $str . "</div>";
	}
}


function whmpress_domain_search_ajax_function( $atts ) {
	return include( WHMP_PLUGIN_DIR . "/includes/shortcodes/whmpress_domain_search_ajax.php" );
}

function whmpress_domain_search_ajax2_function( $atts ) {
	return include( WHMP_PLUGIN_DIR . "/includes/shortcodes/whmpress_domain_search_ajax2.php" );
}

function whmpress_domain_search_extended_ajax_results_function( $atts ) {
	/**
	 * Displays whois search result submitted by whmpress_domain_search_extended_ajax form
	 *
	 * List of parameters
	 * searchonly = * for all domain or get result only for specific extensions comma seperated
	 * html_class = HTML class for wrapper
	 */
	
	$params = shortcode_atts( [
		'html_template' => '',
		'images'        => '',
		'searchonly'    => '*',
		'html_class'    => 'whmpress whmpress_domain_search_ajax_results',
		'html_id'       => '',
	], $atts );
	extract( $params );
	
	$WHMPress = new WHMPress;
	
	$html_template = $WHMPress->check_template_file( $html_template, "whmpress_domain_search_extended_ajax_results" );
	
	# Instructions: creative a div with ID search_result_div in HTML template file
	if ( is_file( $html_template ) ) {
		$vars = [];
		
		# Getting custom fields and adding in output
		$TemplateArray = $WHMPress->get_template_array( "whmpress_domain_search_extended_ajax_results" );
		foreach ( $TemplateArray as $custom_field ) {
			$vars[ $custom_field ] = isset( $atts[ $custom_field ] ) ? $atts[ $custom_field ] : "";
		}
		
		$OutputString = whmp_smarty_template( $html_template, $vars );
		
		return $OutputString;
	} else {
		if ( isset( $_GET["search_domain"] ) ) {
			$str = '<div id="search_result_div">' . "\n";
			$str .= '<div style="font-size:30px;text-align: center;">' . "\n";
			$str .= '<i class="fa fa-spinner fa-spin whmp_domain_search_ajax_results_spinner"></i>' . "\n";
			$str .= "</div>\n";
			$str .= "</div>\n";
			
			if ( isset( $_GET["ext"] ) ) {
				$_GET["search_domain"] .= $_GET["ext"];
			}
			
			$str .= "<script>
            jQuery(function(){
                /*jQuery.post(WHMPAjax.ajaxurl, {'domain':'{$_GET["search_domain"]}','action':'whmpress_action','do':'getDomainData','skip_extra':'$search_extensions','searchonly':'{$searchonly}','lang':'" . $the_lang . "'}, function(data){
                    jQuery(\"#search_result_div\").html(data);
                });*/
                jQuery(document).on('click', \"#load-more-div button\", function () {
                    jQuery(\"#load-more-div\").remove();
                    //jQuery(\"#search_result_div\").append('<div id=\"waiting_div\" style=\"font-size:30px;text-align: center;\"><i class=\"icon-spinner fa-spin\"></i></div>');
                    jQuery(\".result-div\").append('<div id=\"waiting_div\" style=\"font-size:30px;text-align: center;\"><i class=\"fa fa-spinner fa-spin whmp_domain_search_ajax_results_spinner\"></i></div>');
                    whmp_page++;
                    jQuery.post(WHMPAjax.ajaxurl, {'params':" . whmpress_json_encode( $params ) . ",'domain':'{$_GET["search_domain"]}','action':'whmpress_action','do':'loadWhoisPage','skip_extra':'$search_extensions','page':whmp_page,'searchonly':'{$searchonly}','lang':'" . $the_lang . "'}, function(data){
                        jQuery(\"#waiting_div\").remove();
                        jQuery(\".result-div\").append(data);
                    });
                });
            });
            </script>\n";
		} else {
			$str = "";
		}
		
		$ID    = ! empty( $html_id ) ? "id='$html_id'" : "";
		$CLASS = ! empty( $html_class ) ? "class='$html_class'" : "";
		
		return "<div $CLASS $ID>" . $str . "</div>";
	}
}


function whmpress_domain_search_ajax_results_function( $atts ) {
	return include( WHMP_PLUGIN_DIR . "/includes/shortcodes/whmpress_domain_search_ajax_results.php" );
}

function whmpress_pricing_table_function( $atts, $content = null ) {
	/**
	 * Shows pricing table
	 *
	 * List of parameters
	 * html_template = HTML template file path
	 * html_class = HTML class for wrapper
	 * html_id = HTML id for wrapper
	 * id = relid match from whmcs mysql table
	 * billingcycle = Billing cycle e.g. annually, monthly etc.
	 * show_price = Display price or not.
	 * show_combo = Show combo or not, No, Yes
	 * show_button = Show submit button or not
	 * currency = Currency for price
	 */
	$result = include( WHMP_PLUGIN_DIR . "/includes/shortcodes/whmpress_pricing_table.php" );
	
	return $result;
}

function whmpress_price_box_function( $atts ) {
	/**
	 * Shows price box
	 *
	 * List of parameters
	 * html_class = HTML class for wrapper
	 * html_id = HTML id for wrapper
	 * id = relid match from whmcs mysql table
	 * billingcycle = Billing cycle e.g. annually, monthly etc.
	 * show_price = Display price or not.
	 * show_combo = Show combo or not, No, Yes
	 * show_button = Show submit button or not
	 * currency = Currency for price
	 */
	
	extract( shortcode_atts( [
		'html_template'    => '',
		'image'            => '',
		'id'               => '0',
		'billingcycle'     => whmpress_get_option( "pt_billingcycle" ),
		'show_price'       => whmpress_get_option( "pt_show_price" ),
		'show_combo'       => whmpress_get_option( "pt_show_combo" ),
		'show_button'      => whmpress_get_option( "pt_show_button" ),
		'currency'         => '0',
		"button_text"      => whmpress_get_option( "pt_button_text" ),
		"show_description" => "yes",
		'html_id'          => '',
		'html_class'       => 'whmpress whmpress_price_box',
		'show_discount'    => whmpress_get_option( 'combo_show_discount' ),
		'discount_type'    => whmpress_get_option( 'combo_discount_type' ),
		//"button_html_template" => "",
	], $atts ) );
	$currency    = whmp_get_currency( $currency );
	$button_text = __( $button_text, "whmpress" );
	
	# Getting data from MySQL
	global $wpdb;
	$Q   = "SELECT `name`,`description` FROM `" . whmp_get_products_table_name() . "` WHERE `id`=$id";
	$row = $wpdb->get_row( $Q, ARRAY_A );
	if ( isset( $row["name"] ) ) {
		$row["name"] = whmpress_encoding( $row["name"] );
	}
	
	# Check if price is requested or not
	if ( strtolower( $show_price ) == "yes" ) {
		$price = whmpress_price_function( [ "id" => $id, "billingcycle" => $billingcycle, "currency" => $currency ] );
	} else {
		$price = "";
	}
	$simple_price = $price;
	
	
	# Setting up description
	$description = trim( strip_tags( $row["description"] ), "\n" );
	$description = explode( "\n", $description );
	$description = "<ul>\n<li>" . implode( "</li><li>", $description ) . "</li>\n</ul>";
	$description = whmpress_encoding( $description );
	
	# Check if combo is requested or not
	if ( strtolower( $show_combo ) == "yes" ) {
		$combo = whmpress_order_combo_function( [
			"id"            => $id,
			"show_button"   => "No",
			"currency"      => $currency,
			"discount_type" => $discount_type,
			"show_discount" => $show_discount,
		] );
	} else {
		$combo = "";
	}
	
	# Check if button is requested or not
	if ( strtolower( $show_button ) == "yes" ) {
		$button = whmpress_order_button_function( [
			"id"          => $id,
			"button_text" => $button_text
		] );   // "html_template"=>$button_html_template
	} else {
		$button = "";
	}
	
	# Generating OutputString
	$WHMPress = new WHMPress;
	
	$html_template = $WHMPress->check_template_file( $html_template, "whmpress_price_box" );
	
	if ( is_file( $html_template ) ) {
		$decimal_sperator = get_option( 'decimal_replacement', "." );
		$price1           = whmpress_price_function( [
			"show_duration" => "no",
			"id"            => $id,
			"billingcycle"  => $billingcycle,
			"currency"      => $currency,
			"prefix"        => "",
			"suffix"        => ""
		] );
		$totay            = explode( $decimal_sperator, strip_tags( $price1 ) );
		$fraction         = isset( $totay[1] ) ? $totay[1] : "";
		$amount1          = $totay[0];
		
		$duration = explode( "/", strip_tags( $simple_price ) );
		$duration = isset( $duration[1] ) ? $duration[1] : "";
		
		$vars = [
			"product_name"        => $row["name"],
			"product_price"       => $price1,
			"product_description" => $description,
			"order_combo"         => $combo,
			"order_button"        => $button,
			"button_text"         => $button_text,
			"order_button_text"   => $button_text,
			"image"               => $image,
			"prefix"              => whmp_get_currency_prefix( $currency ),
			"suffix"              => whmp_get_currency_suffix( $currency ),
			"amount"              => $amount1,
			"fraction"            => $fraction,
			"decimal"             => $decimal_sperator,
			"duration"            => $duration,
		];
		
		# Getting custom fields and adding in output
		$TemplateArray = $WHMPress->get_template_array( "whmpress_price_box" );
		foreach ( $TemplateArray as $custom_field ) {
			$vars[ $custom_field ] = isset( $atts[ $custom_field ] ) ? $atts[ $custom_field ] : "";
		}
		
		$OutputString = whmp_smarty_template( $html_template, $vars );
		
		return $OutputString;
	} else {
		$OutputString = "<h3>" . $row["name"] . "</h3>";
		
		$OutputString .= "<div class='style1_wrapper'>";
		if ( strtolower( $show_description ) == "yes" ) {
			$OutputString .= "<div style='float:left' class='style1_left'>
                    $description
                </div>";
		}
		$OutputString .= "
            <div style='float:right' class='style1_right'>
                <h2>$price</h2>
                $combo
                $button
            </div>
            <div style='clear:both'></div>
        </div>";
		
		# Returning output string with wrapper div
		$ID    = ! empty( $html_id ) ? "id='$html_id'" : "";
		$CLASS = ! empty( $html_class ) ? "class='$html_class'" : "";
		
		return "<div $CLASS $ID>" . $OutputString . "</div>";
	}
}

function whmpress_description_function( $atts ) {
	/**
	 * Shows service description
	 *
	 * List of parameters
	 * html_class = HTML class for wrapper
	 * html_id = HTML id for wrapper
	 * id = relid match from whmcs mysql table
	 * show_as = display result in ul, ol
	 */
	
	extract( shortcode_atts( [
		'html_template' => '',
		'image'         => '',
		'html_id'       => '',
		'html_class'    => 'whmpress whmpress_description',
		'id'            => '0',
		'show_as'       => whmpress_get_option( 'dsc_description' ),
		'no_wrapper'    => "No",
	], $atts ) );
	
	# Getting data from mysql tables
	global $wpdb;
	$WHMPress = new WHMPress;
	
	$field = "whmpress_product_" . $id . "_desc_" . $WHMPress->get_current_language();
	$v     = get_option( $field );
	if ( empty( $v ) ) {
		$Q            = "SELECT `description` FROM `" . whmp_get_products_table_name() . "` WHERE `id`=$id";
		$ndescription = $description = $wpdb->get_var( $Q );
	} else {
		$ndescription = $description = get_option( $field );
	}
	
	$ndescription = trim( strip_tags( $ndescription ), "\n" );
	$ndescription = explode( "\n", $ndescription );
	
	$smarty_array = [];
	foreach ( $ndescription as $line ) {
		if ( trim( $line ) <> "" ) {
			$data                  = [];
			$data["feature"]       = $line;
			$totay                 = explode( ":", $line );
			$data["feature_title"] = trim( $totay[0] );
			$data["feature_value"] = isset( $totay[1] ) ? trim( $totay[1] ) : "";
			
			$smarty_array[] = $data;
		}
	}
	
	# Checking show_as parameter
	if ( strtolower( $show_as ) == "ul" || strtolower( $show_as ) == "ol" ) {
		$description = trim( strip_tags( $description ), "\n" );
		$description = explode( "\n", $description );
		$description = "<" . $show_as . ">\n<li>" . implode( "</li><li>", $description ) . "</li>\n</" . $show_as . ">";
	}
	
	# Generating output string
	$html_template = $WHMPress->check_template_file( $html_template, "whmpress_description" );
	
	if ( is_file( $html_template ) ) {
		$vars = [
			"product_description" => $description,
			"data"                => $smarty_array,
		];
		
		# Getting custom fields and adding in output
		$TemplateArray = $WHMPress->get_template_array( "whmpress_description" );
		foreach ( $TemplateArray as $custom_field ) {
			$vars[ $custom_field ] = isset( $atts[ $custom_field ] ) ? $atts[ $custom_field ] : "";
		}
		
		$OutputString = whmp_smarty_template( $html_template, $vars );
		
		return $OutputString;
	} else {
		$ID    = ! empty( $html_id ) ? "id='$html_id'" : "";
		$CLASS = ! empty( $html_class ) ? "class='$html_class'" : "";
		
		$no_wrapper = trim( strtolower( $no_wrapper ) );
		if ( $no_wrapper == "yes" || $no_wrapper == "1" || $no_wrapper === true || $no_wrapper == "true" ) {
			$OutputString = $description;
		} else {
			$OutputString = "<div $CLASS $ID>" . $description . "</div>";
		}
		
		# Returning output string
		return whmpress_encoding( $OutputString );
	}
}

function whmpress_cdescription_function( $atts ) {
	/**
	 * Shows service description
	 *
	 * List of parameters
	 * html_class = HTML class for wrapper
	 * html_id = HTML id for wrapper
	 * id = relid match from whmcs mysql table
	 * show_as = display result in ul, ol
	 */
	
	extract( shortcode_atts( [
		'html_template' => '',
		'image'         => '',
		'html_id'       => '',
		'html_class'    => 'whmpress whmpress_description',
		'id'            => '0',
		'show_as'       => whmpress_get_option( 'dsc_description' ),
		"no_wrapper"    => "No"
	], $atts ) );
	
	# Getting data from mysql tables
	$WHMPress = new WHMPress;
	
	$field        = "whmpress_product_" . $id . "_custom_desc_" . $WHMPress->get_current_language();
	$ndescription = $description = get_option( $field );
	
	$ndescription = trim( strip_tags( $ndescription ), "\n" );
	$ndescription = explode( "\n", $ndescription );
	
	$smarty_array = [];
	foreach ( $ndescription as $line ) {
		if ( trim( $line ) <> "" ) {
			$data                  = [];
			$data["feature"]       = $line;
			$totay                 = explode( ":", $line );
			$data["feature_title"] = trim( $totay[0] );
			$data["feature_value"] = isset( $totay[1] ) ? trim( $totay[1] ) : "";
			
			$smarty_array[] = $data;
		}
	}
	
	$no_wrapper = trim( strtolower( $no_wrapper ) );
	# Checking show_as parameter
	if ( $no_wrapper == "yes" || $no_wrapper == "1" || $no_wrapper === true || $no_wrapper == "true" ) {
		// No wrapper arround description text.
	} else {
		if ( strtolower( $show_as ) == "ul" || strtolower( $show_as ) == "ol" ) {
			$description = trim( strip_tags( $description ), "\n" );
			$description = explode( "\n", $description );
			$description = "<" . $show_as . ">\n<li>" . implode( "</li><li>", $description ) . "</li>\n</" . $show_as . ">";
		}
	}
	
	# Generating output string
	$html_template = $WHMPress->check_template_file( $html_template, "whmpress_description" );
	
	if ( is_file( $html_template ) ) {
		$vars = [
			"product_description" => $description,
			"data"                => $smarty_array,
		];
		
		# Getting custom fields and adding in output
		$TemplateArray = $WHMPress->get_template_array( "whmpress_description" );
		foreach ( $TemplateArray as $custom_field ) {
			$vars[ $custom_field ] = isset( $atts[ $custom_field ] ) ? $atts[ $custom_field ] : "";
		}
		
		$OutputString = whmp_smarty_template( $html_template, $vars );
		
		return $OutputString;
	} else {
		$ID    = ! empty( $html_id ) ? "id='$html_id'" : "";
		$CLASS = ! empty( $html_class ) ? "class='$html_class'" : "";
		
		if ( $no_wrapper == "yes" || $no_wrapper == "1" || $no_wrapper === true || $no_wrapper == "true" ) {
			$OutputString = $description;
		} else {
			$OutputString = "<div $CLASS $ID>" . $description . "</div>";
		}
		
		# Returning output string
		return whmpress_encoding( $OutputString );
	}
}

function whmpress_name_function( $atts ) {
	/**
	 * Shows service name
	 *
	 * List of parameters
	 * html_class = HTML class for wrapper
	 * html_id = HTML id for wrapper
	 * id = relid match from whmcs mysql table
	 */
	
	extract( shortcode_atts( [
		'html_template' => '',
		'image'         => '',
		'html_id'       => '',
		'html_class'    => '',
		'id'            => '',
		'no_wrapper'    => '',
	], $atts ) );
	
	$WHMPress = new WHMPress;
	
	global $wpdb;
	if ( strtolower( get_option( "whmpress_use_package_details_from_whmpress" ) ) == "yes" ) {
		$name = get_option( "whmpress_product_" . $id . "_name_" . $WHMPress->get_current_language() );
		if ( empty( $name ) ) {
			$Q    = "SELECT `name` FROM `" . whmp_get_products_table_name() . "` WHERE `id`=$id";
			$name = $wpdb->get_var( $Q );
		}
	} else {
		# Getting data from mysql tables
		$Q    = "SELECT `name` FROM `" . whmp_get_products_table_name() . "` WHERE `id`=$id";
		$name = $wpdb->get_var( $Q );
	}
	
	# Generating output string
	
	$html_template = $WHMPress->check_template_file( $html_template, "whmpress_name" );
	
	if ( is_file( $html_template ) ) {
		$vars = [
			"product_name" => $name
		];
		
		# Getting custom fields and adding in output
		$TemplateArray = $WHMPress->get_template_array( "whmpress_name" );
		foreach ( $TemplateArray as $custom_field ) {
			$vars[ $custom_field ] = isset( $atts[ $custom_field ] ) ? $atts[ $custom_field ] : "";
		}
		
		$OutputString = whmp_smarty_template( $html_template, $vars );
		
		return $OutputString;
	} else {
		$ID    = ! empty( $html_id ) ? "id='$html_id'" : "";
		$CLASS = ! empty( $html_class ) ? "class='$html_class'" : "";
		
		$no_wrapper = trim( strtolower( $no_wrapper ) );
		if ( $no_wrapper == "yes" || $no_wrapper == "1" || $no_wrapper === true ) {
			$OutputString = $name;
		} else {
			$OutputString = "<div $CLASS $ID>" . $name . "</div>";
		}
		
		# Returning output string
		return whmpress_encoding( $OutputString );
	}
}

function whmpress_price_function( $atts, $content = null ) {
	$OutputString = include WHMP_PLUGIN_DIR . "/includes/shortcodes/whmpress_price.php";
	
	return $OutputString;
}


function whmpress_order_combo_function( $atts, $content = null ) {
	/**
	 * Displays price of a WHMCS Service (Hosting Plan) for all billing cycle in dropdown list along with optional order button. Service id is the only required parameters
	 *
	 * List of parameters
	 * decimals = round number of decimals for price/amount
	 * id = relid from mysql pricing table
	 * currency = provide currency code
	 * rows = How many rows for combo and button 1 or 2
	 * show_discount = Whether to show discount or not
	 * show_button = Show HTML button or not yes or no
	 * button_text = HTML button text
	 * combo_class = HTML class name for combo
	 * button_class = HTML class for button
	 * html_id = html id name for wrapper
	 * html_class = HTML class name for wrapper
	 * discount_type = What discout type required. yearly, monthly, quarterly etc.
	 * billingcycles = What columns will display in combo e.g. yearly,monthly,biennially,semiannually,quarterly
	 * prefix = show currency prefix, yes for show prefix
	 * suffix = show currency suffix, yes for show suffix
	 */
	
	$OutputString = include WHMP_PLUGIN_DIR . "/includes/shortcodes/whmpress_order_combo.php";
	
	return $OutputString;
}

function whmpress_order_button_function( $atts ) {
	$OutputString = include WHMP_PLUGIN_DIR . "/includes/shortcodes/whmpress_order_button.php";
	
	return $OutputString;
}

function whmpress_order_link_function( $atts ) {
	/**
	 * Displays order link
	 *
	 * List of parameters
	 * link_text = Link text
	 * html_class = HTML class for link
	 * id = WHMCS product ID from mysql table
	 * billingcycle = Billing cycle e.g. annually
	 * html_id = HTML id for link
	 * currency = Currency ID
	 */
	
	extract( shortcode_atts( [
		'html_template' => '',
		'image'         => '',
		'link_text'     => whmpress_get_option( "Link Text" ),
		'html_class'    => 'whmpress_order_link',
		'id'            => '0',
		'billingcycle'  => 'annually',
		'html_id'       => '',
		'currency'      => ''
	], $atts ) );
	$value    = $link_text;
	$class    = $html_class;
	$currency = whmp_get_currency( $currency );
	
	$WHMPress = new WHMpress();
	
	# Generating URL.    
	if ( is_plugin_active( 'WHMpress_Client_Area/client-area.php' ) ) {
		global $WHMPress_Client_Area;
		if ( empty( $WHMPress_Client_Area ) ) {
			$WHMPress_Client_Area = new WHMPress_Client_Area;
		}
		if ( $WHMPress_Client_Area->is_permalink() ) {
			$url = $WHMPress->get_whmcs_url( "order" ) . "/pid/{$id}/a/add/currency/{$currency}/billingcycle/{$billingcycle}/";
		} else {
			$url = $WHMPress->get_whmcs_url( "order" ) . "pid={$id}&a=add&currency={$currency}&billingcycle={$billingcycle}";
		}
	} else {
		$url = $WHMPress->get_whmcs_url( "order" ) . "pid={$id}&a=add&currency={$currency}&billingcycle={$billingcycle}";
	}
	
	
	# Generating output string.
	$WHMPress = new WHMPress;
	
	$html_template = $WHMPress->check_template_file( $html_template, "whmpress_order_link" );
	
	if ( is_file( $html_template ) ) {
		
		$vars = [
			"product_order_url"  => $url,
			"product_link_text"  => $link_text,
			"product_order_link" => "<a href=\"{$url}\">{$link_text}</a>",
		];
		
		# Getting custom fields and adding in output
		$TemplateArray = $WHMPress->get_template_array( "whmpress_order_link" );
		foreach ( $TemplateArray as $custom_field ) {
			$vars[ $custom_field ] = isset( $atts[ $custom_field ] ) ? $atts[ $custom_field ] : "";
		}
		
		$OutputString = whmp_smarty_template( $html_template, $vars );
		
		return $OutputString;
	} else {
		$str = "<a id='$html_id' class=\"{$class}\" href=\"{$url}\">{$value}</a>";
		
		# Returning output string
		return $str;
	}
}


function whmpress_order_url_function( $atts ) {
	/**
	 * Display/Generate order url
	 *
	 * List of parameters
	 * id = WHMCS product ID from mysql table
	 * billingcycle = Billing cycle e.g. annually
	 * currency = Currency ID
	 */
	extract( shortcode_atts( [
		'html_template' => '',
		'id'            => whmp_get_installation_url(),
		'billingcycle'  => 'annually',
		'currency'      => ''
	], $atts ) );
	$currency = whmp_get_currency( $currency );
	
	$WHMPress = new WHMpress();
	
	# Generating order url and making output string    
	if ( is_plugin_active( 'WHMpress_Client_Area/client-area.php' ) ) {
		global $WHMPress_Client_Area;
		if ( empty( $WHMPress_Client_Area ) ) {
			$WHMPress_Client_Area = new WHMPress_Client_Area;
		}
		if ( $WHMPress_Client_Area->is_permalink() ) {
			$str = $WHMPress->get_whmcs_url( "order" ) . "/a/add/pid/" . $id . "/currency/" . $currency . "/billingcycle/{$billingcycle}/";
		} else {
			$str = $WHMPress->get_whmcs_url( "order" ) . "a=add&pid=" . $id . "&currency=" . $currency . "&billingcycle={$billingcycle}";
		}
	} else {
		$str = $WHMPress->get_whmcs_url( "order" ) . "a=add&pid=" . $id . "&currency=" . $currency . "&billingcycle={$billingcycle}";
	}
	
	$html_template = $WHMPress->check_template_file( $html_template, "whmpress_order_url" );
	if ( is_file( $html_template ) ) {
		$vars = [
			"product_order_url" => $str,
		];
		
		# Getting custom fields and adding in output
		$TemplateArray = $WHMPress->get_template_array( "whmpress_order_url" );
		foreach ( $TemplateArray as $custom_field ) {
			$vars[ $custom_field ] = isset( $atts[ $custom_field ] ) ? $atts[ $custom_field ] : "";
		}
		
		$OutputString = whmp_smarty_template( $html_template, $vars );
		
		return $OutputString;
	} else {
		return $str;
	}
	
	# Returning output string
}

function whmpress_currency_function( $atts ) {
	/**
	 * Display currency symbol
	 *
	 * List of attributes/parameters
	 * html_id = HTML id for wrapper span
	 * html_class = HTML class for wrapper span
	 * show = suffix,prefix,code    (default prefix)
	 */
	
	extract( shortcode_atts( [
		'html_template' => '',
		'image'         => '',
		'html_id'       => '',
		'html_class'    => 'whmpress_currency',
		'show'          => 'prefix',
		'currency'      => ''
	], $atts ) );
	$currency = whmp_get_currency( $currency );
	
	# Getting currency
	$func     = "whmp_get_currency_" . strtolower( $show );
	$currency = $func( $currency );
	/*
    if (!isset($_SESSION["currency"])) {
        $func = "whmp_get_default_currency_".strtolower($show);
        $currency = $func();
    } else {
        $func = "whmp_get_currency_".strtolower($show);
        $currency = $func($_SESSION["currency"]);
    }*/
	
	# Returning output including wrapper
	$WHMPress = new WHMPress;
	
	$html_template = $WHMPress->check_template_file( $html_template, "whmpress_currency" );
	
	if ( is_file( $html_template ) ) {
		$vars = [
			"current_currency" => $currency
		];
		
		# Getting custom fields and adding in output
		$TemplateArray = $WHMPress->get_template_array( "whmpress_currency" );
		foreach ( $TemplateArray as $custom_field ) {
			$vars[ $custom_field ] = isset( $atts[ $custom_field ] ) ? $atts[ $custom_field ] : "";
		}
		
		$OutputString = whmp_smarty_template( $html_template, $vars );
		
		return $OutputString;
	} else {
		return "<span id='$html_id' class='$html_class'>" . $currency . "</span>";
	}
}

function whmpress_currency_combo_function( $atts ) {
	/**
	 * Generate a currency combo and will change currency for prices
	 *
	 * List of parameters
	 * combo_name = HTML name for combo
	 * combo_class = HTML class for combo
	 * prefix = Display or not prefix with currency, e.g. $
	 * html_id = HTML id for wrapper of combo
	 * html_class = HTML class for wrapper of combo
	 */
	
	extract( shortcode_atts( [
		'html_template' => '',
		'image'         => '',
		'combo_name'    => '',
		'combo_class'   => '',
		'prefix'        => 'yes',
		'html_id'       => '',
		'html_class'    => 'whmpress whmpress_currency_combo'
	], $atts ) );
	$name  = $combo_name;
	$class = $combo_class;
	
	# Getting WordPress DB object
	global $wpdb;
	
	# Checking currency
	if ( ! session_id() ) {
		session_start();
	}
	if ( isset( $_SESSION["currency"] ) ) {
		$currency = $_SESSION["currency"];
	} else {
		$currency = whmp_get_default_currency_id();
	}
	
	# getting ajax url which will change currency in session
	#$ajaxFile = WHMP_PLUGIN_URL."/includes/set_currency.php";
	
	# Generating random HTML id for combo
	$myID = "S" . rand();
	
	# Generating Output HTML
	$str = "
    <script>
        jQuery(function(){
        jQuery(\"#{$myID}\").change(function(){
            val = jQuery(this).val();
            jQuery.post(WHMPAjax.ajaxurl + '?setCurrency',{'curency': val,'action':'whmpress_action'},function(data){
                if (data=='OK')
                    window.location.reload();
                else
                    alert(data);
            });
        });
    });
    </script>
    ";
	$str .= "<select id='$myID'";
	$str .= ' class="' . $class . '"';
	$str .= ' name="' . $name . '"';
	$str .= ">\n";
	
	$C            = $currency;
	$Q            = "SELECT `id`, `prefix`, `code` FROM `" . whmp_get_currencies_table_name() . "` ORDER BY `id`";
	$rows         = $wpdb->get_results( $Q );
	$smarty_array = [];
	foreach ( $rows as $row ) {
		$data = [];
		$S    = $C == $row->id ? "selected=selected" : "";
		$str .= "<option $S value='{$row->id}'>{$row->code}";
		if ( strtolower( $prefix ) == "yes" ) {
			$str .= " ({$row->prefix})";
		}
		$str .= "</option>";
		
		$data["prefix"] = $row->prefix;
		$data["code"]   = $row->code;
		$data["id"]     = $row->id;
		$smarty_array[] = $data;
	}
	$str .= "</select>";
	
	# Returning combo output string including wrapper div
	$decimal_sperator = get_option( 'decimal_replacement', "." );
	
	# Generating output string
	$WHMPress = new WHMPress;
	
	$html_template = $WHMPress->check_template_file( $html_template, "whmpress_currency_combo" );
	
	if ( is_file( $html_template ) ) {
		$OutputString = $WHMPress->read_local_file( $html_template );
		$vars         = [
			"currency_combo" => str_replace( ".", $decimal_sperator, $str ),
			"data"           => $smarty_array,
		];
		
		# Getting custom fields and adding in output
		$TemplateArray = $WHMPress->get_template_array( "whmpress_currency_combo" );
		foreach ( $TemplateArray as $custom_field ) {
			$vars[ $custom_field ] = isset( $atts[ $custom_field ] ) ? $atts[ $custom_field ] : "";
		}
		
		$OutputString = whmp_smarty_template( $html_template, $vars );
		
		return $OutputString;
	} else {
		return "<div id='$html_id' class='$html_class'>" . str_replace( ".", $decimal_sperator, $str ) . "</div>";
	}
}

function whmpress_currency_combo_fancy_function() {
	if ( ! session_id() ) {
		session_start();
	}
	if ( isset( $_SESSION["currency"] ) ) {
		$currency = $_SESSION["currency"];
	} else {
		$currency = whmp_get_default_currency_id();
	}
	
	// Getting currencies
	global $wpdb;
	$Q    = "SELECT `id`, `prefix`, `code` FROM `" . whmp_get_currencies_table_name() . "` ORDER BY `id`";
	$rows = $wpdb->get_results( $Q, ARRAY_A );
	#print_r ($rows);
	
	// Adding flag if flag png available in extra folder
	foreach ( $rows as &$row ) {
		if ( is_file( WHMP_PLUGIN_DIR . "/extras/" . $row["code"] . ".png" ) ) {
			$row["flag"] = WHMP_PLUGIN_URL . "/extras/" . $row["code"] . ".png";
		} else {
			$row["flag"] = WHMP_PLUGIN_URL . "/extras/noflag.png";
		}
	}
	
	// Setting current selected currency
	$topurl = "";
	foreach ( $rows as $r ) {
		#print_r ($row);
		if ( $currency == $r["id"] ) {
			$topurl = '<a class="lang_sel_sel icl-en" href="#" onclick="return false;"><img title="' .
			          $r["code"] .
			          ' (' .
			          $r["prefix"] .
			          ')" alt="en" src="' .
			          $r["flag"] .
			          '" class="iclflag">&nbsp;<span class="icl_lang_sel_current">' .
			          $r["code"] .
			          ' (' .
			          $r["prefix"] .
			          ')</span></a>';
			#break;
		}
	}
	
	$func_name = uniqid( 'func_' );
	$str       = '
    <div id="lang_sel">
        <ul>
            <li>
                ' . $topurl . '
                <ul>';
	foreach ( $rows as $row1 ) {
		$str .= "<li class=\"icl-pl\"><a href=\"#\" onclick=\"return " .
		        $func_name .
		        "(" .
		        $row1["id"] .
		        ");\" hreflang=\"pkr\" rel=\"alternate\"><img title=\"{$row1["code"]}\" alt=\"{$row1["code"]}\" src=\"" .
		        $row1["flag"] .
		        "\" class=\"iclflag\">&nbsp; <span class=\"icl_lang_sel_native\">{$row1["code"]} ({$row1["prefix"]})</span> <span class=\"icl_lang_sel_translated\"><span class=\"icl_lang_sel_native\">(</span>{$row["code"]}<span class=\"icl_lang_sel_native\">)</span></span></a></li>\n";
	}
	$str .= '</ul>
            </li>
        </ul>
    </div>
    
    <script>
        function ' . $func_name . '(cur) {
            jQuery.post(WHMPAjax.ajaxurl + "?setCurrency",{curency:cur,action:"whmpress_action"},function(data){
                if (data=="OK") window.location.reload();
                else alert(data);
            });
            return false;
        }
    </script>
    ';
	
	return $str;
}

function whmpress_domain_search_function( $atts ) {
	/**
	 * Generates HTML form for search domains
	 *
	 * List of parameters
	 * show_tlds = provide comma seperated tlds e.g. .com,.net,.org or leave it blank for all tlds
	 * show_tlds_wildcard = provide tld search as wildcard, e.g. pk for all .pk domains or co for all com and .co domains
	 * show_combo = Yes or No for display or hide combo box of tlds
	 * placeholder = HTML placeholder for input search box
	 * text_class = HTML class for wrapper of input search box
	 * combo_class = HTML class for wrapper of combo box
	 * button_class = HTML class for wrapper of submit button of form
	 * action = Specify url where form will submit
	 * button_text = Button text for submit button
	 * html_class = HTML class for wrapper of form
	 * html_id = HTML id for wrapper of form
	 */
	
	$WHMPress = new WHMpress();
	
	$params = shortcode_atts( [
		'html_template'      => '',
		'image'              => '',
		'show_tlds'          => '',
		'show_tlds_wildcard' => '',
		'show_combo'         => whmpress_get_option( 'ds_show_combo' ), //'no',
		'placeholder'        => whmpress_get_option( 'ds_placeholder' ),
		'text_class'         => 'search_div',
		'combo_class'        => 'select_div',
		'button_class'       => 'submit_div',
		'action'             => $WHMPress->get_whmcs_url( "domainchecker" ),
		'button_text'        => whmpress_get_option( 'ds_button_text' ), //'Search',
		'html_class'         => 'whmpress whmpress_domain_search',
		'html_id'            => '',
	], $atts );
	extract( $params );
	
	# Getting WordPress DB object
	global $wpdb;
	
	# Generating and setting combo box if it will display
	if ( strtolower( $show_combo ) == "yes" ):
		$Q = "SELECT `extension` FROM `" . whmp_get_domain_pricing_table_name() . "` WHERE 1";
		if ( $show_tlds <> "" ) {
			$show_tlds = explode( ",", $show_tlds );
			$Q .= " AND (`extension`='" . implode( "' OR `extension`='", $show_tlds ) . "')";
		} elseif ( $show_tlds_wildcard <> "" ) {
			$Q .= " AND `extension` LIKE '%{$show_tlds_wildcard}%'";
		}
		$Q .= " ORDER BY `extension`";
		$rows = $wpdb->get_results( $Q, ARRAY_A );
	endif;
	
	# Generating output string
	$ACTION = empty( $action ) ? "" : "action='$action'";
	$str    = "<form method=\"get\" id=\"searchDomainForm\" $ACTION>";
	$params = parse_url( $action );
	if ( ! isset( $params["query"] ) ) {
		$params["query"] = "";
	}
	if ( $params["query"] <> "" ) {
		parse_str( $params["query"], $params );
		foreach ( $params as $key => $val ) {
			$str .= "<input type=\"hidden\" value=\"{$val}\" name=\"{$key}\">";
		}
	}
	$str .= "<!--input type=\"hidden\" name=\"token\" value=\"24372f4f06ca835d9101d60a258c30a4c93b3bf7\">
    <input type=\"hidden\" name=\"direct\" value=\"true\"-->";
	$str .= "<div class=\"{$text_class} search_div\">";
	$val = isset( $_GET["search_domain"] ) ? $_GET["search_domain"] : "";
	$str .= "<input required='required' title='" . __( "Please fill out this field", "whmpress" ) . "' type=\"search\" name=\"domain\" id=\"search_domain\" placeholder=\"" . __( $placeholder, "whmpress" ) . "\" value=\"{$val}\" />\n";
	$str .= "</div>";
	
	$WHMPress      = new WHMPress;
	$html_template = $WHMPress->check_template_file( $html_template, "whmpress_domain_search" );
	
	$smarty_array                   = [];
	$smarty_array["params"]         = $params;
	$smarty_array["params_encoded"] = whmpress_json_encode( $params );
	$smarty_array["class"]          = $combo_class;
	$smarty_array["rows"]           = [];
	if ( strtolower( $show_combo ) == "yes" ) {
		$smarty_array["rows"] = $rows;
		$Combo                = "<div class=\"{$combo_class} select_div\">";
		
		$Combo .= "<select name='ext'>";
		foreach ( $rows as $row ) {
			$Combo .= "<option>" . $row["extension"] . "</option>\n";
		}
		$Combo .= "</select>";
		$Combo .= "</div>";
		if ( ! is_file( $html_template ) ) {
			$str .= $Combo;
		}
	} else {
		$str .= "<input type='hidden' name='ext' value='' />\n";
		$Combo = "";
	}
	$str .= "<div class=\"{$button_class} submit_div\">";
	$str .= "<input type=\"submit\" value=\"{$button_text}\">";
	$str .= "</div>";
	$str .= "</form>";
	
	
	if ( is_file( $html_template ) ) {
		$vars = [
			"search_text_box" => "<input required='required' type=\"search\" name=\"domain\" id=\"search_domain\" placeholder=\"" . __( $placeholder, "whmpress" ) . "\" value=\"{$val}\" />",
			"search_combo"    => $Combo,
			"search_button"   => "<input type=\"submit\" value=\"{$button_text}\">",
			"action_url"      => $action,
			"combo_data"      => $smarty_array,
		];
		
		# Getting custom fields and adding in output
		$TemplateArray = $WHMPress->get_template_array( "whmpress_domain_search" );
		foreach ( $TemplateArray as $custom_field ) {
			$vars[ $custom_field ] = isset( $atts[ $custom_field ] ) ? $atts[ $custom_field ] : "";
		}
		
		$OutputString = whmp_smarty_template( $html_template, $vars );
		
		return $OutputString;
	} else {
		
		# Returning output string including wrapper div
		$ID    = ! empty( $html_id ) ? "id='$html_id'" : "";
		$CLASS = ! empty( $html_class ) ? "class='$html_class'" : "";
		
		return "<div $CLASS $ID>" . $str . "</div>";
	}
}

function whmpress_price_table_domain_function( $atts = "" ) {
	$html = include( WHMP_PLUGIN_DIR . "/includes/shortcodes/whmpress_price_table_domain.php" );
	
	return $html;
}

function whmpress_price_domain_list_function( $atts = "" ) {
	$html = include( WHMP_PLUGIN_DIR . "/includes/shortcodes/whmpress_price_domain_list.php" );
	
	return $html;
}

function whmpress_price_table_function( $atts ) {
	/**
	 * Displays price table of all services including domain
	 *
	 * name = Matching service name
	 * groups = comma seperated group names
	 * billingcycles = Billing cycle names
	 * decimals = number of decimals with price
	 * type = type of product. e.g. product
	 * table_id = HTML id for table
	 * currency = Currency id for prices
	 * show_hidden = provide yes if you want to show hidden products
	 * replace_zero = replace zero with specific character, default is -
	 * replace_empty = replace empty with specific character, default is x
	 * html_id = HTML id for div wrapper
	 * html_class = HTML class for div wrapper
	 * hide_search = Yes or No for hide search
	 * titles = Comman seprated titles for column titles
	 * search_label = Set label for search
	 * search_placeholder = Set placeholder for search box
	 */
	
	extract( shortcode_atts( [
		'html_template'      => '',
		'name'               => '',
		'groups'             => '',
		'billingcycles'      => '',
		'hide_columns'       => '',
		'decimals'           => whmpress_get_option( 'pm_decimals' ),
		'show_hidden'        => whmpress_get_option( 'pm_show_hidden' ),
		'replace_zero'       => whmpress_get_option( 'pm_replace_zero' ),
		'replace_empty'      => whmpress_get_option( 'pm_replace_empty' ),
		'table_id'           => '',
		'type'               => 'product',
		'html_id'            => '',
		'html_class'         => 'whmpress whmpress_price_matrix',
		'hide_search'        => whmpress_get_option( 'pm_hide_search' ),
		'currency'           => '',
		'titles'             => '',
		'search_label'       => whmpress_get_option( 'pm_search_label' ),
		'search_placeholder' => whmpress_get_option( 'pm_search_placeholder' ),
		'order_link'         => '1',
		'data_table'         => '0',
	], $atts ) );
	
	$cols         = $billingcycles;
	$showhidden   = $show_hidden;
	$replacezero  = $replace_zero;
	$replaceempty = $replace_empty;
	if ( ! isset( $currency ) ) {
		$currency = "";
	}
	$currency = whmp_get_currency( $currency );
	
	# Checking parameters
	
	# Getting WordPress DB object
	global $wpdb;
	
	# Getting symbol type
	$symbol_type = strtolower( whmpress_get_option( 'default_currency_symbol' ) );
	
	# Setting fields/columns for Table
	$fieldss = [
		"sr"           => __( "Sr", "whmpress" ),
		"id"           => __( "ID", "whmpress" ),
		"name"         => __( "Name", "whmpress" ),
		"groupn"       => __( "Group", "whmpress" ),
		"monthly"      => __( "Monthly", "whmpress" ),
		"quarterly"    => __( "3 Months", "whmpress" ),
		"semiannually" => __( "6 Months", "whmpress" ),
		"annually"     => __( "Yearly", "whmpress" ),
		"biennially"   => __( "2 Years", "whmpress" ),
		"triennially"  => __( "3 Years", "whmpress" ),
	];
	
	$hide_columns = explode( ",", $hide_columns );
	foreach ( $hide_columns as $hd ) {
		if ( strtolower( $hd ) == "group" ) {
			$hd = "groupn";
		}
		$hd = trim( $hd );
		if ( isset( $fieldss[ $hd ] ) ) {
			unset( $fieldss[ $hd ] );
		}
	}
	
	$dfieldss = [          // Deletable fields
		"monthly"      => __( "Monthly", "whmpress" ),
		"quarterly"    => __( "3 Months", "whmpress" ),
		"semiannually" => __( "6 Months", "whmpress" ),
		"annually"     => __( "Yearly", "whmpress" ),
		"biennially"   => __( "2 Years", "whmpress" ),
		"triennially"  => __( "3 Years", "whmpress" ),
	];
	$fields   = "pd.`id`,pd.`name`,g.`name` groupn,pr.`monthly`,pr.`quarterly`,pr.`semiannually`,pr.`annually`,pr.`biennially`,pr.`triennially`,`gid`";
	if ( $table_id == "" ) {
		$table_id = uniqid();
	}
	
	# Getting data from DB
	$Q = "SELECT $fields,pd.paytype FROM `" . whmp_get_pricing_table_name() . "` pr, `" . whmp_get_products_table_name() . "` pd, `" . whmp_get_product_group_table_name() . "` g
    WHERE pr.`currency`='$currency' AND pr.`relid`=pd.`id`";
	if ( $name <> "" ) {
		$Q .= " AND pd.`name` LIKE '%{$name}%'";
	}
	if ( $groups <> "" ) {
		$group = explode( ",", $groups );
		$Q .= " AND (";
		foreach ( $group as $g ) {
			if ( is_numeric( $g ) ) {
				$Q .= "g.`id`='" . $g . "' OR ";
			} else {
				$Q .= "g.`name`='" . $g . "' OR ";
			}
		}
		$Q = substr( $Q, 0, - 4 ) . ")";
		//$Q .= " g.`name`='".implode("' OR g.`name`='",$group)."')";
	}
	
	if ( strtolower( $showhidden ) == "no" ) {
		$Q .= " AND pd.`hidden`='0' ";
	}
	
	$Q .= " AND pd.gid=g.id AND pr.`type`='{$type}'
    ORDER BY pd.`gid`, pd.`name`";
	
	# Getting decimal seperator from settings
	$decimal_sperator = get_option( 'decimal_replacement', "." );
	
	# Generating output string
	if ( $cols <> "" ) {
		unset( $fieldss["monthly"], $fieldss["quarterly"], $fieldss["semiannually"], $fieldss["annually"], $fieldss["biennially"], $fieldss["triennially"] );
		$cols = explode( ",", $cols );
		foreach ( $cols as $col ) {
			$fieldss[ trim( $col ) ] = $dfieldss[ trim( $col ) ];
		}
	}
	
	if ( $data_table == '1' || strtolower( $data_table ) == 'yes' ) {
		$str = "<script>
        jQuery(function(){
            jQuery('table#{$table_id}').DataTable();
        });
        </script>\n";
	} else {
		$str = "<script>
        jQuery(function(){
            jQuery('input#search_price_table').quicksearch('table#{$table_id} tbody tr');
        });
        </script>\n";
	}
	
	$rows = $wpdb->get_results( $Q, ARRAY_A );
	$str .= "<table style='width:100%' border='1' id='{$table_id}'>
    <thead>
        <tr>";
	if ( $titles == "" ) {
		$titles = [];
	} else {
		$titles = explode( ",", $titles );
	}
	$x = 0;
	foreach ( $fieldss as $k => $field ) {
		if ( isset( $titles[ $x ] ) ) {
			$t = trim( $titles[ $x ] );
		} else {
			$t = trim( $field, "`" );
		}
		//$t = trim($field,"`");
		if ( $k == "groupn" ) {
			$k = "group";
		}
		$smarty_title  = $k . "_title";
		$$smarty_title = $t;
		
		$str .= "<th>" . $t . "</th>";
		
		$x ++;
	}
	// Show order link column
	if ( $order_link == "1" || strtolower( $order_link ) == "yes" ) {
		$str .= "<th></th>";
	}
	$str .= "</tr>
    </thead>
    <tbody>\n";
	
	$smarty2["table_id"]      = $table_id;
	$smarty2["data_table"]    = $data_table;
	$smarty2["total_records"] = count( $rows );
	$smarty_array             = [];
	foreach ( $rows as $key => $row ) {
		foreach ( $row as &$kr ) {
			$kr = whmpress_encoding( $kr );
		}
		$data = [];
		$str .= "<tr>";
		if ( ! in_array( "sr", $hide_columns ) ) {
			$str .= "<td data-content=\"Sr\">" . ( $key + 1 ) . "</td>";
			$data["sr"] = $key + 1;
		} else {
			$data["sr"] = "";
		}
		
		$x = 0;
		foreach ( $fieldss as $k => $field ) {
			if ( $k <> "sr" ) {
				if ( isset( $titles[ $x ] ) ) {
					$t = trim( $titles[ $x ] );
				} else {
					$t = trim( $field, "`" );
				}
				if ( $k == "id" || $k == "name" || $k == "groupn" ) {
					$str .= "<td data-content=\"{$t}\">" . $row[ $k ] . "</td>";
					
					if ( $k == "groupn" ) {
						$data["group"] = $row[ $k ];
					} else {
						$data[ $k ] = $row[ $k ];
					}
				} elseif ( $row["paytype"] == "free" ) {
					$v = "0";
					if ( $symbol_type == "prefix" ) {
						$v = whmp_get_currency_prefix( $currency ) . "$v";
					} elseif ( $symbol_type == "suffix" ) {
						$v = "$v" . whmp_get_currency_suffix( $currency );
					} elseif ( $symbol_type == "code" ) {
						$v = whmp_get_currency_code( $currency ) . " $v";
					}
					$str .= "<td data-content=\"{$t}\">$v</td>";
					$data[ $k ] = $v;
				} else {
					$v = $row[ $k ];
					if ( $v == "0.00" ) {
						$v = $replacezero;
					}
					if ( $v == "-1.00" ) {
						$v = $replaceempty;
					}
					if ( is_numeric( $v ) ) {
						//$v=str_replace(".",$decimal_sperator,number_format($v,$decimals));                        
						$v = whmpress_price_function( [
							"billingcycle"    => $k,
							"id"              => $row["id"],
							"currency"        => $currency,
							"decimals"        => $decimals,
							"show_duration"   => "no",
							"convert_monthly" => "no",
							"html_id"         => "",
							"html_class"      => "",
							"decimals_tag"    => "",
							"prefix"          => "no",
							"suffix"          => "no",
							"no_wrapper"      => "1",
						] );
					}
					if ( $v <> $replaceempty ) {
						if ( $symbol_type == "prefix" ) {
							$v = whmp_get_currency_prefix( $currency ) . "$v";
						} elseif ( $symbol_type == "suffix" ) {
							$v = "$v" . whmp_get_currency_suffix( $currency );
						} elseif ( $symbol_type == "code" ) {
							$v = whmp_get_currency_code( $currency ) . " $v";
						}
					}
					$str .= "<td data-content=\"{$t}\">" . $v . "</td>";
					$data[ $k ] = $v;
				}
			}
			$x ++;
		}
		
		$order_button        = whmpress_order_button_function( [ "id" => $row["id"], "currency" => $currency ] );
		$data["order_url"]   = $order_button;
		$data["description"] = whmpress_description_function( [
			"id"         => $row["id"],
			"no_wrapper" => "1",
			"show_as"    => "1"
		] );
		
		if ( $order_link == "1" || strtolower( $order_link ) == "yes" ) {
			$str .= "<td>" . $order_button . "</td>";
		}
		$str .= "</tr>\n";
		$smarty_array[] = $data;
	}
	$str .= "
    </tbody>
    </table>";
	
	$WHMPress      = new WHMPress;
	$html_template = $WHMPress->check_template_file( $html_template, "whmpress_price_matrix" );
	
	if ( is_file( $html_template ) ) {
		$vars = [
			"search_label"       => $search_label,
			"search_text_box"    => "<input id='whmpress_text_box' type='search' placeholder='" . __( $search_placeholder, "whmpress" ) . "'>",
			"price_matrix_table" => $str,
			"sr_title"           => $sr_title,
			"id_title"           => $id_title,
			"name_title"         => $name_title,
			"group_title"        => $group_title,
			"monthly_title"      => $monthly_title,
			"quarterly_title"    => $quarterly_title,
			"semiannually_title" => $semiannually_title,
			"annually_title"     => $annually_title,
			"biennially_title"   => $biennially_title,
			"triennially_title"  => $triennially_title,
			"data"               => $smarty_array,
			"params"             => $smarty2
		];
		
		# Getting custom fields and adding in output
		$TemplateArray = $WHMPress->get_template_array( "whmpress_price_matrix" );
		foreach ( $TemplateArray as $custom_field ) {
			$vars[ $custom_field ] = isset( $atts[ $custom_field ] ) ? $atts[ $custom_field ] : "";
		}
		
		$OutputString = whmp_smarty_template( $html_template, $vars );
		
		return $OutputString;
	} else {
		# Adding search box if hide_search is not called
		$search_box = "";
		if ( strtolower( $hide_search ) <> "yes" ) {
			if ( $data_table == '1' || strtolower( $data_table ) == 'yes' ) {
				// Do not include search box
			} else {
				$search_box = "
                <label>{$search_label}</label>
                <input type='search' placeholder='" . __( $search_placeholder, "whmpress" ) . "' id='search_price_table' style='width:50%' >";
			}
		}
		
		# Returning output string including wrapper div
		$ID    = ! empty( $html_id ) ? "id='$html_id'" : "";
		$CLASS = ! empty( $html_class ) ? "class='$html_class'" : "";
		
		return "<div $CLASS $ID>" . $search_box . $str . "</div>";
	}
}