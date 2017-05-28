<?php
include_once ("../../../wp-load.php");
$file = isset($_REQUEST["file"])?$_REQUEST["file"]:"";
if ($file=="") $file = isset($_REQUEST["url"])?$_REQUEST["url"]:"";
if ($file=="") $file = isset($_REQUEST["whmp_url"])?$_REQUEST["whmp_url"]:"";

if (isset($_GET["scheme"]) && (substr($file,0,4)<>"http")) {
    $file = $_GET["scheme"] . "://" . $file;
}

$type = strtolower(@pathinfo($file, PATHINFO_EXTENSION));

# If cache is enabled in admin panel the it will serve cached file, if cached file exists.
if (function_exists("whmpress_get_option")) $cache_enabled_whmp = whmpress_get_option('cache_enabled_whmp');
else $cache_enabled_whmp = get_option('cache_enabled_whmp');

$force_get = false;
if ($cache_enabled_whmp=="1" || strtolower($cache_enabled_whmp)=="yes"):
    $cache_path = WHMP_CA_PATH."/cache/".md5($file);
    if (is_file($cache_path) && ($type=="css" || $type=="jpg" || $type=="jpeg" || $type=="gif" || $type=="png")) {
        if ($type=="css") {
            header("Content-Type: text/css");
            $force_get = true;
        } else if ($type=="js") {
            header("Content-Type: application/javascript");
            $force_get = true;
        } elseif ($type=="jpg" || $type=="jpeg" || $type=="gif" || $type=="png") {
            header("Content-Type: image/$type");
            $force_get = true;
        }
        echo file_get_contents($cache_path);
        exit;
    }
endif;

$WHMP = new WHMPress_Client_Area();
$result = $WHMP->serve_files($file, $_POST, $_GET, $force_get);
if (isset($result["headers"]["Content-Disposition"])) {
    header("Content-Disposition: ".$result["headers"]["Content-Disposition"]);
}

$output = isset($result["output"])?$result["output"]:"";

if ( isset($result["headers"]["Content-Type"]) && strtolower($result["headers"]["Content-Type"])=="text/html" ) {
    $output = $WHMP->parse_html($output, false, false);
}
# Do not parse fontawesome CSS file.   
if ( strtolower($type)=="css" ) {
    $add=false;
    
    if ( strrpos($file, "templates/bootwhmpress") === false ) $add=true;
    if ( strrpos($file, "font-awesome.min.css") !== false ) $add=false;
    if ( strrpos($file, "font-awesome.css") !== false ) $add=false;
    
    $output = $WHMP->parse_css_file($output, $file, $add);
    $output = $WHMP->minify_css($output);
}

# Parse javascript files.
$donot_parse_js = array(
    "dataTables.responsive.min.js",
    "jquery.dataTables.min.js",
    "dataTables.bootstrap.min.js",
    "bootstrap.min.js",
    "bootstrap.js",
    "jquery-ui.min.js",
    "jquery-ui.js",
);
if ($type=="js" || "application/javascript"==@$result["headers"]["Content-Type"]) {
    if ( !in_array( basename($file),  $donot_parse_js) ) {
        $output = $WHMP->parse_for_js($output);
    }
}

# Setting Mime type for captcha image.
/*if ((basename($file))=="viewinvoice.php" || basename($file)=="viewemail.php") {
    $output = $WHMP->parse_html($output, false, false );
} elseif ((basename($file))=="cart.php" || (basename($file))=="serverstatus.php") {
    $output = $WHMP->parse_html($output, false, false );
}*/

# If cache is enabled from admin panel then it will save cache file.
if (get_option('cache_enabled_whmp')=="1" || strtolower(get_option('cache_enabled_whmp'))=="yes"):
    @file_put_contents($cache_path, $output);
endif;

//@ob_start("ob_gzhandler");

$output = str_replace("cart.php?a=view", $WHMP->set_url($WHMP->get_current_url(false), "cart.php?a=view"), $output);

if (isset($result["headers"]["Content-Type"])) {
    header("Content-Type: ".$result["headers"]["Content-Type"]);
}
echo $output;
die;