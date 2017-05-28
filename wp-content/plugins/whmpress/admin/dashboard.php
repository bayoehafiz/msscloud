<?php
/**
 * @package Admin
 * @todo    Dashboard page for WHMpress admin panel
 */

if ( ! defined( 'WHMP_VERSION' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}
global $wpdb;
$WHMPress = new WHMpress();

if ( isset( $_GET["removeSettings"] ) ) {
	// Removing all tables and data.
	#echo __("Removing cached data","whmpress")." ...<br />";
	global $Tables;
	foreach ( $Tables as $table ) {
		$table_name_function = 'whmp_get_' . $table . '_table_name';
		$Q                   = "DROP TABLE `" . $table_name_function() . "`";
		$wpdb->query( $Q );
	}
	
	echo "<div class='updated'>";
	echo "<h3>" . _e( 'Done', 'whmpress' ) . "</h3>";
	echo "<p>" . _e( 'WHMpress cached data removed ....', 'whmpress' ) . "</p></div>";
	
	// Removing settings data.
	// Coming soon.
}

if ( ( is_dir( get_template_directory() . "/whmpress/" ) || is_dir( WHMP_PLUGIN_PATH . "themes/" . basename( get_template_directory() ) ) ) && get_option( 'load_sytle_orders' ) == '' ) {
	?>
	<div class="notice notice-success is-dismissible">
		<h3>WHMPress</h3>
		<p><?php _e( 'Matching Templates found for your active theme <b>' . basename( get_template_directory() ) . '</b>. You can enable <b>' . basename( get_template_directory() ) . '</b> support by selecting Template Source from <a href="admin.php?page=whmp-settings#styles">Settings > Styles</a>.', 'whmpress' ); ?></p>
	</div>
	<?php
}
?>
<div class="full_page_loader">
	<div class="whmp_loader"><?php _e( "Loading", "whmpress" ) ?>...</div>
</div>
<div class="wrap">
	<h2 class="nav-tab-wrapper">
		<a class="nav-tab nav-tab-active" href="<?php echo admin_url() ?>admin.php?page=whmp-dashboard">Dashboard</a>
		<a class="nav-tab" href="<?php echo admin_url() ?>admin.php?page=whmp-services">Products/Services</a>
		<a class="nav-tab" href="<?php echo admin_url() ?>admin.php?page=whmp-settings">Settings</a>
		<a class="nav-tab" href="<?php echo admin_url() ?>admin.php?page=whmp-sync">Sync WHMCS</a>
		<a class="nav-tab" href="<?php echo admin_url() ?>admin.php?page=whmp-extensions">Addons</a>
	</h2>
	
	<!--<div class="whmp-main-title"><span class="whmp-title">WHMpress</span> <?php /*_e("Dashboard", "whmpress") */ ?></div>-->
	<div>
		<div class="whmpress_panel whmpress_50">
			<?php if ( $WHMPress->verified_purchase() ): ?>
				<div id="div3">
					<h3><?php _e( "Registered WHMpress", "whmpress" ) ?></h3>
					<p style="color:#1B6B34;font-weight:bold">
						<?php _e( "Congratulation! Your product is verified with us", "whmpress" ) ?>
					<div style="clear: both;"></div>
					</p>
				</div>
				<div id="div4" style="display: none;">
					<h3><?php _e( "Verify your purchase", "whmpress" ) ?></h3>
					<p>
					<form onsubmit="return Verify(this)">
						<table style="width:100%">
							<tr>
								<td colspan="3"><?php _e( "Email required for providing support for this product", "whmpress" ) ?></td>
							</tr>
							<tr>
								<td><input required="required" style="width:100%;" type="email" name="email"
								           placeholder="<?php _e( "Email address", "whmpress" ) ?>"
								           value="<?php echo get_option( "admin_email" ) ?>"/></td>
								<td><input required="required" style="width:100%;" type="text" name="purchase_code"
								           placeholder="<?php _e( "Purchase Code", "whmpress" ) ?>"/></td>
								<td>
									<button style="width:100%;"
									        class="button button-primary"><?php _e( "Verify", "whmpress" ) ?></button>
								</td>
							</tr>
						</table>
					</form>
					</p>
				</div>
			<?php else: ?>
				<div style="display: none;" id="div1">
					<h3><?php _e( "Verified Product", "whmpress" ) ?></h3>
					<p style="color:#1B6B34;font-weight:bold">
						<?php _e( "Congratulation! Your product is verified with us", "whmpress" ) ?>
					</p>
				</div>
				<div id="div2">
					<h3><?php _e( "Verify your purchase", "whmpress" ) ?></h3>
					<p>
					<form onsubmit="return Verify(this)">
						<table style="width:100%">
							<tr>
								<td colspan="3"><?php _e( "Email required for providing support for this product", "whmpress" ) ?></td>
							</tr>
							<tr>
								<td><input required="required" style="width:100%;" type="email" name="email"
								           placeholder="<?php _e( "Email address", "whmpress" ) ?>"
								           value="<?php echo get_option( "admin_email" ) ?>"/></td>
								<td><input required="required" style="width:100%;" type="text" name="purchase_code"
								           placeholder="<?php _e( "Purchase Code", "whmpress" ) ?>"/></td>
								<td>
									<button style="width:100%;"
									        class="button button-primary"><?php _e( "Verify", "whmpress" ) ?></button>
								</td>
							</tr>
						</table>
					</form>
					</p>
				</div>
			<?php endif; ?>
			<div>
				<h3><?php _e( "General", "whmpress" ) ?></h3>
				<p><?php _e( "Watch this video to quickly learn about the use of WHMpress", "whmpress" ) ?></p>
				<p><a href="https://whmpress.com/documentation/#document-4" target="_blank"
				      class="button button-primary"><?php _e( "Introduction Tour", "whmpress" ) ?></a></p>
			</div>
			<div>
				<h3><?php _e( "Reset WHMpress", "whmpress" ) ?></h3>
				<p style="color: #CC0000;"><?php _e( "If you want to reset all WHMpress as it was newly installed, press this button", "whmpress" ) ?></p>
				<p style="text-align: right;">
					<button <?php if ( ! $WHMPress->WHMpress_synced() )
						echo 'disabled="disabled"' ?> onclick="Reset()"
					                                  class="button button-red"><?php _e( "Reset WHMpress", "whmpress" ) ?></button>
				</p>
			</div>
		</div>
		<div class="whmpress_panel whmpress_50">
			<?php if ( ! $WHMPress->WHMpress_synced() ): ?>
				<div>
					<h3><?php _e( "System Info", "whmpress" ) ?></h3>
					<p><?php _e( "No system info available", "whmpress" ) ?>, <a
							href="admin.php?page=whmp-sync"><?php _e( "Please Sync WHMCS", "whmpress" ) ?></a></p>
				</div>
			<?php else: ?>
				<div>
					<h3><?php _e( "System Info", "whmpress" ) ?></h3>
					<p><strong>WHMpress <?php _e( "Version", "whmpress" ) ?></strong>: <?php echo WHMP_VERSION ?><br/>
						<strong>WHMCS <?php _e( "Version", "whmpress" ) ?></strong>: <?php echo $wpdb->get_var( "SELECT `value` FROM `" . whmp_get_configuration_table_name() . "` WHERE `setting`='Version'" ); ?>
						<br/>
						<strong><?php _e( "Company Name", "whmpress" ) ?></strong>: <?php echo $wpdb->get_var( "SELECT `value` FROM `" . whmp_get_configuration_table_name() . "` WHERE `setting`='CompanyName'" ); ?>
						<br/>
						<strong><?php _e( "Language", "whmpress" ) ?></strong>: <?php echo $wpdb->get_var( "SELECT `value` FROM `" . whmp_get_configuration_table_name() . "` WHERE `setting`='Language'" ); ?>
						<br/>
						<strong><?php _e( "Email", "whmpress" ) ?></strong>: <?php echo $wpdb->get_var( "SELECT `value` FROM `" . whmp_get_configuration_table_name() . "` WHERE `setting`='email'" ); ?>
						<br/>
						<strong><?php _e( "Last Synced", "whmpress" ) ?></strong>: <span
							style="color: #CC0000;"><?php echo get_option( 'sync_time' ) ?></span>
					</p>
					<p>
						<strong><?php _e( "Domains", "whmpress" ) ?></strong>: <?php echo $wpdb->get_var( "SELECT COUNT(*) FROM `" . whmp_get_domain_pricing_table_name() . "`" ); ?>
						<br/>
						<strong><?php _e( "Products", "whmpress" ) ?></strong>: <?php echo $wpdb->get_var( "SELECT COUNT(*) FROM `" . whmp_get_products_table_name() . "`" ); ?>
						<br/>
						<strong><?php _e( "Product Groups", "whmpress" ) ?></strong>: <?php echo $wpdb->get_var( "SELECT COUNT(*) FROM `" . whmp_get_product_group_table_name() . "`" ); ?>
						<br/>
						<strong><?php _e( "Currencies", "whmpress" ) ?></strong>: <?php echo $wpdb->get_var( "SELECT COUNT(*) FROM `" . whmp_get_currencies_table_name() . "`" ); ?>
					</p>
				</div>
			<?php endif; ?>
			
			<?php
			$whmp_ca  = is_plugin_active( 'WHMpress_Client_Area/client-area.php' );
			$whmp_grp = is_plugin_active( 'whmpress_comp_tables/index.php' );
			if ( $whmp_ca || $whmp_grp ) {
				global $plugin_data_ca, $plugin_data_grp;
				?>
				<div>
					<h3><?php _e( "Addons", "whmpress" ) ?></h3>
					
					<?php if ( $whmp_ca ) { ?>
						<p><?php _e( "You have installed", "whmpress" ) ?>
							(<strong><?php echo @$plugin_data_ca["Name"]; ?> -
								v<?php echo @$plugin_data_ca["Version"]; ?></strong>)</p>
					<?php } ?>
					
					<?php if ( $whmp_grp ) { ?>
						<p><?php _e( "You have installed", "whmpress" ) ?>
							(<strong><?php echo @$plugin_data_grp["Name"]; ?> -
								v<?php echo @$plugin_data_grp["Version"]; ?></strong>)</p>
					<?php } ?>
				</div>
			<?php } ?>
		</div>
	</div>
</div>

<script>
	function Reset ()
	{
		if (!confirm("<?php _e( "Warning! All cached data and all settings of WHMpress will removed", "whmpress" ) ?>.\n\n\n<?php _e( "Are you sure", "whmpress" ) ?>")) return false;
		window.location.href = "admin.php?page=whmp-dashboard&removeSettings";
	}
	function Verify (form)
	{
		jQuery(".full_page_loader").show();
		var data = jQuery(form).serialize();
		data += "&action=whmp_verify";
		jQuery.post(ajaxurl, data, function (response)
		{
			if (response == "OK") {
				jQuery("#div1,#div2").toggle();
				jQuery("#not_whmp").remove();
			}
			else {
				alert(response);
			}
			jQuery(".full_page_loader").hide();
		});
		return false;
	}
</script>