<?php
/**
 * Including theme requried actions hooks
 *
 * @package nrghost
 * @since 1.0.0
 *
 */



/**
 * Loads all the js and css script to frontend
 *
 * @return null
 *
 * @package nrghost
 * @since 1.0.0
 */
if ( !function_exists( 'nrghost_enqueue_scripts' ) ) {
	function nrghost_enqueue_scripts() {

		// register styles
		wp_enqueue_style( 'nrghost-bootstrap',			T_URI . '/assets/css/bootstrap.min.css' );
		wp_enqueue_style( 'nrghost-swiper',				T_URI . '/assets/css/idangerous.swiper.css' );
		wp_enqueue_style( 'nrghost-fontawesome',		T_URI . '/assets/css/font-awesome.min.css' );
		wp_enqueue_style( 'nrghost-like-styles',		T_URI . '/assets/css/like-styles.css' );
		wp_enqueue_style( 'nrghost-style',				T_URI . '/assets/css/style.css' );
		wp_enqueue_style( 'nrghost-animate',			T_URI . '/assets/css/animate.css' );
		wp_enqueue_style( 'nrghost-theme-style',		T_URI . '/style.css' );

		
		if ( function_exists( 'is_plugin_active' ) ) {
			if ( is_plugin_active( 'wp-subscribe/wp-subscribe.php' ) ) {
				wp_register_style('nrghost-wp-subscribe', plugins_url( 'wp-subscribe/css/wp-subscribe.css' ) );
				wp_enqueue_style( 'nrghost-wp-subscribe' );
			}
		}

		//register scripts
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'nrghost-bootstrap',			T_URI . '/assets/js/bootstrap.min.js',								array(), false, true );
		wp_enqueue_script( 'nrghost-swiper',			T_URI . '/assets/js/idangerous.swiper.min.js',						array(), false, true );
		wp_enqueue_script( 'nrghost-scrollto',			T_URI . '/assets/js/jquery.scrollTo.min.js',						array(), false, true );
		wp_enqueue_script( 'nrghost-postlikes',			T_URI . '/assets/js/post-like.js',									array(), false, true );
		wp_enqueue_script( 'nrghost-gmaps',				'//maps.googleapis.com/maps/api/js?sensor=false&amp;language=en',	array(), false, true );
		wp_enqueue_script( 'nrghost-map',				T_URI . '/assets/js/map.js',										array(), false, true );
		wp_enqueue_script( 'nrghost-wow',				T_URI . '/assets/js/wow.min.js',									array(), false, true );
		wp_enqueue_script( 'nrghost-global',			T_URI . '/assets/js/global.js',										array(), false, true );

		// include comment-reply script
		if ( is_singular() ) wp_enqueue_script( "comment-reply" );

		// localize script for like system
		wp_localize_script( 'postlikes', 'ajax_var', array(
			'url' => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( 'ajax-nonce' )
			)
		);

		// localize script with marker address for Gmap
		wp_localize_script( 'map', 'image', get_template_directory_uri() . '/assets/img/marker.png');
	}
	add_action( 'wp_enqueue_scripts', 'nrghost_enqueue_scripts');
}



/**
 * Include required plugins
 *
 * @return null
 *
 * @package nrghost
 * @since 1.2.0
 */
