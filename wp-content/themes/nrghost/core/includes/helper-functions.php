<?php
/**
 * Including theme helper functions
 *
 * @package nrghost
 * @since 1.0.0
 *
 */



/**
 * Check if preloader is active, then on success print preloader html
 *
 * @return null
 *
 * @package nrghost
 * @since 1.0.0
 */
if ( !function_exists( 'nrghost_theme_preloader' ) ) {
	function nrghost_theme_preloader() {
		global $nrghost_opt;
		if ( is_object( $nrghost_opt ) && $nrghost_opt->is_loader_enabled() ) {
			echo '<!-- LOADER --><div id="loader-wrapper"><div class="loader-content"><div class="circle1"></div><div class="circle2"></div><div class="title">Loading</div></div></div><!-- LOADER END -->';
		}
	}
}



/**
 * Check string if it exploadable
 *
 * @param  [string]  $page_name string which need to be checked
 *
 * @return [boolean] true if exploadable, false - if not exploadable
 *
 * @package nrghost
 * @since 1.0.0
 */
if ( !function_exists( 'nrghost_is_explodable' ) ) {
	function nrghost_is_explodable( $page_name ) {
		return ( strpos( $page_name, ',' ) === false ) ? false : true;
	}
}



/**
 * Check is js_composer activated
 *
 * @return [bool] true in success, false in failure
 *
 * @package nrghost
 * @since 1.0.0
 */
if ( !function_exists( 'nrghost_is_vc_activated' ) ) {
	function nrghost_is_vc_activated() {
		if ( class_exists( 'Vc_Manager' ) && defined( 'WPB_VC_VERSION' ) && version_compare( WPB_VC_VERSION, '4.2.3', '>=' ) ) {
			return true;
		} else {
			return false;
		}
	}
}



/**
 * Check if shortcode is exists in this context
 *
 * @param  [string]		$shortcode		shortcode name
 *
 * @return [boolean]	true if exists, false if not exists
 *
 * @package nrghost
 * @since 1.0.0
 */
if ( !function_exists( 'nrghost_shortcode_exists' ) ) {
	function nrghost_shortcode_exists( $shortcode = false ) {
		global $shortcode_tags;

		if ( !$shortcode ) {
			return false;
		}
		if ( array_key_exists( $shortcode, $shortcode_tags ) ) {
			return true;
		}
		return false;
	}
}



/**
 * Get first "url" from post content or string
 *
 * @return [stirng] url if success, [bool] false in failure
 *
 * @package nrghost
 * @since 1.0.0
 */
if ( !function_exists( 'nrghost_get_first_url_from_string' ) ) {
	function nrghost_get_first_url_from_string( $string ) {
		$pattern  = "/^\b(?:(?:https?|ftp):\/\/)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i";
		preg_match( $pattern, $string, $link );
		return ( !empty( $link[0] ) ) ? $link[0] : false;
	}
}



/**
 * Get tag from post content or string
 *
 * @return [stirng] needed tag if success, [bool] false in failure
 *
 * @package nrghost
 * @since 1.0.0
 */
if ( !function_exists( 'nrghost_get_first_tag_from_string' ) ) {
	function nrghost_get_first_tag_from_string( $string, $tag = 'iframe' ) {
		switch ( $tag ) {
			case 'iframe':
				$pattern  = '/<' . $tag . '.*src=\"(.*)\".*><\/' . $tag . '>/isU';
				break;
			default:
				$pattern  = '/<' . $tag . '.*><\/' . $tag . '>/isU';
				break;
		}
		preg_match( $pattern, $string, $link );
		return ( !empty( $link[0] ) ) ? $link[0] : false;
	}
}



/**
 * Custom Regular Expression
 *
 * @return regexp
 *
 * @package nrghost
 * @since 1.0.0
 */
