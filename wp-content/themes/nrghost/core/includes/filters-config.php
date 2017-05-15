<?php
/**
 * Including theme filters functions
 *
 * @package nrghost
 * @since 1.0.0
 *
 */



/**
 * Change comment avatar css classes
 *
 * @return null
 *
 * @package nrghost
 * @since 1.0.0
 */
if ( !function_exists( 'nrghost_change_avatar_class' ) ) {
	function nrghost_change_avatar_class( $class ) {
		$class = str_replace( "class='avatar", "class='avatar comment-icon img-circle", $class ) ;
		return $class;
	}
	add_filter( 'get_avatar','nrghost_change_avatar_class' );
}





if ( !function_exists( 'nrghost_replace_reply_link_class' ) ) {
	function nrghost_replace_reply_link_class( $class ) {
		$class = str_replace( "class='comment-reply-link", "class='comment-reply-link comment-link", $class );
		return $class;
	}
	add_filter('comment_reply_link', 'nrghost_replace_reply_link_class');
}



/**
*	Login / register filters
**/

/**
 * Verify if username and password is correct
 *
 * @return null
 *
 * @package nrghost
 * @since 1.0.0
 */
if ( !function_exists( 'nrghost_verify_username_password' ) ) {
	function nrghost_verify_username_password( $user, $username, $password ) {
		$login_page_id = cs_get_option( 'login-page' );
		if ( $login_page_id ) {
			$login_page  = get_permalink( $login_page_id );
			if ( empty( $username ) || empty( $password ) ) {
				wp_redirect( $login_page . "?login=is-empty" );
				exit;
			}
		}
	}
	add_filter( 'authenticate', 'nrghost_verify_username_password', 1, 3);
}