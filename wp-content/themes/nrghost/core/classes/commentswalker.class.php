<?php
/**
 * HTML comment list class.
 *
 * @uses Walker
 * @package nrghost
 * @since 1.0.0
 */

if ( class_exists( 'Walker_Comment' ) && !class_exists( 'Nrghost_Walker_Comment' ) ) {
	class Nrghost_Walker_Comment extends Walker_Comment {
		public $tree_type = 'comment';

		public $db_fields = array ('parent' => 'comment_parent', 'id' => 'comment_ID');

		/**
		 * Start the list before the elements are added.
		 *
		 * @package nrghost
		 * @since 1.0.0
		 */
		public function start_lvl( &$output, $depth = 0, $args = array() ) {
			$GLOBALS['comment_depth'] = $depth + 1;

			switch ( $args['style'] ) {
				case 'div':
					break;
				case 'ol':
					$output .= '<ol class="children">' . "\n";
					break;
				case 'ul':
				default:
					$output .= '<ul class="children">' . "\n";
					break;
			}
		}

		/**
		 * End the list of items after the elements are added.
		 *
		 * @package nrghost
		 * @since 1.0.0
		 */
		public function end_lvl( &$output, $depth = 0, $args = array() ) {
			$GLOBALS['comment_depth'] = $depth + 1;

			switch ( $args['style'] ) {
				case 'div':
					break;
				case 'ol':
					$output .= "</ol><!-- .children -->\n";
					break;
				case 'ul':
				default:
					$output .= "</ul><!-- .children -->\n";
					break;
			}
		}

		/**
		 * Start the element output.
		 *
		 * @package nrghost
		 * @since 1.0.0
		 */
		public function start_el( &$output, $comment, $depth = 0, $args = array(), $id = 0 ) {
			$depth++;
			$GLOBALS['comment_depth'] = $depth;
			$GLOBALS['comment'] = $comment;

			if ( !empty( $args['callback'] ) ) {
				ob_start();
				call_user_func( $args['callback'], $comment, $args, $depth );
				$output .= ob_get_clean();
				return;
			}

			if ( ( 'pingback' == $comment->comment_type || 'trackback' == $comment->comment_type ) && $args['short_ping'] ) {
				ob_start();
				$this->ping( $comment, $depth, $args );
				$output .= ob_get_clean();
			} elseif ( 'html5' === $args['format'] ) {
				ob_start();
				$this->html5_comment( $comment, $depth, $args );
				$output .= ob_get_clean();
			} else {
				ob_start();
				$this->comment( $comment, $depth, $args );
				$output .= ob_get_clean();
			}
		}

		/**
		 * Ends the element output, if needed.
		 *
		 * @package nrghost
		 * @since 1.0.0
		 */
		public function end_el( &$output, $comment, $depth = 0, $args = array() ) {
			if ( !empty( $args['end-callback'] ) ) {
				ob_start();
				call_user_func( $args['end-callback'], $comment, $args, $depth );
				$output .= ob_get_clean();
				return;
			}
			if ( 'div' == $args['style'] )
				$output .= "</div><!-- #comment-## -->\n";
			else
				$output .= "</li><!-- #comment-## -->\n";
		}


		/**
		 * Output a comment in the HTML5 format.
		 *
		 * @access protected
		 * @since 3.6.0
		 *
		 * @see wp_list_comments()
		 *
		 * @param object $comment Comment to display.
		 * @param int    $depth   Depth of comment.
		 * @param array  $args    An array of arguments.
		 */
		protected function html5_comment( $comment, $depth, $args ) {
			$tag = ( 'div' === $args['style'] ) ? 'div' : 'li';
	?>
			<<?php echo esc_attr( $tag ); ?> id="comment-<?php comment_ID(); ?>" <?php comment_class( $this->has_children ? 'parent' : '' ); ?>>
				<div id="div-comment-<?php comment_ID(); ?>" class="comment-wrapper">
					<div class="comment-entry wow fadeInUp">
						<?php if ( 0 != $args['avatar_size'] ) print get_avatar( $comment, $args['avatar_size'] ); ?>
						<div class="title"><span class="name"><?php printf( '<b class="fn">%s</b>', get_comment_author_link() ); ?></span>, <?php printf( _x( '%1$s, %2$s', '1: date, 2: time', 'nrghost' ), get_comment_time( "H:i" ), get_comment_date( "M. d/Y" ) ); ?><?php edit_comment_link( esc_html__( 'Edit', 'nrghost' ), '<span class="edit-link">', '</span>' ); ?></div>
						<?php if ( '0' == $comment->comment_approved ) : ?>
						<p class="comment-awaiting-moderation alert alert-warning"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><?php esc_html_e( 'Your comment is awaiting moderation.', 'nrghost' ); ?></p>
						<?php endif; ?>
						<div class="description"><?php comment_text(); ?></div>
						<?php
						comment_reply_link( array_merge( $args, array(
							'add_below'		=> 'div-comment',
							'depth'			=> $depth,
							'max_depth'		=> $args['max_depth'],
							'before'		=> '',
							'after'			=> '',
						) ) );
						?>
					</div>
				</div>
	<?php
		}
	}
}