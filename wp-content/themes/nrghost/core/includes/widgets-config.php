<?php
/**
 * Widgets for theme
 *
 * @package nrghost
 * @since 1.0.0
 */



/**
 * Register widgets for theme
 *
 * @return null
 *
 * @package nrghost
 * @since 1.0.0
 */
if( !function_exists('nrghost_register_widgets') ) {
	function nrghost_register_widgets() {
		register_widget( 'Nrghost_Popular_Posts_Widget' );
	}
	add_action( 'widgets_init', 'nrghost_register_widgets' );
}



class Nrghost_Popular_Posts_Widget extends WP_Widget {

	function __construct() {
		parent::__construct(
			'nrghost_popular_posts_widget',
			esc_html__( "Popular Posts", 'nrghost' ),
			array( 'description' => esc_html__( "Shows popular posts", 'nrghost' ), )
		);
	}


	public function widget( $args, $instance ) {
		$output = '';

		$post_id1 = $instance['post_id1'];
		$post_id2 = $instance['post_id2'];

		$posts = array();
		$posts[] = get_post( $post_id1 );
		$posts[] = get_post( $post_id2 );

		if ( $posts ) {
			$output .= '<div class="side-menu">';
			$output .= '	<div class="title">' . esc_html__( 'Popular Posts', 'nrghost' ) . '</div>';
			$output .= '		<div class="row nopadding">';
			foreach ( $posts as $single ) {
				$output .= '<div class="col-xs-12 col-md-12 nopadding">';
				$output .= '	<div class="side-menu-image-item">';
				$output .= '		<a class="image" href="' . get_permalink( $single->ID ) . '">';
				$output .= '			' . nrghost_custom_thumbnail( $single->ID, 210, 120, '', false );
				$output .= '		</a>';
				$output .= '		<a class="title" href="' . get_permalink( $single->ID ) . '">' . get_the_title( $single->ID ) . '</a>';
				$output .= '		<div class="author">';
				$output .= '			' . nrghost_post_categories( ', ', $single->ID, false ) . ' by <b>' . get_the_author_meta( 'display_name', $single->post_author ) . '</b>';
				$output .= '		</div>';
				$output .= '	</div>';
				$output .= '</div>';
			}
			$output .= '</div></div>';
		}

		if ( !empty( $single_post ) ) {
			$post_options = get_post_meta( $post_id1, 'nrghost_custom_post_side_options', false );
			$post_color 	= ( isset( $post_options[0]['post-color'] ) && !empty( $post_options[0]['post-color'] ) )		? $post_options[0]['post-color']	: 'green';
			$cats = get_the_category( $post_id1 );
			$post_cat = $cats[0]->cat_name;
			$post_cat_link = get_category_link( $cats[0]->term_id );
			$post_author = get_the_author_meta( 'display_name', $single_post->post_author );


			$image_id = get_post_thumbnail_id( $post_id1 );
			$image =  wp_get_attachment_image_src( $image_id, 'full' );
			if ( !empty( $image ) ) {
				$alt = ( trim( strip_tags( get_post_meta( $image_id, '_wp_attachment_image_alt', true ) ) ) ) ? ' alt="' . trim( strip_tags( get_post_meta( $image_id, '_wp_attachment_image_alt', true ) ) ) . '"' : '';
				$img = '<img src="' . esc_url( aq_resize( $image[0], 370, 240, true ) ) . '"' . esc_attr( $alt ) . '/>' . "\n";
			}

			$output .= '<div class="block-with-img">';
			$output .= '	' . $img;
			$output .= '	<span class="point-caption bg-' . esc_attr( $post_color ) . '"></span>';
			$output .= '	<div class="block-img">';
			$output .= '		<h5><a href="' . esc_url( get_permalink( $post_id1 ) ) . '">' . esc_textarea( $single_post->post_title ) . '</a></h5>';
			$output .= '		<div class="visible-block">';
			$output .= '			<h6>By ' . esc_attr( $post_author ) . ' in <a href="' . esc_url( $post_cat_link ) . '">' . esc_attr( $post_cat ) . '</a></h6>';
			$output .= '			<div class="like-wrap">';
			$output .= '				' . nrghost_get_post_like_link( $post_id1 ) ;
			$output .= '				<span><i class="fa fa-comment col-green"></i></span><span class="counter comments-count">' . esc_attr( $single_post->comment_count ) . '</span>';
			// $output .= '				<i class="fa fa-share-alt col-yellow"></i>';
			$output .= '			</div>';
			$output .= '		</div>';
			$output .= '	</div>';
			$output .= '</div>';
		}

		print $output;
	}


	public function form( $instance ) {
		$post_id1  = isset( $instance['post_id1'] ) ? esc_attr( $instance['post_id1'] ) : '';
		$post_id2  = isset( $instance['post_id2'] ) ? esc_attr( $instance['post_id2'] ) : '';

		$posts_args = array(
			'posts_per_page' => -1,
			'post_status'	=> 'published',
		);
		$posts = get_posts( $posts_args );

		if ( !empty($posts) ) {
			foreach ( $posts as $single ) {
				if ( has_post_thumbnail( $single->ID ) ) {
					$options[$single->ID] = $single->post_title . ' (id=' . $single->ID . ')';
				}
			}
		}
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'post_id1' ) ); ?>"><?php esc_html_e( 'Select post 1:', 'nrghost' ); ?></label>
			<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'post_id1' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'post_id1' ) ); ?>" type="text" value="<?php echo esc_attr( $post_id1 ); ?>">
				<?php if ( !empty( $options ) ) {
					foreach ( $options as $key => $title ) {
						echo '<option' . ( $key == $post_id1 ? ' selected' : '' ) . ' value="' . esc_attr( $key ) . '">' . esc_attr( $title ) . '</option>';
					}
				} else { ?>
					<option value="false" disabled="disabled">No posts found</option>
				<?php } ?>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'post_id2' ) ); ?>"><?php esc_html_e( 'Select post 2:', 'nrghost' ); ?></label>
			<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'post_id2' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'post_id2' ) ); ?>" type="text" value="<?php echo esc_attr( $post_id2 ); ?>">
				<?php if ( !empty( $options ) ) {
					foreach ( $options as $key => $title ) {
						echo '<option' . ( $key == $post_id2 ? ' selected' : '' ) . ' value="' . esc_attr( $key ) . '">' . esc_attr( $title ) . '</option>';
					}
				} else { ?>
					<option value="false" disabled="disabled">No posts found</option>
				<?php } ?>
			</select>
		</p>
		<?php
	}


	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['post_id1'] = ( ! empty( $new_instance['post_id1'] ) ) ? strip_tags( $new_instance['post_id1'] ) : 1;
		$instance['post_id2'] = ( ! empty( $new_instance['post_id2'] ) ) ? strip_tags( $new_instance['post_id2'] ) : 1;

		return $instance;
	}

}