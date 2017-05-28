<?php
extract( shortcode_atts( array(
    'user_field' => 'View Cart',
), $args[0] ) );

$allowed_fields = array(
    "firstname", "lastname", "address1", "address2", "city", "companyname",
    "country", "phonenumber", "email", "postcode", "state", "currency", "groupid",
    "language", "lastlogin", "status"
);

if (!in_array($user_field, $allowed_fields)) return "";

$WHMP = new WHMPress_Client_Area;
if ($user_field<>"") {
    
    $url = $WHMP->whmp_http();
    $base = basename($url);
    if ( strpos($base, "index?")!==false )
        $url = str_replace("index?", "index.php?", $url);
    $url .= "&whmcs_info=user_".$user_field;
    
    $result = $WHMP->read_remote_url($url);
    if ( substr($result, 0, 6)=="**__**" ) {
        echo substr($result, 6);
    }
} else echo "";