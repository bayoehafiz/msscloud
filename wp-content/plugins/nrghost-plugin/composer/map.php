<?php
/**
  * WPBakery Visual Composer Shortcodes settings
  *
  * @package VPBakeryVisualComposer
  *
 */

// Include Helpers
//include_once( T_PATH . '/' . F_DIR . '/composer/helpers.php' );
include_once( EF_ROOT . '/composer/params.php' );

if ( ! function_exists( 'is_plugin_active' ) ) {
  include_once( ABSPATH . 'wp-admin/includes/plugin.php' ); // Require plugin.php to use is_plugin_active() below
}

/**
 *
 * element values post, page, categories
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! function_exists( 'nrghost_element_values' ) ) {
	function nrghost_element_values( $type = '', $query_args = array() ) {

		$options = array();

		switch( $type ) {

			case 'pages':
			case 'page':
			$pages = get_pages( $query_args );

			if ( !empty($pages) ) {
				foreach ( $pages as $page ) {
					$options[$page->post_title . ' (id=' . $page->ID . ')'] = $page->ID;
				}
			}
			break;

			case 'posts':
			case 'post':
			$posts = get_posts( $query_args );

			if ( !empty($posts) ) {
				foreach ( $posts as $post ) {
					$options[$post->post_title . ' (id=' . $post->ID . ')'] = $post->ID;
				}
			}
			break;

			case 'posts_with_thumb':
			$posts = get_posts( $query_args );

			if ( !empty($posts) ) {
				foreach ( $posts as $post ) {
					if ( has_post_thumbnail( $post->ID ) ) {
						$options[$post->post_title . ' (id=' . $post->ID . ')'] = $post->ID;
					}
				}
			}
			break;

			case 'tags':
			case 'tag':

			$tags = get_terms( $query_args['taxonomies'], $query_args['args'] );
			if ( !empty($tags) ) {
				foreach ( $tags as $tag ) {
					$options[$tag->name] = $tag->term_id;
				}
			}
			break;

			case 'categories':
			case 'category':

			$categories = get_categories( $query_args );
			if ( !empty($categories) ) {
				foreach ( $categories as $category ) {
					@$options[$category->name] = $category->term_id;
				}
			}
			break;

			case 'custom':
			case 'callback':

			if( is_callable( $query_args['function'] ) ) {
				$options = call_user_func( $query_args['function'], $query_args['args'] );
			}

			break;

		}

		return $options;

	}
}

$animation_params = array(
	'param_name'	=> 'animation',
	'type'			=> 'dropdown',
	'heading'		=> __('Animation', 'nrghost' ),
	'description'	=> __('Select the animation type', 'nrghost' ),
	'group'			=> __('Animation', 'nrghost' ),
	'value'			=> array(
		__( 'None', 'nrghost' )				=> '',
		__( 'Fade-In', 'nrghost' )			=> 'fadeIn',
		__( 'Fade-In-Up', 'nrghost' )		=> 'fadeInUp',
		__( 'Fade-In-Down', 'nrghost' )		=> 'fadeInDown',
		__( 'Fade-In-Left', 'nrghost' )		=> 'fadeInLeft',
		__( 'Fade-In-Right', 'nrghost' )	=> 'fadeInRight',
		__( 'Bounce-In-Left', 'nrghost' )	=> 'bounceInLeft',
		__( 'Bounce-In-Right', 'nrghost' )	=> 'bounceInRight',
	),
);

$animation_delay_params = array(
	'param_name'	=> 'animation_delay',
	'type'			=> 'textfield',
	'heading'		=> 'Animation Delay',
	'description'	=> 'Type animation delay in seconds, e.g. "0.3s"',
	'group'			=> 'Animation',
);



vc_map( array(
	'name' => __( 'Row', 'nrghost' ),
	'base' => 'vc_row',
	'is_container' => true,
	'icon' => 'icon-wpb-row',
	'show_settings_on_create' => false,
	'category' => __( 'Content', 'nrghost' ),
	'description' => __( 'Place content elements inside the row', 'nrghost' ),
	'params' => array(

		array(
			'param_name'	=> 'container',
			'type'			=> 'dropdown',
			'heading'		=> __( 'Container width', 'nrghost' ),
			'value'			=> array(
				__( 'Fullwidth', 'nrghost' )	=>  'fullwidth',
				__( 'Default', 'nrghost' )		=>  'default',
			),
			'std'			=> 'fullwidth',
		),

		/*array(
			'type' => 'colorpicker',
			'heading' => __( 'Font Color', 'nrghost' ),
			'param_name' => 'font_color',
			'description' => __( 'Select font color', 'nrghost' ),
			'edit_field_class' => 'vc_col-md-6 vc_column'
		),*/

		array(
			'param_name'	=> 'el_class',
			'type'			=> 'textfield',
			'heading'		=> __( 'Extra class name', 'nrghost' ),
			'description'	=> __( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'nrghost' ),
		),

		array(
			'param_name'	=> 'css',
			'type'			=> 'css_editor',
			'heading'		=> __( 'Css', 'nrghost' ),
			'group'			=> __( 'Design options', 'nrghost' ),
		),

		array(
			'param_name'	=> 'back_over_color',
			'type'			=> 'colorpicker',
			'heading'		=> __( 'Background color overlay', 'nrghost' ),
			'description'	=> __( 'You can set up image, then choose color and opacity for overlay. This option isn\'t the same as background color.', 'nrghost' ),
			'group'			=> __( 'Design options', 'nrghost' ),
		),

	),
	'js_view' => 'VcRowView'
) );



vc_map( array(
'name' => __( 'Row', 'nrghost' ), //Inner Row
'base' => 'vc_row_inner',
'content_element' => false,
'is_container' => true,
'icon' => 'icon-wpb-row',
'weight' => 1000,
'show_settings_on_create' => false,
'description' => __( 'Place content elements inside the row', 'nrghost' ),
'params' => array(
	/*array(
		'type' => 'colorpicker',
		'heading' => __( 'Font Color', 'nrghost' ),
		'param_name' => 'font_color',
		'description' => __( 'Select font color', 'nrghost' ),
		'edit_field_class' => 'vc_col-md-6 vc_column'
	),*/

	array(
		'param_name'	=> 'container',
		'type'			=> 'dropdown',
		'heading'		=> __( 'Container width', 'nrghost' ),
		'value'			=> array(
			__( 'Fullwidth', 'nrghost' )	=>  'fullwidth',
			__( 'Default', 'nrghost' )		=>  'default',
		),
		'std'		=> 'fullwidth',
	),
	array(
		'param_name'	=> 'el_class',
		'type'			=> 'textfield',
		'heading'		=> __( 'Extra class name', 'nrghost' ),
		'description'	=> __( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'nrghost' )
	),
	array(
		'param_name'	=> 'css',
		'type'			=> 'css_editor',
		'heading'		=> __( 'Css', 'nrghost' ),
		'group'			=> __( 'Design options', 'nrghost' )
	),
	),
'js_view' => 'VcRowView'
) );



