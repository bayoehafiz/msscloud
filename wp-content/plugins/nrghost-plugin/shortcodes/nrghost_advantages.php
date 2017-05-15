<?php
/**
 * Advantages shortcode
 * Nested item: advantage
 *
 * @package nrghost
 * @since 1.0.0
 *
 */

function nrghost_advantages( $atts, $content = '', $id = '' ) {

	extract( shortcode_atts( array(
		'style'				=> '',
		'main_title'		=> '',
		'main_description'	=> '',
		'background'		=> '',
		'el_class'			=> '',
		'animation'			=> '',
	), $atts ) );

	$output  = '';
	$class = ( !empty( $el_class ) ) ? ' ' . esc_attr( $el_class ) : '';
	$animation = ( !empty( $animation ) ) ? ' ' . esc_attr( $animation ) : '';
	$src = ( !empty( $background ) && is_numeric( $background ) ) ? wp_get_attachment_url( $background ) : false;

	if ( $style == '1' ) {
		global $adv_count;
		$adv_count = 0;
		$mod_content = str_replace( 'nrghost_advantage', 'nrghost_advantage_small', $content );

		$output .= '<div class="block type-7 scroll-to-block wow' . $animation . $class . '">';
		$output .= '	<div class="container">';
		$output .= '		<div class="circle-wrapper">';
		$output .= '			<div class="big-circle-container">';
		$output .= '<div class="big-circle-entry">';
		$output .= ( $src ) ? '	<img src="' . esc_url( $src ) . '" alt="Background"/>' : '';
		$output .= '	<div class="cell-view">';
		$output .= ( $main_title ) ? '		<h3 class="title">' . html_entity_decode( esc_textarea( $main_title ) ) . '</h3>' : '';
		$output .= ( $main_description ) ? '		<div class="text">' . html_entity_decode( esc_textarea( $main_description ) ) . '</div>' : '';
		$output .= '	</div>';
		$output .= '</div>';
		$output .= do_shortcode( $content );
		$output .= '</div>';
			$adv_count = 0;
		$output .= '<div class="row">';
		$output .= do_shortcode( $mod_content );
		$output .= '</div>';

		unset( $adv_count );
		$output .= '</div></div></div>';
	} elseif ( $style == '3' ) {
		$mod_content = str_replace( 'nrghost_advantage', 'nrghost_advantage_style3', $content );

		$output .= '<div class="block type-14 scroll-to-block' . $el_class . $animation . '">';
		$output .= '	<div class="container">';
		$output .= '		<div class="row wow fadeInUp">';
		$output .= do_shortcode( $mod_content );
		$output .= '		</div>';
		$output .= '	</div>';
		$output .= '</div>';
	} else {
		$output .= '<div class="block type-3 style-1 scroll-to-block wow' . $animation . $el_class . '">';
		$output .= '	<div class="container">';
		$output .= '		<div class="swiper-container" data-autoplay="0" data-loop="0" data-speed="500" data-center="0" data-slides-per-view="responsive" data-xs-slides="1" data-sm-slides="2" data-md-slides="4" data-lg-slides="4">';
		$output .= '			<div class="swiper-wrapper">';
			$mod_content = str_replace( 'nrghost_advantage', 'nrghost_advantage_style2', $content );
		$output .= do_shortcode( $mod_content );
		$output .= '			</div>';
		$output .= '			<div class="pagination"></div>';
		$output .= '		</div>';
		$output .= '	</div>';
		$output .= '</div>';
	}


	return $output;
}
add_shortcode( 'nrghost_advantages', 'nrghost_advantages' );



function nrghost_advantage( $atts, $content = '', $id = '' ) {

	extract( shortcode_atts( array(
		'title'				=> '',
		'description'		=> '',
		'image'				=> '',
		'main_title'		=> '',
		'main_description'	=> '',
		'background'		=> '',
		'el_class'			=> '',
	), $atts ) );

	$output = '';
	$el_class = ( !empty( $el_class ) ) ? ' ' . esc_attr( $el_class ) : '';
	$src = ( !empty( $background ) && is_numeric( $background ) ) ? wp_get_attachment_url( $background ) : false;
	global $adv_count;
	$adv_count++;
	if ( $adv_count > 4 ) return false;

	$output .= '<div class="big-circle-entry' . $el_class . '" data-rel="' . esc_attr( $adv_count ) . '">';
	$output .= ( $src ) ? '	<img src="' . esc_url( $src ) . '" alt="Background"/>' : '';
	$output .= '	<div class="cell-view">';
	$output .= ( $main_title ) ? '		<h3 class="title">' . html_entity_decode( esc_textarea( $main_title ) ) . '</h3>' : '';
	$output .= ( $main_description ) ? '		<div class="text">' . html_entity_decode( esc_textarea( $main_description ) ) . '</div>' : '';
	$output .= '	</div>';
	$output .= '</div>';

	return $output;
}
add_shortcode( 'nrghost_advantage', 'nrghost_advantage' );



