<?php
/**
 * Call to action shortcode
 *
 * @package nrghost
 * @since 1.0.0
 *
 */

function nrghost_cta( $atts, $content = '', $id = '' ) {

	extract( shortcode_atts( array(
		'title'			=> '',
		'el_class'		=> '',
		'animation'		=> '',
	), $atts ) );

	$output = '';

	$el_class = ( !empty( $el_class ) ) ? ' ' . esc_attr( $el_class ) : '';
	$animation = ( !empty( $animation ) ) ? ' ' . esc_attr( $animation ) : '';

	$output .= '<div class="block type-9' . $el_class . $animation . '">';
	$output .= '	<div class="container">';
	$output .= '		<div class="entry">';
	$output .= '			<h3 class="title wow fadeInLeft">' . esc_textarea( $title ) . '</h3>';
	$output .= '			<div class="text wow fadeInRight">' . do_shortcode( $content ) . '</div>';
	$output .= '		</div>';
	$output .= '	</div>';
	$output .= '</div>';

	return $output;

}
add_shortcode( 'nrghost_cta', 'nrghost_cta' );