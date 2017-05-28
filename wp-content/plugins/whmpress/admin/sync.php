<div class="wrap">
	<?php
	if ( ( is_dir( get_template_directory() . "/whmpress/" ) || is_dir( WHMP_PLUGIN_PATH . "themes/" . basename( get_template_directory() ) ) ) && get_option( 'load_sytle_orders' ) == '' ) {
		?>
		<div class="notice notice-success is-dismissible">
			<h3>WHMPress</h3>
			<p><?php _e( 'Matching Templates found for your active theme <b>' . basename( get_template_directory() ) . '</b>. You can enable <b>' . basename( get_template_directory() ) . '</b> support by selecting Template Source from <a href="admin.php?page=whmp-settings#styles">Settings > Styles</a>.', 'whmpress' ); ?></p>
		</div>
		<?php
	}
	?>
	
	<!--<div class="whmp-main-title"><span class="whmp-title">WHMpress</span> <?php /*_e("Sync WHMCS", "whmpress") */ ?></div>-->
	<h2 class="nav-tab-wrapper">
		<a class="nav-tab" href="<?php echo admin_url() ?>admin.php?page=whmp-dashboard">Dashboard</a>
		<a class="nav-tab" href="<?php echo admin_url() ?>admin.php?page=whmp-services">Products/Services</a>
		<a class="nav-tab" href="<?php echo admin_url() ?>admin.php?page=whmp-settings">Settings</a>
		<a class="nav-tab nav-tab-active" href="<?php echo admin_url() ?>admin.php?page=whmp-sync">Sync WHMCS</a>
		<a class="nav-tab" href="<?php echo admin_url() ?>admin.php?page=whmp-extensions">Addons</a>
	</h2>
	
	<div class="updated">
		<p><?php _e( "WHMpress makes your life easy by fetching all the services related information from WHMCS. Once you Sync WHMCS, all the services are fetched from WHMCS and are cached and made available in WHMpress for quick access", "whmpress" ) ?></p>
	</div>
	<?php
	if ( @$_GET["settings-updated"] == "true" ) {
		echo whmp_fetch_data();
	}
	$options = get_option( 'whmp_settings' );
	?>
	<form method="post" action="options.php">
		<?php settings_fields( 'whmp_sync_settings' );
		do_settings_sections( 'whmp_sync_settings' );
		?>
		<input type="hidden" name="sync_run" value="1"/>
		<table class="form-table">
			<tr valign="top">
				<td style="width:30%;" scope="row"><?php _e( "WHMCS Database Server", "whmpress" ) ?></td>
				<td><input style="width:100%;" required="required" name="db_server"
				           value="<?php echo esc_attr( get_option( 'db_server' ) ); ?>"/></td>
			</tr>
			<tr valign="top">
				<td scope="row"><?php _e( "WHMCS Database Name", "whmpress" ) ?></td>
				<td><input style="width:100%;" required="required" name="db_name"
				           value="<?php echo esc_attr( get_option( 'db_name' ) ); ?>"/></td>
			</tr>
			<tr valign="top">
				<td scope="row"><?php _e( "WHMCS Database User", "whmpress" ) ?></td>
				<td><input style="width:100%;" required="required" name="db_user"
				           value="<?php echo esc_attr( get_option( 'db_user' ) ); ?>"/></td>
			</tr>
			<tr valign="top">
				<td scope="row"><?php _e( "WHMCS Database Password", "whmpress" ) ?></td>
				<td><input value="<?php echo get_option( "db_pass" ) ?>" type="password" style="width:80%;"
				           name="db_pass"/>
					<label><input <?php echo get_option( 'whmp_save_pwd' ) == "1" ? "checked=checked" : "" ?>
							type="checkbox" name="whmp_save_pwd" value="1"/> Save password</label>
				</td>
			</tr>
			<tr>
				<td colspan="2" style="color:#CC0000;font-style:italic;text-align:right;"><b>Note:</b> Saving password
					is not recommended on production server.
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><?php submit_button( __( 'Sync WHMCS', "whmpress" ), 'primary', 'wpdocs-save-settings', true ); ?></td>
			</tr>
		</table>
	</form>
</div>