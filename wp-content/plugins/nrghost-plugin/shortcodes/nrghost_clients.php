<?php
/**
 * Clients shortcode
 * Nested item: client
 *
 * @package nrghost
 * @since 1.0.0
 *
 */

function nrghost_clients( $atts, $content = '', $id = '' ) {

	extract( shortcode_atts( array(
		'style'			=> '',
		'el_class'		=> '',
		'animation'		=> '',
	), $atts ) );

	$output  = '';
	$class = ( !empty( $el_class ) ) ? ' ' . esc_attr( $el_class ) : '';
	$animation = ( !empty( $animation ) ) ? ' ' . esc_attr( $animation ) : '';

	$output .= '<div class="block type-5 wow' . $animation . $class . '">';
	$output .= '	<div class="container">';
	$output .= '		<div class="swiper-container" data-autoplay="0" data-loop="0" data-speed="500" data-center="0" data-slides-per-view="responsive" data-xs-slides="1" data-sm-slides="3" data-md-slides="5" data-lg-slides="6">';
	$output .= '			<div class="swiper-wrapper">';
	$output .= do_shortcode( $content );
	$output .= '			</div>';
	$output .= '			<div class="pagination"></div>';
	$output .= '		</div>';
	$output .= '	</div>';
	$output .= '</div>';

	return $output;
}
add_shortcode( 'nrghost_clients', 'nrghost_clients' );



function nrghost_client( $atts, $content = '', $id = '' ) {

	extract( shortcode_atts( array(
		'title'			=> '',
		'description'	=> '',
		'image'			=> '',
		'el_class'		=> '',
	), $atts ) );

	$output = '';
	$el_class = ( !empty( $el_class ) ) ? ' ' . esc_attr( $el_class ) : '';
	$src = ( !empty( $image ) && is_numeric( $image ) ) ? wp_get_attachment_url( $image ) : false;

	if ( $src ) {
		$output .= '<div class="swiper-slide">';
		$output .= '	<img src="' . esc_url( $src ) . '" title="' . esc_attr( $title ) . '" alt="' . esc_attr( $title ) . '"/>';
		$output .= '</div>';
	}

	return $output;
}
add_shortcode( 'nrghost_client', 'nrghost_client' );