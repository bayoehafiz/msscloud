<?php
if ( ! defined( 'CURLINFO_REDIRECT_URL' ) ) {
	define( 'CURLINFO_REDIRECT_URL', 1048607 );
}

if ( ! function_exists( 'curl_file_create' ) ) {
	function curl_file_create( $filename, $mimetype = '', $postname = '' ) {
		return "@$filename;filename=" . ( $postname ? $postname : basename( $filename ) ) . ( $mimetype ? ";type=$mimetype" : '' );
	}
}

class WHMPress_Client_Area {

	var $systpl = "";
	var $whmcs_url = null;
	var $css_uris = [];
	var $js_uris = [];
	var $api_url = 'http://plugins.creativeon.com/api/';
	var $plugin_slug = 'whmpress-client-area';
	var $Langs = [
		"ar"    => "Arabic",
		"az"    => "Azerbaijani",
		"ca"    => "Catalan",
		"hr"    => "Croatian",
		"cs_CZ" => "Czech",
		"da_DK" => "Danish",
		"de_DE" => "German",
		"en_US" => "English",
		"en_AU" => "English",
		"en_GB" => "English",
		"en"    => "English",
		"fa_IR" => "Farsi",
		"fr_FR" => "French",
		//"de_CH" => "German",
		"hu_HU" => "Hungarian",
		"it_IT" => "Italian",
		"nb_NO" => "Norwegian",
		"pt_BR" => "Portuguese-br",
		"pt_PT" => "Portuguese-pt",
		"ru_RU" => "Russian",
		"es_ES" => "Spanish",
		"sv_SE" => "Swedish",
		"tr_TR" => "Turkish",
		"nl_NL" => "Dutch",
		//Ukranian
	];

	function __construct() {
		/*global $wpdb;
        $url = get_option("overwrite_whmcs_url");
        if ($url == "") $url = get_option("whmcs_url");
        if ($url == "" && $this->is_whmpress_activated()) {
            $WHMPress = new WHMPress();
            if ($WHMPress->WHMpress_synced()) {
                $Q = "SELECT `value` FROM `" . whmp_get_configuration_table_name() . "` WHERE `setting`='SystemURL' OR `setting`='SystemSSLURL' ORDER BY `setting` DESC";
                $Us = $wpdb->get_results($Q, ARRAY_A);
                foreach ($Us as $U) {
                    if ($U["value"] <> "") {
                        $url = $U["value"];
                        break;
                    }
                }
            }
        }
        if (substr($url, -1) == "/") $url = substr($url, 0, -1);*/
		//$this->whmcs_url = $this->get_whmcs_url();

		add_action( 'init', [ $this, 'enqueue_script' ] );

		// Aajx request for debug info.
		if ( is_admin() ) {
			add_action( 'wp_ajax_whmp_debug', [ $this, "debug_info" ] );

			## Initializing WCA Login ajax.
			add_action( 'wp_ajax_wca_login', [ $this, "wca_login" ] );
			add_action( 'wp_ajax_nopriv_wca_login', [ $this, "wca_login" ] );
		}

	}

	function enqueue_script() {
		if ( get_option( "whmcs_enable_sync" ) == "1" ) {
			## if WHMCS sync is enabled.
			wp_enqueue_script( 'wca-ajax-script', WHMP_CA_URL . "assets/js/ajax.js", [ 'jquery' ], $this->get_my_version() );
		}
		wp_localize_script( 'wca-ajax-script', 'wca_ajax', [
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'we_value' => 1234,
		] );
	}

	/**
	 * This function will call on authenticate WHMCS/WP logins on ajax.
	 */
	function wca_login() {
		/*extract($_POST);
        if (!isset($_POST['username'])) {
            _e("Username is missing", "whmpress");
            wp_die();
        }
        if (!isset($_POST['password'])) {
            _e("Password is missing", "whmpress");
            wp_die();
        }*/
		$ajax_request = true;
		include_once( WHMP_CA_PATH . "/includes/ajax.php" );
		wp_die();
	}

	function debug_info() {
		global $wpdb;

		$whmcs_info = "";
		if ( $this->is_whmpress_activated() ) {
			$ssl_url  = $wpdb->get_var( "SELECT `value` FROM `" . whmp_get_configuration_table_name() . "` WHERE `setting`='SystemSSLURL' ORDER BY `setting` DESC" );
			$_url     = $wpdb->get_var( "SELECT `value` FROM `" . whmp_get_configuration_table_name() . "` WHERE `setting`='SystemURL' ORDER BY `setting` DESC" );
			$_version = $wpdb->get_var( "SELECT `value` FROM `" . whmp_get_configuration_table_name() . "` WHERE `setting`='Version' ORDER BY `setting` DESC" );

			$whmcs_info = "WHMCS Info\n============\n";
			$whmcs_info .= "Version: {$_version}\n";
			$whmcs_info .= "System URL: " . $_url . "\n";
			$whmcs_info .= "System SSL URL: " . $ssl_url . "\n";
		}

		?>
		<textarea onfocus="jQuery(this).select();" style="width: 100%;height: 600px;" readonly="readonly">WordPress
		Information
		==================
		Site URL: <?php echo site_url() . "\n"; ?>
		Home URL: <?php echo home_url() . "\n"; ?>
		WordPress Version: <?php bloginfo( 'version' );
		echo "\n"; ?>
		WordPress Multi Site: <?php if ( is_multisite() ) {
			echo __( 'Yes', 'whmpress' );
		} else {
			echo __( 'No', 'whmpress' );
		}
		echo "\n"; ?>
		WordPress Language: <?php echo $this->get_current_language() . "\n"; ?>
		WordPress Debug
		Mode: <?php if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			_e( 'Yes', 'whmpress' );
		} else {
			_e( 'No', 'whmpress' );
		} ?>

		WordPress Max Upload Size: <?php
		$wp_upload_max     = wp_max_upload_size();
		$server_upload_max = intval( str_replace( 'M', '', ini_get( 'upload_max_filesize' ) ) ) * 1024 * 1024;

		if ( $wp_upload_max <= $server_upload_max ) {
			echo size_format( $wp_upload_max );
		} else {
			echo '<span class="whmp_danger">' . sprintf( __( '%s (The server only allows %s)', 'whmpress' ), size_format( $wp_upload_max ), size_format( $server_upload_max ) ) . '</span>';
		}
		echo "\n";
		?>
		WordPress Memory Limit: <?php echo WP_MEMORY_LIMIT . "\n"; ?>
		<?php if ( $this->is_permalink() ): ?>
			Pretty Permalinks: Enabled<?php echo "\n" ?>
			Pretty Permalink Structure: <?php echo get_option( 'permalink_structure' ) . "\n"; ?>
		<?php else: ?>
			Pretty Permalinks: Not Enabled<?php echo "\n" ?>
		<?php endif; ?>

		<?php echo $whmcs_info; ?>

		Plugins
		============
		WordPress Active Plugins: <?php echo count( (array) get_option( 'active_plugins' ) ); ?>

		Installed Plugins List:
		<?php
		$active_plugins = (array) get_option( 'active_plugins', array() );

		if ( is_multisite() ) {
			$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
		}

		$wp_plugins = [];

		foreach ( $active_plugins as $plugin ) {
			$plugin_data    = @get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin );
			$dirname        = dirname( $plugin );
			$version_string = '';

