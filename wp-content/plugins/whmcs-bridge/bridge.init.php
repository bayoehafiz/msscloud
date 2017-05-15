<?php
if (!defined('WHMCS_BRIDGE')) define('WHMCS_BRIDGE','WHMCS Bridge');
if (!defined('WHMCS_BRIDGE_COMPANY')) define('WHMCS_BRIDGE_COMPANY','i-Plugins');
if (!defined('WHMCS_BRIDGE_PAGE')) define('WHMCS_BRIDGE_PAGE','WHMCS');

define("CC_WHMCS_BRIDGE_VERSION","3.8.5");

if (!defined('PHP_VERSION_ID')) {
    $version = explode('.', PHP_VERSION);

    define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
}


$compatibleWHMCSBridgeProVersions=array('2.0.1'); //kept for compatibility with older Pro versions, not used since version 2.0.0

// Pre-2.6 compatibility for wp-content folder location

if (!defined("WP_CONTENT_URL")) {
    define("WP_CONTENT_URL", get_option("siteurl") . "/wp-content");
}

if (!defined("WP_CONTENT_DIR")) {
    define("WP_CONTENT_DIR", ABSPATH . "wp-content");
}

if (!defined("CC_WHMCS_BRIDGE_PLUGIN")) {
    $cc_whmcs_bridge_plugin = str_replace(realpath(dirname(__FILE__).'/..'),"",dirname(__FILE__));
    $cc_whmcs_bridge_plugin = substr($cc_whmcs_bridge_plugin,1);
    define("CC_WHMCS_BRIDGE_PLUGIN", $cc_whmcs_bridge_plugin);
}

if (!defined("BLOGUPLOADDIR")) {
    $upload=wp_upload_dir();
    define("BLOGUPLOADDIR",$upload['path']);
}

define("CC_WHMCS_BRIDGE_URL", WP_CONTENT_URL . "/plugins/".CC_WHMCS_BRIDGE_PLUGIN."/");

$cc_whmcs_bridge_version = get_option("cc_whmcs_bridge_version");

if ($cc_whmcs_bridge_version) {
    add_action("init","cc_whmcs_bridge_init");

    if (get_option('cc_whmcs_bridge_footer')=='Site')
        add_filter('wp_footer','cc_whmcs_bridge_footer');

    add_filter('the_content', 'cc_whmcs_bridge_content', 10, 2);

    add_action('wp_head','cc_whmcs_bridge_header',10);
    add_action("plugins_loaded", "cc_whmcs_sidebar_init");
    add_action('wp_ajax_check_bridge', 'cc_whmcs_bridge_checks');
}

add_action('admin_head','cc_whmcs_bridge_admin_header');
add_action('admin_notices','cc_whmcs_admin_notices');

require_once(dirname(__FILE__) . '/includes/shared.inc.php');
require_once(dirname(__FILE__) . '/includes/http.class.php');
require_once(dirname(__FILE__) . '/includes/footer.inc.php');
require_once(dirname(__FILE__) . '/includes/integrator.inc.php');
require_once(dirname(__FILE__) . '/bridge_cp.php');

if (!class_exists('iplug_simple_html_dom_node'))
    require_once(dirname(__FILE__) . '/includes/simple_html_dom.php');

require(dirname(__FILE__).'/includes/sidebars.php');
require(dirname(__FILE__).'/includes/parser.inc.php');

