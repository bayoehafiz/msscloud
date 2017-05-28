<?php
/*
 * This file executes ajax requests for Client Area
 */
if (!isset($_GET['file'])) {
    echo "Requested file missing!";
    exit;
}

## Initializing WordPress library
include_once("../../../wp-load.php");

$WHMP = new WHMPress_Client_Area();
$file = $_GET['file'];
unset($_GET['file']);
$url = $WHMP->get_whmcs_url() . $file . ".php";
if (count($_GET) > 0) {
    $url .= "?" . http_build_query($_GET);
}
$_POST['whmp_id'] = $WHMP->get_ip();
$html = $WHMP->read_remote_url($url, $_POST, $_FILES);
if ( !empty($html)>0 && !$WHMP->is_json($html) ) {
    $html = $WHMP->parse_html($html);
}
## If ajax returns JSON data then return with JSON header.
if ($WHMP->is_json($html)) {
    header('Content-type: application/json');
}
echo $html;