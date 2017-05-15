<?php
/**
 * Plan shortcode
 *
 * @package nrghost
 * @since 1.0.0
 *
 */

function nrghost_plan( $atts, $content = '', $id = '' ) {

	extract( shortcode_atts( array(
		'title'				=> '',
		'description'		=> '',
		'image'				=> '',
		'conditions'		=> '',
		'price'				=> '',
		'old_price'			=> '',
		'period'			=> '',
		'button'			=> '',
		'el_class'			=> '',
		'animation'			=> '',
		'animation_delay'	=> '',
	), $atts ) );

	$class = ( !empty( $el_class ) ) ? ' ' . esc_attr( $el_class ) : '';
	$animation = ( !empty( $animation ) ) ? ' ' . esc_attr( $animation ) : '';
	$animation_delay = ( !empty( $animation_delay ) ) ? ' data-wow-delay="' . esc_attr( $animation_delay ) . '"' : '';
	$src = ( !empty( $image ) && is_numeric( $image ) ) ? wp_get_attachment_url( $image ) : false;
	$button = vc_build_link( $button );
	$old_price_before = ( $old_price ) ? '<b style="font-size: 30px;">' : '';
	$old_price = ( $old_price ) ? '</b><b class="line-through" style="font-size: 30px;">' . $old_price . '</b>' : '';

	$output  = '<div class="price-entry wow' . $animation . $class . '"' . $animation_delay . '>';
	if ( $src )		$output .= '	<img class="icon img-circle" src="' . esc_url( $src ) . '" alt="Plan" />';
	$output .= '	<div class="entry">';
	$output .= '		<div class="top">';
	$output .= '			<div class="cell-view">';
	$output .= '				<h3 class="title"><br/>' . esc_textarea( $title ) . '</h3>';
	$output .= '				<div class="text">' . $description . '</div>';
	$output .= '			</div>';
	$output .= '		</div>';

	if ( trim( $conditions ) ) {
		$conds = explode( "\n", $conditions );

		$output .= '		<div class="middle">';
		foreach ($conds as $cond) {
			$output .= ( trim( $cond ) ) ? '<div>' . $cond . '</div>' : '';
		}
		$output .= '		</div>';
	}

	$output .= '		<div class="bottom">';
	$output .= '			<div class="cell-view">';
	$output .= '				<div class="price">' . $old_price_before . $price . $old_price . '<span>' . $period . '</span></div>';
	$output .= '				<div class="clear"></div>';
	$output .= '				<a class="button" href="' . esc_url( $button['url'] ) . '"' . ( $button['target'] ? ' target="' . esc_attr( $button['target'] ) . '"' : '' ) . '>' . esc_textarea( $button['title'] ) . '</a>';
	$output .= '			</div>';
	$output .= '		</div>';
	$output .= '	</div>';
	$output .= '</div>';

	return $output;
}
add_shortcode( 'nrghost_plan', 'nrghost_plan' );