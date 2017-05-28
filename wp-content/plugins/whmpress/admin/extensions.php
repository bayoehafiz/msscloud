<?php
/**
 * @package Admin
 * @todo    Extensions page for WHMpress admin panel
 */

if ( ! defined( 'WHMP_VERSION' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}
?>

<div class="wrap">
	<!--<div class="whmp-main-title"><span class="whmp-title">WHMpress</span> Extensions</div>-->
	
	<h2 class="nav-tab-wrapper">
		<a class="nav-tab" href="<?php echo admin_url() ?>admin.php?page=whmp-dashboard">Dashboard</a>
		<a class="nav-tab" href="<?php echo admin_url() ?>admin.php?page=whmp-services">Products/Services</a>
		<a class="nav-tab" href="<?php echo admin_url() ?>admin.php?page=whmp-settings">Settings</a>
		<a class="nav-tab" href="<?php echo admin_url() ?>admin.php?page=whmp-sync">Sync WHMCS</a>
		<a class="nav-tab nav-tab-active" href="<?php echo admin_url() ?>admin.php?page=whmp-extensions">Addons</a>
	</h2>
	
	<div>
		<?php include_once "sidebar.php" ?>
		<div class="leftbar">
			<div class="whmpress_panel">
				<div>
					<h3><?php _e( 'WHMCS Client Area Integration', 'whmpress' ); ?></h3>
					<p>
						<img src="<?php echo WHMP_ADMIN_URL ?>/images/whmp_ca.jpg" class="response-img left-corner"/>
						<?php _e( 'Go one step further, offer your users their WHMCS client area with the same look and feel as your website. 
                    Giving them a feel that they never left your website for their hosting management and website. 
                    Again offering several Shortcodes for seamless integration with your site.',
							'whmpress' ); ?>
					</p>
					<p>
						<a href="http://codecanyon.net/item/whmcs-client-area-whmpress-addon/11218646" target="_blank"
						   class="button button-primary"><?php _e( "Buy this Addon", "whmpress" ) ?></a>
					</p>
					<div style="clear: both;"></div>
				</div>
			</div>
			<div class="whmpress_panel">
				<div>
					<h3><?php _e( 'WHMCS Pricing Sliders and Comparison Tables', 'whmpress' ); ?></h3>
					<p>
						<img src="<?php echo WHMP_ADMIN_URL ?>/images/whmp_sliders.jpg"
						     class="response-img left-corner"/>
						<?php
						_e( 'Comparison Table and Slider are essential part of every web-hosting site. 
                        It helps user to make informed decisions. 
                        Simply select packages to compare and get a complete comparison table or slider in your desired template.', 'whmpress' );
						?>
					</p>
					<p>
						<a href="http://codecanyon.net/item/whmcs-pricing-sliders-and-comparison-tables-whmpress-addon/14949513"
						   target="_blank" class="button button-primary"><?php _e( "Buy this Addon", "whmpress" ) ?></a>
					</p>
					<div style="clear: both;"></div>
				</div>
			</div>
		</div>
	</div>
</div>