function nrghost_advantage_small( $atts, $content = '', $id = '' ) {

	extract( shortcode_atts( array(
		'title'				=> '',
		'description'		=> '',
		'image'				=> '',
		'main_title'		=> '',
		'main_description'	=> '',
		'background'		=> '',
		'el_class'			=> '',
	), $atts ) );

	$output = '';
	$el_class = ( !empty( $el_class ) ) ? ' ' . esc_attr( $el_class ) : '';
	$src = ( !empty( $image ) && is_numeric( $image ) ) ? wp_get_attachment_url( $image ) : false;
	global $adv_count;
	$adv_count++;
	if ( $adv_count > 4 ) return false;

	$output .= '<div class="circle-entry col-md-5 col-sm-6' . $el_class . '" data-rel="' . esc_attr( $adv_count ) . '">';
	$output .= ( $src ) ? '		<div class="image-wrapper"><img src="' . esc_url( $src ) . '" alt="Icon"/></div>' : '';
	$output .= '		<div class="content">';
	$output .= ( $title ) ? '		<h3 class="title">' . esc_textarea( $title ) . '</h3>' : '';
	$output .= ( $description ) ? '		<div class="text">' . esc_textarea( $description ) . '</div>' : '';
	$output .= '		</div>';
	$output .= '	</div>';

	return $output;
}
add_shortcode( 'nrghost_advantage_small', 'nrghost_advantage_small' );



function nrghost_advantage_style2( $atts, $content = '', $id = '' ) {

	extract( shortcode_atts( array(
		'title'				=> '',
		'description'		=> '',
		'image'				=> '',
		'main_title'		=> '',
		'main_description'	=> '',
		'background'		=> '',
		'el_class'			=> '',
	), $atts ) );

	$output = '';
	$el_class = ( !empty( $el_class ) ) ? ' ' . esc_attr( $el_class ) : '';
	$src = ( !empty( $image ) && is_numeric( $image ) ) ? wp_get_attachment_url( $image ) : false;

	$output .= '<div class="swiper-slide' . $el_class . '">';
	$output .= '	<div class="icon-entry">';
	$output .= ( $src ) ? '		<img class="img-circle" src="' . esc_url( $src ) . '" alt="Image"/>' : '';
	$output .= '		<div class="content">';
	$output .= ( $title ) ? '		<h3 class="title">' . html_entity_decode( esc_textarea( $title ) ) . '</h3>' : '';
	$output .= ( $description ) ? '		<div class="text">' . html_entity_decode( esc_textarea( $description ) ) . '</div>' : '';
	$output .= '		</div>';
	$output .= '	</div>';
	$output .= '</div>';

	return $output;
}
add_shortcode( 'nrghost_advantage_style2', 'nrghost_advantage_style2' );



function nrghost_advantage_style3( $atts, $content = '', $id = '' ) {

	extract( shortcode_atts( array(
		'title'				=> '',
		'description'		=> '',
		'image'				=> '',
		'main_title'		=> '',
		'main_description'	=> '',
		'background'		=> '',
		'el_class'			=> '',
	), $atts ) );

	$output = '';
	$el_class = ( !empty( $el_class ) ) ? ' ' . esc_attr( $el_class ) : '';
	$src = ( !empty( $image ) && is_numeric( $image ) ) ? wp_get_attachment_url( $image ) : false;

	$output .= '<div class="entry col-sm-6' . $el_class . '">';
	$output .= '	<div class="image-wrapper">';
	$output .= ( $src ) ? '		<img class="img-circle" src="' . esc_url( $src ) . '" alt="Image"/>' : '';
	$output .= '	</div>';
	$output .= '	<div class="content">';
	$output .= '		<div class="cell-view">';
	$output .= ( $title ) ? '		<h3 class="title">' . html_entity_decode( esc_textarea( $title ) ) . '</h3>' : '';
	$output .= ( $description ) ? '		<div class="text">' . html_entity_decode( esc_textarea( $description ) ) . '</div>' : '';
	$output .= '		</div>';
	$output .= '	</div>';
	$output .= '</div>';

	return $output;
}
add_shortcode( 'nrghost_advantage_style3', 'nrghost_advantage_style3' );