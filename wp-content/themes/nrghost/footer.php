<?php
/**
 * Footer
 *
 * @package nrghost
 * @since 1.0.0
 *
 */

global $nrghost_opt;
?>	</div>

	<!-- FOOTER -->
	<footer>
		<div class="container">
			<?php if ( is_object( $nrghost_opt ) && $nrghost_opt->get_option( 'enable-footer-sidebar' ) ) { ?><div class="row">
				<div class="sidebar footer-entry col-md-3">
					<?php if ( is_active_sidebar( 'footer-sidebar-1' ) ) { dynamic_sidebar( 'footer-sidebar-1' ); } ?>
				</div>
				<div class="sidebar footer-entry col-md-2 col-sm-3 col-xs-6">
					<?php if ( is_active_sidebar( 'footer-sidebar-2' ) ) { dynamic_sidebar( 'footer-sidebar-2' ); } ?>
				</div>
				<div class="sidebar footer-entry col-md-2 col-sm-3 col-xs-6">
					<?php if ( is_active_sidebar( 'footer-sidebar-3' ) ) { dynamic_sidebar( 'footer-sidebar-3' ); } ?>
				</div><div class="clearfix visible-xs"></div>
				<div class="sidebar footer-entry col-md-5 col-sm-6 col-xs-12">
					<?php if ( is_active_sidebar( 'footer-sidebar-4' ) ) { dynamic_sidebar( 'footer-sidebar-4' ); } ?>
				</div>
			</div><?php } ?>
			<?php if ( is_object( $nrghost_opt ) && $nrghost_opt->get_option( 'enable-footer-socials' ) ) { nrghost_footer_socials(); } ?>
			<div class="row">
				<div class="col-md-8">
					<?php if ( is_object( $nrghost_opt ) && $nrghost_opt->get_option( 'enable-footer-menu' ) ) { nrghost_footer_nav(); } ?>
				</div>
				<?php
				$copyright = $nrghost_opt->get_option( 'footer-copy-text' );
				if ( !empty( $copyright ) ) {
				?>
				<div class="col-md-4">
					<div class="copyright"><?php print $copyright; ?></div>
				</div>
				<?php } ?>
			</div>
		</div>
		<?php
			if ( is_object( $nrghost_opt ) ) {
				$footer_menu = $nrghost_opt->get_option( 'footer-line' );
				if ( $nrghost_opt->get_option( 'enable-footer-line-menu' ) && $footer_menu ) { ?>
					<div class="footer-line">
						<div class="container">
							<div class="row">
							<?php foreach ( $footer_menu as $item ) { ?>
								<div class="footer-line-entry col-md-3 col-sm-6 col-xs-12">
									<img src="<?php echo esc_url( $item['image'] ); ?>" alt="Foot-img"/>
									<div class="content">
										<div class="cell-view"><?php echo ( isset( $item['enable-link'] ) && $item['enable-link'] ) ? '<a href="' . esc_url( $item['link'] ) . '">' . esc_attr( $item['title'] ) . '</a>' : esc_attr( $item['title'] ); ?></div>
									</div>
								</div>
							<?php } ?>
							</div>
						</div>
					</div>
				<?php }
			}
		?>
	</footer>

    <?php global $videoplayer_active;
    if ( $videoplayer_active === true ) { ?>
    <div class="video-player">
        <div class="video-iframe">
            <iframe class="box-size" src="" frameborder="0" allowfullscreen></iframe>
        </div>
        <div class="close-iframe">X</div>
    </div>
    <?php } ?>

	<?php wp_footer(); ?>
	<?php if ( is_object( $nrghost_opt ) ) { $nrghost_opt->get_tracking_code();	} ?>
</body>
</html>