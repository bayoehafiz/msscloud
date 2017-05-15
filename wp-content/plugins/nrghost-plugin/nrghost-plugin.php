<?php
/*
Plugin Name: NRGHostPlugin
Version: 1.6.0
Description: Includes Portfolio Custom Post Types and Visual Composer Shortcodes
Text Domain: nrghost
*/

// Define Constants
defined('EF_ROOT')		or define('EF_ROOT', dirname(__FILE__));
defined('EF_VERSION')	or define('EF_VERSION', '1.0.0');



// ----------------------------------------------------------------------------------------------------
// NRGHost importer integration
// ----------------------------------------------------------------------------------------------------
require_once( 'importer/init.php' );

// ----------------------------------------------------------------------------------------------------
// NRGHost VC tables addon integration
// ----------------------------------------------------------------------------------------------------

if( !class_exists( 'Nrghost_Plugin' ) ) {

	$dir = dirname( __FILE__ );

	class Nrghost_Plugin {

		private $assets_js;

		public function __construct() {
			$this->assets_js	= plugins_url( '/composer/js', __FILE__ );
			add_action( 'admin_init', array( $this, 'nrghost_load_map' ) );
			add_action( 'admin_print_scripts-post.php', array( $this, 'vc_enqueue_scripts' ), 99 );
			add_action( 'admin_print_scripts-post-new.php', array( $this, 'vc_enqueue_scripts' ), 99 );
			add_action( 'wp', array( $this, 'nrghost_load_shortcodes') );
		}


		public function nrghost_load_map() {
			if ( class_exists( 'Vc_Manager' ) ) {
				require_once( EF_ROOT .'/'. 'composer/map.php');
				require_once( EF_ROOT .'/'. 'composer/init.php');
			}
		}

		public function nrghost_load_shortcodes() {

			foreach( glob( EF_ROOT . '/'. 'shortcodes/nrghost_*.php' ) as $shortcode ) {
				require_once(EF_ROOT .'/'. 'shortcodes/'. basename( $shortcode ) );
			}

			foreach( glob( EF_ROOT . '/'. 'shortcodes/vc_*.php' ) as $shortcode ) {
				require_once(EF_ROOT .'/' .'shortcodes/'. basename( $shortcode ) );
			}

		}

		public function vc_enqueue_scripts() {
			wp_enqueue_script( 'vc-script', $this->assets_js .'/vc-script.js' ,  array('jquery'), '1.0.0', true );
		}

	} // end of class

	require_once( $dir . '/nrghost-tables/nrghost-tables.php' );

	// ----------------------------------------------------------------------------------------------------
	// Framework integration
	// ----------------------------------------------------------------------------------------------------
	 require_once ( plugin_dir_path( __FILE__ ) . '/cs-framework/cs-framework.php');

	// ----------------------------------------------------------------------------------------------------
	// PageOptions Class include
	// ----------------------------------------------------------------------------------------------------
	 require_once	plugin_dir_path( __FILE__ ) . '/cs-framework/classes/themeoptions.class.php';

	
	new Nrghost_Plugin;

	function abort_vc_ajax(){ 
		$user_page = get_current_screen();
		if( $user_page->post_type == 'post' || $user_page->post_type == 'page' ) {
		?>
		<script type="text/javascript">
		jQuery.ajaxSetup({
			beforeSend : function(xhr, setting) {
				if( /wpb_single_image_src/.test(setting.data) || /heartbeat/.test(setting.data) ) {
					xhr.abort();
					return false;
				}
			}
		});
		</script>
		<?php 
		}
	}
	add_action('admin_print_footer_scripts', 'abort_vc_ajax');

} // end of class_exists
