<?php
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

$params = shortcode_atts( [
	'text_class'              => '',
	'button_class'            => '',
	'action'                  => '',
	'html_class'              => 'whmpress whmpress_domain_search_ajax',
	'html_id'                 => '',
	'placeholder'             => whmpress_get_option( "dsa_placeholder" ),  // "Search"
	'button_text'             => whmpress_get_option( "dsa_button_text" ), //'Search',
	"whois_link"              => whmpress_get_option( "dsa_whois_link" ), //"yes",
	"www_link"                => whmpress_get_option( "dsa_www_link" ), //"yes",
	"disable_domain_spinning" => whmpress_get_option( "dsa_disable_domain_spinning" ), //"0",
	"order_landing_page"      => whmpress_get_option( "dsa_order_landing_page" ), //"0",
	"show_price"              => whmpress_get_option( "dsa_show_price" ),
	"show_years"              => whmpress_get_option( "dsa_show_years" ),
	"search_extensions"       => whmpress_get_option( "dsa_search_extensions" ),
	"enable_transfer_link"    => whmpress_get_option( "dsa_transfer_link" ),
	"style"                   => "style1",
], $atts );
extract( $params );

# Generating output form
$WHMPress = new WHMPress;
if ( "Go direct to domain settings" == $order_landing_page || strtoupper( $order_landing_page ) == "YES" || $order_landing_page == "1" ) {
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

global $wpdb;
$extensions        = $wpdb->get_results( "SELECT `extension` FROM `" . whmp_get_domain_pricing_table_name() . "` ORDER BY `order`" );
$smarty_extensions = [];
foreach ( $extensions as $ext ) {
	$smarty_extensions[] = $ext->extension;
}
$ajaxID = uniqid( "ajaxForm" );
$ACTION = empty( $action ) ? "" : "action='$action'";
if ( substr( $action, 0, 1 ) == "#" ) {
	$htmlID = $action;
} else {
	$htmlID = "#{$ajaxID}2";
}

$loading_text = __( "Loading", "whmpress" );
$the_lang     = "";

$sd                  = ! empty( $_GET['search_domain'] ) ? $_GET['search_domain'] : "";
$sd                  = esc_attr( $sd );
$javascript_function = "
function Search{$ajaxID}(form) {
            whmp_page=1;
            jQuery('$htmlID').html(\"<div class='whmp_loading_div'><i class='fa fa-spinner fa-spin whmp_domain_search_ajax_results_spinner'></i> $loading_text</div>\");
            
            jQuery('#form{$ajaxID}').prepend('<input type=\"hidden\" name=\"action\" value=\"whmpress_action\" />');
            jQuery('#form{$ajaxID}').prepend('<input type=\"hidden\" name=\"params\" value=\"" . whmpress_json_encode( $params ) . "\" />');
            jQuery('#form{$ajaxID}').prepend('<input type=\"hidden\" name=\"show_price\" value=\"$show_price\" />');
            jQuery('#form{$ajaxID}').prepend('<input type=\"hidden\" name=\"show_years\" value=\"$show_years\" />');
            jQuery('#form{$ajaxID}').prepend('<input type=\"hidden\" name=\"whmpress_json_encode\" value=\"$order_landing_page\" />');
            jQuery('#form{$ajaxID}').prepend('<input type=\"hidden\" name=\"disable_domain_spinning\" value=\"$disable_domain_spinning\" />');
            jQuery('#form{$ajaxID}').prepend('<input type=\"hidden\" name=\"do\" value=\"getDomainData\" />');
            jQuery('#form{$ajaxID}').prepend('<input type=\"hidden\" name=\"www_link\" value=\"$www_link\" />');
            jQuery('#form{$ajaxID}').prepend('<input type=\"hidden\" name=\"whois_link\" value=\"$whois_link\" />');
            jQuery('#form{$ajaxID}').prepend('<input type=\"hidden\" name=\"enable_transfer_link\" value=\"$enable_transfer_link\" />');
            //jQuery('#form{$ajaxID}').prepend('<input type=\"hidden\" name=\"skip_extra\" value=\"$search_extensions\" />');
            jQuery('#form{$ajaxID}').prepend('<input type=\"hidden\" name=\"page\" value=\"1\" />');
            jQuery('#form{$ajaxID}').prepend('<input type=\"hidden\" name=\"lang\" value=\"$the_lang\" />');
            k = jQuery('#form{$ajaxID}').serialize();
           
            //jQuery.post(WHMPAjax.ajaxurl,{'params':" . whmpress_json_encode( $params ) . ",'show_price':'$show_price','show_years':'$show_years','order_landing_page':'$order_landing_page','disable_domain_spinning':'$disable_domain_spinning','action':'whmpress_action','do':'getDomainData','www_link':'$www_link','whois_link':'$whois_link','enable_transfer_link':'$enable_transfer_link','searchonly':'*','skip_extra':'$search_extensions','page':'1','lang':'" . $the_lang . "',k},function(data){
            jQuery.post(WHMPAjax.ajaxurl, k, function(data){
                jQuery('{$htmlID}').html(data);
            });
            return false;
        }";

if ( $action == "" || substr( $action, 0, 1 ) == "#" ) {
	$os = " onsubmit='return Search{$ajaxID}(this);'";
} else {
	$os = "";
}
$hidden_fields = "<!-- hidden fields -->\n";
if ( $action == "" || substr( $action, 0, 1 ) == "#" ) {
	foreach ( $_GET as $k => $v ) {
		if ( ( $style == "style3" || $style == "style2" ) && $k == "skip_extra" ) {
			
		} else if ( $k <> "search_domain" ) {
			$hidden_fields .= "<input type='hidden' name='$k' value=\"$v\" />\n";
		}
	}
}
$hidden_fields .= "<!-- end hidden fields -->\n";
//$hidden_fields .= '<input type="hidden" name="skip_extra" value="' . $search_extensions . '">';

$search_domain = isset( $_GET["search_domain"] ) ? $_GET["search_domain"] : "";

$js_script = "
            <!-- Before -->
            <script>";
if ( ! empty( $_GET['search_domain'] ) ) {
	$js_script .= "
                    jQuery(function(){
                        jQuery('#form{$ajaxID}').submit();
                    });";
}

$js_script .= "
        function Search{$ajaxID}(form) {
            whmp_page=1;
            jQuery('$htmlID').html(\"<div class='whmp_loading_div'><i class='fa fa-spinner fa-spin whmp_domain_search_ajax_results_spinner'></i> $loading_text</div>\");
            jQuery.post(WHMPAjax.ajaxurl,{'params':" . whmpress_json_encode( $params ) . ",'show_price':'$show_price','show_years':'$show_years','order_landing_page':'$order_landing_page','disable_domain_spinning':'$disable_domain_spinning','domain':jQuery('#form{$ajaxID} input[type=search]').val(),'action':'whmpress_action','do':'getDomainData','www_link':'$www_link','whois_link':'$whois_link','enable_transfer_link':'$enable_transfer_link','searchonly':'*','skip_extra':'$search_extensions','page':'1','lang':'" . $the_lang . "'},function(data){
                jQuery('{$htmlID}').html(data);
            });
            return false;
        }
        </script>";

if ( $style == "style2" ) {
	$str = <<<TAG
    <div class="form-container form-container1 pull-left">
        <div class="top-domain-search">
            <form>
                <span class="search-open"><i class="fa fa-search"></i></span>
                <input type="search" name="search_domain" class="search_box form-toggle"  value="{$sd}" placeholder="search for a domain" required="required">
            </form>
        </div>
        <div class="top-domain-search pull-left domain_search_top_hiden">
            <div class="container">
                <div class="col-lg-12 col-md-12 col-lg-ms col-xs-12">
                    <form {$ACTION} id='form{$ajaxID}' $os>
                        {$hidden_fields}
                        <span class="search-open"><i class="fa fa-search"></i></span>
                        <input type="search" required="required" class="search_box" placeholder="search for a domain " value="{$sd}" name="search_domain">
                    </form>
                    <div class="form-toggle2 form-toggle"> <a href="#" class="fa fa-close"> </a> </div>
                </div>
            </div>
        </div>
    </div>
TAG;

	$ajaxID .= "2";
	$str .= whmpress_domain_search_ajax_results_function( [
		'html_template'           => '',
		'image'                   => '',
		'searchonly'              => '*',
		'html_class'              => 'whmpress whmpress_domain_search_ajax_results',
		'html_id'                 => "{$ajaxID}",
		"whois_link"              => $whois_link,
		"www_link"                => $www_link,
		"disable_domain_spinning" => $disable_domain_spinning,
		"order_landing_page"      => $order_landing_page,
		"show_years"              => $show_years,
		"show_price"              => $show_price,
		"search_extensions"       => $search_extensions,
		"enable_transfer_link"    => $enable_transfer_link,
		"target_div"              => "$htmlID"
	] );
	
	$str .= $js_script;
	# Returning output form
	$ID    = ! empty( $html_id ) ? "id='$html_id'" : "";
	$CLASS = ! empty( $html_class ) ? "class='$html_class'" : "";
	
	return "<div $CLASS $ID>" . $str . "</div>";
} else if ( $style == "style3" ) {
	
	$js_script = "
            <!-- Before -->
            <script>";
	if ( ! empty( $_GET['search_domain'] ) ) {
		$js_script .= "
                    jQuery(function(){
                        jQuery('#form{$ajaxID}').submit();
                    });";
	}
	
	$Ps = "";
	foreach ( $params as $k => $p ) {
		$Ps .= "&params[$k]=" . urlencode( $p );
	}
	$js_script .= "
        function Search{$ajaxID}(form) {
            whmp_page=1;
            jQuery('$htmlID').html(\"<div class='whmp_loading_div'><i class='fa fa-spinner fa-spin whmp_domain_search_ajax_results_spinner'></i> $loading_text</div>\");
            //jQuery.post(WHMPAjax.ajaxurl,{'params':" . whmpress_json_encode( $params ) . ",'show_price':'$show_price','show_years':'$show_years','order_landing_page':'$order_landing_page','disable_domain_spinning':'$disable_domain_spinning','domain':jQuery('#form{$ajaxID} input[type=search]').val(),'action':'whmpress_action','do':'getDomainData','www_link':'$www_link','whois_link':'$whois_link','enable_transfer_link':'$enable_transfer_link','searchonly':'*','skip_extra':'$search_extensions','page':'1','lang':'" . $the_lang . "'},function(data){
            k = jQuery('#form{$ajaxID}').serialize();
            k += '$Ps';
            k += '&show_price={$show_price}&show_years={$show_years}&order_landing_page={$order_landing_page}';
            k += '&disable_domain_spinning=$disable_domain_spinning&action=whmpress_action&do=getDomainData';
            k += '&www_link={$www_link}&whois_link={$whois_link}&enable_transfer_link=$enable_transfer_link';
            k += '&ajax_id={$ajaxID}';
            k += '&page=1&lang={$the_lang}';
            jQuery.post(WHMPAjax.ajaxurl, k, function(data){
                jQuery('{$htmlID}').html(data);
            });
            return false;
        }
        </script>";
	
	$combo_options = "";
	foreach ( $smarty_extensions as $ext ) {
		$combo_options .= "<option value='{$ext}'>" . $ext . "</option>\n";
	}
	
	$str = <<<TAG
    <div class="whmpress whmpress_domain_search_ajax simple-01">

	<script type="text/javascript">
		jQuery(document).ready(function() {
			jQuery(".search_tlds").select2({placeholder: "Select TLD(s)"});
			jQuery("input[name='tld_toggle']").on("click", function () {
				var current = jQuery(this).val();
				if (current == "all_tlds") {
					jQuery(".select2-container").hide();
					jQuery("input[name='extention_selection']").val('0');
				} else if (current == "selected_tlds") {
					jQuery(".select2-container").show();
					jQuery("input[name='extention_selection']").val('1');
				}
			});
		});
	</script>
	<div class="form_container">
		<form name="all_tlds_form" method="get" {$ACTION} id='form{$ajaxID}' $os>
		    {$hidden_fields}
		    <input type="hidden" name="ajax_id" value="{$ajaxID}">
			<input type="search" required class="search_box" value="{$sd}"
			       placeholder="Search your desired domains here"
			       name="search_domain">
			<select name="extensions[]" multiple class="search_tlds">
				{$combo_options}
			</select>
			<button class="search_btn "><i class="fa fa-search"></i></button>
		</form>
		<div class="extension_toggle_container">
			<label>
				<input type="radio" name="tld_toggle" value="all_tlds">
				Search all
			</label><br>
			<label>
				<input checked type="radio" name="tld_toggle" value="selected_tlds">
				Select TLD's
			</label>
		</div>
		<div style="clear: both"></div>
	</div>
</div>

TAG;
	
	$str .= whmpress_domain_search_ajax_results_function( [
		'html_template'           => '',
		'image'                   => '',
		'searchonly'              => '*',
		'html_class'              => 'whmpress whmpress_domain_search_ajax_results',
		'html_id'                 => "{$ajaxID}2",
		"whois_link"              => $whois_link,
		"www_link"                => $www_link,
		"disable_domain_spinning" => $disable_domain_spinning,
		"order_landing_page"      => $order_landing_page,
		"show_years"              => $show_years,
		"show_price"              => $show_price,
		"search_extensions"       => $search_extensions,
		"enable_transfer_link"    => $enable_transfer_link,
		"target_div"              => "$htmlID"
	] );
	
	$str .= $js_script;
	# Returning output form
	$ID    = ! empty( $html_id ) ? "id='$html_id'" : "";
	$CLASS = ! empty( $html_class ) ? "class='$html_class'" : "";
	
	return "<div $CLASS $ID>" . $str . "</div>";
} else {
	$str = "<form $ACTION method='get' id='form{$ajaxID}' {$os}>";
	/*if ($action == "" || substr($action, 0, 1) == "#") {
		$str .= " onsubmit='return Search{$ajaxID}(this);'";
	}
	$str .= '>' . "\n";*/
	/*if ($action == "" || substr($action, 0, 1) == "#") {
		foreach ($_GET as $k => $v) {
			if ($k <> "search_domain")
				$str .= "<input type='hidden' name='$k' value=\"$v\" />\n";
		}
	}*/
	$str .= $hidden_fields;
	
	//$str .= '<input type="hidden" name="search_domain" value="'.$search_domain.'" />';
	//$str .= '<input type="hidden" name="params" value='.json_encode($params).'>';
	//$str .= '<input type="hidden" name="order_landing_page" value="'.$order_landing_page.'" />';
	//$str .= '<input type="hidden" name="show_price" value="'.$show_price.'" />';
	//$str .= '<input type="hidden" name="show_years" value="'.$show_years.'" />';
	$str .= '<input required="required" class="' . $text_class . '" placeholder="' . __( $placeholder, "whmpress" ) . '" value="' . $search_domain . '" type="search" id="search_box" name="search_domain">' . "\n";
	$str .= '<button class="search_btn ' . $button_class . '">' . __( $button_text, "whmpress" ) . '</button>' . "\n";
	$str .= "<div class='clear:both'></div>";
	$str .= "</form>\n";
	if ( $action == "" || substr( $action, 0, 1 ) == "#" ) {
		$str .= "<div id='$ajaxID'> <!-- Before -->";
		
		$str .= whmpress_domain_search_ajax_results_function( [
			'html_template'           => '',
			'image'                   => '',
			'searchonly'              => '*',
			'html_class'              => 'whmpress whmpress_domain_search_ajax_results',
			'html_id'                 => "{$ajaxID}2",
			"whois_link"              => $whois_link,
			"www_link"                => $www_link,
			"disable_domain_spinning" => $disable_domain_spinning,
			"order_landing_page"      => $order_landing_page,
			"show_years"              => $show_years,
			"show_price"              => $show_price,
			"search_extensions"       => $search_extensions,
			"enable_transfer_link"    => $enable_transfer_link,
			"target_div"              => "$htmlID"
		] );
		
		$str .= "</div>";
		
		$str .= "
            <!-- Before -->
            <script>";
		
		if ( ! empty( $_GET['search_domain'] ) ) {
			$str .= "
            jQuery(function(){
                jQuery('#form{$ajaxID}').submit();
            });";
		}
		
		$str .= "
        function Search{$ajaxID}(form) {
            whmp_page=1;
            jQuery('$htmlID').html(\"<div class='whmp_loading_div'><i class='fa fa-spinner fa-spin whmp_domain_search_ajax_results_spinner'></i> $loading_text</div>\");
            jQuery.post(WHMPAjax.ajaxurl,{'params':" . whmpress_json_encode( $params ) . ",'show_price':'$show_price','show_years':'$show_years','order_landing_page':'$order_landing_page','disable_domain_spinning':'$disable_domain_spinning','domain':jQuery('#form{$ajaxID} input[type=search]').val(),'action':'whmpress_action','do':'getDomainData','www_link':'$www_link','whois_link':'$whois_link','enable_transfer_link':'$enable_transfer_link','searchonly':'*','skip_extra':'$search_extensions','page':'1','lang':'" . $the_lang . "'},function(data){
                jQuery('{$htmlID}').html(data);
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