<?php
/**
 * Testimonials shortcode
 * Nested item: testimonial
 *
 * @package nrghost
 * @since 1.0.0
 *
 */

function nrghost_testimonials( $atts, $content = '', $id = '' ) {

	extract( shortcode_atts( array(
		'style'			=> '',
		'active'		=> '',
		'el_class'		=> '',
		'animation'		=> '',
	), $atts ) );

	$output  = '';
	$class = ( !empty( $el_class ) ) ? ' ' . esc_attr( $el_class ) : '';
	$animation = ( !empty( $animation ) ) ? ' ' . esc_attr( $animation ) : '';
	global $t_active, $act_tstm;
	$t_active = ( $active ) ? $active : '1';

	if ( $style == '1' ) {

		$users = str_replace( 'nrghost_testimonial', 'nrghost_tstm_user', $content );

		$output .= '<div class="block type-6 style-1">';
		$output .= '<div class="container-fluid testimonials-wrapper wow' . $animation . $class . '">';
		$output .= '	<div class="row">';
		$output .= '		<div class="testimonials-container col-md-6 col-md-offset-3 col-sm-12 col-sm-offset-0">';
		$output .= '			<div class="swiper-container connected-to-bottom-swiper" data-autoplay="0" data-loop="0" data-speed="500" data-center="0" data-slides-per-view="1">';
		$output .= '				<div class="swiper-wrapper">';
			$act_tstm = 1;
		$output .= do_shortcode( $content );
		$output .= '				</div>';
		$output .= '				<div class="pagination"></div>';
		$output .= '			</div>';
		$output .= '		</div>';
		$output .= '	</div>';
		$output .= '	<div class="swiper-container connected-to-top-swiper" data-autoplay="0" data-loop="0" data-speed="500" data-center="1" data-slides-per-view="responsive" data-xs-slides="1" data-sm-slides="3" data-md-slides="7" data-lg-slides="9">';
		$output .= '		<div class="swiper-wrapper testimonials-icons">';
			$act_tstm = 1;
		$output .= do_shortcode( $users );
		$output .= '		</div>';
		$output .= '		<div class="pagination"></div>';
		$output .= '	</div>';
		$output .= '</div>';
		$output .= '</div>';

	} elseif ( $style == '2' ) {

		$users = str_replace( 'nrghost_testimonial', 'nrghost_tstm_user_style2', $content );

		$output .= '<div class="block type-6 style-1 square-slider">';
		$output .= '<div class="container-fluid testimonials-wrapper wow' . $animation . $class . '">';
		$output .= '	<div class="row">';
		$output .= '		<div class="testimonials-container col-md-6 col-md-offset-3 col-sm-12 col-sm-offset-0">';
		$output .= '			<div class="swiper-container connected-to-bottom-swiper" data-autoplay="0" data-loop="0" data-speed="500" data-center="0" data-slides-per-view="1">';
		$output .= '				<div class="swiper-wrapper">';
			$act_tstm = 1;
		$output .= do_shortcode( $content );
		$output .= '				</div>';
		$output .= '				<div class="pagination"></div>';
		$output .= '			</div>';
		$output .= '		</div>';
		$output .= '	</div>';
		$output .= '	<div class="swiper-container connected-to-top-swiper" data-autoplay="0" data-loop="0" data-speed="500" data-center="1" data-slides-per-view="responsive" data-xs-slides="1" data-sm-slides="3" data-md-slides="7" data-lg-slides="9">';
		$output .= '		<div class="swiper-wrapper testimonials-icons">';
			$act_tstm = 1;
		$output .= do_shortcode( $users );
		$output .= '		</div>';
		$output .= '		<div class="pagination"></div>';
		$output .= '	</div>';
		$output .= '</div>';
		$output .= '</div>';

	} elseif ( $style == '3' ) {

		$content2 = str_replace( 'nrghost_testimonial', 'nrghost_testimonial2', $content );

		$output .= '<div class="block type-6 style-2">';
		$output .= '	<div class="testimonials-wrapper wow' . $animation . $class . '">';
		$output .= '		<div class="swiper-container" data-autoplay="0" data-loop="0" data-speed="500" data-center="0" data-slides-per-view="1">';
		$output .= '			<div class="swiper-wrapper">';
			$act_tstm = 1;
		$output .= do_shortcode( $content2 );
		$output .= '			</div>';
		$output .= '			<div class="pagination style-1"></div>';
		$output .= '		</div>';
		$output .= '	</div>';
		$output .= '</div>';

	} elseif ( $style == '4' ) {

		$content3 = str_replace( 'nrghost_testimonial', 'nrghost_testimonial3', $content );

		$output .= '<div class="block type-15">';
		$output .= '	<div class="container">';
		$output .= '		<div class="news-wrapper wow' . $animation . $class . '">';
		$output .= '			<div class="swiper-container" data-autoplay="0" data-loop="0" data-speed="500" data-center="0" data-slides-per-view="responsive" data-xs-slides="1" data-sm-slides="1" data-md-slides="2" data-lg-slides="2">';
		$output .= '				<div class="swiper-wrapper">';
			$act_tstm = 1;
		$output .= do_shortcode( $content3 );
		$output .= '				</div>';
		$output .= '				<div class="pagination"></div>';
		$output .= '			</div>';
		$output .= '		</div>';
		$output .= '	</div>';
		$output .= '</div>';

	} elseif ( $style == '5' ) {

		$content4	= str_replace( 'nrghost_testimonial', 'nrghost_testimonial4', $content );
		$users3		= str_replace( 'nrghost_testimonial', 'nrghost_tstm_user_style3', $content );

		$output .= '<div class="block type-6">';
		$output .= '	<div class="testimonials-wrapper wow' . $animation . $class . '">';
		$output .= '		<div class="testimonials-container">';
		$output .= '			<div class="swiper-container" data-autoplay="0" data-loop="0" data-speed="500" data-center="0" data-slides-per-view="1">';
		$output .= '				<div class="swiper-wrapper">';
			$act_tstm = 1;
		$output .= do_shortcode( $content4 );
		$output .= '				</div>';
		$output .= '				<div class="pagination"></div>';
		$output .= '			</div>';
		$output .= '		</div>';
		$output .= '		<div class="row">';
		$output .= '			<div class="col-sm-3 col-md-4 col-lg-3">';
		$output .= '				<div class="testimonials-arrow left"><span class="glyphicon glyphicon-chevron-left"></span></div>';
		$output .= '				<div class="testimonials-arrow right"><span class="glyphicon glyphicon-chevron-right"></span></div>';
		$output .= '			</div>';
		$output .= '			<div class="testimonials-icons style-1 col-xs-12 col-sm-9 col-md-8 col-lg-9">';
			$act_tstm = 1;
		$output .= do_shortcode( $users3 );
		$output .= '			</div>';
		$output .= '		</div>';
		$output .= '	</div>';
		$output .= '</div>';

	} else {

		$users4 = str_replace( 'nrghost_testimonial', 'nrghost_tstm_user_style4', $content );

		$output .= '<div class="block type-6">';
		$output .= '<div class="container-fluid testimonials-wrapper wow' . $animation . $class . '">';
		$output .= '	<div class="row">';
		$output .= '		<div class="testimonials-container">';
		$output .= '			<div class="swiper-container connected-to-bottom-swiper" data-autoplay="0" data-loop="0" data-speed="500" data-center="0" data-slides-per-view="1">';
		$output .= '				<div class="swiper-wrapper">';
			$act_tstm = 1;
		$output .= do_shortcode( $content );
		$output .= '				</div>';
		$output .= '				<div class="pagination"></div>';
		$output .= '			</div>';
		$output .= '		</div>';
		$output .= '	</div>';
		$output .= '	<div class="row">';
		$output .= '		<div class="testimonials-icons row">';
			$act_tstm = 1;
		$output .= do_shortcode( $users4 );
		$output .= '		</div>';
		$output .= '	</div>';
		$output .= '</div>';
		$output .= '</div>';

	}

	unset( $t_active, $act_tstm );
	return $output;
}
add_shortcode( 'nrghost_testimonials', 'nrghost_testimonials' );