if ( !function_exists( 'nrghost_include_required_plugins' ) ) {
	function nrghost_include_required_plugins() {

		$plugins = array(

			array(
				'name'					=> 'NRGHostPlugin', // The plugin name
				'slug'					=> 'nrghost-plugin', // The plugin slug (typically the folder name)
				'required'				=> true, // If false, the plugin is only 'recommended' instead of required
				'version'				=> '1.6.0', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
				'force_activation'		=> false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
				'force_deactivation'	=> false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
				'source'				=> 'http://demo.nrgthemes.com/projects/plugins/nrghost-plugin.zip', // The plugin source
				'is_callable'			=> '',
			),

			array(
				'name'					=> 'Visual Composer',
				'slug'					=> 'js_composer',
				'required'				=> true,
				'version'				=> '',
				'force_activation'		=> false,
				'force_deactivation'	=> false,
				'source'				=> 'http://demo.nrgthemes.com/projects/plugins/js_composer.zip',
			),

			array(
				'name'					=> 'Contact Form 7',
				'slug'					=> 'contact-form-7',
				'required'				=> true,
				'version'				=> '',
				'force_activation'		=> false,
				'force_deactivation'	=> false,
				'external_url'			=> '',
			),

			array(
				'name'					=> 'WooCommerce',
				'slug'					=> 'woocommerce',
				'required'				=> false,
				'version'				=> '',
				'force_activation'		=> false,
				'force_deactivation'	=> false,
				'external_url'			=> '',
			),

			array(
				'name'					=> 'WHMCS Bridge',
				'slug'					=> 'whmcs-bridge',
				'required'				=> false,
				'version'				=> '',
				'force_activation'		=> false,
				'force_deactivation'	=> false,
				'external_url'			=> '',
			),

			array(
				'name'					=> 'Domain Checker',
				'slug'					=> 'wp-domain-checker',
				'required'				=> false,
				'version'				=> '',
				'force_activation'		=> false,
				'force_deactivation'	=> false,
				'source'				=> 'http://demo.nrgthemes.com/projects/nrghostwp/plugins/wp-domain-checker.zip',
			),

			array(
				'name'					=> 'Subscribe Form',
				'slug'					=> 'wp-subscribe',
				'required'				=> false,
				'version'				=> '',
				'force_activation'		=> false,
				'force_deactivation'	=> false,
				'external_url'			=> '',
			),

		);


		/**
		 * Array of configuration settings. Amend each line as needed.
		 * If you want the default strings to be available under your own theme domain,
		 * leave the strings uncommented.
		 * Some of the strings are added into a sprintf, so see the comments at the
		 * end of each line for what each argument will be.
		 */
		$config = array(
		'domain'       		=> 'tgmpa',         			// Text domain - likely want to be the same as your theme.
		'default_path' 		=> '',                         	// Default absolute path to pre-packaged plugins
		'menu'         		=> 'install-required-plugins', 	// Menu slug
		'has_notices'      	=> true,                       	// Show admin notices or not
		'is_automatic'    	=> false,					   	// Automatically activate plugins after installation or not
		'message' 			=> '',							// Message to output right before the plugins table
		'strings'      		=> array(
			'page_title'                       			=> __( 'Install Required Plugins', 'tgmpa' ),
			'menu_title'                       			=> __( 'Install Plugins', 'tgmpa' ),
			'installing'                       			=> __( 'Installing Plugin: %s', 'tgmpa' ), // %1$s = plugin name
			'oops'                             			=> __( 'Something went wrong with the plugin API.', 'tgmpa' ),
			'notice_can_install_required'     			=> _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.', 'tgmpa' ), // %1$s = plugin name(s)
			'notice_can_install_recommended'			=> _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.', 'tgmpa' ), // %1$s = plugin name(s)
			'notice_cannot_install'  					=> _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.', 'tgmpa' ), // %1$s = plugin name(s)
			'notice_can_activate_required'    			=> _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.', 'tgmpa' ), // %1$s = plugin name(s)
			'notice_can_activate_recommended'			=> _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.', 'tgmpa' ), // %1$s = plugin name(s)
			'notice_cannot_activate' 					=> _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.', 'tgmpa' ), // %1$s = plugin name(s)
			'notice_ask_to_update' 						=> _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.', 'tgmpa' ), // %1$s = plugin name(s)
			'notice_cannot_update' 						=> _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.', 'tgmpa' ), // %1$s = plugin name(s)
			'install_link' 					  			=> _n_noop( 'Begin installing plugin', 'Begin installing plugins', 'tgmpa' ),
			'activate_link' 				  			=> _n_noop( 'Activate installed plugin', 'Activate installed plugins', 'tgmpa' ),
			'return'                           			=> __( 'Return to Required Plugins Installer', 'tgmpa' ),
			'plugin_activated'                 			=> __( 'Plugin activated successfully.', 'tgmpa' ),
			'complete' 									=> __( 'All plugins installed and activated successfully. %s', 'tgmpa' ), // %1$s = dashboard link
			'nag_type'									=> 'updated' // Determines admin notice type - can only be 'updated' or 'error'
		)
	);

		tgmpa( $plugins, $config );
	}
	add_action( 'tgmpa_register', 'nrghost_include_required_plugins' );
}



