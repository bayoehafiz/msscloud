<?php
if (!function_exists ('add_action')) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit();
}
if(!function_exists('MA_file_uri')) {
	/**
	 *
	 * Function used to get the file URI - useful when child theme is used
	 *
	 * @return file URI (in the original theme or in the child theme if file exists)
	 *
	 **/
	function MA_file_uri($path) {
		if(is_child_theme()) {
			if($path == false) {
				return get_stylesheet_directory_uri();
			} else {
				if(is_file(get_stylesheet_directory() . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $path))) {
					return get_stylesheet_directory_uri() . '/' . $path;
				} else {
					return get_template_directory_uri() . '/' . $path;
				}
			}
		} else {
			if($path == false) {
				return get_template_directory_uri();
			} else {
				return get_template_directory_uri() . '/' . $path;
			}
		}
	}
}
class MA_Import {

	public $message = "";
	public $attachments = false;
	function MA_Import() {
		add_action('admin_menu', array(&$this, 'sample_data_import_menu'));

	}


	public function import_content($file){
		if (!class_exists('WP_Importer')) {
			ob_start();
			$class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
			require_once($class_wp_importer);
			require_once(plugin_dir_path( __FILE__ ) . 'wordpress-importer.php');
			$MA_import = new WP_Import();
			set_time_limit(0);
			$path = plugin_dir_path( __FILE__ ) . 'demo-files/' . $file;

			$MA_import->fetch_attachments = $this->attachments;
			$returned_value = $MA_import->import($path);
			if(is_wp_error($returned_value)){
				$this->message = __("An Error Occurred During Import", 'widget-importer-exporter');
			}
			else {
				$this->message = __("Content imported successfully", 'widget-importer-exporter');
			}
			ob_get_clean();
		} else {
			$this->message = __("Error loading files", 'widget-importer-exporter');
		}
	}



