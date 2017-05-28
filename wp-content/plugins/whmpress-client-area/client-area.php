<?php
/*
Plugin Name: WHMCS Client Area - WHMPress
Depends: WHMpress
Plugin URI: http://www.whmpress.com
Description: WHMCS Client Area using WHMPress
Version: 4.1.2
Author: creativeON
Author URI: http://creativeon.com
*/

// Prevent direct file access
if (!function_exists('add_action')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    die("Access denied");
    exit();
}

if (!defined('WHMP_FILE_PATH')) define('WHMP_FILE_PATH', __FILE__);
if (!defined('WHMP_CA_PATH')) define('WHMP_CA_PATH', (dirname(__FILE__)));
if (!defined('WHMP_CA_URL')) define('WHMP_CA_URL', (plugin_dir_url(__FILE__)));
if (!defined('WHMP_LANG')) define('WHMP_LANG', 'whmp_lang_ca');
if (!defined("SERVE_FILES")) define("SERVE_FILES", plugin_dir_url(__FILE__) . "serve-files.php");
if (!defined("WCA_FOLDER")) define("WCA_FOLDER", basename(dirname(__FILE__)));

$WCA_Fields = array(
    'phonenumber' => array(
        "label" => __('Phone number', 'whmpress'),
    ),
    'address1' => array(
        "label" => __('Address 1', 'whmpress')
    ),
    'address2' => array(
        "label" => __('Address 2', 'whmpress')
    ),
    'city' => array(
        "label" => __('City', 'whmpress')
    ),
    'state' => array(
        "label" => __('State/Region', 'whmpress')
    ),
    'postcode' => array(
        "label" => __('Post code', 'whmpress')
    ),
    'country' => array(
        "label" => __('Country', 'whmpress')
    )
);

require_once(ABSPATH . WPINC . '/class-phpass.php');

$wp_hasher = new PasswordHash(10, false);

// Setting SEO URLs fields
$whmp_seo_urls = array(
    "announcements",
    "knowledgebase",
    "serverstatus",
    "contact",
    "domainchecker",
    "cart",
    "submitticket",
    "clientarea",
    "register",
    "pwreset",
);

$p = realpath(plugin_dir_path(__FILE__) . "cache/");
$files = glob($p . "/*");
$whmp_options[1] = array(
    "whmp_use_permalinks" => array("type" => "noyes", "label" => "Do you want to use pretty permalinks?", "helper" => "<b>Note:</b> To use this feature, You should have 'KB SEO Friendly URLs' unchecked in WHMCS > Setup > General Setup > Support."),
    "curl_timeout_whmp" => array("type" => "number", "label" => "cURL timeout (in seconds)"),
    "whmp_follow_lang" => array("type" => "yesno", "label" => "Follow language"),
    "whmp_wp_lang" => array("type" => "text", "label" => "WHMCS language to use<br /><small>(keep empty if you want WHMCS to follow WP language)</small>", "no_placeholder" => "1"),
    "cache_enabled_whmp" => array("type" => "noyes", "label" => "Enable Cache",
        "later_message" => "You've <span id=\"files\">" . count($files) . "</span> cached file(s) - (<a href=\"javascript:;\" onclick=\"RemoveCacheFiles()\">Remove Cache Files</a>)"),
    "jquery_source" =>
        array(
            "type" => "select",
            "label" => "jQuery Source",
            "data" => array(
                "wordpress" => "WordPress",
                "google2.1.3" => "Google 2.1.3",
                "google" => "Google 1.7.2",
                "google1.11.2" => "Google 1.11.2",
                "whmcs" => "Use WHMCS jQquery"
            )
        ),
    "use_whmcs_css_files" => array("type" => "yesno", "label" => "Use WHMCS css"),
    "load_dropdown" => array("type" => "noyes", "label" => "Patch Account Dropdown Problem<br /><small>(Only select if drop down does not works)</small>"),
    "exclude_js_files" => array("type" => "textarea", "label" => "Exclude .js & .css files from WHMCS<br /><small>Comma separated<br />e.g. bootstrap.min.js, jquery.js</small>"),
    "whmp_config_data" => array("type" => "textarea", "label" => "WHMCS template manipulation
    <br><small>Following commands can be used for .CSS-Class or #ID<br>
    NZ - removes the class from html element<br>
    EZ - removes the entire element along with content<br>
    NT - replace any string from WHMCS HTML output<br><br>
    examples:<br>
    #logo-id=EZ (Remove complete html element with id=#logo-id)<br>
    my-old-css-class=NT=my-new-css-class (change the name of class to new one)</small>"),

    #"image_display"=>array("type"=>"noyes","label"=>""),
    #"js_display"=>array("type"=>"noyes","label"=>""),
);

require_once(WHMP_CA_PATH . '/includes/password.php');
if (!function_exists('curl_get_file_contents')) {
    function curl_get_file_contents($URL)
    {
        $c = curl_init();
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_URL, $URL);
        $contents = curl_exec($c);
        curl_close($c);

        if ($contents) return $contents;
        else return FALSE;
    }
}