add_action( 'admin_init', 'vc_remove_elements', 10);
function vc_remove_elements( $e = array() ) {

	if ( !empty( $e ) ) {
		foreach ( $e as $key => $r_this ) {
			vc_remove_element( 'vc_'.$r_this );
		}
	}
}

$s_elements = array(
	'custom_heading',
	'empty_space',
	'clients',
	'toggle',
	'images_carousel',
	'carousel',
	'tour',
	'gallery',
	'posts_slider',
	'posts_grid',
	'teaser_grid',
	'message',
	'facebook',
	'tweetmeme',
	'googleplus',
	'pinterest',
	'button',
	'toogle',
	'button2',
	'cta_button',
	'cta_button2',
	'flickr',
	'progress_bar',
	'pie',
	'wp_search',
	'wp_meta',
	'wp_recentcomments',
	'wp_calendar',
	'wp_pages',
	'wp_custommenu',
	'wp_text',
	'wp_posts',
	'wp_links',
	'wp_categories',
	'wp_archives',
	'wp_rss',
	'basic_grid',
	'media_grid',
	'masonry_grid',
	'masonry_media_grid',
	'icon',
	'wp_tagcloud',
	'btn',
	'cta',
//	'tabs',
//	'tab',
//	'accordion',
//	'accordion_tab',
//	'column_text',
//	'widget_sidebar',
//	'separator',
//	'text_separator',
//	'single_image',
//	'video',
//	'gmaps',
//	'raw_html',
//	'raw_js',
);
// vc_remove_element('client', 'testimonial', 'contact-form-7');
vc_remove_element('woocommerce_checkout');
vc_remove_element('woocommerce_cart');
vc_remove_element('woocommerce_order_tracking');
vc_remove_element('woocommerce_my_account');
vc_remove_element('recent_products');
vc_remove_element('featured_products');
vc_remove_element('product');
vc_remove_element('products');
vc_remove_element('add_to_cart');
vc_remove_element('add_to_cart_url');
vc_remove_element('product_page');
vc_remove_element('product_categories');
vc_remove_element('sale_products');
vc_remove_element('best_selling_products');
vc_remove_element('top_rated_products');
vc_remove_element('product_attribute');
vc_remove_elements( $s_elements );



// ==========================================================================================
// HEADING
// ==========================================================================================
vc_map( array(
	'name'				=> __( 'Heading', 'nrghost' ),
	'base'				=> 'nrghost_heading',
	'category'			=> 'Content',
	'icon'				=> 'icon-wpb-ui-custom_heading',
	'description'		=> __( 'Custom Heading', 'nrghost' ),
	'params'			=> array(
		array(
			'param_name'	=> 'size',
			'type'			=> 'dropdown',
			'heading'		=> __( 'Heading', 'nrghost' ),
			'value'			=> array(
				'H1'	=> 'h1',
				'H2'	=> 'h2',
				'H3'	=> 'h3',
				'H4'	=> 'h4',
				'H5'	=> 'h5',
				'H6'	=> 'h6',
			),
			'std'			=> 'h2',
		),
		array(
			'param_name'	=> 'align',
			'type'			=> 'dropdown',
			'heading'		=> __( 'Text align', 'nrghost' ),
			'value'			=> array(
				__( 'Center', 'nrghost' )		=> 'text-center',
				__( 'Left', 'nrghost' )			=> 'text-left',
				__( 'Right', 'nrghost' )		=> 'text-right',
				__( 'Justify', 'nrghost' )		=> 'text-justify',
			),
		),
		array(
			'param_name'	=> 'style',
			'type'			=> 'dropdown',
			'heading'		=> __( 'Style', 'nrghost' ),
			'value'			=> array(
				__( 'Light', 'nrghost' )	=> 'light',
				__( 'Dark', 'nrghost' )		=> 'dark',
			),
			'std'		=> 'dark',
		),
		array(
			'param_name'	=> 'width',
			'type'			=> 'dropdown',
			'heading'		=> __( 'Width', 'nrghost' ),
			'value'			=> array(
				__( 'Default', 'nrghost' )		=> 'default',
				__( 'Fullwidth', 'nrghost' )	=> 'fullwidth',
			),
			'std'			=> 'default',
		),
		array(
			'param_name'	=> 'heading',
			'type'			=> 'textarea',
			'heading'		=> __( 'Heading', 'nrghost' ),
			'admin_label'	=> true,
		),
		array(
			'param_name'	=> 'content',
			'type'			=> 'textarea_html',
			'heading'		=> __( 'Content', 'nrghost' ),
		),
		array(
			'param_name'	=> 'el_class',
			'type'			=> 'textfield',
			'heading'		=> __( 'Extra class name', 'nrghost' ),
			'description'	=> __( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'nrghost' ),
			'admin_label'	=> true,
		),
		$animation_params,
		array(
			'param_name' => 'css',
			'type' => 'css_editor',
			'heading' => __( 'Css', 'nrghost' ),
			'group' => __( 'Design options', 'nrghost' )
		),
	)

) );



// ==========================================================================================
// SLIDER
// ==========================================================================================
vc_map( array(
	'name'						=> __( 'Slider', 'nrghost' ),
	'base'						=> 'nrghost_slider',
	'description'				=> 'Slider',
	'category'					=> 'Custom Content',
	'as_parent'					=> array( 'only' => 'nrghost_slide' ),
	'content_element'			=> true,
	'icon'						=> 'icon-wpb-images-carousel',
	'show_settings_on_create'	=> true,
	'params'					=> array(
		array(
			'param_name'	=> 'style',
			'type'			=> 'dropdown',
			'heading'		=> 'Style',
			'value'			=> array(
				__( 'Style 1 (Overlay transparent)', 'nrghost' ) => '1',
				__( 'Style 2 (Overlay background and background title)', 'nrghost' ) => '2',
				__( 'Style 3 (Overlay background and transparent title background)', 'nrghost' ) => '3',
			),
		),
		array(
			'param_name'	=> 'pager',
			'type'			=> 'dropdown',
			'heading'		=> 'Pager position',
			'value'			=> array(
				__( 'Bottom', 'nrghost' )	=> 'bottom',
				__( 'Top', 'nrghost' )		=> 'top',
			),
		),
		array(
			'param_name'	=> 'arrows',
			'type'			=> 'checkbox',
			'heading'		=> __( 'Slider arrows', 'nrghost' ),
			'value'			=> array( __( 'Enable arrows', 'nrghost' ) => 'yes' ),
		),
		array(
			'param_name'	=> 'loop',
			'type'			=> 'checkbox',
			'heading'		=> __( 'Infinite loop', 'nrghost' ),
			'value'			=> array( __( 'Enable slider infinite loop', 'nrghost' ) => 'yes' ),
		),
		array(
			'param_name'	=> 'el_class',
			'type'			=> 'textfield',
			'heading'		=> __( 'Extra class name', 'nrghost' ),
			'description'	=> __( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'nrghost' ),
		),
		$animation_params,
	),
	'js_view' => 'VcColumnView'
) );