	public function import_widgets($file){
		global $wp_registered_sidebars, $wp_registered_widget_controls;
		$widget_controls = $wp_registered_widget_controls;

		$available_widgets = array();

		foreach ( $widget_controls as $widget ) {

			if ( ! empty( $widget['id_base'] ) && ! isset( $available_widgets[$widget['id_base']] ) ) {

				$available_widgets[$widget['id_base']]['id_base'] = $widget['id_base'];
				$available_widgets[$widget['id_base']]['name'] = $widget['name'];

			}

		}
		$file = plugin_dir_path( __FILE__ ) . 'demo-files/' . $file ;
		$data = file_get_contents( $file );
		$data = json_decode( $data );
		// Get all existing widget instances
		$widget_instances = array();
		foreach ( $available_widgets as $widget_data ) {
			$widget_instances[$widget_data['id_base']] = get_option( 'widget_' . $widget_data['id_base'] );
		}
	// Begin results
	$results = array();

	// Loop import data's sidebars
	foreach ( $data as $sidebar_id => $widgets ) {

		// Skip inactive widgets
		// (should not be in export file)
		if ( 'wp_inactive_widgets' == $sidebar_id ) {
			continue;
		}

		// Check if sidebar is available on this site
		// Otherwise add widgets to inactive, and say so
		if ( isset( $wp_registered_sidebars[$sidebar_id] ) ) {
			$sidebar_available = true;
			$use_sidebar_id = $sidebar_id;
			$sidebar_message_type = 'success';
			$sidebar_message = '';
		} else {
			$sidebar_available = false;
			$use_sidebar_id = 'wp_inactive_widgets'; // add to inactive if sidebar does not exist in theme
			$sidebar_message_type = 'error';
			$sidebar_message = __( 'Sidebar does not exist in theme (using Inactive)', 'widget-importer-exporter' );
		}

		// Result for sidebar
		$results[$sidebar_id]['name'] = ! empty( $wp_registered_sidebars[$sidebar_id]['name'] ) ? $wp_registered_sidebars[$sidebar_id]['name'] : $sidebar_id; // sidebar name if theme supports it; otherwise ID
		$results[$sidebar_id]['message_type'] = $sidebar_message_type;
		$results[$sidebar_id]['message'] = $sidebar_message;
		$results[$sidebar_id]['widgets'] = array();

		// Loop widgets
		foreach ( $widgets as $widget_instance_id => $widget ) {

			$fail = false;

			// Get id_base (remove -# from end) and instance ID number
			$id_base = preg_replace( '/-[0-9]+$/', '', $widget_instance_id );
			$instance_id_number = str_replace( $id_base . '-', '', $widget_instance_id );

			// Does site support this widget?
			if ( ! $fail && ! isset( $available_widgets[$id_base] ) ) {
				$fail = true;
				$widget_message_type = 'error';
				$widget_message = __( 'Site does not support widget', 'widget-importer-exporter' ); // explain why widget not imported
			}

			// Filter to modify settings before import
			// Do before identical check because changes may make it identical to end result (such as URL replacements)
			$widget = apply_filters( 'wie_widget_settings', $widget );

			// Does widget with identical settings already exist in same sidebar?
			if ( ! $fail && isset( $widget_instances[$id_base] ) ) {

				// Get existing widgets in this sidebar
				$sidebars_widgets = get_option( 'sidebars_widgets' );
				$sidebar_widgets = isset( $sidebars_widgets[$use_sidebar_id] ) ? $sidebars_widgets[$use_sidebar_id] : array(); // check Inactive if that's where will go

				// Loop widgets with ID base
				$single_widget_instances = ! empty( $widget_instances[$id_base] ) ? $widget_instances[$id_base] : array();
				foreach ( $single_widget_instances as $check_id => $check_widget ) {

					// Is widget in same sidebar and has identical settings?
					if ( in_array( "$id_base-$check_id", $sidebar_widgets ) && (array) $widget == $check_widget ) {

						$fail = true;
						$widget_message_type = 'warning';
						$widget_message = __( 'Widget already exists', 'widget-importer-exporter' ); // explain why widget not imported

						break;

					}

				}

			}

			// No failure
			if ( ! $fail ) {

				// Add widget instance
				$single_widget_instances = get_option( 'widget_' . $id_base ); // all instances for that widget ID base, get fresh every time
				$single_widget_instances = ! empty( $single_widget_instances ) ? $single_widget_instances : array( '_multiwidget' => 1 ); // start fresh if have to
				$single_widget_instances[] = (array) $widget; // add it

					// Get the key it was given
					end( $single_widget_instances );
					$new_instance_id_number = key( $single_widget_instances );

					// If key is 0, make it 1
					// When 0, an issue can occur where adding a widget causes data from other widget to load, and the widget doesn't stick (reload wipes it)
					if ( '0' === strval( $new_instance_id_number ) ) {
						$new_instance_id_number = 1;
						$single_widget_instances[$new_instance_id_number] = $single_widget_instances[0];
						unset( $single_widget_instances[0] );
					}

					// Move _multiwidget to end of array for uniformity
					if ( isset( $single_widget_instances['_multiwidget'] ) ) {
						$multiwidget = $single_widget_instances['_multiwidget'];
						unset( $single_widget_instances['_multiwidget'] );
						$single_widget_instances['_multiwidget'] = $multiwidget;
					}

					// Update option with new widget
					update_option( 'widget_' . $id_base, $single_widget_instances );

				// Assign widget instance to sidebar
				$sidebars_widgets = get_option( 'sidebars_widgets' ); // which sidebars have which widgets, get fresh every time
				$new_instance_id = $id_base . '-' . $new_instance_id_number; // use ID number from new widget instance
				$sidebars_widgets[$use_sidebar_id][] = $new_instance_id; // add new instance to sidebar
				update_option( 'sidebars_widgets', $sidebars_widgets ); // save the amended data

				// Success message
				if ( $sidebar_available ) {
					$widget_message_type = 'success';
					$widget_message = __( 'Imported', 'widget-importer-exporter' );
				} else {
					$widget_message_type = 'warning';
					$widget_message = __( 'Imported to Inactive', 'widget-importer-exporter' );
				}

			}

			// Result for widget instance
			$results[$sidebar_id]['widgets'][$widget_instance_id]['name'] = isset( $available_widgets[$id_base]['name'] ) ? $available_widgets[$id_base]['name'] : $id_base; // widget name or ID if name not available (not supported by site)
			$results[$sidebar_id]['widgets'][$widget_instance_id]['title'] = $widget->title ? $widget->title : __( 'No Title', 'widget-importer-exporter' ); // show "No Title" if widget instance is untitled
			$results[$sidebar_id]['widgets'][$widget_instance_id]['message_type'] = $widget_message_type;
			$results[$sidebar_id]['widgets'][$widget_instance_id]['message'] = $widget_message;

		}

	}

		}



