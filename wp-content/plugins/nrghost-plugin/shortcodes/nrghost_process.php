<?php
/**
 * Process shortcode
 * Nested item: step
 *
 * @package nrghost
 * @since 1.0.0
 *
 */

function nrghost_process( $atts, $content = '', $id = '' ) {

	extract( shortcode_atts( array(
		'el_class'		=> '',
		'animation'		=> '',
	), $atts ) );

	$output = '';
	$class = ( !empty( $el_class ) ) ? ' ' . esc_attr( $el_class ) : '';
	$animation = ( !empty( $animation ) ) ? ' ' . esc_attr( $animation ) : '';
	global $step_count;
	$step_count = 0;
	$mod_content = str_replace('nrghost_process_step', 'nrghost_step_details', $content);

	$output .= '<div class="block type-13 scroll-to-block wow' . $animation . $class . '">';
	$output .= '	<div class="container">';
	$output .= '		<div class="circle-slide-box">';
	$output .= '			<div class="swiper-container connected-to-bottom-swiper" data-autoplay="0" data-loop="0" data-speed="500" data-center="1" data-slides-per-view="responsive" data-xs-slides="3" data-sm-slides="3" data-md-slides="5" data-lg-slides="5">';
	$output .= '				<div class="swiper-wrapper">';
	$output .= do_shortcode( $content );
	$output .= '				</div>';
	$output .= '				<div class="pagination"></div>';
	$output .= '			</div>';
	$output .= '		</div>';
	$output .= '		<div class="circle-description-slide-box row">';
	$output .= '				<div class="swiper-container connected-to-top-swiper" data-autoplay="0" data-loop="0" data-speed="500" data-center="0" data-slides-per-view="1">';
	$output .= '					<div class="swiper-wrapper">';
		$step_count = 0;
	$output .= do_shortcode( $mod_content );
	$output .= '					</div>';
	$output .= '					<div class="pagination"></div>';
	$output .= '				</div>';
	$output .= '		</div>';
	$output .= '	</div>';
	$output .= '</div>';

	return $output;
}
add_shortcode( 'nrghost_process', 'nrghost_process' );



function nrghost_process_step( $atts, $content = '', $id = '' ) {

	extract( shortcode_atts( array(
		'title'			=> '',
		'description'	=> '',
		'image'			=> '',
		'el_class'		=> '',
	), $atts ) );

	$output = '';

	$el_class = ( !empty( $el_class ) ) ? ' ' . esc_attr( $el_class ) : '';
	$src = ( !empty( $image ) && is_numeric( $image ) ) ? wp_get_attachment_url( $image ) : false;
	global $step_count;
	$active_step_class = ( $step_count == 1 ) ? ' default-active' : '';

	$output .= '<div class="swiper-slide' . $el_class . '">';
	$output .= '	<img class="img-circle" src="' . esc_url( $src ) . '" alt="Step" />';
	$output .= '</div>';

	return $output;
}
add_shortcode( 'nrghost_process_step', 'nrghost_process_step' );



function nrghost_step_details( $atts, $content = '', $id = '' ) {

	extract( shortcode_atts( array(
		'title'			=> '',
		'description'	=> '',
		'image'			=> '',
		'el_class'		=> '',
	), $atts ) );

	$output  = '';

	$el_class = ( !empty( $el_class ) ) ? ' ' . esc_attr( $el_class ) : '';
	global $step_count;
	$active_step_class = ( $step_count == 1 ) ? ' default-active' : '';

	$output .= '<div class="swiper-slide' . $active_step_class . $el_class . '">';
	$output .= '	<div class="col-md-6 col-md-offset-3 col-sm-12 col-sm-offset-0">';
	$output .= '		<h3 class="title">' . $title . '</h3>';
	$output .= '		<div class="text">' . $description . '</div>';
	$output .= '	</div>';
	$output .= '</div>';

	return $output;
}
add_shortcode( 'nrghost_step_details', 'nrghost_step_details' );