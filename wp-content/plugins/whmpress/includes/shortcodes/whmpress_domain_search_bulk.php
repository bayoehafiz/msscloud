<?php
/**
 * Copyright (c) 2014-2016 by creativeON.
 */

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
 */

extract( shortcode_atts( [
	'html_template' => '',
	'image'         => '',
	'text_class'    => '',
	'button_class'  => '',
	'html_class'    => 'whmpress whmpress_domain_search_bulk',
	'html_id'       => '',
	'placeholder'   => whmpress_get_option( 'dsb_placeholder' ),
	'button_text'   => whmpress_get_option( 'dsb_button_text' ),
], $atts ) );
global $wpdb;
$button_text = __( $button_text, "whmpress" );

$WHMPress = new WHMpress();

# Generating output result
$HTML = "";
//$HTML = print_r($_POST, true);
if ( isset( $_POST["search_bulk_domain"] ) ) {
	$HTML .= "<div class='result-div'>";
	
	$extention_selection = isset( $_POST["extention_selection"] ) ? $_POST["extention_selection"] : "0";
	
	if ( $extention_selection == "1" ) {
		$domains_set = isset( $_POST["search_bulk_domain"] ) ? $_POST["search_bulk_domain"] : "";
		$domains_set = explode( "\n", $domains_set );
		if ( ! isset( $_POST["extension"] ) ) {
			$extension          = [ ".com" ];
			$_POST["extension"] = [ ".com" ];
		} else {
			$extension = $_POST["extension"];
		}
		//$HTML .= print_r($_POST, true);
		$domains = [];
		foreach ( $extension as $ext ) {
			foreach ( $domains_set as $domain ) {
				$domains[] = $domain . $ext;
			}
		}
	} else {
		$domains = isset( $_POST["search_bulk_domain"] ) ? $_POST["search_bulk_domain"] : "";
		$domains = explode( "\n", $domains );
	}
	
	include_once( WHMP_PLUGIN_DIR . "/includes/whois.class.php" );
	$whois = new Whois;
	
	global $wpdb;
	
	foreach ( $domains as $domain ) {
		if ( trim( $domain ) <> "" ):
			$domain = ltrim( $domain, '//' );
			if ( substr( strtolower( $domain ), 0, 7 ) == "http://" ) {
				$domain = substr( $domain, 7 );
			}
			if ( substr( strtolower( $domain ), 0, 8 ) == "https://" ) {
				$domain = substr( $domain, 8 );
			}
			if ( strtolower( substr( $domain, 0, 4 ) ) == "www." ) {
				$domain["host"] = substr( $domain, 4 );
			}
			if ( strtolower( substr( $domain, 0, 3 ) ) == "ww." ) {
				$domain["host"] = substr( $domain, 3 );
			}
			
			$ext        = whmp_get_domain_extension( $domain );
			$onlydomain = str_replace( "." . $ext, "", $domain );
			
			if ( $ext == $onlydomain ) {
				$ext = "com";
				$onlydomain .= ".com";
			}
			$result = $whois->whoislookup( $domain, $ext );
			
			if ( $result ) {
				$ext     = "." . ltrim( $ext, "." );
				$Dom     = $domain;
				$Message = whmpress_get_option( 'ongoing_domain_available_message' );
				if ( $Message == "" ) {
					$Message = __( "[domain-name] is available", 'whmpress' );
				}
				$Message = str_replace( "[domain-name]", $Dom, $Message );
				
				$Q     = "SELECT d.id, d.extension 'tld', t.type, c.code, c.suffix, c.prefix, t.msetupfee, t.qsetupfee
                FROM `" . whmp_get_domain_pricing_table_name() . "` AS d
                INNER JOIN `" . whmp_get_pricing_table_name() . "` AS t ON t.relid = d.id
                INNER JOIN `" . whmp_get_currencies_table_name() . "` AS c ON c.id = t.currency
                WHERE t.type
                IN (
                'domainregister'
                ) AND d.extension IN ('{$ext}')
                AND c.code='" . whmp_get_currency_code( whmp_get_currency() ) . "' 
                ORDER BY d.id ASC 
                LIMIT 0 , 30";
				$price = $wpdb->get_row( $Q, ARRAY_A );
				
				$register_text = whmpress_get_option( 'register_domain_button_text' );
				if ( $register_text == "" ) {
					$register_text = __( "Select", "whmpress" );
				}
				if ( is_plugin_active( 'WHMpress_Client_Area/client-area.php' ) ) {
					global $WHMPress_Client_Area;
					if ( empty( $WHMPress_Client_Area ) ) {
						$WHMPress_Client_Area = new WHMPress_Client_Area;
					}
					if ( $WHMPress_Client_Area->is_permalink() ) {
						$_url = $WHMPress->get_whmcs_url( "order" ) . "/a/add/domain/register/sld/{$onlydomain}/tld/{$ext}/";
					} else {
						$_url = $WHMPress->get_whmcs_url( "order" ) . "a=add&domain=register&sld={$onlydomain}&tld={$ext}";
					}
				} else {
					$_url = $WHMPress->get_whmcs_url( "order" ) . "a=add&domain=register&sld={$onlydomain}&tld={$ext}";
				}
				if ( ! empty( $price["msetupfee"] ) ) {
					$button = "<a href='$_url' class='buy-button'>$register_text</a>";
				} else {
					$button = "";
				}
				
				$pricef = ( $price["msetupfee"] > "0" ? $price["msetupfee"] : $price["qsetupfee"] );
				if ( ( get_option( 'default_currency_symbol', "prefix" ) == "code" || get_option( 'default_currency_symbol', "prefix" ) == "prefix" ) && isset( $price[ get_option( 'default_currency_symbol', "prefix" ) ] ) ) {
					$pricef = $price[ get_option( 'default_currency_symbol', "prefix" ) ] . $pricef;
				} else if ( get_option( 'default_currency_symbol' ) == "suffix" && isset( $price[ get_option( 'default_currency_symbol' ) ] ) ) {
					$pricef = $pricef . $price[ get_option( 'default_currency_symbol' ) ];
				}
				
				if ( $price["msetupfee"] > 0 ) {
					$year = "(1 " . __( "Year", "whmpress" ) . ")";
				} else if ( $price["qsetupfee"] > 0 ) {
					$year = "(2 " . __( "Years", "whmpress" ) . ")";
				} else {
					$year = "";
				}
				
				$HTML .= "
                <div class='found-div'>
                    <div class=\"domain-name\">$Message</div>
                    <div class=\"rate\">$pricef</div>
                    <div class=\"year\">$year</div>
                    <div class=\"select-box\">
                        $button
                    </div>
                    <div style=\"clear:both\"></div>
                </div>\n";
				
				/*$HTML .= "
				<div class='found-div'><span><i class='fa fa-check'></i> $Message</span>
				$button
				<div style='clear:both'></div>
				</div>\n";*/
			} else {
				/*$Dom = $domain;
				$Message = whmpress_get_option('ongoing_domain_not_available_message');
				$Message = str_replace("[domain-name]", $Dom, $Message);
				$HTML .= "<div class='not-found-div'>
					<span><i class='fa fa-warning'></i> $Message<span></div>\n";*/
				
				$Dom = $domain;
				#if (isset($_POST["www_link"]) && strtolower($_POST["www_link"])=="yes") {
				$www_link = "<a class=\"www-button\" href='http://{$Dom}' target='_blank'>" . __( "WWW", "whmpress" ) . "</a>";
				#} else {
				#$www_link = "";
				#}
				
				#if (isset($_POST["whois_link"]) && strtolower($_POST["whois_link"])=="yes") {
				//$whois_link = "<a class='whois-button' href='javascript:;' onclick='window.open(\"".WHMP_PLUGIN_URL."/whois.php?domain={$Dom}\",\"whmpwin\",\"width=600,height=600,toolbar=no,location=no,directories=no,status=no,menubar=no,resizable=0\")'>WHOIS</a>";
				$whois_link = "<a class='whois-button' href='" . WHMP_PLUGIN_URL . "/whois.php?domain={$Dom}' target='_blank'>" . __( "WHOIS", "whmpress" ) . "</a>";
				#} else {
				#$whois_link = "";
				#}
				
				$Message = whmpress_get_option( 'ongoing_domain_not_available_message' );
				if ( $Message == "" ) {
					$Message = "[domain-name] is registered";
				}
				$Message = str_replace( "[domain-name]", "<span>" . $Dom . "</span>", $Message );
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
		endif;
	}
	$HTML .= "</div>";
}

$extensions        = $wpdb->get_results( "SELECT `extension` FROM `" . whmp_get_domain_pricing_table_name() . "` ORDER BY `order`" );
$exts              = "";
$smarty_extensions = [];
foreach ( $extensions as $ext ) {
	if ( isset( $_POST["extension"] ) && is_array( $_POST["extension"] ) ) {
		$checked = in_array( $ext->extension, $_POST["extension"] ) ? "checked=checked" : "";
	} else {
		$checked = "";
	}
	$exts .= "<div class=\"\">
              <input type=\"checkbox\" value=\"{$ext->extension}\" id=\"{$ext->extension}\" name=\"extension[]\" $checked />
              <label for=\"{$ext->extension}\">{$ext->extension}</label>
            </div>\n";
	$smarty_extensions[] = $ext->extension;
}

$WHMPress = new WHMPress;

$html_template = $WHMPress->check_template_file( $html_template, "whmpress_domain_search_bulk" );

if ( is_file( $html_template ) ) {
	$search_bulk_domain = isset( $_POST["search_bulk_domain"] ) ? $_POST["search_bulk_domain"] : "";
	$vars               = [
		"search_textarea" => '<textarea required="required" class="' . $text_class . '" placeholder="' . __( $placeholder, "whmpress" ) . '" name="search_bulk_domain">' . $search_bulk_domain . '</textarea>',
		"search_button"   => '<button class="search_btn ' . $button_class . '">' . $button_text . '</button>',
		"search_results"  => $HTML,
		"button_text"     => $button_text,
		"data_extensions" => $smarty_extensions,
	];
	
	# Getting custom fields and adding in output
	$TemplateArray = $WHMPress->get_template_array( "whmpress_domain_search_bulk" );
	foreach ( $TemplateArray as $custom_field ) {
		$vars[ $custom_field ] = isset( $atts[ $custom_field ] ) ? $atts[ $custom_field ] : "";
	}
	
	$OutputString = whmp_smarty_template( $html_template, $vars );
	
	return $OutputString;
} else {
	//$str = '<form method="post">'."\n";
	$search_bulk_domain = isset( $_POST["search_bulk_domain"] ) ? $_POST["search_bulk_domain"] : "";
	//$str .= '<textarea required="required" class="'.$text_class.'" placeholder="'.$placeholder.'" name="search_bulk_domain">'.$search_bulk_domain.'</textarea>'."\n";
	
	if ( isset( $_POST["extention_selection"] ) && $_POST["extention_selection"] == "1" ) {
		$display = "";
	} else {
		$display = "none";
	}
	if ( ! isset( $_POST["extention_selection"] ) || ( isset( $_POST["extention_selection"] ) && $_POST["extention_selection"] == "0" ) ) {
		$checked1 = "checked";
	} else {
		$checked1 = "";
	}
	
	if ( isset( $_POST["extention_selection"] ) && $_POST["extention_selection"] == "1" ) {
		$checked2 = "checked";
	} else {
		$checked2 = "";
	}
	
	$ientered    = __( "I entered fully qualified names", "whmpress" );
	$sthext      = __( "Search these extentions", "whmpress" );
	$placeholder = __( $placeholder, "whmpress" );
	$str         = <<<EOT
    <script> jQuery(document).ready(function(){ jQuery('input:radio[name="extention_selection"]').change(function(){ if(jQuery(this).val() == '0'){ jQuery('.extentions').css('display','none'); } else { jQuery('.extentions').css('display', 'block'); } }); }); </script>
    <div>
    <form method="post">
        <div class="bulk-domains">
            <textarea required="required" class="$text_class" placeholder="$placeholder" name="search_bulk_domain">$search_bulk_domain</textarea>
        </div>
        <div class="bulk-options">
            <div class="extention-selection">
                <label><input type="radio" name="extention_selection" value="0" $checked1> $ientered</label><br>
                <label><input type="radio" name="extention_selection" value="1" $checked2> $sthext</label>
            </div>
            <div class="extentions" style="display:$display">
                $exts
            </div>
            <div style="clear:both"></div>
            <div class="search-button">
                <button class="search_btn $button_class">$button_text</button>
            </div>
        </div>
    </form>
    </div>
    <div style="clear:both"></div>
EOT;
	
	# Returning output form
	$ID    = ! empty( $html_id ) ? "id='$html_id'" : "";
	$CLASS = ! empty( $html_class ) ? "class='$html_class'" : "";
	
	return "<!-- WHMPress -->\n<div $CLASS $ID>" . $str . $HTML . "</div><!-- End WHMPress -->";
}