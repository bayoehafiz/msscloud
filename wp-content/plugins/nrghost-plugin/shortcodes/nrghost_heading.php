<?php
/**
 * Heading shortcode
 *
 * @package nrghost
 * @since 1.0.0
 *
 */

function nrghost_heading( $atts, $content = '', $id = '' ) {

	extract( shortcode_atts( array(
		'heading'		=> '',
		'align'			=> 'text-center',
		'style'			=> '',
		'size'			=> 'h2',
		'width'			=> '',
		'css'			=> '',
		'el_class'		=> '',
		'animation'		=> '',
	), $atts ) );

	$text_align = ' ' . esc_attr( $align );
	$class = ( !empty( $el_class ) ) ? ' ' . esc_attr( $el_class ) : '';
	$class .= vc_shortcode_custom_css_class( $css, ' ' );
	$style = ( $style == 'light' ) ? ' h-light' : ' h-dark';
	$animation = ( !empty( $animation ) ) ? ' wow ' . esc_attr( $animation ) : '';
	$width_class = ( $width == 'fullwidth' ) ? '' : ' width-50 col-sm-offset-3';

	$output  = '<div class="heading typography-block clearfix' . $width_class . $text_align . $animation . $style . $class . '">';
	$output .= '<' . esc_attr( $size ) . '>' . esc_textarea( $heading ) . '</' . esc_attr( $size ) . '>';
	$output .= ( $content ) ? '<div class="text">' . wpautop( do_shortcode( $content ) ) . '</div>' : '';
	$output .= '</div>';

	return $output;

}
add_shortcode( 'nrghost_heading', 'nrghost_heading' );