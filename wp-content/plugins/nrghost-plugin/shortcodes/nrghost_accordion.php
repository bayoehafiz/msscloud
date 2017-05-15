<?php
/**
 * Accordion shortcode
 * Nested item: acc_section
 *
 * @package nrghost
 * @since 1.0.0
 *
 */

function vc_accordion( $atts, $content = '', $id = '' ) {

	extract( shortcode_atts( array(
		'title'			=> '',
		'description'	=> '',
		'head_width'	=> '',
		'el_class'		=> '',
		'animation'		=> '',
	), $atts ) );

	$output = '';
	$class = ( !empty( $el_class ) ) ? ' ' . esc_attr( $el_class ) : '';
	$animation = ( !empty( $animation ) ) ? ' ' . esc_attr( $animation ) : '';
	global $active_tab;
	$active_tab = true;

	$output .= '<div class="block type-6 wow' . $animation . $class . '">';
	$output .= '	<div class="container">';
	$output .= '		<div class="accordeon-wrapper">';

	$output .= do_shortcode( $content );

	$output .= '		</div>';
	$output .= '	</div>';
	$output .= '</div>';

	unset( $active_tab );

	return $output;
}
add_shortcode( 'vc_accordion', 'vc_accordion' );



function vc_accordion_tab( $atts, $content = '', $id = '' ) {

	extract( shortcode_atts( array(
		'title'			=> '',
		'el_class'		=> '',
	), $atts ) );

	$output = '';

	$el_class = ( !empty( $el_class ) ) ? ' ' . esc_attr( $el_class ) : '';
	global $active_tab;

	$maybe_active_tab = ( $active_tab ) ? ' style="display: block;"' : '';
	$maybe_active_title = ( $active_tab ) ? ' active' : '';

	$output .= '<div class="accordeon-entry' . $maybe_active_title . '">';
	$output .= '	<div class="title">' . $title . '</div>';
	$output .= '	<div' . $maybe_active_tab . ' class="text">' . do_shortcode( $content ) . '</div>';
	$output .= '</div>';

	$active_tab = false;

	return $output;
}
add_shortcode( 'vc_accordion_tab', 'vc_accordion_tab' );