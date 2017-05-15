<?php
/**
 * Blog shortcode
 *
 * @package nrghost
 * @since 1.0.0
 *
 */

function nrghost_blog( $atts, $content = '', $id = '' ) {

	extract( shortcode_atts( array(
		'cats'			=> '',
		'order'			=> '',
		'orderby'		=> '',
		'style'			=> '',
		'el_class'		=> '',
	), $atts ) );

	$output = '';
	$class = ( !empty( $el_class ) ) ? ' ' . esc_attr( $el_class ) : '';
	$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

	$args = array(
		'posts_per_page'	=> get_option( 'posts_per_page' ),
		'category'			=> $cats,
		'orderby'			=> $orderby,
		'order'				=> $order,
		'paged'				=> $paged,
		'post_type'			=> 'post',
		'post_status'		=> 'publish',
		'suppress_filters'	=> true
	);

	$the_query = new WP_Query( $args );
	global $wp_query;
	$orig_query = $wp_query;
	$wp_query = null;
	$wp_query = $the_query;

	if ( $the_query->have_posts() ) :
		while ( $the_query->have_posts() ) : $the_query->the_post();
		global $post;
			$post_format = ( get_post_format() == true ) ? get_post_format() : 'standard';

			if ( $post_format == 'gallery' ) {
				$gallery = nrghost_get_post_gallery( $post->ID );
			} elseif ( $post_format == 'video' OR $post_format == 'audio' ) {
				$iframe = nrghost_get_first_tag_from_string( $post->post_content );
				$post->post_content = str_replace( $iframe, '', $post->post_content );
			} elseif ( $post_format == 'quote' ) {
				$quote = nrghost_get_first_tag_from_string( $post->post_content, 'blockquote' );
				$post->post_content = str_replace( $quote, '', $post->post_content );
			}

			$empty_image = ( ( $post_format == 'video' && !empty( $iframe ) ) || ( $post_format == 'audio' && !empty( $iframe ) ) || ( $post_format == 'quote' && !empty( $quote ) ) || ( $post_format == 'gallery' && !empty( $gallery ) ) || has_post_thumbnail( $post->ID ) ) ? false : true;

			if ( $style == '2' ) {

				$output .= '<div id="post-id-' . get_the_ID() . '" class="' . implode( get_post_class( 'blog-entry wow fadeInLeft' ), ' ' ) . '">';
				$output .= '	<div class="data-column">';
					$date_format = '\<\s\p\a\n\>d\<\/\s\p\a\n\>M Y';
				$output .= '		<div class="date">' . get_the_time( $date_format ) . '</div>';
				$output .= nrghost_post_like_link( $post->ID, false );
				$output .= '		<div class="data-entry"><span class="icon-entry views"></span><br/><span class="count">' . nrghost_post_views( $post->ID, false ) . '</span></div>';
				$output .= ( comments_open( $post->ID ) ) ? '<div class="data-entry"><span class="data-entry scrollto"><span class="icon-entry comments"></span><br/><span class="count">' . get_comments_number('0','1', '%') . '</span></span></div>' : '';
				$output .= '	</div>';
				$output .= '	<div class="content">';
				$output .= '		<div class="row">';
				if ( !$empty_image ) {
					$output .= '			<div class="col-md-7">';
					$output .= '				<div class="thumbnail-entry">';

						if ( $post_format == 'video' && !empty( $iframe ) ) {
					$output .= '					<div class="embed-responsive embed-responsive-16by9">' . $iframe . '</div>';
						} elseif ( $post_format == 'audio' && !empty( $iframe ) ) {
					$output .= '					<div class="soundcloud-wrapper">' . $iframe . '</div>';
						} elseif ( $post_format == 'quote' && !empty( $quote ) ) {
					$output .= $quote;
						} elseif ( $post_format == 'gallery' && !empty( $gallery ) ) {
					$output .= '					<div class="blog-swiper">';
					$output .= '						<div class="swiper-container" data-autoplay="0" data-loop="1" data-speed="500" data-center="0" data-slides-per-view="1">';
					$output .= '							<div class="swiper-wrapper">';
																foreach ( $gallery as $image ) {
					$output .= '								<div class="swiper-slide">';
					$output .= '									<img class="center-image" src="' . esc_url( $image['url'] ) . '" alt="' . esc_attr( $image['alt'] ) . '" />';
					$output .= '								</div>';
																}
					$output .= '							</div>';
					$output .= '							<div class="pagination style-1"></div>';
					$output .= '						</div>';
					$output .= '					</div>';
						} else {
					$output .= '<a href="' . esc_url( get_permalink( $post->ID ) ) . '" title="Go to ' . esc_textarea( $post->post_title ) . '">';
					$output .= nrghost_post_thumbnail( $post->ID, 'thumbnail-img', false );
					$output .= '</a>';
						}
					$output .= '				</div>';
					$output .= '			</div>';
					$output .= '			<div class="col-md-5">';
				} else {
					$output .= '			<div class="col-md-12">';
				}
				$output .= '				<a href="' . get_permalink() . '" class="title">' . get_the_title() . '</a>';
				$output .= '				<div class="author">';
				$output .= nrghost_post_categories( ', ', $post->ID, false ) . ' by <b>' . get_the_author_meta('display_name') . '</b>';
				$output .= get_the_tags('<div class="post-tags"> <span class="fa fa-tag col-red"></span>', '', '</div>');
				$output .= '				</div>';
				$output .= '				<div class="description typography-block"><div class="medium-font">' . get_the_excerpt() . '</div></div>';
				$output .= '				<a class="button" href="' . get_permalink() . '">' . __( 'Read More', 'nrghost' ) . '</a>';
				$output .= '			</div>';
				$output .= '		</div>';
				$output .= '	</div>';
				$output .= '	<div class="clear"></div>';
				$output .= '</div>';
			} else {
				$output .= '<div id="post-id-' . get_the_ID() . '" class="' . implode( get_post_class( 'blog-entry wow fadeInLeft' ), ' ' ) . '">';
				$output .= '	<div class="data-column">';
					$date_format = '\<\s\p\a\n\>d\<\/\s\p\a\n\>M Y';
				$output .= '		<div class="date">' . get_the_time( $date_format ) . '</div>';
				$output .= nrghost_post_like_link( $post->ID, false );
				$output .= '		<div class="data-entry"><span class="icon-entry views"></span><br/><span class="count">' . nrghost_post_views( $post->ID, false ) . '</span></div>';
				$output .= ( comments_open( $post->ID ) ) ? '<div class="data-entry"><span class="data-entry scrollto"><span class="icon-entry comments"></span><br/><span class="count">' . get_comments_number('0','1', '%') . '</span></span></div>' : '';
				$output .= '	</div>';
				$output .= '	<div class="content">';
				$output .= '				<div class="thumbnail-entry">';

					if ( $post_format == 'video' && !empty( $iframe ) ) {
				$output .= '					<div class="embed-responsive embed-responsive-16by9">' . $iframe . '</div>';
					} elseif ( $post_format == 'audio' && !empty( $iframe ) ) {
				$output .= '					<div class="soundcloud-wrapper">' . $iframe . '</div>';
					} elseif ( $post_format == 'quote' && !empty( $quote ) ) {
				$output .= $quote;
					} elseif ( $post_format == 'gallery' && !empty( $gallery ) ) {
				$output .= '					<div class="blog-swiper">';
				$output .= '						<div class="swiper-container" data-autoplay="0" data-loop="1" data-speed="500" data-center="0" data-slides-per-view="1">';
				$output .= '							<div class="swiper-wrapper">';
															foreach ( $gallery as $image ) {
				$output .= '								<div class="swiper-slide">';
				$output .= '									<img class="center-image" src="' . esc_url( $image['url'] ) . '" alt="' . esc_attr( $image['alt'] ) . '" />';
				$output .= '								</div>';
															}
				$output .= '							</div>';
				$output .= '							<div class="pagination style-1"></div>';
				$output .= '						</div>';
				$output .= '					</div>';
					} else {
				$output .= '<a href="' . esc_url( get_permalink( $post->ID ) ) . '" title="Go to ' . esc_textarea( $post->post_title ) . '">';
				$output .= nrghost_post_thumbnail( $post->ID, 'thumbnail-img', false );
				$output .= '</a>';
					}
				$output .= '				</div>';
				$output .= '				<a href="' . get_permalink() . '" class="title">' . get_the_title() . '</a>';
				$output .= '				<div class="author">';
				$output .= nrghost_post_categories( ', ', $post->ID, false ) . ' by <b>' . get_the_author_meta('display_name') . '</b>';
				$output .= get_the_tags('<div class="post-tags"> <span class="fa fa-tag col-red"></span>', '', '</div>');
				$output .= '				</div>';
				$output .= '				<div class="typography-block description">';
				$output .= '					<div class="medium-font">';
				$output .= get_the_excerpt();
				$output .= '					</div>';
				$output .= '				</div>';
				$output .= '				<a class="button" href="' . get_permalink() . '">' . __( 'Read More', 'nrghost' ) . '</a>';
				$output .= '	</div>';
				$output .= '</div>';
			}
		endwhile;

		$paginator = get_the_posts_pagination( array(
			'mid_size' => 3,
			'prev_text' => __( 'Prev page', 'nrghost' ),
			'next_text' => __( 'Next page', 'nrghost' ),
		) );
		$paginator = str_replace( 'class="next', 'class="next button', $paginator );
		$paginator = str_replace( 'class="prev', 'class="prev button', $paginator );
		$output .= $paginator;

		wp_reset_postdata();
		$wp_query = null;
		$wp_query = $orig_query;
	else:
		_e( 'Sorry, no posts matched your criteria.' );
	endif;


	return $output;

}
add_shortcode( 'nrghost_blog', 'nrghost_blog' );