<?php
/**
 * Tabs shortcode
 * Nested item: tab
 *
 * @package nrghost
 * @since 1.0.0
 *
 */

function vc_tabs( $atts, $content = '', $id = '' ) {

	extract( shortcode_atts( array(
		'style'			=> '',
		'el_class'		=> '',
		'animation'		=> '',
	), $atts ) );

	$output = '';
	$class = ( !empty( $el_class ) ) ? ' ' . esc_attr( $el_class ) : '';
	$animation = ( !empty( $animation ) ) ? ' ' . esc_attr( $animation ) : '';
	$tabs_content = str_replace( 'vc_tab', 'vc_tab_pagin', $content );
	$sel_content = str_replace( 'vc_tab', 'vc_tab_select', $content );
	$style_class = ( $style == '2' ) ? ' style-1' : '';
	global $active_tab;
	$active_tab = true;

	$output .= '<div class="block type-12 text-image-box scroll-to-block wow' . $animation . $class . '">';
	$output .= '	<div class="container">';
	$output .= '		<div class="tabs-wrapper' . $style_class . ' wow fadeInUp">';
	$output .= '			<div class="tabs-switch-box">';
	$output .= '				<div class="tabs-desktop">';
	$output .= do_shortcode( $tabs_content );
	$output .= '				</div>';
	$output .= '				<div class="tabs-select-text"><span class="text">Secure Data</span><span class="glyphicon glyphicon-chevron-down"></span></div>';
	$output .= '				<select>';
		$active_tab = true;
	$output .= do_shortcode( $sel_content );
	$output .= '				</select>';
	$output .= '			</div>';
	$output .= do_shortcode( $content );
	$output .= '		</div>';
	$output .= '	</div>';
	$output .= '</div>';

	unset( $active_tab );

	return $output;
}
add_shortcode( 'vc_tabs', 'vc_tabs' );



function vc_tab( $atts, $content = '', $id = '' ) {

	extract( shortcode_atts( array(
		'price'			=> '',
		'title'			=> '',
		'subtitle'		=> '',
		'description'	=> '',
		'link'			=> '',
		'image'			=> '',
		'background'	=> '',
		'layout'		=> '',
		'el_class'		=> '',
	), $atts ) );

	$output = '';

	$el_class = ( !empty( $el_class ) ) ? ' ' . esc_attr( $el_class ) : '';
	global $active_tab;

	$active_style = ( $active_tab ) ? ' style="display: block;"' : '';

	$output .= '<div class="tabs-container"' . $active_style . '>';
	$output .= '	<div class="row">';
	$output .= do_shortcode( $content );
	$output .= '	</div>';
	$output .= '</div>';

	$active_tab = false;

	return $output;
}
add_shortcode( 'vc_tab', 'vc_tab' );



function vc_tab_pagin( $atts, $content = '', $id = '' ) {

	extract( shortcode_atts( array(
		'title'			=> '',
	), $atts ) );

	$output  = '';
	global $active_tab;
	$active_slide = ( $active_tab ) ? ' class="active"' : '';

	$output .= '<div' . $active_slide . '>' . $title . '</div>';

	$active_tab = false;

	return $output;
}
add_shortcode( 'vc_tab_pagin', 'vc_tab_pagin' );



function vc_tab_select( $atts, $content = '', $id = '' ) {

	extract( shortcode_atts( array(
		'title'			=> '',
	), $atts ) );

	$output  = '';

	$output .= '<option>' . $title . '</option>';

	return $output;
}
add_shortcode( 'vc_tab_select', 'vc_tab_select' );