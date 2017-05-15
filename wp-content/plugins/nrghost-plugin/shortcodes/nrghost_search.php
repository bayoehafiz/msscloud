<?php
/**
 * Search shortcode
 *
 * @package nrghost
 * @since 1.0.0
 *
 */


function nrghost_search($atts, $content = '', $id = '')
{
    extract(shortcode_atts(array(
        'style' => '',
        'title' => '',
        'subtitle' => '',
        'links' => '',
        'link1' => '',
        'link2' => '',
        'link3' => '',
        'link4' => '',
        'link5' => '',
        'el_class' => '',
        'animation' => '',
    ), $atts));

    $output = '';
    if ( !class_exists( 'TitanFramework' ) ) { return '';}

    $titan = TitanFramework::getInstance('wdc-options');
    $item_id = $titan->getOption('additional_button_link');
    $image = $titan->getOption('loading_image');
    $recaptcha_enable = $titan->getOption('recaptcha');
    $placeholder = $titan->getOption('input_placeholder');
    $image = wp_get_attachment_image_src($image);
    if ($image == '') {
        $image = plugins_url('wp-domain-checker/images/load.gif');
    } else {
        $image = $image[0];
    }
    $atts = shortcode_atts(
        array(
            'width' => '1170',
            'button' => 'Check',
            'recaptcha' => 'no',
            'item_id' => $item_id,
            'tld' => ''
        ), $atts);
    if ($atts['recaptcha'] == 'yes') {
        $show_recaptcha = '<div id="wdc-recaptcha" class="wdc"></div>';
    } else {
        $show_recaptcha = '';
    }

    $style = ' ' . esc_attr($style);
    $class = (!empty($el_class)) ? ' ' . esc_attr($el_class) : '';
    $animation = (!empty($animation)) ? ' ' . esc_attr($animation) : '';
    if ($links) {
        $link = '';
        for ($i = 1; $i <= $links; $i++) {
            $link = 'link' . $i;
            $links_arr[$i] = vc_build_link($$link);
        }
        unset($link);
    }

    $output .= '<div class="block type-8 wow' . $animation . $style . $class . '">';
    $output .= '	<div class="container">';
    $output .= '		<div id="domain-form">';
    $output .= '			<form method="post" action="./" id="form" class="pure-form">';
    $output .= '				<div class="row">';
    $output .= '					<div class="form-description col-md-4 wow fadeInLeft">';
    $output .= '						<h3 class="title">' . esc_textarea($title) . '</h3>';
    $output .= '						<div class="text">' . $subtitle . '</div>';
    $output .= '					</div>';
    $output .= '					<div class="block-form-wrapper col-md-8 wow fadeInRight">';
    $output .= '						<div class="block-form">';
    $output .= '							<input type="text" class="form-control" autocomplete="off" id="Search" name="domain" placeholder="' . esc_textarea($placeholder) . '">';
    $output .= '							<input type="submit" value="">';
    $output .= '						</div>';
    if ($links) {
        $output .= '					<ul class="links-examples">';
        foreach ($links_arr as $key => $link) {
            $output .= ($link['url'] && $link['title']) ? '<li><a href="' . esc_url($link['url']) . '"' . ($link['target'] ? ' target="' . esc_attr($link['target']) . '"' : '') . '>' . esc_textarea($link['title']) . '</a></li>' : '';
        }
        $output .= '						</ul>';
    }
    $output .= '					</div>';
    $output .= '					<div class="container">';
    $output .= '						<div class="row text-center"><div id="loading"><img src="' . $image . '" alt="Loader" /></div></div>';
    $output .= '						<div class="row"><div id="results" class="result"></div></div>';
    $output .= '					</div>';
    $output .= '				</div>';
    $output .= '			</form>';
    $output .= '		</div>'; // #domain-form
    $output .= '	</div>';
    $output .= '</div>';

    return $output;
}

add_shortcode('nrghost_search', 'nrghost_search');