	public function import_options($file){
						global $wpdb;
						$file = plugin_dir_path( __FILE__ ) . 'demo-files/' . $file ;
							$encode_options = file_get_contents($file);
							$options = json_decode($encode_options, true);
							if(is_array($options) && count($options) > 0) {
								foreach($options as $key => $value) {
									update_option($key, esc_attr($value));
								}
							}
							//CompileOptionsLess('options.less');

	}

	public function import_revsliders(){
	if(file_exists(ABSPATH .'wp-content/plugins/revslider/revslider_admin.php')){
		require_once(ABSPATH .'wp-content/plugins/revslider/revslider_admin.php');
			if ($handle = opendir(plugin_dir_path( __FILE__ ).'demo-files/revsliders')) {
				while (false !== ($entry = readdir($handle))) {
					if ($entry != "." && $entry != "..") {
						$_FILES['import_file']['tmp_name']=plugin_dir_path( __FILE__ ).'demo-files/revsliders/'.$entry;
						$slider = new RevSlider();
						$response = $slider->importSliderFromPost(true, true);
					}
				}
				closedir($handle);
			}
		}

	}

	public function import_grids($file){
	if(file_exists(ABSPATH .'wp-content/plugins/essential-grid/admin/includes/import.class.php')){
		require_once(ABSPATH .'wp-content/plugins/essential-grid/essential-grid.php');
		require_once(ABSPATH .'wp-content/plugins/essential-grid/admin/includes/import.class.php');
						$im = new Essential_Grid_Import();
						$file=plugin_dir_path( __FILE__ ).'demo-files/'.$file;
						$grid_extract = json_decode(file_get_contents($file), true);
						$skins = @$grid_extract['skins'];
							if(!empty($skins) && is_array($skins)){
								$skin_ids = array();
								foreach($skins as $skin){
									$skin_ids[] = $skin['id'];
								}
								$im->import_skins($skins,$skin_ids);
							}
						$navigation_skins = @$grid_extract['navigation-skins'];
							if(!empty($navigation_skins) && is_array($navigation_skins)){
								$navigation_skin_ids = array();
								foreach($navigation_skins as $navigation_skin){
									$navigation_skin_ids[] = $navigation_skin['id'];
								}
								$im->import_navigation_skins($navigation_skins,$navigation_skin_ids);
							}
						$custom_meta = @$grid_extract['custom-meta'];
							if(!empty($custom_meta) && is_array($custom_meta)){
								$im->import_custom_meta($custom_meta);
							}
						$grids = @$grid_extract['grids'];
							if(!empty($grids) && is_array($grids)){
								$im->import_grids($grids);
							}

		}

	}



