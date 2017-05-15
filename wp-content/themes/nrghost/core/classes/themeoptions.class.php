<?php
/**
 * NrghostThemeOptions Class
 *
 * @package nrghost
 * @since 1.0.0
 *
 */

if ( !class_exists( 'NrghostThemeOptions' ) ) {
	class NrghostThemeOptions {
		public $options = array();
		private static $instance;

		private function __construct() {
			global $post;
			defined( 'CS_OPTION' )  or  define( 'CS_OPTION',  'cs-framework' );
			$options = apply_filters( 'cs_get_option', get_option( CS_OPTION ) );
			$this->options['global']	= ( !empty( $options ) && is_array( $options ) ) ? $options : NULL;
			if ( is_page() ) {
				$temp_pageside = get_post_meta( $post->ID, 'nrghost_custom_page_side_options',		false );
				$this->options['page']		= ( !empty( $temp_pageside[0] ) && is_array( $temp_pageside[0] ) ) ? $temp_pageside[0] : NULL;
			}
			if ( is_single() ) {
				$temp_postside = get_post_meta( $post->ID, 'nrghost_custom_post_side_options',		false );
				$this->options['post']		= ( !empty( $temp_postside[0] ) && is_array( $temp_postside[0] ) ) ? $temp_postside[0] : NULL;
			}
		}

		public static function initialize() {
			if ( empty( self::$instance ) ) {
				self::$instance = new NrghostThemeOptions();
			}
			global $nrghost_opt;
			$nrghost_opt = self::$instance;
		}

		public function get_option( $option, $context = "global" ) {
			return ( isset( $this->options[$context][$option] ) ) ? $this->options[$context][$option] : NULL;
		}

		public function is_loader_enabled() {
			return ( $this->get_option( 'show-preloader' ) ) ? true : false;
		}

		public function get_logo() {
			$logo = $this->get_option( 'site-logo' );
			return ( $logo ) ? $logo : false;
		}

		public function get_sublogo() {
			$sublogo = $this->get_option( 'site-sublogo' );
			return ( $sublogo ) ? $sublogo : false;
		}

		public function get_socials() {
			$ocials = $this->get_option( 'socials' );
			return ( $ocials ) ? $ocials : false;
		}

		public function get_tracking_code( $print = true ) {
			if ( $print ) { print $this->get_option( 'tracking-code' ); } else { return $this->get_option( 'tracking-code' ); }
		}

		public function get_header_adv( $print = true ) {
			$is_active = ( is_page() ) ? $this->get_option( 'enable-header-adv', 'page' ) : $this->get_option( 'enable-header-adv' ) ;
			$adv = ( $is_active ) ? $this->get_option( 'header-adv' ) : false;
			if ( $print ) { print $adv; } else { return $adv; }
		}
	}

	add_action( 'wp', array( 'NrghostThemeOptions', 'initialize' ) );
}