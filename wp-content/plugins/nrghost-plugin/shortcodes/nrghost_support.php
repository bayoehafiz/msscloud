<?php
/**
 * Support shortcode
 *
 * @package nrghost
 * @since 1.0.0
 *
 */

function nrghost_support( $atts, $content = '', $id = '' ) {

	extract( shortcode_atts( array(
		'title'			=> '',
		'description'	=> '',
		'background'	=> '',
		'el_class'		=> '',
		'animation'		=> '',
	), $atts ) );

	$output = '';

	$el_class = ( !empty( $el_class ) ) ? ' ' . esc_attr( $el_class ) : '';
	$animation = ( !empty( $animation ) ) ? ' ' . esc_attr( $animation ) : '';
	$src = ( !empty( $background ) && is_numeric( $background ) ) ? wp_get_attachment_url( $background ) : false;

	$output .= '<div class="block type-14' . $el_class . $animation . '">';
	$output .= ( $src ) ? '	<img class="center-image" src="' . esc_url( $src ) . '" alt="Background" />' : '';
	$output .= '	<div class="container">';
	$output .= '		<div class="row wow fadeInDown">';
	$output .= '			<div class="block-header col-md-6 col-md-offset-3 col-sm-12 col-sm-offset-0">';
	$output .= '				<h2 class="title">' . $title . '</h2>';
	$output .= '				<div class="text">' . $description . '</div>';
	$output .= '			</div>';
	$output .= '		</div>';
	$output .= '		<div class="block-button-container wow fadeInUp">';
	$output .= '			<div class="button-description">Hot Number <a href="tel:+485558453264">+48 555 8453 264</a> <br/> or</div>';
	$output .= '			<a class="button" href="contact.html">have a question? form for suggestions and comments</a>';
	$output .= '		</div>';
	$output .= '	</div>';
	$output .= '</div>';

	return $output;

}
add_shortcode( 'nrghost_support', 'nrghost_support' );