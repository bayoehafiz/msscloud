<div class="settings-wrap">
	<div id="whmp-default-tabs" class="tab-container">
		<ul class='etabs'>
			<li class='tab'><a href="#Price1"><?php _e( "Price", "whmpress" ) ?></a></li>
			<li class='tab'><a href="#DomainPrice1"><?php _e( "Domain Price", "whmpress" ) ?></a></li>
			<li class='tab'><a href="#PriceMatrix"><?php _e( "Price Matrix", "whmpress" ) ?></a></li>
			<li class='tab'><a href="#PriceMatrixDomain"><?php _e( "Price Matrix Domain", "whmpress" ); ?></a></li>
			<li class='tab'><a href="#Combo1"><?php _e( "Order Combo", "whmpress" ); ?></a></li>
			<li class='tab'><a href="#OrderButton"><?php _e( "Order Button", "whmpress" ); ?></a></li>
			<li class='tab'><a href="#PricingTable"><?php _e( "Pricing Table", "whmpress" ); ?></a></li>
			<li class='tab'><a href="#DomainSearch"><?php _e( 'Domain Search', 'whmpress' ); ?></a></li>
			<li class='tab'><a href="#DomainSearchAjax"><?php _e( 'Domain Search Ajax', 'whmpress' ); ?></a></li>
			<!--<li class='tab'><a href="#DomainSearchAjaxResult"><?php /*_e('Domain Search Ajax Result','whmpress');*/ ?></a></li>-->
			<li class='tab'><a href="#DomainSearchBulk"><?php _e( 'Domain Search Bulk', 'whmpress' ); ?></a></li>
			<li class='tab'><a href="#DomainWhoIS"><?php _e( 'Domain WhoIS', 'whmpress' ); ?></a></li>
			<li class='tab'><a href="#OrderLink"><?php _e( 'Order Link', 'whmpress' ); ?></a></li>
			<li class='tab'><a href="#Description">Description</a></li>
		</ul>
		
		<div id="Price1">
			<h3><?php _e( "Price", "whmpress" ) ?></h3>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php _e( "Billing cycle", "whmpress" ) ?></th>
					<td>
						<?php
						$selected = whmpress_get_option( 'billingcycle' );
						$data     = [
							"monthly"      => __( "Monthly", "whmpress" ),
							"annually"     => __( "Annually", "whmpress" ),
							"quarterly"    => __( "Quarterly", "whmpress" ),
							"semiannually" => __( "Semi Annually", "whmpress" ),
							"biennially"   => __( "Biennially", "whmpress" ),
							"triennially"  => __( "Triennially", "whmpress" )
						];
						echo whmpress_draw_combo( $data, $selected, "billingcycle" );
						?>
						
						<span
							class="description"><?php _e( 'Select default billing cycle for price', 'whmpress' ); ?></span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( "Hide decimals", "whmpress" ) ?></th>
					<td>
						<?php
						$selected = whmpress_get_option( 'hide_decimal' );
						$data     = [ "No", "Yes" ];
						echo whmpress_draw_combo( $data, $selected, "hide_decimal" );
						?>
						
						<span class="description"><?php _e( 'Hide/Show decimals with price', 'whmpress' ); ?></span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( "Decimals", "whmpress" ) ?></th>
					<td>
						<?php
						$selected = whmpress_get_option( 'decimals' );
						$data     = [ 0, 1, 2, 3 ];
						echo whmpress_draw_combo( $data, $selected, "decimals" );
						?>
						
						<?php
						$selected = whmpress_get_option( 'decimals_tag' );
						$data     = [
							""    => "==No Tag==",
							"b"   => "Bold",
							"i"   => "Italic",
							"u"   => "Underline",
							"sup" => "Superscript",
							"sub" => "Subscript"
						];
						echo whmpress_draw_combo( $data, $selected, "decimals_tag" );
						?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( "Show prefix", "whmpress" ) ?></th>
					<td>
						<?php
						$selected = whmpress_get_option( 'prefix' );
						$data     = [
							"No"  => "Do not show",
							"Yes" => "Show prefix",
							"b"   => "Bold tag",
							"i"   => "Italic tag",
							"u"   => "Underline tag",
							"sup" => "Superscript tag",
							"sub" => "Subscript tag"
						];
						echo whmpress_draw_combo( $data, $selected, "prefix" );
						?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( "Show suffix", "whmpress" ) ?></th>
					<td>
						<?php
						$selected = whmpress_get_option( 'suffix' );
						$data     = [
							"No"  => "Do not show",
							"Yes" => "Show suffix",
							"b"   => "Bold tag",
							"i"   => "Italic tag",
							"u"   => "Underline tag",
							"sup" => "Superscript tag",
							"sub" => "Subscript tag"
						];
						echo whmpress_draw_combo( $data, $selected, "suffix" );
						?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( "Show duration", "whmpress" ) ?></th>
					<td>
						<?php
						$selected = whmpress_get_option( 'show_duration' );
						$data     = [
							"No"  => "Do not show",
							"Yes" => "Show duration",
							"b"   => "Bold tag",
							"i"   => "Italic tag",
							"u"   => "Underline tag",
							"sup" => "Superscript tag",
							"sub" => "Subscript tag"
						];
						echo whmpress_draw_combo( $data, $selected, "show_duration" );
						?>
						
						<?php
						$selected = whmpress_get_option( 'show_duration_as' );
						$data     = [
							"loing" => __( "Long Duration (Year)", "whmpress" ),
							"short" => __( "Short Duration (Yr)", "whmpress" )
						];
						echo whmpress_draw_combo( $data, $selected, "show_duration_as" );
						?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( "Price/Setup", "whmpress" ) ?></th>
					<td>
						<?php
						$selected = whmpress_get_option( 'price_type' );
						$data     = [ "price" => "Price", "setup" => "Setup Fee", "total" => "Price + Setup Fee" ];
						echo whmpress_draw_combo( $data, $selected, "price_type" );
						?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( "Convert price into monthly price", "whmpress" ) ?></th>
					<td>
						<?php
						$selected = whmpress_get_option( 'convert_monthly' );
						$data     = [ "0" => "No", "1" => "Yes" ];
						echo whmpress_draw_combo( $data, $selected, "convert_monthly" );
						?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( "Calculate configurable options", "whmpress" ) ?></th>
					<td>
						<?php
						$selected = whmpress_get_option( 'configureable_options' );
						$data     = [ "0" => "No", "1" => "Yes" ];
						echo whmpress_draw_combo( $data, $selected, "configureable_options" );
						?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( "String for config price", "whmpress" ) ?></th>
					<td>
						<input name="config_option_string"
						       value="<?php echo esc_attr( whmpress_get_option( 'config_option_string' ) ); ?>">
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( "Price/Tax", "whmpress" ) ?></th>
					<td>
						<?php
						$selected = whmpress_get_option( 'price_tax' );
						$data     = [
							"default"   => "WHMCS Default",
							"inclusive" => "Inclusive Tax",
							"exclusive" => "Exclusive Tax",
							"tax"       => "Tax Only"
						];
						echo whmpress_draw_combo( $data, $selected, "price_tax" );
						?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( "Currency", "whmpress" ) ?></th>
					<td>
						<?php
						$selected = whmpress_get_option( 'price_currency' );
						$data     = [ "0" => "Default" ];
						foreach ( $currencies as $cur ) {
							$data[ $cur["id"] ] = $cur["code"];
						}
						//echo whmpress_draw_combo($data,$selected,"price_currency");
						?>
						<select name="price_currency">
							<?php foreach ( $data as $k => $v ) {
								$S = $selected == $k ? "selected=selected" : ""; ?>
								<option <?php echo $S ?> value="<?php echo $k ?>"><?php echo $v ?></option>
							<?php } ?>
						</select>
					</td>
				</tr>
				<tr>
					<td></td>
					<td><?php submit_button(); ?></td>
				</tr>
			</table>
		</div>
		<div id="DomainPrice1">
			<h3><?php _e( "Domain Price", "whmpress" ) ?></h3>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php _e( "Type", "whmpress" ) ?></th>
					<td>
						<?php
						$selected = whmpress_get_option( 'dp_type' );
						$data     = [
							"domainregister" => "Domain Registration",
							"domainrenew"    => "Domain Renew",
							"domaintransfer" => "Domain Transfer"
						];
						echo whmpress_draw_combo( $data, $selected, "dp_type" );
						?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( "Years", "whmpress" ) ?></th>
					<td>
						<?php
						$selected = whmpress_get_option( 'dp_years' );
						$data     = [];
						for ( $x = 1; $x <= 10; $x ++ ) {
							$data[] = $x;
						}
						echo whmpress_draw_combo( $data, $selected, "dp_years" );
						?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( "Decimals", "whmpress" ) ?></th>
					<td>
						<?php
						$selected = whmpress_get_option( 'dp_decimals' );
						$data     = [];
						for ( $x = 1; $x <= 4; $x ++ ) {
							$data[] = $x;
						}
						echo whmpress_draw_combo( $data, $selected, "dp_decimals" );
						?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( "Hide Decimals", "whmpress" ) ?></th>
					<td>
						<?php
						$selected = whmpress_get_option( 'dp_hide_decimal' );
						$data     = [ "No", "Yes" ];
						echo whmpress_draw_combo( $data, $selected, "dp_hide_decimal" );
						?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( "Show Decimals As", "whmpress" ) ?></th>
					<td>
						<?php
						$selected = whmpress_get_option( 'dp_decimals_tag' );
						$data     = [
							""    => "-- No Tag --",
							"b"   => "Bold",
							"i"   => "Italic",
							"u"   => "Underline",
							"sup" => "Superscript",
							"sub" => "Subscript"
						];
						echo whmpress_draw_combo( $data, $selected, "dp_decimals_tag" );
						?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( "Show Currency Prefix", "whmpress" ) ?></th>
					<td>
						<?php
						$selected = whmpress_get_option( 'dp_prefix' );
						$data     = [
							"Yes" => "Yes",
							"No"  => "Do not show prefix",
							"b"   => "Bold",
							"i"   => "Italic",
							"u"   => "Underline",
							"sup" => "Superscript",
							"sub" => "Subscript"
						];
						echo whmpress_draw_combo( $data, $selected, "dp_prefix" );
						?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( "Show Currency Suffix", "whmpress" ) ?></th>
					<td>
						<?php
						$selected = whmpress_get_option( 'dp_suffix' );
						$data     = [
							"Yes" => "Yes",
							"No"  => "Do not show prefix",
							"b"   => "Bold",
							"i"   => "Italic",
							"u"   => "Underline",
							"sup" => "Superscript",
							"sub" => "Subscript"
						];
						echo whmpress_draw_combo( $data, $selected, "dp_suffix" );
						?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( "Show number of years", "whmpress" ) ?></th>
					<td>
						<?php
						$selected = whmpress_get_option( 'dp_show_duration' );
						$data     = [
							"Yes" => "Yes",
							"No"  => "Do not show duration",
							"b"   => "Bold",
							"i"   => "Italic",
							"u"   => "Underline",
							"sup" => "Superscript",
							"sub" => "Subscript"
						];
						echo whmpress_draw_combo( $data, $selected, "dp_show_duration" );
						?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( "Price/Tax", "whmpress" ) ?></th>
					<td>
						<?php
						$selected = whmpress_get_option( 'dp_price_tax' );
						$data     = [
							"default"   => "WHMCS Default",
							"inclusive" => "Inclusive Tax",
							"exclusive" => "Exclusive Tax",
							"tax"       => "Tax Only"
						];
						echo whmpress_draw_combo( $data, $selected, "dp_price_tax" );
						?>
					</td>
				</tr>
				<tr>
					<td></td>
					<td><?php submit_button(); ?></td>
				</tr>
			</table>
		</div>
		<div id="PriceMatrix">
			<h3>Price Matrix</h3>
			<table class="form-table">
				<tr valign="top">
					<th scope="row">
					<?php _e( "Decimals", "whmpress" ) ?></td>
					<td>
						<?php
						$selected = whmpress_get_option( 'pm_decimals' );
						$data     = [];
						for ( $x = 1; $x <= 4; $x ++ ) {
							$data[] = $x;
						}
						echo whmpress_draw_combo( $data, $selected, "pm_decimals" );
						?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( "Show Hidden", "whmpress" ) ?></th>
					<td>
						<?php
						$selected = whmpress_get_option( 'pm_show_hidden' );
						$data     = [ "No", "Yes" ];
						echo whmpress_draw_combo( $data, $selected, "pm_show_hidden" );
						?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( "Replace Zero With", "whmpress" ) ?></th>
					<td>
						<input name="pm_replace_zero"
						       value="<?php echo esc_attr( whmpress_get_option( 'pm_replace_zero' ) ); ?>">
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( "Replace Empty With", "whmpress" ) ?></th>
					<td>
						<?php
						$selected = whmpress_get_option( 'pm_replace_empty' );
						?>
						<input name="pm_replace_empty" value="<?php echo esc_attr( $selected ); ?>"/>
					</td>
				</tr>
				
				<tr valign="top">
					<th scope="row"><?php _e( "Hide Search", "whmpress" ) ?></th>
					<td>
						<?php
						$selected = whmpress_get_option( 'pm_hide_search' );
						$data     = [ "No", "Yes" ];
						echo whmpress_draw_combo( $data, $selected, "pm_hide_search" );
						?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( "Search Label", "whmpress" ) ?></th>
					<td>
						<?php
						$selected = whmpress_get_option( 'pm_search_label' );
						?>
						<input name="pm_search_label" value="<?php echo esc_attr( $selected ); ?>"/>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( "Search Placeholder", "whmpress" ) ?></th>
					<td>
						<?php
						$selected = whmpress_get_option( 'pm_search_placeholder' );
						?>
						<input name="pm_search_placeholder" value="<?php echo esc_attr( $selected ); ?>"/>
					</td>
				</tr>
				<tr>
					<td></td>
					<td><?php submit_button(); ?></td>
				</tr>
			</table>
		</div>
		<div id="PriceMatrixDomain">
			<h3>Price Matrix Domain</h3>
			<table class="form-table">
				<tr valign="top">
					<th scope="row">
					<?php _e( "Decimals", "whmpress" ) ?></td>
					<td>
						<?php
						$selected = whmpress_get_option( 'pmd_decimals' );
						$data     = [];
						for ( $x = 1; $x <= 4; $x ++ ) {
							$data[] = $x;
						}
						echo whmpress_draw_combo( $data, $selected, "pmd_decimals" );
						?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( "Show Renewel Price", "whmpress" ) ?></th>
					<td>
						<?php
						$selected = whmpress_get_option( 'pmd_show_renewel' );
						$data     = [ "Yes", "No" ];
						echo whmpress_draw_combo( $data, $selected, "pmd_show_renewel" );
						?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( "Show Transfer Price", "whmpress" ) ?></th>
					<td>
						<?php
						$selected = whmpress_get_option( 'pmd_show_transfer' );
						$data     = [ "Yes", "No" ];
						echo whmpress_draw_combo( $data, $selected, "pmd_show_transfer" );
						?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( "Hide Search", "whmpress" ) ?></th>
					<td>
						<?php
						$selected = whmpress_get_option( 'pmd_hide_search' );
						$data     = [ "No", "Yes" ];
						echo whmpress_draw_combo( $data, $selected, "pmd_hide_search" );
						?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( "Search Label", "whmpress" ) ?></th>
					<td>
						<?php
						$selected = whmpress_get_option( 'pmd_search_label' );
						?>
						<input name="pmd_search_label" value="<?php echo esc_attr( $selected ); ?>"/>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( "Search Placeholder", "whmpress" ) ?></th>
					<td>
						<?php
						$selected = whmpress_get_option( 'pmd_search_placeholder' );
						?>
						<input name="pmd_search_placeholder" value="<?php echo esc_attr( $selected ); ?>"/>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( "Show Disabled Domains", "whmpress" ) ?></th>
					<td>
						<?php
						$selected = whmpress_get_option( 'pmd_show_disabled' );
						$data     = [ "No", "Yes" ];
						echo whmpress_draw_combo( $data, $selected, "pmd_show_disabled" );
						?>
					</td>
				</tr>
				<tr>
					<td></td>
					<td><?php submit_button(); ?></td>
				</tr>
			</table>
		</div>
		<div id="Combo1">
			<h3>Combo</h3>
			<table class="form-table">
				<tr valign="top">
					<th scope="row">Show duration
					</td>
					<td>
						<?php
						$selected = whmpress_get_option( 'combo_billingcycles' );
						$data     = [
							"monthly"      => "Monthly",
							"annually"     => "Annually",
							"quarterly"    => "Quarterly",
							"semiannually" => "Semi Annually",
							"biennially"   => "Biennially",
							"triennially"  => "Triennially"
						];
						echo whmpress_draw_combo_multiple( $data, $selected, "combo_billingcycles" );
						?>
						<span
							class="description"><?php _e( "Press {Ctrl} button for multiple selection", "whmpress" ) ?></span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( "Decimals", "whmpress" ) ?></th>
					<td>
						<?php
						$selected = whmpress_get_option( 'combo_decimals' );
						$data     = [ 0, 1, 2, 3 ];
						echo whmpress_draw_combo( $data, $selected, "combo_decimals" );
						?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( "Show button", "whmpress" ) ?></th>
					<td>
						<?php
						$selected = whmpress_get_option( 'combo_show_button' );
						$data     = [ "Yes", "No" ];
						echo whmpress_draw_combo( $data, $selected, "combo_show_button" );
						?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( "Button text", "whmpress" ) ?></th>
					<td>
						<?php
						$selected = whmpress_get_option( 'combo_button_text' );
						?>
						<input name="combo_button_text" value="<?php echo esc_attr( $selected ); ?>"/>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( "Show discount", "whmpress" ) ?></th>
					<td>
						<?php
						$selected = whmpress_get_option( 'combo_show_discount' );
						$data     = [ "Yes", "No" ];
						echo whmpress_draw_combo( $data, $selected, "combo_show_discount" );
						
						$selected = whmpress_get_option( 'combo_discount_type' );
						$data     = [ "yearly" => "%age", "monthly" => "Monthly" ];
						echo whmpress_draw_combo( $data, $selected, "combo_discount_type" );
						?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( "Show prefix", "whmpress" ) ?></th>
					<td>
						<?php
						$selected = whmpress_get_option( 'combo_prefix' );
						$data     = [ "Yes", "No" ];
						echo whmpress_draw_combo( $data, $selected, "combo_prefix" );
						?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( "Show suffix", "whmpress" ) ?></th>
					<td>
						<?php
						$selected = whmpress_get_option( 'combo_suffix' );
						$data     = [ "Yes", "No" ];
						echo whmpress_draw_combo( $data, $selected, "combo_suffix" );
						?>
					</td>
				</tr>
				<tr>
					<td></td>
					<td><?php submit_button(); ?></td>
				</tr>
			</table>
		</div>
		<div id="OrderButton">
			<h3>Order Button</h3>
			<table class="form-table">
				<tr valign="top">
					<th scope="row">
					<?php _e( "Billing Cycle", "whmpress" ) ?></td>
					<td>
						<?php
						$selected = whmpress_get_option( 'ob_billingcycle' );
						$data     = [
							"monthly"      => "Monthly",
							"annually"     => "Annually",
							"quarterly"    => "Quarterly",
							"semiannually" => "Semi Annually",
							"biennially"   => "Biennially",
							"triennially"  => "Triennially"
						];
						echo whmpress_draw_combo( $data, $selected, "ob_billingcycle" );
						?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( "Button text", "whmpress" ) ?></th>
					<td>
						<?php
						$selected = whmpress_get_option( 'ob_button_text' );
						?>
						<input name="ob_button_text" value="<?php echo esc_attr( $selected ); ?>">
					</td>
				</tr>
				<tr>
					<td></td>
					<td><?php submit_button(); ?></td>
				</tr>
			</table>
		</div>
		<div id="PricingTable">
			<h3>Pricing Box / Pricing Table</h3>
			<table class="form-table">
				<tr valign="top">
					<th scope="row">
					<?php _e( "Billing Cycle", "whmpress" ) ?></td>
					<td>
						<?php
						$selected = whmpress_get_option( 'pt_billingcycle' );
						$data     = [
							"monthly"      => "Monthly",
							"annually"     => "Annually",
							"quarterly"    => "Quarterly",
							"semiannually" => "Semi Annually",
							"biennially"   => "Biennially",
							"triennially"  => "Triennially"
						];
						echo whmpress_draw_combo( $data, $selected, "pt_billingcycle" );
						?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( "Show Price", "whmpress" ) ?></th>
					<td>
						<?php
						$selected = whmpress_get_option( 'pt_show_price' );
						$data     = [ "Yes", "No" ];
						echo whmpress_draw_combo( $data, $selected, "pt_show_price" );
						?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( "Show Combo", "whmpress" ) ?></th>
					<td>
						<?php
						$selected = whmpress_get_option( 'pt_show_combo' );
						$data     = [ "No", "Yes" ];
						echo whmpress_draw_combo( $data, $selected, "pt_show_combo" );
						?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( "Show Button", "whmpress" ) ?></th>
					<td>
						<?php
						$selected = whmpress_get_option( 'pt_show_button' );
						$data     = [ "Yes", "No" ];
						echo whmpress_draw_combo( $data, $selected, "pt_show_button" );
						?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( "Button text", "whmpress" ) ?></th>
					<td>
						<?php
						$selected = whmpress_get_option( 'pt_button_text' );
						?>
						<input name="pt_button_text" value="<?php echo esc_attr( $selected ); ?>">
					</td>
				</tr>
				<tr>
					<td></td>
					<td><?php submit_button(); ?></td>
				</tr>
			</table>
		</div>
		<div id="DomainSearch">
			<h3>Domain Search</h3>
			<table class="form-table">
				<tr valign="top">
					<th scope="row">
					<?php _e( "Show Combo", "whmpress" ) ?></td>
					<td>
						<?php
						$selected = whmpress_get_option( 'ds_show_combo' );
						$data     = [ "No", "Yes" ];
						echo whmpress_draw_combo( $data, $selected, "ds_show_combo" );
						?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( "Placeholder", "whmpress" ) ?></th>
					<td>
						<?php
						$selected = whmpress_get_option( 'ds_placeholder' );
						?>
						<input name="ds_placeholder" value="<?php echo esc_attr( $selected ); ?>">
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( "Button Text", "whmpress" ) ?></th>
					<td>
						<?php
						$selected = whmpress_get_option( 'ds_button_text' );
						?>
						<input name="ds_button_text" value="<?php echo esc_attr( $selected ); ?>">
					</td>
				</tr>
				<tr>
					<td></td>
					<td><?php submit_button(); ?></td>
				</tr>
			</table>
		</div>
		<div id="DomainSearchAjax">
			<h3>Domain Search Ajax</h3>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php _e( "Placeholder", "whmpress" ) ?></th>
					<td>
						<?php $selected = whmpress_get_option( 'dsa_placeholder' ); ?>
						<input name="dsa_placeholder" value="<?php echo esc_attr( $selected ); ?>">
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( "Button Text", "whmpress" ) ?></th>
					<td>
						<?php
						$selected = whmpress_get_option( 'dsa_button_text' );
						?>
						<input name="dsa_button_text" value="<?php echo esc_attr( $selected ); ?>">
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
					<?php _e( "Show WhoIs Link", "whmpress" ) ?></td>
					<td>
						<?php
						$selected = whmpress_get_option( 'dsa_whois_link' );
						$data     = [ "Yes", "No" ];
						echo whmpress_draw_combo( $data, $selected, "dsa_whois_link" );
						?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
					<?php _e( "Show WWW Link", "whmpress" ) ?></td>
					<td>
						<?php
						$selected = whmpress_get_option( 'dsa_www_link' );
						$data     = [ "Yes", "No" ];
						echo whmpress_draw_combo( $data, $selected, "dsa_www_link" );
						?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
					<?php _e( "Show Transfer Link", "whmpress" ) ?></td>
					<td>
						<?php
						$selected = whmpress_get_option( 'dsa_transfer_link' );
						$data     = [ "Yes", "No" ];
						echo whmpress_draw_combo( $data, $selected, "dsa_transfer_link" );
						?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
					<?php _e( "Disable Domain Spinning", "whmpress" ) ?></td>
					<td>
						<?php
						$selected = whmpress_get_option( 'dsa_disable_domain_spinning' );
						$data     = [ "0" => "No", "1" => "Yes" ];
						echo whmpress_draw_combo( $data, $selected, "dsa_disable_domain_spinning" );
						?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
					<?php _e( "Order Landing Page", "whmpress" ) ?></td>
					<td>
						<?php
						$selected = whmpress_get_option( 'dsa_order_landing_page' );
						$data     = [
							"no"  => "No of years and Additional domains first",
							"yes" => "Go direct to domain settings"
						];
						echo whmpress_draw_combo( $data, $selected, "dsa_order_landing_page" );
						?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
					<?php _e( "Show Price", "whmpress" ) ?></td>
					<td>
						<?php
						$selected = whmpress_get_option( 'dsa_show_price' );
						$data     = [ "1" => "Yes", "0" => "No" ];
						echo whmpress_draw_combo( $data, $selected, "dsa_show_price" );
						?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
					<?php _e( "Show Years", "whmpress" ) ?></td>
					<td>
						<?php
						$selected = whmpress_get_option( 'dsa_show_years' );
						$data     = [ "1" => "Yes", "0" => "No" ];
						echo whmpress_draw_combo( $data, $selected, "dsa_show_years" );
						?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
					<?php _e( "Search in Extensions", "whmpress" ) ?></td>
					<td>
						<?php
						$selected = whmpress_get_option( 'dsa_search_extensions' );
						$data     = [ "1" => "Only Listed in WHMCS", "0" => "All" ];
						echo whmpress_draw_combo( $data, $selected, "dsa_search_extensions" );
						?>
					</td>
				</tr>
				<tr>
					<td></td>
					<td><?php submit_button(); ?></td>
				</tr>
			</table>
		</div>
		<!--<div id="DomainSearchAjaxResult">
            <h3>Domain Search Ajax Result</h3>
            <div class="whmp_error">
                <p><?php /*_e("<b>Warning:</b> This short-code is listed only for compatibility with previous versions. It will be removed in v.3.0. Please use domain_search_ajax  on both search box and search results pages.","whmpress"); */ ?></p>
            </div>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php /*_e("Show WhoIs Link", "whmpress") */ ?></td>
                    <td>
                        <?php
		/*                        $selected = whmpress_get_option('dsar_whois_link');
								$data = array("Yes","No");
								echo whmpress_draw_combo($data,$selected,"dsar_whois_link");
								*/ ?>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php /*_e("Show WWW Link", "whmpress") */ ?></td>
                    <td>
                        <?php
		/*                        $selected = whmpress_get_option('dsar_www_link');
								$data = array("Yes","No");
								echo whmpress_draw_combo($data,$selected,"dsar_www_link");
								*/ ?>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php /*_e("Show Price", "whmpress") */ ?></td>
                    <td>
                        <?php
		/*                        $selected = whmpress_get_option('dsar_show_price');
								$data = array("1"=>"Yes","0"=>"No");
								echo whmpress_draw_combo($data,$selected,"dsar_show_price");
								*/ ?>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php /*_e("Show Years", "whmpress") */ ?></td>
                    <td>
                        <?php
		/*                        $selected = whmpress_get_option('dsar_show_years');
								$data = array("1"=>"Yes","0"=>"No");
								echo whmpress_draw_combo($data,$selected,"dsar_show_years");
								*/ ?>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td><?php /*submit_button(); */ ?></td>
                </tr>
            </table>
        </div>-->
		<div id="DomainSearchBulk">
			<h3>Domain Search Bulk</h3>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php _e( "Placeholder", "whmpress" ) ?></th>
					<td>
						<?php
						$selected = whmpress_get_option( 'dsb_placeholder' );
						?>
						<input name="dsb_placeholder" value="<?php echo esc_attr( $selected ); ?>">
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( "Button Text", "whmpress" ) ?></th>
					<td>
						<?php
						$selected = whmpress_get_option( 'dsb_button_text' );
						?>
						<input name="dsb_button_text" value="<?php echo esc_attr( $selected ); ?>">
					</td>
				</tr>
				<tr>
					<td></td>
					<td><?php submit_button(); ?></td>
				</tr>
			</table>
		</div>
		<div id="DomainWhoIS">
			<h3>Domain WhoIS</h3>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php _e( "Placeholder", "whmpress" ) ?></th>
					<td>
						<?php
						$selected = whmpress_get_option( 'dw_placeholder' );
						?>
						<input name="dw_placeholder" value="<?php echo esc_attr( $selected ); ?>">
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( "Button Text", "whmpress" ) ?></th>
					<td>
						<?php
						$selected = whmpress_get_option( 'dw_button_text' );
						?>
						<input name="dw_button_text" value="<?php echo esc_attr( $selected ); ?>">
					</td>
				</tr>
				<tr>
					<td></td>
					<td><?php submit_button(); ?></td>
				</tr>
			</table>
		</div>
		<div id="OrderLink">
			<h3>Order Link</h3>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php _e( "Link Text", "whmpress" ) ?></th>
					<td>
						<?php
						$selected = whmpress_get_option( 'ol_link_text' );
						?>
						<input name="ol_link_text" value="<?php echo esc_attr( $selected ); ?>">
					</td>
				</tr>
				<tr>
					<td></td>
					<td><?php submit_button(); ?></td>
				</tr>
			</table>
		</div>
		<div id="Description">
			<h3>WMMpress Description</h3>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php _e( "Show As", "whmpress" ) ?></th>
					<td>
						<?php
						$selected = whmpress_get_option( 'dsc_description' );
						$data     = [ "ul" => "Unordered List", "ol" => "Ordered List", "s" => "Simple" ];
						echo whmpress_draw_combo( $data, $selected, "dsc_description" );
						?>
					</td>
				</tr>
				<tr>
					<td></td>
					<td><?php submit_button(); ?></td>
				</tr>
			</table>
		</div>
	</div>
</div>