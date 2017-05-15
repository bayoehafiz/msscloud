<?php
/**
 * Single template
 *
 * @package nrghost
 * @since 1.0.0
 *
 */

global $nrghost_opt;
nrghost_set_post_views();

get_header();

$post_format = ( get_post_format() == true ) ? get_post_format() : 'standard';

if ( $post_format == 'gallery' ) {
	$gallery = nrghost_get_post_gallery( $post->ID );
} elseif ( $post_format == 'video' OR $post_format == 'audio' ) {
	$iframe = nrghost_get_first_tag_from_string( $post->post_content );
	$post->post_content = str_replace( $iframe, '', $post->post_content );
} elseif( $post_format == 'quote' ) {
	$quote = nrghost_get_first_tag_from_string( $post->post_content, 'blockquote' );
	$post->post_content = str_replace( $quote, '', $post->post_content );
}
?>

<?php if ( have_posts() ) : ?>
<div class="container-above-header"></div>

<div class="blocks-container">

	<div class="container blog-wrapper">
		<div class="row">
			<div class="col-md-9">
				<?php while ( have_posts() ) : the_post(); ?>
					<div id="post-id-<?php the_ID(); ?>" <?php post_class( 'blog-entry wow fadeInLeft' ); ?>>
						<div class="data-column">
							<?php $date_format = '\<\s\p\a\n\>d\<\/\s\p\a\n\>M Y'; ?>
							<div class="date"><?php the_date( $date_format ); ?></div>
							<?php nrghost_post_like_link( $post->ID ); ?>
							<div class="data-entry"><span class="icon-entry views"></span><br/><span class="count"><?php nrghost_post_views(); ?></span></div>
							<?php if ( comments_open( $post->ID ) ) { ?><div class="data-entry"><a href="#single-comments" class="data-entry scrollto"><span class="icon-entry comments"></span><br/><span class="count"><?php comments_number('0','1', '%'); ?></span></a></div><?php } ?>
						</div>
						<div class="content">
							<h1 class="title"><?php the_title(); ?></h1>
							<div class="author">
								<?php nrghost_post_categories( ', ' ); ?> by <b><?php the_author_meta('display_name') ?></b>
								<?php the_tags('<div class="post-tags"> <span class="fa fa-tag col-red"></span>', '', '</div>'); ?>
							</div>

							<div class="thumbnail-entry">
								<?php if ( $post_format == 'video' && !empty( $iframe ) ) { ?>
									<div class="embed-responsive embed-responsive-16by9"><?php print $iframe; ?></div>
								<?php } elseif ( $post_format == 'audio' && !empty( $iframe ) ) { ?>
									<div class="soundcloud-wrapper"><?php print $iframe; ?></div>
								<?php } elseif ( $post_format == 'quote' && !empty( $quote ) ) { ?>
									<?php print $quote; ?>
								<?php } elseif ( $post_format == 'gallery' && !empty( $gallery ) ) { ?>
									<div class="blog-swiper">
										<div class="swiper-container" data-autoplay="0" data-loop="1" data-speed="500" data-center="0" data-slides-per-view="1">
											<div class="swiper-wrapper">
												<?php foreach ( $gallery as $image ) { ?>
													<div class="swiper-slide">
														<img class="center-image" src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ); ?>" />
													</div>
												<?php } ?>
											</div>
											<div class="pagination style-1"></div>
										</div>
									</div>
								<?php } else {
									nrghost_post_thumbnail( $post->ID, 'thumbnail-img' );
								} ?>
							</div>

							<div class="typography-block">
								<div class="medium-font">
									<?php the_content(); ?>
								</div>
							</div>
							<div class="post-pagination row"><div class="col-md-12">
								<?php $links_args = array(
									'before'		=> '<p class="post-pagination">' . esc_html__( 'Pages:', 'nrghost' ),
									'after'			=> '</p>',
								);
								wp_link_pages( $links_args ); ?>
							</div></div>
							<?php
								$prev_link = get_previous_post_link();
								$next_link = get_next_post_link();
							?>
							<?php if ( !empty( $prev_link ) || !empty( $next_link ) ) { ?><div class="prev-next-posts row"><?php } ?>
								<div class="prev-post-link col-md-5"><?php if ( !empty( $prev_link ) ) { ?><span class="prev-post"><?php previous_post_link( '%link', '<i class="fa fa-angle-left"></i> <span class="smil">Previous Post</span><br><span>%title</span>' ); ?></span><?php } ?></div>
								<div class="next-post-link col-md-5 col-md-offset-2"><?php if ( !empty( $next_link ) ) { ?><span class="next-post"><?php next_post_link( '%link', '<span class="smil">Next Post</span> <i class="fa fa-angle-right"></i><br><span>%title</span>' ); ?></span><?php } ?></div>
							<?php if ( !empty( $prev_link ) || !empty( $next_link ) ) { ?></div><?php } ?>
						</div>
					</div>
				<?php endwhile; ?>

				<div class="blog-detail-content">
					<?php if ( is_object( $nrghost_opt ) && $nrghost_opt->get_option( 'enable-related-posts' ) ) { ?>
						<?php $related_posts = nrghost_get_related_posts(); ?>
						<?php if ( $related_posts ) { ?>
						<div class="related-posts">
							<div class="row wow fadeInDown">
								<div class="block-header col-md-6 col-md-offset-3 col-sm-12 col-sm-offset-0">
									<h2 class="title"><?php esc_html_e( 'Related Posts', 'nrghost' ); ?></h2>
								</div>
							</div>

							<div class="wow fadeInUp">
								<div class="swiper-container" data-autoplay="0" data-loop="0" data-speed="500" data-center="0" data-slides-per-view="responsive" data-xs-slides="1" data-sm-slides="2" data-md-slides="2" data-lg-slides="2">
									<div class="swiper-wrapper">
										<?php foreach ( $related_posts as $post ) {
											setup_postdata( $post ); ?>
										<div class="swiper-slide">
											<div class="related-post-entry">
												<a class="image" href="<?php the_permalink(); ?>">
													<?php nrghost_custom_thumbnail( $post->ID, 370, 250 ); ?>
												</a>
												<a class="title" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
												<div class="author">
													<?php nrghost_post_categories( ', ' ); ?> by <b><?php the_author_meta( 'display_name' ) ?></b>
												</div>
												<div class="description"><?php the_excerpt(); ?></div>
											</div>
										</div>
										<?php }
										wp_reset_postdata();
										?>
									</div>
									<div class="pagination"></div>
								</div>
							</div>
						</div>
						<?php } ?>
					<?php } ?>

					<?php if( comments_open( $post->ID ) ) { ?>
						<?php comments_template(); ?>
					<?php } ?>
				</div>

			</div>
			<div class="sidebar col-md-3 wow fadeInRight">
				<?php get_sidebar( 'sidebar' ); ?>
			</div>
		</div>
	</div>

</div>
<?php endif; ?>

<?php get_footer(); ?>