<?php
/**
 * Header
 *
 * @package nrghost
 * @since 1.0.0
 *
 */

global $nrghost_opt;
$body_class  = ( is_object( $nrghost_opt ) ) ? $nrghost_opt->get_option( 'color-scheme' ) : false;
$header_adv = $nrghost_opt->get_header_adv( false );
$body_class .= ( $header_adv ) ? ' header-moved' : false;

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<?php wp_head(); ?>
<link rel="stylesheet" href="<?php print get_template_directory_uri(); ?>/assets/whmcs/style.css">

</head>

<body <?php body_class( $body_class ); ?>>
<?php nrghost_theme_preloader(); ?>

	<!-- HEADER -->
	<header>
		<div class="container">
			<?php nrghost_logo(); ?>
			<div class="open-icon">
				<span></span>
				<span></span>
				<span></span>
			</div>
			<div class="header-container">
				<div class="scrollable-container">
					<div class="header-left">
						<?php nrghost_header_nav(); ?>
					</div>
					<div class="header-right">
						<?php
							if ( is_object( $nrghost_opt ) ) {
								$header_menu = $nrghost_opt->get_option( 'header-add-menu' );
								if ( $nrghost_opt->get_option( 'enable-header-add-menu' ) && $header_menu ) { ?>
									<div class="header-inline-entry">
										<?php foreach ( $header_menu as $item ) { ?>
											<div><?php print ( isset( $item['enable-link'] ) && $item['enable-link'] ) ? '<a href="' . esc_url( $item['link'] ) . '" class="telephone-link"><span class="' . esc_attr( $item['icon'] ) . '"></span>' . $item['title'] . '</a>' : $item['title']; ?></div>
										<?php } ?>
										<?php if ( $nrghost_opt->get_option( 'enable-add-menu-cart' ) && class_exists( 'WooCommerce' ) ) { ?>
										<div class="woocommerce">
											<a class="view-cart telephone-link" href="<?php echo esc_url( WC()->cart->get_cart_url() ); ?>" title="<?php esc_html_e( 'View your shopping cart', 'nrghost' ); ?>"><span class="glyphicon glyphicon-shopping-cart"></span>Cart<?php echo ( WC()->cart->get_cart_contents_count() ) ? ' (' . esc_attr( WC()->cart->get_cart_contents_count() ) . ')' : ''; ?></a>
										</div>
										<?php } ?>
									</div>
								<?php }
							}
						?>
						<?php if ( is_object( $nrghost_opt ) && $nrghost_opt->get_option( 'enable-header-login-buttons' ) && $nrghost_opt->get_option( 'login-page' ) ) { ?>
							<?php if ( !is_user_logged_in() ) { ?>
								<div class="header-inline-entry">
									<a class="button" href="<?php echo esc_url( get_permalink( $nrghost_opt->get_option( 'login-page' ) ) ); ?>"><?php esc_html_e( 'Login', 'nrghost' ); ?></a>
								</div>
								<?php if ( get_option( 'users_can_register' ) ) { ?>
									<div class="header-inline-entry">
										<a class="link" href="<?php echo esc_url( get_permalink( $nrghost_opt->get_option( 'login-page' ) ) ); ?>?action=register"><?php esc_html_e( 'Register', 'nrghost' ); ?></a>
									</div>
								<?php } ?>
							<?php } else { ?>
								<div class="header-inline-entry">
									<a class="button" href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>"><?php esc_html_e( 'Log Out', 'nrghost' ); ?></a>
								</div>
							<?php } ?>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</header>

	<div id="content-wrapper">
	<?php if ( $header_adv ) { ?>
		<div class="container-above-header">
			<!-- BLOCK "TYPE 9" -->
			<div class="block type-9">
				<div class="container">
					<div class="entry">
						<?php $nrghost_opt->get_header_adv(); ?>
					</div>
				</div>
			</div>
		</div>
	<?php } ?>