	function sample_data_import_menu()
			{
			   $page = add_submenu_page(
				'themes.php',
				"Import Demo",
				"Import Demo",
				"manage_options",
				"demo_import",
				array($this,'MA_generate_import_page')
				);
			}
	function MA_generate_import_page() {
	global $MA_tpl;
	global $wpdb;
	wp_register_style('dp-import-export-css', plugin_dir_url( __FILE__ ) . 'css/importexport.css');
	wp_enqueue_style('dp-import-export-css');
	wp_register_style('dp-metabox-css',  plugin_dir_url( __FILE__ ) . 'css/metabox.css');
	wp_enqueue_style('dp-metabox-css');
	wp_register_style('dp-jquery-ui-css',  plugin_dir_url( __FILE__ ) . 'css/jquery-ui.min.css');
	wp_enqueue_style('dp-jquery-ui-css');
	wp_register_script('dp-jquery-ui-js', plugin_dir_url( __FILE__ ) . 'js/jquery-ui.min.js', array('jquery'),  false,true);
	wp_enqueue_script('dp-jquery-ui-js');
		?>
		<div class="dpWrap wrap">

			<h3><?php _e('Import Demo Content') ?></h3>
			<form method="post" action="" id="importContentForm">
					<div class="clearfix">
					<div class="col_onefourth">
					<p><?php _e('<b>Import</b><br/>Choose demo content you want to import</p>'); ?>
					</div>
					<div class="col_onefourth">
						<p>
							<em>Demo Site</em><br/>
							<select class="width100" name="import_example" id="import_example">
								<option value="maxine1">NRGHost Demo</option>
							</select>
						</p>
					</div>
					<div class="col_onefourth">
						<p>
							<em>Import Type</em><br/>
							<select class="width100" name="import_option" id="import_option" class="form-control dynamof-form-element">
								<option value="">Please Select</option>
								<option value="complete_content">All</option>
								<option value="content">Content</option>
								<option value="widgets">Widgets</option>
								<!--option value="options">Options</option>
								<option value="revsliders">Revsliders</option-->
							</select>
						</p>
					</div>
					<div class="clearboth"></div>
					<div class="col_onefourth" >
						<p>
							<b><?php esc_html_e('Import attachments'); ?></b><br/>
							<?php esc_html_e('Do you want to import media files?'); ?>
						</p>
					</div>
					<div class="col_onefourth" >
						<p><input type="checkbox" value="1" name="import_attachments" id="import_attachments" /></p>
					</div>
					<div class="clearboth"></div>
						<p>
						<input type="submit" class="dpMedia" value="<?php esc_html_e('Import Demo Content'); ?>" name="import" id="import_demo_data" />
						</p>

						<div class="dp_import_load col_onefourth"><span><?php _e('The import process may take some time. Please be patient.') ?> </span><br />
							<div class="dynamo-progress-bar-wrapper">
								<div class="progress-bar-wrapper">
									<div id="progressbar" class="dynamo-progress-bar"></div>
								</div>
								<div class="progress-value">1%</div>
								<div class="progress-bar-message">
								</div>
							</div>
						</div>
						<div class="clearboth"></div>
						<div class="notification warning"><p><i class="Default-warning2"></i><span><?php _e('Important notes:') ?></span><br/>                            <ol>
								<li><?php _e('Please note that import process will take time needed to download all attachments from demo web site.'); ?></li>
								<li> <?php _e('Please install these plugins before you run import.')?></li>
								<li> <?php _e('If you plan to use shop, please install WooCommerce before you run import.')?></li>
							</ol>
</p></div>


					</div>

			</form>
		</div>
		<script type="text/javascript">
			jQuery(document).ready(function() {
				jQuery(document).on('click', '#import_demo_data', function(e) {
					e.preventDefault();
					var import_opt = jQuery( "#import_option" ).val();
					if(import_opt == '') {
							alert ("Please select import type");
							return false;
						}
					if (confirm('Are you sure, you want to import Demo Data now?')) {
						jQuery('.dp_import_load').css('display','block');
						var dp_progressbar = jQuery('#progressbar')

						var import_expl = jQuery( "#import_example" ).val();
						var p = 0;
						dp_progressbar.progressbar({
							value: 1
						});
						if(import_opt == 'content'){
							for(var i=1;i<10;i++){
								var str;
								if (i < 10) str = 'demo_content_0'+i+'.xml';
								else str = 'demo_content_'+i+'.xml';
								jQuery.ajax({
									type: 'POST',
									url: ajaxurl,
									data: {
										action: 'MA_dataImport',
										xml: str,
										example: import_expl,
										import_attachments: (jQuery("#import_attachments").is(':checked') ? 1 : 0)
									},
									success: function(data, textStatus, XMLHttpRequest){
										p+= 10;
										jQuery('.progress-value').html((p) + '%');
										dp_progressbar.progressbar("value", p)
										if (p == 90) {
											str = 'demo_content_10.xml';
											jQuery.ajax({
												type: 'POST',
												url: ajaxurl,
												data: {
													action: 'MA_dataImport',
													xml: str,
													example: import_expl,
													import_attachments: (jQuery("#import_attachments").is(':checked') ? 1 : 0)
												},
												success: function(data, textStatus, XMLHttpRequest){
													p+= 10;
													jQuery('.progress-value').html((p) + '%');
													dp_progressbar.progressbar("value", p)
													jQuery('.progress-bar-message').html('<div class="notification success"><p><i class="Default-thumbs-up2"></i><span>Success!</span><br/>Demo content is imported.</p></div>');
												},
												error: function(MLHttpRequest, textStatus, errorThrown){
												}
											});
										}
									},
									error: function(MLHttpRequest, textStatus, errorThrown){
									}
								});
							}
						} else if(import_opt == 'widgets') {
							jQuery.ajax({
								type: 'POST',
								url: ajaxurl,
								data: {
									action: 'MA_widgetsImport',
									example: import_expl
								},
								success: function(data, textStatus, XMLHttpRequest){
									jQuery('.progress-value').html((100) + '%');
									dp_progressbar.progressbar("value", 100)
								},
								error: function(MLHttpRequest, textStatus, errorThrown){
								}
							});
						} else if(import_opt == 'options'){
							jQuery.ajax({
								type: 'POST',
								url: ajaxurl,
								data: {
									action: 'MA_optionsImport',
									example: import_expl
								},
								success: function(data, textStatus, XMLHttpRequest){
									jQuery('.progress-value').html((100) + '%');
									dp_progressbar.progressbar("value", 100)
								},
								error: function(MLHttpRequest, textStatus, errorThrown){
								}
							});
						}else if(import_opt == 'revsliders'){
							jQuery.ajax({
								type: 'POST',
								url: ajaxurl,
								data: {
									action: 'MA_revImport',
									example: import_expl
								},
								success: function(data, textStatus, XMLHttpRequest){
									jQuery('.progress-value').html((100) + '%');
									dp_progressbar.progressbar("value", 100)
								},
								error: function(MLHttpRequest, textStatus, errorThrown){
								}
							});
						}else if(import_opt == 'grids'){
							jQuery.ajax({
								type: 'POST',
								url: ajaxurl,
								data: {
									action: 'MA_gridImport',
									example: import_expl
								},
								success: function(data, textStatus, XMLHttpRequest){
									jQuery('.progress-value').html((100) + '%');
									dp_progressbar.progressbar("value", 100)
								},
								error: function(MLHttpRequest, textStatus, errorThrown){
								}
							});
						}else if(import_opt == 'complete_content'){
							for(var i=1;i<10;i++){
								var str;
								if (i < 10) str = 'demo_content_0'+i+'.xml';
								else str = 'demo_content_'+i+'.xml';
								jQuery.ajax({
									type: 'POST',
									url: ajaxurl,
									data: {
										action: 'MA_dataImport',
										xml: str,
										example: import_expl,
										import_attachments: (jQuery("#import_attachments").is(':checked') ? 1 : 0)
									},
									success: function(data, textStatus, XMLHttpRequest){
										p+= 10;
										jQuery('.progress-value').html((p) + '%');
										dp_progressbar.progressbar("value", p)
										if (p == 90) {
											str = 'demo_content_10.xml';
											jQuery.ajax({
												type: 'POST',
												url: ajaxurl,
												data: {
													action: 'MA_dataImport',
													xml: str,
													example: import_expl,
													import_attachments: (jQuery("#import_attachments").is(':checked') ? 1 : 0)
												},
												success: function(data, textStatus, XMLHttpRequest){
													jQuery.ajax({
														type: 'POST',
														url: ajaxurl,
														data: {
															action: 'MA_otherImport',
															example: import_expl
														},
														success: function(data, textStatus, XMLHttpRequest){
															jQuery('.progress-value').html((100) + '%');
															dp_progressbar.progressbar("value", 100)
															jQuery('.progress-bar-message').html('<div class="notification success"><p><i class="Default-thumbs-up2"></i><span>Success!</span><br/>Demo content is imported.</p></div>');
														},
														error: function(MLHttpRequest, textStatus, errorThrown){
														}
													});
												},
												error: function(MLHttpRequest, textStatus, errorThrown){
												}
											});
										}
									},
									error: function(MLHttpRequest, textStatus, errorThrown){
									}
								});
							}
						}
					}
					return false;
				});
			});
		</script>

		</div>

	<?php   }

}
global $my_MA_Import;
$my_MA_Import = new MA_Import();