if ( !function_exists( 'nrghost_get_shortcode_regex' ) ) {
	function nrghost_get_shortcode_regex( $tagregexp = '' ) {
		// WARNING! Do not change this regex without changing do_shortcode_tag() and strip_shortcode_tag()
		// Also, see shortcode_unautop() and shortcode.js.
		return
		'\\['                                // Opening bracket
		. '(\\[?)'                           // 1: Optional second opening bracket for escaping shortcodes: [[tag]]
		. "($tagregexp)"                     // 2: Shortcode name
		. '(?![\\w-])'                       // Not followed by word character or hyphen
		. '('                                // 3: Unroll the loop: Inside the opening shortcode tag
		.     '[^\\]\\/]*'                   // Not a closing bracket or forward slash
		.     '(?:'
		.         '\\/(?!\\])'               // A forward slash not followed by a closing bracket
		.         '[^\\]\\/]*'               // Not a closing bracket or forward slash
		.     ')*?'
		. ')'
		. '(?:'
		.     '(\\/)'                        // 4: Self closing tag ...
		.     '\\]'                          // ... and closing bracket
		. '|'
		.     '\\]'                          // Closing bracket
		.     '(?:'
		.         '('                        // 5: Unroll the loop: Optionally, anything between the opening and closing shortcode tags
		.             '[^\\[]*+'             // Not an opening bracket
		.             '(?:'
		.                 '\\[(?!\\/\\2\\])' // An opening bracket not followed by the closing shortcode tag
		.                 '[^\\[]*+'         // Not an opening bracket
		.             ')*+'
		.         ')'
		.         '\\[\\/\\2\\]'             // Closing shortcode tag
		.     ')?'
		. ')'
		. '(\\]?)';                          // 6: Optional second closing brocket for escaping shortcodes: [[tag]]
	}
}



/**
 * Tag Regular Expression
 *
 * @package nrghost
 * @since 1.0.0
 */
if ( !function_exists( 'nrghost_tagregexp' ) ) {
	function nrghost_tagregexp() {
		return apply_filters( 'nrghost_custom_tagregexp', 'video|audio|playlist|video-playlist|embed|cs_media' );
	}
}



/**
 * Get related posts
 *
 * @param  [integer] $post_id   ID of post
 * @param  [integer] $posts_qty Number of related posts, that we need to get
 *
 * @return [array] Array with related posts in success, [bool] false in failure
 *
 * @package nrghost
 * @since 1.0.0
 */
if ( !function_exists( 'nrghost_get_related_posts' ) ) {
	function nrghost_get_related_posts( $post_id = NULL, $posts_qty = 5 ) {
		global $post;
		$post_id = ( $post_id != NULL )? $post_id : $post->ID;
		$related_posts_args = array(
			'category__in' => wp_get_post_categories($post_id),
			'numberposts' => $posts_qty,
			'post__not_in' => array( $post_id ),
		);

		$related = get_posts( $related_posts_args );
		return ( $related ) ? $related : false;
	}
}



/**
 * Get post gallery if it exists
 *
 * @param  [string/int]		$post_id		post ID with gallery
 *
 * @return [array/bool]		Array images url and alt in success, [bool] false in failure
 *
 * @package nrghost
 * @since 1.0.0
 */
if ( !function_exists( 'nrghost_get_post_gallery' ) ) {
	function nrghost_get_post_gallery( $post_id = null ) {
		if ( $post_id == null && isset( $post ) && is_object( $post ) ) { $post_id = $post->ID; }

		$gallery = get_post_gallery( $post_id, false );
		if ( isset( $gallery['ids'] ) ) {
			$ids = explode(',', $gallery['ids']);

			$images = array();
			foreach ( $ids as $id ) {
				$image = wp_get_attachment_image_src( $id, 'full' );
				$img['url'] = $image[0];
				$img['alt'] = trim( strip_tags( get_post_meta( $id, '_wp_attachment_image_alt', true ) ) );
				if ( empty( $img['alt'] ) ) { $img['alt'] = 'Image'; }

				if ( !empty( $img['url'] ) ) { $images[] = $img; }
			}
		}

		if ( !empty( $images ) ) {
			return $images;
		} else {
			return false;
		}
	}
}



/**
 * Cut string
 *
 * @param  [string]		$str		String, that must be cutted
 * @param  [int]		$size		Symbols
 *
 * @return [string]		cutted string
 *
 * @package nrghost
 * @since 1.0.0
 */
if ( !function_exists( 'nrghost_cut_string' ) ) {
	function nrghost_cut_string( $str, $size ) {
		if ( strlen( $str ) > $size ) {
			$new_str = substr( $str, 0, $size );
			$sized = $size - 30;
			$pos = strrpos( $new_str, " ", $sized );
			$done_str = substr( $new_str, 0, $pos );
			$done_str .= '...';
			return $done_str;
		} else {
			return $str;
		}
	}
}



/**
 * Get the nrghost theme comments form
 *
 * @return null
 *
 * @package nrghost
 * @since 1.0.0
 */
