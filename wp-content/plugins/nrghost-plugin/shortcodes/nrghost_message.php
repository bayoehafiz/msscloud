<?php
/**
 * Message shortcode
 *
 * @package nrghost
 * @since 1.0.0
 *
 */

function nrghost_message( $atts, $content = '', $id = '' ) {

	extract( shortcode_atts( array(
		'style'			=> '',
		'close_button'	=> '',
		'el_class'		=> '',
		'animation'		=> '',
	), $atts ) );

	$output = '';

	switch ( $style ) {
		case 'danger':
			$m_style = ' alert alert-danger alert-dismissible';
			break;
		case 'warning':
			$m_style = ' alert alert-warning';
			break;
		case 'success':
			$m_style = ' alert alert-success';
			break;

		default:
			$m_style = ' alert alert-info';
			break;
	}
	$class = ( !empty( $el_class ) ) ? ' ' . esc_attr( $el_class ) : '';
	$animation = ( !empty( $animation ) ) ? ' wow ' . esc_attr( $animation ) : '';

	$output .= '<div class="' . $m_style . $class . $animation . '" role="alert">';
	$output .= ( $close_button == 'yes' ) ? '	<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' : '';
	$output .= do_shortcode( $content );
	$output .= '</div>';

	return $output;

}
add_shortcode( 'nrghost_message', 'nrghost_message' );