/**
 * Add required theme elements
 *
 * @return null
 *
 * @package nrghost
 * @since 1.0.0
 */
if ( !function_exists( 'nrghost_after_setup' ) ) {
	function nrghost_after_setup() {

		// register nav
		register_nav_menus( array( 'primary-menu' => esc_html__( 'Main Menu', 'nrghost' ) ) );
		register_nav_menus( array( 'footer-menu' => esc_html__( 'Footer Menu [Doesn\'t support nested items]', 'nrghost' ) ) );

		//add elements theme support
		add_theme_support( 'title-tag' );
		add_theme_support( 'custom-header' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'custom-background' );
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'post-formats',	array( /*'aside',*/ 'quote', 'audio', 'video', 'gallery' ) );
		add_theme_support( 'html5', array( 'comment-list', 'comment-form', 'search-form', 'gallery', 'caption' ) );
		if ( 0 ) posts_nav_link();
		if ( !isset( $content_width ) ) $content_width = 1280;
	}
	add_action('after_setup_theme', 'nrghost_after_setup');
}



/**
 * Add editor styles
 * @return null
 *
 * @package nrghost
 * @since 1.0.0
 */
if ( !function_exists( 'nrghost_add_editor_styles' ) ) {
	function nrghost_add_editor_styles () {
		add_editor_style( 'assets/css/style.css' );
	}
	add_action( 'admin_init', 'nrghost_add_editor_styles' );
}



/**
 * Print in header needed technical information
 *
 * @return null
 *
 * @package nrghost
 * @since 1.0.0
 */
if ( !function_exists( 'nrghost_header_tech_info' ) ) {
	function nrghost_header_tech_info() {
		$output = '<meta charset="' . sanitize_text_field( get_bloginfo('charset') ) . '" />' . "\n";

		$favicon = cs_get_option( 'site-favicon' );
		if ( !empty( $favicon ) ) {
			$output .= '<link rel="shortcut icon" href="' . esc_url( $favicon ) . '"/>' . "\n";
		}
		$output .= '<meta name="format-detection" content="telephone=no" />' . "\n";
		$output .= '<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">' . "\n";
		print $output;
	}
	add_action( 'wp_head', 'nrghost_header_tech_info', 1 );
}



/**
 * Register sidebars for theme
 *
 * @return null
 *
 * @package nrghost
 * @since 1.0.0
 */