require_once(dirname(__FILE__) . '/includes/ca.init.php');

function Register_WHMCS_settings()
{
    register_setting('whmp_whmcs_settings', 'client_area_page_url');
    register_setting('whmp_whmcs_settings', 'remove_whmcs_logo');
    register_setting('whmp_whmcs_settings', 'remove_whmcs_menu');
    register_setting('whmp_whmcs_settings', 'remove_copyright');
    register_setting('whmp_whmcs_settings', 'remove_breadcrumb');
    register_setting('whmp_whmcs_settings', 'remove_powered_by');
    register_setting('whmp_whmcs_settings', 'whmp_hide_currency_select');
    register_setting('whmp_whmcs_settings', 'whmp_remove_top_bar');
    register_setting('whmp_whmcs_settings', 'whmp_show_admin_notice1');
    register_setting('whmp_whmcs_settings', 'whmpca_custom_css');
    register_setting('whmp_whmcs_settings', 'whmp_logout_url');

    register_setting('whmp_whmcs_settings', 'whmcs_main_url');
    register_setting('whmp_whmcs_settings', 'whmcs_sso_admin_user');
    register_setting('whmp_whmcs_settings', 'whmcs_sso_admin_pass');
    register_setting('whmp_whmcs_settings', 'sync_direction');
    register_setting('whmp_whmcs_settings', 'whmcs_both_ways_priority');
    register_setting('whmp_whmcs_settings', 'whmcs_wordpress_role');
    register_setting('whmp_whmcs_settings', 'whmcs_sso_login_name_type');
    register_setting('whmp_whmcs_settings', 'whmcs_sso_handle_fields');
    //register_setting('whmp_whmcs_settings', 'whmcs_sso_create_user');
    register_setting('whmp_whmcs_settings', 'whmcs_enable_sync');
    register_setting('whmp_whmcs_settings', 'whmcs_enable_sso');
    register_setting('whmp_whmcs_settings', 'whmcs_hide_wp_admin_bar');

    register_setting('whmp_whmcs_settings', 'whmcs_load_sytle_orders');
    register_setting('whmp_whmcs_settings', 'whmcs_create_wp_fields');


    # WHMCS Client Area
    global $whmp_options;
    foreach ($whmp_options[1] as $key => $ar) {
        register_setting('whmp_whmcs_settings', $key);
    }
    register_setting('whmp_whmcs_settings', 'whmp_seo_enable_urls');
    register_setting('whmp_whmcs_settings', 'whmp_langs');

    global $whmp_seo_urls;
    foreach ($whmp_seo_urls as $file) {
        register_setting('whmp_whmcs_settings', 'whmp_seo_' . $file);
    }
}

add_action('admin_init', 'Register_WHMCS_settings');

# Check if WHMpress plugin activated
require_once(dirname(__FILE__) . '/includes/functions.php');

# Check if WHMpress plugin activated
require_once(dirname(__FILE__) . '/includes/whmpress_ca.class.php');

$x_api_url = 'http://plugins.creativeon.com/api/';
$x_plugin_slug = basename(dirname(__FILE__));

function whmpress_register_first($class)
{
    $file = WHMP_CA_PATH . '/includes/Sabberworm/lib/' . strtr($class, '\\', '/') . '.php';
    if (is_file($file)) {
        require $file;
        return true;
    }
}

spl_autoload_register('whmpress_register_first');