if ( !function_exists( 'nrghost_comment_form' ) ) {
	function nrghost_comment_form() {
		$req = $aria_req = true;
		if ( is_user_logged_in() ) {
			$user_id = get_current_user_id();

			if ( get_user_meta( $user_id, 'first_name' ) == '' && get_user_meta( $user_id, 'last_name', true ) == '' ) {
				$user_name = 'value="' . get_user_meta( $user_id, 'display_name', true ) . '"';
			} else {
				$user_name = 'value="' . get_user_meta( $user_id, 'first_name', true ) . " " . get_user_meta( $user_id, 'last_name', true ) . '"';
			}
			$user_email = 'value="' . get_user_meta( $user_id, 'user_email', true ) . '"';
		} else {
			$user_name = $user_email = '';
		}

		$nrghost_comment_args = array(
			'title_reply'			=> '',
			'fields'				=> apply_filters( 'comment_form_default_fields', array(
				'author'				=> '<div class="field-entry"><label for="field-1">' . __ ( 'Your Name *', 'nrghost' ) . '</label><input type="text" name="author" ' . $user_name . ' id="field-1" required="required" /></div>',
				'email'					=> '<div class="field-entry"><label for="field-2">' . __ ( 'Email *', 'nrghost' ) . '</label><input type="text" name="email" ' . $user_email . ' id="field-2" required="required" /></div>',
				'url'					=> '',
			) ),
			'comment_field'			=> '<div class="field-entry"><label for="field-3">' . __ ( 'Message', 'nrghost' ) . '</label><textarea id="field-3" name="comment" required="required"></textarea></div>',
			'comment_notes_before'	=> '',
			'comment_notes_after'	=> '',
			'label_submit'			=> esc_html__( 'Submit', 'nrghost' ),
			'class_submit'			=> 'button col-md-8 col-md-offset-2',
		);
		ob_start();
		comment_form( $nrghost_comment_args );
		print str_replace('class="comment-respond"','class="comment-respond form-block form-wrapper wow fadeInUp animated"',ob_get_clean());
		// comment_form( $nrghost_comment_args );
	}
}



/**
 * Get header nav menu
 *
 * @return [string/null]			if print == false return html code / else return null
 *
 * @package nrghost
 * @since 1.0.0
 */
if ( !function_exists( 'nrghost_header_nav' ) ) {
	function nrghost_header_nav( $print = true ) {

		$walker = new NrghostHeaderNavWalker();
		$header_menu_nav_args = array(
			'theme_location'	=> 'primary-menu',
			'container'			=> 'nav',
			'container_class'   => 'menu-main-menu-container',
			'echo'				=> $print,
			'fallback_cb'		=> 'wp_page_menu',
			'items_wrap'		=> '<ul id="%1$s" class="%2$s">%3$s</ul>',
			'menu_class'        => '',
			'after'				=> '<span class="submenu-icon"><span class="glyphicon glyphicon-chevron-down"></span></span>'
			//'walker'			=> $walker,
		);
		if ( has_nav_menu( 'primary-menu' ) ) {
			wp_nav_menu( $header_menu_nav_args );
		} else {
			print '<nav><span class="no-menu">Please register Header Menu from <a href="' . esc_url( admin_url('nav-menus.php') ) . '" target="_blank">Appearance &gt; Menus</a></span></nav>';
		}
	}
}



/**
 * Get footer nav menu
 *
 * @return [string/null]			if print == false return html code / else return null
 *
 * @package nrghost
 * @since 1.0.0
 */
if ( !function_exists( 'nrghost_footer_nav' ) ) {
	function nrghost_footer_nav( $print = true ) {

		$walker = new NrghostFooterNavWalker();
		$footer_menu_nav_args = array(
			'theme_location'	=> 'footer-menu',
			'container'			=> '',
			'echo'				=> $print,
			'fallback_cb'		=> 'wp_page_menu',
			'items_wrap'		=> '<ul class="footer-menu">%3$s</ul>',
			'walker'			=> $walker,
		);
		if ( has_nav_menu( 'footer-menu' ) ) {
			wp_nav_menu( $footer_menu_nav_args );
		} else {
			print '<nav><span class="no-menu">Please register Footer Menu from <a href="' . esc_url( admin_url('nav-menus.php') ) . '" target="_blank">Appearance &gt; Menus</a></span></nav>';
		}
	}
}