function cc_whmcs_admin_notices() {
    global $wpdb;
    $errors=array();
    $warnings=array();
    $notices=array();
    $files=array();
    $dirs=array();

    $cc_whmcs_bridge_version=get_option("cc_whmcs_bridge_version");
    if ($cc_whmcs_bridge_version && $cc_whmcs_bridge_version != CC_WHMCS_BRIDGE_VERSION) $warnings[]='You downloaded version '.CC_WHMCS_BRIDGE_VERSION.' and need to update your settings (currently at version '.$cc_whmcs_bridge_version.') by verifying your settings and clicking the "Save Settings" button on the <a href="options-general.php?page=cc-ce-bridge-cp">bridge control panel</a>.';
    $upload=wp_upload_dir();

    if (cc_whmcs_bridge_mainpage()) {
        if (isset($_REQUEST['whmcs_clear'])) $warnings[] = 'Cache clear has been triggered.';

        $cache = (int)get_option('cc_whmcs_bridge_sso_cache');
        if ($cache != false && $cache > 0 && !is_writable(dirname(__FILE__).'/cache'))
            $warnings[] = 'Your cache directory is not writable. Please make sure the "cache" folder inside your whmcs-bridge plugin folder is writable.';

        //if (session_save_path() && !is_writable(session_save_path())) $warnings[]='It looks like PHP sessions are not properly configured on your server, the sessions save path <'.session_save_path().'> is not writable. This may be a false warning, contact us if in doubt.';

        if ($upload['error'])
            $errors[]=$upload['error'];

        if (!get_option('cc_whmcs_bridge_url'))
            $warnings[]="Please update your WHMCS connection settings on the plugin control panel";

        //if (get_option('cc_whmcs_bridge_debug')) $warnings[]="Debug is active, once you finished debugging, it's recommended to turn this off";

        if (phpversion() < '5')
            $warnings[]="You are running PHP version ".phpversion().". We recommend you upgrade to PHP 5.3 or higher.";

        if (ini_get("zend.ze1_compatibility_mode"))
            $warnings[]="You are running PHP in PHP 4 compatibility mode. We recommend you turn this option off.";

        if (!function_exists('curl_init')) $errors[]="You need to have cURL installed. Contact your hosting provider to do so.";
    }

    if (get_option("cc_whmcs_bridge_url") && !preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', get_option("cc_whmcs_bridge_url"))) $errors[]='Your WHMCS URL '.get_option("cc_whmcs_bridge_url").' seems to be incorrect, please verify it and make sure it starts with http or https.';

    if (count($errors) > 0) {
        foreach ($errors as $message)  {
            echo "<div id='zing-warning' style='background-color:pink' class='updated fade'><p><strong>";
            echo WHMCS_BRIDGE.':'.$message.'<br />';
            echo "</strong> "."</p></div>";
        }
    }
    if (count($warnings) > 0) {
        foreach ($warnings as $message) {
            echo "<div id='zing-warning' style='background-color:greenyellow' class='updated fade'><p><strong>";
            echo WHMCS_BRIDGE.': '.$message.'<br />';
            echo "</strong> "."</p></div>";
        }
    }
    if (isset($_REQUEST['page']) && ($_REQUEST['page']=='cc-ce-bridge-cp') && count($notices) > 0) {
        foreach ($notices as $message) {
            echo "<div id='zing-warning' style='background-color:lightyellow' class='updated fade'><p><strong>";
            echo $message.'<br />';
            echo "</strong> "."</p></div>";
        }
    }

    return array('errors'=> $errors, 'warnings' => $warnings);
}


function cc_whmcs_bridge_checks() {
    /*
     * Checks to perform
     * 1) WHMCS URL is correct
     * 2) WHMCS URL does not have any HTTP errors
     * 3) WHMCS Bridge page exists
     * 4) Bridge page doesn't contain any irregularities (beta)
     */

    $return_message = array();
    $proceed = true;

    $whmcs_url = get_option('cc_whmcs_bridge_url');

    if (stristr($whmcs_url, '.php') !== false) {
        $return_message[] = "Your WHMCS URL should not include any filenames, only to your WHMCS folder (not admin), eg: http://yourdomain.tld/whmcs/";
        $proceed = false;
    }

    if ($proceed) {
        $ch = curl_init();    // initialize curl handle
        curl_setopt($ch, CURLOPT_URL, $whmcs_url); // set url to post to
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); // times out after 30s
        curl_setopt($ch, CURLOPT_HEADER, true);
        if (stristr($whmcs_url, "https") !== false) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_CAINFO, NULL);
            curl_setopt($ch, CURLOPT_CAPATH, NULL);
        }
        $data = curl_exec($ch); // run the whole process

        $network_blurb = "<br/><br/>The bridge needs to be able to use your WordPress website to call your WHMCS URL; if they are hosted on the same machine then the hosting provider must allow for traffic to be routed back to the same host; this functionality is standard on most hosting providers so it should not be a problem for it to be resolved. There is no need to query i-Plugins on this issue as we are unable to change settings on your server.";

        if (curl_errno($ch) != 0) {
            $return_message[] = "Please rectify this with your system administrator (hosting provider), when your WordPress site attempts to use PHP CURL to connect to {$whmcs_url}, the error code ".curl_errno($ch)." [".curl_error($ch)."] is being returned (more details on curl errors @ <a target=\"_blank\" href=\"https://curl.haxx.se/libcurl/c/libcurl-errors.html\">https://curl.haxx.se/libcurl/c/libcurl-errors.html</a>). {$network_blurb}";

            $proceed = false;
        } else {
            /*if (strstr($data, 'btnOrderHosting') === false) {
                $return_message[] = "The bridge was unable to find a component common to WHMCS installations (if you are using 'six' or 'five' as your template, it definitely should be there), please make sure {$whmcs_url} is the location of your direct WHMCS installation (the bridge requires you already have WHMCS set up and installed - if you don't have WHMCS, please <a target=\"_blank\" href=\"http://www.whmcs.com/members/aff.php?aff=23386\">click here</a>).";
                $proceed = false;
            }*/
        }

        if ($proceed) {
            /*$nossl_url = str_replace('https://', 'http://', $whmcs_url);
            curl_setopt($ch, CURLOPT_URL, $nossl_url.'/cart.php'); // set url to post to
            $data = curl_exec($ch); // run the whole process
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($code != 200) {
                $return_message[] = "Your cart page is returning an HTTP response code of {$code}.<br/>Codes of 301/302 mean that a redirect is occurring. The most common reason for this is that you have entered a 'WHMCS SSL System URL' in your WHMCS Admin 'General Settings' page. You can have a SSL (https) URL for your WHMCS but to use the bridge you must put the SSL URL into the 'WHMCS System URL' field and make sure the 'WHMCS SSL System URL' field is left blank. The redirects in WHMCS do not function correctly with the bridge.";

                $proceed = false;
            }*/
        }
    }


    if (count($return_message) > 0) {
        echo "<ul style='margin-left:20px; font-weight: bold; color:#cc0000'><li style='list-style-type: square;'>";
        echo implode("</li><li>", $return_message);
        echo "</li></ul>";
    } else {
        echo "As far as we can see, it all looks good!";
    }

    wp_die();
}