add_action('init', 'whmpress_check_rules', 10, 0);
function whmpress_check_rules()
{
    #$this->rewrite_rule_with_languages();
    whmpress_ca_activation();

    wp_enqueue_style('whmcs_style', WHMP_CA_URL . "assets/css/styles.css");
    wp_enqueue_script('whmcs_scripts', WHMP_CA_URL . "assets/js/scripts.js", array('jquery'));

    ## Hide WP admin bar for non-Admins.
    if (get_option("whmcs_hide_wp_admin_bar") == "1" && !is_super_admin()) {
        add_filter('show_admin_bar', '__return_false');
    }

    /*if ( ! has_action( 'login_enqueue_scripts', 'wp_print_styles' ) ) {
        add_action( 'login_enqueue_scripts', 'wp_print_styles', 11 );
    }*/

    /*if (get_option('whmcs_enable_sso')=='1' && is_user_logged_in() && !empty($_SESSION['whmcs_wp_password']) && empty($_SESSION['whmcs_loggedin'])) {
        $whmcs = new WHMPress_Client_Area();
        $login_url = rtrim($whmcs->get_whmcs_url(), "/")."/dologin.php";
        $user = wp_get_current_user();
        $args = array(
            "username" => $user->data->user_email,
            "password" => $_SESSION['whmcs_wp_password']
        );
        $whmcs->start_session();
        $_SESSION['whmcs_loggedin'] = "1";
        $response = $whmcs->read_remote_url($login_url, $args);
//        if (!is_admin()) {
//            wp_redirect( $whmcs->get_whmcs_url() );
//        }
    }*/
}

if (!function_exists('show_array')) {
    function show_array($ar)
    {
        if (is_array($ar) || is_object($ar)) {
            echo "<pre>";
            print_r($ar);
            echo "</pre>";
        } elseif (is_bool($ar)) {
            if ($ar) return "TRUE";
            else return "FALSE";
        } else {
            print_r($ar);
        }
    }
}

register_activation_hook(__FILE__, 'whmpress_ca_activation');
function whmpress_ca_activation()
{
    $WHMP = new WHMPress_Client_Area();
    //$WHMP->whmp_rewrite_rule();
    $file = get_option("client_area_page_url");

    if ($file == "" || empty($file) || is_null($file)) {
        $install = true;
        $pages = get_pages();
        foreach ($pages as $page) {
            if ($page->post_title == "Client Area") {
                $install = false;
                $post_id = $page->ID;
                update_option("client_area_page_url", $post_id);
                update_option("whmp_show_admin_notice1", "1");
                break;
            }
        }

        if ($install) {
            $post = array(
                'post_content' => '[whmpress_client_area whmcs_template="" carttpl=""]', // The full text of the post.
                'post_title' => "Client Area", // The title of the post.
                'post_status' => 'publish', // Status will be published
                'post_type' => 'page', // Post type will be page
                'comment_status' => 'closed', // Comment status will be closed
            );

            /*$post_id = wp_insert_post($post);
            if ($post_id > 0) {
                update_option("client_area_page_url", $post_id);
                update_option("whmp_show_admin_notice1", "1");
            }*/
        }
    }

    $WHMP->rewrite_rule_with_languages();
}

if (is_admin()) {
    /**
     * Checking folder name of the plugin directory.
     * Added in 2.4.8
     */
    function whmpca_folder_name_check()
    {
        $c_folder = basename(dirname(__FILE__));
        if (WCA_FOLDER <> $c_folder) {
            echo "<div class='error'>
                <p><b>Cuation</b>: Your <i><b>WHMPress Client Area</b></i> installation folder name is <b><i>$c_folder</i></b>. Please rename folder to <i><b>" . WCA_FOLDER . "</b></i>, You can face problem in performance.</p>
            </div>";
        }
    }

    add_action('admin_notices', 'whmpca_folder_name_check', 1);

    include_once WHMP_CA_PATH . '/admin/admin.php';

    include_once dirname(__FILE__) . "/admin/index.php";
}

/**
 * Filters wp_title to print a neat <title> tag based on what is being viewed.
 *
 * @param string $title Default title text for current view.
 * @param string $sep Optional separator.
 * @return string The filtered title.
 */
