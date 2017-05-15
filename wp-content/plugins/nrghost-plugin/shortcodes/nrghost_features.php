<?php
/**
 * Features shortcode
 * Nested item: nested_feature
 *
 * @package nrghost
 * @since 1.0.0
 *
 */

function nrghost_features( $atts, $content = '', $id = '' ) {

	extract( shortcode_atts( array(
		'style'			=> '',
		'el_class'		=> '',
		'animation'		=> '',
	), $atts ) );

	$output  = '';
	$class = ( !empty( $el_class ) ) ? ' ' . esc_attr( $el_class ) : '';
	$animation = ( !empty( $animation ) ) ? ' ' . esc_attr( $animation ) : '';
	global $f_count;
	$f_count = 0;

	$style = ( $style == '2' ) ? ' style-1' : '';

	$output .= '<div class="block type-16 wow' . $animation . $class . $style . '">';
	$output .= '	<div class="container">';
	$output .= '		<div class="timeline">';
	$output .= '			<div class="row">';

	$output .= do_shortcode( $content );

	$output .= '			</div>';
	$output .= '		</div>';
	$output .= '	</div>';
	$output .= '</div>';

	unset( $f_count );

	return $output;
}
add_shortcode( 'nrghost_features', 'nrghost_features' );



function nrghost_nested_feature( $atts, $content = '', $id = '' ) {

	extract( shortcode_atts( array(
		'title'			=> '',
		'description'	=> '',
		'image'			=> '',
		'date'			=> '',
		'el_class'		=> '',
	), $atts ) );

	$output = '';
	$el_class = ( !empty( $el_class ) ) ? ' ' . esc_attr( $el_class ) : '';
	$src = ( !empty( $image ) && is_numeric( $image ) ) ? wp_get_attachment_url( $image ) : false;
	global $f_count;
	$f_count++;
	$fadeclass = ($f_count % 2 == 0) ? 'fadeInRight' : 'fadeInLeft';

	$output .= '<div class="col-md-6 timeline-entry wow ' . $fadeclass . '">';
	$output .= '	<div class="timeline-entry-container">';
	$output .= '		<div class="image-wrapper">';
	$output .= ( $src ) ? '			<img class="img-circle" src="' . esc_url( $src ) . '" alt="Feature" />' : '';
	$output .= '		</div>';
	$output .= '		<div class="content">';
	$output .= '			<div class="cell-view">';
	$output .= '				<div class="date">' . $date . '</div>';
	$output .= '				<h3 class="title">' . $title . '</h3>';
	$output .= '				<div class="text">' . $description . '</div>';
	$output .= '			</div>';
	$output .= '		</div>';
	$output .= '	</div>';
	$output .= '</div>';

	return $output;
}
add_shortcode( 'nrghost_nested_feature', 'nrghost_nested_feature' );