if(!function_exists('MA_dataImport')){
	function MA_dataImport(){
		global $my_MA_Import;

		if ($_POST['import_attachments'] == 1)
			$my_MA_Import->attachments = true;
		else
			$my_MA_Import->attachments = false;


		if (!empty($_POST['example']))
			$folder = $_POST['example']."/";

		$my_MA_Import->import_content($_POST['xml']);

		die();
	}

	add_action('wp_ajax_MA_dataImport', 'MA_dataImport');
}

if(!function_exists('MA_widgetsImport')){
	function MA_widgetsImport(){
		global $my_MA_Import;


		if (!empty($_POST['example']))
			$folder = $_POST['example']."/";

		$my_MA_Import->import_widgets('widgets.wie');

		die();
	}

	add_action('wp_ajax_MA_widgetsImport', 'MA_widgetsImport');
}

if(!function_exists('MA_optionsImport')){
	function MA_optionsImport(){
		global $my_MA_Import;


		if (!empty($_POST['example']))
			$folder = $_POST['example']."/";

		$my_MA_Import->import_options('options.json');

		die();
	}

	add_action('wp_ajax_MA_optionsImport', 'MA_optionsImport');
}

if(!function_exists('MA_otherImport')){
	function MA_otherImport(){
		global $my_MA_Import;


		if (!empty($_POST['example']))
			$folder = $_POST['example']."/";

		$my_MA_Import->import_options('options.json');
		$my_MA_Import->import_widgets('widgets.wie');
		$my_MA_Import->import_grids();
		$my_MA_Import->import_revsliders();
		die();
	}

	add_action('wp_ajax_MA_otherImport', 'MA_otherImport');
}

if(!function_exists('MA_revImport')){
	function MA_revImport(){
		global $my_MA_Import;


		if (!empty($_POST['example']))
			$folder = $_POST['example']."/";

		$my_MA_Import->import_revsliders();

		die();
	}

	add_action('wp_ajax_MA_revImport', 'MA_revImport');
}

if(!function_exists('MA_gridImport')){
	function MA_gridImport(){
		global $my_MA_Import;


		if (!empty($_POST['example']))
			$folder = $_POST['example']."/";

		$my_MA_Import->import_grids('ess_grid.json');

		die();
	}

	add_action('wp_ajax_MA_gridImport', 'MA_gridImport');
}