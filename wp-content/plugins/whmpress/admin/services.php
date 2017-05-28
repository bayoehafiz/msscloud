<?php
/**
 * @package Admin
 * @todo    Services page for WHMpress admin panel
 */

if ( ! defined( 'WHMP_VERSION' ) ) {
	wp_die( "Direct acces not allowed by WHMPress", "Forbidden" );
}
$WHMPress = new WHMpress();

if ( ( is_dir( get_template_directory() . "/whmpress/" ) || is_dir( WHMP_PLUGIN_PATH . "themes/" . basename( get_template_directory() ) ) ) && get_option( 'load_sytle_orders' ) == '' ) {
	?>
	<div class="notice notice-success is-dismissible">
		<h3>WHMPress</h3>
		<p><?php _e( 'Matching Templates found for your active theme <b>' . basename( get_template_directory() ) . '</b>. You can enable <b>' . basename( get_template_directory() ) . '</b> support by selecting Template Source from <a href="admin.php?page=whmp-settings#styles">Settings > Styles</a>.', 'whmpress' ); ?></p>
	</div>
	<?php
}

global $wpdb;
$WHMP = new WHMPress();
?>
<div class="wrap">
	<h2 class="nav-tab-wrapper">
		<a class="nav-tab"
		   href="<?php echo admin_url() ?>admin.php?page=whmp-dashboard"><?php echo __( 'Dashboard', 'whmpress' ); ?></a>
		<a class="nav-tab nav-tab-active"
		   href="<?php echo admin_url() ?>admin.php?page=whmp-services"><?php echo __( 'Products/Services', 'whmpress' ); ?></a>
		<a class="nav-tab"
		   href="<?php echo admin_url() ?>admin.php?page=whmp-settings"><?php echo __( 'Settings', 'whmpress' ); ?></a>
		<a class="nav-tab"
		   href="<?php echo admin_url() ?>admin.php?page=whmp-sync"><?php echo __( 'Sync WHMCS', 'whmpress' ); ?></a>
		<a class="nav-tab"
		   href="<?php echo admin_url() ?>admin.php?page=whmp-extensions"><?php echo __( 'Addons', 'whmpress' ); ?></a>
	</h2>
	<!--<div class="whmp-main-title"><span class="whmp-title">WHMpress</span> <?php /*_e("Services", "whmpress") */ ?></div>-->
	<?php if ( ! $WHMPress->WHMpress_synced() ): ?>
		
		<div class="error">
			<p>
				<b>WHMPress <?php _e( "Error", "whmpress" ) ?></b>
				<?php _e( "WHMCS is not Synced", "whmpress" ) ?> <a
					href="admin.php?page=whmp-sync"><?php _e( "Please Sync WHMCS", "whmpress" ) ?></a>.
			</p>
		</div>
	
	<?php else: ?>
		
		<div class="updated">
			<p>
				<?php _e( "This page has all services list", "whmpress" ) ?>
			</p>
		</div>
		
		<?php
		$Q    = "SELECT DISTINCT `type` FROM `" . whmp_get_products_table_name() . "` WHERE `type`<>''";
		$tabs = whmp_get_service_types();
		?>
		
		<style>
			.mytr, .mytr th, .mytr th:hover {
				background-color: #666666 !important;
				color: #FFFFFF !important;
			}
		</style>
		
		<?php if ( isset( $_POST['save_values'] ) ) {
			unset( $_POST['save_values'] );
			foreach ( $_POST as $key => $value ) {
				update_option( $key, $value );
			}
			//show_array($_POST);
		} ?>
		
		<div class="settings-wrap">
			<div id="whmp-services-tabs" class="tab-container">
				<ul class='etabs'>
					<?php $k = 1;
					foreach ( $tabs as $key => $tab ): ?>
						<li class='tab'><a href='#tab<?php echo $k ?>'><?php echo $tab ?></a></li>
						<?php $k ++; endforeach; ?>
					<li class='tab'><a href="#domains"><?php _e( "Domains", "whmpress" ) ?></a></li>
					<li class='tab'><a href="#currencies"><?php _e( "Currencies", "whmpress" ) ?></a></li>
				</ul>
				
				<form name="" method="post">
					<input type="hidden" name="save_values" value="1">
					<?php $k = 1;
					foreach ( $tabs as $key => $tab ) { ?>
						<div id='tab<?php echo $k ?>'>
							<table class="fancy" style="width:100%">
								<thead>
								<tr class="mytr">
									<th style="width:25px"><?php _e( "ID", "whmpress" ) ?></th>
									<th style="width:30%"><?php _e( "Name", "whmpress" ) ?></th>
									<th><?php _e( "Description", "whmpress" ) ?></th>
								</tr>
								</thead>
								<tbody>
								<?php
								$groups = $wpdb->get_results( "SELECT `id`,`name`,`hidden` FROM `" . whmp_get_product_group_table_name() . "` ORDER BY `order`", ARRAY_A );
								foreach ( $groups as $group ) {
									$rows = $wpdb->get_results( "SELECT `id`, `name`,`description`,`hidden` FROM `" . whmp_get_products_table_name() . "` WHERE `gid`='{$group["id"]}' AND `type`='{$key}' ORDER BY `name`" );
									if ( is_array( $rows ) && sizeof( $rows ) > 0 ) {
										$hidden = $group["hidden"] == "on" ? " (<i>" . __( 'Hidden', 'whmpress' ) . "</i>)" : "";
										echo "<tr><th colspan='3' style='text-align:center'>Group: " . $group["name"] . " ({$group["id"]}) $hidden</th></tr>";
										foreach ( $rows as $row ): ?>
											<tr>
												<td><?php echo $row->id; ?></td>
												<td>
													<?php echo $row->name;
													if ( $row->hidden == "on" )
														echo " (<i>" . __( 'Hidden', 'whmpress' ) . "<i>)" ?>
													<br>
													<?php $field = "whmpress_product_" . $row->id . "_name_" . $WHMP->get_current_language(); ?>
													<input style="padding:5px; width: 100%;"
													       placeholder="<?php _e( "Type custom name for current language" ); ?>"
													       type="text" name="<?php echo $field; ?>"
													       value="<?php echo esc_attr( get_option( $field ) ) ?>">
												</td>
												<td>
													<div style="float:left;min-width:250px;">
														<?php echo $row->description; ?>&nbsp;
													</div>
													<div style="float:left;margin-left:10px">
														<?php $field = "whmpress_product_" . $row->id . "_desc_" . $WHMP->get_current_language(); ?>
														<textarea name="<?php echo $field ?>" cols="30" rows="5"
														          placeholder="Description according to current language"><?php echo esc_attr( get_option( $field ) ); ?></textarea>
													</div>
													<div style="float:left;margin-left:10px">
														<?php $field = "whmpress_product_" . $row->id . "_custom_desc_" . $WHMP->get_current_language(); ?>
														<textarea name="<?php echo $field ?>" cols="30" rows="5"
														          placeholder="Custom description according to current language"><?php echo esc_attr( get_option( $field ) ); ?></textarea>
													</div>
												</td>
											</tr>
										<?php endforeach;
									}
								} ?>
								</tbody>
							</table>
							<button class="button button-primary"><?php _e( "Save", "whmpress" ); ?></button>
						</div>
						<?php $k ++;
					} ?>
					<div id="domains">
						<?php $Q = "SELECT `id`,`extension` FROM `" . whmp_get_domain_pricing_table_name() . "` ORDER BY `order`";
						$rows    = $wpdb->get_results( $Q, ARRAY_A ); ?>
						<div> <?php echo __( 'You are selling', 'whmpress' ) . count( $rows ); ?> domain(s)</div>
						<table class="fancy" style="width:100%">
							<thead>
							<tr>
								<th><?php _e( "ID", "whmpress" ) ?></th>
								<th><?php _e( "Domain", "whmpress" ) ?></th>
							</tr>
							</thead>
							<tbody>
							<?php foreach ( $rows as $row ): ?>
								<tr>
									<td><?php echo $row["id"] ?></td>
									<td><?php echo $row["extension"] ?></td>
								</tr>
							<?php endforeach; ?>
							</tbody>
						</table>
					</div>
					<div id="currencies">
						<?php $Q = "SELECT * FROM `" . whmp_get_currencies_table_name() . "`";
						$rows    = $wpdb->get_results( $Q, ARRAY_A ); ?>
						<button class="button button-primary"><?php _e( "Save", "whmpress" ); ?></button>
						<table class="fancy" style="width:100%">
							<thead>
							<tr>
								<th><?php _e( "ID", "whmpress" ) ?></th>
								<th><?php _e( "Code", "whmpress" ) ?></th>
								<th><?php _e( "Prefix", "whmpress" ) ?></th>
								<th><?php _e( "Suffix", "whmpress" ) ?></th>
							</tr>
							</thead>
							<tbody>
							<?php foreach ( $rows as $row ): ?>
								<tr <?php if ( $row["default"] == "1" )
									echo "style='font-weight:bold'" ?>>
									<td><?php echo $row["id"] ?><?php if ( $row["default"] == "1" )
											echo " <sup>[default]</sup>" ?></td>
									<td>
										<?php echo $row["code"] ?>
										<?php $field = "whmpress_currencies_" . trim( $row['code'] ) . "_code_" . $WHMP->get_current_language(); ?>
										<input placeholder="<?php _e( "Altername code", "whmpress" ); ?>" type="text"
										       name="<?php echo $field ?>"
										       value="<?php echo esc_attr( get_option( $field ) ); ?>">
									</td>
									<td>
										<?php echo $row["prefix"] ?>
										<?php $field = "whmpress_currencies_" . trim( $row['prefix'] ) . "_prefix_" . $WHMP->get_current_language(); ?>
										<input placeholder="<?php _e( "Altername pefix", "whmpress" ); ?>" type="text"
										       name="<?php echo $field ?>"
										       value="<?php echo esc_attr( get_option( $field ) ); ?>">
									</td>
									<td>
										<?php echo( $row["suffix"] ); ?>
										<?php $field = "whmpress_currencies_" . trim( $row['suffix'] ) . "_suffix_" . $WHMP->get_current_language(); ?>
										<input placeholder="<?php _e( "Altername suffix", "whmpress" ); ?>" type="text"
										       name="<?php echo $field ?>"
										       value="<?php echo esc_attr( get_option( $field ) ); ?>">
									</td>
								</tr>
							<?php endforeach; ?>
							</tbody>
						</table>
						<button class="button button-primary"><?php _e( "Save", "whmpress" ); ?></button>
					</div>
				</form>
			</div>
		</div>
	<?php endif; ?>
</div>


<script type="text/javascript">
	jQuery(document).ready(function ()
	{
		jQuery('#whmp-services-tabs').easytabs();
	});
</script>