function whmp_wp_title($title, $sep)
{
    global $post;

    if (!is_object($post)) return "";

    if ($post->ID <> get_option("client_area_page_url")) return $title;
    //if ($post->ID <> $this->get_client_area_page_id()) return $title;
    if (get_option("whmp_seo_enable_urls") <> "1") return $title;

    $current_url = wp_current_url();

    global $whmp_seo_urls;
    $WHMP = new WHMPress_Client_Area;
    foreach ($whmp_seo_urls as $file) {
        $url = $WHMP->set_url($WHMP->get_current_url(), $file);
        if (strpos($current_url, $url) !== false) {
            return get_option("whmp_seo_" . $file) . " " . $sep . " ";
        }
    }
    return $title;
}

add_filter('wp_title', 'whmp_wp_title', 10, 2);

/*
 * This action will act before WP authentication and will add new user if needed.
 */
add_action('wp_authenticate', 'whmcs_authentication', 30, 2);
function whmcs_authentication($username, $password)
{
    global $WCA_Fields;
    ## If username and password is provided.
    if (get_option("whmcs_enable_sync") == "1" && !empty($username) && !empty($password)) {
        $user = get_user_by('login', $username);
        if (!$user)
            $user = get_user_by('email', $username);

        ## If user is an Administrator, then sync will not work.
        if ($user && is_super_admin($user->ID)) {
            return;
        }

        $W = new WHMPress_Client_Area();
        if (get_option("sync_direction") == "3") {
            $priority = get_option('whmcs_both_ways_priority');
            if ($priority <> "wp") $priority = "whmcs";
            $is_whmcs_user = $W->is_whmcs_user($username);
            $is_wp_user = $W->is_wp_user($username);

            $role = get_option('whmcs_wordpress_role');
            if (empty($role)) $role = 'subscriber';
            if ($is_wp_user) {
                $user = get_user_by("login", $username);
                if (!$user) {
                    $user = get_user_by("email", $username);
                }
            }
            if ($is_whmcs_user && $is_wp_user) {
                $whmcs_authenticated = $W->authenticate_whmcs_user($username, $password);
                $wp_authenticated = $W->is_wp_user_valid($username, $password);

                if ($priority == "whmcs") {
                    if ($whmcs_authenticated == "OK") {
                        ## If WHMCS user authenticated.
                        ## Updating WordPress user password.
                        wp_set_password($password, $user->ID);

                        $whmcs_user = $W->get_whmcs_user($username);
                        $W->update_wp_user_metas($user->ID, $whmcs_user);

                        $W->start_session();
                        $_SESSION['whmcs_wp_password'] = $password;

                        whmcs_login($username, $user);
                    } else if ($wp_authenticated) {
                        ## If WHMCS user not authenticated.
                        ## Updating WHMCS user password.
                        $data["firstname"] = get_the_author_meta("first_name", $user->ID);
                        $data["lastname"] = get_the_author_meta("last_name", $user->ID);
                        if (get_option("whmcs_create_wp_fields") == "1") {
                            foreach ($WCA_Fields as $field => $A) {
                                $data[$field] = get_the_author_meta($field, $user->ID);
                            }
                        }
                        $W->update_whmcs_user_password($user->data->user_email, $password, $data);

                        $W->start_session();
                        $_SESSION['whmcs_wp_password'] = $password;
                        whmcs_login($username, $user);
                    }
                } else if ($priority == "wp") {
                    if ($wp_authenticated) {
                        ## If WP user authenticated.
                        ## Updating WHMCS user password.
                        $data["firstname"] = get_the_author_meta("first_name", $user->ID);
                        $data["lastname"] = get_the_author_meta("last_name", $user->ID);
                        if (get_option("whmcs_create_wp_fields") == "1") {
                            foreach ($WCA_Fields as $field => $A) {
                                $data[$field] = get_the_author_meta($field, $user->ID);
                            }
                        }

                        $W->update_whmcs_user_password($user->data->user_email, $password, $data);

                        $W->start_session();
                        $_SESSION['whmcs_wp_password'] = $password;
                        whmcs_login($username, $user);
                    } else if ($whmcs_authenticated == "OK") {
                        ## If WP user not authenticated.
                        ## Updating WordPress user password.
                        wp_set_password($password, $user->ID);

                        $w_user = $W->get_whmcs_user($user->data->user_email);
                        $W->update_wp_user_metas($user->ID, $w_user);
                        $W->start_session();
                        $_SESSION['whmcs_wp_password'] = $password;
                        whmcs_login($username, $user);
                    }
                }
            } else if ($is_whmcs_user && !$is_wp_user) {
                $whmcs_authenticated = $W->authenticate_whmcs_user($username, $password);
                if ($whmcs_authenticated == "OK") {
                    ## If WHMCS user authenticated.
                    ## Creating WP user

                    $w_user = $W->get_whmcs_user($username);

                    $userdata = array(
                        'user_login' => $username,
                        'user_email' => $username,
                        'user_pass' => $password,
                        'first_name' => $w_user['firstname'],
                        'last_name' => $w_user['lastname'],
                        'display_name' => $w_user['fullname'],
                        'description' => __("User created by WHMCS Client Area", "whmpress"),
                        'role' => $role
                    );
                    $user_id = wp_insert_user($userdata);

                    if (!is_wp_error($user_id)) {
                        $W->update_wp_user_metas($user_id, $w_user);
                        $W->start_session();
                        $_SESSION['whmcs_wp_password'] = $password;
                        whmcs_login($username, $user);
                    }
                }
            } else if (!$is_whmcs_user && $is_wp_user) {
                $wp_authenticated = $W->is_wp_user_valid($username, $password);
                if ($wp_authenticated) {
                    if (get_option('whmcs_create_wp_fields') == "1") {
                        $firstname = get_the_author_meta("first_name", $user->ID);
                        $lastname = get_the_author_meta("last_name", $user->ID);
                        $country = (get_the_author_meta("country", $user->ID) == "" ? $W->get_country() : $W->get_country_code(get_the_author_meta("country", $user->ID)));
                    } else {
                        $firstname = get_the_author_meta("first_name", $user->ID);
                        $lastname = get_the_author_meta("last_name", $user->ID);
                        $country = $W->get_country();
                    }

                    if (empty($firstname)) $firstname = "FirstName";
                    if (empty($lastname)) $lastname = "LastName";

                    $data = array(
                        "firstname" => $firstname,
                        "lastname" => $lastname,
                        "email" => $user->data->user_email,
                        "country" => $country,
                        "password2" => $password
                    );

                    if (get_option("whmcs_sso_handle_fields") == "disable_in_whmcs") {
                        // Do nothing.
                    } else {
                        if (get_option('whmcs_create_wp_fields') == "1") {
                            $data["address1"] = (get_the_author_meta("address1", $user->ID) == "" ? "Address 1" : get_the_author_meta("address1", $user->ID));
                            $data["address2"] = (get_the_author_meta("address2", $user->ID) == "" ? "Address 2" : get_the_author_meta("address2", $user->ID));
                            $data["city"] = (get_the_author_meta("city", $user->ID) == "" ? "Enter City" : get_the_author_meta("city", $user->ID));
                            $data["state"] = (get_the_author_meta("state", $user->ID) == "" ? "Enter State" : get_the_author_meta("state", $user->ID));
                            $data["postcode"] = (get_the_author_meta("postcode", $user->ID) == "" ? "012345" : get_the_author_meta("postcode", $user->ID));
                            $data["phonenumber"] = (get_the_author_meta("phonenumber", $user->ID) == "" ? "12345678" : get_the_author_meta("phonenumber", $user->ID));
                        }
                    }
                    $r = $W->add_whmcs_user($data);
                    if (substr($r, 0, 2) == "OK") {
                        $W->start_session();
                        $_SESSION['whmcs_wp_password'] = $password;
                        whmcs_login($username, $user);
                    } else {
                        die($r);
                    }
                }
            }
        } else if (get_option("sync_direction") == "2") {
            ## If Sync Direction is "WP to WHMCS"
            if (!$user) {
                $user = get_user_by('login', $username);
            }
            if (!$user) {
                $user = get_user_by('email', $username);
            }
            if ($user) {
                $valid_password = wp_check_password($password, $user->data->user_pass, $user->ID);
                if ($valid_password) {
                    ## If provided password is valid
                    $r = $W->get_whmcs_user($user->data->user_email);
                    if (!is_array($r)) {

                        if (get_option('whmcs_create_wp_fields') == "1") {
                            $firstname = get_the_author_meta("first_name", $user->ID);
                            $lastname = get_the_author_meta("last_name", $user->ID);
                            $country = (get_the_author_meta("country", $user->ID) == "" ? $W->get_country() : $W->get_country_code(get_the_author_meta("country", $user->ID)));
                        } else {
                            $firstname = get_the_author_meta("first_name", $user->ID);
                            $lastname = get_the_author_meta("last_name", $user->ID);
                            $country = $W->get_country();
                        }

                        if (empty($firstname)) $firstname = "FirstName";
                        if (empty($lastname)) $lastname = "LastName";

                        $data = array(
                            "firstname" => $firstname,
                            "lastname" => $lastname,
                            "email" => $user->data->user_email,
                            "country" => $country,
                            "password2" => $password
                        );

                        ## User not found in WHMCS, Add new user in WHMCS.
                        if (get_option("whmcs_sso_handle_fields") == "disable_in_whmcs") {
                            // Do nothing.
                        } else {
                            if (get_option('whmcs_create_wp_fields') == "1") {
                                $data["address1"] = (get_the_author_meta("address1", $user->ID) == "" ? "Address 1" : get_the_author_meta("address1", $user->ID));
                                $data["address2"] = (get_the_author_meta("address2", $user->ID) == "" ? "Address 2" : get_the_author_meta("address2", $user->ID));
                                $data["city"] = (get_the_author_meta("city", $user->ID) == "" ? "Enter City" : get_the_author_meta("city", $user->ID));
                                $data["state"] = (get_the_author_meta("state", $user->ID) == "" ? "Enter State" : get_the_author_meta("state", $user->ID));
                                $data["postcode"] = (get_the_author_meta("postcode", $user->ID) == "" ? "012345" : get_the_author_meta("postcode", $user->ID));
                                $data["phonenumber"] = (get_the_author_meta("phonenumber", $user->ID) == "" ? "12345678" : get_the_author_meta("phonenumber", $user->ID));
                            }
                        }
                        $r = $W->add_whmcs_user($data);
                        if (substr($r, 0, 2) <> "OK") {
                            die($r);
                        }
                    } else {
                        $data["firstname"] = get_the_author_meta("first_name", $user->ID);
                        $data["lastname"] = get_the_author_meta("last_name", $user->ID);
                        if (get_option("whmcs_create_wp_fields") == "1") {
                            foreach ($WCA_Fields as $field => $A) {
                                $data[$field] = get_the_author_meta($field, $user->ID);
                            }
                        }
                        $r = $W->update_whmcs_user_password($user->data->user_email, $password, $data);
                    }

                    if (substr($r, 0, 2) == "OK") {
                        $W->start_session();
                        $_SESSION['whmcs_wp_password'] = $password;
                        whmcs_login($username, $user);
                    }
                }
            }
        } else {
            ## Sync direction WHMCS to WP
            //if ( !$W->is_email($username) ) return;
            $response = $W->authenticate_whmcs_user($username, $password);
            if ($response == "OK") {
                $User = $W->get_whmcs_user($username);

                $role = get_option('whmcs_wordpress_role');
                if (empty($role)) $role = 'subscriber';

                if (get_option("whmcs_sso_login_name_type") == "fnln") {
                    $c_username = $User['firstname'] . " " . $User['lastname'];
                } else {
                    $c_username = $username;
                }

                $userdata = array(
                    'user_login' => $c_username,
                    'user_email' => $username,
                    'user_pass' => $password,
                    'first_name' => $User['firstname'],
                    'last_name' => $User['lastname'],
                    'display_name' => $User['fullname'],
                    'description' => __("User created by WHMCS Client Area", "whmpress"),
                    'role' => $role
                );
                $user_id = wp_insert_user($userdata);

                if (!is_wp_error($user_id)) {
                    $W->update_wp_user_metas($user_id, $User);
                    $W->start_session();
                    $_SESSION['whmcs_wp_password'] = $password;
                    whmcs_login($username, $user);
                }
            } else {
                die($response);
            }
        }
    }
}