/**
 * Activation: creation of database tables & set up of pages
 * @return unknown_type
 */
function cc_whmcs_bridge_activate() {
    //nothing much to do
}

function cc_whmcs_bridge_install() {
    global $wpdb,$current_user,$wp_rewrite;

    ob_start();
    cc_whmcs_log();
    set_error_handler('cc_whmcs_log');
    error_reporting(E_ALL & ~E_NOTICE);

    $cc_whmcs_bridge_version=get_option("cc_whmcs_bridge_version");
    if (!$cc_whmcs_bridge_version) add_option("cc_whmcs_bridge_version",CC_WHMCS_BRIDGE_VERSION);
    else update_option("cc_whmcs_bridge_version",CC_WHMCS_BRIDGE_VERSION);

    $cc_whmcs_bridge_page=get_option("cc_whmcs_bridge_pages");
    $create_page = false;
    if (is_numeric($cc_whmcs_bridge_page) && $cc_whmcs_bridge_page > 0) {
        $query = '';
        $pages = get_pages(array(
            'post_type' => 'page',
            'post_status' => 'publish',
        ));
        $found = false;
        foreach ($pages as $p) {
            if ($p->ID == $cc_whmcs_bridge_page) {
                $found = true;
                break;
            }
        }
        if (!$found) $create_page = true;
    } else {
        $create_page = true;
    }

    //create pages
    if ($create_page) {
        cc_whmcs_log(0,'Creating pages');
        $pages=array();
        $pages[]=array(WHMCS_BRIDGE_PAGE.'-bridge',WHMCS_BRIDGE_PAGE,"*",0);

        $ids="";
        foreach ($pages as $i =>$p)
        {
            $my_post = array();
            $my_post['post_title'] = $p['0'];
            $my_post['post_content'] = '';
            $my_post['post_status'] = 'publish';
            $my_post['post_author'] = 1;
            $my_post['post_type'] = 'page';
            $my_post['menu_order'] = 100+$i;
            $my_post['comment_status'] = 'closed';
            $id=wp_insert_post( $my_post );
            if (empty($ids)) { $ids.=$id; } else { $ids.=",".$id; }
            if (!empty($p[1])) add_post_meta($id,'cc_whmcs_bridge_page',$p[1]);
        }
        update_option("cc_whmcs_bridge_pages",$ids);
    }

    restore_error_handler();

    $wp_rewrite->flush_rules();

    return true;
}

function cc_whmcs_bridge_uninstall() {
    $cc_whmcs_bridge_options=cc_whmcs_bridge_options();

    delete_option('cc_whmcs_bridge_log');
    foreach ($cc_whmcs_bridge_options as $value) {
        delete_option( $value['id'] );
    }

    delete_option("cc_whmcs_bridge_page");
    delete_option("cc_whmcs_bridge_pages");
    delete_option("cc_whmcs_bridge_log");
    delete_option("cc_whmcs_bridge_ftp_user"); //legacy
    delete_option("cc_whmcs_bridge_ftp_password"); //legacy
    delete_option("cc_whmcs_bridge_version");
    delete_option("cc_whmcs_bridge_pages");
    delete_option('cc-ce-bridge-cp-support-us');
}

/**
 * Deactivation: nothing to do
 * @return void
 */
function cc_whmcs_bridge_deactivate() {
    $ids=get_option("cc_whmcs_bridge_pages");
    $ida=explode(",",$ids);
    foreach ($ida as $id) {
        wp_delete_post($id);
    }
}

