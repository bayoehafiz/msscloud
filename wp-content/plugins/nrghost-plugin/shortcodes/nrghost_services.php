<?php
/**
 * Services shortcode
 * Nested item: service
 *
 * @package nrghost
 * @since 1.0.0
 *
 */

function nrghost_services( $atts, $content = '', $id = '' ) {

	extract( shortcode_atts( array(
		'style'			=> '',
		'el_class'		=> '',
		'animation'		=> '',
	), $atts ) );

	$output  = '';
	$class = ( !empty( $el_class ) ) ? ' ' . esc_attr( $el_class ) : '';
	$animation = ( !empty( $animation ) ) ? ' ' . esc_attr( $animation ) : '';

	$output .= '<div class="block type-1 scroll-to-block wow' . $animation . $class . '">';
	$output .= '	<div class="container">';
	$output .= '		<div class="row">';
	$output .= do_shortcode( $content );
	$output .= '		</div>';
	$output .= '	</div>';
	$output .= '</div>';

	return $output;
}
add_shortcode( 'nrghost_services', 'nrghost_services' );



function nrghost_service( $atts, $content = '', $id = '' ) {

	extract( shortcode_atts( array(
		'title'			=> '',
		'description'	=> '',
		'image'			=> '',
		'el_class'		=> '',
	), $atts ) );

	$output = '';
	$el_class = ( !empty( $el_class ) ) ? ' ' . esc_attr( $el_class ) : '';
	$src = ( !empty( $image ) && is_numeric( $image ) ) ? wp_get_attachment_url( $image ) : false;

	$output .= '<div class="icon-entry col-xs-12 col-sm-6 col-md-4">';
		if ( $src )			$output .= '	<img src="' . esc_url( $src ) . '" alt="Icon"/>';
	$output .= '	<div class="content">';
		if ( $title )		$output .= '		<h3 class="title">' . esc_textarea( $title ) . '</h3>';
		if ( $description )	$output .= '		<div class="text">' . $description . '</div>';
	$output .= '	</div>';
	$output .= '</div>';

	return $output;
}
add_shortcode( 'nrghost_service', 'nrghost_service' );