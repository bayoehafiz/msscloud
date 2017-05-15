<?php
/**
 * Search form
 *
 * @package nrghost
 * @since 1.0.0
 */

global $nrghost_opt;
?>

<div class="search-form">
	<form id="searchform" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="search submit">
		<input type="text" placeholder="<?php esc_html_e( 'Keyword...', 'nrghost' ); ?>" id="s" name="s" required="required" class="field" />
		<input type="submit" value="" />
	</form>
</div>