function cc_whmcs_bridge_output($page=null) {
    global $post;
    global $wpdb;
    global $wordpressPageName;
    global $cc_whmcs_bridge_loaded,$cc_whmcs_bridge_to_include;

    $ajax=false;

    $ref = rand(100, 999);

    cc_whmcs_log(0, '[URL '.$ref.'] Remote fetch started');

    // contribution northgatewebhosting.co.uk
    if (isset($post)) {
        $post_id = $post->ID;
    } else {
        $post_id = 1;
    }
    // contribution northgatewebhosting.co.uk

    $cf=get_post_custom($post_id);

    if ($page) {
        $cc_whmcs_bridge_to_include=$page;
    } elseif (isset($_REQUEST['ccce']) && (isset($_REQUEST['ajax']) && $_REQUEST['ajax'])) {
        $cc_whmcs_bridge_to_include=$_REQUEST['ccce'];
        $ajax=intval($_REQUEST['ajax']);
    } elseif (isset($_REQUEST['ccce'])) {
        $cc_whmcs_bridge_to_include=$_REQUEST['ccce'];
    } elseif (isset($cf['cc_whmcs_bridge_page']) && $cf['cc_whmcs_bridge_page'][0]==WHMCS_BRIDGE_PAGE) {
        $cc_whmcs_bridge_to_include="index";
    } else {
        $cc_whmcs_bridge_to_include="index";
    }

    $http = cc_whmcs_bridge_http($cc_whmcs_bridge_to_include);

    cc_whmcs_log(0, '[URL '.$ref.'] HTTP Request initiated');

    if (strstr($http, '?a=checkout') !== false && isset($_REQUEST['action']) && $_REQUEST['action'] == 'doPayment') {
        $http = str_replace('?a=checkout', '?a=complete', $http);
        cc_whmcs_log(0, '[URL '.$ref.'] URL Adjusted to ?a=complete');
    }

    if (strstr($http, 'index.php') !== false && isset($_REQUEST['a']) && in_array($_REQUEST['a'], array('addToCart', 'updateDomainPeriod')) && isset($_REQUEST['domain'])) {
        $http = str_replace('index.php', 'cart.php', $http);
        cc_whmcs_log(0, '[URL '.$ref.'] URL Adjusted to ?a=complete');
    }

    $news = new bridgeHttpRequest($http,'whmcs-bridge-sso');
    $news->debugFunction='cc_whmcs_log';
    if (function_exists('cc_whmcs_bridge_sso_httpHeaders')) $news->httpHeaders=cc_whmcs_bridge_sso_httpHeaders($news->httpHeaders);

    if (isset($news->post['whmcsname'])) {
        $news->post['name']=$news->post['whmcsname'];
        unset($news->post['whmcsname']);
    }

    $news=apply_filters('bridge_http',$news);

    cc_whmcs_log(0, '[URL '.$ref.'] Filters applied');

    $news->forceWithRedirect['systpl'] = get_option('cc_whmcs_bridge_template') ? get_option('cc_whmcs_bridge_template') : 'five';

    if (!function_exists('cc_whmcs_bridge_parser_with_permalinks') && !in_array($news->forceWithRedirect['systpl'], array('portal', 'five', 'six'))) {
        $news->forceWithRedirect['systpl'] = 'five';
    }

    if ($cc_whmcs_bridge_to_include=='dologin') {
        $news->post['rememberme']='on';
    }

    cc_whmcs_log(0, '[URL '.$ref.'] Filters applied [2]');

    if (!$news->curlInstalled()) {
        cc_whmcs_log('Error','CURL not installed');
        return "cURL not installed";
    } elseif (!$news->live()) {
        cc_whmcs_log('Error','A HTTP Error occurred');
        return "A HTTP Error occurred";
    } else {
        //@cc_whmcs_log(0, '[URL '.$ref.'] Headers: '.$news->headers['content-type'].' - Disposition: '.$news->headers['content-disposition']);

        cc_whmcs_log(0, '[URL '.$ref.'] Processing...');

        if ($cc_whmcs_bridge_to_include=='verifyimage' || (isset($_REQUEST['showqrimage']) && $_REQUEST['showqrimage'] == 1)
            || (isset($_REQUEST['js']) && (
                    stristr($_REQUEST['js'], '.jpg') !== false ||
                    stristr($_REQUEST['js'], '.png') !== false ||
                    stristr($_REQUEST['js'], '.jpeg') !== false ||
                    stristr($_REQUEST['js'], '.gif') !== false
                ))
        ) {
            $output = $news->DownloadToString();
            while (count(ob_get_status(true)) > 0) ob_end_clean();

            $cache_setting = (int)get_option("cc_whmcs_bridge_sso_cache");
            if (is_numeric($cache_setting) && $cache_setting > 0 &&
                $cc_whmcs_bridge_to_include != 'verifyimage' && !isset($_REQUEST['showqrimage'])) {
                $cache_dir = dirname(__FILE__) . '/cache/';
                if (file_exists($cache_dir) && is_writable($cache_dir)) {
                    $extension = pathinfo($http, PATHINFO_EXTENSION);

                    $filename = md5($_REQUEST['js']) . '_parsed_' . strtotime('+' . $cache_setting . ' minutes') . '.' . $extension;

                    file_put_contents($cache_dir . $filename, $news->body);
                    cc_whmcs_log(0, '[1] Image cache written for ' . $_REQUEST['js']);
                    // log cached file
                    cc_update_cache($_REQUEST['js'], $filename);
                }
            }

            $filename = basename($_REQUEST['js']);
            $file_extension = strtolower(substr(strrchr($filename,"."),1));

            switch ($file_extension) {
                case "gif": $ctype="image/gif"; break;
                case "png": $ctype="image/png"; break;
                case "jpeg":
                case "jpg": $ctype="image/jpeg"; break;
                default: $ctype="image"; break;
            }

            header("Content-Type: $ctype");
            echo $news->body;

            die();
        } elseif (stristr($cc_whmcs_bridge_to_include, 'announcementsrss') !== false
            || stristr($cc_whmcs_bridge_to_include, 'networkissuesrss') !== false
        ) {
            while (count(ob_get_status(true)) > 0) ob_end_clean();
            $output=$news->DownloadToString();
            header('Content-Type: application/rss+xml; charset=utf-8');
            echo $news->body;
            die();
        } elseif (($cc_whmcs_bridge_to_include=='dl' && $news->headers['content-type'] != 'text/html') ||
            (isset($_REQUEST['playlist'], $_REQUEST['device_mac'])) ||
            (isset($_REQUEST['act']) && $_REQUEST['act'] == 'download') ||
            (isset($_REQUEST['playlist'], $_REQUEST['action']) && $_REQUEST['action'] == 'productdetails') ||
            (isset($_REQUEST['a']) && $_REQUEST['a'] == 'CreateEmailAccount') ||
            (isset($_REQUEST['action'], $_REQUEST['m']) && $_GET['action'] == 'download' && $_GET['m'] == 'invoiceme') ||
            (stristr($cc_whmcs_bridge_to_include, 'wbteampro') !== false && isset($_REQUEST['view']) && $_REQUEST['view'] == 'raw') ||
            (stristr($cc_whmcs_bridge_to_include, 'wbteampro') !== false && isset($_REQUEST['act']) && $_REQUEST['act'] == 'download') ||
            (stristr($cc_whmcs_bridge_to_include, 'project_management') !== false && isset($_REQUEST['action']) && $_REQUEST['action'] == 'dl') ||
            (stristr($cc_whmcs_bridge_to_include, 'project_management') !== false && stristr($cc_whmcs_bridge_to_include, '.css') !== false)
        ) {
            while (count(ob_get_status(true)) > 0) ob_end_clean();
            $output=$news->DownloadToString();
            header("Content-Disposition: ".$news->headers['content-disposition']);
            header("Content-Type: ".$news->headers['content-type']);
            echo $news->body;
            die();
        } elseif ($ajax == 1 ||
            (isset($_REQUEST['vserverid'])) ||
            (isset($_REQUEST['_vnc']) && $_REQUEST['ccce'] == 'vnc') ||
            (stristr($cc_whmcs_bridge_to_include, 'serverstatus') !== false && isset($_REQUEST['num'])) ||
            (isset($_REQUEST['action'])	&& $_REQUEST['action'] == 'getcustomfields') ||
            (isset($_REQUEST['check'], $_REQUEST['addtocart'], $_REQUEST['domain'])) ||
            (isset($_REQUEST['a'], $_REQUEST['domain']) && $_REQUEST['a'] == 'updateDomainPeriod') ||
            (isset($_REQUEST['a'], $_REQUEST['domain']) && $_REQUEST['a'] == 'validateCaptcha') ||
            (isset($_REQUEST['a'], $_REQUEST['domain']) && $_REQUEST['a'] == 'checkDomain') ||
            (isset($_REQUEST['responseType']) && $_REQUEST['responseType'] == 'json') ||
            (isset($_REQUEST['action']) && $_REQUEST['action'] == 'doPayment') ||
            (stristr($cc_whmcs_bridge_to_include, 'creditcard') !== false && isset($_REQUEST['cccvv']) && $_REQUEST['action'] == 'submit' && stristr($news->DownloadToString(), 'twocheckout.php') !== false) ||
            (stristr($cc_whmcs_bridge_to_include, 'cart') !== false && isset($_REQUEST['cccvv'], $_REQUEST['paymentmethod']) && $_REQUEST['paymentmethod'] == 'twocheckout') ||
            (isset($_REQUEST['PaRes']) && isset($_REQUEST['MD'])) ||
            (isset($_REQUEST['action']) && $_REQUEST['action'] == 'productdetails' && isset($_REQUEST['give'])) ||
            (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
        ) {
            $output=$news->DownloadToString();
            if (!$news->redirect) {
                while (count(ob_get_status(true)) > 0) ob_end_clean();
                $body=$news->body;
                if (isset($_REQUEST['give']) && stristr($_REQUEST['give'], '.html') !== false) {
                    $body = cc_whmcs_bridge_parser_ajax1($body, $cc_whmcs_bridge_to_include);
                } elseif (strstr($cc_whmcs_bridge_to_include, 'creditcard') !== false && strstr($body, 'twocheckout') !== false) {
                    $body = cc_whmcs_bridge_parser($output);
                    $body = '<html><head>'.$body['head'].'</head><body onload="callcreatetoken()">'.str_replace('<script>', '<script type="text/javascript">', $body['main']).'</body></html>';
                } elseif (strstr($cc_whmcs_bridge_to_include, 'creditcard') !== false) {
                    return $output;
                } else if (isset($_REQUEST['vserverid'])) {
                    header('Content-Type: application/json');
                    echo $body;
                    die();
                } else
                    $body = cc_whmcs_bridge_parser_ajax1($body, $cc_whmcs_bridge_to_include);

                if ((isset($_REQUEST['js']) && stristr($_REQUEST['js'], '.css') !== false) || (isset($_REQUEST['give']) && stristr($_REQUEST['give'], '.css') !== false)) {
                    header('Content-Type: text/css');

                    if (get_option('cc_whmcs_bridge_prefix') &&
                        !isset($_REQUEST['give']) &&
                        stristr($_REQUEST['js'], 'invoice.css') === false &&
                        stristr($_REQUEST['js'], 'font-awesome') === false)
                    {

                        $bl = explode("\n", $body); //split css into lines
                        foreach ($bl as $line) {
                            if (strpos($line, "{") !== false) {
                                if (stristr($line, 'rgba') !== false && stristr($line, 'font-family') !== false) {
                                    $line = str_replace(",", ",#bridge ", $line); //Add #bridge to styles
                                }
                                $line = str_replace("}#", "}#bridge #", $line); // Minified files
                                $line = str_replace("}.", "}#bridge .", $line); // Minified files
                                if (substr($line, 0, 1) !== "@")
                                    $line = "#bridge $line"; //do not break responsiveness
                                echo $line . "\n";
                            } else if (substr(trim($line),-1) == ',' && (substr($line, 0, 1) !== "@")) {
                                echo "#bridge $line\n";
                            } else {
                                echo $line . "\n";
                            }
                        }

                    } else {
                        echo $body;
                    }
                    die();
                } else if ((isset($_REQUEST['js']) && stristr($_REQUEST['js'], '.js') !== false) || (isset($_REQUEST['give']) && stristr($_REQUEST['give'], '.js') !== false)) {
                    header('Content-Type: application/javascript');
                } else if (isset($_REQUEST['a'], $_REQUEST['type'], $_REQUEST['domain']) ||
                    isset($_REQUEST['responseType']) && $_REQUEST['responseType'] == 'json' ||
                    isset($_REQUEST['a'], $_REQUEST['domain'])) {
                    header('Content-Type: application/json');
                }
                echo $body;
                die();
            } else {
                header('Location:'.$output);
                die();
            }
        } elseif ($ajax==2) {
            while (count(ob_get_status(true)) > 0) ob_end_clean();
            $output=$news->DownloadToString();
            $body=$news->body;
            $body=cc_whmcs_bridge_parser_ajax2($body);

            if (isset($_REQUEST['js']) && stristr($_REQUEST['js'], '.css') !== false) {
                header('Content-Type: text/css');
            } else if (isset($_REQUEST['js']) && stristr($_REQUEST['js'], '.js') !== false) {
                header('Content-Type: application/javascript');
            } else {
                header('HTTP/1.1 200 OK');
            }

            echo $body;
            die();
        } elseif ($news->redirect) {
            $output=$news->DownloadToString();
            if ($wordpressPageName) $p=$wordpressPageName;
            else $p='/';
            $f[]='/.*\/([a-zA-Z\_]*?).php.(.*?)/';
            $r[]=get_option('home').$p.'?ccce=$1&$2';
            $f[]='/([a-zA-Z\_]*?).php.(.*?)/';
            $r[]=get_option('home').$p.'?ccce=$1&$2';
            $output=preg_replace($f,$r,$news->location,-1,$count);
            cc_whmcs_log('Notification','[1] Redirect to: '.$output);
            header('Location:'.$output);
            die();
        } else {
            if (isset($_REQUEST['aff'])) $news->follow=false;
            $output=$news->DownloadToString();

            if ($news->redirect) {
                header('Location:'.$output);
                die();
            }
            if (isset($_REQUEST['aff']) && isset($news->headers['location'])) {
                if ($wordpressPageName) $p=$wordpressPageName;
                else $p='/';
                $f[]='/.*\/([a-zA-Z\_]*?).php.(.*?)/';
                $r[]=get_option('home').$p.'?ccce=$1&$2';
                $f[]='/([a-zA-Z\_]*?).php.(.*?)/';
                $r[]=get_option('home').$p.'?ccce=$1&$2';
                $output=preg_replace($f,$r,$news->headers['location'],-1,$count);
                cc_whmcs_log('Notification','[2] Redirect to: '.$output);
                header('Location:'.$output);

                //if (strstr($news->headers['location'],get_option('home')))
                //    header('Location:'.$news->headers['location']);
                //else header('Location:'.get_option('home'));
                die();
            }

            cc_whmcs_log(0, '[URL '.$ref.'] Remote fetch completed (standard content)');

            return $output;
        }
    }
}

/**
 * Page content filter
 * @param $content
 * @return unknown_type
 */
function cc_whmcs_bridge_content($content) {
    global $cc_whmcs_bridge_content,$post;

    if (!is_page()) return $content;

    $cf=get_post_custom($post->ID);
    if (isset($_REQUEST['ccce']) || (isset($cf['cc_whmcs_bridge_page']) && $cf['cc_whmcs_bridge_page'][0]==WHMCS_BRIDGE_PAGE)) {
        if (!isset($cc_whmcs_bridge_content)) { //support Gantry framework
            $cc_whmcs_bridge_content = cc_whmcs_bridge_parser();
        }
        if ($cc_whmcs_bridge_content) {
            $content='';
            ob_start();

            if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('whmcs-top-page')):
            endif;

            $content .= ob_get_clean();
            $content .= '<div id="bridge">';

            if (is_array($cc_whmcs_bridge_content) && isset($cc_whmcs_bridge_content['main']))
                $content .= $cc_whmcs_bridge_content['main'];

            $content .= '</div><!--end bridge-->';

            ob_start();

            if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('whmcs-bottom-page')):
            endif;

            $content .= ob_get_clean();

            if (get_option('cc_whmcs_bridge_footer')=='Page')
                $content .= cc_whmcs_bridge_footer(true);
        }
    }

    return $content;
}

