<?php
if(!function_exists('convert_encode_chars')) {
    function convert_encode_chars($string) {
        return preg_replace('/\&amp\;(\#|cent|pound|yen|euro|sect|copy|reg|trade)/', '&$1', $string);
    }
}

extract( shortcode_atts( array(
    'table_style'   => '',
    'el_class'      => '',
    'animation'     => '',
    ), $atts
) );

$output = '';

$m_class = ( !empty( $el_class ) ) ? ' ' . esc_attr( $el_class ) : '';
$animation = ( !empty( $animation ) ) ? ' ' . esc_attr( $animation ) : '';

$table_data = vc_table_parse_table_param( $content );

$m_class .= ( $table_style == '1' || $table_style == '2' ) ? ' block type-4' : '';
$t_class = ( $table_style == '2' ) ? ' style-1' : '';

$css_class = ' ' . trim( apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, 'wpb_vc_table wpb_content_element wow' . $animation .  $m_class, $this->settings['base'] ) );

$output .= '<div class="table-responsive' . $css_class . '">';
$output .= '<table class="table' . $t_class . '">';
$output .= '<tbody>';
foreach ( $table_data as $index => $row ) {
    $output .= '<tr' . ( $index === 0 ? ' class="vc-th"' : '' ) . '>';
    foreach ( $row as $cell ) {
        $style = empty( $cell['css_style'] ) ? '' : ' style="' . implode( '', $cell['css_style'] ) . '"';
        $class = empty( $cell['css_class'] ) ? '' : ' class="'. implode( ' ', $cell['css_class'] ) . '"';
        $output .= '<' . ( $index === 0 ? 'th' : 'td' ) . $style . $class . '><span class="vc_table_content">' . html_entity_decode( $cell['content'] ) . '</span></' . ( $index===0 ? 'th' : 'td' ) . '>';
    }
    $output .= '</tr>';
}
$output .= '</tbody>';
$output .= '</table>';
$output .= '</div>';

echo $output;