vc_map( array(
	'name'						=> __('Slide', 'nrghost'),
	'base'						=> 'nrghost_slide',
	'content_element'			=> true,
	'as_child'					=> array( 'only' => 'nrghost_slider' ),
	'params' 					=> array(
		array(
			'param_name'	=> 'price',
			'type'			=> 'textfield',
			'heading'		=> __('Price', 'nrghost'),
			'description'	=> __('Here you can use tags &lt;b&gt; and &lt;small&gt; for editing', 'nrghost'),
		),
		array(
			'param_name'	=> 'title',
			'type'			=> 'textfield',
			'heading'		=> __('Title', 'nrghost'),
			'description'	=> __('Slide title', 'nrghost'),
			'admin_label'	=> true,
		),
		array(
			'param_name'	=> 'subtitle',
			'type'			=> 'textfield',
			'heading'		=> __('Subtitle', 'nrghost'),
			'description'	=> __('Slide subtitle (used only in style 1)', 'nrghost'),
		),
		array(
			'param_name'	=> 'description',
			'type'			=> 'textarea',
			'heading'		=> __('Description', 'nrghost'),
			'description'	=> __('Slide description', 'nrghost'),
		),
		array(
			'param_name'	=> 'link',
			'type'			=> 'vc_link',
			'heading'		=> __('Link', 'nrghost'),
		),
		array(
			'param_name'	=> 'link2',
			'type'			=> 'vc_link',
			'heading'		=> __('Link 2', 'nrghost'),
		),
		array(
			'param_name'	=> 'image',
			'type'			=> 'attach_image',
			'heading'		=> __('Image', 'nrghost'),
		),
        array(
            'param_name'    => 'extra_option',
            'type'          => 'dropdown',
            'heading'       => __( 'Image extra option', 'nrghost' ),
            'value'         => array(
                __( 'None', 'nrghost' ) => 'none',
                __( 'Button', 'nrghost' ) => 'image-button',
                __( 'Video', 'nrghost' ) => 'image-video'
            ),
            'description'   => __( 'Select if you want to add some extra option to image.', 'nrghost' ),
        ),
        array(
            'param_name' => 'image_button',
            'type' => 'vc_link',
            'heading' => __( 'Button', 'nrghost' ),
            'dependency' => array(
                'element' => 'extra_option',
                'value' => array( 'image-button' ),
            ),
        ),
        array(
            'param_name' => 'image_video_link',
            'type' => 'href',
            'heading' => __( 'Video link', 'nrghost' ),
            'description' => __( 'Enter video URL', 'nrghost' ),
            'dependency' => array(
                'element' => 'extra_option',
                'value' => array( 'image-video' ),
            ),
        ),
		array(
			'param_name'	=> 'background',
			'type'			=> 'attach_image',
			'heading'		=> __('Background', 'nrghost'),
		),
		array(
			'param_name'	=> 'layout',
			'type'			=> 'dropdown',
			'heading'		=> 'Slide layout',
			'value'			=> array(
				__( 'Content on the right side', 'nrghost' )	=> 'right',
				__( 'Content on the left side', 'nrghost' )		=> 'left',
			),
			'std'			=> 'right',
			'description'	=> __('Choose layout of current slide', 'nrghost'),
		),
		array(
			'param_name'	=> 'el_class',
			'type'			=> 'textfield',
			'heading'		=> __('Extra class name', 'nrghost'),
			'description'	=> __('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'nrghost')
		),
	)
) );

if ( class_exists( 'WPBakeryShortCodesContainer' ) ) {
	class WPBakeryShortCode_Nrghost_Slider extends WPBakeryShortCodesContainer {}
}
if ( class_exists( 'WPBakeryShortCode' ) ) {
	class WPBakeryShortCode_Nrghost_Slide extends WPBakeryShortCode {}
}



// ==========================================================================================
// SERVICES
// ==========================================================================================
vc_map( array(
	'name'						=> __( 'Services', 'nrghost' ),
	'base'						=> 'nrghost_services',
	'description'				=> 'Services',
	'category'					=> 'Custom Content',
	'as_parent'					=> array( 'only' => 'nrghost_service' ),
	'content_element'			=> true,
	'show_settings_on_create'	=> true,
	'params'					=> array(
		array(
			'param_name'	=> 'style',
			'type'			=> 'dropdown',
			'heading'		=> 'Style',
			'value'			=> array(
				__( 'Style 1', 'nrghost' ) => '1',
				__( 'Style 2', 'nrghost' ) => '2',
			),
		),
		array(
			'param_name'	=> 'el_class',
			'type'			=> 'textfield',
			'heading'		=> __( 'Extra class name', 'nrghost' ),
			'description'	=> __( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'nrghost' ),
		),
		$animation_params,
	),
	'js_view' => 'VcColumnView'
) );



vc_map( array(
	'name'						=> __('Service', 'nrghost'),
	'base'						=> 'nrghost_service',
	'content_element'			=> true,
	'as_child'					=> array( 'only' => 'nrghost_services' ),
	'params' 					=> array(
		array(
			'param_name'	=> 'title',
			'type'			=> 'textfield',
			'heading'		=> __('Title', 'nrghost'),
			'admin_label'	=> true,
			'value'			=> '',
		),
		array(
			'param_name'	=> 'description',
			'type'			=> 'textarea',
			'heading'		=> __('Description', 'nrghost'),
		),
		array(
			'param_name'	=> 'image',
			'type'			=> 'attach_image',
			'heading'		=> __('Image', 'nrghost'),
		),
		array(
			'param_name'	=> 'el_class',
			'type'			=> 'textfield',
			'heading'		=> __('Extra class name', 'nrghost'),
			'description'	=> __('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'nrghost')
		),
	)
) );

if ( class_exists( 'WPBakeryShortCodesContainer' ) ) {
	class WPBakeryShortCode_Nrghost_Services extends WPBakeryShortCodesContainer {}
}
if ( class_exists( 'WPBakeryShortCode' ) ) {
	class WPBakeryShortCode_Nrghost_Service extends WPBakeryShortCode {}
}



// ==========================================================================================
// PLAN
// ==========================================================================================
vc_map( array(
	'name'				=> __( 'Plan', 'nrghost' ),
	'base'				=> 'nrghost_plan',
	'category'			=> 'Custom Content',
	'icon'				=> 'icon-wpb-toggle-small-expand',
	'description'		=> __( 'Plan', 'nrghost' ),
	'params'			=> array(
		array(
			'param_name'	=> 'title',
			'type'			=> 'textfield',
			'heading'		=> __( 'Title', 'nrghost' ),
			'admin_label'	=> true,
		),
		array(
			'param_name'	=> 'description',
			'type'			=> 'textarea',
			'heading'		=> __( 'Description', 'nrghost' ),
		),
		array(
			'param_name'	=> 'image',
			'type'			=> 'attach_image',
			'heading'		=> __('Image (optional)', 'nrghost'),
		),
		array(
			'param_name'	=> 'conditions',
			'type'			=> 'textarea',
			'heading'		=> __( 'List of conditions (optional)', 'nrghost' ),
			'description'	=> __( 'You can use tag &lt;b&gt;&lt;/b&gt; for bold text', 'nrghost' ),
		),
		array(
			'param_name'	=> 'price',
			'type'			=> 'textfield',
			'heading'		=> __( 'Price', 'nrghost' ),
			'description'	=> __( 'You can use tag &lt;b&gt;&lt;/b&gt; for bold text', 'nrghost' ),
		),
		array(
			'param_name'	=> 'old_price',
			'type'			=> 'textfield',
			'heading'		=> __( 'Old price', 'nrghost' ),
		),
		array(
			'param_name'	=> 'period',
			'type'			=> 'textfield',
			'heading'		=> __( 'Period', 'nrghost' ),
		),
		array(
			'param_name'	=> 'button',
			'type'			=> 'vc_link',
			'heading'		=> __( 'Button', 'nrghost' ),
		),
		array(
			'param_name'	=> 'el_class',
			'type'			=> 'textfield',
			'heading'		=> __( 'Extra class name', 'nrghost' ),
			'description'	=> __( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'nrghost' ),
			'admin_label'	=> true,
		),
		$animation_params,
		$animation_delay_params,
	)

) );



// ==========================================================================================
// ADVANTAGES
// ==========================================================================================
vc_map( array(
	'name'						=> __( 'Advantages', 'nrghost' ),
	'base'						=> 'nrghost_advantages',
	'description'				=> __( 'Advantages', 'nrghost' ),
	'category'					=> 'Custom Content',
	'as_parent'					=> array( 'only' => 'nrghost_advantage' ),
	'content_element'			=> true,
	'show_settings_on_create'	=> false,
	'params'					=> array(
		array(
			'param_name'	=> 'style',
			'type'			=> 'dropdown',
			'heading'		=> __( 'Style', 'nrghost' ),
			'value'			=> array(
				__( 'Style 1', 'nrghost' ) => '1',
				__( 'Style 2', 'nrghost' ) => '2',
				__( 'Style 3', 'nrghost' ) => '3',
			),
		),
		array(
			'param_name'	=> 'main_title',
			'type'			=> 'textfield',
			'heading'		=> __( 'Big section default title', 'nrghost' ),
			'dependency'	=> array( 'element' => 'style', 'value' => array('1') ),
		),
		array(
			'param_name'	=> 'main_description',
			'type'			=> 'textarea',
			'heading'		=> __( 'Big section default description', 'nrghost' ),
			'dependency'	=> array( 'element' => 'style', 'value' => array('1') ),
		),
		array(
			'param_name'	=> 'background',
			'type'			=> 'attach_image',
			'heading'		=> __( 'Background', 'nrghost' ),
			'dependency'	=> array( 'element' => 'style', 'value' => array('1') ),
		),
		array(
			'param_name'	=> 'el_class',
			'type'			=> 'textfield',
			'heading'		=> __( 'Extra class name', 'nrghost' ),
			'description'	=> __( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'nrghost' ),
		),
		$animation_params,
	),
	'js_view' => 'VcColumnView'
) );



vc_map( array(
	'name'						=> __( 'Advantage', 'nrghost' ),
	'base'						=> 'nrghost_advantage',
	'content_element'			=> true,
	'as_child'					=> array( 'only' => 'nrghost_advantages' ),
	'params' 					=> array(
		array(
			'param_name'	=> 'title',
			'type'			=> 'textfield',
			'heading'		=> __( 'Title', 'nrghost' ),
			'admin_label'	=> true,
			'value'			=> '',
		),
		array(
			'param_name'	=> 'description',
			'type'			=> 'textarea',
			'heading'		=> __( 'Description', 'nrghost' ),
		),
		array(
			'param_name'	=> 'image',
			'type'			=> 'attach_image',
			'heading'		=> __( 'Image', 'nrghost' ),
		),
		array(
			'param_name'	=> 'main_title',
			'type'			=> 'textfield',
			'heading'		=> __( 'Big section title', 'nrghost' ),
			'description'	=> __( 'Works only on style 1', 'nrghost' ),
		),
		array(
			'param_name'	=> 'main_description',
			'type'			=> 'textarea',
			'heading'		=> __( 'Big section description', 'nrghost' ),
			'description'	=> __( 'Works only on style 1', 'nrghost' ),
		),
		array(
			'param_name'	=> 'background',
			'type'			=> 'attach_image',
			'heading'		=> __( 'Background', 'nrghost' ),
			'description'	=> __( 'Works only on style 1', 'nrghost' ),
		),
		array(
			'param_name'	=> 'el_class',
			'type'			=> 'textfield',
			'heading'		=> __( 'Extra class name', 'nrghost' ),
			'description'	=> __( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'nrghost' ),
		),
	)
) );

if ( class_exists( 'WPBakeryShortCodesContainer' ) ) {
	class WPBakeryShortCode_Nrghost_Advantages extends WPBakeryShortCodesContainer {}
}
if ( class_exists( 'WPBakeryShortCode' ) ) {
	class WPBakeryShortCode_Nrghost_Advantage extends WPBakeryShortCode {}
}



// ==========================================================================================
// SEARCH (enabled only if plugin WP-domain is active)
// ==========================================================================================
if ( is_plugin_active( 'wp-domain-checker/wdc.php' && ( class_exists( 'TitanFramework' ) ) ) ) {
	vc_map( array(
		'name'				=> __( 'Search', 'nrghost' ),
		'base'				=> 'nrghost_search',
		'category'			=> 'Custom Content',
		'icon'				=> '',
		'description'		=> __( 'Search block', 'nrghost' ),
		'params'			=> array(
			array(
				'param_name'	=> 'style',
				'type'			=> 'dropdown',
				'heading'		=> __( 'Style', 'nrghost' ),
				'value'			=> array(
					__( 'Dark', 'nrghost' )		=> 'style-0',
					__( 'Light', 'nrghost' )	=> 'style-1',
				),
			),
			array(
				'param_name'	=> 'title',
				'type'			=> 'textfield',
				'heading'		=> __( 'Title', 'nrghost' ),
				'admin_label'	=> true,
			),
			array(
				'param_name'	=> 'subtitle',
				'type'			=> 'textarea',
				'heading'		=> __( 'Subtitle', 'nrghost' ),
				'description'	=> __( "You can use tags:<br>&lt;b&gt;&lt;/b&gt; for bold text,<br> &lt;em&gt;&lt;/em&gt; for colored text,<br>&lt;small&gt;&lt;/small&gt; for smaller text", 'nrghost' ),
			),
			array(
				'param_name'	=> 'links',
				'type'			=> 'dropdown',
				'heading'		=> __( 'Links number', 'nrghost' ),
				'value'			=> array(
					'0'		=> '0',
					'1'		=> '1',
					'2'		=> '2',
					'3'		=> '3',
					'4'		=> '4',
					'5'		=> '5',
				),
				'std'			=> '0',
			),
			array(
				'param_name'	=> 'link1',
				'type'			=> 'vc_link',
				'heading'		=> __( 'Link 1', 'nrghost' ),
				'dependency'	=> array( 'element' => 'links', 'value' => array('1', '2', '3', '4', '5') ),
			),
			array(
				'param_name'	=> 'link2',
				'type'			=> 'vc_link',
				'heading'		=> __( 'Link 2', 'nrghost' ),
				'dependency'	=> array( 'element' => 'links', 'value' => array('2', '3', '4', '5') ),
			),
			array(
				'param_name'	=> 'link3',
				'type'			=> 'vc_link',
				'heading'		=> __( 'Link 3', 'nrghost' ),
				'dependency'	=> array( 'element' => 'links', 'value' => array('3', '4', '5') ),
			),
			array(
				'param_name'	=> 'link4',
				'type'			=> 'vc_link',
				'heading'		=> __( 'Link 4', 'nrghost' ),
				'dependency'	=> array( 'element' => 'links', 'value' => array('4', '5') ),
			),
			array(
				'param_name'	=> 'link5',
				'type'			=> 'vc_link',
				'heading'		=> __( 'Link 5', 'nrghost' ),
				'dependency'	=> array( 'element' => 'links', 'value' => array('5') ),
			),
			array(
				'param_name'	=> 'el_class',
				'type'			=> 'textfield',
				'heading'		=> __( 'Extra class name', 'nrghost' ),
				'description'	=> __( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'nrghost' ),
			),
			$animation_params,
		)

	) );
}



// ==========================================================================================
// FEATURES
// ==========================================================================================
vc_map( array(
	'name'						=> __( 'Features', 'nrghost' ),
	'base'						=> 'nrghost_features',
	'description'				=> 'Features section',
	'category'					=> 'Custom Content',
	'as_parent'					=> array( 'only' => 'nrghost_nested_feature' ),
	'content_element'			=> true,
	'show_settings_on_create'	=> true,
	'params'					=> array(
		array(
			'param_name'	=> 'style',
			'type'			=> 'dropdown',
			'heading'		=> __( 'Style', 'nrghost' ),
			'value'			=> array(
				__( 'Features', 'nrghost' ) => '1',
				__( 'Timeline', 'nrghost' ) => '2',
			),
		),
		array(
			'param_name'	=> 'el_class',
			'type'			=> 'textfield',
			'heading'		=> __( 'Extra class name', 'nrghost' ),
			'description'	=> __( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'nrghost' ),
		),
		$animation_params,
	),
	'js_view' => 'VcColumnView'
) );



vc_map( array(
	'name'						=> __('Feature', 'nrghost'),
	'base'						=> 'nrghost_nested_feature',
	'content_element'			=> true,
	'as_child'					=> array( 'only' => 'nrghost_features' ),
	'params' 					=> array(
		array(
			'param_name'	=> 'title',
			'type'			=> 'textfield',
			'heading'		=> __( 'Title', 'nrghost' ),
			'admin_label'	=> true,
			'value'			=> '',
		),
		array(
			'param_name'	=> 'description',
			'type'			=> 'textarea',
			'heading'		=> __( 'Description', 'nrghost' ),
		),
		array(
			'param_name'	=> 'image',
			'type'			=> 'attach_image',
			'heading'		=> __( 'Image', 'nrghost' ),
		),
		array(
			'param_name'	=> 'date',
			'type'			=> 'textfield',
			'heading'		=> __( 'Date (optional)', 'nrghost' ),
			'description'	=> __( 'Shows only on Timeline type', 'nrghost' ),
		),
	)
) );

if ( class_exists( 'WPBakeryShortCodesContainer' ) ) {
	class WPBakeryShortCode_Nrghost_Features extends WPBakeryShortCodesContainer {}
}
if ( class_exists( 'WPBakeryShortCode' ) ) {
	class WPBakeryShortCode_Nrghost_Nested_Feature extends WPBakeryShortCode {}
}



// ==========================================================================================
// FEATURE
// ==========================================================================================
vc_map( array(
	'name'				=> __( 'Feature', 'nrghost' ),
	'base'				=> 'nrghost_feature',
	'category'			=> 'Custom Content',
	'icon'				=> '',
	'description'		=> __( 'Feature block', 'nrghost' ),
	'params'			=> array(
		array(
			'param_name'	=> 'title',
			'type'			=> 'textfield',
			'heading'		=> __( 'Title', 'nrghost' ),
			'admin_label'	=> true,
		),
		array(
			'param_name'	=> 'description',
			'type'			=> 'textarea',
			'heading'		=> __( 'Description', 'nrghost' ),
		),
		array(
			'param_name'	=> 'image',
			'type'			=> 'attach_image',
			'heading'		=> __( 'Image', 'nrghost' ),
		),
		array(
			'param_name'	=> 'rounded_image',
			'type'			=> 'checkbox',
			'heading'		=> __( 'Rounded image', 'nrghost' ),
			'value'			=> array( __( 'Rounded', 'nrghost' ) => 'yes' ),
		),
		array(
			'param_name'	=> 'style',
			'type'			=> 'dropdown',
			'heading'		=> __( 'Style', 'nrghost' ),
			'value'			=> array(
				__( 'Feature', 'nrghost' ) => '1',
				__( 'FAQ item', 'nrghost' ) => '2',
			),
		),
		array(
			'param_name'	=> 'el_class',
			'type'			=> 'textfield',
			'heading'		=> __( 'Extra class name', 'nrghost' ),
			'description'	=> __( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'nrghost' ),
		),
		$animation_params,
		$animation_delay_params,
	)

) );



// ==========================================================================================
// BUTTON
// ==========================================================================================
vc_map( array(
	'name'				=> __( 'Button', 'nrghost' ),
	'base'				=> 'nrghost_button',
	'category'			=> 'Custom Content',
	'icon'				=> 'icon-wpb-ui-button',
	'description'		=> __( 'Button', 'nrghost' ),
	'params'			=> array(
		array(
			'param_name'	=> 'link',
			'type'			=> 'vc_link',
			'heading'		=> __( 'Link', 'nrghost' ),
			'admin_label'	=> true,
		),
		array(
			'param_name'	=> 'align',
			'type'			=> 'dropdown',
			'heading'		=> 'Button align',
			'value'			=> array(
				__( 'Center', 'nrghost' )	=> 'text-center',
				__( 'Left', 'nrghost' )		=> 'text-left',
				__( 'Right', 'nrghost' )	=> 'text-right',
			),
		),
		array(
			'param_name'	=> 'el_class',
			'type'			=> 'textfield',
			'heading'		=> __( 'Extra class name', 'nrghost' ),
			'description'	=> __( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'nrghost' ),
		),
		$animation_params,
		$animation_delay_params,
	)

) );



// ==========================================================================================
// CALL TO ACTION
// ==========================================================================================
vc_map( array(
	'name'				=> __( 'Call To Action', 'nrghost' ),
	'base'				=> 'nrghost_cta',
	'category'			=> 'Custom Content',
	'icon'				=> 'icon-wpb-call-to-action',
	'description'		=> __( 'Call To Action Block', 'nrghost' ),
	'params'			=> array(
		array(
			'param_name'	=> 'title',
			'type'			=> 'textfield',
			'heading'		=> __( 'Title', 'nrghost' ),
			'admin_label'	=> true,
		),
		array(
			'param_name'	=> 'content',
			'type'			=> 'textarea_html',
			'heading'		=> __( 'Proposition', 'nrghost' ),
			'description'	=> __( "You can use tags:<br>&lt;b&gt;&lt;/b&gt; for bold text,<br> &lt;em&gt;&lt;/em&gt; for colored text,<br>&lt;small&gt;&lt;/small&gt; for smaller text", 'nrghost' ),
		),
		array(
			'param_name'	=> 'el_class',
			'type'			=> 'textfield',
			'heading'		=> __( 'Extra class name', 'nrghost' ),
			'description'	=> __( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'nrghost' ),
		),
		$animation_params,
	)

) );



// ==========================================================================================
// SUPPORT
// ==========================================================================================
vc_map( array(
	'name'				=> __( 'Support', 'nrghost' ),
	'base'				=> 'nrghost_support',
	'category'			=> 'Custom Content',
	'icon'				=> '',
	'description'		=> __( 'Support section', 'nrghost' ),
	'params'			=> array(
		array(
			'param_name'	=> 'title',
			'type'			=> 'textfield',
			'heading'		=> __( 'Title', 'nrghost' ),
			'admin_label'	=> true,
		),
		array(
			'param_name'	=> 'description',
			'type'			=> 'textarea',
			'heading'		=> __( 'Description', 'nrghost' ),
		),
		array(
			'param_name'	=> 'background',
			'type'			=> 'attach_image',
			'heading'		=> __( 'Background image', 'nrghost' ),
		),
		array(
			'param_name'	=> 'el_class',
			'type'			=> 'textfield',
			'heading'		=> __( 'Extra class name', 'nrghost' ),
			'description'	=> __( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'nrghost' ),
		),
		$animation_params,
	)

) );



// ==========================================================================================
// CLIENTS
// ==========================================================================================
vc_map( array(
	'name'						=> __( 'Clients', 'nrghost' ),
	'base'						=> 'nrghost_clients',
	'description'				=> 'Clients section',
	'category'					=> 'Custom Content',
	'as_parent'					=> array( 'only' => 'nrghost_client' ),
	'content_element'			=> true,
	'show_settings_on_create'	=> true,
	'params'					=> array(
		array(
			'param_name'	=> 'el_class',
			'type'			=> 'textfield',
			'heading'		=> __( 'Extra class name', 'nrghost' ),
			'description'	=> __( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'nrghost' ),
		),
		$animation_params,
	),
	'js_view' => 'VcColumnView'
) );



vc_map( array(
	'name'						=> __('Client', 'nrghost'),
	'base'						=> 'nrghost_client',
	'content_element'			=> true,
	'as_child'					=> array( 'only' => 'nrghost_clients' ),
	'params' 					=> array(
		array(
			'param_name'	=> 'title',
			'type'			=> 'textfield',
			'heading'		=> __('Title', 'nrghost'),
			'admin_label'	=> true,
			'value'			=> '',
		),
		array(
			'param_name'	=> 'image',
			'type'			=> 'attach_image',
			'heading'		=> __('Image', 'nrghost'),
		),
		array(
			'param_name'	=> 'el_class',
			'type'			=> 'textfield',
			'heading'		=> __('Extra class name', 'nrghost'),
			'description'	=> __('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'nrghost')
		),
	)
) );

if ( class_exists( 'WPBakeryShortCodesContainer' ) ) {
	class WPBakeryShortCode_Nrghost_Clients extends WPBakeryShortCodesContainer {}
}
if ( class_exists( 'WPBakeryShortCode' ) ) {
	class WPBakeryShortCode_Nrghost_Client extends WPBakeryShortCode {}
}



// ==========================================================================================
// PROCESS
// ==========================================================================================
vc_map( array(
	'name'						=> __( 'Process', 'nrghost' ),
	'base'						=> 'nrghost_process',
	'description'				=> 'Process section',
	'category'					=> 'Custom Content',
	'as_parent'					=> array( 'only' => 'nrghost_process_step' ),
	'content_element'			=> true,
	'show_settings_on_create'	=> true,
	'params'					=> array(
		array(
			'param_name'	=> 'el_class',
			'type'			=> 'textfield',
			'heading'		=> __( 'Extra class name', 'nrghost' ),
			'description'	=> __( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'nrghost' ),
		),
		$animation_params,
	),
	'js_view' => 'VcColumnView'
) );



vc_map( array(
	'name'						=> __('Step', 'nrghost'),
	'base'						=> 'nrghost_process_step',
	'content_element'			=> true,
	'as_child'					=> array( 'only' => 'nrghost_process' ),
	'params' 					=> array(
		array(
			'param_name'	=> 'title',
			'type'			=> 'textfield',
			'heading'		=> __( 'Title', 'nrghost' ),
			'admin_label'	=> true,
			'value'			=> '',
		),
		array(
			'param_name'	=> 'description',
			'type'			=> 'textarea',
			'heading'		=> __( 'Description', 'nrghost' ),
		),
		array(
			'param_name'	=> 'image',
			'type'			=> 'attach_image',
			'heading'		=> __( 'Image', 'nrghost' ),
		),
		array(
			'param_name'	=> 'el_class',
			'type'			=> 'textfield',
			'heading'		=> __( 'Extra class name', 'nrghost' ),
			'description'	=> __( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'nrghost' ),
		),
	)
) );

if ( class_exists( 'WPBakeryShortCodesContainer' ) ) {
	class WPBakeryShortCode_Nrghost_Process extends WPBakeryShortCodesContainer {}
}
if ( class_exists( 'WPBakeryShortCode' ) ) {
	class WPBakeryShortCode_Nrghost_Process_Step extends WPBakeryShortCode {}
}



// ==========================================================================================
// TABS
// ==========================================================================================
$tab_id_1 = ''; // 'def' . time() . '-1-' . rand( 0, 100 );
$tab_id_2 = ''; // 'def' . time() . '-2-' . rand( 0, 100 );
$tab_id_3 = ''; // 'def' . time() . '-3-' . rand( 0, 100 );
vc_map( array(
	'name'						=> __( 'Tabs', 'nrghost' ),
	'base'						=> 'vc_tabs',
	'show_settings_on_create'	=> false,
	'is_container'				=> true,
	'icon'						=> 'icon-wpb-ui-tab-content',
	'category'					=> __( 'Custom Content', 'nrghost' ),
	'description'				=> __( 'Tabbed content', 'nrghost' ),
	'params'					=> array(

		array(
			'param_name'	=> 'style',
			'type'			=> 'dropdown',
			'heading'		=> 'Style',
			'value'			=> array(
				__( 'Style 1', 'nrghost' )	=> '1',
				__( 'Style 2', 'nrghost' )	=> '2',
			),
		),

		array(
			'type'			=> 'textfield',
			'heading'		=> __( 'Extra class name', 'nrghost' ),
			'param_name'	=> 'el_class',
			'description'	=> __( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'nrghost' )
		),

		$animation_params,
	),
	'custom_markup'				=> '
<div class="wpb_tabs_holder wpb_holder vc_container_for_children">
<ul class="tabs_controls">
</ul>
%content%
</div>'
,
	'default_content'			=> '
[vc_tab title="' . __( 'Tab 1', 'nrghost' ) . '" tab_id="' . $tab_id_1 . '"][/vc_tab]
[vc_tab title="' . __( 'Tab 2', 'nrghost' ) . '" tab_id="' . $tab_id_2 . '"][/vc_tab]
[vc_tab title="' . __( 'Tab 3', 'nrghost' ) . '" tab_id="' . $tab_id_3 . '"][/vc_tab]
',
	'js_view'					=> 'VcTabsView',
) );



vc_map( array(
	'name'						=> __( 'Tab', 'nrghost' ),
	'base'						=> 'vc_tab',
	'allowed_container_element'	=> 'vc_row',
	'is_container'				=> true,
	'content_element'			=> false,
	'params'					=> array(
		array(
			'type'			=> 'textfield',
			'heading'		=> __( 'Title', 'nrghost' ),
			'param_name'	=> 'title',
			'description'	=> __( 'Enter title of tab.', 'nrghost' )
		),
		array(
			'type'			=> 'tab_id',
			'heading'		=> __( 'Tab ID', 'nrghost' ),
			'param_name'	=> 'tab_id'
		)
	),
	'js_view'				=> 'VcTabView',
) );



// ==========================================================================================
// ACCORDION BLOCK
// ==========================================================================================
vc_map( array(
	'name'						=> __( 'Accordion', 'nrghost' ),
	'base'						=> 'vc_accordion',
	'show_settings_on_create'	=> false,
	'is_container'				=> true,
	'icon'						=> 'icon-wpb-ui-accordion',
	'category'					=> __( 'Content', 'nrghost' ),
	'description'				=> __( 'Collapsible content panels', 'nrghost' ),
	'params'					=> array(
		array(
			'param_name'	=> 'el_class',
			'type'			=> 'textfield',
			'heading'		=> __( 'Extra class name', 'nrghost' ),
			'description'	=> __( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'nrghost' )
		),
		$animation_params,
	),
	'custom_markup' => '
<div class="wpb_accordion_holder wpb_holder clearfix vc_container_for_children">
%content%
</div>
<div class="tab_controls">
    <a class="add_tab" title="' . __( 'Add section', 'nrghost' ) . '"><span class="vc_icon"></span> <span class="tab-label">' . __( 'Add section', 'nrghost' ) . '</span></a>
</div>
',
	'default_content' => '
    [vc_accordion_tab title="' . __( 'Section 1', 'nrghost' ) . '"][/vc_accordion_tab]
    [vc_accordion_tab title="' . __( 'Section 2', 'nrghost' ) . '"][/vc_accordion_tab]
    [vc_accordion_tab title="' . __( 'Section 3', 'nrghost' ) . '"][/vc_accordion_tab]
',
	'js_view' => 'VcAccordionView'
) );



vc_map( array(
	'name'						=> __( 'Section', 'nrghost' ),
	'base'						=> 'vc_accordion_tab',
	'allowed_container_element'	=> 'vc_row',
	'is_container'				=> true,
	'content_element'			=> false,
	'params'					=> array(
		array(
			'param_name'	=> 'title',
			'type'			=> 'textfield',
			'heading'		=> __( 'Title', 'nrghost' ),
			'description'	=> __( 'Enter accordion section title.', 'nrghost' ),
		),
		array(
			'param_name'	=> 'el_class',
			'type'			=> 'textfield',
			'heading'		=> __( 'Extra class name', 'nrghost' ),
			'description'	=> __( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'nrghost' )
		),
	),
	'js_view' => 'VcAccordionTabView'
) );



// ==========================================================================================
// TESTIMONIALS
// ==========================================================================================
vc_map( array(
	'name'						=> __( 'Testimonials', 'nrghost' ),
	'base'						=> 'nrghost_testimonials',
	'description'				=> 'Testimonials',
	'category'					=> 'Custom Content',
	'as_parent'					=> array( 'only' => 'nrghost_testimonial' ),
	'content_element'			=> true,
	'show_settings_on_create'	=> true,
	'params'					=> array(
		array(
			'param_name'	=> 'style',
			'type'			=> 'dropdown',
			'heading'		=> 'Style',
			'value'			=> array(
				__( 'Style 1', 'nrghost' ) => '1',
				__( 'Style 2', 'nrghost' ) => '2',
				__( 'Style 3', 'nrghost' ) => '3',
				__( 'Style 4', 'nrghost' ) => '4',
				__( 'Style 5', 'nrghost' ) => '5',
				__( 'Style 6', 'nrghost' ) => '6',
			),
		),
		array(
			'param_name'	=> 'active',
			'type'			=> 'textfield',
			'heading'		=> __('Active testimonial', 'nrghost'),
			'value'			=> '1',
		),
		array(
			'param_name'	=> 'el_class',
			'type'			=> 'textfield',
			'heading'		=> __( 'Extra class name', 'nrghost' ),
			'description'	=> __( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'nrghost' ),
		),
		$animation_params,
	),
	'js_view' => 'VcColumnView'
) );



vc_map( array(
	'name'						=> __('Testimonial', 'nrghost'),
	'base'						=> 'nrghost_testimonial',
	'content_element'			=> true,
	'as_child'					=> array( 'only' => 'nrghost_testimonials' ),
	'params' 					=> array(
		array(
			'param_name'	=> 'image',
			'type'			=> 'attach_image',
			'heading'		=> __('Photo', 'nrghost'),
		),
		array(
			'param_name'	=> 'name',
			'type'			=> 'textfield',
			'heading'		=> __('Name', 'nrghost'),
			'admin_label'	=> true,
			'value'			=> '',
		),
		array(
			'param_name'	=> 'profession',
			'type'			=> 'textfield',
			'heading'		=> __('Profession', 'nrghost'),
		),
		array(
			'param_name'	=> 'testimonial',
			'type'			=> 'textarea',
			'heading'		=> __('Testimonial', 'nrghost'),
		),
		array(
			'param_name'	=> 'el_class',
			'type'			=> 'textfield',
			'heading'		=> __('Extra class name', 'nrghost'),
			'description'	=> __('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'nrghost')
		),
	)
) );

if ( class_exists( 'WPBakeryShortCodesContainer' ) ) {
	class WPBakeryShortCode_Nrghost_Testimonials extends WPBakeryShortCodesContainer {}
}
if ( class_exists( 'WPBakeryShortCode' ) ) {
	class WPBakeryShortCode_Nrghost_Testimonial extends WPBakeryShortCode {}
}



// ==========================================================================================
// BLOG
// ==========================================================================================
vc_map( array(
	'name'			=> __( 'Blog', 'nrghost' ),
	'base'			=> 'nrghost_blog',
	'icon'			=> '',
	'description'	=> __( 'Blog section', 'nrghost' ),
	'category'		=> 'Custom Content',
	'params'		=> array(

		array(
			'param_name'	=> 'style',
			'type'			=> 'dropdown',
			'heading'		=> 'Style',
			'value'			=> array(
				__( 'Style 1', 'nrghost' )	=> '1',
				__( 'Style 2', 'nrghost' )	=> '2',
			),
		),

		array(
			'type'			=> 'vc_efa_chosen',
			'heading'		=> __( 'Select Categories', 'nrghost' ),
			'param_name'	=> 'cats',
			'placeholder'	=> __( 'Select category', 'nrghost' ),
			'value'			=> nrghost_element_values( 'categories', array(
				'sort_order'	=> 'ASC',
				'taxonomy'		=> 'category',
				'hide_empty'	=> false,
			) ),
			'std'			=> '',
			'description'	=> __( 'You can choose spesific categories of posts, default is all categories', 'nrghost' ),
		),

		array(
			'param_name'	=> 'orderby',
			'type'			=> 'dropdown',
			'heading'		=> 'Order by',
			'value'			=> array(
				__( 'None', 'nrghost' )					=> 'none',
				__( 'ID', 'nrghost' )					=> 'ID',
				__( 'Author', 'nrghost' )				=> 'author',
				__( 'Title', 'nrghost' )				=> 'title',
				__( 'Date', 'nrghost' )					=> 'date',
				__( 'Modified', 'nrghost' )				=> 'modified',
				__( 'Post parent', 'nrghost' )			=> 'parent',
				__( 'Random', 'nrghost' )				=> 'rand',
				__( 'Number of comments', 'nrghost' )	=> 'comment_count',
				__( 'Menu order', 'nrghost' )			=> 'menu_order',
			),
			'std'			=> 'date',
		),

		array(
			'param_name'	=> 'order',
			'type'			=> 'dropdown',
			'heading'		=> 'Sorting',
			'value'			=> array(
				__( 'Ascending', 'nrghost' )	=> 'ASC',
				__( 'Descending', 'nrghost' )	=> 'DESC',
			),
			'std'			=> 'DESC',
		),
	),
));

/* Google maps
----------------------------------------------------------- */
vc_map( array(
  'name'          => __( 'Custom Google Maps', 'js_composer' ),
  'base'          => 'nrghost_maps',
  'icon' => 'icon-wpb-map-pin',
  'params' => array(
    
    array(
      'type' => 'textfield',
      'heading' => __( 'Gmaps latitude', 'js_composer' ),
      'param_name' => 'latitude',
      'value' => '43.653226'
    ),
    array(
      'type' => 'textfield',
      'heading' => __( 'Gmaps longitude', 'js_composer' ),
      'param_name' => 'longitude',
      'value' => '-79.383184'
    ),
    array(
      'type' => 'textfield',
      'heading' => __( 'Zoom', 'js_composer' ),
      'param_name' => 'zoom',
      'value' => '3',
    ),
    array(
      'type'        => 'attach_image',
      'heading'     => __( 'Select image marker', 'js_composer' ),
      'param_name'  => 'marker',
    ),

    array(
      'type' => 'param_group',
      'heading' => __( 'Addresses Block', 'js_composer' ),
      'param_name' => 'group_adress',
      'value' => urlencode( json_encode( array(

       ) ) ),
      'params' => array(
      			array(
      			  'type' => 'textfield',
      			  'heading' => __( 'Gmaps latitude', 'js_composer' ),
      			  'param_name' => 'latitude',
      			  'value' => '43.653226'
      			),
      			array(
      			  'type' => 'textfield',
      			  'heading' => __( 'Gmaps longitude', 'js_composer' ),
      			  'param_name' => 'longitude',
      			  'value' => '-107.383184'
      			),
      			array(
      			  'type' => 'textarea',
      			  'heading' => __( 'Description point', 'js_composer' ),
      			  'param_name' => 'description',
      			  'admin_label' => true,
      			),
      ),
    ),
    array(
    	'param_name'	=> 'el_class',
    	'type'			=> 'textfield',
    	'heading'		=> __('Extra class name', 'nrghost'),
    	'description'	=> __('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'nrghost')
    ),
  )
) );



// ==========================================================================================
// LATEST NEWS
// ==========================================================================================
vc_map( array(
	'name'			=> __( 'Latest news', 'nrghost' ),
	'base'			=> 'nrghost_latest_news',
	'icon'			=> '',
	'description'	=> __( 'Latest news', 'nrghost' ),
	'category'		=> 'Custom Content',
	'params'		=> array(

		array(
			'param_name'	=> 'cats',
			'type'			=> 'vc_efa_chosen',
			'heading'		=> __( 'Select Categories', 'nrghost' ),
			'placeholder'	=> __( 'Select category', 'nrghost' ),
			'value'			=> nrghost_element_values( 'categories', array(
				'sort_order'	=> 'ASC',
				'taxonomy'		=> 'category',
				'hide_empty'	=> false,
			) ),
			'std'			=> '',
			'description'	=> __( 'You can choose spesific categories of posts, default is all categories', 'nrghost' ),
		),

		array(
			'param_name'	=> 'number',
			'type'			=> 'textfield',
			'heading'		=> __('Posts number', 'nrghost'),
		),

		array(
			'param_name'	=> 'el_class',
			'type'			=> 'textfield',
			'heading'		=> __('Extra class name', 'nrghost'),
			'description'	=> __('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'nrghost')
		),
		$animation_params,
	),
));



// ==========================================================================================
// MESSAGE
// ==========================================================================================
vc_map( array(
	'name'				=> __( 'Message Box', 'nrghost' ),
	'base'				=> 'nrghost_message',
	'category'			=> 'Custom Content',
	'icon'				=> 'icon-wpb-information-white',
	'description'		=> __( 'Info message', 'nrghost' ),
	'params'			=> array(
		array(
			'param_name'	=> 'style',
			'type'			=> 'dropdown',
			'heading'		=> __( 'Style', 'nrghost' ),
			'value'			=> array(
				__( 'Danger (red)', 'nrghost' )			=> 'danger',
				__( 'Warning (orange)', 'nrghost' )		=> 'warning',
				__( 'Success (green)', 'nrghost' )		=> 'success',
				__( 'Info (blue)', 'nrghost' )			=> 'info',
			),
			'std'			=> 'info',
		),
		array(
			'param_name'	=> 'close_button',
			'type'			=> 'checkbox',
			'heading'		=> __( 'Close button', 'nrghost' ),
			'value'			=> array( __( 'Show close button', 'nrghost' ) => 'yes' ),
		),
		array(
			'param_name'	=> 'content',
			'type'			=> 'textarea_html',
			'heading'		=> __( 'Content', 'nrghost' ),
		),
		array(
			'param_name'	=> 'el_class',
			'type'			=> 'textfield',
			'heading'		=> __( 'Extra class name', 'nrghost' ),
			'description'	=> __( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'nrghost' ),
			'admin_label'	=> true,
		),
		$animation_params,
	)

) );