/**
 * Get site logo
 *
 * @return [null/string]			if print == true return null / else return html code
 *
 * @package nrghost
 * @since 1.0.0
 */
if ( !function_exists( 'nrghost_logo' ) ) {
	function nrghost_logo( $print = true ) {
		global $nrghost_opt;
		if ( is_object( $nrghost_opt ) && $nrghost_opt->get_logo() ) {
			$output = '<div id="logo-wrapper"><div class="cell-view"><a id="logo" href="' .  home_url() . '"><img src="' . esc_url( $nrghost_opt->get_logo() ) . '" alt="Logo" /></a></div></div>';
			if ( $print ) { print $output; } else { return $output; }
		}
	}
}



/**
 * Get site subheader logo
 *
 * @return [null/string]			if print == true return null / else return html code
 *
 * @package nrghost
 * @since 1.0.0
 */
if ( !function_exists( 'nrghost_sublogo' ) ) {
	function nrghost_sublogo( $print = true ) {
		global $nrghost_opt;
		if ( is_object( $nrghost_opt ) && $nrghost_opt->get_sublogo() ) {
			$output = '<a id="subheader-logo" href="' .  home_url() . '"><img src="' . esc_url( $nrghost_opt->get_logo() ) . '" alt="Sub-logo" /></a>';
			if ( $print ) { print $output; } else { return $output; }
		}
	}
}



/**
 * Get footer social icons
 *
 * @return [null/string]			if print == true return null / else return html code
 *
 * @package nrghost
 * @since 1.0.0
 */
if ( !function_exists( 'nrghost_footer_socials' ) ) {
	function nrghost_footer_socials( $print = true ) {
		global $nrghost_opt;
		if ( is_object( $nrghost_opt ) && $nrghost_opt->get_socials() ) {
			$socials = $nrghost_opt->get_socials();
			$output = '<div class="row nopadding social-icons-wrapper">';
			foreach ($socials as $social) {
				$output .= '<div class="col-xs-3 nopadding"><a class="social-icon" href="' . esc_url( $social['link'] ) . '" target="_blank" title="' . esc_attr( $social['title'] ) . '" style="background-color: ' . esc_attr( $social['color'] ) . ';"><i class="' . esc_attr( $social['icon'] ) . '"></i></a></div>';
			}
			$output .= '</div>';
			if ( $print ) { print $output; } else { return $output; }
		}
	}
}



/**
 * Get post categories
 *
 * @return [null/string]			if print == true return null / else return html code
 *
 * @package nrghost
 * @since 1.0.0
 */
if ( !function_exists( 'nrghost_post_categories' ) ) {
	function nrghost_post_categories( $separator = ' ', $post_id = null, $print = true ) {
		if ( $post_id === null ) {
			global $post;
			$post_id = $post->ID;
		}

		$output = '';

		$categories = get_the_category( $post_id );
		if ( $categories ) {
			$i = 0;
			foreach( $categories as $category ) {
				$output .= ( $i++ > 0 ) ? $separator : '';
				$output .= '<a href="'.get_category_link( $category->term_id ).'" title="' . esc_attr( sprintf( esc_html__( "View all posts in %s", 'nrghost' ), $category->name ) ) . '">' . esc_attr( $category->cat_name ) . '</a>';
			}
		}

		$output = trim( $output );
		if ( $print ) { print $output; } else { return $output; }
	}
}



/**
 * Get post views count
 *
 * @return [null/string]			if print == true return null / else return html code
 *
 * @package nrghost
 * @since 1.0.0
 */
if ( !function_exists( 'nrghost_post_views' ) ) {
	function nrghost_post_views( $post_id = null, $print = true ) {
		if ( $post_id === null ) {
			global $post;
			$post_id = $post->ID;
		}

		$count_key = 'post_views_count';
		$count = get_post_meta( $post_id, $count_key, true );
		if ( $count == '' ) {
			delete_post_meta( $post_id, $count_key );
			add_post_meta( $post_id, $count_key, '0' );
			$count = 0;
		}
		if ( $print ) { print $count; } else { return $count; }
	}
}



/**
 * Set post views
 *
 * @return [null/string]			if print == true return null / else return html code
 *
 * @package nrghost
 * @since 1.0.0
 */
