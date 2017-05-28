<?php if ( ! defined( 'WHMP_VERSION' ) ) {
	exit;
} ?>
<div class="wrap">
	<div class="whmp-main-title"><span class="whmp-title">WHMpress</span> Custom CSS</div>
	<?php if ( isset( $_GET["settings-updated"] ) && $_GET["settings-updated"] == "true" ) {
		echo "<div class='updated'><p><b>" . __( 'Success', 'whmpress' ) . "</b><br />" . __( 'CSS saved', 'whmpress' ) . "</p></div>";
	} ?>
	<form method="post" action="options.php">
		<?php settings_fields( 'whmp_settings' );
		//do_settings_sections( 'whmp_settings' ); ?>
		<!--h3 class="whmp-sub-head"><span>Custom CSS</span></h3-->
	
	</form>


</div>