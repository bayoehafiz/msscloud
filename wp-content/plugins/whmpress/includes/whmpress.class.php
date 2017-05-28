<?php
/**
 * Copyright (c) 2014-2016 by creativeON.
 */

/**
 * WHMPress main class
 *
 * @Since   1.3.0
 */
class WHMPress {
	
	/**
	 * It will return array for html, image files.
	 * It will return false if no files found.
	 */
	function __construct() {
		if ( ! session_id() ) {
			session_start();
		}
		if ( ! isset( $_SESSION["currency"] ) ) {
			$time = date( "H:i:s" );
			//$currenct_country = file_get_contents("http://api.hostip.info/get_json.php?ip=" . $_SERVER["REMOTE_ADDR"]);
			/*
            $response = wp_remote_get("http://api.hostip.info/get_json.php?ip=" . $_SERVER["REMOTE_ADDR"]);

            if (is_array($response)) {
                $currenct_country = $response['body'];
                $currenct_country = json_decode($currenct_country);
            } else {
                $currenct_country = json_decode(
                    array(
                        "country_name" => "(Private Address)",
                        "country_code" => "XX",
                        "city" => "(Private Address)",
                        "ip" => "::1"
                    )
                );
            }
            //echo "<!-- Response Time: $time > ".date("H:i:s")." -->";
            $countries = get_option("whmp_countries_currencies");

            if (isset($countries["country"]) && is_array($countries["country"])) {
                $__key = array_search($currenct_country->country_code, $countries["country"]);
            } else {
                $__key = false;
            }

            if ($__key!==false) {
                if ($countries["currency"][$__key]<>"") $_SESSION["currency"] = $countries["currency"][$__key];
                else $_SESSION["currency"] = whmp_get_default_currency_id('id');
            } else {
                $_SESSION["currency"] = whmp_get_default_currency_id('id');
            }*/
			$_SESSION["currency"] = $this->whmp_get_default_currency_id( 'id' );
		}
	}
	
	public function whmp_get_default_currency_id() {
		if ( ! $this->WHMpress_synced() ) {
			return '';
		}
		
		$currency = get_option( "whmpress_default_currency" );
		if ( ! empty( $currency ) && is_numeric( $currency ) ) {
			return $currency;
		}
		
		global $wpdb;
		$Q = "SELECT `id` FROM `" . $this->whmp_get_currencies_table_name() . "` WHERE `default`='1'";
		
		return $wpdb->get_var( $Q );
	}
	
	public function WHMpress_synced() {
		if ( get_option( "sync_run" ) <> "1" ) {
			return false;
		}
		global $wpdb;
		$Ts = $wpdb->get_results( "SHOW TABLES LIKE '" . $this->whmp_get_configuration_table_name() . "'", ARRAY_A );
		if ( sizeof( $Ts ) == 0 ) {
			return false;
		}
		
		return true;
	}
	
	public function whmp_get_configuration_table_name() {
		global $wpdb;
		
		return $wpdb->prefix . "whmpress_configuration";
	}
	
	public function whmp_get_currencies_table_name() {
		global $wpdb;
		
		return $wpdb->prefix . "whmpress_currencies";
	}
	
	function is_valid_domain_name( $domain_name ) {
		if ( strpos( $domain_name, " " ) !== false ) {
			return false;
		}
		if ( strlen( $domain_name ) > 253 ) {
			return false;
		}
		
		if ( preg_match( '/[\'^£$%&*()}{@#~?><>,|=_+¬]/', $domain_name ) ) {
			return false;
		}
		
		return true;
		
		/*return (preg_match("/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i", $domain_name) //valid chars check
                && preg_match("/^.{1,253}$/", $domain_name) //overall length check
                && preg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $domain_name)   ); //length of each label*/
	}
	