/*
 * This action will act on WP authentication and can rewrite error messages etc.
 */
add_action('wp_authenticate_user', 'whmcs_wp_authenticate', 10, 2);
function whmcs_wp_authenticate($user, $password)
{
    ## If sync is not enabled in admin settings page then exit without any action.
    if (get_option("whmcs_enable_sync") <> "1") return $user;

    ## Return if user is not valid
    if (empty($user)) return $user;

    ## If user is an Administrator, then sync will not work.
    if (is_super_admin($user->ID)) {
        return $user;
    }

    ## Getting username.
    $username = $user->data->user_email;

    if (get_option("sync_direction") == "3") {
        ## Both direction sync
        return $user;
    } else if (get_option("sync_direction") == "2") {
        ## If Sync Direction is "WP to WHMCS"
        return $user;
    } else {

        ## If Sync Direction is "WHMCS to WP" or "Both Ways"
        $W = new WHMPress_Client_Area();
        $whmcs_authenticated = $W->authenticate_whmcs_user($username, $password);
        if ($whmcs_authenticated <> "OK") {
            $user_error = new WP_Error('100', "WHMCS: " . __($whmcs_authenticated, "whmpress"));
            return $user_error;
        } else {
            $W->start_session();
            $_SESSION['whmcs_wp_password'] = $password;
            wp_set_password($password, $user->ID);

            $w_user = $W->get_whmcs_user($username);
            $W->update_wp_user_metas($user->ID, $w_user);
            $W->start_session();
            $_SESSION['whmcs_wp_password'] = $password;
            whmcs_login($username, $user);

            return $user;
        }
    }
}

