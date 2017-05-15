<?php
/**
 * Sidebar template
 *
 * @package nrghost
 * @since 1.0.0
 *
 */

?>
<?php if ( is_active_sidebar( 'sidebar' ) ) { ?>
	<?php dynamic_sidebar( 'sidebar' ); ?>
<?php } ?>