	function get_current_language() {
		if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
			return ICL_LANGUAGE_CODE;
		} elseif ( function_exists( 'pll_current_language' ) ) {
			return pll_current_language();
		} elseif ( isset( $_GET["lang"] ) ) {
			return $_GET["lang"];
		} else {
			return get_locale();
		}
	}
	
	function check_template_file( $html_template, $shortcode_name ) {
		$html_template = basename( $html_template );
		
		if ( get_option( "load_sytle_orders" ) == "whmpress" ) {
			$Path = WHMP_PLUGIN_DIR . "/themes/" . basename( $this->whmp_get_template_directory() ) . "/" . $shortcode_name . "/" . $html_template;
		} elseif ( get_option( "load_sytle_orders" ) == "author" ) {
			$Path = $this->whmp_get_template_directory() . "/whmpress/" . $shortcode_name . "/" . $html_template;
		} else {
			$Path = WHMP_PLUGIN_DIR . "/templates/" . $shortcode_name . "/" . $html_template;
		}
		
		if ( is_file( $Path ) ) {
			return $Path;
		}
		
		$Path = WHMP_PLUGIN_DIR . "/templates/" . $shortcode_name . "/default.html";
		
		return $Path;
		
		/*$Path = $this->whmp_get_template_directory()."/whmpress/".$shortcode_name."/".$html_template;
        if (is_file($Path)) return $Path;

        $Path = WHMP_PLUGIN_DIR."/themes/". basename($this->whmp_get_template_directory()). "/". $shortcode_name."/".$html_template;
        if (is_file($Path)) return $Path;

        $Path = WHMP_PLUGIN_DIR."/templates/".$shortcode_name."/".$html_template;
        if (is_file($Path)) return $Path;

        $Path = $this->whmp_get_template_directory()."/whmpress/".$shortcode_name."/default.html";
        if (is_file($Path)) return $Path;

        $Path = WHMP_PLUGIN_DIR."/templates/".$shortcode_name."/default.html";
        return $Path;*/
	}
	
	public function whmp_get_template_directory() {
		return str_replace( "\\", "/", get_stylesheet_directory() );
	}
	
	public function get_template_files( $shortcode_name, $tiny_compatible = false ) {
		$FilesList  = $ImagesList = $CustomFields = [];
		$ThemeFiles = true;
		
		if ( get_option( "load_sytle_orders" ) == "whmpress" ) {
			$Dir        = WHMP_PLUGIN_DIR . "/themes/" . basename( $this->whmp_get_template_directory() ) . "/" . $shortcode_name;
			$ThemeFiles = false;
		} elseif ( get_option( "load_sytle_orders" ) == "author" ) {
			$Dir        = $this->whmp_get_template_directory() . "/whmpress/" . $shortcode_name;
			$ThemeFiles = false;
			/*if ( !is_dir($Dir) ) {
                $Dir = WHMP_PLUGIN_DIR."/templates/".$shortcode_name;
                $ThemeFiles = false;
            }*/
		} else {
			$Dir        = WHMP_PLUGIN_DIR . "/templates/" . $shortcode_name;
			$ThemeFiles = false;
			/*if ( !is_dir($Dir) ) {
                $Dir = $this->whmp_get_template_directory()."/whmpress/".$shortcode_name;
                $ThemeFiles = true;
            }*/
		}
		
		if ( is_dir( $Dir ) ) {
			$Files = glob( $Dir . "/*.html" );
			foreach ( $Files as $k => $file ) {
				if ( $tiny_compatible ) {
					$FilesList[] = [ "value" => basename( $file ), "text" => substr( basename( $file ), 0, - 5 ) ];
				} else {
					$FilesList[ substr( basename( $file ), 0, - 5 ) ] = basename( $file );
				}
			}
			/*if (!$ThemeFiles) {
                $Dir = WHMP_PLUGIN_PATH."/themes/".basename($this->whmp_get_template_directory())."/".$shortcode_name;
                if (is_dir($Dir)) {
                    $Files = glob($Dir . "/*.html");
                    foreach($Files as $file) {
                        if ($tiny_compatible)
                            $FilesList[] = array("value"=>basename($file), "text"=>substr(basename($file),0,-5));
                        else
                            $FilesList[substr(basename($file),0,-5)] = basename($file);
                    }
                }
            }*/
			
			// Getting custom fields from CSV file.
			if ( is_file( $Dir . "/custom_fields.csv" ) ) {
				$CustomFields = $this->read_csv_file( $Dir . "/custom_fields.csv" );
				//if ($shortcode_name=="whmpress_pricing_table") $this->debug($CustomFields);
			}
			
			if ( is_dir( $Dir . "/images/" ) ) {
				$Files = glob( $Dir . "/images/*.{jpg,jpeg,png,gif}", GLOB_BRACE );
				foreach ( $Files as $file ) {
					if ( $ThemeFiles ) {
						if ( $tiny_compatible ) {
							$ImagesList[] = [
								"value" => get_stylesheet_directory_uri() . "/whmpress/$shortcode_name/images/" . basename( $file ),
								"text"  => basename( $file ),
							];
						} else {
							$ImagesList[ basename( $file ) ] = get_stylesheet_directory_uri() . "/whmpress/$shortcode_name/images/" . basename( $file );
						}
					} else {
						if ( $tiny_compatible ) {
							$ImagesList[] = [
								"value" => WHMP_PLUGIN_URL . "templates/$shortcode_name/images/" . basename( $file ),
								"text"  => basename( $file ),
							];
						} else {
							$ImagesList[ basename( $file ) ] = WHMP_PLUGIN_URL . "templates/$shortcode_name/images/" . basename( $file );
						}
					}
				}
			}
			
			if ( is_file( $Dir . "/whmpress.css" ) ) {
				$css_file = $Dir . "/whmpress.css";
			} else {
				$css_file = "-no-file-";
			}
			
			return [
				"html"          => $FilesList,
				"images"        => $ImagesList,
				"custom_fields" => $CustomFields,
				"css"           => $css_file,
			];
		} else {
			return false;
		}
	}
	
	public function read_csv_file( $csv_file ) {
		$rows   = array_map( 'str_getcsv', file( $csv_file ) );
		$header = array_shift( $rows );
		$header = array_filter( $header );
		$csv    = [];
		foreach ( $rows as $row ) {
			$ar = [];
			foreach ( $header as $x => $col ) {
				if ( ! isset( $row[ $x ] ) ) {
					$ar[ $col ] = null;
				} else if ( $row[ $x ] == "NULL" ) {
					$ar[ $col ] = null;
				} else {
					$ar[ $col ] = isset( $row[ $x ] ) ? $row[ $x ] : null;
				}
			}
			$csv[] = $ar;
		}
		
		return $csv;
	}
	
	public function read_remote_url( $url ) {
		$response = wp_remote_post( $url );
		
		if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
			
			return $error_message;
		} else {
			return $response["body"];
		}
	}
	
	public function read_local_file( $filepath ) {
		if ( ! is_file( $filepath ) ) {
			return false;
		}
		global $wp_filesystem;
		if ( empty( $wp_filesystem ) ) {
			require_once( ABSPATH . '/wp-admin/includes/file.php' );
			WP_Filesystem();
		}
		
		$data = $wp_filesystem->get_contents( $filepath );
		if ( $data === false ) {
			$data = file_get_contents( $filepath );
		}
		
		return $data;
	}
	
	public function font_awesome_icons() {
		include_once WHMP_PLUGIN_DIR . "/includes/font-awesome.class.php";
		$fa    = new Smk_FontAwesome;
		$icons = $fa->getArray( WHMP_ADMIN_DIR . '/css/font-awesome.css' );
		
		#$icons = $fa->sortByName($icons);   //Sort by key name. Alphabetically sort: from a to z
		$icons = $fa->onlyClass( $icons );    //Only HTML class, no unicode. 'fa-calendar' => 'fa-calendar',
		#$icons = $fa->onlyUnicode($icons);  //Only unicode, no HTML class. '\f073' => '\f073',
		#$icons = $fa->readableName($icons); //Only HTML class, readable. 'fa-video-camera' => 'Video Camera',
		
		return $icons;
	}
	
	public function get_shortcode_parameters( $shortcode ) {
		/**
		 * Explanation about parameters
		 *
		 * vc_hide = template_file, parameter will not show in VC editor if tempalte file exists.
		 * "hide_in_editor"=>"yes" will hide from editor's combo list.
		 */
		switch ( $shortcode ) {
			case "whmpress_pricing_table":
				return [
					"vc_options"    => [ "title" => "Pricing Table" ],
					"html_template",
					"image",
					"id"            => [
						"vc_type" => "productids",
						"heading" => "Select Product/Service Package",
					],
					"billingcycle"  => [
						"vc_type" => "dropdown",
						"value"   => "billing_cycle",
						"heading" => "Billing cycle",
					],
					"show_price"    => [
						"vc_type"     => "yesno",
						"description" => "Weather to show service/package price or not.",
					],
					"show_combo"    => [
						"heading"     => "Show order combo",
						"vc_type"     => "noyes",
						"description" => "Weather to show billingcycle combo to select duration.",
					],
					"show_button"   => [
						"heading"     => "Show order button",
						"vc_type"     => "yesno",
						"description" => "Show Order Button > Weather to show order button or not.",
					],
					"currency"      => [
						"vc_type"        => "currencies",
						"heading"        => "Select currency",
						"hide_in_editor" => "yes",
					],
					"button_text"   => [ "value" => "" ],
					"show_discount" => [
						"vc_type"     => "yesno",
						"description" => "Weather to show auto calculate discount or not. Default is <b>yes</b>",
					],
					"discount_type" => [
						"vc_type"     => "dropdown",
						"value"       => [
							"Default" => "",
							"Yearly"  => "yearly",
							"Monthly" => "monthly",
						],
						"heading"     => "Discount type (Monthly or Yearly)",
						"description" => "monthly: Additionally shows calculated monthly price with multiyear prices.<br />Yearly: Additionally shows calculated discount in % with multiyear prices.",
					],
					"html_id"       => [ "hide_if_template_file" => "yes", "hide_in_editor" => "yes" ],
					"html_class"    => [
						"value"                 => "whmpress whmpress_pricing_table",
						"hide_if_template_file" => "yes",
					],
				];
				break;
			case "whmpress_price_table":
			case "whmpress_price_matrix":
				return [
					"vc_options"         => [ "title" => "Price Matrix" ],
					"html_template",
					"image",
					"name"               => [
						"heading"     => "Names of services to include in price matrix",
						"description" => "Enter coma separated names of services to include in price matrix, leaving it empty will show all services.",
					],
					"groups"             => [
						"heading"     => "",
						"description" => "Enter coma separated names of groups to include in price matrix, leaving it empty will show all services.",
					],
					"billingcycles"      => [
						"heading"     => "",
						"description" => "Comma separated billing cycles, use from these <b>monthly,quarterly,semiannually,annually,biennially,triennially</b>",
					],
					"hide_columns"       => [ "description" => "Hide columns - comma seperated (e.g. sr,id,name,group)" ],
					"decimals"           => [
						"vc_type"     => "dropdown",
						"value"       => [ "Default", "0", "1", "2", "3", "4" ],
						"description" => "",
					],
					"show_hidden"        => [
						"vc_type"     => "noyes",
						"heading"     => "Show Hidden Services",
						"description" => "If you want to force show services that are set as hidden in WHMCS, select <b>YES</b>",
					],
					"replace_zero"       => [
						"value"       => whmpress_get_option( "pm_replace_zero" ),
						"heading"     => "Replace Zero With",
						"description" => "You can replace <b>0</b> with <b>Free</b> or <b>-</b> or any thing.",
					],
					"replace_empty"      => [
						"value"       => whmpress_get_option( "pm_replace_empty" ),
						"heading"     => "Replace empty with",
						"description" => "If you have not set pricing for some billing cycle, you can set how to set it.",
					],
					"table_id"           => [
						"description"           => "HTML ID for table object",
						"hide_if_template_file" => "yes",
						"hide_in_editor"        => "yes",
					],
					"type"               => [
						"value"          => [ "Product" => "product" ],
						"vc_type"        => "dropdown",
						"description"    => "",
						"hide_in_editor" => "yes",
					],
					"hide_search"        => [
						"vc_type"     => "noyes",
						"description" => "Hide the search text box used to search in price matrix",
					],
					"search_label"       => [
						"value"       => whmpress_get_option( "pm_search_label" ),
						"description" => "Label for search box",
					],
					"search_placeholder" => [
						"value"       => whmpress_get_option( "pm_search_placeholder" ),
						"description" => "Text to show inside search option",
					],
					"titles"             => [
						"heading"     => "Change column headers with",
						"description" => "Comma seperated new title name (Equals number of columns)",
					],
					"currency"           => [
						"vc_type"        => "currencies",
						"heading"        => "Select currency",
						"hide_in_editor" => "yes",
						"description"    => "You can override default currency here, use this option if you want to show your prices in any other currency.",
					],
					"order_link"         => [
						"vc_type"     => "noyes",
						"heading"     => "Show order button",
						"description" => "Show order button in table",
					],
					"data_table"         => [
						"vc_type"     => "noyes",
						"heading"     => "Apply DataTables",
						"description" => "Apply DataTables on HTML table",
					],
					"html_id"            => [
						"hide_if_template_file" => "yes",
						"hide_in_editor"        => "yes",
						"description"           => "",
					],
					"html_class"         => [
						"value"                 => "whmpress whmpress_price_matrix",
						"hide_if_template_file" => "yes",
						"description"           => "",
					],
				];
				break;
			case "whmpress_currency_combo":
				return [
					"vc_options"  => [ "title" => "Currency Combo" ],
					"html_template",
					"image",
					"prefix"      => [ "vc_type" => "yesno", "heading" => "Show prefix" ],
					"combo_name",
					"combo_class" => [ "hide_in_editor" => "yes" ],
					"html_class"  => [
						"value"                 => "whmpress whmpress_currency_combo",
						"hide_if_template_file" => "yes",
					],
					"html_id"     => [ "hide_if_template_file" => "yes", "hide_in_editor" => "yes" ],
				];
				break;
			case "whmpress_currency":
				return [
					"vc_options" => [ "title" => "Currency" ],
					"html_template",
					"image",
					"show"       => [
						"vc_type"     => "dropdown",
						"value"       => [ "Default", "Prefix", "Suffix", "Code" ],
						"description" => "Select weather you want to show prefix, postfix or code.",
					],
					"html_class" => [ "value" => "whmpress_currency", "hide_if_template_file" => "yes" ],
					"html_id"    => [ "hide_if_template_file" => "yes", "hide_in_editor" => "yes" ],
				];
				break;
			case "whmpress_description":
				return [
					"vc_options" => [ "title" => "Description" ],
					"html_template",
					"image",
					"id"         => [ "vc_type" => "productids", "heading" => "Select Product/Service Package" ],
					"show_as"    => [
						"vc_type" => "dropdown",
						"value"   => [
							"Default"        => "",
							"Unordered List" => "ul",
							"Ordered List"   => "ol",
							"Simple"         => "s",
						],
					],
					"html_class" => [ "hide_if_template_file" => "yes" ],
					"html_id"    => [ "hide_if_template_file" => "yes", "hide_in_editor" => "yes" ],
				];
				break;
			case "whmpress_domain_search_ajax":
				return [
					"vc_options"              => [ "title" => "Domain Search Ajax" ],
					"html_template",
					"image",
					"action"                  => [
						"heading"     => "Search result Div/URL",
						"description" => "To show output in specific div, on the current page, Use #div-id<br>To show output on a different page e.g. Page-B, place same short-code on Page-B, and mentions B's URL in this field.",
					],
					"text_class"              => [ "hide_in_editor" => "yes", "heading" => "Text class" ],
					"button_class"            => [ "hide_in_editor" => "yes", "heading" => "Button class" ],
					"whois_link"              => [ "vc_type" => "yesno", "heading" => "Show whois link" ],
					"www_link"                => [ "vc_type" => "yesno", "heading" => "Show www link" ],
					"enable_transfer_link"    => [ "vc_type" => "yesno", "heading" => "Show transfer link" ],
					"disable_domain_spinning" => [ "vc_type" => "noyes" ],
					"order_landing_page"      => [
						"vc_type" => "dropdown",
						"value"   => [
							"Default"                                       => "",
							"Select No of years & Additional domains first" => "0",
							"Go direct to domain settings"                  => "1",
						],
					],
					"show_price"              => [ "vc_type" => "yesno", "heading" => "Show price" ],
					"show_years"              => [ "vc_type" => "yesno", "heading" => "Show years" ],
					"placeholder"             => [ "value" => "", "heading" => "Placeholder" ],
					"button_text"             => [ "value" => "", "heading" => "Button text" ],
					"search_extensions"       => [
						"vc_type" => "dropdown",
						"heading" => "Search in Extensions",
						"value"   => [
							"Default"              => "",
							"Only Listed in WHMCS" => "1",
							"All"                  => "0",
						],
					],
					"html_id"                 => [ "hide_if_template_file" => "yes", "hide_in_editor" => "yes" ],
					"html_class"              => [
						"value"                 => "whmpress whmpress_domain_search_ajax",
						"hide_if_template_file" => "yes",
					],
				];
				break;
			/*case "whmpress_domain_search_ajax_results":
                return array(
                    "vc_options" => array("title"=>"Domain Search Ajax Result"),
                    "html_template",
                    "image",
                    "searchonly" => array("value"=>"","heading"=>"Search TLDs"),
                    "html_class" => array("value"=>"whmpress whmpress_domain_search_ajax_results","hide_if_template_file"=>"yes"),
                    "whois_link" => array("vc_type"=>"yesno","heading"=>"Show whois link"),
                    "www_link" => array("vc_type"=>"yesno","heading"=>"Show www link"),
                    "disable_domain_spinning" => array("vc_type"=>"noyes"),
                    "order_landing_page" => array("vc_type"=>"dropdown", "value"=>array("Default"=>"","Select No of years & Additional domains first"=>"0", "Go direct to domain settings"=>"1")),
                    "show_price" => array("vc_type"=>"yesno", "heading"=>"Show price"),
                    "show_years" => array("vc_type"=>"yesno", "heading"=>"Show years"),
                );
                break;*/
			case "whmpress_domain_search_bulk":
				return [
					"vc_options"   => [ "title" => "Domain Search Bulk" ],
					"html_template",
					"image",
					"button_text"  => [ "value" => "" ],
					"text_class"   => [ "hide_in_editor" => "yes", "heading" => "Text class" ],
					"button_class" => [ "hide_in_editor" => "yes", "heading" => "Button class" ],
					"placeholder"  => [ "value" => "", "heading" => "Placeholder" ],
					"html_id"      => [
						"hide_if_template_file" => "yes",
						"hide_in_editor"        => "yes",
						"heading"               => "HTML id",
					],
					"html_class"   => [
						"value"                 => "whmpress whmpress_domain_search_bulk",
						"hide_if_template_file" => "yes",
					],
				];
				break;
			/*case "whmpress_domain_search_extended_ajax":
                return array(
                    "vc_options" => array("title"=>"Domain Search Extended Ajax"),
                    "html_template",
                    "image",
                    "placeholder" => array("value"=>"","heading"=>"Placeholder"),
                    "button_text" => array("value"=>"","heading"=>"Button text"),
                    "text_class" => array("hide_in_editor"=>"yes","heading"=>"Text class"),
                    "button_class" => array("hide_in_editor"=>"yes","heading"=>"Button class"),
                    "action" => array("heading"=>"Search result URL"),
                    "html_class" => array("value"=>"whmpress whmpress_domain_search_ajax","hide_if_template_file"=>"yes","heading"=>"HTML class"),
                    "html_id" => array("hide_if_template_file"=>"yes","hide_in_editor"=>"yes","heading"=>"HTML id"),
                    "whois_link" => array("vc_type"=>"yesno","heading"=>"Show whois link"),
                    "www_link" => array("vc_type"=>"yesno","heading"=>"Show www link"),
                );
                break;
            case "whmpress_domain_search_extended_ajax_results":
                return array(
                    "vc_options" => array("title"=>"Domain Search Extended Ajax Result"),
                    "html_template",
                    "image",
                    "searchonly" => array("value"=>"","heading"=>"Search TLDs"),
                    "html_class" => array("value"=>"whmpress whmpress_domain_search_extended_results","hide_if_template_file"=>"yes"),
                    "whois_link" => array("vc_type"=>"yesno","heading"=>"Show whois link"),
                    "www_link" => array("vc_type"=>"yesno","heading"=>"Show www link"),
                );
                break;*/
			case "whmpress_domain_search":
				return [
					"vc_options"         => [ "title" => "Domain Search" ],
					"html_template",
					"image",
					"show_combo"         => [ "vc_type" => "noyes", "heading" => "Show combo" ],
					"show_tlds"          => [
						"value"       => "",
						"heading"     => "TLDs to show (comma separated)",
						"description" => "Weather to show available TLDs in combo",
					],
					"show_tlds_wildcard" => [
						"heading"     => "TLDs to show (wildcard)",
						"description" => "Provide tld search as wildcard, e.g. pk for all .pk domains or co for all com and .co domains",
					],
					"placeholder"        => [
						"heading"     => "Placeholder for domain search",
						"description" => "Enter text to show as place holder in domain search box.",
					],
					"text_class"         => [ "hide_in_editor" => "yes", "heading" => "Text class" ],
					"combo_class"        => [ "hide_in_editor" => "yes", "heading" => "Combo class" ],
					"button_class"       => [ "hide_in_editor" => "yes", "heading" => "Button class" ],
					"action"             => [
						"hide_in_vc"     => "yes",
						"hide_in_editor" => "yes",
						"heading"        => "Search result URL",
					],
					"button_text"        => [ "value" => "", "heading" => "Button text" ],
					"html_class"         => [
						"value"                 => "whmpress whmpress_domain_search",
						"hide_if_template_file" => "yes",
						"heading"               => "HTML class",
					],
					"html_id"            => [
						"hide_if_template_file" => "yes",
						"hide_in_editor"        => "yes",
						"heading"               => "HTML id",
					],
				];
				break;
			case "whmpress_login_form":
				return [
					"vc_options"   => [ "title" => "Login Form" ],
					"html_template",
					"image",
					"button_text"  => [ "value" => "", "heading" => "Button text" ],
					"button_class" => [ "hide_in_editor" => "yes", "heading" => "Button class" ],
					"html_class"   => [
						"value"                 => "whmpress whmpress_login_form",
						"hide_if_template_file" => "yes",
						"heading"               => "HTML class",
					],
					"html_id"      => [
						"hide_if_template_file" => "yes",
						"hide_in_editor"        => "yes",
						"heading"               => "HTML id",
					],
				];
				break;
			case "whmpress_name":
				return [
					"vc_options" => [ "title" => "Name" ],
					"html_template",
					"image",
					"id"         => [ "vc_type" => "productids", "heading" => "Select Product/Service Package" ],
					"html_class" => [ "hide_if_template_file" => "yes", "heading" => "HTML class" ],
					"html_id"    => [
						"hide_if_template_file" => "yes",
						"hide_in_editor"        => "yes",
						"heading"               => "HTML id",
					],
				];
				break;
			case "whmpress_order_button":
				return [
					"vc_options"   => [ "title" => "Order Button" ],
					"html_template",
					"image",
					"id"           => [ "vc_type" => "productids", "heading" => "Select Product/Service Package" ],
					"billingcycle" => [
						"vc_type"     => "dropdown",
						"value"       => "billing_cycle",
						"heading"     => "Billing cycle",
						"description" => "Order will be placed for selected billing cycle.",
					],
					"button_text"  => [ "value" => whmpress_get_option( "ob_button_text" ) ],
					"currency"     => [
						"vc_type"        => "currencies",
						"heading"        => "Currency Override",
						"hide_in_editor" => "yes",
						"description"    => "Used with multi currency, If you want to generate order button with a currency other than default.",
					],
					"params"       => [ "value" => "", "heading" => "Additional parameters for order URL" ],
					"html_class"   => [ "value" => "whmpress_order_button", "hide_if_template_file" => "yes" ],
					"html_id"      => [ "hide_if_template_file" => "yes", "hide_in_editor" => "yes" ],
				];
				break;
			case "whmpress_order_combo":
				return [
					"vc_options"    => [ "title" => "Order Combo" ],
					"html_template",
					"image",
					"id"            => [ "vc_type" => "productids", "heading" => "Select Product/Service Package" ],
					"decimals"      => [ "vc_type" => "dropdown", "value" => [ "Default", "0", "1", "2", "3", "4" ] ],
					"show_button"   => [
						"vc_type"     => "yesno",
						"description" => "Weather to show order button or not.",
					],
					"button_text"   => [
						"description" => "Text to show on button",
						"value"       => whmpress_get_option( "combo_button_text" ),
					],
					//"rows" => array("hide_if_template_file"=>"yes","vc_type"=>"dropdown","value"=>array("Default","1","2")),
					"show_discount" => [
						"vc_type"     => "yesno",
						"description" => "Weather to show auto calculate discount or not. Default is <b>yes</b>",
					],
					"discount_type" => [
						"vc_type"     => "dropdown",
						"value"       => [
							"Default"                  => "",
							"%age"                     => "yearly",
							"Calculated Monthly Price" => "monthly",
						],
						"heading"     => "Discount type (in %age or Calculated Monthly Price)",
						"description" => "monthly: Show discount as Calculated monthly Price.<br />Yearly: Show discount in %age.",
					],
					"combo_class"   => [ "hide_in_editor" => "yes" ],
					"button_class"  => [ "hide_in_editor" => "yes" ],
					"billingcycles" => [ "description" => "Billing cycle to include in combo, comma separated with one of these, one-time, monthly, quarterly, semi-annually, annually, biennially, triennially. If skipped all will be included." ],
					"prefix"        => [
						"vc_type"     => "yesno",
						"heading"     => "Show currency prefix",
						"description" => "Weather to show currency prefix or not",
					],
					"suffix"        => [
						"vc_type"     => "yesno",
						"heading"     => "Show currency suffix",
						"description" => "Weather to show currency suffix or not",
					],
					"currency"      => [
						"vc_type"        => "currencies",
						"heading"        => "Select currency",
						"hide_in_editor" => "yes",
					],
					"params"        => [ "value" => "", "heading" => "Additional parameters for order URL" ],
					"html_class"    => [ "value" => "whmpress whmpress_order_combo", "hide_if_template_file" => "yes" ],
					"html_id"       => [ "hide_if_template_file" => "yes", "hide_in_editor" => "yes" ],
				];
				break;
			case "whmpress_order_link":
				return [
					"vc_options"   => [ "title" => "Order Link" ],
					"html_template",
					"image",
					"id"           => [ "vc_type" => "productids", "heading" => "Select Product/Service Package" ],
					"billingcycle" => [
						"vc_type" => "dropdown",
						"value"   => "billing_cycle",
						"heading" => "Billing cycle",
					],
					"link_text"    => [ "value" => whmpress_get_option( "ol_link_text" ) ],
					"currency"     => [
						"vc_type"        => "currencies",
						"heading"        => "Select currency",
						"hide_in_editor" => "yes",
					],
					"html_class"   => [ "value" => "whmpress_order_link", "hide_if_template_file" => "yes" ],
					"html_id"      => [ "hide_if_template_file" => "yes", "hide_in_editor" => "yes" ],
				];
				break;
			case "whmpress_order_url":
				return [
					"vc_options"   => [ "title" => "Order URL" ],
					"html_template",
					"id"           => [ "vc_type" => "productids", "heading" => "Select Product/Service Package" ],
					"billingcycle" => [
						"vc_type" => "dropdown",
						"value"   => "billing_cycle",
						"heading" => "Billing cycle",
					],
					"currency"     => [
						"vc_type"        => "currencies",
						"heading"        => "Select currency",
						"hide_in_editor" => "yes",
					],
				];
				break;
			case "whmpress_price_box":
				return [
					"vc_options"       => [ "title" => "Price Box" ],
					"html_template",
					"image",
					"id"               => [ "vc_type" => "productids", "heading" => "Select Product/Service Package" ],
					"billingcycle"     => [
						"vc_type" => "dropdown",
						"value"   => "billing_cycle",
						"heading" => "Billing cycle",
					],
					"show_price"       => [
						"heading"     => "Show price",
						"vc_type"     => "yesno",
						"description" => "Weather to show service/package price or not.",
					],
					"show_combo"       => [
						"heading"     => "Show order combo",
						"vc_type"     => "noyes",
						"description" => "Weather to show billingcycle combo to select duration.",
					],
					"show_button"      => [
						"heading"     => "Show order button",
						"vc_type"     => "yesno",
						"description" => "Show Order Button > Weather to show order button or not.",
					],
					"currency"         => [
						"vc_type"        => "currencies",
						"heading"        => "Select currency",
						"hide_in_editor" => "yes",
					],
					"html_class"       => [
						"value"                 => "whmpress whmpress_price_box",
						"description"           => "HTML class for container",
						"hide_if_template_file" => "yes",
					],
					"html_id"          => [ "hide_if_template_file" => "yes", "hide_in_editor" => "yes" ],
					"button_text"      => [ "value" => '' ],
					"show_discount"    => [
						"vc_type"     => "yesno",
						"description" => "Weather to show auto calculate discount or not. Default is <b>yes</b>",
					],
					"discount_type"    => [
						"vc_type"     => "dropdown",
						"value"       => [
							"Default" => "",
							"Yearly"  => "yearly",
							"Monthly" => "monthly",
						],
						"heading"     => "Discount type (Monthly or Yearly)",
						"description" => "monthly: Additionally shows calculated monthly price with multiyear prices.<br />Yearly: Additionally shows calculated discount in % with multiyear prices.",
					],
					//"button_html_template" => array("vc_type"=>"textfield"),
					"show_description" => [ "vc_type" => "yesno" ],
				];
				break;
			case "whmpress_price":
				return [
					"vc_options"            => [ "title" => "Price" ],
					"html_template",
					"image",
					"id"                    => [
						"vc_type" => "productids",
						"heading" => "Select Product/Service Package",
					],
					"billingcycle"          => [
						"vc_type"     => "dropdown",
						"value"       => "billing_cycle",
						"heading"     => "Billing cycle",
						"description" => "Select a billing cycle to show price for",
					],
					"price_type"            => [
						"vc_type" => "dropdown",
						"heading" => "Show",
						"value"   => [
							"Default"           => "",
							"Price"             => "price",
							"Setup Fee"         => "setup",
							"Price + Setup Fee" => "total",
						],
					],
					"hide_decimal"          => [
						"vc_type"     => "noyes",
						"description" => "Show price decimal symbol or not",
						"heading"     => "Hide decimal symbol",
					],
					"decimals"              => [
						"vc_type"     => "dropdown",
						"value"       => [ "Default", "1", "2", "3", "4" ],
						"description" => "How many decimals to show with price",
					],
					"decimals_tag"          => [
						"vc_type"     => "dropdown",
						"value"       => [
							"Default"     => "",
							"==No Tag=="  => "-",
							"Bold"        => "b",
							"Italic"      => "i",
							"Underline"   => "u",
							"Superscript" => "sup",
							"Subscript"   => "sub",
						],
						"description" => "Select how you want currency symbol to show",
						"heading"     => "Show decimals value as",
					],
					"prefix"                => [
						"vc_type"     => "dropdown",
						"value"       => [
							"Default"            => "",
							"Do not show prefix" => "No",
							"==No Tag=="         => "-",
							"Bold"               => "b",
							"Italic"             => "i",
							"Underline"          => "u",
							"Superscript"        => "sup",
							"Subscript"          => "sub",
						],
						"heading"     => "Show Currency Prefix",
						"description" => "Select how you want currency symbol to show",
					],
					"suffix"                => [
						"vc_type"     => "dropdown",
						"heading"     => "Show Currency Suffix",
						"value"       => [
							"Default"            => "",
							"Do not show suffix" => "No",
							"==No Tag=="         => "-",
							"Bold"               => "b",
							"Italic"             => "i",
							"Underline"          => "u",
							"Superscript"        => "sup",
							"Subscript"          => "sub",
						],
						"description" => "Select how you want currency symbol to show",
					],
					"show_duration"         => [
						"vc_type"     => "dropdown",
						"value"       => [
							"Default"              => "",
							"Do not show duration" => "No",
							"==No Tag=="           => "-",
							"Bold"                 => "b",
							"Italic"               => "i",
							"Underline"            => "u",
							"Superscript"          => "sup",
							"Subscript"            => "sub",
						],
						"description" => "Select how you want to show duration (billing cycle) with price",
						"heading"     => "Show Duration/Billing Cycle",
					],
					"show_duration_as"      => [
						"vc_type"     => "dropdown",
						"value"       => [
							"Default"     => "",
							"Long (Year)" => "long",
							"Short (Yr)"  => "short",
						],
						"description" => "Weather to show duration as full or in short",
					],
					"convert_monthly"       => [
						"vc_type"     => "noyes",
						"heading"     => "Convert price into monthly price",
						"description" => "convert price into monthly price > example: If you have selected yearly price and select this option as yes, it will return <b>yearly price/12</b>",
					],
					"currency"              => [
						"vc_type" => "currencies",
						"heading" => "Currency",
					],      // "hide_in_editor"=>"yes"
					"config_option_string"  => [
						"value"       => "",
						"description" => "Prefix text to add if price is from configurable options",
						"heading"     => "String for config price",
					],
					"configureable_options" => [
						"heading"     => "Calculate configurable options",
						"vc_type"     => "noyes",
						"description" => "Calculate configureable options and add in price",
					],
					"price_tax"             => [
						"heading" => "Price/Tax",
						"vc_type" => "dropdown",
						"value"   => [
							"Default"       => "",
							"WHMCS Default" => "default",
							"Inclusive Tax" => "inclusive",
							"Exclusive Tax" => "exclusive",
							"Tax Only"      => "tax",
						],
					],
					"no_wrapper"            => [
						"vc_type" => "noyes",
						"heading" => "No wrapper",
					],
					"html_class"            => [
						"value"                 => "whmpress whmpress_price",
						"description"           => "HTML class for container",
						"hide_if_template_file" => "yes",
					],
					"html_id"               => [
						"hide_if_template_file" => "yes",
						"hide_in_editor"        => "yes",
					],
				];
				break;
			case "whmpress_domain_price":
				return [
					"vc_options"    => [ "title" => "Domain Price" ],
					"html_template",
					"type"          => [
						"vc_type" => "dropdown",
						"value"   => [
							"Default"             => "",
							"Domain Registration" => "domainregister",
							"Domain Renew"        => "domainrenew",
							"Domain Transfer"     => "domaintransfer",
						],
					],
					"years"         => [
						"vc_type" => "dropdown",
						"value"   => [
							"Default",
							"1",
							"2",
							"3",
							"4",
							"5",
							"6",
							"7",
							"8",
							"9",
							"10",
						],
					],
					"tld"           => [
						"value"   => ".com",
						"heading" => "Domain TLD",
					],
					"currency"      => [
						"vc_type"     => "currencies",
						"heading"     => "Currency Override",
						"description" => "Used with multi currency, If you want to generate order button with a currency other than default.",
					],
					"decimals"      => [
						"vc_type" => "dropdown",
						"value"   => [ "Default", "1", "2", "3", "4" ],
					],
					"hide_decimal"  => [
						"vc_type"     => "noyes",
						"description" => "Show price decimal symbol or not",
						"heading"     => "Hide decimal symbol",
					],
					"decimals_tag"  => [
						"vc_type"     => "dropdown",
						"value"       => [
							"Default"     => "",
							"Italic"      => "i",
							"Underline"   => "u",
							"Superscript" => "sup",
							"Subscript"   => "sub",
						],
						"description" => "Select how you want currency symbol to show",
						"heading"     => "Show decimals as",
					],
					"prefix"        => [
						"vc_type"     => "dropdown",
						"heading"     => "Show Currency Prefix",
						"value"       => [
							"Default"            => "",
							"Yes"                => "Yes",
							"Do not show prefix" => "No",
							"Bold"               => "b",
							"Italic"             => "i",
							"Underline"          => "u",
							"Superscript"        => "sup",
							"Subscript"          => "sub",
						],
						"description" => "Select how you want currency symbol to show",
					],
					"suffix"        => [
						"vc_type"     => "dropdown",
						"heading"     => "Show Currency Suffix",
						"value"       => [
							"Default"            => "",
							"Do not show suffix" => "No",
							"Yes"                => "Yes",
							"Bold"               => "b",
							"Italic"             => "i",
							"Underline"          => "u",
							"Superscript"        => "sup",
							"Subscript"          => "sub",
						],
						"description" => "Select how you want currency symbol to show",
					],
					"show_duration" => [
						"vc_type"     => "dropdown",
						"value"       => [
							"Default"              => "",
							"Yes"                  => "Yes",
							"Do not show duration" => "No",
							"Bold"                 => "b",
							"Italic"               => "i",
							"Underline"            => "u",
							"Superscript"          => "sup",
							"Subscript"            => "sub",
						],
						"description" => "Select how you want to show duration (billing cycle) with price",
						"heading"     => "Show number of years",
					],
					"price_tax"     => [
						"heading" => "Price/Tax",
						"vc_type" => "dropdown",
						"value"   => [
							"Default"       => "",
							"WHMCS Default" => "default",
							"Inclusive Tax" => "inclusive",
							"Exclusive Tax" => "exclusive",
							"Tax Only"      => "tax",
						],
					],
					"html_class"    => [
						"value"                 => "whmpress whmpress_domain_price",
						"description"           => "HTML class for container",
						"hide_if_template_file" => "yes",
					],
					"html_id"       => [
						"hide_if_template_file" => "yes",
						"hide_in_editor"        => "yes",
					],
				];
				break;
			case "whmpress_price_table_domain":
			case "whmpress_price_matrix_domain":
				return [
					"vc_options"         => [ "title" => "Price Matrix Domain" ],
					"html_template",
					"image",
					"currency"           => [
						"vc_type"        => "currencies",
						"heading"        => "Select currency",
						"hide_in_editor" => "yes",
						"description"    => "",
					],
					"show_tlds"          => [
						"value"       => "",
						"description" => "comma separated values of tlds to to list in table. Only tlds that exists in WHMCS will be added. No spaces in comma separated values.",
					],
					"show_tlds_wildcard" => [
						"heading"     => "Show TLDs Wildcard",
						"description" => "Show only tlds matching with given string. Very useful if you want to show only tlds related to your country, e.g. <b>.in</b>",
					],
					"decimals"           => [
						"vc_type"     => "dropdown",
						"value"       => [ "Default", "0", "1", "2", "3", "4" ],
						"description" => "",
					],
					
					// Removed from 1.5.4
					//"cols" => array("heading"=>"Number of columns","vc_type"=>"dropdown","value"=>array("1","2","3","4","5","6"),"description"=>"","hide_if_template_file"=>"yes"),
					
					"show_renewel"       => [
						"vc_type"     => "yesno",
						"heading"     => "Show Renewal Price",
						"description" => "Weather to show domain renewal price",
					],
					"show_transfer"      => [
						"vc_type"     => "yesno",
						"heading"     => "Show Transfer Price",
						"description" => "Weather to show domain transfer price",
					],
					"hide_search"        => [ "vc_type" => "noyes", "description" => "" ],
					"search_label"       => [
						"value"       => whmpress_get_option( "pmd_search_label" ),
						"description" => "",
					],
					"search_placeholder" => [
						"value"       => whmpress_get_option( "pmd_search_placeholder" ),
						"description" => "",
						"heading"     => "Search placeholder",
					],
					"show_disabled"      => [
						"vc_type"     => "yesno",
						"heading"     => "Show Disabled Domains",
						"description" => "If you want to force show domains that are set as hidden in WHMCS, select <b>YES</b>",
					],
					"table_id"           => [ "hide_if_template_file" => "yes", "hide_in_editor" => "yes" ],
					"titles"             => [
						"heading"     => "Change column headers with",
						"description" => "Change table column headers with",
					],
					"pricing_slab"       => [ "vc_type" => "pricing_slabs", "heading" => "Select pricing slab" ],
					"data_table"         => [
						"vc_type"     => "noyes",
						"heading"     => "Apply DataTables",
						"description" => "Apply DataTables on HTML table",
					],
					"num_of_rows"        => [
						"heading" => "Number of rows",
						"vc_type" => "dropdown",
						"value"   => [ "Default", "10", "25", "50", "100" ],
					],
					"replace_empty"      => [ "heading" => "Replace empty value with", "value" => "-" ],
					"html_id"            => [
						"hide_if_template_file" => "yes",
						"hide_in_editor"        => "yes",
						"description"           => "",
					],
					"html_class"         => [
						"hide_if_template_file" => "yes",
						"value"                 => "whmpress whmpress_price_matrix",
						"description"           => "",
						"heading"               => "HTML class",
					],
				];
				break;
			case "whmpress_price_table_group":
				return [
					"vc_options" => [ "title" => "Price Table Group" ],
					"html_template",
					"image",
					"id"         => [ "vc_type" => "productids", "heading" => "Select Product/Service Package" ],
				];
				break;
			case "whmpress_whois":
			case "whmpress_domain_whois":
				return [
					"vc_options"        => [ "title" => "Domain Whois" ],
					"html_template",
					"image",
					"button_text"       => [ "value" => "" ],
					"result_text_class" => [ "heading" => "Whois result class" ],
					"text_class"        => [ "hide_in_editor" => "yes" ],
					"button_class"      => [ "hide_in_editor" => "yes" ],
					"placeholder"       => [ "value" => "" ],
					"html_id"           => [ "hide_if_template_file" => "yes", "hide_in_editor" => "yes" ],
					"html_class"        => [
						"value"                 => "whmpress whmpress_domain_whois",
						"hide_if_template_file" => "yes",
					],
				];
				break;
			case "whmpress_url":
				return [
					"vc_options" => [ "title" => "WHMpress URL" ],
					"type"       => [
						"heading" => "URL type",
						"vc_type" => "dropdown",
						"value"   => [
							"client_area",
							"announcements",
							"submit_ticket",
							"downloads",
							"support_tickets",
							"knowledgebase",
							"affiliates",
							"order",
							"contact_url",
							"server_status",
							"network_issues",
							"whmcs_login",
							"whmcs_register",
							"whmcs_forget_password",
						],
					],
				];
			case "whmpress_client_area":
				return [
					"vc_options"     => [ "title" => "WHMpress Client Area" ],
					"whmcs_template" => [
						"heading"     => "WHMCS template",
						"value"       => "",
						"description" => "Leave it blank, if you are not sure",
					],
					"carttpl"        => [
						"heading"     => "WHMCS Cart template",
						"value"       => "",
						"description" => "Leave it blank, if you are not sure",
					],
				];
				break;
			case "whmpress_whmcs_page":
				return [
					"vc_options" => [ "title" => "WHMpress WHMCS Page" ],
					"page"       => [
						"heading" => "WHMCS page",
						"vc_type" => "dropdown",
						"value"   => [
							"Home"             => "index",
							"View Cart"        => "cart",
							"Announcements"    => "announcements",
							"Knowledge Base"   => "knowledgebase",
							"Server Status"    => "serverstatus",
							"Contact Page"     => "contact",
							"Submit Ticket"    => "submitticket",
							"Client Area"      => "clientarea",
							"Register Account" => "register",
							"Forget Password"  => "pwreset",
						],
					],
					"return"     => [
						"heading" => "Output return type",
						"vc_type" => "dropdown",
						"value"   => [ "URL" => "url", "Link" => "link" ],
					],
				];
				break;
			case "whmpress_whmcs_cart":
				return [
					"vc_options" => [ "title" => "WHMCS Cart Items" ],
					"link_text"  => [ "heading" => "Link Text", "value" => "" ],
				];
				break;
			case "whmpress_whmcs_if_loggedin":
				return [
					"vc_options" => [ "title" => "WHMCS Logged In" ],
				];
				break;
			case "whmpress_whmcs_if_not_loggedin":
				return [
					"vc_options" => [ "title" => "WHMCS Not Logged In" ],
				];
				break;
			case "whmpress_announcements":
				return [
					"vc_options" => [ "title" => "Announcements" ],
					"count"      => [ "heading" => "How many announcements to show?", "value" => "3" ],
					"word"       => [ "heading" => "Number of words to show", "value" => "25" ],
				];
				break;
			default:
				return [];
		}
	}
	
	public function get_product_types( $vc_compatible = false ) {
		$WHMPress = new WHMPress();
		if ( ! $WHMPress->WHMpress_synced() ) {
			return [];
		}
		$Q = "SELECT DISTINCT `type` FROM `" . whmp_get_products_table_name() . "` WHERE `type`<>''";
		global $wpdb;
		$rows = $wpdb->get_results( $Q, ARRAY_A );
		if ( $vc_compatible ) {
			$Out = [];
			foreach ( $rows as $row ) {
				$Out[ $row["type"] ] = $row["type"];
			}
			
			return $Out;
		} else {
			return $rows;
		}
	}
	
	public function get_currencies( $vc_compatible = false ) {
		$WHMPress = new WHMpress();
		if ( ! $WHMPress->WHMpress_synced() ) {
			return [];
		}
		$Q = "SELECT * FROM `" . whmp_get_currencies_table_name() . "`";
		global $wpdb;
		$rows = $wpdb->get_results( $Q, ARRAY_A );
		if ( $vc_compatible ) {
			$Out["Default"] = "0";
			foreach ( $rows as $row ) {
				$Out[ $row['prefix'] . " " . $row['suffix'] ] = $row['id'];
			}
			
			return $Out;
		} else {
			if ( is_object( $rows ) ) {
				die( "Here!" );
				$rows = (array) $rows;
			}
			
			return $rows;
		}
	}
	
	public function get_template_array( $shortcode ) {
		if ( get_option( "load_sytle_orders" ) == "whmpress" ) {
			$file_path = WHMP_PLUGIN_DIR . "/themes/" . basename( $this->whmp_get_template_directory() ) . "/" . $shortcode . "/custom_fields.csv";
		} elseif ( get_option( "load_sytle_orders" ) == "author" ) {
			$file_path = $this->whmp_get_template_directory() . "/whmpress/" . $shortcode . "/custom_fields.csv";
		} else {
			$file_path = WHMP_PLUGIN_DIR . "/templates/" . $shortcode . "/custom_fields.csv";
		}
		
		/*
        $Dir1 = $this->whmp_get_template_directory()."/whmpress/".$shortcode;
        if (!is_dir($Dir1)) {
            $Dir2 = WHMP_PLUGIN_DIR."/templates/".$shortcode;
            if (!is_dir($Dir2)) return array();
            $file_path = $Dir2 . "/custom_fields.csv";
        } else {
            $file_path = $Dir1 . "/custom_fields.csv";
        }
        */
		if ( ! is_file( $file_path ) ) {
			return [];
		}
		$CustomFields = array_map( 'str_getcsv', file( $file_path ) );
		
		$field_names = [];
		foreach ( $CustomFields as $custom_field ) {
			$field_names[] = @$custom_field[0];
		}
		
		return $field_names;
	}
	
	public function is_json( $json_value ) {
		json_decode( $json_value );
		
		return ( json_last_error() == JSON_ERROR_NONE );
	}
	
	public function send_info_to_author() {
		if ( ! $this->verified_purchase() ) {
			echo "Your product purchase is not verified.\n\nPurchase your product from Dashboard of WHMpress";
			
			return;
		}
		global $wpdb;
		$wp_upload_max     = wp_max_upload_size();
		$server_upload_max = intval( str_replace( 'M', '', ini_get( 'upload_max_filesize' ) ) ) * 1024 * 1024;
		
		$String = "<table border='1' cellpadding='5' cellspacing='0'>
        <tr><th colspan='2'>WordPressInfo</th></tr>
        <tr><td>Site URL</td><td>" . site_url() . "</td></tr>
        <tr><td>Site Home</td><td>" . home_url() . "</td></tr>
        <tr><td>WP Version</td><td>" . get_bloginfo( 'version' ) . "</td></tr>
        <tr><td>Is Multi Site</td><td>" . ( is_multisite() ? "Yes" : "No" ) . "</td></tr>
        <tr><td>WordPress Language</td><td>" . get_locale() . "</td></tr>
        <tr><td>WordPress Debug Mode</td><td>" . ( defined( 'WP_DEBUG' ) && WP_DEBUG ? "Yes" : "No" ) . "</td></tr>
        <tr><td>WordPress Active Plugins</td><td>" . ( count( (array) get_option( 'active_plugins' ) ) ) . "</td></tr>
        <tr><td>WordPress Max Upload Size</td><td>" . ( $wp_upload_max <= $server_upload_max ? size_format( $wp_upload_max ) : size_format( $wp_upload_max ) . " but server allows " . size_format( $server_upload_max ) ) . "</td></tr>
        <tr><td>WordPress Memory Limit</td><td>" . ( WP_MEMORY_LIMIT ) . "</td></tr>
        
        <tr><th colspan='2'>Server Info</th></tr>
        <tr><td>PHP Version</td><td>" . ( function_exists( 'phpversion' ) ? phpversion() : "-" ) . "</td></tr>
        <tr><td>Server Software</td><td>" . ( esc_html( @$_SERVER['SERVER_SOFTWARE'] ) ) . "</td></tr>
        <tr><td>MySQLi Extension</td><td>" . ( function_exists( 'mysqli_connect' ) ? "Yes" : "No" ) . "</td></tr>
        <tr><td>cURL Extension</td><td>" . ( function_exists( 'curl_version' ) ? "Yes" : "No" ) . "</td></tr>

        <tr><th colspan='2'>WHMpress Info</th></tr>
        <tr><td>Version</td><td>" . ( WHMP_VERSION ) . "</td></tr>
        <tr><td>Last Synced</td><td>" . ( get_option( "sync_time" ) ) . "</td></tr>
        <tr><td>WHMCS Version</td><td>" . $wpdb->get_var( "SELECT `value` FROM `" . whmp_get_configuration_table_name() . "` WHERE `setting`='Version'" ) . "</td></tr>
        <tr><td>Company Name</td><td>" . $wpdb->get_var( "SELECT `value` FROM `" . whmp_get_configuration_table_name() . "` WHERE `setting`='CompanyName'" ) . "</td></tr>
        <tr><td>Email Address</td><td>" . $wpdb->get_var( "SELECT `value` FROM `" . whmp_get_configuration_table_name() . "` WHERE `setting`='email'" ) . "</td></tr>
        <tr><td>Domains</td><td>" . $wpdb->get_var( "SELECT COUNT(*) FROM `" . whmp_get_domain_pricing_table_name() . "`" ) . "</td></tr>
        <tr><td>Products</td><td>" . $wpdb->get_var( "SELECT COUNT(*) FROM `" . whmp_get_products_table_name() . "`" ) . "</td></tr>
        <tr><td>Product Groups</td><td>" . $wpdb->get_var( "SELECT COUNT(*) FROM `" . whmp_get_product_group_table_name() . "`" ) . "</td></tr>
        <tr><td>Currencies</td><td>" . $wpdb->get_var( "SELECT COUNT(*) FROM `" . whmp_get_currencies_table_name() . "`" ) . "</td></tr>
        <tr><td>WHMCS URL</td><td>" . whmp_get_installation_url() . "</td></tr>";
		
		if ( is_plugin_active( 'WHMpress_Client_Area/client-area.php' ) ) {
			$uurl = get_option( 'client_area_page_url' );
			if ( is_numeric( $uurl ) ) {
				$uurl = get_page_link( $uurl );
			}
			if ( substr( $uurl, 0, 4 ) <> "http" ) {
				$uurl = get_bloginfo( "url" ) . "/" . $uurl;
			}
			$String .= "<tr><td>Client Area URL</td><td>" . $uurl . "</td></tr>";
		}
		
		$String .= "<tr><th colspan='2'>Addons</th></tr>";
		if ( is_plugin_active( 'WHMpress_Client_Area/client-area.php' ) ) {
			global $plugin_data_ca;
			$String .= "<tr><td>" . @$plugin_data_ca["Name"] . "</td><td>" . @$plugin_data_ca["Version"] . "</td></tr>";
		} else {
			$String .= "<tr><td></td><td>No Addon installed</td></tr>";
		}
		
		$String .= "<tr><th colspan='2'>Plugins</th></tr>
        <tr><td>Installed</td>";
		$active_plugins = (array) get_option( 'active_plugins', [] );
		
		if ( is_multisite() ) {
			$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', [] ) );
		}
		
		$wp_plugins = [];
		
		foreach ( $active_plugins as $plugin ) {
			
			$plugin_data    = @get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin );
			$dirname        = dirname( $plugin );
			$version_string = '';
			
			if ( ! empty( $plugin_data['Name'] ) ) {
				
				// link the plugin name to the plugin url if available
				$plugin_name = $plugin_data['Name'];
				if ( ! empty( $plugin_data['PluginURI'] ) ) {
					$plugin_name = '<a target="_blank" href="' . esc_url( $plugin_data['PluginURI'] ) . '" title="Visit plugin homepage">' . $plugin_name . '</a>';
				}
				
				$wp_plugins[] = $plugin_name . ' by ' . $plugin_data['Author'] . ' version ' . $plugin_data['Version'] . $version_string;
				
			}
		}
		if ( sizeof( $wp_plugins ) == 0 ) {
			$String .= "<td>-</td>";
		} else {
			$String .= "<td>" . implode( ', <br/>', $wp_plugins ) . "</td>";
		}
		
		$active_theme = wp_get_theme();
		$String .= "<tr><th colspan='2'>Theme</th></tr>
        <tr><td>Theme Name</td><td>" . $active_theme->Name . "</td></tr>
        <tr><td>Theme Version</td><td>" . $active_theme->Version . "</td></tr>
        <tr><td>Theme Author URL</td><td>" . $active_theme->{'Author URI'} . "</td></tr>
        <tr><td>Is Child Theme</td><td>" . ( is_child_theme() ? "Yes" : "No" ) . "</td></tr>";
		if ( is_child_theme() ) {
			$parent_theme = wp_get_theme( $active_theme->Template );
			$String .= "<tr><td>Parent Theme Name</td><td>" . $parent_theme->Name . "</td></tr>
            <tr><td>Parent Theme Version</td><td>" . $parent_theme->Version . "</td></tr>
            <tr><td>Parent Theme Author URL</td><td>" . $parent_theme->{'Author URI'} . "</td></tr>";
		}
		$String .= "</table><br /><br />
        From IP: " . $this->ip_address();
		
		$headers  = "Content-type: text/html";
		$response = wp_mail( "shakeel@shakeel.pk,farooqomer@gmail.com", "WHMPress Debug Info", $String, $headers );
		if ( $response === true ) {
			echo "OK";
		} else {
			"Email not sent.";
		}
	}
	
	public function verified_purchase() {
		return get_option( "whmp_verified" ) == "1";
	}
	
	public function ip_address() {
		$ip_keys = [
			'HTTP_CLIENT_IP',
			'HTTP_X_FORWARDED_FOR',
			'HTTP_X_FORWARDED',
			'HTTP_X_CLUSTER_CLIENT_IP',
			'HTTP_FORWARDED_FOR',
			'HTTP_FORWARDED',
			'REMOTE_ADDR',
		];
		foreach ( $ip_keys as $key ) {
			if ( array_key_exists( $key, $_SERVER ) === true ) {
				foreach ( explode( ',', $_SERVER[ $key ] ) as $ip ) {
					// trim for safety measures
					$ip = trim( $ip );
					// attempt to validate IP
					if ( $this->validate_ip( $ip ) ) {
						return $ip;
					}
				}
			}
		}
		
		return isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : false;
	}
	
	public function validate_ip( $ip ) {
		if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) === false ) {
			return false;
		}
		
		return true;
	}
	
	function debug( $string ) {
		if ( $_SERVER["HTTP_HOST"] == "whmpress.pk" ) {
			if ( is_object( $string ) || is_array( $string ) ) {
				$string = print_r( $string, true );
			}
			file_put_contents( "D:\\whmpress_logs.txt", $string . "\n", FILE_APPEND );
		}
	}
	
	public function unverify_purchase( $vars = [] ) {
		$url                    = "http://plugins.creativeon.com/envato/unverify.php";
		$vars["purchase_code"]  = get_option( "whmp_purchase_code" );
		$vars["email2"]         = get_option( "whmp_purchase_email" );
		$vars["registered_url"] = parse_url( get_bloginfo( "url" ), PHP_URL_HOST );
		$vars["registered_url"] = str_replace( "www.", "", $vars["registered_url"] );
		
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_HEADER, false );
		curl_setopt( $ch, CURLOPT_VERBOSE, false );
		#curl_setopt($ch, CURLOPT_COOKIE, $cookies);
		curl_setopt( $ch, CURLOPT_POST, count( $vars ) );
		#curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		#curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$data = $vars;
		if ( is_array( $vars ) ) {
			$vars = http_build_query( $vars );
		}
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $vars );
		curl_setopt( $ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13' );
		$output = curl_exec( $ch );
		
		if ( $output == "OK" ) {
			update_option( "whmp_purchase_code", '' );
			update_option( "whmp_purchase_email", '' );
			update_option( "whmp_verified", "0" );
		}
		
		echo $output;
	}
	
	public function verify_purchase( $vars = [] ) {
		$url = "http://plugins.creativeon.com/envato/";
		
		$vars["registered_url"] = parse_url( get_bloginfo( "url" ), PHP_URL_HOST );
		if ( $vars["registered_url"] == "" ) {
			$vars["registered_url"] = parse_url( get_bloginfo( "url" ), PHP_URL_PATH );
		}
		$vars["registered_url"] = str_replace( "www.", "", $vars["registered_url"] );
		
		$vars["item_name"] = "WHMpress - WHMCS WordPress Integration Plugin";
		$vars["version"]   = WHMP_VERSION;
		
		if ( ! isset( $vars["email"] ) ) {
			$vars["email"] = get_option( "whmp_purchase_email" );
		}
		if ( $vars["email"] == "" ) {
			$vars["email"] = get_option( "admin_email" );
		}
		
		if ( ! isset( $vars["purchase_code"] ) ) {
			$vars["purchase_code"] = get_option( "whmp_purchase_code" );
		}
		
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_HEADER, false );
		curl_setopt( $ch, CURLOPT_VERBOSE, false );
		curl_setopt( $ch, CURLOPT_POST, count( $vars ) );
		#curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		#curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$data = $vars;
		if ( is_array( $vars ) ) {
			$vars = http_build_query( $vars );
		}
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $vars );
		curl_setopt( $ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13' );
		$output = curl_exec( $ch );
		
		if ( $errno = curl_errno( $ch ) ) {
			$error_message = curl_error( $ch );
			echo "cURL error:\n {$error_message}<br />Fetching: $fetch_url";
			
			return;
		}
		
		if ( $output == "OK" ) {
			update_option( "whmp_purchase_code", $data["purchase_code"] );
			update_option( "whmp_purchase_email", $data["email"] );
			update_option( "whmp_verified", "1" );
		} else {
			update_option( "whmp_verified", "0" );
		}
		echo $output;
	}
	
	public function get_whmcs_url( $url_type = 'order' ) {
		$url      = "";
		$blog_url = $this->get_current_client_area_page();
		
		switch ( $url_type ) {
			case "order":
				if ( is_plugin_active( 'WHMpress_Client_Area/client-area.php' ) ) {
					$WHMPress_Client_Area = new WHMPress_Client_Area;
					if ( $WHMPress_Client_Area->is_permalink() ) {
						$url = $blog_url . "/cart";
					} else {
						$params = parse_url( $blog_url );
						if ( isset( $params["query"] ) ) {
							$url = $blog_url . "&whmpca=cart";
						} else {
							$url = $blog_url . "?whmpca=cart";
						}
					}
				} else {
					$url = whmpress_get_option( 'order_url' );
					if ( $url == "" ) {
						$url = rtrim( whmp_get_installation_url(), "/" ) . "/cart.php";
					}
				}
				#$url = esc_attr( whmpress_get_option('order_url') );
				#if ($url=="") $url = whmp_get_installation_url()."/cart.php";
				
				if ( substr( $url, - 2 ) == "//" ) {
					$url = substr( $url, 0, - 1 );
				}
				break;
			case "domainchecker":
				if ( is_plugin_active( 'WHMpress_Client_Area/client-area.php' ) ) {
					$WHMPress_Client_Area = new WHMPress_Client_Area;
					if ( $WHMPress_Client_Area->is_permalink() ) {
						$url = $blog_url . "/domainchecker";
					} else {
						$params = parse_url( $blog_url );
						if ( isset( $params["query"] ) ) {
							$url = $blog_url . "&whmpca=domainchecker";
						} else {
							$url = $blog_url . "?whmpca=domainchecker";
						}
					}
				} else {
					$url = whmpress_get_option( 'domain_checker_url' );
					if ( $url == "" ) {
						$url = rtrim( whmp_get_installation_url(), "/" ) . "/domainchecker.php";
					}
				}
				break;
			case "loginurl":
				if ( is_plugin_active( 'WHMpress_Client_Area/client-area.php' ) ) {
					$WHMPress_Client_Area = new WHMPress_Client_Area;
					if ( $WHMPress_Client_Area->is_permalink() ) {
						$url = $blog_url . "/dologin";
					} else {
						$params = parse_url( $blog_url );
						if ( isset( $params["query"] ) ) {
							$url = $blog_url . "&whmpca=dologin";
						} else {
							$url = $blog_url . "?whmpca=dologin";
						}
					}
				} else {
					$url = whmpress_get_option( 'whmcs_login_url' );
					if ( $url == "" ) {
						$url = rtrim( get_option( "whmcs_url" ), "/" ) . "/dologin.php";
					}
				}
				break;
		}
		if ( is_plugin_active( 'WHMpress_Client_Area/client-area.php' ) ) {
			$WHMPress_Client_Area = new WHMPress_Client_Area;
			if ( $WHMPress_Client_Area->is_permalink() ) {
				return $url;
			} else {
				$params = @parse_url( $url );
				if ( @$params["query"] <> "" ) {
					$url .= "&";
				} else {
					$url .= "?";
				}
				
				return $url;
			}
		} else {
			$params = @parse_url( $url );
			if ( @$params["query"] <> "" ) {
				$url .= "&";
			} else {
				$url .= "?";
			}
			
			return $url;
		}
	}
	
	function get_current_client_area_page() {
		if ( $this->is_client_area_activated() ) {
			$WHMCS    = new WHMPress_Client_Area();
			$blog_url = $WHMCS->get_client_area_page_id();
		} else {
			$blog_url = get_option( "client_area_page_url" );
		}
		
		if ( is_numeric( $blog_url ) ) {
			$blog_url = get_page_link( $blog_url );
		} else {
			if ( substr( $blog_url, 0, 4 ) != "http" ) {
				$blog_url = get_bloginfo( "url" ) . "/" . $blog_url;
			}
		}
		$blog_url = rtrim( $blog_url, "/" );
		
		return $blog_url;
	}
	
	public function is_client_area_activated() {
		return is_plugin_active( 'WHMpress_Client_Area/client-area.php' );
	}
	
	public function show_array( $ar ) {
		echo "<pre>";
		if ( is_object( $ar ) || is_array( $ar ) ) {
			print_r( $ar );
		} else {
			var_dump( $ar );
		}
		echo "</pre>";
	}
}