if( !function_exists('nrghost_register_sidebars') ) {
	function nrghost_register_sidebars() {

		// register sidebar
		register_sidebar( array(
			'id'			=> 'sidebar',
			'name'			=> esc_html__( 'Sidebar', 'nrghost' ),
			'before_widget'	=> '<div id="%1$s" class="widget main-widget %2$s">',
			'after_widget'	=> '</div>',
			'before_title'	=> '<h4 class="widgettitle">',
			'after_title'	=> '</h4>',
			'description'	=> esc_html__( 'Drag the widgets for sidebar.', 'nrghost' ),
		));

		// register footer sidebar-1
		register_sidebar( array(
			'id'			=> 'footer-sidebar-1',
			'name'			=> esc_html__( 'Footer-1', 'nrghost' ),
			'before_widget'	=> '<div id="%1$s" class="widget footer-widget %2$s">',
			'after_widget'	=> '</div>',
			'before_title'	=> '<h2 class="widgettitle">',
			'after_title'	=> '</h2>',
			'description'	=> esc_html__( 'Drag the widgets for footer-1.', 'nrghost' ),
		));

		// register footer sidebar-2
		register_sidebar( array(
			'id'			=> 'footer-sidebar-2',
			'name'			=> esc_html__( 'Footer-2', 'nrghost' ),
			'before_widget'	=> '<div id="%1$s" class="widget footer-widget %2$s">',
			'after_widget'	=> '</div>',
			'before_title'	=> '<h2 class="widgettitle">',
			'after_title'	=> '</h2>',
			'description'	=> esc_html__( 'Drag the widgets for footer-2.', 'nrghost' ),
		));

		// register footer sidebar-3
		register_sidebar( array(
			'id'			=> 'footer-sidebar-3',
			'name'			=> esc_html__( 'Footer-3', 'nrghost' ),
			'before_widget'	=> '<div id="%1$s" class="widget footer-widget %2$s">',
			'after_widget'	=> '</div>',
			'before_title'	=> '<h2 class="widgettitle">',
			'after_title'	=> '</h2>',
			'description'	=> esc_html__( 'Drag the widgets for footer-3.', 'nrghost' ),
		));

		// register footer sidebar-4
		register_sidebar( array(
			'id'			=> 'footer-sidebar-4',
			'name'			=> esc_html__( 'Footer-4', 'nrghost' ),
			'before_widget'	=> '<div id="%1$s" class="widget footer-widget %2$s">',
			'after_widget'	=> '</div>',
			'before_title'	=> '<h2 class="widgettitle">',
			'after_title'	=> '</h2>',
			'description'	=> esc_html__( 'Drag the widgets for footer-4.', 'nrghost' ),
		));
	}
	add_action( 'widgets_init', 'nrghost_register_sidebars' );
}



/**
 * Add custom CSS code to header
 *
 * @return null
 *
 * @package nrghost
 * @since 1.0.0
 */
if ( !function_exists( 'nrghost_add_custom_css' ) ) {
	function nrghost_add_custom_css() {
		$custom_css = cs_get_option( 'custom-css' );
		if ( !empty( $custom_css ) ) {
			$output = '<style>' . $custom_css . '</style>';
			print $output;
		}
	}
	add_action( 'wp_head', 'nrghost_add_custom_css', 50 );
}

/**
 * If framework exists
 *
 * @return null
 *
 * @package nrghost
 * @since 1.0.0
 */

if (! function_exists('cs_get_option')) {
  function cs_get_option(){
   return false;
  }
}
if ( !class_exists( 'NrghostThemeOptions' ) ) {
	class NrghostThemeOptions {
		private static $instance;

		public static function initialize() {
			if ( empty( self::$instance ) ) {
				self::$instance = new NrghostThemeOptions();
			}
			global $nrghost_opt;
			$nrghost_opt = self::$instance;
		}

		function get_option(){return false;}
		function is_loader_enabled(){return false;}
		function get_logo(){return false;}
		function get_sublogo(){return false;}
		function get_socials(){return false;}
		function get_tracking_code(){return false;}
		function get_header_adv(){return false;}
		
	}
	add_action( 'wp', array( 'NrghostThemeOptions', 'initialize' ) );
}
/**
 * Add custom JS code to footer
 *
 * @return null
 *
 * @package nrghost
 * @since 1.0.0
 */
if ( !function_exists( 'nrghost_add_custom_js' ) ) {
	function nrghost_add_custom_js() {
		$custom_js = cs_get_option( 'custom-js' );
		if ( !empty( $custom_js ) ) {
			$output = '<script>' . $custom_js . '</script>';
			print $output;
		}
	}
	add_action( 'wp_footer', 'nrghost_add_custom_js', 50 );
}



/**
 * Add custom JS code to footer
 *
 * @return null
 *
 * @package nrghost
 * @since 1.0.0
 */