function nrghost_testimonial( $atts, $content = '', $id = '' ) {

	extract( shortcode_atts( array(
		'image'			=> '',
		'name'			=> '',
		'profession'	=> '',
		'testimonial'	=> '',
		'el_class'		=> '',
	), $atts ) );

	$output = '';
	$class = ( !empty( $el_class ) ) ? ' ' . esc_attr( $el_class ) : '';
	$src = ( !empty( $image ) && is_numeric( $image ) ) ? wp_get_attachment_url( $image ) : false;
	global $t_active, $act_tstm;

	$output .= '<div class="swiper-slide' . $class . ( $act_tstm == $t_active ? ' default-active' : '' ) . '">';
	$output .= '	<blockquote>';
	$output .= '		<p>' . html_entity_decode( $testimonial ) . '</p>';
	$output .= '		<footer><cite>' . esc_textarea( $name ) . '</cite>, ' . esc_textarea( $profession ) . '</footer>';
	$output .= '	</blockquote>';
	$output .= '</div>';

	$act_tstm++;
	return $output;
}
add_shortcode( 'nrghost_testimonial', 'nrghost_testimonial' );



function nrghost_testimonial2( $atts, $content = '', $id = '' ) {

	extract( shortcode_atts( array(
		'image'			=> '',
		'name'			=> '',
		'profession'	=> '',
		'testimonial'	=> '',
		'el_class'		=> '',
	), $atts ) );

	$output = '';
	$class = ( !empty( $el_class ) ) ? ' ' . esc_attr( $el_class ) : '';
	$src = ( !empty( $image ) && is_numeric( $image ) ) ? wp_get_attachment_url( $image ) : false;
	global $t_active, $act_tstm;

	$output .= '<div class="swiper-slide' . $class . ( $act_tstm == $t_active ? ' default-active' : '' ) . '">';
	$output .= '	<div class="row">';
	$output .= '		<div class="col-xs-10 col-xs-offset-1">';
	$output .= '			<div class="testimonials-container">';
	$output .= '				<blockquote>';
	$output .= '					<p>' . html_entity_decode( $testimonial ) . '</p>';
	$output .= '					<footer><cite>' . esc_textarea( $name ) . '</cite>, ' . esc_textarea( $profession ) . '</footer>';
	$output .= '				</blockquote>';
	$output .= '			</div>';
	$output .= '			<div class="testimonial-image">';
	$output .= '				<img class="img-circle" src="' . esc_url( $src ) . '" alt="' . esc_attr( $name ) . '" />';
	$output .= '			</div>';
	$output .= '		</div>';
	$output .= '	</div>';
	$output .= '</div>';

	$act_tstm++;
	return $output;
}
add_shortcode( 'nrghost_testimonial2', 'nrghost_testimonial2' );



