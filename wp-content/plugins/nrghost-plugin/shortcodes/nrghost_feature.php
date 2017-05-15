<?php
/**
 * Feature shortcode
 *
 * @package nrghost
 * @since 1.0.0
 *
 */

function nrghost_feature( $atts, $content = '', $id = '' ) {

	extract( shortcode_atts( array(
		'title'				=> '',
		'description'		=> '',
		'image'				=> '',
		'rounded_image'		=> '',
		'style'				=> '',
		'el_class'			=> '',
		'animation'			=> '',
		'animation_delay'	=> '',
	), $atts ) );

	$output = '';

	$class = ( !empty( $el_class ) ) ? ' ' . esc_attr( $el_class ) : '';
	$animation = ( !empty( $animation ) ) ? ' ' . esc_attr( $animation ) : '';
	$animation_delay = ( !empty( $animation_delay ) ) ? ' data-wow-delay="' . esc_attr( $animation_delay ) . '"' : '';
	$src = ( !empty( $image ) && is_numeric( $image ) ) ? wp_get_attachment_url( $image ) : false;
	$maybe_rounded = ( $rounded_image == 'yes' ) ? ' class="img-circle"' : '';

	if ( $style == '2' ) {
		$output .= '<div class="popular-faq-entry wow ' . $animation . $class . '"' . $animation_delay . '>';
		$output .= '	<div class="image-wrapper">';
		$output .= ( $src ) ? '		<img' . $maybe_rounded . ' src="' . esc_url( $src ) . '" alt="Image" />' : '';
		$output .= '	</div>';
		$output .= '	<div class="content">';
		$output .= ( $title ) ? '		<h3 class="title">' . $title . '</h3>' : '';
		$output .= ( $description ) ? '		<div class="text">' . $description . '</div>' : '';
		$output .= '	</div>';
		$output .= '</div>';
		$output .= '<div class="clear"></div>';
	} else {
		$output .= '<div class="feature icon-entry wow' . $animation . $class . '"' . $animation_delay . '>';
		$output .= ( $src ) ? '		<img' . $maybe_rounded . ' src="' . esc_url( $src ) . '" alt="Image"/>' : '';
		$output .= '	<div class="content">';
		$output .= ( $title ) ? '		<h3 class="title">' . $title . '</h3>' : '';
		$output .= ( $description ) ? '		<div class="text">' . $description . '</div>' : '';
		$output .= '	</div>';
		$output .= '</div>';
	}

	return $output;

}
add_shortcode( 'nrghost_feature', 'nrghost_feature' );