function cc_whmcs_bridge_header() {
    global $cc_whmcs_bridge_content,$post;

    if (!(isset($post->ID))) return;

    $cf=get_post_custom($post->ID);
    if (isset($_REQUEST['ccce']) || (isset($cf['cc_whmcs_bridge_page']) && $cf['cc_whmcs_bridge_page'][0]==WHMCS_BRIDGE_PAGE)) {
        if (isset($_REQUEST['ajax']) && in_array($_REQUEST['ajax'], array(1,2))) return;

        if (!isset($cc_whmcs_bridge_content)) {
            $cc_whmcs_bridge_content=cc_whmcs_bridge_parser();
        }

        if (isset($cc_whmcs_bridge_content['head'])) echo $cc_whmcs_bridge_content['head'];

        echo '<style type="text/css">#whmcsimglogo { display: none }</style>';

        if (get_option('cc_whmcs_bridge_css')) {
            echo '<style type="text/css">'.get_option('cc_whmcs_bridge_css').'</style>';
        }
        if (get_option('cc_whmcs_bridge_sso_js')) {
            echo '<script type="text/javascript">'.stripslashes(get_option('cc_whmcs_bridge_sso_js')).'</script>';
        }
    }
    if (get_option('cc_whmcs_bridge_jquery')=='wp') echo '<script type="text/javascript">$=jQuery;</script>';
}