			if ( ! empty( $plugin_data['Name'] ) ) {

				// link the plugin name to the plugin url if available
				$plugin_name = $plugin_data['Name'];
				if ( ! empty( $plugin_data['PluginURI'] ) ) {
					$plugin_name = "\t" . $plugin_name;
				}
				echo $plugin_name . ' by ' . strip_tags( $plugin_data['Author'] ) . ' version ' . $plugin_data['Version'] . $version_string;
				echo "\n";
			}
		}

		if ( sizeof( $wp_plugins ) == 0 ) {
			echo '-';
		} else {
			echo implode( ', \n\t', $wp_plugins );
		}
		?>

		Theme
		=============
		Theme Name: <?php
		$active_theme = wp_get_theme();
		echo $active_theme->Name;
		?>

		Theme Version: <?php
		echo $active_theme->Version; ?>

		Theme Author URL: <?php echo $active_theme->{'Author URI'}; ?>

		Is Child Theme: <?php echo is_child_theme() ? 'Yes' : 'No'; ?>

		<?php if ( is_child_theme() ) :
			$parent_theme = wp_get_theme( $active_theme->Template );
			?>Parent Theme Name: <?php echo $parent_theme->Name; ?>
			Parent Theme Version: <?php echo $parent_theme->Version; ?>
			Parent Theme Author URL: <?php
			echo $parent_theme->{'Author URI'};
			?>
		<?php endif; ?>

		Server Information
		==================
		PHP Version :<?php if ( function_exists( 'phpversion' ) ) {
			echo esc_html( phpversion() );
		} ?>

		PHP Safe Mode: <?php
		if ( ini_get( 'safe_mode' ) ) {
			echo "ON";
		} else {
			echo "OFF";
		} ?>

		PHP Time Execution: <?php echo ini_get( 'max_execution_time' ); ?> Seconds

		PHP Temporary Directory: <?php echo sys_get_temp_dir() ?>

		MySQL Version: <?php echo $wpdb->get_var( "SELECT VERSION()" ); ?>

		Server Software: <?php echo esc_html( @$_SERVER['SERVER_SOFTWARE'] ); ?>

		MySQLi Extension: <?php echo function_exists( 'mysqli_connect' ) ? "Installed" : "Not Installed"; ?>

		cURL Extension Info
		===================
		cURL Extension: <?php echo function_exists( 'curl_version' ) ? "Installed" : "Not Installed"; ?>

		cURL Test with google.com: <?php if ( ! function_exists( 'curl_version' ) ) {
			echo "cURL not Installed";
		} else {
			$ch = curl_init();
			curl_setopt( $ch, CURLOPT_URL, "http://www.google.com" );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			$result = curl_exec( $ch );
			if ( $result === false ) {
				echo "Failed: ";
				if ( $errno = curl_errno( $ch ) ) {
					$error_message = curl_error( $ch );
					echo "cURL error:\n {$error_message}";
				}
			} else {
				echo "Passed";
			}
		}
		?>

		<?php if ( $this->get_whmcs_url() <> "" ): ?>
			cURL Test with WHMCS <?php echo $this->get_whmcs_url() ?>: <?php if ( ! function_exists( 'curl_version' ) ) {
				echo "cURL not Installed";
			} else {
				$ch = curl_init();
				curl_setopt( $ch, CURLOPT_URL, $this->get_whmcs_url() );
				curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
				curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
				curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
				$result = curl_exec( $ch );
				if ( $result === false ) {
					echo "Failed: ";
					if ( $errno = curl_errno( $ch ) ) {
						$error_message = curl_error( $ch );
						echo "cURL error:\n {$error_message}";
					}
				} else {
					echo "Passed";
				}
			}
			?>
		<?php endif; ?>

		cURL Test with port 443 and google.com: <?php if ( ! function_exists( 'curl_version' ) ) {
			echo "cURL not Installed";
		} else {
			$ch = curl_init();
			curl_setopt( $ch, CURLOPT_URL, "https://www.google.com" );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
			curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
			$result = curl_exec( $ch );
			if ( $result === false ) {
				echo "Failed: ";
				if ( $errno = curl_errno( $ch ) ) {
					$error_message = curl_error( $ch );
					echo "cURL error:\n {$error_message}";
				}
			} else {
				echo "Passed";
			}
		}

		/*$url = $this->whmp_http();
        $base = basename($url);
        if (strpos($base, "index?") !== false)
            $url = str_replace("index?", "index.php?", $url);
        $url .= "&whmp_login_check=";
        $is_helper = $this->read_remote_url($url);
        if ($is_helper == "1" || $is_helper == "0") $is_helper = true;
        else $is_helper = false;*/

		/*WHMPress Helper
        ==================
Is WHMPress Helper installed and active: <?php echo $is_helper ? "Yes" : "No"; */
		?>
		</textarea><?php wp_die(); ?>
		<table class="fancy" style="width: 100%;">
			<tr>
				<th colspan="2">WordPress Information</th>
			</tr>
			<tr>
				<td>Site URL</td>
				<td><?php echo site_url(); ?></td>
			</tr>
			<tr>
				<td>Home URL</td>
				<td><?php echo home_url(); ?></td>
			</tr>
			<tr>
				<td>WordPress Version</td>
				<td><?php bloginfo( 'version' ); ?></td>
			</tr>
			<tr>
				<td>WordPress Multi Site</td>
				<td><?php if ( is_multisite() ) {
						echo __( 'Yes', 'whmpress' );
					} else {
						echo __( 'No', 'whmpress' );
					} ?></td>
			</tr>
			<tr>
				<td>WordPress Language</td>
				<td><?php echo get_locale(); ?></td>
			</tr>
			<tr>
				<td>WordPress Debug Mode</td>
				<td><?php if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
						_e( 'Yes', 'whmpress' );
					} else {
						_e( 'No', 'whmpress' );
					} ?></td>
			</tr>
			<tr>
				<td>WordPress Max Upload Size</td>
				<td><?php
					$wp_upload_max     = wp_max_upload_size();
					$server_upload_max = intval( str_replace( 'M', '', ini_get( 'upload_max_filesize' ) ) ) * 1024 * 1024;

					if ( $wp_upload_max <= $server_upload_max ) {
						echo size_format( $wp_upload_max );
					} else {
						echo '<span class="whmp_danger">' . sprintf( __( '%s (The server only allows %s)', 'whmpress' ), size_format( $wp_upload_max ), size_format( $server_upload_max ) ) . '</span>';
					}
					?></td>
			</tr>
			<tr>
				<td>WordPress Memory Limit</td>
				<td><?php echo WP_MEMORY_LIMIT; ?></td>
			</tr>
			<?php if ( $this->is_permalink() ): ?>
				<tr>
					<td>Pretty Permalinks</td>
					<td>Enabled</td>
				</tr>
				<tr>
					<td>Pretty Permalink Structure</td>
					<td><?php echo get_option( 'permalink_structure' ) ?></td>
				</tr>
			<?php else: ?>
				<tr>
					<td>Pretty Permalinks</td>
					<td>Not Enabled</td>
				</tr>
			<?php endif; ?>
			<tr>
				<th colspan="2">WHMCS Info</th>
			</tr>
			<tr>
				<td>Version</td>
				<td><?php echo $_version ?></td>
			</tr>
			<tr>
				<td>System URL</td>
				<td><?php echo $_url; ?></td>
			</tr>
			<tr>
				<td>System SSL URL</td>
				<td><?php $ssl_url ?></td>
			</tr>
			<tr>
				<th colspan="2">Plugins</th>
			</tr>
			<tr>
				<td>WordPress Active Plugins</td>
				<td><?php echo count( (array) get_option( 'active_plugins' ) ); ?></td>
			</tr>
			<tr>
				<td>Installed Plugins List</td>
				<td>
					<?php
					$active_plugins = (array) get_option( 'active_plugins', array() );

					if ( is_multisite() ) {
						$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', [] ) );
					}

					$wp_plugins = [];

					foreach ( $active_plugins as $plugin ) {

						$plugin_data    = @get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin );
						$dirname        = dirname( $plugin );
						$version_string = '';

						if ( ! empty( $plugin_data['Name'] ) ) {

							// link the plugin name to the plugin url if available
							$plugin_name = $plugin_data['Name'];
							if ( ! empty( $plugin_data['PluginURI'] ) ) {
								$plugin_name = '<a target="_blank" href="' . esc_url( $plugin_data['PluginURI'] ) . '" title="Visit plugin homepage">' . $plugin_name . '</a>';
							}

							$wp_plugins[] = $plugin_name . ' by ' . $plugin_data['Author'] . ' version ' . $plugin_data['Version'] . $version_string;

						}
					}

					if ( sizeof( $wp_plugins ) == 0 ) {
						echo '-';
					} else {
						echo implode( ', <br/>', $wp_plugins );
					}
					?>
				</td>
			</tr>
			<tr>
				<th colspan="2">Theme</th>
			</tr>
			<tr>
				<td>Theme Name</td>
				<td><?php
					$active_theme = wp_get_theme();
					echo $active_theme->Name;
					?></td>
			</tr>
			<tr>
				<td>Theme Version</td>
				<td><?php
					echo $active_theme->Version;
					?></td>
			</tr>
			<tr>
				<td>Theme Author URL</td>
				<td><a target="_blank"
				       href="<?php echo $active_theme->{'Author URI'}; ?>"><?php echo $active_theme->{'Author URI'}; ?></a>
				</td>
			</tr>
			<tr>
				<td>Is Child Theme</td>
				<td><?php echo is_child_theme() ? 'Yes' : 'No'; ?></td>
			</tr>
			<?php
			if ( is_child_theme() ) :
				$parent_theme = wp_get_theme( $active_theme->Template );
				?>
				<tr>
					<td>Parent Theme Name</td>
					<td><?php echo $parent_theme->Name; ?></td>
				</tr>
				<tr>
					<td>Parent Theme Version</td>
					<td><?php echo $parent_theme->Version; ?></td>
				</tr>
				<tr>
					<td>Parent Theme Author URL</td>
					<td><?php
						echo $parent_theme->{'Author URI'};
						?></td>
				</tr>
			<?php endif; ?>
			<tr>
				<th colspan="2">Server Information</th>
			</tr>
			<tr>
				<td>PHP Version</td>
				<td><?php if ( function_exists( 'phpversion' ) ) {
						echo esc_html( phpversion() );
					} ?></td>
			</tr>
			<tr>
				<td>PHP Safe Mode</td>
				<td><?php
					if ( ini_get( 'safe_mode' ) ) {
						echo "ON";
					} else {
						echo "OFF";
					} ?>
				</td>
			</tr>
			<tr>
				<td>PHP Time Execution</td>
				<td><?php echo ini_get( 'max_execution_time' ); ?> Seconds</td>
			</tr>
			<tr>
				<td>PHP Temporary Directory</td>
				<td><?php echo sys_get_temp_dir() ?></td>
			</tr>
			<tr>
				<td>MySQL Version</td>
				<td><?php echo $wpdb->get_var( "SELECT VERSION()" ); ?></td>
			</tr>
			<tr>
				<td>Server Software</td>
				<td><?php echo esc_html( @$_SERVER['SERVER_SOFTWARE'] ); ?></td>
			</tr>
			<tr>
				<td>MySQLi Extension</td>
				<td><?php echo function_exists( 'mysqli_connect' ) ? "Installed" : "Not Installed"; ?></td>
			</tr>
			<tr>
				<th colspan="2">cURL Extension Info</th>
			</tr>
			<tr>
				<td>cURL Extension</td>
				<td><?php echo function_exists( 'curl_version' ) ? "Installed" : "Not Installed"; ?></td>
			</tr>
			<tr>
				<td>cURL Test with <b>google.com</b></td>
				<td>
					<?php if ( ! function_exists( 'curl_version' ) ) {
						echo "cURL not Installed";
					} else {
						$ch = curl_init();
						curl_setopt( $ch, CURLOPT_URL, "http://www.google.com" );
						curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
						$result = curl_exec( $ch );
						if ( $result === false ) {
							echo "Failed: ";
							if ( $errno = curl_errno( $ch ) ) {
								$error_message = curl_error( $ch );
								echo "cURL error:\n {$error_message}";
							}
						} else {
							echo "Passed";
						}
					}
					?></td>
			</tr>
			<?php if ( $this->get_whmcs_url() <> "" ): ?>
				<tr>
					<td>cURL Test with WHMCS <b><?php echo $this->get_whmcs_url() ?></b></td>
					<td>
						<?php if ( ! function_exists( 'curl_version' ) ) {
							echo "cURL not Installed";
						} else {
							$ch = curl_init();
							curl_setopt( $ch, CURLOPT_URL, $this->get_whmcs_url() );
							curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
							curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
							curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
							$result = curl_exec( $ch );
							if ( $result === false ) {
								echo "Failed: ";
								if ( $errno = curl_errno( $ch ) ) {
									$error_message = curl_error( $ch );
									echo "cURL error:\n {$error_message}";
								}
							} else {
								echo "Passed";
							}
						}
						?></td>
				</tr>
			<?php endif; ?>
			<tr>
				<td>cURL Test with port 443 and <b>google.com</b></td>
				<td>
					<?php if ( ! function_exists( 'curl_version' ) ) {
						echo "cURL not Installed";
					} else {
						$ch = curl_init();
						curl_setopt( $ch, CURLOPT_URL, "https://www.google.com" );
						curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
						curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
						curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
						$result = curl_exec( $ch );
						if ( $result === false ) {
							echo "Failed: ";
							if ( $errno = curl_errno( $ch ) ) {
								$error_message = curl_error( $ch );
								echo "cURL error:\n {$error_message}";
							}
						} else {
							echo "Passed";
						}
					}
					?></td>
			</tr>
			<tr>
				<th colspan="2">WHMPress Helper</th>
			</tr>
			<tr>
				<td>Is WHMPress installed and active?</td>
				<td><?php echo $is_helper ? "Yes" : "No"; ?></td>
			</tr>
		</table>
		<?php wp_die();
	}

	function url_scheme_part( $url ) {
		if ( strpos( $url, "://" ) === false ) {
			$url = "://" . $url;
		}
		$parts = explode( "://", $url );

		$output_array           = [];
		$output_array["scheme"] = $parts[0];
		$output_array["url"]    = isset( $parts[1] ) ? $parts[1] : "";

		return $output_array;
	}

	public function client_area() {
		#register_activation_hook( __FILE__, array( $this, 'activate' ) );
		#register_deactivation_hook(__FILE__,array($this, 'deactivate'));
		#register_uninstall_hook(__FILE__,array($this, 'uninstall'));
		//$this->generate_shortcodes();
		add_action( "init", [ $this, "whmp_init" ], 10 );
		add_action( 'wp_head', [ $this, 'whmp_header' ], 10 );
		add_action( 'wp_enqueue_scripts', [ $this, 'mytheme_enqueue_scripts' ] );

		# Adding content in page
		//add_filter('the_content', array($this, 'the_page_content'), 10, 3);
	}

	function whmp_header() {
		//echo '<script type="text/javascript">$=jQuery;</script>';
	}

	function whmp_init() {
		if ( ! is_admin() ) {
			ob_start();
			$W = new WHMPress_Client_Area();
			$W->start_session();
		} else {
			wp_enqueue_script( 'whmcs-hash-change', WHMP_CA_URL . 'admin/js/easytabs/jquery.hashchange.min.js', [ 'jquery' ], false, true );
			wp_enqueue_script( 'whmcs-easy-tabs', WHMP_CA_URL . 'admin/js/easytabs/jquery.easytabs.min.js', [ 'jquery' ], false, true );

			wp_enqueue_script( 'whmcs-hash-change' );
			wp_enqueue_script( 'whmcs-easy-tabs' );

			wp_register_style( 'whmcs-admin-settings-style', WHMP_CA_URL . 'admin/css/style.css', [] );
			wp_enqueue_style( 'whmcs-admin-settings-style' );
		}
	}

	function mytheme_enqueue_scripts() {
		if ( ! is_admin() ) {
			if ( get_option( 'jquery_source' ) == "google" ) {
				wp_deregister_script( 'jquery' );
				wp_register_script( 'jquery', '//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js', false, '1.7.2' );
				wp_enqueue_script( 'jquery' );
			} else if ( "google1.11.2" == get_option( 'jquery_source' ) ) {
				wp_deregister_script( 'jquery' );
				wp_register_script( 'jquery', '//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js', false, '1.11.2' );
				wp_enqueue_script( 'jquery' );
			} else if ( "google2.1.3" == get_option( 'jquery_source' ) || get_option( 'jquery_source' ) == "" ) {
				wp_deregister_script( 'jquery' );
				wp_register_script( 'jquery', '//ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js', false, '2.1.3' );
				wp_enqueue_script( 'jquery' );
			} else if ( "whmcs" == get_option( 'jquery_source' ) ) {
				wp_deregister_script( 'jquery' );
				wp_register_script( 'jquery', $this->get_whmcs_url() . '/assets/js/jquery.min.js', false );
				wp_enqueue_script( 'jquery' );
			}
		}
	}

	function get_client_area_page() {
		//$page = get_option("client_area_page_url");
		$page = $this->get_client_area_page_id();
		if ( is_numeric( $page ) && get_post_status( $page ) !== false ) {
			$page = get_page_link( $page );
		}
		if ( substr( $page, 0, 4 ) == "http" ) {
			return $page;
		} else {
			return get_bloginfo( "url" ) . "/" . $page;
		}
	}

	function get_current_web_url() {
		$url = "http" . ( ( $_SERVER['SERVER_PORT'] == 443 ) ? "s://" : "://" ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

		return $url;
	}

	/**
	 * @param bool $remove_vars
	 *
	 * @return int|mixed|string|void
	 *
	 * This function will return current page URL where ClientArea shortcode is available.
	 */
	public function get_current_url( $remove_vars = false ) {
		$url   = "";
		$lang  = $this->get_current_language();
		$langs = get_option( "whmp_langs" );
		if ( isset( $langs[ $lang ] ) && get_post_status( $langs[ $lang ] ) !== false ) {
			$url = get_page_link( $langs[ $lang ] );
		}

		if ( empty( $url ) ) {
			$url = get_option( "whmpress_current_page" );
		}
		if ( empty( $url ) ) {
			$url = $this->get_client_area_page();
		}

		if ( $remove_vars ) {
			$url = preg_replace( '/\?.*/', '', $url );
		}

		return $url;
	}

	public function activate() {
		// Nothing to do on Activation
	}

	public function deactivate() {
		// Nothing to do on DeActivatoin yet.
	}

	function uninstall() {
		// Do when plugin un-install
	}

	public function generate_shortcodes() {
		// Getting list of all shortcodes
		global $whmpca_shortcodes_list;
		if ( is_array( $whmpca_shortcodes_list ) ) {
			foreach ( $whmpca_shortcodes_list as $shortcode => $func ) {
				add_shortcode( $shortcode, [ $this, $func ] );
			}
		}
	}

	function get_whmrepss_version() {
		if ( ! $this->is_whmpress_activated() ) {
			return "0";
		}
		$files = get_plugins();
		foreach ( $files as $k => $data ) {
			if ( basename( $k ) == "whmpress.php" ) {
				return $data["Version"];
			}
		}

		//$data = get_plugin_data('whmpress/whmpress.php');
		return "0";
	}

	function is_whmpress_activated() {
		return is_plugin_active( 'whmpress/whmpress.php' );
	}

	function __call( $func, $args ) {
		$file = WHMP_CA_PATH . "/includes/shortcodes/" . $func . ".php";

		if ( is_file( $file ) ) {
			ob_start();
			include( $file );

			return ob_get_clean();

		} else {
			return __( "Oops, shortcode codes file '" . basename( $file ) . "' missing", WHMP_LANG );
		}
	}

	function whmpress_client_area_function( $args, $content = null ) {
		$client_area_page_url = $this->get_client_area_page_id();
		if ( ! is_numeric( $client_area_page_url ) ) {
			$client_area_page_url = url_to_postid( $client_area_page_url );
		}

		# If Permalink is enable then reidriect url without query string.
		if ( $this->is_permalink() && count( $_GET ) > 0 ) {
			if ( is_ssl() ) {
				$REQUEST_URI = "https://";
			} else {
				$REQUEST_URI = "http://";
			}
			$REQUEST_URI .= $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
			$REQUEST_URI  = substr( $REQUEST_URI, 0, strpos( $REQUEST_URI, "?" ) );
			$redirect_url = trailingslashit( $redirect_url . $REQUEST_URI );
			if ( isset( $_GET["whmpca"] ) ) {
				$redirect_url .= $_GET["whmpca"] . "/";
				unset( $_GET["whmpca"] );
			}
			foreach ( $_GET as $k => $v ) {
				$redirect_url .= $k . "/" . $v . "/";
			}
			wp_redirect( $redirect_url );
		}

		//echo $client_area_page_url." ".$post->ID;
		/*global $post;
        if ($client_area_page_url<>$post->ID) {
            $langs = get_option("whmp_langs");
            $current_lang = $this->get_current_language();
            $found = false;
            foreach($langs as $k=>$lang) {
                if ($lang==$current_lang && $post->ID==$langs["page"][$k]) {
                    $found=$k;
                    break;
                }
            }

            if ($found!==false) {
                //return "Found ID: ".$langs["page"][$found];
            } else {
                $link = rtrim(get_admin_url(),"/")."/admin.php?page=whmp_client-area";
                return "\n".__("Language: {$current_lang} - Current page/post is not selected in <b>Client Area</b> Settings of <b>WHMPress</b>. Click <a href='$link'><b>here to open Settings page</b></a>.", "whmpress");
            }
        }*/

		if ( isset( $args["whmcs_template"] ) ) {
			$this->systpl = $args["whmcs_template"];
		}

		$html = include_once( WHMP_CA_PATH . "/includes/shortcodes/whmpress_client_area_function.php" );

		//header('Content-type: text/html; charset=UTF-8');

		return "\n<!-- WHMPress Client Area -->\n<div class='whmp whmpress_client_area'>\n" . $html . "\n</div>\n<!-- End WHMPress Client Area -->";
	}

	/**
	 * @param string $page
	 * @param string $whmcs_template
	 * @param string $carttpl
	 *
	 * @return string
	 *
	 * This function will generate WHMCS page url e.g. $this->whmp_http("clientarea") will return
	 * WHMCS_URL/clientarea.php?language......
	 */
	function whmp_http( $page = "index", $whmcs_template = "", $carttpl = "" ) {
		global $wpdb;

		$whmcs = $this->get_whmcs_url();
		//$whmcs=$this->get_current_url();

		if ( substr( $whmcs, - 1 ) != '/' ) {
			$whmcs .= '/';
		}
		if ( ( strpos( $whmcs, 'https://' ) !== 0 ) && isset( $_REQUEST['sec'] ) && ( $_REQUEST['sec'] == '1' ) ) {
			$whmcs = str_replace( 'http://', 'https://', $whmcs );
		}
		$vars = "";

		if ( $page == 'verifyimage' ) {
			$http = $whmcs . 'includes/' . $page . '.php';
		} elseif ( isset( $_REQUEST['whmpca'] ) && ( $_REQUEST['whmpca'] == 'js' ) ) {
			$http = $whmcs . $_REQUEST['js'];

			return $http;
		} elseif ( isset( $_REQUEST['whmpca'] ) && ( $_REQUEST['whmpca'] == 'png' ) ) {
			$http = $whmcs . $_REQUEST['png'];

			return $http;
		} elseif ( isset( $_REQUEST['whmpca'] ) && ( $_REQUEST['whmpca'] == 'jpg' ) ) {
			$http = $whmcs . $_REQUEST['jpg'];

			return $http;
		} elseif ( isset( $_REQUEST['whmpca'] ) && ( $_REQUEST['whmpca'] == 'gif' ) ) {
			$http = $whmcs . $_REQUEST['gif'];

			return $http;
		} elseif ( isset( $_REQUEST['whmpca'] ) && ( $_REQUEST['whmpca'] == 'css' ) ) {
			$http = $whmcs . $_REQUEST['css'];

			return $http;
		} elseif ( substr( $page, - 1 ) == '/' ) {
			$http = $whmcs . substr( $page, 0, - 1 );
		} else {
			if ( $this->is_permalink() ) {
				$http = $whmcs . $page . '.php';
			} else {
				$http = $whmcs . $page;
			}
		}

		$CallingHTTP = basename( $http );
		if ( strpos( $CallingHTTP, "?" ) ) {
			$CallingHTTP = substr( $CallingHTTP, 0, strpos( $CallingHTTP, "?" ) );
		}
		/*
        ## These options will not work with WHMCS 7 and above.
        if (
            $CallingHTTP == "knowledgebase.php"
            || $CallingHTTP == "downloads.php"
            || $CallingHTTP == "announcements.php"
            || $CallingHTTP == "index.php"
        ) {
            $http = str_replace("https://", "http://", $http);
        }*/

		$and = "";
		//unset($_GET["whmpca"]);
		if ( count( $_GET ) > 0 ) {
			foreach ( $_GET as $n => $v ) {
				if ( $n != "page_id" && $n != "ccce" && $n != 'whmcspage' ) {
					if ( is_array( $v ) ) {
						foreach ( $v as $n2 => $v2 ) {
							$vars .= $and . $n . '[' . $n2 . ']' . '=' . urlencode( $v2 );
						}
					} else {
						$vars .= $and . $n . '=' . urlencode( $v );
					}
					$and = "&";
				}
			}
		}

		if ( isset( $_GET['whmcspage'] ) ) {
			$vars .= $and . 'page=' . $_GET['whmcspage'];
			$and = '&';
		}

		//if ($whmcs_template=="") $whmcs_template=$this->get_whmcs_theme_name();

		if ( ! isset( $_GET["language"] ) && strtolower( get_option( "whmp_follow_lang" ) ) <> "no" ) {
			$lang = get_locale();
			$lang = isset( $this->Langs[ $lang ] ) ? $this->Langs[ $lang ] : "English";
			if ( get_option( "whmp_wp_lang" ) <> "yes" && get_option( "whmp_wp_lang" ) <> "" ) {
				$lang = get_option( "whmp_wp_lang" );
			}
			$vars .= $and . "language=" . $lang;
			$and = '&';
		}

		if ( ! isset( $_GET["currency"] ) ) {
			$curr = whmp_get_currency();
			$vars .= $and . "currency=" . $curr;
			$and = '&';
		}

		if ( ! isset( $_REQUEST["a"] ) ) {
			$_REQUEST["a"] = "";
		}
		if ( strpos( $vars, "systpl=" ) === false && @$_REQUEST["a"] <> "confproduct" ) {
			if ( $whmcs_template <> "" ) {
				$vars .= $and . 'systpl=' . $whmcs_template;
				$and = "&";
			}
		}

		if ( $carttpl <> "" ) {
			$vars .= "&carttpl=" . $carttpl;
		}

		if ( $vars ) {
			$http .= '?' . $vars;
		}

		if ( $page == 'dl' ) {
			$whmcs    = $this->url_scheme_part( $whmcs );
			$goto_url = SERVE_FILES . "?file={$whmcs["url"]}dl.php&scheme={$whmcs["scheme"]}&" . $vars;
			echo "Please wait while downloading ...";
			echo "<script>
            window.location.href = '$goto_url';
            </script>";
			die;
		}

		return $http;
	}

	function get_whmcs_theme_name() {
		if ( $this->systpl <> "" ) {
			return $this->systpl;
		}
		global $wpdb;
		$Q          = "SELECT `value` FROM `" . $wpdb->prefix . "whmpress_configuration` WHERE `setting`='Template'";
		$theme_name = $wpdb->get_var( $Q );
		if ( $theme_name == "" || $theme_name == "default" ) {
			$theme_name = "";
		}

		return $theme_name;
	}

	public function serve_files( $url, $post_vars = [], $get_vars = [], $force_get = false ) {
		if ( $this->is_whmpress_activated() ) {
			$WHMPress = new WHMPress();
		}
		$query_string = parse_url( $url, PHP_URL_QUERY );
		if ( ! is_array( $query_string ) ) {
			$query_string = "";
		}
		parse_str( $query_string, $query_string );
		$get_vars = array_merge( $get_vars, $query_string );
		if ( isset( $get_vars["whmp_url"] ) ) {
			unset( $get_vars["whmp_url"] );
		}

		$url = preg_replace( '/\?.*/', '', $url );

		## If requesting download a file, then filter URL.
		if ( substr( basename( $url ), 0, 6 ) == "dl.php" ) {
			unset( $get_vars["file"] );
			unset( $get_vars["scheme"] );
			unset( $get_vars["dl_php"] );
		}

		if ( count( $get_vars ) > 0 ) {
			$url .= "?" . http_build_query( $get_vars );
		}

		$post_vars["whmp_ip"] = $_SERVER["REMOTE_ADDR"];
		if ( $this->is_whmpress_activated() && $WHMPress->verified_purchase() ) {
			unset( $post_vars["whmp_ip"] );
		}

		$cookies = $this->curl_cookies();
		$ch      = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_HEADER, true );
		curl_setopt( $ch, CURLOPT_VERBOSE, false );
		curl_setopt( $ch, CURLOPT_COOKIE, $cookies );
		//curl_setopt($ch, CURLOPT_POST, 1);

		//if (basename($url))

		if ( substr( $url, 0, 5 ) == "https" ) {
			curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
			curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
		}

		curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
		$t = get_option( 'curl_timeout_whmp' );
		if ( ! is_numeric( $t ) ) {
			$t = "20";
		}
		curl_setopt( $ch, CURLOPT_TIMEOUT, $t ); //timeout in seconds

		//curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);

		if ( is_array( $post_vars ) ) {
			$post_vars = http_build_query( $post_vars );
		}

		//if (!empty($post_vars) && $force_get===false) {
		if ( $force_get === false ) {
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $post_vars );
		}

		$output = curl_exec( $ch );

		if ( $errno = curl_errno( $ch ) ) {
			$error_message = curl_error( $ch );

			return "cURL error:\n {$error_message}<br />Fetching2: $url<br /><pre>" . print_r( $_REQUEST, true ) . "</pre>";
		}

		$header_size = curl_getinfo( $ch, CURLINFO_HEADER_SIZE );
		$header      = substr( $output, 0, $header_size );
		$output      = substr( $output, $header_size );

		preg_match_all( '/^Set-Cookie:\s*([^\r\n]*)/mi', $header, $ms );
		$cookies = [];
		foreach ( $ms[1] as $m ) {
			$sets = explode( ";", $m );
			$name = $value = $path = "";
			foreach ( $sets as $line ) {
				$line  = trim( $line );
				$sets2 = explode( "=", $line );
				if ( count( $sets2 ) == "2" && $sets2[0] == "path" ) {
					$path = $sets2[1];
				} elseif ( count( $sets2 ) == "2" ) {
					$name  = $sets2[0];
					$value = $sets2[1];
				}
			}
			setcookie( $name, $value, strtotime( '+30 days' ), $path );
		}

		$header = http_parse_headers( $header );

		$info = curl_getinfo( $ch );
		if ( $info["http_code"] == "302" || $info["http_code"] == "301" ) {
			$next_url = curl_getinfo( $ch, CURLINFO_REDIRECT_URL );
			if ( is_null( $next_url ) ) {
				$next_url = $header["Location"];
			}

			if ( $next_url <> "" ) {
				$next_url = str_replace( "?", "&", $next_url );
				$next_url = $this->url_scheme_part( $next_url );
				$next_url = SERVE_FILES . "?file=" . $next_url["url"] . "&scheme=" . $next_url["scheme"];

				if ( ! headers_sent() ) {
					header( 'Location:' . $next_url );
				} else {
					echo "<script>window.location.href='" . $next_url . "'</script>";
				}
				die();
			}
		} else if ( $info["http_code"] <> "200" ) {
			#$this->debug("Not 200?:");
			#$this->debug($header);
			#$this->debug(curl_getinfo($ch, CURLINFO_REDIRECT_URL));
		}

		$type = strtolower( @pathinfo( $url, PATHINFO_EXTENSION ) );
		if ( $type == "css" ) {
			//$output = $this->parse_css($output);
		}

		curl_close( $ch );

		return [ "headers" => $header, "output" => $output ];
	}

	public function get_client_area_page_id() {
		$lang  = $this->get_current_language();
		$langs = get_option( "whmp_langs" );

		if ( isset( $langs[ $lang ] ) ) {
			return $langs[ $lang ];
		} else {
			global $post;
			$client_area_page_url = get_option( "client_area_page_url" );
			if ( ! is_numeric( $client_area_page_url ) ) {
				$client_area_page_url = url_to_postid( $client_area_page_url );
			}
			if ( $post ) {
				if ( $client_area_page_url == $post->ID ) {
					return $post->ID;
				}

				if ( $client_area_page_url <> $post->ID ) {
					if ( isset( $langs[ $lang ] ) ) {
						return $langs[ $lang ];
					} else {
						return $client_area_page_url;
					}
				}
			} else {
				return $client_area_page_url;
			}
		}
	}

	function whmp_rewrite_rule() {
		global $wp_rewrite;
		//$url = get_option('client_area_page_url');
		$url = $this->get_client_area_page_id();
		if ( empty( $url ) ) {
			return false;
		}
		if ( is_numeric( $url ) && get_post_status( $url ) !== false ) {
			$postid = $url;
			$url    = get_page_link( $url );
		} else {
			$postid = url_to_postid( $url );
			if ( empty( $postid ) ) {
				return false;
			}
		}

		if ( get_post_status( $postid ) !== false ) {
			$url      = get_page_link( $postid );
			$main_url = get_bloginfo( "url" );
			$url      = substr( $url, strlen( $main_url ) );

			$url = trim( $url, "/" );

			if ( ! isset( $wp_rewrite->rules[ $url . '(/([^/]+))?(/([^/]+))?/?' ] ) ) {
				add_rewrite_rule( $url . '(/([^/]+))?(/([^/]+))?/?', 'index.php?page_id=' . $postid, 'top' );
				flush_rewrite_rules();
			}
		}
	}

	function rewrite_rule_with_languages() {
		$this->whmp_rewrite_rule();
		$langs = get_option( "whmp_langs" );
		global $wp_rewrite;
		if ( is_array( $langs ) ) {
			$written = false;
			foreach ( $langs as $k => $postid ) {
				if ( ! is_numeric( $postid ) ) {
					continue;
				}
				if ( empty( $postid ) ) {
					continue;
				}

				## Check if page or post exists in wordpress
				if ( get_post_status( $postid ) !== false ) {
					$url = get_page_link( $postid );

					$main_url = get_bloginfo( "url" );
					$url      = substr( $url, strlen( $main_url ) );
					$url      = trim( $url, "/" );
					if ( ! isset( $wp_rewrite->rules[ $url . '(/([^/]+))?(/([^/]+))?/?' ] ) ) {
						add_rewrite_rule( $url . '(/([^/]+))?(/([^/]+))?/?', 'index.php?page_id=' . $postid, 'top' );
						$written = true;
					}
				}
			}
			if ( $written ) {
				flush_rewrite_rules();
			}
		} else {
			$this->whmp_rewrite_rule();
		}
	}

	public function read_remote_url( $fetch_url, $post_vars = [], $files = [], $redirect = true ) {
		if ( $this->is_whmpress_activated() ) {
			$WHMPress = new WHMPress();
		}
		set_time_limit( 0 );
		$post_vars = stripslashes_deep( $post_vars );

		global $wpdb;
		if ( $this->is_whmpress_activated() ) {
			$Q     = "SELECT `value` FROM `" . whmp_get_configuration_table_name() . "` WHERE `setting`='SystemSSLURL' ORDER BY `setting` DESC";
			$value = $wpdb->get_var( $Q );
			if ( $value <> "" && substr( get_option( "whmcs_url" ), 0, 8 ) <> "https://" ) {
				return "WHMPress detected that your WHMCS configured SSL URL (WHMCS Admin Area > Setup > General Settings) but you have Non SSL URL in WHMpress > Settings > General (tab). You must select HTTPS in WHMPress or you will end up with redirect loops.";
			}
		}

		$ch = curl_init();
		// Uploading files
		$is_attachment = false;
		if ( isset( $files["attachments"]["error"] ) && is_array( $files["attachments"]["error"] ) && count( $files["attachments"]["error"] ) > 0 ) {
			$y = 0;
			for ( $x = 0; $x < count( $files["attachments"]["error"] ); $x ++ ) {
				if ( $files["attachments"]["error"][ $x ] == "0" ) {
					$is_attachment                = true;
					$post_vars["attachments[$y]"] = curl_file_create( $files["attachments"]["tmp_name"][ $x ], $files["attachments"]["type"][ $x ], $files["attachments"]["name"][ $x ] );
					$y ++;
				}
			}
		}

		# Checking and setting whmppage into page
		$fetch_url = str_replace( "whmppage=", "page=", $fetch_url );

		$_SERVER["HTTP_REFERER"] = $fetch_url;

		$cookies = $this->curl_cookies();

		if ( count( $post_vars ) > 0 ) {
			#$this->debug($fetch_url);
			#$this->debug($post_vars);
		}
		$post_vars["whmp_ip"] = $this->get_ip();
		if ( $this->is_whmpress_activated() && ! $WHMPress->verified_purchase() ) {
			unset( $post_vars["whmp_ip"] );
		}
		$fetch_url = str_replace( "&language=English&currency=1", "", $fetch_url );

		if ( substr_count( $fetch_url, "?" ) > 1 ) {
			$occur = 0;
			for ( $x = 0; $x < strlen( $fetch_url ); $x ++ ) {
				if ( $fetch_url[ $x ] == "?" ) {
					$occur ++;
					if ( $occur > 1 ) {
						$fetch_url[ $x ] = "&";
					}
				}
			}
		}
		curl_setopt( $ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:7.0.1) Gecko/20100101 Firefox/7.0.12011-10-16 20:23:00" );
		curl_setopt( $ch, CURLOPT_URL, $fetch_url );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_HEADER, true );
		curl_setopt( $ch, CURLOPT_VERBOSE, false );
		curl_setopt( $ch, CURLOPT_COOKIE, $cookies );

		if ( substr( $fetch_url, 0, 5 ) == "https" ) {
			curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
			curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
		}

		/*curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,10);
        $t = get_option('curl_timeout_whmp'); if (!is_numeric($t)) $t="20";
        curl_setopt($ch, CURLOPT_TIMEOUT, $t); //timeout in seconds*/

		curl_setopt( $ch, CURLOPT_BINARYTRANSFER, true );
		//curl_setopt($ch , CURLOPT_HTTPHEADER , $header);
		if ( count( $post_vars ) > 0 ) {
			curl_setopt( $ch, CURLOPT_POST, 1 );
			$the_post = $post_vars;
			if ( is_array( $post_vars ) && ! $is_attachment ) {
				$post_vars = http_build_query( $post_vars );
			}
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $post_vars );
		}
		$output = curl_exec( $ch );

		if ( $errno = curl_errno( $ch ) ) {
			$error_message = curl_error( $ch );

			return "cURL error:\n {$error_message}<br />Fetching: $fetch_url";
		}

		$header_size = curl_getinfo( $ch, CURLINFO_HEADER_SIZE );

		# If Character set required
		#$charset = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
		$header = substr( $output, 0, $header_size );

		$output = substr( $output, $header_size );

		## If WP user is logged in then also logout WP user with WHMCS user.
		if ( substr( basename( $fetch_url ), 0, 10 ) == "logout.php" && $redirect && get_option( 'whmcs_enable_sso' ) == "1" && is_user_logged_in() ) {
			wp_logout();
		}

		// Setting cookies
		preg_match_all( '/^Set-Cookie:\s*([^\r\n]*)/mi', $header, $ms );
		$cookies = [];
		foreach ( $ms[1] as $m ) {
			$sets = explode( ";", $m );
			$name = $value = $path = "";
			foreach ( $sets as $line ) {
				$line  = trim( $line );
				$sets2 = explode( "=", $line );
				if ( count( $sets2 ) == "2" && $sets2[0] == "path" ) {
					$path = $sets2[1];
				} elseif ( count( $sets2 ) == "2" ) {
					$name  = $sets2[0];
					$value = $sets2[1];
				}
			}
			$cookies[ $name ] = $value;
			setcookie( $name, $value, strtotime( '+30 days' ), $path );
		}
		$header = http_parse_headers( $header );

		$info = curl_getinfo( $ch );
		if ( $this->is_permalink() ) {
			$includeParameters = true;
		} else {
			$includeParameters = false;
		}
		if ( $info["http_code"] == "302" || $info["http_code"] == "301" ) {
			$next_url = curl_getinfo( $ch, CURLINFO_REDIRECT_URL );

			if ( is_null( $next_url ) ) {
				$next_url = $header["Location"];
			}

			if ( get_option( "whmcs_enable_sync" ) == "1" && stripos( $fetch_url, "dologin.php" ) !== false && stripos( $next_url, "incorrect=true" ) === false ) {
				$priority = get_option( 'whmcs_both_ways_priority' );
				if ( $priority <> "wp" ) {
					$priority = "whmcs";
				}
				$username = $_POST['username'];
				$password = $_POST['password'];
				if ( get_option( "sync_direction" ) == "1" || ( get_option( "sync_direction" ) == "3" && $priority == "whmcs" ) ) {
					$is_wp_user = $this->is_wp_user( $username );

					## Create WordPress user if not exists.
					$user = get_user_by( 'email', $username );
					if ( ! $is_wp_user ) {
						$role = get_option( 'whmcs_wordpress_role' );
						if ( empty( $role ) ) {
							$role = 'subscriber';
						}
						$w_user = $this->get_whmcs_user( $username );

						$userdata = [
							'user_login'   => $username,
							'user_email'   => $username,
							'user_pass'    => $password,
							'first_name'   => $w_user['firstname'],
							'last_name'    => $w_user['lastname'],
							'display_name' => $w_user['fullname'],
							'description'  => __( "User created by WHMCS Client Area", "whmpress" ),
							'role'         => $role,
						];
						$user_id  = wp_insert_user( $userdata );

						if ( ! is_wp_error( $user_id ) ) {
							$this->update_wp_user_metas( $user_id, $w_user );
							$this->start_session();
							$_SESSION['whmcs_wp_password'] = $password;
						}
					} else {
						## If wordpress user exists then login with wp user.
						if ( $user ) {
							wp_set_password( $password, $user->ID );
						}
					}
					if ( $user ) {
						wp_set_current_user( $user->ID, $user->user_login );
						wp_set_auth_cookie( $user->ID );
						do_action( 'wp_login', $user->user_login );
					}
				} else {
					$username = $_POST['username'];
					$password = $_POST['password'];
					$user     = get_user_by( 'email', $username );
					if ( $user && wp_check_password( $password, $user->data->user_pass, $user->ID ) ) {
						wp_set_current_user( $user->ID, $user->user_login );
						wp_set_auth_cookie( $user->ID );
						do_action( 'wp_login', $user->user_login );
					}
				}
			}

			if ( ! empty( $next_url ) && isset( $the_post['ajax'] ) ) {
				//$this->debug($next_url);
				return $this->read_remote_url( $next_url );
			} else if ( $next_url <> "" && $redirect ) {
				$dom1 = parse_url( $fetch_url );
				$dom2 = parse_url( $next_url );
				if ( @$dom1["host"] == @$dom2["host"] || ! isset( $dom2["host"] ) ) {
					$next_url = $this->set_url( $this->get_current_url( $includeParameters ), $next_url );
				}

				#echo $next_url."<br />"; die;
				ob_start();
				if ( ! headers_sent() ) {
					header( 'Location:' . $next_url );
				} else {
					echo "<script>window.location.href='" . $next_url . "'</script>";
				}
				die();
			} else {
				return $next_url;
			}
		} else if ( $info["http_code"] <> "200" ) {
			if ( defined( 'WP_DEBUG' ) && ( true === WP_DEBUG || WP_DEBUG == 1 ) ) {
				return "<pre>Status Code: " . $info["http_code"] . "<br />Trying $fetch_url</pre>";
			} else {
				wp_redirect( get_home_url() . "/oo0ops" );
			}
		}

		curl_close( $ch );

		return $output;
	}

	function curl_cookies() {
		$cookies = "";
		foreach ( $_COOKIE as $k => $v ) {
			//if (substr($k,0,5)=="WHMCS")
			$cookies .= "$k=$v; ";
		}
		$cookies = rtrim( $cookies, "; " );

		return $cookies;
	}

	public function get_ip_address() {
		$ip_keys = [
			'HTTP_CLIENT_IP',
			'HTTP_X_FORWARDED_FOR',
			'HTTP_X_FORWARDED',
			'HTTP_X_CLUSTER_CLIENT_IP',
			'HTTP_FORWARDED_FOR',
			'HTTP_FORWARDED',
			'REMOTE_ADDR',
		];
		foreach ( $ip_keys as $key ) {
			if ( array_key_exists( $key, $_SERVER ) === true ) {
				foreach ( explode( ',', $_SERVER[ $key ] ) as $ip ) {
					// trim for safety measures
					$ip = trim( $ip );
					// attempt to validate IP
					if ( $this->validate_ip( $ip ) ) {
						return $ip;
					}
				}
			}
		}

		return isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : false;
	}

	function validate_ip( $ip ) {
		if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) === false ) {
			return false;
		}

		return true;
	}

	/**
	 * Parse JS files.
	 *
	 * @param      $html
	 * @param bool $includeParameters
	 *
	 * @return mixed
	 */
	function parse_for_js( $html, $includeParameters = true ) {
		#require_once WHMP_CA_PATH."/includes/ganon/ganon.php";
		#$this->debug($html);
		#$html = str_get_dom($html);
		if ( ! $this->is_permalink() ) {
			$includeParameters = false;
		}

		$_whmcs_url = $this->url_scheme_part( $this->get_whmcs_url() );

		$Fields = [
			'"announcements.php"' => '"' . WHMP_CA_URL . 'ajax.php?file=announcements"',
			'"cart.php",'                               => '"' . WHMP_CA_URL . 'ajax.php?file=cart&",',
			"includes/verifyimage.php?"                 => SERVE_FILES . "?file=" . $_whmcs_url["url"] . "/includes/verifyimage.php?scheme=" . $_whmcs_url["scheme"] . "&",
			"window.location = 'cart.php?a=confproduct" => "window.location = '" . $this->set_url( $this->get_current_url( $includeParameters ), "cart.php" ) . "?a=confproduct",
			'cart.php?a=view'                           => $this->set_url( $this->get_current_url( $includeParameters ), "cart.php?a=view" ),
			//SERVE_FILES."?whmp_url=".($this->get_whmcs_url()."/cart.php&a=view"),
			'cart.php?a=confdomains'                    => $this->set_url( $this->get_current_url( $includeParameters ), "cart.php?a=confdomains" ),
			//SERVE_FILES."?whmp_url=".($this->get_whmcs_url()."/cart.php&a=confdomains"),
			"cart.php?gid="                             => $this->set_url( $this->get_current_url( $includeParameters ), "cart.php" ) . "?gid=",
			//SERVE_FILES."?whmp_url=".($this->get_whmcs_url()."/cart.php&gid="),
			#"window.location='cart.php'" => "window.location='".$this->set_url($this->get_current_url($includeParameters), "cart.php")."'",     //SERVE_FILES."?whmp_url=".($this->get_whmcs_url()."/cart.php&gid="),
			'language="javascript"'                     => '',
			'cart.php'                                  => SERVE_FILES . "?file=" . ( rtrim( $_whmcs_url["url"], "/" ) . "/cart.php&scheme=" . $_whmcs_url["scheme"] ),
			'src="images'                               => 'src="' . $this->get_whmcs_url() . '/images',
			'announcements.php'                         => $this->set_url( $this->get_current_url( $includeParameters ), "announcements.php" ),
			'viewticket.php?'                           => ( $this->set_url( $this->get_current_url( $includeParameters ), "viewticket.php" ) . "&" ),
			"$('"                                       => "jQuery('",
			'domainchecker.php'                         => WHMP_CA_URL . "serve-files.php?file=" . $_whmcs_url["url"] . "domainchecker.php&scheme=" . $_whmcs_url["scheme"],
			"whois.php"                                 => WHMP_CA_URL . "serve-files.php?file=" . $_whmcs_url["url"] . "whois.php&scheme=" . $_whmcs_url["scheme"],
			"window.location = 'clientarea.php?"        => "window.location = '" . $this->set_url( $this->get_current_url( $includeParameters ), "clientarea.php" ) . "?&scheme=" . $_whmcs_url["scheme"],
			'"clientarea.php?'                          => '"' . WHMP_CA_URL . "serve-files.php?file=" . $_whmcs_url["url"] . "clientarea.php&scheme=" . $_whmcs_url["scheme"] . "&",
			"'clientarea.php?"                          => "'" . WHMP_CA_URL . "serve-files.php?file=" . $_whmcs_url["url"] . "clientarea.php&scheme=" . $_whmcs_url["scheme"] . "&",
			"'clientarea.php"                           => "'" . WHMP_CA_URL . "serve-files.php?file=" . $_whmcs_url["url"] . "clientarea.php&scheme=" . $_whmcs_url["scheme"],
			'"clientarea.php'                           => '"' . WHMP_CA_URL . "serve-files.php?file=" . $_whmcs_url["url"] . "clientarea.php&scheme=" . $_whmcs_url["scheme"],
			".post('serverstatus.php'"                  => ".post('" . WHMP_CA_URL . "serve-files.php?file=" . $_whmcs_url["url"] . "serverstatus.php&scheme=" . $_whmcs_url["scheme"] . "'",
			'.post("serverstatus.php"'                  => '.post("' . WHMP_CA_URL . "serve-files.php?file=" . $_whmcs_url["url"] . 'serverstatus.php&' . 'scheme=' . $_whmcs_url["scheme"] . '"',
			'.post("submitticket.php"'                  => '.post("' . WHMP_CA_URL . "serve-files.php?file=" . $_whmcs_url["url"] . 'submitticket.php&scheme=' . $_whmcs_url["scheme"] . '"',
			".post('submitticket.php'"                  => ".post('" . WHMP_CA_URL . "serve-files.php?file=" . $_whmcs_url["url"] . "submitticket.php&scheme=" . $_whmcs_url["scheme"] . "'",
			'"submitticket.php"'                        => '"' . WHMP_CA_URL . "serve-files.php?file=" . $_whmcs_url["url"] . 'submitticket.php?scheme=' . $_whmcs_url["scheme"] . '"',
			"container: 'body',"                        => "container: '#header',",
			'form[action*="dologin.php"]'               => '.logincontainer form',
			"<~root~>"                                  => "",
			"</~root~>"                                 => "",
			"$("                                        => "jQuery(",
			"jQuery()"                                  => "$()",
			"$.extend"                                  => "jQuery.extend",
		    "window.location.pathname," => '"' . WHMP_CA_URL . 'ajax.php?file=cart",',
		];

		$html = str_replace( array_keys( $Fields ), array_values( $Fields ), $html );

		return $html;
	}

	function parse_css( $html ) {
		preg_match_all( '/url\((.*?)\)/is', $html, $matches );
		$FoundImages = [];
		foreach ( $matches[1] as $img ) {
			$FoundImages[] = $img;
		}
		$FoundImages = array_unique( $FoundImages );
		$url         = $this->get_current_url( true );
		$url2        = $this->get_current_url();
		$target_url  = @parse_url( $url2 );
		$target_url  = @$target_url["query"];
		parse_str( $target_url, $target_url );
		$target_url = @$target_url["css"];
		foreach ( $FoundImages as $img ) {
			$img1 = str_replace( "'", "", $img );
			$img1 = str_replace( '"', "", $img1 );
			if ( substr( $img1, 0, 2 ) == "//" || substr( $img1, 0, 7 ) == "http://" || substr( $img1, 0, 8 ) == "https://" ) {
				// Do nothing
			} else {
				$ext  = pathinfo( $img1, PATHINFO_EXTENSION );
				$file = basename( $target_url );
				$U    = str_replace( $file, $img1, $target_url );
				$with = $url . "?whmpca=" . $ext . "&" . $ext . "=" . $U;
				$html = str_replace( $img, "'{$with}'", $html );
			}
		}

		return $html;
	}

	function parse_html( $html, $includeParameters = true, $remove_header_footer = true ) {
		if ( stripos( $html, "Logout" ) === false ) {
			$_SESSION['whmcs_loggedin'] = 0;
		} else {
			$_SESSION['whmcs_loggedin'] = 1;
		}

		$omit1_string = "<!--whmpress-omit-content-start-->";
		$omit2_string = "<!--whmpress-omit-content-end-->";
		$omit1_pos    = strpos( $html, $omit1_string );
		$omit2_pos    = strpos( $html, $omit2_string );

		if ( $omit1_pos !== false && $omit2_pos !== false ) {
			$html1 = substr( $html, 0, $omit1_pos );
			$html2 = substr( $html, $omit2_pos + strlen( $omit2_string ) );
			$html  = $html1 . $html2;
		}

		$omit         = false;
		$omit1_string = "<!--whmpress-omit-processing-start-->";
		$omit2_string = "<!--whmpress-omit-processing-end-->";
		$omit1_pos    = strpos( $html, $omit1_string );
		$omit2_pos    = strpos( $html, $omit2_string );
		if ( $omit1_pos !== false && $omit2_pos !== false ) {
			$omit_html = substr( $html, $omit1_pos + strlen( $omit1_string ), ( $omit2_pos - ( $omit1_pos + strlen( $omit1_string ) ) ) );
			$html1     = substr( $html, 0, $omit1_pos + strlen( $omit1_string ) );
			$html2     = substr( $html, $omit2_pos );
			$html      = $html1 . $html2;
			$omit      = true;
		}

		$html           = str_replace( 'name="name"', 'whmp_marzi', $html );
		$whmcs_template = "";
		if ( ! $this->is_permalink() ) {
			$includeParameters = false;
		}

		require_once( WHMP_CA_PATH . "/includes/ganon/ganon.php" );
		$_whmcs_url = $this->url_scheme_part( $this->get_whmcs_url() );

		$html = str_get_dom( $html );

		## Parsing all images tags and setting image paths.
		foreach ( $html( 'img' ) as $x => $element ) {
			$pos = strpos( $element->src, 'templates/' );
			if ( $pos !== false ) {
				$whmcs_template = substr( $element->src, $pos + 10 );
				$whmcs_template = explode( "/", $whmcs_template );
				$whmcs_template = $whmcs_template[0];
				break;
			}
		}

		if ( $whmcs_template == "six" ) {
			foreach ( $html( '#btnBulkOptions' ) as $x => $element ) {
				$element->delete();
			}
			$html = $html->html();
			$html = str_replace( 'Bulk Options', '<a href="domainchecker.php?search=bulk" id="btnBulkOptions" class="btn btn-warning btn-sm">Bulk Options</a>', $html );
			$html = str_get_dom( $html );
		}

		/**
		 * Affiliate span text setting
		 * Added in 2.4.8
		 */
		foreach ( $html( '.affiliate-referral-link > span' ) as $x => $element ) {
			$old_url = $element->getInnerText();
			$element->setInnerText( $this->set_url( $this->get_current_url( $includeParameters ), $old_url ) );
		}

		## Checking all link elements in HTML
		foreach ( $html( 'a' ) as $x => $element ) {
			$is_hash    = false;
			$hash_pos   = 0;
			$hash_after = "";

			if ( strpos( $element->href, "#" ) !== false ) {
				$is_hash    = true;
				$hash_pos   = strpos( $element->href, "#" );
				$hash_after = substr( $element->href, $hash_pos );
			}

			if ( substr( $element->href, 0, 1 ) == '?' || substr( $element->href, 0, 1 ) == '#' ) {
				// Do nothing
			}
			if ( strpos( $element->href, '#tabChangepw' ) !== false ) {
				$element->href = "#tabChangepw";
			} elseif ( $element->href == "networkissuesrss.php" ) {
				$element->target = "_blank";
				$element->href   = SERVE_FILES . "?file=" . $_whmcs_url["url"] . '/networkissuesrss.php&scheme=' . $_whmcs_url["scheme"];
			} elseif ( $element->href == "announcementsrss.php" ) {
				$element->target = "_blank";
				$element->href   = SERVE_FILES . "?file=" . $_whmcs_url["url"] . '/announcementsrss.php&scheme=' . $_whmcs_url["scheme"];
			} elseif ( strrpos( $element->href, "dosinglesignon=1" ) !== false && strrpos( $element->href, "clientarea.php" ) !== false ) {
				//$element->href = $this->get_whmcs_url() . "/" . str_replace("clientarea.php", "clientarea.pphp", $element->href);
			} elseif ( substr( $element->href, 0, 6 ) == "dl.php" || strpos( $element->href, "dl.php" ) ) {
				$element->href = SERVE_FILES . "?file=" . $_whmcs_url["url"] . "/dl.php&scheme={$_whmcs_url["scheme"]}&" . str_replace( "?", "&", substr( $element->href, strpos( $element->href, "dl.php" ) ) );
				#$element->download = strip_tags($element->getInnerText());
			} elseif ( substr( $element->href, 0, 13 ) == "viewemail.php" ) {
				$element->href = SERVE_FILES . "?file=" . $_whmcs_url["url"] . "/viewemail.php&scheme=" . $_whmcs_url["scheme"] . "&" . substr( $element->href, 14 );
			} elseif ( substr( $element->href, 0, 1 ) == "#" || substr( strtolower( $element->href ), 0, 11 ) == "javascript:" ) {
				// Do nothing
			} elseif ( substr( $element->href, 0, 7 ) == "http://" || substr( $element->href, 0, 8 ) == "https://" || substr( $element->href, 0, 2 ) == "//" ) {
				if ( substr( $element->href, 0, strlen( $this->get_whmcs_url() ) ) == $this->get_whmcs_url() ) {
					$element->href = $this->set_url( $this->get_current_url( $includeParameters ), $element->href );
				} else {
					$element->target = "_blank";
				}
				if ( $is_hash ) {
					$element->href .= $hash_after;
				}
			} else {
				$element->href = $this->set_url( $this->get_current_url( $includeParameters ), $element->href );
				if ( $is_hash ) {
					$element->href .= $hash_after;
				}
			}
			$element->href = ltrim( $element->href, "/" );
		}

		if ( function_exists( "whmpress_get_option" ) ) {
			$cache_enabled_whmp = whmpress_get_option( 'cache_enabled_whmp' );
		} else {
			$cache_enabled_whmp = get_option( 'cache_enabled_whmp' );
		}

		# Setting scripts's url
		$ExcluseJSFiles = get_option( "exclude_js_files" );
		$ExcluseJSFiles = explode( ",", $ExcluseJSFiles );
		$ExcluseJSFiles = array_map( "trim", $ExcluseJSFiles );

		$whmcs_url = $this->url_scheme_part( $this->get_whmcs_url() );

		#var_dump ($ExcluseJSFiles); die;
		$js_in_head = [];
		foreach ( $html( 'script' ) as $x => $element ) {
			$f = basename( $element->src );
			if ( empty( $element->src ) || substr( $element->src, 0, 21 ) == "http://www.google.com" || substr( $element->src, 0, 17 ) == "http://google.com" || substr( $element->src, 0, 22 ) == "https://www.google.com" || substr( $element->src, 0, 18 ) == "https://google.com" || substr( $element->src, 0, 12 ) == "//google.com" || substr( $element->src, 0, 16 ) == "//www.google.com" ) {
				/* If this block will execute then js files in this will remain same and will place at same place */
				if ( $element->parent->tag == "head" || $element->parent->parent->tag == "head" ) {
					$js_in_head[] = $element->src;
				}

			} else if ( ! $this->is_multi_language() && ( $cache_enabled_whmp == "1" || strtolower( $cache_enabled_whmp ) == "yes" ) ) {
				if ( substr( $element->src, 0, 8 ) == "https://" || substr( $element->src, 0, 7 ) == "http://" ) {
					$uri = $element->src;
				} else if ( substr( $element->src, 0, 2 ) == "//" ) {
					$uri = "http:" . $element->src;
				} else if ( substr( $element->src, 0, 1 ) == "/" ) {
					$uri = $_whmcs_url["scheme"] . "://";
					$UU  = parse_url( "http://" . $_whmcs_url["url"] );
					if ( ! empty( $UU['host'] ) ) {
						$uri .= $UU['host'];
					}
					$uri .= "/" . ltrim( $element->src, "/" );
				} else {
					$uri = $_whmcs_url["scheme"] . "://" . rtrim( $_whmcs_url["url"], "/" ) . "/" . ltrim( $element->src, "/" );
				}

				$folder = WHMP_CA_PATH . "/cache/";
				if ( ! is_dir( $folder ) ) {
					@mkdir( $folder );
				}
				$cache_path = $folder . basename( $uri );
				if ( ! is_file( $cache_path ) ) {
					$content = curl_get_file_contents( $uri );
					if ( $content !== false ) {
						$content = $this->parse_for_js( $content );
						@file_put_contents( $cache_path, $content );
					}
				}
				if ( is_file( $cache_path ) ) {
					$uri = WHMP_CA_URL . "cache/" . basename( $uri );
				}
				$element->src = $uri;

				if ( $element->parent->tag == "head" || $element->parent->parent->tag == "head" ) {
					$js_in_head[] = $element->src;
				}
			} else if ( ! in_array( $f, $ExcluseJSFiles ) ) {
				$src = $this->url_scheme_part( $element->src );

				if ( substr( $element->src, 0, 8 ) == "https://" || substr( $element->src, 0, 2 ) == "//" ) {
					/* If this block will execute then js files in this will remain same and place at top or bottom */
					if ( basename( $element->src ) == "clef.js" ) {
						$element->src = SERVE_FILES . "?file=" . $src["url"] . "&scheme=" . ( $src["scheme"] == "" ? $whmcs_url["scheme"] : $src["scheme"] );
					}
				} else {
					#$element->src = $this->set_url($this->get_current_url($includeParameters),$element->src,false);
					if ( substr( $element->src, 0, 7 ) == "http://" ) {
						$element->src = SERVE_FILES . "?file=" . $src["url"] . "&scheme=" . ( $src["scheme"] == "" ? $whmcs_url["scheme"] : $src["scheme"] );
					} else if ( substr( $element->src, 0, 1 ) == "/" ) {
						$domain       = parse_url( $this->get_whmcs_url() );
						$element->src = SERVE_FILES . "?file=" . @$domain["host"] . $element->src . "&scheme=" . @$domain["scheme"];
					} else {
						$element->src = SERVE_FILES . "?file=" . rtrim($_whmcs_url["url"],"/") . "/" . $element->src . "&scheme=" . $_whmcs_url["scheme"];
					}
				}

				if ( $element->parent->tag == "head" || $element->parent->parent->tag == "head" ) {
					$js_in_head[] = $element->src;
				}
			} else {
				$element->delete();
				echo "<!-- ** Deleting {$element->src} -->\n";
			}
		}
		$this->css_uris = [];
		# Setting CSS files.
		foreach ( $html( 'link[rel=stylesheet]' ) as $x => $element ) {
			$f = basename( $element->href );
			if ( ! in_array( $f, $ExcluseJSFiles ) ) {
				if ( $cache_enabled_whmp == "1" || strtolower( $cache_enabled_whmp ) == "yes" ) {
					if ( substr( $element->href, 0, 8 ) == "https://" || substr( $element->href, 0, 7 ) == "http://" ) {
						$uri = $element->href;
					} else if ( substr( $element->href, 0, 2 ) == "//" ) {
						$uri = "http:" . $element->href;
					} else if ( substr( $element->href, 0, 1 ) == "/" ) {
						$uri = $_whmcs_url["scheme"] . "://";
						$UU  = parse_url( "http://" . $_whmcs_url["url"] );
						if ( ! empty( $UU['host'] ) ) {
							$uri .= $UU['host'];
						}
						$uri .= "/" . ltrim( $element->href, "/" );
					} else {
						$uri = $_whmcs_url["scheme"] . "://" . trim( $_whmcs_url["url"], "/" ) . "/" . ltrim( $element->href, "/" );
					}

					$cache_path = WHMP_CA_PATH . "/cache/" . md5( $uri ) . "_" . basename( $uri );
					if ( ! is_file( $cache_path ) ) {
						$content = curl_get_file_contents( $uri );
						if ( $content !== false ) {
							$content = $this->parse_css_file( $content, $uri, true );
							@file_put_contents( $cache_path, $content );
						}
					}
					if ( is_file( $cache_path ) ) {
						$uri = WHMP_CA_URL . "cache/" . md5( $uri ) . "_" . basename( $uri );
					}
					$this->css_uris[] = $uri;
				} else if ( substr( $element->href, 0, 8 ) == "https://" || substr( $element->href, 0, 2 ) == "//" ) {
					// Do nothing
					$this->css_uris[] = $element->href;
				} else {
					$href = $this->url_scheme_part( $element->href );
					if ( substr( $element->href, 0, 7 ) == "http://" ) {
						if ( $remove_header_footer ) {
							$this->css_uris[] = SERVE_FILES . "?file=" . $href["url"] . "&scheme=" . ( $href["scheme"] == "" ? $whmcs_url["scheme"] : $href["scheme"] );
						} else {
							$element->href = SERVE_FILES . "?file=" . $href["url"] . "&scheme=" . ( $href["scheme"] == "" ? $whmcs_url["scheme"] : $href["scheme"] );
						}
					} else if ( substr( $element->href, 0, 1 ) == "/" ) {
						$domain = parse_url( $this->get_whmcs_url() );
						if ( $remove_header_footer ) {
							$this->css_uris[] = SERVE_FILES . "?file=" . @$domain["host"] . $element->href . "&scheme=" . @$domain["scheme"];
						} else {
							$element->href = SERVE_FILES . "?file=" . @$domain["host"] . $element->href . "&scheme=" . @$domain["scheme"];
						}
					} else {
						if ( $remove_header_footer ) {
							$this->css_uris[] = SERVE_FILES . "?file=" . $_whmcs_url["url"] . "/" . $element->href . "&scheme=" . $_whmcs_url["scheme"];
						} else {
							$element->href = SERVE_FILES . "?file=" . $_whmcs_url["url"] . "/" . $element->href . "&scheme=" . $_whmcs_url["scheme"];
						}
					}
				}
			}

			if ( $remove_header_footer ) {
				$element->delete();
			}
		}
		#print_r ($this->css_uris); die;

		# Settings images url
		foreach ( $html( 'img' ) as $x => $element ) {
			if ( substr( $element->src, 0, 8 ) == "https://" || substr( $element->src, 0, 2 ) == "//" ) {
				// Do nothing
			} else if ( strpos( $element->src, "placehold.it" ) !== false ) {
				// Do not chang images if they are serving from placehold.it
				// Added in 2.4.8
			} else {
				#$element->src = $this->set_url($this->get_current_url($includeParameters),$element->src,false);
				if ( get_option( "image_js_css_display" ) == "direct" && $element->src <> "" ) {
					$element->src = $this->get_whmcs_url() . "/" . $element->src;
				} elseif ( $element->src <> "" ) {
					#$element->src = $this->set_url($this->get_current_url($includeParameters),$element->src,false);
					$src = $this->url_scheme_part( $element->src );
					if ( substr( $element->src, 0, 7 ) == "http://" ) {
						$element->src = SERVE_FILES . "?file=" . $src["url"] . "&scheme=" . ( $src["scheme"] == "" ? $whmcs_url["scheme"] : $src["scheme"] );
					} else if ( substr( $element->src, 0, 1 ) == "/" ) {
						$domain       = parse_url( $this->get_whmcs_url() );
						$element->src = SERVE_FILES . "?file=" . @$domain["host"] . $element->src . "&scheme=" . @$domain["scheme"];
					} else {
						$element->src = SERVE_FILES . "?file=" . $_whmcs_url["url"] . "/" . $element->src . "&scheme=" . $_whmcs_url["scheme"];
					}
				}
			}
		}

		# Setting
		//foreach($html('form:not([action ^= "http://"])') as $x=>$element) {

		## Parsing forms and action urls
		foreach ( $html( 'form' ) as $x => $element ) {
			if ( $element->action ) {
				$domain_url = str_replace( "www.", "", parse_url( $element->action, PHP_URL_HOST ) );
				$whmcs_host = str_replace( "www.", "", parse_url( $this->get_whmcs_url(), PHP_URL_HOST ) );

				if ( $element->action == '?action=details' ) {
					if ( ! $this->is_permalink() ) {
						$element->action = $this->set_url( "clientarea.php?action=details", "" );
					} else {
						$element->action = "";
					}
				} else if ( ( $domain_url <> $whmcs_host && substr( $element->action, 0, 8 ) == "https://" ) || substr( $element->action, 0, 1 ) == "?" || $domain_url == "www.sandbox.paypal.com" || $domain_url == "sandbox.paypal.com" || $domain_url == "www.paypal.com" || $domain_url == "paypal.com" || $domain_url == "www.2checkout.com" || $domain_url == "2checkout.com" ) {
					// Do nothing
				} else if ( $element->action == "dl.php" ) {
					if ( $element->method && $element->method == "submit" ) {
						$element->method = "post";
					}
					$element->action = SERVE_FILES . "?file=" . $_whmcs_url["url"] . "/dl.php&scheme={$_whmcs_url["scheme"]}&" . substr( $element->action, 7 );
				} else if ( $element->action == "cart.php" && @$element->id == "frmDomainChecker" ) {
					$element->action = WHMP_CA_URL . "ajax.php?file=cart";
				} else {
					$element->action = $this->set_url( $this->get_current_url( $includeParameters ), ltrim( $element->action, "/" ) ); //."&whmp_blank";
				}

				$element->action = str_replace( "&amp;", "&", $element->action );
			}
		}

		if ( $remove_header_footer ) {
			foreach ( $html( "div#fb-root" ) as $element ) {
				$element->delete();
			}
			# Compatibility with WHMCS 6.2. Removing objexts with id footer
			foreach ( $html( "#footer" ) as $element ) {
				$element->delete();
			}

			/*if (strtolower(get_option("remove_powered_by")) == "yes") {
                foreach ($html("p:contains(Powered by)") as $element) {
                    $element->delete();
                }
            }*/

			/*if (strtolower(get_option("remove_copyright")) == "yes") {
                foreach ($html("div:contains(Copyright )") as $element) {
                    $element->delete();
                }
                foreach ($html("form[name=languagefrm]") as $element) {
                    $element->delete();
                }
            }*/
		}

		/*if ( count($html("div.container"))>0 ) {
            foreach($html("div.container") as $element) {
                $element->deleteAttribute("class");
            }
        }*/

		$configFile = WHMP_CA_PATH . "/includes/config/" . $this->get_whmcs_theme_name() . ".cnf";
		#NZ = remove name, EZ = remove content, NT = Name replace
		if ( is_file( $configFile ) ) {
			$configContent = file( $configFile );
			foreach ( $configContent as $line ) {
				$line = trim( $line );
				if ( substr( $line, 0, 2 ) <> "//" ) {
					$_parts = explode( "=", $line );
					$_parts = array_map( "trim", $_parts );
					if ( count( $_parts ) == "2" && count( $html( $_parts[0] ) ) > 0 ) {
						if ( $_parts[1] == "NZ" && substr( $_parts[0], 0, 1 ) == "." ) {
							$CClass = $html( $_parts[0], 0 )->class;
							$CClass = explode( " ", $CClass );
							for ( $x = 0; $x < count( $CClass ); $x ++ ) {
								if ( $CClass[ $x ] == substr( $_parts[0], 1 ) ) {
									unset( $CClass[ $x ] );
								}
							}
							$CClass                       = implode( " ", $CClass );
							$html( $_parts[0], 0 )->class = $CClass;
						} elseif ( $_parts[1] == "NZ" && substr( $_parts[0], 0, 1 ) == "#" ) {
							$html( $_parts[0], 0 )->id = str_replace( substr( $_parts[0], 1 ), "", $html( $_parts[0], 0 )->id );
						} elseif ( $_parts[1] == "EZ" ) {
							$html( $_parts[0], 0 )->delete();
						}
					}
				}
			}
		}

		$configContent = trim( get_option( 'whmp_config_data' ) );
		$configContent = explode( "\n", $configContent );
		foreach ( $configContent as $line ) {
			$line = trim( $line );
			if ( substr( $line, 0, 2 ) <> "//" ) {
				$_parts = explode( "=", $line );
				$_parts = array_map( "trim", $_parts );
				if ( count( $_parts ) == "2" && count( $html( $_parts[0] ) ) > 0 ) {
					if ( $_parts[1] == "NZ" && substr( $_parts[0], 0, 1 ) == "." ) {
						$CClass = $html( $_parts[0], 0 )->class;
						$CClass = explode( " ", $CClass );
						for ( $x = 0; $x < count( $CClass ); $x ++ ) {
							if ( $CClass[ $x ] == substr( $_parts[0], 1 ) ) {
								unset( $CClass[ $x ] );
							}
						}
						$CClass                       = implode( " ", $CClass );
						$html( $_parts[0], 0 )->class = $CClass;
					} elseif ( $_parts[1] == "NZ" && substr( $_parts[0], 0, 1 ) == "#" ) {
						$html( $_parts[0], 0 )->id = str_replace( substr( $_parts[0], 1 ), "", $html( $_parts[0], 0 )->id );
					} elseif ( $_parts[1] == "EZ" ) {
						$html( $_parts[0], 0 )->delete();
					}
				}
			}
		}
		if ( count( $html( "div#content_left" ) ) > 0 ) {
			/*$html = $html("div#content_left", 0)->getInnerText();*/
			$html = $html( "div#content_left", 0 )->toString( true, true, 1 );
		} else if ( count( $html( "body" ) ) > 0 ) {
			if ( $remove_header_footer ) {
				/*$html = $html("body", 0)->getInnerText();*/
				$html = $html( "body", 0 )->toString( true, true, 1 );
			} else {
				$html = $html->html();
			}
			#var_dump($html);
			#die($html);
		} else if ( gettype( $html ) == "object" ) {
			$html = $html->html();
		}

		//$html = $html->html();

		if ( $this->is_permalink() ) {
			$ca = $this->set_url( $this->get_current_url( $includeParameters ), "clientarea.php" ) . "?";
		} else {
			$ca = $this->set_url( $this->get_current_url( $includeParameters ), "clientarea.php" ) . "&";
		}

		$viewInvoiceLink  = "event, '" . $this->set_url( $this->get_current_url( $includeParameters ), "viewinvoice.php" );
		$viewInvoiceLink2 = "event, &#039;" . $this->set_url( $this->get_current_url( $includeParameters ), "viewinvoice.php" );

		if ( strpos( $viewInvoiceLink, "?" ) !== false ) {
			$viewInvoiceLink .= "&id=";
		} else {
			$viewInvoiceLink .= "?id=";
		}

		if ( strpos( $viewInvoiceLink2, "?" ) !== false ) {
			$viewInvoiceLink2 .= "&id=";
		} else {
			$viewInvoiceLink2 .= "?id=";
		}

		if ( $this->is_permalink() ) {
			$viewQuote = "'" . rtrim( $this->get_current_url( true ), "/" ) . "/viewquote/id/";
			$cartphp   = $this->get_current_url( true ) . "?whmpca=cart&";
		} else {
			$viewQuote = "'" . rtrim( $this->get_current_url( false ), "/" );
			if ( strpos( $viewQuote, "?" ) !== false ) {
				$viewQuote .= "&whmpca=viewquote&id=";
			} else {
				$viewQuote .= "?whmpca=viewquote&id=";
			}

			$cartphp = $this->get_current_url( false );
			if ( strpos( $cartphp, "?" ) !== false ) {
				$cartphp .= "&whmpca=cart&";
			} else {
				$cartphp .= "?whmpca=cart&";
			}
		}

		$With = array(//SERVE_FILES."?whmp_url=".$_whmcs_url["url"]."/cart.php&scheme=".$_whmcs_url["scheme"], // $this->set_url($this->get_current_url(true), "cart.php"),
		);

		$view_ticket_url = $this->set_url( $this->get_current_url( $includeParameters ), "viewticket.php" );

		$Fields = [
			'jQuery.post("cart.php'            => 'jQuery.post("' . WHMP_CA_URL . 'ajax.php?file=cart&',
			'language="javascript"'            => '',
			"<~root~>"                         => '',
			"</~root~>"                        => '',
			"clientarea.pphp"                  => "clientarea.php",
			"whmp_marzi"                       => 'name="whmp_name"',
			"'viewquote.php?id="               => $viewQuote,
			"&#039;viewquote.php?id="          => $viewQuote,
			"event, 'viewinvoice.php?id="      => $viewInvoiceLink,
			"event, &#039;viewinvoice.php?id=" => $viewInvoiceLink2,
			"window.location='/cart.php?"      => "window.location='" . $this->set_url( $this->get_current_url( $includeParameters ), "cart.php" ) . "?",
			"window.location='/cart.php'"      => "window.location='" . $this->set_url( $this->get_current_url( $includeParameters ), "cart.php" ) . "'",
			"window.location='cart.php'"       => "window.location='" . $this->set_url( $this->get_current_url( $includeParameters ), "cart.php" ) . "'",
			'window.location="/cart.php"'      => 'window.location="' . $this->set_url( $this->get_current_url( $includeParameters ), "cart.php" ) . '"',
			'window.location="cart.php"'       => 'window.location="' . $this->set_url( $this->get_current_url( $includeParameters ), "cart.php" ) . '"',
			'cart.php?'                        => $cartphp,
			'cart.php'                         => $this->get_current_url( true ) . "?whmpca=cart&",
			'whois.php?'                       => $this->get_current_url( true ) . "?whmpca=whois&",
			"http://www.google.com/recaptcha"  => "//www.google.com/recaptcha",
			"https://www.google.com/recaptcha" => "//www.google.com/recaptcha",
			'src="images'                      => 'src="' . $this->get_whmcs_url() . '/images',
			"name size"                        => "name=\"name\" size",
			"announcements.php"                => $this->set_url( $this->get_current_url( $includeParameters ), "announcements.php" ),
			'" name id'                        => '" name=\'name\' id',
			'$("'                              => 'jQuery("',
			'viewemail.php?'                   => SERVE_FILES . "?file={$_whmcs_url["url"]}/viewemail.php&scheme={$_whmcs_url["scheme"]}&",
			'"assets/img/loading.gif"'         => '"' . SERVE_FILES . "?file={$_whmcs_url["url"]}/assets/img/loading.gif&scheme={$_whmcs_url["scheme"]}" . '"',
			'submitticket.php'                 => SERVE_FILES . "?file=" . $_whmcs_url["url"] . "/submitticket.php&scheme=" . $_whmcs_url["scheme"],
			"supporttickets.php"               => $this->set_url( $this->get_current_url( $includeParameters ), "supporttickets.php" ),

			"/viewticket.php?tid=" => $view_ticket_url . ( $this->is_permalink() ? "?tid=" : "&tld=" ),
			"viewticket.php?tid="  => $view_ticket_url . ( $this->is_permalink() ? "?tid=" : "&tid=" ),
			"/viewticket.php"      => $view_ticket_url,
			"viewticket.php"       => $view_ticket_url,

			"clientarea.php?"         => $ca,
			"clientarea.php"          => $this->set_url( $this->get_current_url( $includeParameters ), "clientarea.php" ),
			"popupWindow('whois.php?" => "popupWindow('" . SERVE_FILES . "?whmp_url=" . $_whmcs_url["url"] . "/whois.php&scheme={$_whmcs_url["scheme"]}" . "&",
			"&#039;"                  => "'",
			"'?tid="                  => "'" . $view_ticket_url . ( $this->is_permalink() ? "?tid=" : "&tid=" ),

			//'jQuery("#prodconfigcontainer").html(data);' => 'jQuery("#prodconfigcontainer").html(data);'
			//'function(data){' => 'function(data){ alert(data);'
		];
		$html   = str_replace( array_keys( $Fields ), array_values( $Fields ), $html );

		$found = preg_match_all( "/ value=\"(.*?)\"/im", $html, $matches );
		if ( is_numeric( $found ) && $found > 0 ) {
			$matches2 = [];
			foreach ( $matches[1] as $string ) {
				$s = str_replace( "/", "\/", $this->get_whmcs_url() );
				if ( preg_match( "/^{$s}/i", $string ) ) {
					//$string2 = str_replace($this->get_whmcs_url(), "", $string);
					//echo $string2."\n";
					if ( substr( basename( strtolower( $string ) ), 0, 15 ) == "viewinvoice.php" ) {
						#$uparts = parse_url($string);
						#$string2 = SERVE_FILES."?file=".$this->get_whmcs_url()."/viewinvoice.php&".$uparts["query"];
						$string2 = $this->set_url( $this->get_current_url( $includeParameters ), $string );
					} elseif ( basename( strtolower( $string ) ) == "paypal.php" ) {
						$s       = $this->url_scheme_part( $string );
						$string2 = SERVE_FILES . "?file=" . $s["url"] . "&scheme=" . $s["scheme"];
					} else {
						$string2 = $string;
					}

					$html = str_replace( ' value="' . $string . '"', ' value="' . $string2 . '"', $html );
				}
			}
		}

		// Replace from config file
		#NZ = remove name, EZ = remove content, NT = Name replace
		if ( is_file( $configFile ) ) {
			$configContent = file( $configFile );
			foreach ( $configContent as $line ) {
				$line = trim( $line );
				if ( substr( $line, 0, 2 ) <> "//" ) {
					$_parts = explode( "=", $line );
					$_parts = array_map( "trim", $_parts );
					if ( count( $_parts ) == "3" ) {
						if ( $_parts[1] == "NT" ) {
							$html = str_replace( $_parts[0], $_parts[2], $html );
						}
					}
				}
			}
		}
		$configContent = explode( "\n", get_option( "whmp_config_data" ) );
		foreach ( $configContent as $line ) {
			$line = trim( $line );
			if ( substr( $line, 0, 2 ) <> "//" ) {
				$_parts = explode( "=", $line );
				$_parts = array_map( "trim", $_parts );
				if ( count( $_parts ) == "3" ) {
					if ( $_parts[1] == "NT" ) {
						$html = str_replace( $_parts[0], $_parts[2], $html );
					}
				}
			}
		}

		$css = "";

		if ( strtolower( get_option( "use_whmcs_css_files" ) ) <> "no" ) {
			foreach ( $this->css_uris as $uri ) {
				if ( strtolower( basename( $uri ) ) == "font-awesome.min.css" || strtolower( basename( $uri ) ) == "font-awesome.css" ) {
					$css .= '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">' . "\n";
					#wp_enqueue_style( "whmp_fontawesome", "https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css", array());
				} else {
					$css .= '<link rel="stylesheet" href="' . $uri . '">' . "\n";
					#$numb = rand();
					#wp_enqueue_style( 'whmp_'. $numb, $uri, array());
				}
			}

			#if ($remove_header_footer)
			#    $html = $css.$html;
		}

		if ( is_file( WHMP_CA_PATH . "/themes/" . $this->get_wordpress_theme_folder_name() . ".css" ) ) {
			$css .= '<link rel="stylesheet" href="' . WHMP_CA_URL . "themes/" . $this->get_wordpress_theme_folder_name() . ".css" . '">' . "\n";
			#wp_enqueue_style( 'whmp_'.rand(), WHMP_CA_URL."themes/".$this->get_wordpress_theme_folder_name().".css");
		}

		$css .= "\n<style>\n .whmp .container {max-width: 100%;}";
		if ( strtolower( get_option( "remove_whmcs_logo" ) ) == "yes" ) {
			$css .= ".whmp #header .container > a {display: none}";
		}
		if ( strtolower( get_option( "remove_whmcs_menu" ) ) == "yes" ) {
			$css .= ".whmp #main-menu {display: none}";
		}
		if ( strtolower( get_option( "remove_breadcrumb" ) ) == "yes" ) {
			$css .= ".whmp .breadcrumb {display: none}";
		}
		if ( strtolower( get_option( "whmp_hide_currency_select" ) ) == "yes" ) {
			$css .= ".whmp #order-standard_cart [menuitemname=\"Choose Currency\"] { display: none;}";
		}
		if ( strtolower( get_option( "whmp_remove_top_bar" ) ) == "yes" ) {
			$css .= ".whmp #header {display: none}";
		}
		$css .= "</style>\n";

		if ( strtolower( get_option( "use_whmcs_css_files" ) ) <> "no" ) {
			$css .= "\n<style>\n" . get_option( "whmpca_custom_css" ) . "\n</style>\n";
		}

		$html = $css . $html;

		if ( strtolower( get_option( "load_dropdown" ) ) == "yes" ) {
			$html .= '
            <script>
            jQuery(document).ready(function() {
                jQuery(".dropdown-toggle").dropdown();
            });
            </script>
            ';
		}

		if ( function_exists( 'mb_detect_encoding' ) ) {
			$encoding = mb_detect_encoding( $html );
			if ( $encoding === false ) {
				$html = iconv( "Windows-1252", "UTF-8", $html );
			} elseif ( $encoding <> "UTF-8" ) {
				$html = iconv( $encoding, "UTF-8", $html );
			}
		}

		/*$text_muted = $html(".text-muted",0)->getInnerText();
        $charsets = array(
                "UTF-8",
                "ASCII",
                "Windows-1252",
                "ISO-8859-15",
                "ISO-8859-1",
                "ISO-8859-6",
                "CP1256"
                );

        foreach ($charsets as $ch1) {
            foreach ($charsets as $ch2){
                echo "<h3>Combination $ch1 to $ch2 produces: ".iconv($ch1, $ch2, $text_muted)."</h3>";
            }
        }*/

		$scripts          = "";
		$scripts_before   = "";
		$donot_include_js = [ "jquery.js", "jquery.min.js" ];

		$before_scripts = [ "icheck.js", "base.js", "checkout.js", "ion.rangeSlider.min.js" ];

		if ( is_array( $this->js_uris ) && count( $this->js_uris ) > 0 ) {
			foreach ( $this->js_uris as $x => $js ) {
				$f = basename( $js );
				if ( strpos( $f, "&" ) !== false ) {
					$f = explode( "&", $f );
					$f = $f[0];
				}
				if ( ! in_array( $f, $donot_include_js ) ) {
					if ( in_array( $f, $before_scripts ) ) {
						//$scripts_before .= "<script type='text/javascript' src='$js'></script>\n";
						wp_enqueue_script( basename( $js ), $js, [ 'jquery' ], false, false );
					} else {
						//$scripts .= "<script type='text/javascript' src='$js'></script>\n";
						wp_enqueue_script( basename( $js ), $js, [ 'jquery' ], false, true );
					}
				}
			}
		}
		if ( count( $js_in_head ) > 0 ) {
			foreach ( $js_in_head as $k => $js ) {
				$f = basename( $js );
				if ( strpos( $f, "&" ) !== false ) {
					$f = explode( "&", $f );
					$f = $f[0];
				}
				if ( in_array( $f, $donot_include_js ) ) {
					unset( $js_in_head[ $k ] );
				}
			}
		}

		if ( $omit ) {
			$omit1_pos = strpos( $html, $omit1_string );
			if ( $omit2_pos !== false ) {
				$html = substr_replace( $html, "<!--omitted-start-->" . $omit_html . "<!--omitted-end-->", $omit1_pos + strlen( $omit1_string ), 0 );
			}
		}

		foreach ( $js_in_head as $src ) {
			$scripts_before .= "\n" . '<script src="' . $src . '"></script>';
		}

		return $scripts_before . $html . $scripts;
	}

	function parse_scripts( $html ) {
		require_once WHMP_CA_PATH . "/includes/ganon/ganon.php";

		$html = str_get_dom( $html );

		$Output1 = "";

		$Fields  = [
			'src="templates'                  => 'src="' . $this->get_whmcs_url() . '/templates',
			'src="images'                     => 'src="' . $this->get_whmcs_url() . '/images',
			'type="text/css" href="templates' => 'type="text/css" href="' . $this->get_whmcs_url() . '/templates',
			'post("'                          => 'post("' . $this->get_whmcs_url() . "/",
			"$('"                             => "jQuery('",
		];
		$Output1 = str_replace( array_keys( $Fields ), array_values( $Fields ), $Output1 );

		return $Output1;
	}

	/**
	 * @param      $url
	 * @param      $url2
	 * @param bool $debug
	 *
	 * @return string
	 */
	function set_url( $url, $url2, $debug = false ) {
		//$url2 = html_entity_decode($url2);
		//$this->debug($url." (=) ".$url2);
		$query = [];

		$ar = parse_url( $url );
		//$url = preg_replace('/\?.*/', '', $url);
		if ( isset( $ar["query"] ) ) {
			parse_str( $ar["query"], $query );
		}
		$ar = parse_url( $url2 );
		if ( isset( $ar["query"] ) ) {
			parse_str( $ar["query"], $query2 );
			foreach ( $query2 as $k => $v ) {
				$query[ $k ] = $v;
			}
		}

		if ( isset( $ar["path"] ) ) {
			if ( substr( basename( $ar["path"] ), "-3" ) == ".js" ) {
				$query["whmpca"] = "js";
				//$query["js"] = html_entity_decode($ar["path"]);
				$query["js"]   = $ar["path"];
				$query["ajax"] = "2";
			} elseif ( strtolower( substr( basename( $ar["path"] ), "-4" ) ) == ".png" ) {
				$query["whmpca"] = "png";
				$query["png"]    = $ar["path"];
			} elseif ( strtolower( substr( basename( $ar["path"] ), "-4" ) ) == ".jpg" ) {
				$query["whmpca"] = "jpg";
				$query["jpg"]    = $ar["path"];
			} elseif ( strtolower( substr( basename( $ar["path"] ), "-4" ) ) == ".gif" ) {
				$query["whmpca"] = "gif";
				$query["gif"]    = $ar["path"];
			} elseif ( substr( basename( $ar["path"] ), "-4" ) == ".css" ) {
				$query["whmpca"] = "css";
				$query["css"]    = $ar["path"];
			} else {
				$query["whmpca"] = basename( $ar["path"], ".php" );
			}
		} else {
			$query["whmpca"] = "index";
		}

		foreach ( $query as $key => $val ) {
			if ( substr( $key, 0, 4 ) == "amp;" ) {
				$query = $this->change_key( $query, $key, substr( $key, 4 ) );
			}
		}
		if ( isset( $query["page"] ) ) {
			$query = $this->change_key( $query, "page", "whmppage" );
		}
		if ( isset( $query["amp;page"] ) ) {
			$query = $this->change_key( $query, "amp;page", "whmppage" );
		}
		if ( $this->is_permalink() ) {
			$whmpca = $query["whmpca"];
			unset( $query["whmpca"] );

			if ( count( $query ) > 0 ) {
				//$query = "?".$this->my_build_query($query);
				$out = "";
				foreach ( $query as $k => $v ) {
					$out .= $k . "/" . $v . "/";
				}
				$query = $out;
			} else {
				$query = "";
			}
			$url = rtrim( $url, "/" );

			return $url . "/{$whmpca}/" . $query;
		}
		if ( $this->is_permalink() ) {
			$url = rtrim( $url, "/" ) . "/";
			foreach ( $query as $k => $v ) {
				$url .= $k . "/" . $v . "/";
			}

			return $url;
		} else {
			$query = $this->my_build_query( $query );

			if ( strpos( $url, "?" ) !== false ) {
				return $url . "&" . $query;
			} else {
				return $url . "?" . $query;
			}
		}
	}

	function change_key( $array, $old_key, $new_key ) {
		if ( ! array_key_exists( $old_key, $array ) ) {
			return $array;
		}

		$keys                                    = array_keys( $array );
		$keys[ array_search( $old_key, $keys ) ] = $new_key;

		return array_combine( $keys, $array );
	}

	function my_build_query( $query ) {
		if ( ! is_array( $query ) ) {
			return $query;
		} else {
			if ( isset( $query["whmpca"] ) ) {
				$whmpca = $query["whmpca"];
				unset( $query["whmpca"] );
			} else {
				$whmpca = "";
			}
			$output = "&";
			foreach ( $query as $k => $v ) {
				$output .= ( $k . "=" . $v . "&" );
			}
			if ( $whmpca <> "" ) {
				$output = "&whmpca=" . $whmpca . $output;
			}
			$output = trim( $output, "&" );

			return $output;
		}
	}

	function debug( $string ) {
		if ( $this->is_developer_machine() ) {
			if ( is_array( $string ) || is_object( $string ) ) {
				file_put_contents( 'd:/logs.txt', print_r( $string, true ), FILE_APPEND );
			} else {
				file_put_contents( 'd:/logs.txt', $string . "\n", FILE_APPEND );
			}
		}
	}

	// This function will return page ID, containing data
	function get_whmp_page_id() {
		//settings_fields( 'whmp_client_area' );
		$whmp_page_id = get_option( "whmp_page_id" );

		$create_new_page = false;

		if ( is_numeric( $whmp_page_id ) && $whmp_page_id > 0 ) {
			$query      = '';
			$pages      = get_pages( [
				'post_type'   => 'page',
				'post_status' => 'publish',
			] );
			$page_found = false;
			foreach ( $pages as $p ) {
				if ( $p->ID == $whmp_page_id ) {
					$page_found = true;
					break;
				}
			}
			if ( ! $page_found ) {
				$create_new_page = true;
			}
		} else {
			$create_new_page = true;
		}

		$create_new_page = false;
		if ( $create_new_page ) {
			$whmp_page_data                   = [];
			$whmp_page_data['post_title']     = "Client Area";
			$whmp_page_data['post_content']   = '[whmpress_client_area]';
			$whmp_page_data['post_status']    = 'publish';
			$whmp_page_data['post_author']    = 1;
			$whmp_page_data['post_type']      = 'page';
			$whmp_page_data['menu_order']     = 100;
			$whmp_page_data['comment_status'] = 'closed';
			$whmp_page_id                     = wp_insert_post( $whmp_page_data );
			add_post_meta( $whmp_page_id, 'whmp_client_area_page', "yes" );
			update_option( "whmp_page_id", $whmp_page_id );
		} else {
			return "0";
		}

		//$this->debug($whmp_page_id);
		return $whmp_page_id;
	}

	function parse_css_file( $css, $url, $add_whmp = false ) {
		set_time_limit( 0 );

		include_once "url_to_absolute.php";
		preg_match_all( '/url\((.*?)\)/is', $css, $matches );

		$FoundImages = [];
		foreach ( $matches[1] as $img ) {
			$FoundImages[] = $img;
		}
		$FoundImages = array_unique( $FoundImages );
		foreach ( $FoundImages as $file ) {
			$file1 = str_replace( "'", "", $file );
			$file1 = str_replace( '"', "", $file1 );
			if ( substr( $file1, 0, 2 ) == "//" || substr( $file1, 0, 7 ) == "http://" || substr( $file1, 0, 8 ) == "https://" ) {
				// Do nothing
			} else {
				$file    = str_replace( "'", '', $file );
				$file    = str_replace( '"', '', $file );
				$css_url = $this->url_scheme_part( url_to_absolute( dirname( $url ) . "/", $file ) );
				$css     = str_replace( $file, SERVE_FILES . "?file=" . $css_url["url"] . "&scheme=" . $css_url["scheme"], $css );
			}
		}

		if ( $add_whmp ) {
			#$path = WHMP_CA_PATH.'\Sabberworm\CSS\Settings';
			#$path = str_replace("/", "\\", $path);
			$oSettings = Sabberworm\CSS\Settings::create()->withMultibyteSupport( false );
			$oParser   = new Sabberworm\CSS\Parser( $css, $oSettings );

			$oDoc = $oParser->parse();

			$myClass = ".whmp";
			foreach ( $oDoc->getAllDeclarationBlocks() as $oBlock ) {
				foreach ( $oBlock->getSelectors() as $oSelector ) {
					if ( $oSelector->getSelector() == "html" || $oSelector->getSelector() == "body" ) {
						$oSelector->setSelector( $oSelector->getSelector() . $myClass );
					} else {
						$oSelector->setSelector( $myClass . ' ' . $oSelector->getSelector() );
					}
				}
			}

			$css = $oDoc->render();
		}

		return str_replace( "\\\\", "\\", $css );
	}

	function show_array( $ar ) {
		echo "<pre>";
		print_r( $ar );
		echo "</pre>";
	}

	function is_permalink() {
		if ( strtolower( get_option( "whmp_use_permalinks" ) ) <> "yes" ) {
			return false;
		}
		if ( get_option( 'permalink_structure' ) ) {
			return true;
		} else {
			return false;
		}
	}

	function write_php_ini( $array, $file ) {
		$res = [];
		foreach ( $array as $key => $val ) {
			if ( is_array( $val ) ) {
				$res[] = "[$key]";
				foreach ( $val as $skey => $sval ) {
					$res[] = "$skey = " . ( is_numeric( $sval ) ? $sval : '"' . $sval . '"' );
				}
			} else {
				$res[] = "$key = " . ( is_numeric( $val ) ? $val : '"' . $val . '"' );
			}
		}
		$this->safefilerewrite( $file, implode( "\r\n", $res ) );
	}

	//////
	function safefilerewrite( $fileName, $dataToSave ) {
		if ( $fp = fopen( $fileName, 'w' ) ) {
			$startTime = microtime();
			do {
				$canWrite = flock( $fp, LOCK_EX );
				// If lock not obtained sleep for 0 - 100 milliseconds, to avoid collision and CPU load
				if ( ! $canWrite ) {
					usleep( round( rand( 0, 100 ) * 1000 ) );
				}
			} while ( ( ! $canWrite ) and ( ( microtime() - $startTime ) < 1000 ) );

			//file was locked so now we can store information
			if ( $canWrite ) {
				fwrite( $fp, $dataToSave );
				flock( $fp, LOCK_UN );
			}
			fclose( $fp );
		}
	}

	function get_wordpress_theme_folder_name() {
		return basename( get_stylesheet_directory() );
	}

	function get_my_version() {
		$data = get_plugin_data( WHMP_FILE_PATH );

		return $data["Version"];
	}

	function get_all_pages() {
		global $wpdb;
		$Q    = "SELECT `post_title`, `ID` FROM `" . $wpdb->prefix . "posts` WHERE `post_status`='publish' AND `post_type`='page' ORDER BY `post_title`";
		$rows = $wpdb->get_results( $Q, ARRAY_A );

		return $rows;
	}

	/**
	 * @return bool|string
	 *
	 * Returns current selected langauge..
	 * Compatible with WPML (https://wpml.org/)
	 * Compatible with PolyLang language plugin (https://wordpress.org/plugins/polylang/)
	 */
	function get_current_language() {
		if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
			return ICL_LANGUAGE_CODE;
		} elseif ( function_exists( 'pll_current_language' ) ) {
			return pll_current_language();
		} else {
			return get_locale();
		}
	}

	/**
	 * @return bool
	 *
	 * Check if multi language plugins are enabled.
	 */
	function is_multi_language() {
		if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
			return true;
		} elseif ( function_exists( 'pll_current_language' ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * @param $css_content
	 *
	 * @return mixed
	 *
	 * This method converts CSS into minified CSS.
	 */
	function minify_css( $css_content ) {
		// Remove comments
		$css_content = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css_content );

		// Remove space after colons
		$css_content = str_replace( ': ', ':', $css_content );

		// Remove whitespace
		$css_content = str_replace( [ "\r\n", "\r", "\n", "\t", '  ', '    ', '    ' ], '', $css_content );

		return $css_content;
	}

	/**
	 * @param $password
	 * @param $hash
	 *
	 * @return bool
	 *
	 * Return true/false that hash matches with provided password.
	 */
	/*function password_hash_matches($password, $hash)
    {
        if (version_compare(PHP_VERSION, '5.3.7', '<')) {
            return md5($password) == $hash;
        } else {
            if (function_exists('password_verify')) {
                return password_verify($password, $hash);
            }

            $new_hash = crypt($password, $hash);
            return $hash == $new_hash;
        }
    }*/

	function get_whmcs_user( $email ) {
		if ( ! is_email( $email ) ) {
			$user = get_user_by( "login", $email );
			if ( ! $user ) {
				return __( "WHMCS user must be an email", "whmpress" );
			}
			$email = $user->data->user_email;
		}
		$jsonData = $this->execute_whmcs_api( "getclientsdetails", [
			'email' => $email,
			'stats' => true,
		] );

		if ( ! $jsonData['result'] ) {
			return __( "Can't get users from WHMCS", "whmpress" );
		}
		if ( $jsonData['result'] <> "success" ) {
			if ( isset( $jsonData['message'] ) ) {
				return __( $jsonData['message'], "whmpress" );
			} else {
				return __( "Can't get valid data from WHMCS server.", "whmpress" );
			}
		}
		if ( isset( $jsonData['result'] ) && $jsonData['result'] == 'success' ) {
			return $jsonData['client'];
		} else {
			if ( isset( $jsonData['message'] ) ) {
				return $jsonData['message'];
			} else {
				return __( "WHMCS user not found.", "whmpress" );
			}
		}
	}

	/**
	 * @return mixed|string|void
	 *
	 * This function will return provided WHMCS url.
	 */
	function get_whmcs_url() {
		if ( $this->is_whmpress_activated() ) {
			$whmcsUrl = get_option( "whmcs_url" );
			if ( empty( $whmcsUrl ) ) {
				global $wpdb;
				$Q  = "SELECT `value` FROM `" . whmp_get_configuration_table_name() . "` WHERE `setting`='SystemURL' OR `setting`='SystemSSLURL' ORDER BY `setting` DESC";
				$Us = $wpdb->get_results( $Q, ARRAY_A );
				foreach ( $Us as $U ) {
					if ( whmp_get_installation_url() == $U["value"] ) {
						$whmcsUrl = $U["value"];
					}
				}
				if ( empty( $whmcsUrl ) ) {
					$whmcsUrl = whmp_get_installation_url();
				}
			}
		} else {
			$whmcsUrl = get_option( "whmcs_main_url" );
			if ( empty( $whmcsUrl ) ) {
				$whmcsUrl = get_option( "whmcs_url" );
			}
		}

		$whmcsUrl = trim( $whmcsUrl, "/" ) . "/";

		return $whmcsUrl;
	}

	function execute_whmcs_api( $action, $postfields ) {
		$whmcsUrl = $this->get_whmcs_url();

		$username = get_option( "whmcs_sso_admin_user" );
		$password = get_option( "whmcs_sso_admin_pass" );

		$whmcsUrl = rtrim( $whmcsUrl, "/" ) . "/";

		$postfields['username']     = $username;
		$postfields['password']     = md5( $password );
		$postfields['action']       = $action;
		$postfields['responsetype'] = 'json';
		if ( $this->is_developer_machine() ) {
			$postfields['accesskey'] = 'Farash..88';
		}

		// Call the API
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $whmcsUrl . 'includes/api.php' );
		curl_setopt( $ch, CURLOPT_POST, 1 );
		curl_setopt( $ch, CURLOPT_TIMEOUT, 30 );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $postfields ) );
		$response = curl_exec( $ch );

		if ( curl_error( $ch ) ) {
			return ( 'Unable to connect: ' . curl_errno( $ch ) . ' - ' . curl_error( $ch ) );
		}
		curl_close( $ch );
		$jsonData = json_decode( $response, true );

		return $jsonData;
	}

	function get_whmcs_users( $total_records = 9999 ) {
		$jsonData = $this->execute_whmcs_api( "GetClients", [
			'limitnum' => $total_records,
		] );

		if ( ! $jsonData['result'] ) {
			return __( "Can't get users from WHMCS", "whmpress" );
		}
		if ( $jsonData['result'] <> "success" ) {
			return __( "Can't get valid data from WHMCS server.", "whmpress" );
		}

		return $jsonData;
	}

	function get_whmcs_password_hash( $email ) {
		if ( ! is_email( $email ) ) {
			$user = get_user_by( "login", $email );
			if ( ! $user ) {
				return __( "Please provide valid email address", "whmpress" );
			}
			$email = $user->data->user_email;
		}

		$jsonData = $this->execute_whmcs_api( "getclientpassword", [
			'email' => $email,
		] );

		if ( isset( $jsonData['result'] ) && $jsonData['result'] == "success" ) {
			return $jsonData['password'];
		}

		return false;
	}

	/**
	 * @param $username
	 *
	 * @return bool
	 *
	 * Check if WHMCS user exists.
	 */
	function is_whmcs_user( $username ) {
		if ( ! is_email( $username ) ) {
			$user     = get_user_by( "login", $username );
			$username = $user->data->user_email;
		}
		$response = $this->get_whmcs_password_hash( $username );
		if ( $response === false || $response == __( "Please provide valid email address", "whmpress" ) ) {
			return false;
		} else {
			return true;
		}
	}

	function whmcs_user_logout() {
		$url      = $this->get_whmcs_url() . "logout.php";
		$response = $this->read_remote_url( $url, [], [], false );
	}

	function authenticate_whmcs_user( $w_username, $w_password ) {
		if ( ! is_email( $w_username ) ) {
			$user = get_user_by( "login", $w_username );
			if ( ! $user ) {
				return __( "Please provide valid email address", "whmpress" );
			}
			$w_username = $user->data->user_email;
		}

		$jsonData = $this->execute_whmcs_api( "validatelogin", [
			'email'     => $w_username,
			'password2' => $w_password,
		] );

		if ( isset( $jsonData['result'] ) && $jsonData['result'] == "success" && isset( $jsonData['userid'] ) ) {
			return "OK";
		} else {
			if ( isset( $jsonData["message"] ) ) {
				return $jsonData["message"];
			} else {
				return __( "Can't get username/password data from WHMCS", "whmpress" );
			}
		}
	}

	function add_whmcs_user( $data ) {
		$response = $this->execute_whmcs_api( "addclient", $data );
		if ( isset( $response["result"] ) && $response["result"] == "success" ) {
			return "OK" . $response["clientid"];
		} else {
			return "WHMCS: " . @$response["message"];
		}
	}

	function update_whmcs_user_password( $user_email, $new_password, $data = [] ) {
		$data["clientemail"] = $user_email;
		$data["password2"]   = $new_password;
		$response            = $this->execute_whmcs_api( "updateclient", $data );
		if ( $response['result'] && $response['result'] == "success" ) {
			return "OK";
		} else {
			return $response["message"];
		}
	}

	function is_admin_user_valid( $username, $password ) {
		$whmcsUrl = $this->get_whmcs_url();

		$whmcsUrl = rtrim( $whmcsUrl, "/" ) . "/";

		$postfields['username']     = $username;
		$postfields['password']     = md5( $password );
		$postfields['action']       = 'getadmindetails';
		$postfields['responsetype'] = 'json';
		if ( $this->is_developer_machine() ) {
			$postfields['accesskey'] = 'Farash..88';
		}

		// Call the API
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $whmcsUrl . 'includes/api.php' );
		curl_setopt( $ch, CURLOPT_POST, 1 );
		curl_setopt( $ch, CURLOPT_TIMEOUT, 30 );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $postfields ) );
		$response = curl_exec( $ch );

		if ( curl_error( $ch ) ) {
			return ( 'Unable to connect: ' . curl_errno( $ch ) . ' - ' . curl_error( $ch ) );
		}
		curl_close( $ch );
		$jsonData = json_decode( $response, true );

		if ( isset( $jsonData['result'] ) && $jsonData['result'] == 'error' ) {
			return $jsonData['message'];
		} else {
			return "OK";
		}
	}

	function get_ip() {
		foreach (
			[
				'HTTP_CLIENT_IP',
				'HTTP_X_FORWARDED_FOR',
				'HTTP_X_FORWARDED',
				'HTTP_X_CLUSTER_CLIENT_IP',
				'HTTP_FORWARDED_FOR',
				'HTTP_FORWARDED',
				'REMOTE_ADDR',
			] as $key
		) {
			if ( array_key_exists( $key, $_SERVER ) === true ) {
				foreach ( explode( ',', $_SERVER[ $key ] ) as $ip ) {
					if ( filter_var( $ip, FILTER_VALIDATE_IP ) !== false ) {
						return $ip;
					}
				}
			}
		}

		return "";
	}

	function get_country_code( $country ) {
		$country = strtoupper( trim( $country ) );

		if ( $country == "PAK" ) {
			return "PK";
		}
		if ( $country == "UAE" ) {
			return "AE";
		}
		if ( $country == "USA" ) {
			return "US";
		}
		if ( $country == "United States" ) {
			return "US";
		}
		if ( $country == "Hindustan" ) {
			return "IN";
		}

		$country_list = [
			'AF' => 'Afghanistan',
			'AX' => 'Aland Islands',
			'AL' => 'Albania',
			'DZ' => 'Algeria',
			'AS' => 'American Samoa',
			'AD' => 'Andorra',
			'AO' => 'Angola',
			'AI' => 'Anguilla',
			'AQ' => 'Antarctica',
			'AG' => 'Antigua and Barbuda',
			'AR' => 'Argentina',
			'AM' => 'Armenia',
			'AW' => 'Aruba',
			'AU' => 'Australia',
			'AT' => 'Austria',
			'AZ' => 'Azerbaijan',
			'BS' => 'Bahamas the',
			'BH' => 'Bahrain',
			'BD' => 'Bangladesh',
			'BB' => 'Barbados',
			'BY' => 'Belarus',
			'BE' => 'Belgium',
			'BZ' => 'Belize',
			'BJ' => 'Benin',
			'BM' => 'Bermuda',
			'BT' => 'Bhutan',
			'BO' => 'Bolivia',
			'BA' => 'Bosnia and Herzegovina',
			'BW' => 'Botswana',
			'BV' => 'Bouvet Island (Bouvetoya)',
			'BR' => 'Brazil',
			'IO' => 'British Indian Ocean Territory (Chagos Archipelago)',
			'VG' => 'British Virgin Islands',
			'BN' => 'Brunei Darussalam',
			'BG' => 'Bulgaria',
			'BF' => 'Burkina Faso',
			'BI' => 'Burundi',
			'KH' => 'Cambodia',
			'CM' => 'Cameroon',
			'CA' => 'Canada',
			'CV' => 'Cape Verde',
			'KY' => 'Cayman Islands',
			'CF' => 'Central African Republic',
			'TD' => 'Chad',
			'CL' => 'Chile',
			'CN' => 'China',
			'CX' => 'Christmas Island',
			'CC' => 'Cocos (Keeling) Islands',
			'CO' => 'Colombia',
			'KM' => 'Comoros the',
			'CD' => 'Congo',
			'CG' => 'Congo the',
			'CK' => 'Cook Islands',
			'CR' => 'Costa Rica',
			'CI' => 'Cote d\'Ivoire',
			'HR' => 'Croatia',
			'CU' => 'Cuba',
			'CY' => 'Cyprus',
			'CZ' => 'Czech Republic',
			'DK' => 'Denmark',
			'DJ' => 'Djibouti',
			'DM' => 'Dominica',
			'DO' => 'Dominican Republic',
			'EC' => 'Ecuador',
			'EG' => 'Egypt',
			'SV' => 'El Salvador',
			'GQ' => 'Equatorial Guinea',
			'ER' => 'Eritrea',
			'EE' => 'Estonia',
			'ET' => 'Ethiopia',
			'FO' => 'Faroe Islands',
			'FK' => 'Falkland Islands (Malvinas)',
			'FJ' => 'Fiji the Fiji Islands',
			'FI' => 'Finland',
			'FR' => 'France, French Republic',
			'GF' => 'French Guiana',
			'PF' => 'French Polynesia',
			'TF' => 'French Southern Territories',
			'GA' => 'Gabon',
			'GM' => 'Gambia the',
			'GE' => 'Georgia',
			'DE' => 'Germany',
			'GH' => 'Ghana',
			'GI' => 'Gibraltar',
			'GR' => 'Greece',
			'GL' => 'Greenland',
			'GD' => 'Grenada',
			'GP' => 'Guadeloupe',
			'GU' => 'Guam',
			'GT' => 'Guatemala',
			'GG' => 'Guernsey',
			'GN' => 'Guinea',
			'GW' => 'Guinea-Bissau',
			'GY' => 'Guyana',
			'HT' => 'Haiti',
			'HM' => 'Heard Island and McDonald Islands',
			'VA' => 'Holy See (Vatican City State)',
			'HN' => 'Honduras',
			'HK' => 'Hong Kong',
			'HU' => 'Hungary',
			'IS' => 'Iceland',
			'IN' => 'India',
			'ID' => 'Indonesia',
			'IR' => 'Iran',
			'IQ' => 'Iraq',
			'IE' => 'Ireland',
			'IM' => 'Isle of Man',
			'IL' => 'Israel',
			'IT' => 'Italy',
			'JM' => 'Jamaica',
			'JP' => 'Japan',
			'JE' => 'Jersey',
			'JO' => 'Jordan',
			'KZ' => 'Kazakhstan',
			'KE' => 'Kenya',
			'KI' => 'Kiribati',
			'KP' => 'Korea',
			'KR' => 'Korea',
			'KW' => 'Kuwait',
			'KG' => 'Kyrgyz Republic',
			'LA' => 'Lao',
			'LV' => 'Latvia',
			'LB' => 'Lebanon',
			'LS' => 'Lesotho',
			'LR' => 'Liberia',
			'LY' => 'Libyan Arab Jamahiriya',
			'LI' => 'Liechtenstein',
			'LT' => 'Lithuania',
			'LU' => 'Luxembourg',
			'MO' => 'Macao',
			'MK' => 'Macedonia',
			'MG' => 'Madagascar',
			'MW' => 'Malawi',
			'MY' => 'Malaysia',
			'MV' => 'Maldives',
			'ML' => 'Mali',
			'MT' => 'Malta',
			'MH' => 'Marshall Islands',
			'MQ' => 'Martinique',
			'MR' => 'Mauritania',
			'MU' => 'Mauritius',
			'YT' => 'Mayotte',
			'MX' => 'Mexico',
			'FM' => 'Micronesia',
			'MD' => 'Moldova',
			'MC' => 'Monaco',
			'MN' => 'Mongolia',
			'ME' => 'Montenegro',
			'MS' => 'Montserrat',
			'MA' => 'Morocco',
			'MZ' => 'Mozambique',
			'MM' => 'Myanmar',
			'NA' => 'Namibia',
			'NR' => 'Nauru',
			'NP' => 'Nepal',
			'AN' => 'Netherlands Antilles',
			'NL' => 'Netherlands the',
			'NC' => 'New Caledonia',
			'NZ' => 'New Zealand',
			'NI' => 'Nicaragua',
			'NE' => 'Niger',
			'NG' => 'Nigeria',
			'NU' => 'Niue',
			'NF' => 'Norfolk Island',
			'MP' => 'Northern Mariana Islands',
			'NO' => 'Norway',
			'OM' => 'Oman',
			'PK' => 'Pakistan',
			'PW' => 'Palau',
			'PS' => 'Palestinian Territory',
			'PA' => 'Panama',
			'PG' => 'Papua New Guinea',
			'PY' => 'Paraguay',
			'PE' => 'Peru',
			'PH' => 'Philippines',
			'PN' => 'Pitcairn Islands',
			'PL' => 'Poland',
			'PT' => 'Portugal, Portuguese Republic',
			'PR' => 'Puerto Rico',
			'QA' => 'Qatar',
			'RE' => 'Reunion',
			'RO' => 'Romania',
			'RU' => 'Russian Federation',
			'RW' => 'Rwanda',
			'BL' => 'Saint Barthelemy',
			'SH' => 'Saint Helena',
			'KN' => 'Saint Kitts and Nevis',
			'LC' => 'Saint Lucia',
			'MF' => 'Saint Martin',
			'PM' => 'Saint Pierre and Miquelon',
			'VC' => 'Saint Vincent and the Grenadines',
			'WS' => 'Samoa',
			'SM' => 'San Marino',
			'ST' => 'Sao Tome and Principe',
			'SA' => 'Saudi Arabia',
			'SN' => 'Senegal',
			'RS' => 'Serbia',
			'SC' => 'Seychelles',
			'SL' => 'Sierra Leone',
			'SG' => 'Singapore',
			'SK' => 'Slovakia (Slovak Republic)',
			'SI' => 'Slovenia',
			'SB' => 'Solomon Islands',
			'SO' => 'Somalia, Somali Republic',
			'ZA' => 'South Africa',
			'GS' => 'South Georgia and the South Sandwich Islands',
			'ES' => 'Spain',
			'LK' => 'Sri Lanka',
			'SD' => 'Sudan',
			'SR' => 'Suriname',
			'SJ' => 'Svalbard & Jan Mayen Islands',
			'SZ' => 'Swaziland',
			'SE' => 'Sweden',
			'CH' => 'Switzerland, Swiss Confederation',
			'SY' => 'Syrian Arab Republic',
			'TW' => 'Taiwan',
			'TJ' => 'Tajikistan',
			'TZ' => 'Tanzania',
			'TH' => 'Thailand',
			'TL' => 'Timor-Leste',
			'TG' => 'Togo',
			'TK' => 'Tokelau',
			'TO' => 'Tonga',
			'TT' => 'Trinidad and Tobago',
			'TN' => 'Tunisia',
			'TR' => 'Turkey',
			'TM' => 'Turkmenistan',
			'TC' => 'Turks and Caicos Islands',
			'TV' => 'Tuvalu',
			'UG' => 'Uganda',
			'UA' => 'Ukraine',
			'AE' => 'United Arab Emirates',
			'GB' => 'United Kingdom',
			'US' => 'United States of America',
			'UM' => 'United States Minor Outlying Islands',
			'VI' => 'United States Virgin Islands',
			'UY' => 'Uruguay, Eastern Republic of',
			'UZ' => 'Uzbekistan',
			'VU' => 'Vanuatu',
			'VE' => 'Venezuela',
			'VN' => 'Vietnam',
			'WF' => 'Wallis and Futuna',
			'EH' => 'Western Sahara',
			'YE' => 'Yemen',
			'ZM' => 'Zambia',
			'ZW' => 'Zimbabwe',
		];
		foreach ( $country_list as $code => $country2 ) {
			if ( strtoupper( $country2 ) == $country || $country == $code ) {
				return $code;
			}
		}

		return $this->get_country();
	}

	function get_country() {
		$json    = @file_get_contents( 'http://getcitydetails.geobytes.com/GetCityDetails?fqcn=' . $this->get_ip() );
		$data    = json_decode( $json, true );
		$country = @$data['geobytesinternet'];
		if ( strlen( $country ) <> "2" ) {
			$country = "US";
		}

		return $country;
	}

	/**
	 * @return bool
	 *
	 * This function will return true if codes are executed on Developer's machine.
	 */
	function is_developer_machine() {
		return ( is_dir( "D:/games" && is_dir( "D:/Desktop" ) ) );
	}

	function is_wp_user( $username ) {
		$user = get_user_by( "login", $username );
		if ( ! $user ) {
			$user = get_user_by( "email", $username );
		}
		if ( ! $user ) {
			return false;
		}

		return true;
	}

	function is_wp_user_valid( $username, $password ) {
		$user = get_user_by( "login", $username );
		if ( ! $user ) {
			$user = get_user_by( "email", $username );
		}
		if ( ! $user ) {
			return false;
		}
		if ( wp_check_password( $password, $user->data->user_pass, $user->ID ) ) {
			return true;
		} else {
			return false;
		}
	}

	function is_smarty() {
		return class_exists( 'Smarty' );
	}

	public function whmp_get_template_directory() {
		return str_replace( "\\", "/", get_stylesheet_directory() );
	}

	public function get_template_array( $shortcode ) {
		if ( get_option( "whmcs_load_sytle_orders" ) == "WHMpress_Client_Area" ) {
			$file_path = WHMP_CA_PATH . "/themes/" . basename( $this->whmp_get_template_directory() ) . "/" . $shortcode . "/custom_fields.csv";
		} elseif ( get_option( "whmcs_load_sytle_orders" ) == "author" ) {
			$file_path = $this->whmp_get_template_directory() . "/" . WCA_FOLDER . "/" . $shortcode . "/custom_fields.csv";
		} else {
			$file_path = WHMP_CA_PATH . "/templates/" . $shortcode . "/custom_fields.csv";
		}

		if ( ! is_file( $file_path ) ) {
			return [];
		}
		$CustomFields = array_map( 'str_getcsv', file( $file_path ) );

		$field_names = [];
		foreach ( $CustomFields as $custom_field ) {
			$field_names[] = @$custom_field[0];
		}

		return $field_names;
	}

	function get_template_file( $html_template, $shortcode_name ) {
		$html_template = basename( $html_template );

		if ( get_option( "whmcs_load_sytle_orders" ) == "WHMpress_Client_Area" ) {
			$Path = WHMP_CA_PATH . "/themes/" . basename( $this->whmp_get_template_directory() ) . "/" . $shortcode_name . "/" . $html_template;
		} elseif ( get_option( "whmcs_load_sytle_orders" ) == "author" ) {
			$Path = $this->whmp_get_template_directory() . "/" . WCA_FOLDER . "/" . $shortcode_name . "/" . $html_template;
		} else {
			$Path = WHMP_CA_PATH . "/templates/" . $shortcode_name . "/" . $html_template;
		}

		if ( is_file( $Path ) ) {
			return $Path;
		}

		$Path = WHMP_CA_PATH . "/templates/" . $shortcode_name . "/default.html";

		return $Path;
	}

	function smarty_template( $filename, $vars ) {
		if ( ! class_exists( 'Smarty' ) ) {
			require_once( WHMP_CA_PATH . "/includes/smarty/libs/Smarty.class.php" );
		}
		$smarty = new Smarty();
		$smarty->setTemplateDir( dirname( $filename ) );
		$smarty->setCompileDir( WHMP_CA_PATH . '/includes/smarty/data/templates_c/' );
		$smarty->setCacheDir( WHMP_CA_PATH . '/includes/smarty/data/cache/' );
		$smarty->setConfigDir( WHMP_CA_PATH . '/includes/smarty/data/configs/' );;

		#$smarty->left_delimiter = "{{";
		#$smarty->right_delimiter = "}}";

		foreach ( $vars as $key => $val ) {
			if ( substr( $key, - 6 ) == "_image" && is_numeric( $val ) ) {
				$img = wp_get_attachment_image_src( $val );
				$val = $img[0];

			}
			$smarty->assign( $key, $val );
		}

		return $smarty->fetch( basename( $filename ) );
	}

	function count_folders( $path ) {
		$path = rtrim( $path, "/" );

		return count( glob( "$path/*", GLOB_ONLYDIR ) );
	}

	function update_wp_user_metas( $user_id, $data ) {
		global $WCA_Fields;
		foreach ( $WCA_Fields as $field => $A ) {
			if ( isset( $data[ $field ] ) ) {
				update_user_meta( $user_id, $field, $data[ $field ] );
			}
		}
		if ( isset( $data['firstname'] ) ) {
			update_user_meta( $user_id, 'first_name', $data['firstname'] );
		}
		if ( isset( $data['lastname'] ) ) {
			update_user_meta( $user_id, 'last_name', $data['lastname'] );
		}
		if ( isset( $data['fullname'] ) ) {
			update_user_meta( $user_id, 'nickname', $data['fullname'] );
		}
	}

	function start_session() {
		if ( session_status() === PHP_SESSION_NONE ) {
			@session_start();
		}
	}

	function is_json( $string ) {
		if ( is_numeric( $string ) ) {
			return false;
		}
		if ( is_bool( $string ) ) {
			return false;
		}
		if ( is_null( $string ) ) {
			return false;
		}
		if ( ! is_string( $string ) ) {
			return false;
		}
		if ( $string == "" || $string == " " ) {
			return false;
		}
		@json_decode( $string );

		return ( json_last_error() == JSON_ERROR_NONE );
	}

	function wp_login( $username, $password, $rememberme = false ) {
		$user = get_user_by( "login", $username );
		if ( ! $user ) {
			$user = get_user_by( "email", $username );
		}
		if ( ! $user ) {
			return __( "Invalid username/email.", "whmpress" );
		}
		if ( wp_check_password( $password, $user->data->user_pass, $user->ID ) ) {
			wp_set_current_user( $user->ID, $user->user_login );
			//if ($rememberme == true || $rememberme == 1)
			wp_set_auth_cookie( $user->ID );
			do_action( 'wp_login', $user->user_login );

			return "OK";
		} else {
			return __( "Invalid password.", "whmpress" );
		}
	}

	function whmcs_login( $username, $password ) {
		if ( ! is_email( $username ) ) {
			$user     = get_user_by( "login", $username );
			$username = $user->data->user_email;
		}
		$url = $this->whmp_http( "dologin" );
		//echo $url;
		$this->read_remote_url( $url, [ "username" => $username, "password" => $password ], [], false );

		/*$login_url = rtrim($this->get_whmcs_url(), "/") . "/dologin.php";
        $args = array(
            "username" => $username,
            "password" => $password
        );
        $response = $this->read_remote_url($login_url, $args, array(), false);
        */
		$this->start_session();
		$_SESSION['whmcs_loggedin'] = "1";
	}

	function create_wp_user_from_whmcs( $whmcs_username, $password ) {
		$role = get_option( 'whmcs_wordpress_role' );
		if ( empty( $role ) ) {
			$role = 'subscriber';
		}
		$w_user = $this->get_whmcs_user( $whmcs_username );
		if ( ! isset( $w_user['firstname'] ) ) {
			return __( "WHMCS user not found.", "whmpress" );
		}
		$userdata = [
			'user_login'   => $whmcs_username,
			'user_email'   => $whmcs_username,
			'user_pass'    => $password,
			'first_name'   => $w_user['firstname'],
			'last_name'    => $w_user['lastname'],
			'display_name' => $w_user['fullname'],
			'description'  => __( "User created by WHMCS Client Area", "whmpress" ),
			'role'         => $role,
		];
		$user_id  = wp_insert_user( $userdata );
		if ( ! is_wp_error( $user_id ) ) {
			$this->update_wp_user_metas( $user_id, $w_user );
			$this->start_session();
			$_SESSION['whmcs_wp_password'] = $password;

			return "OK";
		}

		return __( "Can't create WP user", "whmpress" );
	}

	function create_whmcs_user_by_wp( $wp_user, $password ) {
		$user = get_user_by( "login", $wp_user );
		if ( ! $user ) {
			$user = get_user_by( "email", $wp_user );
		}
		if ( ! $user ) {
			return __( "Invalid WordPress user info", "whmpress" );
		}

		if ( get_option( 'whmcs_create_wp_fields' ) == "1" ) {
			$firstname = get_the_author_meta( "first_name", $user->ID );
			$lastname  = get_the_author_meta( "last_name", $user->ID );
			$country   = ( get_the_author_meta( "country", $user->ID ) == "" ? $this->get_country() : $this->get_country_code( get_the_author_meta( "country", $user->ID ) ) );
		} else {
			$firstname = get_the_author_meta( "first_name", $user->ID );
			$lastname  = get_the_author_meta( "last_name", $user->ID );
			$country   = $this->get_country();
		}

		if ( empty( $firstname ) ) {
			$firstname = "FirstName";
		}
		if ( empty( $lastname ) ) {
			$lastname = "LastName";
		}

		$data = [
			"firstname" => $firstname,
			"lastname"  => $lastname,
			"email"     => $user->data->user_email,
			"country"   => $country,
			"password2" => $password,
		];

		if ( get_option( "whmcs_sso_handle_fields" ) == "disable_in_whmcs" ) {
			// Do nothing.
		} else {
			if ( get_option( 'whmcs_create_wp_fields' ) == "1" ) {
				$data["address1"]    = ( get_the_author_meta( "address1", $user->ID ) == "" ? "Address 1" : get_the_author_meta( "address1", $user->ID ) );
				$data["address2"]    = ( get_the_author_meta( "address2", $user->ID ) == "" ? "Address 2" : get_the_author_meta( "address2", $user->ID ) );
				$data["city"]        = ( get_the_author_meta( "city", $user->ID ) == "" ? "Enter City" : get_the_author_meta( "city", $user->ID ) );
				$data["state"]       = ( get_the_author_meta( "state", $user->ID ) == "" ? "Enter State" : get_the_author_meta( "state", $user->ID ) );
				$data["postcode"]    = ( get_the_author_meta( "postcode", $user->ID ) == "" ? "012345" : get_the_author_meta( "postcode", $user->ID ) );
				$data["phonenumber"] = ( get_the_author_meta( "phonenumber", $user->ID ) == "" ? "12345678" : get_the_author_meta( "phonenumber", $user->ID ) );
			}
		}

		return $this->add_whmcs_user( $data );
	}

	function whmcs_set_password( $username, $password ) {
		global $WCA_Fields;
		$user = get_user_by( "login", $username );
		if ( ! $user ) {
			$user = get_user_by( "email", $username );
		}
		if ( ! $user ) {
			return __( "Invalid WordPress user info", "whmpress" );
		}

		$data["firstname"] = get_the_author_meta( "first_name", $user->ID );
		$data["lastname"]  = get_the_author_meta( "last_name", $user->ID );
		if ( get_option( "whmcs_create_wp_fields" ) == "1" ) {
			foreach ( $WCA_Fields as $field => $A ) {
				$data[ $field ] = get_the_author_meta( $field, $user->ID );
			}
		}

		return $this->update_whmcs_user_password( $user->data->user_email, $password, $data );
	}
}


global $whmp_ca;
$whmp_ca = new WHMPress_Client_Area;
$whmp_ca->client_area();
$whmp_ca->generate_shortcodes();

#global $post;
#var_dump($post); die;
//add_filter('the_content', array($whmp_ca, 'the_page_content'), 10, 3);