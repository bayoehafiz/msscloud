<?php
/**
 * Comments template
 *
 * @package nrghost
 * @since 1.0.0
 *
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}
?>

<div id="single-comments" class="blog-comments">
	<div class="row wow fadeInDown">
		<div class="block-header col-md-6 col-md-offset-3 col-sm-12 col-sm-offset-0">
			<h2 class="title">Comments (<span><?php echo esc_attr( get_comments_number() ); ?></span>)</h2>
		</div>
	</div>
	<div id="single-comments" class="last-comment">
		<?php
			$comments_walker = new Nrghost_Walker_Comment();

			$args = array(
				'walker'            => $comments_walker,
				'max_depth'			=> '',
				'style'				=> 'div',
				'callback'			=> null,
				'end-callback'		=> null,
				'type'				=> 'all',
				'reply_text'		=> '<span class="glyphicon glyphicon-comment"></span>' . esc_html__( 'Reply', 'nrghost' ),
				'page'				=> '',
				'per_page'			=> -1,
				'avatar_size'		=> 70,
				'echo'				=> true     // boolean, default is true
			);

			wp_list_comments( $args );
			paginate_comments_links();
		?>
	</div>

	<?php nrghost_comment_form(); ?>
</div>