if ( !function_exists( 'nrghost_load_custom_wp_admin_style' ) ) {
	function nrghost_load_custom_wp_admin_style() {
		wp_register_style( 'custom_wp_admin_css', get_template_directory_uri() . '/assets/css/admin-style.css', false, '1.0.0' );
		wp_enqueue_style( 'custom_wp_admin_css' );
	}
	add_action( 'admin_enqueue_scripts', 'nrghost_load_custom_wp_admin_style' );
}



/**
 * Save like data
 *
 * @return null
 *
 * @package nrghost
 * @since 1.0.0
 */
if ( !function_exists( 'nrghost_post_like' ) ) {
	function nrghost_post_like() {
		$nonce = $_POST['nonce'];
		if ( ! wp_verify_nonce( $nonce, 'ajax-nonce' ) )
			die ( 'Nope!' );

		if ( isset( $_POST['nrghost_post_like'] ) ) {

			$post_id = $_POST['post_id']; // post id
			$post_like_count = get_post_meta( $post_id, "_post_like_count", true ); // post like count

			if ( function_exists ( 'wp_cache_post_change' ) ) { // invalidate WP Super Cache if exists
				$GLOBALS["super_cache_enabled"]=1;
				wp_cache_post_change( $post_id );
			}

			if ( is_user_logged_in() ) { // user is logged in
				$user_id = get_current_user_id(); // current user
				$meta_POSTS = get_user_option( "_liked_posts", $user_id  ); // post ids from user meta
				$meta_USERS = get_post_meta( $post_id, "_user_liked" ); // user ids from post meta
				$liked_POSTS = NULL; // setup array variable
				$liked_USERS = NULL; // setup array variable

				if ( count( $meta_POSTS ) != 0 ) { // meta exists, set up values
					$liked_POSTS = $meta_POSTS;
				}

				if ( !is_array( $liked_POSTS ) ) // make array just in case
				$liked_POSTS = array();

				if ( count( $meta_USERS ) != 0 ) { // meta exists, set up values
					$liked_USERS = $meta_USERS[0];
				}

				if ( !is_array( $liked_USERS ) ) // make array just in case
				$liked_USERS = array();

				$liked_POSTS['post-'.$post_id] = $post_id; // Add post id to user meta array
				$liked_USERS['user-'.$user_id] = $user_id; // add user id to post meta array
				$user_likes = count( $liked_POSTS ); // count user likes

				if ( !nrghost_post_already_liked( $post_id ) ) { // like the post
					update_post_meta( $post_id, "_user_liked", $liked_USERS ); // Add user ID to post meta
					update_post_meta( $post_id, "_post_like_count", ++$post_like_count ); // +1 count post meta
					update_user_option( $user_id, "_liked_posts", $liked_POSTS ); // Add post ID to user meta
					update_user_option( $user_id, "_user_like_count", $user_likes ); // +1 count user meta
					echo esc_attr( $post_like_count ); // update count on front end

				} else { // unlike the post
					$pid_key = array_search( $post_id, $liked_POSTS ); // find the key
					$uid_key = array_search( $user_id, $liked_USERS ); // find the key
					unset( $liked_POSTS[$pid_key] ); // remove from array
					unset( $liked_USERS[$uid_key] ); // remove from array
					$user_likes = count( $liked_POSTS ); // recount user likes
					update_post_meta( $post_id, "_user_liked", $liked_USERS ); // Remove user ID from post meta
					update_post_meta($post_id, "_post_like_count", --$post_like_count ); // -1 count post meta
					update_user_option( $user_id, "_liked_posts", $liked_POSTS ); // Remove post ID from user meta
					update_user_option( $user_id, "_user_like_count", $user_likes ); // -1 count user meta
					echo "already" . esc_attr( $post_like_count ); // update count on front end

				}

			} else { // user is not logged in (anonymous)
				$ip = $_SERVER['REMOTE_ADDR']; // user IP address
				$meta_IPS = get_post_meta( $post_id, "_user_IP" ); // stored IP addresses
				$liked_IPS = NULL; // set up array variable

				if ( count( $meta_IPS ) != 0 ) { // meta exists, set up values
					$liked_IPS = $meta_IPS[0];
				}

				if ( !is_array( $liked_IPS ) ) // make array just in case
				$liked_IPS = array();

				if ( !in_array( $ip, $liked_IPS ) ) // if IP not in array
					$liked_IPS['ip-'.$ip] = $ip; // add IP to array

				if ( !nrghost_post_already_liked( $post_id ) ) { // like the post
					update_post_meta( $post_id, "_user_IP", $liked_IPS ); // Add user IP to post meta
					update_post_meta( $post_id, "_post_like_count", ++$post_like_count ); // +1 count post meta
					echo esc_attr( $post_like_count ); // update count on front end

				} else { // unlike the post
					$ip_key = array_search( $ip, $liked_IPS ); // find the key
					unset( $liked_IPS[$ip_key] ); // remove from array
					update_post_meta( $post_id, "_user_IP", $liked_IPS ); // Remove user IP from post meta
					update_post_meta( $post_id, "_post_like_count", --$post_like_count ); // -1 count post meta
					echo "already" . esc_attr( $post_like_count ); // update count on front end

				}
			}
		}

		exit;
	}
	add_action( 'wp_ajax_nopriv_nrghost-post-like', 'nrghost_post_like' );
	add_action( 'wp_ajax_nrghost-post-like', 'nrghost_post_like' );
}



