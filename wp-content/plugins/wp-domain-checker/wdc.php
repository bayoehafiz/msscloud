<?php
/*
	Plugin Name: WP Domain Checker
	Plugin URI: http://asdqwe.net/wordpress-plugins/wp-domain-checker/
	Description: Check domain name availability for all Top Level Domains using shortcode or widget with Ajax search.
	Author: Asdqwe Dev
	Version: 3.3
	Author URI: http://asdqwe.net/wordpress-plugins/wp-domain-checker/
	Text Domain: wdc
	Domain Path: languages
 */

require_once('titan-framework/titan-framework-embedder.php');

function wdc_load_styles() {
	wp_enqueue_style( 'wdc-main-styles', plugins_url( 'assets/style.css', __FILE__ ) );
	wp_enqueue_style( 'wdc-styles-extras', plugins_url( 'assets/bootstrap-flat-extras.css', __FILE__ ) );
	wp_enqueue_style( 'wdc-styles-flat', plugins_url( 'assets/bootstrap-flat.css', __FILE__ ) );
	wp_enqueue_script( 'wdc-script', plugins_url( 'assets/script.js', __FILE__ ), array('jquery'), '1', 'true');
 	wp_localize_script( 'wdc-script', 'wdc_ajax', array(
        'ajaxurl'       => admin_url( 'admin-ajax.php'),
        'wdc_nonce'     => wp_create_nonce( 'wdc_nonce' ))
    );
  	wp_enqueue_script('recaptcha', '//www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit', array(), '1', 'true');
	
}
add_action( 'wp_enqueue_scripts', 'wdc_load_styles', 99 );
add_action( 'admin_enqueue_scripts', 'wdc_load_styles', 99 );


function header_script() {
$titan = TitanFramework::getInstance( 'wdc-options' );
$site_key = $titan->getOption( 'recaptcha_sitekey' );

?>
	<script>
		var wdcs = {};
		var x;
	    var onloadCallback = function() {
	        jQuery('div#wdc-recaptcha').each(function(i){
		     jQuery(this).attr('id','wdc' + (i+1));
		     x = i+1;
		    wdcs['wdc' + x.toString()] = grecaptcha.render('wdc'+(i+1), {
	          'sitekey' : '<?php echo $site_key; ?>',
	          'theme' : 'light'
	        });
		});
	      
		};

	</script>
<?php
}

add_action( 'wp_head', 'header_script' );

