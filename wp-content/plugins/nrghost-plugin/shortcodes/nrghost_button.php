<?php
/**
 * Button shortcode
 *
 * @package nrghost
 * @since 1.0.0
 *
 */

function nrghost_button( $atts, $content = '', $id = '' ) {

	extract( shortcode_atts( array(
		'link'			=> '',
		'icon'			=> '',
		'align'			=> '',
		'el_class'		=> '',
		'animation'		=> '',
	), $atts ) );

	$output = '';

	$class = ( !empty( $el_class ) ) ? ' ' . esc_attr( $el_class ) : '';
	$animation = ( !empty( $animation ) ) ? ' ' . esc_attr( $animation ) : '';
	$animation_delay = ( !empty( $animation_delay ) ) ? ' data-wow-delay="' . esc_attr( $animation_delay ) . '"' : '';
	$align = ( $align ) ? ' ' . $align : ' text-center';
	$link = vc_build_link( $link );
	$url = $link['url'];
	$title = $link['title'];
	$target = ( $link['target'] ) ? ' target="' . $link['target'] . '"' : '';

	if ( $url && $title ) {
		$output .= '<div class="block-button-container wow' . $animation . $class . $align . '"' . $animation_delay . '>';
		$output .= '	<a href="' . esc_url( $url ) . '" class="button"' . $target . '>';
		$output .= ( $icon ) ? '<span class="icon"><span class="glyphicon glyphicon-map-marker"></span></span>' : '';
		$output .= esc_textarea( $title );
		$output .= '</a>';
		$output .= '</div>';
	}

	return $output;

}
add_shortcode( 'nrghost_button', 'nrghost_button' );