<?php
$atts = shortcode_atts( [
	'link_text'     => 'View Cart',
	'html_template' => '',
], $args[0] );

extract( $atts );

if ( ! function_exists( 'is_json' ) ) {
	function is_json( $string ) {
		json_decode( $string );

		return ( json_last_error() == JSON_ERROR_NONE );
	}
}

$WHMP = new WHMPress_Client_Area;

$url  = $WHMP->whmp_http();
$base = basename( $url );
if ( strpos( $base, "index?" ) !== false ) {
	$url = str_replace( "index?", "index.php?", $url );
}
$url .= "&whmp_cart=";

$json = $WHMP->read_remote_url( $url );
if ( is_json( $json ) ) {
	$link_url = get_option( 'client_area_page_url' );
	if ( is_numeric( $link_url ) ) {
		$link_url = get_page_link( $link_url );
	}
	$link_url = rtrim( $link_url, "/" );

	if ( $WHMP->is_permalink() ) {
		$link_url .= "/cart/a/view/";
	} else {
		$q = @parse_url( $link_url, PHP_URL_QUERY );
		if ( $q == "" || is_null( $q ) || $q === false ) {
			$link_url .= "?whmpca=cart&a=view";
		} else {
			$link_url .= "&whmpca=cart&a=view";
		}
	}

	if ( $link_text == "" ) {
		$link_text = __( "View Cart", "whmpress" );
	}

	$data = json_decode( $json, true );

	## Getting template filepath.
	$html_template = $WHMP->get_template_file( $html_template, $args[2] );

	if ( is_file( $html_template ) ) {
		$vars = [
			"link_url"    => $link_url,
			"link_text"   => $link_text,
			"total_items" => $data["total_items"],
		];

		$TemplateArray = $WHMP->get_template_array( $args[2] );
		foreach ( $TemplateArray as $custom_field ) {
			$vars[ $custom_field ] = isset( $atts[ $custom_field ] ) ? $atts[ $custom_field ] : "";
		}
		echo $WHMP->smarty_template( $html_template, $vars );
	} else {
		?>
		<div class="whmp_cart">
			<a href="<?php echo $link_url ?>" class="whmp_cart_link">
				<i class="fa fa-shopping-cart"></i>
				<span class="hidden-xs whmp_cart_label"><?php echo $link_text ?> </span>
				(<span class="whmp_cart_quantity"><?php echo $data["total_items"]; ?></span>)
			</a>
		</div>
	<?php }
}