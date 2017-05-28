<?php

extract( shortcode_atts( array(
    'page' => 'index',
    'return' => 'url',
), $args[0] ) );

$return = strtolower($return);

$Links = array(
    "index"=>"Home",
    "cart"=>"View Cart",
    "announcements"=>"Announcements",
    "knowledgebase"=>"Knowledge Base",
    "serverstatus"=>"Server Status",
    "contact"=>"Contact Page",
    "submitticket"=>"Submit Ticket",
    "clientarea"=>"Client Area",
    "register"=>"Register Account",
    "pwreset"=>"Forget Password"
);
if (isset($Links[$page])) $link_text = $Links[$page];
else $link_text = "View";

$url = get_option('client_area_page_url');
if (is_numeric($url)) $url = get_page_link($url);
$return_url = $url;
$return_url = rtrim($return_url, "/");

$whmp = new WHMPress_Client_Area;

if ( $whmp->is_permalink() ) {
    $return_url .= "/$page/";
    if ($page=="cart") $return_url .= "a/view/";
} else {
    $q = @parse_url($return_url, PHP_URL_QUERY);
    if ($q=="" || is_null($q) || $q===false) {
        $return_url .= "?whmpca=".$page;
    } else {
        $return_url .= "&whmpca=".$page;
    }
    if ($page=="cart") $return_url .= "&a=view";
}

if ($return<>"link") echo $return_url;
else {
    echo "<a href='$return_url'>".$link_text."</a>";
}