/**
 * Retrieve User Likes and Show on Profile
 * @param  [type] $user [description]
 *
 * @return null
 *
 * @package nrghost
 * @since 1.0.0
 */
if ( !function_exists( 'nrghost_show_user_likes' ) ) {
	function nrghost_show_user_likes( $user ) { ?>
	<table class="form-table">
		<tr>
			<th><label for="user_likes"><?php esc_html_e( 'You Like:', 'nrghost' ); ?></label></th>
			<td>
				<?php
				$user_likes = get_user_option( "_liked_posts", $user->ID );
				if ( !empty( $user_likes ) && count( $user_likes ) > 0 ) {
					$the_likes = $user_likes;
				} else {
					$the_likes = '';
				}
				if ( !is_array( $the_likes ) )
					$the_likes = array();
				$count = count( $the_likes );
				$i=0;
				if ( $count > 0 ) {
					$like_list = '';
					print "<p>\n";
					foreach ( $the_likes as $the_like ) {
						$i++;
						$like_list .= "<a href=\"" . esc_url( get_permalink( $the_like ) ) . "\" title=\"" . esc_attr( get_the_title( $the_like ) ) . "\">" . get_the_title( $the_like ) . "</a>\n";
						if ($count != $i) $like_list .= " &middot; ";
						else $like_list .= "</p>\n";
					}
					print $like_list;
				} else {
					print "<p>" . esc_html_e( 'You don\'t like anything yet.', 'nrghost' ) . "</p>\n";
				} ?>
			</td>
		</tr>
	</table>
	<?php }
	add_action( 'show_user_profile', 'nrghost_show_user_likes' );
	add_action( 'edit_user_profile', 'nrghost_show_user_likes' );
}


/**
 * Log In function for custom authorization page
 *
 * @return null
 *
 * @package nrghost
 * @since 1.0.0
 */
if ( !function_exists( 'nrghost_login_failed' ) ) {
	function nrghost_login_failed() {
		$login_page_id = cs_get_option( 'login-page' );
		if ( isset( $login_page_id ) && $login_page_id ) {
			$login_page  = get_permalink( $login_page_id );
			wp_redirect( $login_page . '?login=failed' );
			exit;
		}
	}
	add_action( 'wp_login_failed', 'nrghost_login_failed' );
}



/**
 * Register new user
 *
 * @return null
 *
 * @package nrghost
 * @since 1.0.0
 */
