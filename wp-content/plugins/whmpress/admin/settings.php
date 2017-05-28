<?php
/**
 * @package Admin
 * @todo    Settings page for WHMpress admin panel
 */

if ( ! defined( 'WHMP_VERSION' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}
$WHMPress = new WHMpress();

global $wpdb;
$countries  = $wpdb->get_results( "SELECT * FROM `{$wpdb->prefix}whmpress_countries` ORDER BY `country_name`" );
$WHMP       = new WHMPress;
$currencies = $WHMP->get_currencies();

$settings_file = str_replace( "\\", "/", get_stylesheet_directory() ) . "/whmpress/settings.ini";
if ( ! is_file( $settings_file ) ) {
	$settings_file = str_replace( "\\", "/", WHMP_PLUGIN_DIR ) . "/themes/" . basename( get_stylesheet_directory() ) . "/settings.ini";
}

$newTR = "<tr>";
$newTR .= '<td><select name="whmp_countries_currencies[country][]">';
$newTR .= '<option value="">-- Select Country --</option>';
foreach ( $countries as $country ):
	$newTR .= '<option value="' . $country->country_code . '">' . $country->country_name . '</option>';
endforeach;
$newTR .= '</select>';
$newTR .= '</td>';
$newTR .= '<td>';
$newTR .= '<select name="whmp_countries_currencies[currency][]">';
$newTR .= '<option value="">-- Currency --</option>';
foreach ( $currencies as $currency ) {
	$newTR .= '<option value="' . $currency["id"] . '">' . $currency["code"] . '</option>';
}
$newTR .= '</select> ';
$newTR .= '[<a title="Remove this country" href="javascript:;" onclick="Remove(this)">X</a>]';
$newTR .= '</td>';
$newTR .= '</tr>';
$newTR = str_replace( '"', "'", $newTR );

if ( ( is_dir( get_template_directory() . "/whmpress/" ) || is_dir( WHMP_PLUGIN_PATH . "themes/" . basename( get_template_directory() ) ) ) && get_option( 'load_sytle_orders' ) == '' ) {
	?>
	<div class="notice notice-success is-dismissible">
		<h3>WHMPress</h3>
		<p><?php _e( 'Matching Templates found for your active theme <b>' . basename( get_template_directory() ) . '</b>. You can enable <b>' . basename( get_template_directory() ) . '</b> support by selecting Template Source from <a href="admin.php?page=whmp-settings#styles">Settings > Styles</a>.', 'whmpress' ); ?></p>
	</div>
	<?php
}

global $wpdb; ?>

<div class="full_page_loader">
	<div class="whmp_loader"><?php _e( "Loading", "whmpress" ) ?>...</div>
</div>
<div class="wrap"><?php
	if ( is_file( $settings_file ) ) {
		$Data  = parse_ini_file( $settings_file, true );
		$theme = wp_get_theme();
		?>
		<div class="updated">
			<form method="post" action="<?php echo WHMP_PLUGIN_URL; ?>/includes/apply_settings.php" name="whmp_form">
				<input type="hidden" name="import_settings">
				<input type="hidden" name="file" value="<?php echo $settings_file ?>">
				<p>
					<?php
					$current_theme = "(<b>" . $theme->Name . "</b>)";
					printf( __( 'This plugin comes pre-configured for your current theme %1s.
            The look and feel of WHMCS client area have been adjusted to match %2s.', 'whmpress' ), $current_theme, $current_theme );
					echo "<br>";
					printf( __( 'To further adjust the settings click the button(s) below.', 'whmpress' ) );
					?>
					<br><br>
					<button class="button" onclick="ImportSettings('')">
						<i><?php _e( 'Adjust All Settings', 'whmpress' ); ?></i></button>
					<?php if ( is_array( $Data ) ) {
						foreach ( $Data as $k => $v ): ?>
							<button class="button button-primary"
							        onclick='ImportSettings("<?php echo $k ?>")'><?php echo $k ?></button>
						<?php endforeach;
					} ?>
				</p>
			</form>
		</div>
		<script>
			function ImportSettings (Section)
			{
				jQuery("input[name=import_settings]").val(Section);
				document.whmp_form.submit();
			}
		</script><?php
	} ?>
	
	
	<!--<div class="whmp-main-title"><span class="whmp-title">WHMpress</span> <?php /*_e("Settings", "whmpress") */ ?></div>-->
	<h2 class="nav-tab-wrapper">
		<a class="nav-tab" href="<?php echo admin_url() ?>admin.php?page=whmp-dashboard">Dashboard</a>
		<a class="nav-tab" href="<?php echo admin_url() ?>admin.php?page=whmp-services">Products/Services</a>
		<a class="nav-tab nav-tab-active" href="<?php echo admin_url() ?>admin.php?page=whmp-settings">Settings</a>
		<a class="nav-tab" href="<?php echo admin_url() ?>admin.php?page=whmp-sync">Sync WHMCS</a>
		<a class="nav-tab" href="<?php echo admin_url() ?>admin.php?page=whmp-extensions">Addons</a>
	</h2>
	
	<?php if ( ! $WHMPress->WHMpress_synced() ): ?>
		
		<style>
			#price table tr th {
				text-align: right;
			}
		</style>
		
		<div class="error">
			<p>
				<b>WHMPress <?php _e( "Error", "whmpress" ) ?></b>
				<?php _e( "WHMCS is not Synced", "whmpress" ) ?> <a
					href="admin.php?page=whmp-sync"><?php _e( "Please Sync WHMCS", "whmpress" ) ?></a>.
			</p>
		</div>
	
	<?php else: ?>
	
	<?php if ( isset( $_GET["settings-updated"] ) && $_GET["settings-updated"] == "true" ) {
		echo "<div class='updated'><p><b>Success</b><br />Settings saved.</p></div>";
	} ?>
		
		<div id="whmp-tabs" class="tab-container">
			<ul class='etabs'>
				<li class='tab'><a href='#general'><?php _e( "General", "whmpress" ) ?></a></li>
				<li class='tab'><a href="#styles"><?php _e( "Styles", "whmpress" ) ?></a></li>
				<?php if ( function_exists( 'whmpress_domain_search_ajax_function' ) ): ?>
					<li class='tab'><a href="#ajax_domain_search"><?php _e( "Domain Search", "whmpress" ) ?></a></li>
				<?php endif; ?>
				<li class='tab'><a href="#defaults"><?php _e( "Default Values", "whmpress" ) ?></a></li>
				<!--<li class='tab'><a href="#currencies"><?php /*_e("Currencies", "whmpress") */ ?></a></li>-->
				<li class='tab'><a href="#3rdparty"><?php _e( "3rd Party", "whmpress" ) ?></a></li>
				<li class='tab'><a href="#advanced"><?php _e( "Advanced", "whmpress" ) ?></a></li>
				<li class='tab'><a href="#registration"><?php _e( "Registration", "whmpress" ) ?></a></li>
				<li class='tab'><a href="#debug_info"><?php _e( "Debug Info", "whmpress" ) ?></a></li>
			</ul>
			
			<form method="post" action="options.php">
				<?php settings_fields( 'whmp_settings' );
				do_settings_sections( 'whmp_settings' ); ?>
				
				<div id="general">
					<table class="form-table">
						<?php if ( is_plugin_active( 'WHMpress_Client_Area/client-area.php' ) ) { ?>
							<tr>
								<td colspan="2">
									<p style="border-left:4px solid #CC0000;background-color:#fff;padding:10px;">
										<?php _e( "If you have SSL URL configured in WHMCS Admin Area > Setup > General Settings then you must select HTTPS from here, Or you will endup with redirect loop.", "whmpress" ); ?>
									</p>
								</td>
							</tr>
						<?php } ?>
						<tr valign="top">
							<td scope="row" style="width:30%;"><?php _e( "WHMCS URL", "whmpress" ) ?></td>
							<td>
								<?php
								$Q  = "SELECT `value` FROM `" . whmp_get_configuration_table_name() . "` WHERE `setting`='SystemURL' OR `setting`='SystemSSLURL' ORDER BY `setting` DESC";
								$Us = $wpdb->get_results( $Q, ARRAY_A ); ?>
								<select style="width:100%;" name="whmcs_url">
									<?php foreach ( $Us as $U ):
										if ( whmp_get_installation_url() == $U["value"] ) {
											$S = "selected=selected";
										} else {
											$S = "";
										} ?>
										<option <?php echo $S ?>><?php echo $U["value"] ?></option>
									<?php endforeach; ?>
								</select>
							</td>
						</tr>
						<tr valign="top">
							<td scope="row" style="width:30%;"><?php _e( "Decimal Seperator", "whmpress" ) ?></td>
							<td>
								<input style="width:100%;" name="decimal_replacement"
								       value="<?php echo esc_attr( get_option( 'decimal_replacement', "." ) ); ?>">
							</td>
						</tr>
						<tr valign="top">
							<td scope="row"
							    style="width:30%;"><?php _e( "Show trailing zeros in price", "whmpress" ) ?></td>
							<td>
								<?php $dz = esc_attr( get_option( 'show_trailing_zeros', "no" ) ); ?>
								<select style="width:100%;" name="show_trailing_zeros">
									<option value="no"><?php _e( "No", "whmpress" ) ?></option>
									<option
										value="yes" <?php echo $dz == "yes" ? "selected=selected" : ""; ?>><?php _e( "Yes", "whmpress" ) ?></option>
								</select>
							</td>
						</tr>
						<tr valign="top">
							<td scope="row" style="width:30%;"><?php _e( "Show symbol with price", "whmpress" ) ?></td>
							<td>
								<?php $symb = esc_attr( get_option( 'default_currency_symbol', "prefix" ) ); ?>
								<select style="width:100%;" name="default_currency_symbol">
									<option value="prefix"><?php _e( "Prefix", "whmpress" ) ?></option>
									<option
										value="suffix" <?php echo $symb == "suffix" ? "selected=selected" : ""; ?>><?php _e( "Suffix", "whmpress" ) ?></option>
									<option
										value="code" <?php echo $symb == "code" ? "selected=selected" : ""; ?>><?php _e( "Code", "whmpress" ) ?></option>
									<option
										value="none" <?php echo $symb == "none" ? "selected=selected" : ""; ?>><?php _e( "None", "whmpress" ) ?></option>
								</select>
							</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td><?php submit_button(); ?></td>
						</tr>
					</table>
				</div>
				<div id="styles">
					<table class="form-table">
						<tr scope="row">
							<td style="width:30%;"><?php _e( "Select style", "whmpress" ) ?></td>
							<td>
								<select name="whmp_custom_css">
									<?php $theme_files = glob( WHMP_PLUGIN_DIR . "/styles/*.css" );
									if ( is_array( $theme_files ) ) {
										foreach ( $theme_files as $theme_file ):
											$S = whmpress_get_option( "whmp_custom_css" ) == basename( $theme_file ) ? "selected=selected" : ""; ?>
											<option <?php echo $S ?>><?php echo basename( $theme_file ) ?></option>
										<?php endforeach;
									} ?>
								</select>
								<br>
								<span style="color: #CC0000;">
                            <b>Note:</b> <?php _e( "These styles are used if you do not select any templates while inserting WHMpress shortcodes.", "whmpress" ); ?>
                            </span>
							</td>
						</tr>
						<?php
						$theme_whmpress_folder = get_template_directory() . "/whmpress/";
						$disable1              = false;
						if ( ! is_dir( $theme_whmpress_folder ) ) {
							$disable1 = true;
						} else {
							if ( count_folders( $theme_whmpress_folder ) == 0 ) {
								$disable1 = true;
							}
						}
						$path     = basename( get_template_directory() );
						$path     = WHMP_PLUGIN_PATH . "themes/" . $path;
						$disable2 = false;
						if ( ! is_dir( $path ) ) {
							$disable2 = true;
						}
						
						$message1 = basename( get_template_directory() ) . __( ' Templates by Theme Author', 'whmpress' );
						if ( $disable1 ) {
							$message1 .= " " . __( "(Not found)", "whmpress" );
						}
						
						$message2 = basename( get_template_directory() ) . __( ' Templates by WHMpress', 'whmpress' );
						if ( $disable2 ) {
							$message2 .= " " . __( "(Not found)", "whmpress" );
						}
						?>
						<tr valign="top">
							<td scope="row" style="width:30%;"><?php _e( "Templates to Use", "whmpress" ) ?></td>
							<td>
								<select name="load_sytle_orders">
									<option
										value=""><?php _e( 'Generic Templates (Works with any theme)', 'whmpress' ); ?></option>
									<option <?php echo $disable1 ? 'disabled="disabled"' : ''; ?> <?php echo get_option( "load_sytle_orders" ) == "author" ? "selected=selected" : "" ?>
										value="author"><?php echo $message1; ?></option>
									<option <?php echo $disable2 ? 'disabled="disabled"' : ''; ?> <?php echo get_option( "load_sytle_orders" ) == "whmpress" ? "selected=selected" : "" ?>
										value="whmpress"><?php echo $message2; ?></option>
								</select>
								<br>
								<span style="color: #CC0000;">
                                <b>Note:</b> <?php _e( "Matching Pricing Tables and other templates for your active theme are available. To use them select appropriate option", "whmpress" ); ?>
                            </span>
							</td>
						</tr>
						<tr scope="row">
							<td valign="top" style="width:30%;"><?php _e( "Include font awesome", "whmpress" ) ?></td>
							<td>
								<select name="include_fontawesome">
									<option value="0"><?php _e( "No", "whmpress" ) ?></option>
									<option
										value="1" <?php echo get_option( 'include_fontawesome' ) == "1" ? "selected=selected" : "" ?>><?php _e( "Yes", "whmpress" ) ?></option>
								</select><br/>
								<span style="color: #CC0000;">
                    <b>Note:</b> <a href="http://fontawesome.io/icons/" target="_blank">FontAwesome</a> is font icons library, which is used by our "Pricing Table Groups". If your theme doesn't include FontAwesome then you can use FontAwesome.
                    </span>
							</td>
						</tr>
						<tr scope="row">
							<td valign="top" style="width:30%;"><?php _e( "Custom CSS codes", "whmpress" ) ?></td>
							<td>
                                <textarea style="width: 100%; height:200px"
                                          name="whmp_custom_css_codes"><?php echo esc_attr( whmpress_get_option( "whmp_custom_css_codes" ) ); ?></textarea>
							</td>
						</tr>
						<tr>
							<td></td>
							<td><?php submit_button(); ?></td>
						</tr>
					</table>
				</div>
				<?php if ( function_exists( 'whmpress_domain_search_ajax_function' ) ): ?>
					<div id="ajax_domain_search">
						<?php include_once( dirname( __FILE__ ) . "/settings-tabs/domain_search.php" ); ?>
					</div>
				<?php endif; ?>
				<div id="defaults">
					<?php include_once( dirname( __FILE__ ) . "/settings-tabs/defaults.php" ); ?>
				</div>
				<!--<div id="currencies">
                <h3 class="whmp-sub-head"><?php /*_e("Currency settings", "whmpress") */ ?></h3>
                <table class="form-table">
                    <tr valign="top">
                        <td style="width:30%;" scope="row"><?php /*_e("Override WHMCS default currency with", "whmpress") */ ?></td>
                        <td><?php /*$default_curr=whmpress_get_option("whmpress_default_currency"); */ ?>
                            <select name="whmpress_default_currency">
                                <?php
				/*                                foreach($currencies as $currency): */ ?>
                                    <option <?php /*echo ($default_curr==$currency["id"])?"selected='selected'":"" */ ?> value="<?php /*echo $currency["id"] */ ?>"><?php /*echo $currency["code"] */ ?></option>
                                <?php /*endforeach; */ ?>
                            </select>
                        </td>
                    </tr>
                </table>
                <style>
                    table#country_table thead td {
                        font-weight:bold;
                        font-size:16px;
                        padding:5px;
                    }
                </style>
                <h3 class="whmp-sub-head"><?php /*_e("Country specific currency", "whmpress") */ ?></h3>
                <table id="country_table">
                    <thead>
                    <tr>
                        <td colspan="2"><button onclick="AddTR()" type="button" class="button button-primary"><?php /*_e("Add Country", "whmpress") */ ?></button></td>
                    </tr>
                    <tr>
                        <td>Country</td>
                        <td>Currency</td>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
				/*                    $whmp_countries_currencies = get_option("whmp_countries_currencies");
									if (!is_array($whmp_countries_currencies)) $whmp_countries_currencies = array();
									if (isset($whmp_countries_currencies["country"]))
										for($x=0; $x<count($whmp_countries_currencies["country"]); $x++): */ ?>
                            <tr>
                                <td>
                                    <select name="whmp_countries_currencies[country][]">
                                        <option value="">-- Select Country --</option>
                                        <?php /*foreach($countries as $country): $S = $whmp_countries_currencies["country"][$x]==$country->country_code?"selected=selected":""; */ ?>
                                            <option <?php /*echo $S */ ?> value="<?php /*echo $country->country_code */ ?>"><?php /*echo $country->country_name */ ?></option>
                                        <?php /*endforeach */ ?>
                                    </select>
                                </td>
                                <td>
                                    <select name="whmp_countries_currencies[currency][]">
                                        <option value=""><?php /*_e('-- Currency --','whmpress');*/ ?></option>
                                        <?php /*foreach($currencies as $currency): $S = $whmp_countries_currencies["currency"][$x]==$currency["id"]?"selected=selected":""; */ ?>
                                            <option <?php /*echo $S */ ?> value="<?php /*echo $currency["id"] */ ?>"><?php /*echo $currency["code"] */ ?></option>
                                        <?php /*endforeach */ ?>
                                    </select>
                                    [<a title="Remove this country" href="javascript:;" onclick="Remove(this)">X</a>]
                                </td>
                            </tr>
                        <?php /*endfor; */ ?>
                    </tbody>
                    <tfoot>

                    <?php
				/*                    if (is_plugin_active( 'WHMpress_Client_Area/client-area.php' ))
										$disabled="disabled=disabled";
									else
										$disabled=""; */ ?>
                    <tr>
                        <td></td>
                        <?php /*if ($disabled==""): */ ?>
                            <td colspan="3"><?php /*submit_button(); */ ?></td>
                        <?php /*else: */ ?>
                            <td colspan="3"><input disabled="disabled" type="button" name="submit" id="submit" class="button button-primary" value="<?php /*_e("Save Changes", "whmpress") */ ?>"></td>
                        <?php /*endif; */ ?>
                    </tr>
                    </tfoot>
                </table>
            </div>-->
				<div id="3rdparty">
					<h3 class="whmp-sub-head"><?php _e( "URL override for compatibility with third party tools (WHMCS Bridge)", "whmpress" ) ?></h3>
					
					<?php if ( is_plugin_active( 'WHMpress_Client_Area/client-area.php' ) ) {
						$disabled = "disabled=disabled"; ?>
						<div style="border: 2px solid #FCBD28;padding:10px;background:#FFFFFF;font-weight:bold">
							<?php _e( "WHMCS Client Area Addon is Active. If you want to use a third party plugin for this purpose (WHMCS-bridge) please deactivate the WHMCS Client Area Addon by WHMpress", "whmpress" ) ?></div>
					<?php } else {
						$disabled = "";
					} ?>
					
					<table class="form-table">
						<tr valign="top">
							<td style="width:30%;" scope="row"><?php _e( "Client Area URL", "whmpress" ) ?></td>
							<td>
								<input <?php echo $disabled ?> type="url" style="width:100%;" name="client_area_url"
								                               value="<?php echo esc_attr( whmpress_get_option( 'client_area_url' ) ); ?>"/>
							</td>
						</tr>
						
						<tr valign="top">
							<td scope="row"><?php _e( "Announcements URL", "whmpress" ) ?></td>
							<td>
								<input <?php echo $disabled ?> type="url" style="width:100%;" name="announcements_url"
								                               value="<?php echo esc_attr( whmpress_get_option( 'announcements_url' ) ); ?>"/>
							</td>
						</tr>
						
						<tr valign="top">
							<td scope="row"><?php _e( "Submit Ticket URL", "whmpress" ) ?></td>
							<td>
								<input <?php echo $disabled ?> type="url" style="width:100%;" name="submit_ticket_url"
								                               value="<?php echo esc_attr( whmpress_get_option( 'submit_ticket_url' ) ); ?>"/>
							</td>
						</tr>
						
						<tr valign="top">
							<td scope="row"><?php _e( "Downloads page URL", "whmpress" ) ?></td>
							<td>
								<input <?php echo $disabled ?> type="url" style="width:100%;" name="downloads_url"
								                               value="<?php echo esc_attr( whmpress_get_option( 'downloads_url' ) ); ?>"/>
							</td>
						</tr>
						
						<tr valign="top">
							<td scope="row"><?php _e( "Support Tickets URL", "whmpress" ) ?></td>
							<td>
								<input <?php echo $disabled ?> type="url" style="width:100%;" name="support_tickets_url"
								                               value="<?php echo esc_attr( whmpress_get_option( 'support_tickets_url' ) ); ?>"/>
							</td>
						</tr>
						
						<tr valign="top">
							<td scope="row"><?php _e( "Knowledgebase URL", "whmpress" ) ?></td>
							<td>
								<input <?php echo $disabled ?> type="url" style="width:100%;" name="knowledgebase_url"
								                               value="<?php echo esc_attr( whmpress_get_option( 'knowledgebase_url' ) ); ?>"/>
							</td>
						</tr>
						
						<tr valign="top">
							<td scope="row"><?php _e( "Affiliates URL", "whmpress" ) ?></td>
							<td>
								<input <?php echo $disabled ?> type="url" style="width:100%;" name="affiliates_url"
								                               value="<?php echo esc_attr( whmpress_get_option( 'affiliates_url' ) ); ?>"/>
							</td>
						</tr>
						
						<tr valign="top">
							<td scope="row"><?php _e( "Order URL", "whmpress" ) ?></td>
							<td>
								<input <?php echo $disabled ?> type="url" style="width:100%;" name="order_url"
								                               value="<?php echo esc_attr( whmpress_get_option( 'order_url' ) ); ?>"/>
							</td>
						</tr>
						
						<tr valign="top">
							<td scope="row"><?php _e( "Pre-sales Contact URL", "whmpress" ) ?></td>
							<td>
								<input <?php echo $disabled ?> type="url" style="width:100%;"
								                               name="pre_sales_contact_url"
								                               value="<?php echo esc_attr( whmpress_get_option( 'pre_sales_contact_url' ) ); ?>"/>
							</td>
						</tr>
						
						<tr valign="top">
							<td scope="row"><?php _e( "Domain Checker URL", "whmpress" ) ?></td>
							<td>
								<input <?php echo $disabled ?> type="url" style="width:100%;" name="domain_checker_url"
								                               value="<?php echo esc_attr( whmpress_get_option( 'domain_checker_url' ) ); ?>"/>
							</td>
						</tr>
						
						<tr valign="top">
							<td scope="row"><?php _e( "Server Status URL", "whmpress" ) ?></td>
							<td>
								<input <?php echo $disabled ?> type="url" style="width:100%;" name="server_status_url"
								                               value="<?php echo esc_attr( whmpress_get_option( 'server_status_url' ) ); ?>"/>
							</td>
						</tr>
						
						<tr valign="top">
							<td scope="row"><?php _e( "Network Issues URL", "whmpress" ) ?></td>
							<td>
								<input <?php echo $disabled ?> type="url" style="width:100%;" name="network_issues_url"
								                               value="<?php echo esc_attr( whmpress_get_option( 'network_issues_url' ) ); ?>"/>
							</td>
						</tr>
						
						<tr valign="top">
							<td scope="row"><?php _e( "WHMCS Login URL", "whmpress" ) ?></td>
							<td>
								<input <?php echo $disabled ?> type="url" style="width:100%;" name="whmcs_login_url"
								                               value="<?php echo esc_attr( whmpress_get_option( 'whmcs_login_url' ) ); ?>"/>
							</td>
						</tr>
						
						<tr valign="top">
							<td scope="row"><?php _e( "WHMCS Register URL", "whmpress" ) ?></td>
							<td>
								<input <?php echo $disabled ?> type="url" style="width:100%;" name="whmcs_register_url"
								                               value="<?php echo esc_attr( whmpress_get_option( 'whmcs_register_url' ) ); ?>"/>
							</td>
						</tr>
						
						<tr valign="top">
							<td scope="row"><?php _e( "WHMCS Forget Password URL", "whmpress" ) ?></td>
							<td>
								<input <?php echo $disabled ?> type="url" style="width:100%;"
								                               name="whmcs_forget_password_url"
								                               value="<?php echo esc_attr( whmpress_get_option( 'whmcs_forget_password_url' ) ); ?>"/>
							</td>
						</tr>
						
						<tr>
							<td></td>
							<?php if ( $disabled == "" ): ?>
								<td colspan="3"><?php submit_button(); ?></td>
							<?php else: ?>
								<td colspan="3"><input disabled="disabled" type="button" name="submit" id="submit"
								                       class="button button-primary"
								                       value="<?php _e( "Save Changes", "whmpress" ) ?>"></td>
							<?php endif; ?>
						</tr>
					</table>
				</div>
				<div id="advanced">
					<h3 class="whmp-sub-head"><?php _e( "Advanced Settings", "whmpress" ) ?></h3>
					<table class="form-table">
						<tr valign="top">
							<td style="width:30%;" scope="row"><?php _e( "Override WHMCS URL", "whmpress" ) ?></td>
							<td>
								<input type="url" style="width:100%;" name="overwrite_whmcs_url"
								       value="<?php echo esc_attr( whmpress_get_option( 'overwrite_whmcs_url' ) ); ?>"/>
							</td>
						</tr>
						
						<tr valign="top">
							<td style="width:30%;" scope="row"><?php _e( "Use UTF encode/decode", "whmpress" ) ?></td>
							<td>
								<select name="whmpress_utf_encode_decode">
									<option value="">== Nothing ==</option>
									<option <?php echo whmpress_get_option( 'whmpress_utf_encode_decode' ) == "utf_encode" ? "selected=selected" : ""; ?>
										value="utf_encode">UTF Encode
									</option>
									<option <?php echo whmpress_get_option( 'whmpress_utf_encode_decode' ) == "utf_decode" ? "selected=selected" : ""; ?>
										value="utf_decode">UTF Decode
									</option>
								</select>
							</td>
						</tr>
						
						<tr valign="top">
							<td style="width:30%;"
							    scope="row"><?php _e( "Package Details from WHMpress", "whmpress" ) ?></td>
							<td>
								<select name="whmpress_use_package_details_from_whmpress">
									<option value="No">No</option>
									<option <?php echo whmpress_get_option( 'whmpress_use_package_details_from_whmpress' ) == "Yes" ? "selected=selected" : ""; ?>
										value="Yes">Yes
									</option>
								</select>
							</td>
						</tr>
						
						<tr valign="top">
							<td style="width:30%;" scope="row"><?php _e( "WHMCS Sync Frequency", "whmpress" ) ?></td>
							<td>
								<select name="whmpress_cron_recurrance">
									<option value="">Disabled</option>
									<option <?php echo get_option( "whmpress_cron_recurrance" ) == "hourly" ? "selected=selected" : ""; ?>
										value="hourly">Hourly
									</option>
									<option <?php echo get_option( "whmpress_cron_recurrance" ) == "twicedaily" ? "selected=selected" : ""; ?>
										value="hourly">Twice Daily
									</option>
									<option <?php echo get_option( "whmpress_cron_recurrance" ) == "daily" ? "selected=selected" : ""; ?>
										value="daily">Daily
									</option>
								</select>
								&nbsp;
								Use <code><?php echo get_home_url(); ?>/wp-cron.php?doing_wp_cron</code> for your
								cron.<br>
								<?php _e( "You must of have (Save Password) enabled on Sync WHMCS page." ); ?>
							</td>
						</tr>
						
						<tr>
							<td></td>
							<td colspan="3"><?php submit_button(); ?></td>
						</tr>
					</table>
				</div>
			</form>
			<div id="registration">
				<h3 class="whmp-sub-head"><?php _e( "Registration", "whmpress" ) ?></h3>
				<?php if ( $WHMPress->verified_purchase() ): ?>
					<table class="form-table">
						<tr>
							<td><?php _e( "Your purchase of WHMpress is registered" ) ?></td>
							<td style="text-align: right;">
								<button type="button" class="button button-red"
								        onclick="UnVerify();"><?php _e( "Un-Register WHMpress", "whmpress" ) ?></button>
							</td>
						</tr>
					</table>
				<?php else: ?>
					<form onsubmit="return Verify();" name="verify_form" id="verify_form">
						<table style="width:100%">
							<tr>
								<td colspan="3"><?php _e( "Email required for providing support for this product", "whmpress" ) ?></td>
							</tr>
							<tr>
								<td><input required="required" style="width:100%;" type="email" id="femail" name="email"
								           placeholder="<?php _e( "Email address", "whmpress" ) ?>"
								           value="<?php echo get_option( "admin_email" ) ?>"/></td>
								<td><input required="required" style="width:100%;" type="text" id="fpurchase_code"
								           name="purchase_code"
								           placeholder="<?php _e( "Purchase Code", "whmpress" ) ?>"/>
								</td>
								<td>
									<button type="button" onclick="Verify();" style="width:100%;"
									        class="button button-primary"><?php _e( "Verify", "whmpress" ) ?></button>
								</td>
							</tr>
						</table>
					</form>
				<?php endif; ?>
			</div>
			<div id="debug_info">
				<h3 class="whmp-sub-head"><?php _e( "Debug Info", "whmpress" ) ?></h3>
				<table class="fancy" style="width: 100%;">
					<tr>
						<th colspan="2"><?php _e( "WordPress", "whmpress" ) ?></th>
					</tr>
					<tr>
						<td class="row-title"><?php _e( "Site URL", "whmpress" ) ?></td>
						<td><?php echo site_url(); ?></td>
					</tr>
					<tr>
						<td class="row-title"><?php _e( "Home URL", "whmpress" ) ?></td>
						<td><?php echo home_url(); ?></td>
					</tr>
					<tr>
						<td class="row-title"><?php _e( "WordPress Version", "whmpress" ) ?></td>
						<td><?php bloginfo( 'version' ); ?></td>
					</tr>
					<tr>
						<td class="row-title"><?php _e( "WordPress Multisite", "whmpress" ) ?></td>
						<td><?php if ( is_multisite() ) {
								echo __( 'Yes', 'whmpress' );
							} else {
								echo __( 'No', 'whmpress' );
							} ?></td>
					</tr>
					<tr>
						<td class="row-title"><?php _e( "WordPress Language", "whmpress" ) ?></td>
						<td><?php echo get_locale(); ?></td>
					</tr>
					<tr>
						<td class="row-title"><?php _e( "WordPress Debug Mode", "whmpress" ) ?></td>
						<td><?php if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
								_e( 'Yes', 'whmpress' );
							} else {
								_e( 'No', 'whmpress' );
							} ?></td>
					</tr>
					<tr>
						<td class="row-title"><?php _e( "WordPress Active Plugins", "whmpress" ) ?></td>
						<td><?php echo count( (array) get_option( 'active_plugins' ) ); ?></td>
					</tr>
					<tr>
						<td class="row-title"><?php _e( "WordPress Max Upload Size", "whmpress" ) ?></td>
						<td>
							<?php
							$wp_upload_max     = wp_max_upload_size();
							$server_upload_max = intval( str_replace( 'M', '', ini_get( 'upload_max_filesize' ) ) ) * 1024 * 1024;
							
							if ( $wp_upload_max <= $server_upload_max ) {
								echo size_format( $wp_upload_max );
							} else {
								echo '<span class="whmp_danger">' . sprintf( __( '%s (The server only allows %s)', 'whmpress' ), size_format( $wp_upload_max ), size_format( $server_upload_max ) ) . '</span>';
							}
							?>
						</td>
					</tr>
					<tr>
						<td class="row-title"><?php _e( "WordPress Memory Limit", "whmpress" ) ?></td>
						<td><?php echo WP_MEMORY_LIMIT; ?></td>
					</tr>
					
					<tr>
						<th colspan="2"><?php _e( "Server Info", "whmpress" ) ?></th>
					</tr>
					<tr>
						<td class="row-title"><?php _e( "PHP Version", "whmpress" ) ?></td>
						<td><?php if ( function_exists( 'phpversion' ) ) {
								echo esc_html( phpversion() );
							} ?></td>
					</tr>
					<tr>
						<td class="row-title"><?php _e( "Server Software", "whmpress" ) ?></td>
						<td><?php echo esc_html( @$_SERVER['SERVER_SOFTWARE'] ); ?></td>
					</tr>
					<tr>
						<td class="row-title"><?php _e( "MySQLi Extension", "whmpress" ); ?></td>
						<td><?php echo function_exists( 'mysqli_connect' ) ? __( "Installed", "whmpress" ) : "<span class='whmp_danger'>" . __( "Not Installed", "whmpress" ) . "</span>"; ?></td>
					</tr>
					<tr>
						<td class="row-title">cURL Extension</td>
						<td><?php echo function_exists( 'curl_version' ) ? __( "Installed", "whmpress" ) : "<span class='whmp_danger'>" . __( "Not Installed", "whmpress" ) . "</span>"; ?></td>
					</tr>
					
					<tr>
						<th colspan="2">WHMpress Info</th>
					</tr>
					<tr>
						<td class="row-title"><?php _e( "Version", "whmpress" ) ?></td>
						<td><?php echo WHMP_VERSION ?></td>
					</tr>
					<tr>
						<td class="row-title"><?php _e( "Last Synced", "whmpress" ) ?></td>
						<td><?php $last_synced = get_option( "sync_time" );
							if ( $last_synced == "" ) {
								echo "<span class='whmp_danger'>Not yet synced</span>";
							} else {
								echo $last_synced;
							}
							?></td>
					</tr>
					<tr>
						<td class="row-title"><?php _e( "WHMCS Version", "whmpress" ) ?></td>
						<td><?php echo $wpdb->get_var( "SELECT `value` FROM `" . whmp_get_configuration_table_name() . "` WHERE `setting`='Version'" ); ?></td>
					</tr>
					<tr>
						<td class="row-title"><?php _e( "Company Name", "whmpress" ) ?></td>
						<td><?php echo $wpdb->get_var( "SELECT `value` FROM `" . whmp_get_configuration_table_name() . "` WHERE `setting`='CompanyName'" ); ?></td>
					</tr>
					<tr>
						<td class="row-title"><?php _e( "Email address", "whmpress" ) ?></td>
						<td><?php echo $wpdb->get_var( "SELECT `value` FROM `" . whmp_get_configuration_table_name() . "` WHERE `setting`='email'" ); ?></td>
					</tr>
					<tr>
						<td class="row-title"><?php _e( "Domains", "whmpress" ) ?></td>
						<td><?php echo $wpdb->get_var( "SELECT COUNT(*) FROM `" . whmp_get_domain_pricing_table_name() . "`" ); ?></td>
					</tr>
					<tr>
						<td class="row-title"><?php _e( "Products", "whmpress" ) ?></td>
						<td><?php echo $wpdb->get_var( "SELECT COUNT(*) FROM `" . whmp_get_products_table_name() . "`" ); ?></td>
					</tr>
					<tr>
						<td class="row-title"><?php _e( "Product Groups", "whmpress" ) ?></td>
						<td><?php echo $wpdb->get_var( "SELECT COUNT(*) FROM `" . whmp_get_product_group_table_name() . "`" ); ?></td>
					</tr>
					<tr>
						<td class="row-title"><?php _e( "Currencies", "whmpress" ) ?></td>
						<td><?php echo $wpdb->get_var( "SELECT COUNT(*) FROM `" . whmp_get_currencies_table_name() . "`" ); ?></td>
					</tr>
					<tr>
						<td class="row-title"><?php _e( "WHMCS URL", "whmpress" ) ?></td>
						<td><?php echo whmp_get_installation_url(); ?></td>
					</tr>
					<?php if ( is_plugin_active( 'WHMpress_Client_Area/client-area.php' ) ): ?>
						<tr>
							<td class="row-title"><?php _e( "Client Area URL", "whmpress" ) ?></td>
							<td><?php
								$page = $WHMP->get_current_client_area_page();
								if ( substr( $page, 0, 4 ) == "http" ) {
									echo $page;
								} else {
									echo get_bloginfo( "url" ) . "/" . $page;
								}
								?></td>
						</tr>
					<?php endif; ?>
					
					<tr>
						<th colspan="2"><?php _e( "Plugins", "whmpress" ) ?></th>
					</tr>
					<tr>
						<td class="row-title"><?php _e( "Installed", "whmpress" ) ?></td>
						<td>
							<?php
							$active_plugins = (array) get_option( 'active_plugins', [] );
							
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
						<th colspan="2"><?php _e( "Theme", "whmpress" ) ?></th>
					</tr>
					<tr>
						<td class="row-title"><?php _e( "Theme Name", "whmpress" ) ?></td>
						<td><?php
							$active_theme = wp_get_theme();
							echo $active_theme->Name;
							?></td>
					</tr>
					<tr class="alt">
						<td class="row-title"><?php _e( "Theme Version", "whmpress" ) ?></td>
						<td><?php
							echo $active_theme->Version;
							?></td>
					</tr>
					<tr>
						<td class="row-title"><?php _e( "Theme Author URL", "whmpress" ) ?></td>
						<td><?php
							echo $active_theme->{'Author URI'};
							?></td>
					</tr>
					<tr class="alt">
						<td class="row-title"><?php _e( "Is Child Theme", "whmpress" ) ?></td>
						<td><?php echo is_child_theme() ? __( 'Yes', 'whmpress' ) : __( 'No', 'whmpress' ); ?></td>
					</tr>
					<?php
					if ( is_child_theme() ) :
						$parent_theme = wp_get_theme( $active_theme->Template );
						?>
						<tr>
							<td class="row-title"><?php _e( "Parent Theme Name", "whmpress" ) ?></td>
							<td><?php echo $parent_theme->Name; ?></td>
						</tr>
						<tr class="alt">
							<td class="row-title"><?php _e( "Parent Theme Version", "whmpress" ) ?></td>
							<td><?php echo $parent_theme->Version; ?></td>
						</tr>
						<tr>
							<td class="row-title"><?php _e( "Parent Theme Author URL", "whmpress" ) ?></td>
							<td><?php
								echo $parent_theme->{'Author URI'};
								?></td>
						</tr>
					<?php endif; ?>
					<tr>
						<th colspan="2"><?php _e( "Addons", "whmpress" ) ?></th>
					</tr>
					<?php if ( is_plugin_active( 'WHMpress_Client_Area/client-area.php' ) ): global $plugin_data_ca; ?>
					<tr>
						<td class="row-title"><?php echo @$plugin_data_ca["Name"]; ?></td>
						<td>v<?php echo @$plugin_data_ca["Version"]; ?></td>
						<?php else: ?>
							<td></td>
							<td><span class="whmp_danger">No addon installed</span></td>
						<?php endif; ?>
				</table>
				<br>
				<div style="text-align: center;">
					<input onclick="SendInfo()" type="button" class="button button-red"
					       value="Send this information to Author"/>
				</div>
			</div>
		</div>
		
		
		<span id="settingsloaded"></span>
		<script>
			/*jQuery(function(){
			 var editor = CodeMirror.fromTextArea(document.getElementById("whmp_custom_css"), {
			 lineNumbers: true,
			 mode: 'css'
			 });
			 editor.setSize('900','500');
			 });*/
		</script>
	<?php endif; ?>
