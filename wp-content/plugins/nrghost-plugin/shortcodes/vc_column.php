<?php
/**
 * VC ROW, VC ROW INNER, VC COLUMN and VC COLUMN INNER
 *
 * @package nrghost
 * @since 1.0
 */

function nrghost_row( $atts, $content = '', $id = '' ) {
	extract( shortcode_atts( array(
		'container'			=> 'fullwidth',
		'el_class'			=> '',
		'css'				=> '',
		'back_over_color'	=> '',
	), $atts ) );

	$class = ( $el_class ) ? " " . $el_class : '';
	$class .= vc_shortcode_custom_css_class( $css, ' ' );
	$wrap_class = ( $back_over_color ) ? ' posrel' : '';
	$maybe_back_overlay = ( $back_over_color ) ? '<div class="over_color" style="background-color: ' . $back_over_color . ';"></div>' : '';

	$container = ( $container == 'fullwidth' ) ? 'container-fluid nopadding' : 'container';

	$output  = '<div class="container-fluid blockback' . $wrap_class . '">' . $maybe_back_overlay . '<div class="' . $container . '"><div class="row' . esc_attr( $class ) . '">';
	$output .= do_shortcode( $content );
	$output .= '</div></div></div>';

	return $output;
}

function nrghost_row_inner( $atts, $content = '', $id = '' ) {
	extract( shortcode_atts( array(
		'container'		=> '',
		'el_class'		=> '',
		'css'			=> '',
	), $atts ) );

	$class = ( $el_class ) ? " " . $el_class : '';
	$class .= vc_shortcode_custom_css_class( $css, ' ' );

	$container = ( $container == 'fullwidth' ) ? 'container-fluid nopadding' : 'container';

	$output  = '<div class="' . $container . '"><div class="row ' . esc_attr( $class ) . '">';
	$output .= do_shortcode( $content );
	$output .= '</div></div>';

	return $output;
}


function nrghost_column( $atts, $content = '', $id = '' ) {
	return do_shortcode( $content );
}

// add_shortcode( 'vc_column',			'nrghost_column' );
// add_shortcode( 'vc_column_inner',	'nrghost_column' );
add_shortcode( 'vc_row',			'nrghost_row' );
add_shortcode( 'vc_row_inner',		'nrghost_row_inner' );