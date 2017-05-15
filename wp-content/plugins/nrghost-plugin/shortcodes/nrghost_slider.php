<?php
/**
 * Slider shortcode
 * Nested item: slide
 *
 * @package nrghost
 * @since 1.0.0
 *
 */

function nrghost_slider( $atts, $content = '', $id = '' ) {

	extract( shortcode_atts( array(
		'style'			=> '',
		'arrows'		=> '',
		'loop'			=> '',
		'pager'			=> '',
		'el_class'		=> '',
		'animation'		=> '',
	), $atts ) );

	$class = ( !empty( $el_class ) ) ? ' ' . esc_attr( $el_class ) : '';
	$animation = ( !empty( $animation ) ) ? ' ' . esc_attr( $animation ) : '';
	global $slider_style;
	$slider_style = $style;

	$pag_class = ( $style == '2' || $style == '3' ) ? ' style-1' : '';
	$slider_class = ( $style == '2' ) ? ' style-1 background-block' : '';
	$slider_class = ( $style == '3' ) ? ' style-1 background-block video-slider' : '';

	$output  = '<div class="block type-10' . $pag_class . $slider_class . $class . '">';

	if ($pager == 'top' ) {
		$output .= '<div class="banner-tabs">
		<div class="container-fluid">
			<div class="row">';
		$new_content = str_replace( 'nrghost_slide', 'nrghost_slide_pagin', $content );
		global $active_slide;
		$active_slide = true;
		$output .= do_shortcode( $new_content );
		unset( $active_slide );
		$output .= '</div></div></div>';
	}

	$output .= '<div class="main-banner-height">
		<div class="swiper-container" data-autoplay="0" data-loop="' . ( $loop ? '1' : '0' ) . '" data-speed="500" data-center="0" data-slides-per-view="1">
			<div class="swiper-wrapper">';
	$output .= do_shortcode( $content );
	$output .= '		</div>';

	if ( $arrows == 'yes' ) {
		$output .= '		<div class="swiper-arrow left"><span class="glyphicon glyphicon-chevron-left"></span></div>';
		$output .= '		<div class="swiper-arrow right"><span class="glyphicon glyphicon-chevron-right"></span></div>';
	}

	$output .= '		<div class="pagination' . $pag_class . '"></div>';
	$output .= '	</div>';
	$output .= '</div>';
	if ( $pager == 'bottom' || $pager == '' ) {
		$output .= '<div class="banner-tabs">
		<div class="container-fluid">
			<div class="row">';
		$new_content = str_replace( 'nrghost_slide', 'nrghost_slide_pagin', $content );
		global $active_slide;
		$active_slide = true;
		$output .= do_shortcode( $new_content );
		unset( $active_slide );
		$output .= '</div></div></div>';
	}
	$output .= '</div>';
	unset( $slider_style );

	return $output;
}
add_shortcode( 'nrghost_slider', 'nrghost_slider' );