function nrghost_testimonial3( $atts, $content = '', $id = '' ) {

	extract( shortcode_atts( array(
		'image'			=> '',
		'name'			=> '',
		'profession'	=> '',
		'testimonial'	=> '',
		'el_class'		=> '',
	), $atts ) );

	$output = '';
	$class = ( !empty( $el_class ) ) ? ' ' . esc_attr( $el_class ) : '';
	$src = ( !empty( $image ) && is_numeric( $image ) ) ? wp_get_attachment_url( $image ) : false;
	global $t_active, $act_tstm;

	$output .= '<div class="swiper-slide' . $class . ( $act_tstm == $t_active ? ' default-active' : '' ) . '">';
	$output .= '	<div class="blockquote-entry">';
	$output .= '		<img class="blockquote-icon img-circle" src="' . esc_url( $src ) . '" alt="' . esc_attr( $name ) . '" />';
	$output .= '		<blockquote>';
	$output .= '			<p>' . html_entity_decode( $testimonial ) . '</p>';
	$output .= '			<footer><cite>' . esc_textarea( $name ) . '</cite>, ' . esc_textarea( $profession ) . '</footer>';
	$output .= '		</blockquote>';
	$output .= '	</div>';
	$output .= '</div>';

	$act_tstm++;
	return $output;
}
add_shortcode( 'nrghost_testimonial3', 'nrghost_testimonial3' );



function nrghost_testimonial4( $atts, $content = '', $id = '' ) {

	extract( shortcode_atts( array(
		'image'			=> '',
		'name'			=> '',
		'profession'	=> '',
		'testimonial'	=> '',
		'el_class'		=> '',
	), $atts ) );

	$output = '';
	$class = ( !empty( $el_class ) ) ? ' ' . esc_attr( $el_class ) : '';
	$src = ( !empty( $image ) && is_numeric( $image ) ) ? wp_get_attachment_url( $image ) : false;
	global $t_active, $act_tstm;

	$output .= '<div class="swiper-slide' . $class . ( $act_tstm == $t_active ? ' default-active' : '' ) . '">';
	$output .= '	<blockquote>';
	$output .= '		<p>' . html_entity_decode( $testimonial ) . '</p>';
	$output .= '	</blockquote>';
	$output .= '</div>';

	$act_tstm++;
	return $output;
}
add_shortcode( 'nrghost_testimonial4', 'nrghost_testimonial4' );