function cc_whmcs_bridge_admin_header() {
    echo '<link rel="stylesheet" type="text/css" href="' . str_replace('trunk/', 'whmcs-bridge/', CC_WHMCS_BRIDGE_URL) . 'cc.css?ver=3.7.0" media="screen" /><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css">';
}

function cc_whmcs_bridge_http($page="index") {
    global $wpdb;

    $whmcs=cc_whmcs_bridge_url();
    if (substr($whmcs,-1)!='/') $whmcs.='/';
    if ((strpos($whmcs,'https://')!==0) && isset($_REQUEST['sec']) && ($_REQUEST['sec']=='1'))
        $whmcs=str_replace('http://','https://',$whmcs);

    $vars="";

    if ($page=='verifyimage') {
        $http=$whmcs.'includes/'.$page.'.php';
    } elseif (isset($_REQUEST['ccce']) && ($_REQUEST['ccce']=='js')) {
        if (isset($_REQUEST['js']))
            $http = $whmcs.$_REQUEST['js'];
        return $http;
    } elseif (substr($page,-1)=='/') {
        $http=$whmcs.substr($page,0,-1);
    } else {
        $http=$whmcs.$page.'.php';
    }

    $and="";
    if (count($_GET) > 0) {
        foreach ($_GET as $n => $v) {
            if ($n!="page_id" && $n!="ccce" && $n!='whmcspage')  {
                if (is_array($v)) {
                    foreach ($v as $n2 => $v2) {
                        $vars.= $and.$n.'['.$n2.']'.'='.urlencode($v2);
                        if (is_array($v2)) {
                            foreach ($v2 as $n3 => $v3)
                                $vars.= $and.$n.'['.$n2.']['.$n3.']'.'='.urlencode($v3);
                        }
                    }
                } else {
                    $vars.= $and.$n.'='.urlencode($v);
                }
                $and="&";
            }
        }
    }

    if (isset($_GET['whmcspage'])) {
        $vars.=$and.'page='.$_GET['whmcspage'];
        $and='&';
    }

    $systpl=get_option('cc_whmcs_bridge_template') ? get_option('cc_whmcs_bridge_template') : 'five';
    if (!function_exists('cc_whmcs_bridge_parser_with_permalinks') && !in_array($systpl, array('portal', 'five', 'six'))) {
        $systpl = 'five';
    }
    $vars.=$and.'systpl='.$systpl;
    $and="&";

    if (function_exists('cc_whmcs_bridge_sso_http')) cc_whmcs_bridge_sso_http($vars,$and);

    if ($vars != '') $http.='?'.$vars;

    return $http;
}