function nrghost_slide( $atts, $content = '', $id = '' ) {

	extract( shortcode_atts( array(
		'price'			    => '',
		'title'			    => '',
		'subtitle'		    => '',
		'description'	    => '',
		'link'			    => '',
		'link2'			    => '',
		'image'			    => '',
        'extra_option'      => '',
        'image_button'      => '',
        'image_video_link'  => '',
		'background'	    => '',
		'layout'		    => '',
		'el_class'		    => '',
	), $atts ) );

	global $slider_style;

	$output = '';

	$el_class = ( !empty( $el_class ) ) ? ' ' . esc_attr( $el_class ) : '';

	$src = ( !empty( $image ) && is_numeric( $image ) ) ? wp_get_attachment_url( $image ) : false;
	$back_img_src = ( !empty( $background ) && is_numeric( $background ) ) ? wp_get_attachment_url( $background ) : false;

	$link = vc_build_link( $link );
	$link = ( !empty( $link['url'] ) && !empty( $link['title'] ) ) ? $link : false;
	if ( $link ) $link['target'] = ( $link['target'] ) ? ' target="' . $link['target'] . '"' : '';

	$link2 = vc_build_link( $link2 );
	$link2 = ( !empty( $link2['url'] ) && !empty( $link2['title'] ) ) ? $link2 : false;
	if ( $link2 ) $link2['target'] = ( $link2['target'] ) ? ' target="' . $link2['target'] . '"' : '';

	$content = '				<div class="vertical-align">';
	$content .= '					<div class="content text-entry">';
	if ( $price )				$content .= '						<div class="price">' . $price . '</div>';
	if ( $title )				$content .= '						<h3 class="title">' . $title . '</h3>';
	if ( $slider_style == '3' )	$content .= '						<h4 class="subtitle">' . $subtitle . '</h4>';
	if ( $description )			$content .= '						<div class="text typography-block">' . $description . '</div>';
	if ( $link )				$content .= '						<a class="button" href="' . esc_url( $link['url'] ) . '"' . esc_attr( $link['target'] ) . '>' . esc_textarea( $link['title'] ) . '</a>';
	if ( $link2 )				$content .= '						<a class="button" href="' . esc_url( $link2['url'] ) . '"' . esc_attr( $link2['target'] ) . '>' . esc_textarea( $link2['title'] ) . '</a>';
	$content .= '					</div>';
	$content .= '				</div>';

	if ( !empty( $src ) ) {
        $img_xtra = '';
        if ($extra_option == 'image-button') {
            $image_link = vc_build_link( $image_button );
            $image_link = ( !empty( $image_link['url'] ) && !empty( $image_link['title'] ) ) ? $image_link : false;
            if ( $image_link ) $image_link['target'] = ( $image_link['target'] ) ? ' target="' . $image_link['target'] . '"' : '';
            $img_xtra .= ( $image_link ) ? '<div class="image-overlay"><a class="button" href="' . esc_url( $image_link['url'] ) . '">' . $image_link['title'] . '</a></div>' : '';
        } elseif ( $extra_option == 'image-video' ) {
            $img_xtra .= ( $image_video_link ) ? '<div class="image-overlay"><img class="video-open" src="' . get_template_directory_uri() . '/assets/img/icon-117.png" alt="Video-image" data-src="' . esc_url( $image_video_link ) . '" /></div>' : '';
            global $videoplayer_active;
            $videoplayer_active = true;
        }

		$image  = '				<div class="vertical-align">';
		$image .= '					<div class="content">';
		$image .= '						<img src="' . esc_url( $src ) . '" alt="Slide img" />';
		$image .= ( $img_xtra ) ? '						' . $img_xtra : '';
		$image .= '					</div>';
		$image .= '				</div>';
	} else {
		$image = '';
	}

	$output .= '<div class="swiper-slide' . $el_class . '">';


	$output .= ( !empty( $back_img_src ) ) ? '<img class="center-image" src="' . $back_img_src . '" alt="back slide image" />' : '';

	$output .= '	<div class="container">';
	$output .= '		<div class="slide-container">';
	$output .= '			<div class="slide-block nopadding col-sm-6">';
		$output .= ( $layout == 'left' ) ? $image : $content;
	$output .= '			</div>';
	$output .= '			<div class="slide-block nopadding col-sm-6">';
		$output .= ( $layout == 'left' ) ? $content : $image;
	$output .= '			</div>';
	$output .= '		</div>';
	$output .= '	</div>';
	$output .= '</div>';

	return $output;
}
add_shortcode( 'nrghost_slide', 'nrghost_slide' );



function nrghost_slide_pagin( $atts, $content = '', $id = '' ) {

	extract( shortcode_atts( array(
		'price'			=> '',
		'title'			=> '',
		'subtitle'		=> '',
		'description'	=> '',
		'link'			=> '',
		'image'			=> '',
		'layout'		=> '',
		'el_class'		=> '',
	), $atts ) );

	$output  = '';

	$el_class = ( !empty( $el_class ) ) ? ' ' . esc_attr( $el_class ) : '';

	global $active_slide;
	$active = ( $active_slide ) ? ' active' : '';

	$output .= '<div class="tab-entry' . $active . $el_class . ' col-md-3">';
	if ( $title ) 		$output .= '	<div class="title">' . $title . '</div>';
	if ( $subtitle )	$output .= '	<div class="text">' . $subtitle . '</div>';
	$output .= '</div>';

	$active_slide = false;

	return $output;
}
add_shortcode( 'nrghost_slide_pagin', 'nrghost_slide_pagin' );