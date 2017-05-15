<?php
/**
 * 404 Page
 *
 * @package nrghost
 * @since 1.0
 *
 */

global $nrghost_opt;

get_header();
?>

<div class="page-404 container">
	<div class="page-404-text">
		<h3 class="col-green"><?php esc_html_e( 'We searched everywhere!', 'nrghost' ); ?></h3>
		<h4><?php esc_html_e( 'This page could not be found', 'nrghost' ); ?></h4>
		<div class="go-back">
			<h6><?php esc_html_e( 'You can either', 'nrghost' ); ?>
				<a href="javascript:history.go(-1)"><?php esc_html_e( 'Go Back', 'nrghost' ); ?></a> <?php esc_html_e( 'or', 'nrghost' ); ?> 
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Go Home', 'nrghost' ); ?></a>
			</h6>
		</div>
	</div>
</div>

<?php get_footer(); ?>