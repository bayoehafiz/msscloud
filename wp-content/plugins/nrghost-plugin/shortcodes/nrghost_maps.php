<?php
/**
 *
 * Custum Google Maps
 * @since 1.0.0
 * @version 1.1.0
 *
 */
add_shortcode( 'nrghost_maps', 'nrghost_maps' );
function nrghost_maps ( $atts, $content = '', $id = '' ) {

    extract( shortcode_atts( array(
		'id'              => '',
		'el_class'        => '',
		'latitude'        => '43.653226',
		'longitude'		  => '-79.383184',
	    'zoom'            => '3',
	    'marker'          => '',
        'group_adress'    => '',
    ), $atts ) );


    $el_class = (!empty($css)) ? ' '.$el_class : '';
    $marker_src = (!empty($marker)) ? wp_get_attachment_image_src($marker, "full") : '';
    $marker_src = $marker_src ? $marker_src[0] : get_template_directory_uri().'/assets/img/marker.png';


    $group_adress = json_decode(urldecode($group_adress));

    $content = (!empty($content)) ? do_shortcode( $content ) : '';

    $output  =  '';
    $output .=  '<div>';
    $output .=  '<div class="map-canvas'.esc_attr($el_class).'" id="map-canvas"  data-lat="'.esc_attr($latitude).'" data-lng="'.esc_attr($longitude).'" data-zoom="'.esc_attr($zoom).'"></div>';
    $output .=  '<div class="addresses-block">';
    foreach ($group_adress as $adress) {
    $output .=  '<a data-lat="'.$adress->latitude.'" data-lng="'.$adress->longitude.'" data-string="'.$adress->description.'" data-marker="'.esc_attr($marker_src).'"></a>';
    }
    $output .=  '</div>';
    $output .=  '</div>';

  	return $output;

}