add_action('wp_logout', 'clean_ca_session_var');
function clean_ca_session_var()
{
    if (get_option("whmcs_enable_sync") == "1") {
        $W = new WHMPress_Client_Area();
        $W->start_session();
        if (isset($_SESSION['whmcs_wp_password'])) {
            unset($_SESSION['whmcs_wp_password']);
        }
        if (isset($_SESSION['whmcs_loggedin'])) {
            unset($_SESSION['whmcs_loggedin']);
        }

        $W->whmcs_user_logout();
        $logout_url = get_option('whmp_logout_url');
        if (empty($logout_url))
            wp_redirect($W->get_current_url());
        else
            wp_redirect($logout_url);
        exit();
    }
    /*$_current_url = $W->get_client_area_page_id();
    if (get_post_status($_current_url) !== false) {
        if (is_numeric($_current_url)) $_current_url = get_page_link($_current_url);
        wp_redirect($_current_url."?whmpca=logout");
        exit();
    }*/
}

/*function whmcs_authenticate($user, $username, $password)
{
    if (empty($username)) return;
    show_array($user);
    die;
    return $user;
}
add_filter('authenticate', 'whmcs_authenticate', 30, 3);*/

add_action('wp_login', 'whmcs_login', 10, 2);
function whmcs_login($username, $user)
{
    if (get_option('whmcs_enable_sso') <> '1') return;
    if (empty($_SESSION['whmcs_wp_password'])) return;
    if (!wp_check_password($_SESSION['whmcs_wp_password'], $user->data->user_pass, $user->ID)) return;

    $whmcs = new WHMPress_Client_Area();
    $login_url = rtrim($whmcs->get_whmcs_url(), "/") . "/dologin.php";
    $args = array(
        "username" => $user->data->user_email,
        "password" => $_SESSION['whmcs_wp_password']
    );
    $W = new WHMPress_Client_Area();
    $W->start_session();
    $_SESSION['whmcs_loggedin'] = "1";

    $response = $whmcs->read_remote_url($login_url, $args, array(), false);
}


add_action('wp_ajax_wca_admin_ajax', 'wca_admin_ajax');
function wca_admin_ajax()
{
    include_once WHMP_CA_PATH . "/admin/ajax.php";
    wp_die();
}


if (get_option('whmcs_create_wp_fields') == '1') {
    add_filter('user_contactmethods', 'whmcs_custom_contact_fields');
    function whmcs_custom_contact_fields()
    {
        global $WCA_Fields;
        foreach ($WCA_Fields as $field => $A) {
            $profile_fields[$field] = $A['label'];
        }

        return $profile_fields;
    }

//add_action( 'register_form', 'whmcs_custom_contact_fields_register' );
    function whmcs_custom_contact_fields_register()
    {
        global $WCA_Fields;

        // Display each field if 3th parameter set to "true"
        foreach ($WCA_Fields as $field => $data) {
            $field_value = isset($_POST[$field]) ? $_POST[$field] : '';
            echo '<p>
			<label for="' . esc_attr($field) . '">' . esc_html($data['label']) . '<br />
			<input type="text" name="' . esc_attr($field) . '" id="' . esc_attr($data['label']) . '" class="input" value="' . esc_attr($field_value) . '" size="20" /></label>
			</label>
		</p>';
        } // end foreach
    }
}