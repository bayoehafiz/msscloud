<?php
/**
 * Latest news shortcode
 *
 * @package nrghost
 * @since 1.0.0
 *
 */

function nrghost_latest_news( $atts, $content = '', $id = '' ) {

	extract( shortcode_atts( array(
		'cats'			=> '',
		'number'		=> '',
		'el_class'		=> '',
		'animation'		=> '',
	), $atts ) );

	$output = '';
	$class = ( !empty( $el_class ) ) ? ' ' . esc_attr( $el_class ) : '';
	$animation = ( !empty( $animation ) ) ? ' ' . esc_attr( $animation ) : '';
	$number = ( !empty( $number ) ) ? ' ' . intval( $number ) : 5;

	$args = array(
		'posts_per_page'	=> $number,
		'category'			=> $cats,
		'orderby'			=> 'post_date',
		'order'				=> 'DESC',
		'post_type'			=> 'post',
		'post_status'		=> 'publish',
		'suppress_filters'	=> true
	);
	$all_posts = new WP_Query( $args );
	global $post;
	$date_format = 'M. d, Y';

	if ( $all_posts->have_posts() ) :
		$output .= '<div class="block type-15 scroll-to-block">';
		$output .= '	<div class="news-wrapper wow' . $animation . $class . '">';
		$output .= '		<div class="swiper-container" data-autoplay="0" data-loop="0" data-speed="500" data-center="0" data-slides-per-view="responsive" data-xs-slides="1" data-sm-slides="1" data-md-slides="2" data-lg-slides="2">';
		$output .= '			<div class="swiper-wrapper">';
			while( $all_posts->have_posts() ) : $all_posts->the_post();
				$output .= '<div class="swiper-slide">';
				$output .= '	<div class="news-entry">';
				$output .= '		<a class="image-wrapper" href="' . get_permalink() . '">' . nrghost_custom_thumbnail( $post->ID, 300, 300, '', false ) . '</a>';
				$output .= '		<div class="content">';
				$output .= '			<div class="data-line">';
				$output .= '				<div><span class="glyphicon glyphicon-time"></span>' . get_the_time( $date_format ) . '</div>';
				// $output .= '				<div>Tech</div>';
				$output .= '				<div><span class="glyphicon glyphicon-comment"></span>' . get_comments_number( $post->ID ) . ' comments</div>';
				$output .= '			</div>';
				$output .= '			<a class="title" href="' . get_permalink() . '">' . get_the_title() . '</a>';
				$output .= '			<div class="text">' . get_the_excerpt() . '</div>';
				$output .= '		</div>';
				$output .= '		<div class="clear"></div>';
				$output .= '	</div>';
				$output .= '</div>';
			endwhile;
		$output .= '			</div>';
		$output .= '			<div class="pagination"></div>';
		$output .= '		</div>';
		$output .= '	</div>';
		$output .= '</div>';
		wp_reset_postdata();
	endif;

	return $output;

}
add_shortcode( 'nrghost_latest_news', 'nrghost_latest_news' );