if ( !function_exists( 'nrghost_set_post_views' ) ) {
	function nrghost_set_post_views( $post_id = null ) {
		if ( $post_id === null ) {
			global $post;
			$post_id = $post->ID;
		}

		$count_key = 'post_views_count';
		$count = get_post_meta( $post_id, $count_key, true );
		if ( $count == '' ) {
			$count = 0;
			delete_post_meta( $post_id, $count_key );
			add_post_meta( $post_id, $count_key, '0' );
		} else {
			$count++;
			update_post_meta( $post_id, $count_key, $count );
		}
	}
}



/**
 * Test if user already liked post
 * @param	[int]		$post_id Post ID
 *
 * @return	[bool]		true if already liked, false on the other way
 *
 * @package nrghost
 * @since 1.0.0
 */
if ( !function_exists( 'nrghost_post_already_liked' ) ) {
	function nrghost_post_already_liked( $post_id ) { // test if user liked before
		if ( is_user_logged_in() ) { // user is logged in
			$user_id = get_current_user_id(); // current user
			$meta_USERS = get_post_meta( $post_id, "_user_liked" ); // user ids from post meta
			$liked_USERS = ""; // set up array variable

			if ( count( $meta_USERS ) != 0 ) { // meta exists, set up values
				$liked_USERS = $meta_USERS[0];
			}

			if( !is_array( $liked_USERS ) ) // make array just in case
			$liked_USERS = array();

			if ( in_array( $user_id, $liked_USERS ) ) { // True if User ID in array
				return true;
			}
			return false;

		} else { // user is anonymous, use IP address for voting

			$meta_IPS = get_post_meta( $post_id, "_user_IP" ); // get previously voted IP address
			$ip = $_SERVER["REMOTE_ADDR"]; // Retrieve current user IP
			$liked_IPS = ""; // set up array variable

			if ( count( $meta_IPS ) != 0 ) { // meta exists, set up values
				$liked_IPS = $meta_IPS[0];
			}

			if ( !is_array( $liked_IPS ) ) // make array just in case
			$liked_IPS = array();

			if ( in_array( $ip, $liked_IPS ) ) { // True is IP in array
				return true;
			}
			return false;
		}

	}
}



/**
 * Front end like button
 *
 * @return [string]		html code of like button
 *
 * @package nrghost
 * @since 1.0.0
 */
if ( !function_exists( 'nrghost_post_like_link' ) ) {
	function nrghost_post_like_link( $post_id, $print = true ) {
		$like_count = get_post_meta( $post_id, "_post_like_count", true ); // get post likes
		$count = ( empty( $like_count ) || $like_count == "0" ) ? '0' : esc_attr( $like_count );
		if ( nrghost_post_already_liked( $post_id ) ) {
			$class = esc_attr( ' liked' );
			$title = esc_attr( 'Unlike it :(' );
		} else {
			$class = esc_attr( '' );
			$title = esc_attr( 'Like it :)' );
		}
		$output = '<div class="data-entry"><span class="icon-entry like nrghost-post-like' . $class . '" data-post_id="' . $post_id . '" title="' . $title . '"></span><br> <span class="counter">' . $count . '</span></div>';
		if ( $print ) { print $output; } else { return $output; }
	}
}



/**
 * If the user is logged in, output a list of posts that the user likes
 * Markup assumes sidebar/widget usage
 *
 * @return null
 *
 * @package nrghost
 * @since 1.0.0
 */
if ( !function_exists( 'nrghost_frontend_user_likes' ) ) {
	function nrghost_frontend_user_likes() {
		if ( is_user_logged_in() ) { // user is logged in
			$like_list = '';
			$user_id = get_current_user_id(); // current user
			$user_likes = get_user_option( "_liked_posts", $user_id );
			if ( !empty( $user_likes ) && count( $user_likes ) > 0 ) {
				$the_likes = $user_likes;
			} else {
				$the_likes = '';
			}
			if ( !is_array( $the_likes ) )
				$the_likes = array();
			$count = count( $the_likes );
			if ( $count > 0 ) {
				$limited_likes = array_slice( $the_likes, 0, 5 ); // this will limit the number of posts returned to 5
				$like_list .= "<aside>\n";
				$like_list .= "<h3>" . esc_html__( 'You Like:', 'nrghost' ) . "</h3>\n";
				$like_list .= "<ul>\n";
				foreach ( $limited_likes as $the_like ) {
					$like_list .= "<li><a href='" . esc_url( get_permalink( $the_like ) ) . "' title='" . esc_attr( get_the_title( $the_like ) ) . "'>" . get_the_title( $the_like ) . "</a></li>\n";
				}
				$like_list .= "</ul>\n";
				$like_list .= "</aside>\n";
			}
			print $like_list;
		}
	}
}