function nrghost_tstm_user( $atts, $content = '', $id = '' ) {

	extract( shortcode_atts( array(
		'image'			=> '',
		'name'			=> '',
		'profession'	=> '',
		'testimonial'	=> '',
		'el_class'		=> '',
	), $atts ) );

	$output = '';
	$class = ( !empty( $el_class ) ) ? ' ' . esc_attr( $el_class ) : '';
	$src = ( !empty( $image ) && is_numeric( $image ) ) ? wp_get_attachment_url( $image ) : false;
	global $t_active, $act_tstm;

	$output .= '			<div class="entry swiper-slide' . $class . ( $act_tstm == $t_active ? ' default-active' : '' ) . '">';
	$output .= '				<div><img src="' . esc_url( $src ) . '" alt="' . esc_attr( $name ) . '" /></div>';
	$output .= '			</div>';

	$act_tstm++;
	return $output;
}
add_shortcode( 'nrghost_tstm_user', 'nrghost_tstm_user' );



function nrghost_tstm_user_style2( $atts, $content = '', $id = '' ) {

	extract( shortcode_atts( array(
		'image'			=> '',
		'name'			=> '',
		'profession'	=> '',
		'testimonial'	=> '',
		'el_class'		=> '',
	), $atts ) );

	$output = '';
	$class = ( !empty( $el_class ) ) ? ' ' . esc_attr( $el_class ) : '';
	$src = ( !empty( $image ) && is_numeric( $image ) ) ? wp_get_attachment_url( $image ) : false;
	global $t_active, $act_tstm;

	$output .= '			<div class="entry swiper-slide' . $class . ( $act_tstm == $t_active ? ' default-active' : '' ) . '">';
	$output .= '				<div' . ( $act_tstm == $t_active ? ' class="active"' : '' ) . '><img src="' . esc_url( $src ) . '" alt="' . esc_attr( $name ) . '" /></div>';
	$output .= '			</div>';

	$act_tstm++;
	return $output;
}
add_shortcode( 'nrghost_tstm_user_style2', 'nrghost_tstm_user_style2' );



function nrghost_tstm_user_style3( $atts, $content = '', $id = '' ) {

	extract( shortcode_atts( array(
		'image'			=> '',
		'name'			=> '',
		'profession'	=> '',
		'testimonial'	=> '',
		'el_class'		=> '',
	), $atts ) );

	$output = '';
	$class = ( !empty( $el_class ) ) ? ' ' . esc_attr( $el_class ) : '';
	$src = ( !empty( $image ) && is_numeric( $image ) ) ? wp_get_attachment_url( $image ) : false;
	global $t_active, $act_tstm;

	$output .= '<div class="entry' . $class . ( $act_tstm == $t_active ? ' default-active' : '' ) . '">';
	$output .= '	<div' . ( $act_tstm == $t_active ? ' class="active"' : '' ) . '>';
	$output .= '		<img class="img-circle" src="' . esc_url( $src ) . '" alt="' . esc_attr( $name ) . '" />';
	$output .= '		<span class="title">' . esc_textarea( $name ) . '</span>';
	$output .= '		<span class="text">' . esc_textarea( $profession ) . '</span>';
	$output .= '	</div>';
	$output .= '</div>';

	$act_tstm++;
	return $output;
}
add_shortcode( 'nrghost_tstm_user_style3', 'nrghost_tstm_user_style3' );



function nrghost_tstm_user_style4( $atts, $content = '', $id = '' ) {

	extract( shortcode_atts( array(
		'image'			=> '',
		'name'			=> '',
		'profession'	=> '',
		'testimonial'	=> '',
		'el_class'		=> '',
	), $atts ) );

	$output = '';
	$class = ( !empty( $el_class ) ) ? ' ' . esc_attr( $el_class ) : '';
	$src = ( !empty( $image ) && is_numeric( $image ) ) ? wp_get_attachment_url( $image ) : false;
	global $t_active, $act_tstm;

	$output .= '<div class="entry col-xs-4 col-sm-3' . $class . '">';
	$output .= '	<div' . ( $act_tstm == $t_active ? ' class="active"' : '' ) . '>';
	$output .= '		<img src="' . esc_url( $src ) . '" alt="' . esc_attr( $name ) . '" />';
	$output .= '	</div>';
	$output .= '</div>';

	$act_tstm++;
	return $output;
}
add_shortcode( 'nrghost_tstm_user_style4', 'nrghost_tstm_user_style4' );