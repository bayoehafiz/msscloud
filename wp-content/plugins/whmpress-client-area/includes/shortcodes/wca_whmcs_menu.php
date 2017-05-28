<?php
	$WHMP = new WHMPress_Client_Area;
	
	## Creating parameters variable names if parameters of shortcodes are provided.
	if (isset($args[0]) && is_array($args[0]) && count($args[0])>0) {
		extract($args[0]);
	}
	
	$url = $WHMP->whmp_http();
	$base = basename($url);
	if (strpos($base, "index?") !== false)
		$url = str_replace("index?", "index.php?", $url);
	$url .= "&whmp_login_check=";
	
	$logged = $WHMP->read_remote_url($url);
	
	## Getting template filepath.
	$html_template = $WHMP->get_template_file( $html_template, $args[2] );
	
	if (is_file($html_template)) {
		$vars = [];
		
		$TemplateArray = $WHMP->get_template_array( $args[2] );
		foreach ( $TemplateArray as $custom_field ) {
			$vars[ $custom_field ] = isset( $atts[ $custom_field ] ) ? $atts[ $custom_field ] : "";
		}
		echo $WHMP->smarty_template( $html_template, $vars );
	} else {
		if ( $logged === "1" ) {
			## Generating dynamic links
			$edit_account_link = $WHMP->set_url($this->get_current_url( "clientarea.php" ), "clientarea.php?action=details");
			$contacts_link = $WHMP->set_url($this->get_current_url( "clientarea.php" ), "clientarea.php?action=contacts");
			$creditcard_link = $WHMP->set_url($this->get_current_url( "clientarea.php" ), "clientarea.php?action=creditcard");
			$changepw_link = $WHMP->set_url($this->get_current_url( "clientarea.php" ), "clientarea.php?action=changepw");
			$security_link = $WHMP->set_url($this->get_current_url( "clientarea.php" ), "clientarea.php?action=security");
			$emails_link = $WHMP->set_url($this->get_current_url( "clientarea.php" ), "clientarea.php?action=emails");
			$logout_link = $WHMP->set_url($this->get_current_url( "logout.php" ), "logout.php");
			?>
			<ul class="<?php echo $outer_ul_class;?>">
				<li class="<?php echo $outer_li_class;?>">
					<a href="#"><?php echo __("Hello", "whmpress"); ?> <?php echo do_shortcode('[whmpress_whmcs_info user_field="firstname"]') ?> <b class="caret"></b> </a>
					<ul class="<?php echo $inner_ul_class;?>">
						<li class="<?php echo $inner_li_class;?>"><a href="<?php echo $edit_account_link ?>"><?php echo __("Edit Account Details", "whmpress"); ?></a></li>
						<li class="<?php echo $inner_li_class;?>"><a href="<?php echo $creditcard_link ?>"><?php echo __("Manage Credit Card", "whmpress"); ?></a></li>
						<li class="<?php echo $inner_li_class;?>"><a href="<?php echo $contacts_link ?>"><?php echo __("Contacts/Sub-Accounts", "whmpress"); ?></a></li>
						<li class="<?php echo $inner_li_class;?>"><a href="<?php echo $changepw_link ?>"><?php echo __("Change Password", "whmpress"); ?></a></li>
						<li class="<?php echo $inner_li_class;?>"><a href="<?php echo $security_link ?>"><?php echo __("Security Settings", "whmpress"); ?></a></li>
						<li class="<?php echo $inner_li_class;?>"><a href="<?php echo $emails_link ?>"><?php echo __("Email History", "whmpress"); ?></a></li>
						<li class="<?php echo $inner_li_class;?>"><a href="<?php echo $logout_link ?>"><?php echo __("Logout", "whmpress"); ?></a></li>
					</ul>
				</li>
			</ul>
		<?php } else {
			## Generating dynamic links
			$login_link = $WHMP->set_url($this->get_current_url( "clientarea.php" ), "clientarea.php");
			$register_link = $WHMP->set_url($this->get_current_url( "register.php" ), "register.php");
			$pwreset_link = $WHMP->set_url($this->get_current_url( "pwreset.php" ), "pwreset.php");
			?>
			<ul class="<?php echo $outer_ul_class;?>">
				<li class="<?php echo $outer_li_class;?>">
					<a href="#"><?php echo __("Account", "whmpres") ?> <b class="caret"></b> </a>
					<ul class="<?php echo $inner_ul_class;?>">
						<li class="<?php echo $inner_li_class;?>"><a href="<?php echo $login_link ?>"><?php echo __("Login", "whmpress"); ?></a></li>
						<li class="<?php echo $inner_li_class;?>"><a href="<?php echo $register_link ?>"><?php echo __("Register", "whmpress") ?></a></li>
						<li class="<?php echo $inner_li_class;?>"><a href="<?php echo $pwreset_link ?>"><?php echo __("Forgot Password?", "whmpress") ?></a></li>
					</ul>
				</li>
			</ul>
		<?php }
	} ?>