/**
 * Custom post thumbnail
 *
 * @return [string/bool]		if print == false return html img code / else return false
 *
 * @package nrghost
 * @since 1.0.0
 */
if ( !function_exists( 'nrghost_post_thumbnail' ) ) {
	function nrghost_post_thumbnail( $post_id = null, $class = '', $print = true ) {
		if ( $post_id === null ) {
			global $post;
			$post_id = ( is_object( $post ) && isset( $post->ID ) ) ? $post->ID : false;
		}

		if ( $post_id && has_post_thumbnail( $post_id ) ) {
			$image_id = get_post_thumbnail_id( $post_id );
			$image =  wp_get_attachment_image_src( $image_id, 'full' );
			$img = '';
			if ( !empty( $image ) ) {
				if ( ( $image[2] <= $image[1] ) ) {
					$img = get_the_post_thumbnail( $post_id, 'full', array( 'class' => $class ) );
				} else {
					$width = $image[1];
					$height = intval( $image[1] * 0.75 );
					$src = aq_resize( $image[0], $width, $height, true, true, true );
					$img = get_the_post_thumbnail( $post_id, 'full', array( 'src' => $src, 'class' => $class ) );
				}
			}
		} else {
			$img = false;
		}
		if ( $print ) { print $img; } else { return $img; }
	}
}



/**
 * Custom size post thumbnail
 *
 * @return [string/bool]		if print == false return html img code / else return false
 *
 * @package nrghost
 * @since 1.0.0
 */
if ( !function_exists( 'nrghost_custom_thumbnail' ) ) {
	function nrghost_custom_thumbnail( $post_id, $width, $height, $class = '', $print = true ) {
		$img = '';
		if ( $post_id === null ) {
			global $post;
			$post_id = ( is_object( $post ) && isset( $post->ID ) ) ? $post->ID : false;
		}

		if ( $post_id && has_post_thumbnail( $post_id ) ) {
			$image_id = get_post_thumbnail_id( $post_id );
			$image =  wp_get_attachment_image_src( $image_id, 'full' );
			if ( $image ) {
				$src = aq_resize( $image[0], $width, $height, true, true, true );
				$img = get_the_post_thumbnail( $post_id, 'full', array( 'src' => $src, 'class' => $class ) );
			}
		} else {
			$img = false;
		}
		if ( $print ) { print $img; } else { return $img; }
	}
}



/**
 * Get related posts
 *
 * @param  [integer] $post_id   ID of post
 * @param  [integer] $posts_qty Number of related posts, that we need to get
 *
 * @return [array] Array with related posts in success, [bool] false in failure
 *
 * @package nrghost
 * @since 1.0.0
 */
if ( !function_exists( 'nrghost_get_related_posts' ) ) {
	function nrghost_get_related_posts( $posts_qty = 4, $post_id = NULL ) {
		if ( $post_id === null ) {
			global $post;
			$post_id = ( is_object( $post ) && isset( $post->ID ) ) ? $post->ID : false;
		}

		$related_posts_args = array(
			'category__in' => wp_get_post_categories( $post_id ),
			'numberposts' => $posts_qty,
			'post__not_in' => array( $post_id ),
		);

		$related = get_posts( $related_posts_args );
		return ( $related ) ? $related : false;
	}
}



/**
 * Display navigation to next/previous comments when applicable.
 *
 * @return null
 *
 * @package nrghost
 * @since 1.0.0
 */
if ( !function_exists( 'nrghost_comment_nav' ) ) {
	function nrghost_comment_nav() {

		// Are there comments to navigate through?
		if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) { ?>
		<nav class="navigation comment-navigation" role="navigation">
			<h2 class="screen-reader-text"><?php esc_html_e( 'Comment navigation', 'nrghost' ); ?></h2>
			<div class="nav-links">
				<?php
				if ( $prev_link = get_previous_comments_link( esc_html__( 'Older Comments', 'nrghost' ) ) ) {
					printf( '<div class="nav-previous">%s</div>', $prev_link );
				}

				if ( $next_link = get_next_comments_link( esc_html__( 'Newer Comments', 'nrghost' ) ) ) {
					printf( '<div class="nav-next">%s</div>', $next_link );
				} ?>
			</div><!-- .nav-links -->
		</nav><!-- .comment-navigation -->
		<?php
		}
	}
}