function cc_whmcs_bridge_title($title,$id=0) {
    global $cc_whmcs_bridge_content;


    if (!in_the_loop()) {
        return $title;
    }
    //if ($id == 0) {
    //  return $title;
    //}

    if (!isset($cc_whmcs_bridge_content) || !$cc_whmcs_bridge_content) {
        $cc_whmcs_bridge_content = cc_whmcs_bridge_parser();
    }

    #if (isset($cc_whmcs_bridge_content['page_title']) && $cc_whmcs_bridge_content['page_title'] != '') {
    #    $p_title = explode('-', $cc_whmcs_bridge_content['page_title']);
    #    $title = trim($p_title[0]);
    #}

    return $title;
}

function cc_whmcs_bridge_default_page($pid) {
    $isPage=false;
    $ids=get_option("cc_whmcs_bridge_pages");
    $ida=explode(",",$ids);
    foreach ($ida as $id) {
        if (!empty($id) && $pid==$id) $isPage=true;
    }
    return $isPage;
}

function cc_whmcs_bridge_mainpage() {
    $ids=get_option("cc_whmcs_bridge_pages");
    $ida=explode(",",$ids);
    return $ida[0];
}

function cc_whmcs_bridge_init() {
    ob_start();
    if (function_exists('cc_whmcsbridge_sso_session')) cc_whmcsbridge_sso_session();
    if (!session_id()) @session_start();
    register_sidebars(1,array('name'=>'WHMCS Top Page Widget Area','id'=>'whmcs-top-page',));
    //register_sidebars(1,array('name'=>'WHMCS Bottom Page Widget Area','id'=>'whmcs-top-page',));
    if(get_option('cc_whmcs_bridge_jquery')=='wp'){
        wp_enqueue_script(array('jquery','jquery-ui','jquery-ui-slider','jquery-ui-button'));
    }
    if (is_admin() && isset($_REQUEST['page']) && ($_REQUEST['page']=='cc-ce-bridge-cp')) {
        wp_enqueue_script(array('jquery-ui-tabs'));
        wp_enqueue_style('jquery-style', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/themes/flick/jquery-ui.css');
    }
}

function cc_whmcs_log($type=0,$msg='',$filename="",$linenum=0) {
    if ($type==0) $type='Debug';
    if (get_option('cc_whmcs_bridge_debug')) {
        if (is_array($msg)) $msg=print_r($msg,true);
        $v=get_option('cc_whmcs_bridge_log');
        if (!is_array($v)) $v=array();
        array_unshift($v,array(microtime(),$type,$msg));
        if (count($v) > 100) array_pop($v);
        update_option('cc_whmcs_bridge_log',$v);
    }
}

function cc_get_cache($flag = '') {
    if ($flag = 'all') {
        $loop = array('', '_js', '_css');
    } else {
        $loop = array('');
    }

    $cache = array();
    foreach ($loop as $cache_flag) {
        $current = unserialize(get_option('cc_whmcs_bridge_cache' . $cache_flag));

        if (!is_array($current))
            $current = array();

        $cache_dir = dirname(__FILE__) . '/cache/';

        // check current cache
        foreach ($current as $url => $val) {
            $expires = explode('_', $val);
            // if expired, delete
            if (time() > $expires[(count($expires) - 1)] || !file_exists($cache_dir . $val)) {
                if (file_exists($cache_dir . $val))
                    unlink($cache_dir . $val);
                unset($current[$url]);
            } else {
                $cache[$url] = $val;
            }
        }
    }

    return $cache;
}

function cc_update_cache($url, $cached_filename) {
    if (substr(strtolower($url), -4) == '.css') {
        $flag = '_css';
    } else if (substr(strtolower($url), -3) == '.js') {
        $flag = '_js';
    } else {
        $flag = '';
    }
    $current = cc_get_cache($flag);
    $current[$url] = $cached_filename;
    update_option('cc_whmcs_bridge_cache'.$flag, serialize($current));

    cc_whmcs_log(0, 'Updating '.$flag.' cache {'.$url.'} {'.$cached_filename.'} - '.print_r($current, true));
}

function cc_whmcs_bridge_url() {
    $url=get_option('cc_whmcs_bridge_url');
    if (substr($url,-1)=='/') $url=substr($url,0,-1);
    return $url;
}

//Kept for compatibility reasons
if (class_exists('bridgeHttpRequest')) {
    class HTTPRequestWHMCS extends bridgeHttpRequest {}
}