function wdc_display_func(){
	check_ajax_referer( 'wdc_nonce', 'security' );
	$titan = TitanFramework::getInstance( 'wdc-options' );
 	$whois = $titan->getOption( 'whois_option' );
 	$integration = $titan->getOption( 'integration' );
 	$extensions = $titan->getOption( 'extensions' );
 	$multi_tlds = $titan->getOption( 'wdc_multi_tlds' );
 	$ext_message = $titan->getOption( 'ext_message' );
 	$additional_button_name = $titan->getOption( 'additional_button_name' );
 	$additional_button_link = $titan->getOption( 'additional_button_link' );
    $custom_found_result_texts = $titan->getOption( 'custom_found_result_text' );
    if($custom_found_result_texts == '') $custom_found_result_texts = __('Congratulations! {domain} is available!', 'wdc');
    $custom_not_found_result_texts = $titan->getOption( 'custom_not_found_result_text' );
    if($custom_not_found_result_texts == '') $custom_not_found_result_texts = __('Sorry! {domain} is already taken!', 'wdc');
    if($ext_message == '') $ext_message = __('Sorry, we currently do not handle that particular tld.', 'wdc');



if(isset($_POST['domain']))
{
	if($integration == 'woocommerce'){
 		
 		if($_POST['item_id'] != ''){
 		$additional_button_link = $_POST['item_id'];
 		}

	}
	$domain = str_replace(array('www.', 'http://'), NULL, $_POST['domain']);
	list($sp, $split) = explode('.', $domain,2);

	if(count($split) < 1) {
		if($multi_tlds == ''){
			$multi_tlds = array('com');
		}else{
			$multi_tlds = explode(',', $multi_tlds);
		}

	}else{
		$multi_tlds = array($split);
	}


	if (function_exists('idn_to_ascii')) {
		$punny_domain = idn_to_ascii($domain);
	}else{
		$punny_domain = $domain;
		$punny_domain = preg_replace("/[^-a-zA-Z0-9.]+/", "", $punny_domain);
		$domain = $punny_domain;
	}
	$punny_domain = preg_replace("/[^-a-zA-Z0-9.]+/", "", $punny_domain);
	if(strlen($punny_domain) > 0)
	{

		include ('lib/DomainAvailability.php');  
		$Domains = new DomainAvailability();
    	list($dom, $ext) = explode('.', $punny_domain, 2);

		
		foreach($multi_tlds as $ex)
		{
	   	$domain = $dom.'.'.$ex;
	   	if($extensions != ''){
	   	$tlds = explode(',', $extensions);
		if (!in_array($ex, $tlds)) {
	    	$result = array('status'=>2,
	    					'domain'=>$domain, 
	    					'text'=> 	'<div class="callout callout-warning alert-warning clearfix">
										<div class="col-xs-10" style="padding-left:1px;text-align:left;">
										<i class="glyphicon glyphicon-exclamation-sign" style="margin-right:1px;"></i> '.$ext_message.' 
										</div>
										</div>
										');
			echo $result['text'];
			wp_die();
		}
		}

		$available = json_decode($Domains->is_available($domain));
		$custom_found_result_text = str_replace( '{domain}', $domain, $custom_found_result_texts );
		if($whois > 1) {
				$whois_link = "<a href='".get_permalink($whois)."?&domain=$domain'><button class='btn btn-danger btn-xs pull-right whois-btn'>WHOIS</button></a>";
			}else{
				$whois_link = '';
			}
		
		if($integration == 'whmcs'){
$check_ex = explode('.',$ex);

if(count($check_ex) == 2){
	$ex_name = $check_ex[0]."_".$check_ex[1];
}else{
	$ex_name = $check_ex[0];
}
			$additional_button = "<a href='javascript:void(0)' onclick='submitform_$dom_$ex_name()'><button class='btn btn-success btn-xs pull-right order-btn'>$additional_button_name</button></a>";
		}elseif($integration == 'woocommerce'){
			$additional_button = "<a href='?&add-to-cart=$additional_button_link&domain=$domain' class='btn btn-success btn-xs pull-right order-btn' >$additional_button_name</a>";
			}elseif($integration == 'custom'){
			if(!$additional_button_name == '' AND !$additional_button_link == ''){
				$additional_button_link = str_replace( '{domain}', $domain, $additional_button_link );
				$additional_button = "<a class='btn btn-success btn-xs pull-right order-btn' href='$additional_button_link'>$additional_button_name</a>";
			}else{
				$additional_button = '';
			}
		}else{
			$additional_button = '';
		}
		
		$custom_not_found_result_text = str_replace( '{domain}', $domain, $custom_not_found_result_texts );
		$whmcs = "<script type='text/javascript'>
				function submitform_$dom_$ex_name()
				{
				  document.whmcs_$dom_$ex_name.submit();
				}
				</script>
				<form method='post' name='whmcs_$dom_$ex_name' id='whmcs' action='$additional_button_link/cart.php?a=add&domain=register'>
				<input type='hidden' name='domains[]' value='$domain' >
				<input type='hidden' name='domainsregperiod[$domain]' value='1'>
				</form>";
		if ($available->status == 1) {
				$result = array('status'=>1,
								'domain'=>$domain, 
								'text'=> 	'<div class="callout callout-success alert-success clearfix available">
											<div class="col-xs-10" style="padding-left:1px;text-align:left;">
											<i class="glyphicon glyphicon-ok" style="margin-right:1px;"></i> '.$custom_found_result_text.' 
											</div>
											<div class="col-xs-2" style="padding-right:1px">'.$additional_button.' '.$whmcs.'</div>
											</div>
											');
		    	echo $result['text'];

		} elseif($available->status == 0) {
				$result = array('status'=>0,
								'domain'=>$domain, 
								'text'=> 	'<div class="callout callout-danger alert-danger clearfix not-available">
											<div class="col-xs-10" style="padding-left:1px;text-align:left;">
											<i class="glyphicon glyphicon-remove" style="margin-right:1px;"></i> '.$custom_not_found_result_text.' 
											</div>
											<div class="col-xs-2" style="padding-right:1px">'.$whois_link.'</div>
											</div>
											');
		    	echo $result['text'];
		}elseif ($available->status == 2) {
				$result = array('status'=>2,
								'domain'=>$domain, 
								'text'=> 	'<div class="callout callout-warning alert-warning clearfix notfound">
											<div class="col-xs-10" style="padding-left:1px;text-align:left;">
											<i class="glyphicon glyphicon-exclamation-sign" style="margin-right:1px;"></i> WHOIS server not found for that TLD 
											</div>
											</div>
											');
		    	echo $result['text'];
				
		}
	}
	}
	else
	{
		echo 'Please enter the domain name';
	}
}
wp_die();
}

add_action('wp_ajax_wdc_display','wdc_display_func');
add_action('wp_ajax_nopriv_wdc_display','wdc_display_func');

function wdc_display_dashboard(){
	echo do_shortcode('[wpdomainchecker]');
}

function wdc_add_dashboard_widgets() {

	wp_add_dashboard_widget(
                 'wdc_dashboard_widget',         
                 'WP Domain Checker',        
                 'wdc_display_dashboard'
                 
        );	
}
add_action( 'wp_dashboard_setup', 'wdc_add_dashboard_widgets' );

function wdc_whois_shortcode(){

	if(isset($_GET['domain'])){
		
		echo '<h3>Whois record for <b>'.htmlspecialchars($_GET['domain']).'</b></h3>';

		require("lib/whoisClass.php");
		$whois=new Whois;
		echo "<pre>";
		if (function_exists('idn_to_ascii')) {
		echo $whois->whoislookup(idn_to_ascii($_GET['domain']));
		}else{
		echo $whois->whoislookup($_GET['domain']);
		}
		echo "</pre>";
	}


		
}
add_shortcode( 'wpdomainwhois', 'wdc_whois_shortcode' );

function wdc_display_shortcode($atts){

	$titan = TitanFramework::getInstance( 'wdc-options' );
 	$item_id = $titan->getOption( 'additional_button_link' );
	$image = $titan->getOption( 'loading_image' );
	$recaptcha_enable = $titan->getOption( 'recaptcha' );
	$placeholder = $titan->getOption( 'input_placeholder' );
	$image = wp_get_attachment_image_src($image);
	if($image == '') {
		$image = plugins_url( '/images/load.gif', __FILE__ );
	}else{
		$image = $image[0];
	}
		$atts = shortcode_atts(
		array(
			'width' => '900',
			'button' => 'Check',
			'recaptcha' => 'no',
			'item_id' => $item_id,
			'tld' => ''
		), $atts );
	if($atts['recaptcha'] == 'yes'){
		$show_recaptcha = "<p> <div id='wdc-recaptcha' class='wdc' ></div></p>";
	}else{
		$show_recaptcha = "";
	}
$content = "<div id='domain-form'>
	<div id='wdc-style'>
		<form method='post' action='./' id='form' class='pure-form'> 
			<input type='hidden' name='item_id' value='{$atts['item_id']}'>
			<input type='hidden' name='tld' value='{$atts['tld']}'>
			<div class='input-group' style='max-width:{$atts["width"]}px;'>
     			<input type='text' class='form-control' autocomplete='off' id='Search' name='domain' placeholder='$placeholder'>
      				<span class='input-group-btn'>
					<button type='submit' id='Submit' class='btn btn-default btn-info'>{$atts["button"]}</button>
     	 			</span>
    		</div>
		{$show_recaptcha}
		<div id='loading'><img src='$image'></img></div>
	</form>
<div style='max-width:{$atts["width"]}px;'>
		<div id='results' class='result'></div>
</div>
	</div>
</div>";

return $content;

}


add_shortcode( 'wpdomainchecker', 'wdc_display_shortcode' );

/* Woocommerce Function */
function custom_add_to_cart_redirect() { 
	//if($_REQUEST['domain']){
    return WC()->cart->get_cart_url(); 
	//}
}
add_filter( 'woocommerce_add_to_cart_redirect', 'custom_add_to_cart_redirect' );

function save_name_on_wdc_field( $cart_item_key, $product_id = null, $quantity= null, $variation_id= null, $variation= null ) {

	WC()->session->set( $cart_item_key.'_domain', $_GET['domain'] );
	WC()->session->set( $cart_item_key.'_price', $_GET['price'] );
	
}
add_action( 'woocommerce_add_to_cart', 'save_name_on_wdc_field', 1, 5 );

add_action( 'woocommerce_before_calculate_totals', 'add_custom_price');

function add_custom_price( $cart_object ) {
	$titan = TitanFramework::getInstance( 'wdc-options' );

	global $woocommerce;
	$tld = array();
 	$extensions = $titan->getOption( 'wdc_custom_price' );
 	$extensions = preg_replace('/\s+/', '', $extensions);
 	$tlds = explode(',', $extensions);
 	
	foreach ( $woocommerce->cart->get_cart() as $cart_item_key => $cart_item ) {
	if(WC()->session->get( $cart_item_key.'_domain')){
	$domain = WC()->session->get( $cart_item_key.'_domain');
    list($domain, $ext) = explode('.', $domain, 2);
	
	 foreach ($tlds as $key => $value) {
  		$tld = explode('|', $value);
		if($ext == $tld[0]){
     		$price = $tld[1];
     		$cart_item['data']->price = $price;
		}
	}
	}
	}
 }

function render_meta_on_cart_item( $title = null, $cart_item = null, $cart_item_key = null ) {
	global $product_id;
	if( $cart_item_key && is_cart() ) {
	
		if(WC()->session->get( $cart_item_key.'_domain')){
		echo $title. '<dl class="">
				 <dt class="">Domain : </dt>
				 <dd class=""><p>'. WC()->session->get( $cart_item_key.'_domain') .'</p></dd>			
			  </dl>';
		}else{
			echo $title;
		}
	}else {
		echo $title;
	}
}
add_filter( 'woocommerce_cart_item_name', 'render_meta_on_cart_item', 1, 3 );

function render_meta_on_checkout_order_review_item( $quantity = null, $cart_item = null, $cart_item_key = null ) {
	if( $cart_item_key ) {
		if(WC()->session->get( $cart_item_key.'_domain')){
		echo $quantity. '<dl class="">
				 <dt class="">Domain : </dt>
				 <dd class=""><p>'. WC()->session->get( $cart_item_key.'_domain') .'</p></dd>
			  </dl>';
		}else{
			echo $quantity;
		}
	}
}
add_filter( 'woocommerce_checkout_cart_item_quantity', 'render_meta_on_checkout_order_review_item', 1, 3 );

function wdc_order_meta_handler( $item_id, $values, $cart_item_key ) {
	if(WC()->session->get( $cart_item_key.'_domain')){
	wc_add_order_item_meta( $item_id, "Domain", WC()->session->get( $cart_item_key.'_domain') );
	}	
	
}
add_action( 'woocommerce_add_order_item_meta', 'wdc_order_meta_handler', 1, 3 );

function wdc_force_individual_cart_items($cart_item_data, $product_id)
{
	$titan = TitanFramework::getInstance( 'wdc-options' );
 	$id = $titan->getOption( 'additional_button_link' );
	$unique_cart_item_key = md5( microtime().rand() );
	$cart_item_data['unique_key'] = $unique_cart_item_key;

	return $cart_item_data;
	
}
add_filter( 'woocommerce_add_cart_item_data','wdc_force_individual_cart_items', 10, 2 );

add_filter('woocommerce_loop_add_to_cart_link','wdc_replace_add_to_cart');
function wdc_replace_add_to_cart() {
global $product;
 $link = sprintf( '<a href="%s" rel="nofollow" data-product_id="%s" data-product_sku="%s" data-quantity="%s" class="button product_type_%s">%s</a>',
        esc_url( $product->add_to_cart_url() ),
        esc_attr( $product->id ),
        esc_attr( $product->get_sku() ),
        esc_attr( isset( $quantity ) ? $quantity : 1 ),
        esc_attr( $product->product_type ),
        esc_html( $product->add_to_cart_text() )
    );
  $links = sprintf( '<a href="%s" rel="nofollow" data-product_id="%s" data-product_sku="%s" data-quantity="%s" class="button product_type_%s">%s</a>',
        esc_url( get_permalink($product->id) ),
        esc_attr( $product->id ),
        esc_attr( $product->get_sku() ),
        esc_attr( isset( $quantity ) ? $quantity : 1 ),
        esc_attr( $product->product_type ),
        esc_html( $product->add_to_cart_text() )
    );
   
   if(get_post_meta( $product->id, 'wdc_hide_addtocart', true ) == 'yes'){
   	return $links;
   }else{
   	return $link;
   }
}

function wdc_remove_cart_button(){
$product_id = get_the_ID();
	if(get_post_meta( $product_id, 'wdc_hide_addtocart', true ) == 'yes'){
	remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
	}
}
add_action('wp','wdc_remove_cart_button');

function wdc_woo_add_custom_general_fields() {
 
  global $woocommerce, $post;
  
  echo '<div class="options_group">';
  
 	woocommerce_wp_checkbox( 
	array( 
	'id'            => 'wdc_hide_addtocart', 
	'wrapper_class' => 'wdc_item_edit_class', 
	'label'         => __('WDC?', 'wdc' ), 
	'description'   => __( 'Check me if you want to hide Add to Cart button on single product page.', 'wdc' ) 
	)
);
  
  echo '</div>';
	
}

function wdc_woo_add_custom_general_fields_save( $post_id ){
	$woocommerce_checkbox = isset( $_POST['wdc_hide_addtocart'] ) ? 'yes' : 'no';
	update_post_meta( $post_id, 'wdc_hide_addtocart', $woocommerce_checkbox );
}

// Display Fields
add_action( 'woocommerce_product_options_general_product_data', 'wdc_woo_add_custom_general_fields' );

// Save Fields
add_action( 'woocommerce_process_product_meta', 'wdc_woo_add_custom_general_fields_save' );
/* Woocommerce End Function */


function wdc_recaptcha_func() {
	check_ajax_referer( 'wdc_nonce', 'security' );

	if(isset($_POST['response']))
	{
		$titan = TitanFramework::getInstance( 'wdc-options' );
		$captcha = $_POST['response'];
		$secret_key = $titan->getOption( 'recaptcha_secretkey' );
		$response=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$secret_key."&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR']);
       	echo $response;
        
	}
wp_die();
}
add_action('wp_ajax_wdc_recaptcha','wdc_recaptcha_func');
add_action('wp_ajax_nopriv_wdc_recaptcha','wdc_recaptcha_func');

function wdc_recaptcha_dis_func() {
	check_ajax_referer( 'wdc_nonce', 'security' );

		echo json_encode(array('success' => 'true'));
        
wp_die();
}
add_action('wp_ajax_wdc_recaptcha_dis','wdc_recaptcha_dis_func');
add_action('wp_ajax_nopriv_wdc_recaptcha_dis','wdc_recaptcha_dis_func');

function wdc_options() {
    $titan = TitanFramework::getInstance( 'wdc-options' );
	global $panel;

	$panel = $titan->createAdminPanel( array(
    'name' => 'WP Domain Checker',
    'parent' => 'options-general.php',
	) );

	$generaltab = $panel->createTab( array(
    'name' => 'General',
	) );

	$generaltab->createOption( array(
    'name' => 'Custom Available Result Text',
    'id' => 'custom_found_result_text',
    'type' => 'textarea',
    'desc' => 'This is custom available result text. Use {domain} to replace domain name.'
	) );

	$generaltab->createOption( array(
    'name' => 'Custom Not Available Result Text',
    'id' => 'custom_not_found_result_text',
    'type' => 'textarea',
    'desc' => 'This is custom not available result text. Use template tag {domain} to replace domain name.'
	) );

	$generaltab->createOption( array(
    'name' => 'Input Placeholder',
    'id' => 'input_placeholder',
    'type' => 'text',
    'desc' => 'Placeholder for domain input.'
	) );

 	$generaltab->createOption( array(
    'name' => 'Loading Image',
    'id' => 'loading_image',
    'type' => 'upload',
    'desc' => 'Upload your image'
	) );
	$pages=get_pages( array('post_type' => 'page','post_status' => 'publish') );
	foreach ($pages as $page) {
		$whois_page['disable'] = 'Disable';
		$whois_page[$page->ID] = $page->post_title;
	}
	$generaltab->createOption( array(
    'name' => 'Whois Page',
    'id' => 'whois_option',
    'type' => 'select',
    'options' => $whois_page,
    'desc' => 'Enable or disable whois link if domain not available',
    'default' => 'disable',
	) );
 	
 	$generaltab->createOption( array(
    'name' => 'Integration With',
    'id' => 'integration',
    'type' => 'select',
    'options' => array(
    	'disable' => 'Disable',
        'whmcs' => 'WHMCS',
        'woocommerce' => 'Woocommerce',
        'custom' => 'Custom Link',
    ),
    'desc' => 'Enable or disable integration.',
    'default' => 'disable',
	) );

 	$generaltab->createOption( array(
    'name' => 'Integration Button Text',
    'id' => 'additional_button_name',
    'type' => 'text',
    'desc' => 'Integration Button Text. (e.g.: "ORDER NOW")'
	) );

	$generaltab->createOption( array(
    'name' => 'Integration Button Link',
    'id' => 'additional_button_link',
    'type' => 'text',
    'desc' => 'Integration button link. (e.g. for WHMCS: "http://billing.host.com"). <a href="http://asdqwe.net/wordpress-plugins/wp-domain-checker-docs/" target="_blank">Documentation</a><br>
    			For custom link, you can use template tag {domain} to include domain in the link. <br>e.g: http://godaddy.com/?aff=12345&domain={domain}'
	) );

	$generaltab->createOption( array(
    'name' => 'Supported TLD Extensions',
    'id' => 'extensions',
    'type' => 'textarea',
    'desc' => 'Allow only specific extensions to check. separate by comma for each extension. (e.g: com,net,org,co.uk,co.id)<br>Leave it blank to allow all extensions.'
	) );

	$generaltab->createOption( array(
    'name' => 'Not Supported TLD Extensions Messages',
    'id' => 'ext_message',
    'type' => 'text',
    'desc' => 'Not Supported TLD Extensions Messages. (e.g.: "Sorry, we currently do not handle that particular tld.")'
	) );

	$generaltab->createOption( array(
    'name' => 'WooCommerce Custom Price',
    'id' => 'wdc_custom_price',
    'type' => 'textarea',
    'desc' => 'Allow custom price for specific tld. (e.g: com|9,net|10,org|11,co.uk|12,co.id|13)'
	) );

	$generaltab->createOption( array(
    'name' => 'Multiple TLDs Check',
    'id' => 'wdc_multi_tlds',
    'type' => 'textarea',
    'desc' => 'Multiple TLDs check if user not define tld on the domain. (e.g: com,net,org,info)'
	) );

	$recaptchaTab = $panel->createTab( array(
    'name' => 'reCaptcha',
	) );


	$recaptchaTab->createOption( array(
    'name' => 'reCaptcha Site Key',
    'id' => 'recaptcha_sitekey',
    'type' => 'text',
    'desc' => 'Your reCaptcha Site Key. <a href="https://www.google.com/recaptcha/intro/index.html" target="_blank"> Get reCaptcha Key</a>'
	) );

	$recaptchaTab->createOption( array(
    'name' => 'reCaptcha Secret Key',
    'id' => 'recaptcha_secretkey',
    'type' => 'text',
    'desc' => 'Your reCaptcha Secret Key.'
	) );

	$wdc_styles = $panel->createTab( array(
    'name' => 'Styles',
	) );

	$wdc_styles->createOption( array(
    'name' => 'Check Button Color',
    'id' => 'check_button_color',
    'type' => 'color',
    'default' => '#5bc0de',
    'css' => '#wdc-style .btn-info { background-color: value !important;border-color:value! important; } #wdc-style input:focus {border-color:value !important}'
	));

	$wdc_styles->createOption( array(
    'name' => 'Check Button Text Color',
    'id' => 'check_button_text_color',
    'type' => 'color',
    'default' => '#fff',
    'css' => '#wdc-style .btn-info { color: value !important; }'
	));

	$wdc_styles->createOption( array(
    'name' => 'Order Button Color',
    'id' => 'order_button_color',
    'type' => 'color',
    'default' => '#5cb85c',
    'css' => '#wdc-style .order-btn { background-color: value !important;border-color: value !important; }'
	));

	$wdc_styles->createOption( array(
    'name' => 'Order Button Text Color',
    'id' => 'order_button_text_color',
    'type' => 'color',
    'default' => '#fff',
    'css' => '#wdc-style .order-btn { color: value !important; }'
	));

	$wdc_styles->createOption( array(
    'name' => 'Whois Button Color',
    'id' => 'whois_button_color',
    'type' => 'color',
    'default' => '#d9534f',
    'css' => '#wdc-style .whois-btn { background-color: value !important;border-color: value !important; }'
	));

	$wdc_styles->createOption( array(
    'name' => 'Whois Button Text Color',
    'id' => 'whois_button_text_color',
    'type' => 'color',
    'default' => '#fff',
    'css' => '#wdc-style .whois-btn { color: value !important; }'
	));

	$wdc_styles->createOption( array(
    'name' => 'Available Result Background Color',
    'id' => 'available_background_color',
    'type' => 'color',
    'default' => '#e7fadf',
    'css' => '#wdc-style .available { background-color: value !important; }'
	));

	$wdc_styles->createOption( array(
    'name' => 'Available Result Border Color',
    'id' => 'available_border_color',
    'type' => 'color',
    'default' => '#b9ceab',
    'css' => '#wdc-style .available { border-color: value !important; }'
	));

	$wdc_styles->createOption( array(
    'name' => 'Available Result Text Color',
    'id' => 'available_text_color',
    'type' => 'color',
    'default' => '#3c763d',
    'css' => '#wdc-style .available { color: value !important; }'
	));

	$wdc_styles->createOption( array(
    'name' => 'Not Available Background Color',
    'id' => 'not_available_background_color',
    'type' => 'color',
    'default' => '#fcf2f2',
    'css' => '#wdc-style .not-available { background-color: value !important; }'
	));

	$wdc_styles->createOption( array(
    'name' => 'Not Available Border Color',
    'id' => 'not_available_border_color',
    'type' => 'color',
    'default' => '#dFb5b4',
    'css' => '#wdc-style .not-available { border-color: value !important; }'
	));

	$wdc_styles->createOption( array(
    'name' => 'Not Available Result Text Color',
    'id' => 'not_available_text_color',
    'type' => 'color',
    'default' => '#a94442',
    'css' => '#wdc-style .not-available { color: value !important; }'
	));

	$customCSS = $panel->createTab( array(
    'name' => 'Custom CSS',
	) );

	$customCSS->createOption( array(
    'name' => 'Custom CSS',
    'id' => 'custom_css',
    'type' => 'code',
    'desc' => 'Put your custom CSS rules here',
    'lang' => 'css',
	));

	$panel->createOption( array(
	    'type' => 'save'
	) );

}
add_action( 'tf_create_options', 'wdc_options' );

	function hextorgb($hex) {
	   $hex = str_replace("#", "", $hex);

	   if(strlen($hex) == 3) {
	      $r = hexdec(substr($hex,0,1).substr($hex,0,1));
	      $g = hexdec(substr($hex,1,1).substr($hex,1,1));
	      $b = hexdec(substr($hex,2,1).substr($hex,2,1));
	   } else {
	      $r = hexdec(substr($hex,0,2));
	      $g = hexdec(substr($hex,2,2));
	      $b = hexdec(substr($hex,4,2));
	   }
	   $rgb = array($r, $g, $b);
	   //return implode(",", $rgb); // returns the rgb values separated by commas
	   return $rgb; // returns an array with the rgb values
}

class wdc_widget extends WP_Widget {
	function __construct() {
		parent::__construct(false, $name = __('WP Domain Checker Widget'));
	}
	function form($instance) {
			if (isset($instance['title'])) {
				$title = $instance['title'];
				$width = $instance['width'];
				$button = $instance['button'];
				$button = $instance['recaptcha'];
			}else{
			$title = "Domain Availability Check";
			}
	?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:','wdc'); ?>
				<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
			</label>
		</p>
		<p>
		<label for="<?php echo $this->get_field_id('width'); ?>"><?php _e('Width:','wdc'); ?>
			<input class="widefat" id="<?php echo $this->get_field_id('width'); ?>" name="<?php echo $this->get_field_name('width'); ?>" type="text" value="<?php echo $width; ?>" />
		</label>
		</p>
		<p>
		<label for="<?php echo $this->get_field_id('button'); ?>"><?php _e('Button Name:','wdc'); ?>
			<input class="widefat" id="<?php echo $this->get_field_id('button'); ?>" name="<?php echo $this->get_field_name('button'); ?>" type="text" value="<?php echo $button; ?>" />
		</label>
		</p>
		<p>
		<label for="<?php echo $this->get_field_id('recaptcha'); ?>"><?php _e('reCaptcha:','wdc'); ?>
			<select id="<?php echo $this->get_field_id( 'recaptcha' ); ?>" name="<?php echo $this->get_field_name( 'recaptcha' ); ?>">
            <option <?php if ( 'no' == $instance['recaptcha'] ) echo 'selected="selected"'; ?> value="no">Disable</option>
    		<option <?php if ( 'yes' == $instance['recaptcha'] ) echo 'selected="selected"'; ?> value="yes">Enable</option>
            </select>
		</label>
		</p>
	<?php
	}
	function update($new_instance, $old_instance) {
	    $instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['width'] = ( ! empty( $new_instance['width'] ) ) ? strip_tags( $new_instance['width'] ) : '';
		$instance['button'] = ( ! empty( $new_instance['button'] ) ) ? strip_tags( $new_instance['button'] ) : '';
		$instance['recaptcha'] = ( ! empty( $new_instance['recaptcha'] ) ) ? strip_tags( $new_instance['recaptcha'] ) : '';

		return $instance;
	}

	function widget($args, $instance) {
		$title = $instance['title']; if ($title == '') $title = 'Domain Availability Check';
		$width = $instance['width']; if ($width == '') $width = '500';
		$button = $instance['button']; if ($button == '') $button = 'Check';
		$recaptcha = $instance['recaptcha']; if ($recaptcha == '') $recaptcha = 'no';

		echo $args['before_widget'];
	   
	 	if ( $title ) {
	      echo $args['before_title'] . $title. $args['after_title'];
	   	}
			
		echo do_shortcode("[wpdomainchecker width='$width' button='$button' recaptcha='$recaptcha']");

	  	echo $args['after_widget'];
		}
}

function register_wdc_widget()
{
    register_widget( 'wdc_widget' );
}
add_action( 'widgets_init', 'register_wdc_widget');