if ( !function_exists( 'nrghost_add_new_member' ) ) {
	function nrghost_add_new_member() {
		if (isset( $_POST["reg_log"] ) && wp_verify_nonce( $_POST['nrghost_register_nonce'], 'nrghost-register-nonce' ) ) {
			$user_login		= $_POST["reg_log"];
			$user_email		= $_POST["reg_email"];
			$user_pass		= $_POST["reg_pwd"];
			$pass_confirm 	= $_POST["reg_pwd_rpt"];

			if( username_exists( $user_login ) ) {
				// Username already registered
				wp_redirect( get_permalink( $post->ID ) . '?action=register&reg_error=username_exists' );
				exit;
			}
			if( !validate_username( $user_login ) ) {
				// invalid username
				wp_redirect( get_permalink( $post->ID ) . '?action=register&reg_error=username_invalid' );
				exit;
			}
			if( $user_login  ==  '' ) {
				// empty username
				wp_redirect( get_permalink( $post->ID ) . '?action=register&reg_error=username_empty' );
				exit;
			}
			if( !is_email( $user_email ) ) {
				//invalid email
				wp_redirect( get_permalink( $post->ID ) . '?action=register&reg_error=email_invalid' );
				exit;
			}
			if( email_exists( $user_email ) ) {
				//Email address already registered
				wp_redirect( get_permalink( $post->ID ) . '?action=register&reg_error=email_exists' );
				exit;
			}
			if( $user_pass  ==  '' ) {
				// passwords do not match
				wp_redirect( get_permalink( $post->ID ) . '?action=register&reg_error=password_empty' );
				exit;
			}
			if( $user_pass  !=  $pass_confirm ) {
				// passwords do not match
				wp_redirect( get_permalink( $post->ID ) . '?action=register&reg_error=password_mismatch' );
				exit;
			}

			// only create the user in if there are no errors
			$new_user_id = wp_insert_user( array(
				'user_login'		=> $user_login,
				'user_pass'	 		=> $user_pass,
				'user_email'		=> $user_email,
				'user_registered'	=> date('Y-m-d H:i:s'),
				'role'				=> 'subscriber',
				)
			);
			if ( $new_user_id ) {
				// send an email to the admin alerting them of the registration
				wp_new_user_notification( $new_user_id );

				// log the new user in
				wp_setcookie( $user_login, $user_pass, true );
				wp_set_current_user( $new_user_id, $user_login );
				do_action( 'wp_login', $user_login );

				// send the newly created user to the home page after logging them in
				wp_redirect( home_url() ); exit;
			}

		}
	}
	add_action('init', 'nrghost_add_new_member');
}



if ( !function_exists( 'nrghost_validate_reset' ) ) {
	function nrghost_validate_reset() {
		$login_page_id = cs_get_option( 'login-page' );
		if ( isset( $login_page_id ) && $login_page_id ) {
			if( isset( $_POST['user_login'] ) && !empty( $_POST['user_login'] ) ) {
				$email_address = $_POST['user_login'];
				if ( filter_var( $email_address, FILTER_VALIDATE_EMAIL ) ) {
					if( !email_exists( $email_address ) ) {
						wp_redirect( get_permalink( $login_page_id ) . '?lostpassword=true&lost_error=noemail' );
						exit;
					}
				} else {
					$username = $_POST['user_login'];
					if ( !username_exists( $username ) ) {
						wp_redirect( get_permalink( $login_page_id ) . '?lostpassword=true&lost_error=nouser' );
						exit;
					} else {
					}
				}
			}else{
				wp_redirect( get_permalink( $login_page_id ) . '?lostpassword=true&lost_error=emptyname' );
				exit;
			}
		}
	}
	add_action( 'lostpassword_post', 'nrghost_validate_reset', 99, 3 );
}



/**
 * Log Out function for custom authorization page
 *
 * @return null
 *
 * @package nrghost
 * @since 1.0.0
 */
if ( !function_exists( 'nrghost_logout_page' ) ) {
	function nrghost_logout_page() {
		wp_redirect( home_url() );
		exit;
	}
	add_action('wp_logout','nrghost_logout_page');
}