</div>

<script type="text/javascript">
	jQuery(document).ready(function ()
	{
		jQuery('#whmp-tabs').easytabs();
		jQuery('#whmp-default-tabs').easytabs();
		jQuery('#whmp-dsearch-tabs').easytabs();
		/*jQuery('ul.tabs').each(function(){
		 // For each set of tabs, we want to keep track of
		 // which tab is active and it's associated content
		 var $active, $content, $links = jQuery(this).find('a');
		 
		 // If the location.hash matches one of the links, use that as the active tab.
		 // If no match is found, use the first link as the initial active tab.
		 $active = jQuery($links.filter('[href="'+location.hash+'"]')[0] || $links[0]);
		 $active.addClass('active');
		 
		 $content = jQuery($active[0].hash);
		 
		 // Hide the remaining content
		 $links.not($active).each(function () {
		 jQuery(this.hash).hide();
		 });
		 
		 // Bind the click event handler
		 jQuery(this).on('click', 'a', function(e){
		 // Make the old tab inactive.
		 $active.removeClass('active');
		 
		 $content.hide();
		 
		 // Update the variables with the new link and content
		 $active = jQuery(this);
		 $content = jQuery(this.hash);
		 
		 // Make the tab active.
		 $active.addClass('active');
		 $content.show();
		 
		 // Prevent the anchor's default click action
		 e.preventDefault();
		 });
		 });*/
	});
	function UnVerify ()
	{
		if (!confirm("Are you sure you want to unverify your purchase?")) return;
		jQuery(".full_page_loader").show();
		jQuery.post(ajaxurl, {'action': 'whmp_unverify'}, function (response)
		{
			if (response == "OK") {
				window.location.reload();
			}
			else {
				alert(response);
			}
			jQuery(".full_page_loader").hide();
		});
		return false;
	}
	function Verify ()
	{
		jQuery(".full_page_loader").show();
		var data = "purchase_code=" + jQuery("#fpurchase_code").val() + "&email=" + jQuery("#femail").val();
		data += "&action=whmp_verify";
		jQuery.post(ajaxurl, data, function (response)
		{
			if (response == "OK") {
				window.location.reload();
			}
			else {
				jQuery(".full_page_loader").hide();
				alert(response);
			}
		});
		return false;
	}
	function AddTR ()
	{
		jQuery("#country_table tbody").append("<?php echo $newTR ?>");
	}
	function Remove (tthis)
	{
		jQuery(tthis).parent().parent().remove();
	}
	function SendInfo ()
	{
		if (!confirm("Are you sure you want to send this information to Author for support?")) return;
		jQuery(".full_page_loader").show();
		jQuery.post(ajaxurl, {'action': 'send_info_to_author'}, function (response)
		{
			jQuery(".full_page_loader").hide();
			if (response == "OK") {
				alert("Thank you, your debug information has been sent to Author");
			}
			else {
				alert(response);
			}
		});
		return false;
	}
</script>