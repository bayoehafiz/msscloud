<div class="settings-wrap">
	<div id="whmp-dsearch-tabs" class="tab-container">
		<ul class='etabs'>
			<li class="tab"><a href="#SearchOptions"><?php _e( "Search Options", "whmpress" ) ?></a></li>
			<li class="tab"><a href="#CustomMessages"><?php _e( "Custom Messages", "whmpress" ) ?></a></li>
			<li class="tab"><a href="#WhoIsServers"><?php _e( "WhoIs Servers", "whmpress" ) ?></a></li>
		</ul>
		
		<div id="SearchOptions">
			<h3><?php _e( "Search Options", "whmpress" ) ?></h3>
			<table class="form-table">
				<tr valign="top">
					<td style="width:30%;" scope="row"><?php _e( "Enable logs for searches", "whmpress" ) ?></td>
					<td>
						<select name="enable_logs">
							<option value="0">No</option>
							<option
								value="1" <?php echo get_option( 'enable_logs' ) == "1" ? "selected=selected" : ""; ?>>
								Yes
							</option>
						</select>
					</td>
				</tr>
				<tr valign="top">
					<td style="width:30%;"
					    scope="row"><?php _e( "Number of domains to show in load more page", "whmpress" ) ?></td>
					<td><input type="number" style="width:100%;" name="no_of_domains_to_show"
					           value="<?php echo esc_attr( get_option( 'no_of_domains_to_show', '10' ) ); ?>"/></td>
				</tr>
				<!--<tr valign="top">
                    <td scope="row"><?php /*_e("TLD orders, (Comma seprated)", "whmpress") */ ?></td>
                    <td><textarea style="width:100%;" rows="5" name="tld_order"><?php /*echo esc_attr( get_option('tld_order') ); */ ?></textarea></td>
                </tr>-->
				<tr>
					<td></td>
					<td><?php submit_button(); ?></td>
				</tr>
			</table>
		</div>
		<div id="CustomMessages">
			<h3><?php _e( "Custom Messages", "whmpress" ) ?></h3>
			<table class="form-table">
				<tr valign="top">
					<td style="width:30%;" scope="row"><?php _e( "Domain available message", "whmpress" ) ?></td>
					<td><input style="width:100%;"
					           name="<?php echo whmpress_process_key_name( 'domain_available_message' ); ?>"
					           value="<?php echo esc_attr( whmpress_get_option( 'domain_available_message' ) ); ?>"/>
					</td>
				</tr>
				<tr valign="top">
					<td scope="row"><?php _e( "Domain not available message", "whmpress" ) ?></td>
					<td><input style="width:100%;"
					           name="<?php echo whmpress_process_key_name( 'domain_not_available_message' ); ?>"
					           value="<?php echo esc_attr( whmpress_get_option( 'domain_not_available_message' ) ); ?>"/>
					</td>
				</tr>
				<tr valign="top">
					<td scope="row"><?php _e( "Recommended domains list message", "whmpress" ) ?></td>
					<td><input style="width:100%;"
					           name="<?php echo whmpress_process_key_name( 'domain_recommended_list' ); ?>"
					           value="<?php echo esc_attr( whmpress_get_option( 'domain_recommended_list' ) ); ?>"/>
					</td>
				</tr>
				<tr valign="top">
					<td scope="row"><?php _e( "Ongoing domain available message", "whmpress" ) ?></td>
					<td><input style="width:100%;"
					           name="<?php echo whmpress_process_key_name( 'ongoing_domain_available_message' ); ?>"
					           value="<?php echo esc_attr( whmpress_get_option( 'ongoing_domain_available_message', __( "[domain-name] is available", "whmpress" ) ) ); ?>"/>
					</td>
				</tr>
				<tr valign="top">
					<td scope="row"><?php _e( "Ongoing domain not available message", "whmpress" ) ?></td>
					<td><input style="width:100%;"
					           name="<?php echo whmpress_process_key_name( 'ongoing_domain_not_available_message' ); ?>"
					           value="<?php echo esc_attr( whmpress_get_option( 'ongoing_domain_not_available_message', __( "[domain-name] is registered", "whmpress" ) ) ); ?>"/>
					</td>
				</tr>
				<tr valign="top">
					<td scope="row"><?php _e( "Register domain button text", "whmpress" ) ?></td>
					<td><input style="width:100%;"
					           name="<?php echo whmpress_process_key_name( 'register_domain_button_text' ); ?>"
					           value="<?php echo esc_attr( whmpress_get_option( 'register_domain_button_text', __( "Select", "whmpress" ) ) ); ?>"/>
					</td>
				</tr>
				<tr valign="top">
					<td scope="row"><?php _e( "Load more button text", "whmpress" ) ?></td>
					<td><input style="width:100%;"
					           name="<?php echo whmpress_process_key_name( 'load_more_button_text' ); ?>"
					           value="<?php echo esc_attr( whmpress_get_option( 'load_more_button_text', __( "Load More", "whmpress" ) ) ); ?>"/>
					</td>
				</tr>
				<tr>
					<td></td>
					<td><?php submit_button(); ?></td>
				</tr>
			</table>
		</div>
		<div id="WhoIsServers">
			<h3><?php _e( "WhoIs Servers", "whmpress" ) ?></h3>
			
			<table class="form-table">
				<tr>
					<td style="width:30%;" scope="row"><?php _e( "WhoIs Servers", "whmpress" ) ?></td>
					<td>
						<textarea style="width:100%;" rows="15"
						          name="whois_db"><?php echo whmpress_get_option( 'whois_db' ); ?></textarea>
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