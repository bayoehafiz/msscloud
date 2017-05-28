<?php
if (!defined('WHMP_CA_PATH')) {
    echo "Sorry, you can't access this page.";
    exit;
}
if (!isset($_POST['do'])) {
    echo "Sorry, It seems it is invalid ajax call.";
    exit;
}

switch ($_POST['do']) {
    case "authenticate":
        if (empty($_POST['user']) || empty($_POST['pass'])) {
            echo __("Please provide username and password", "whmpress");
            exit;
        }
        $W = new WHMPress_Client_Area();
        echo $W->is_admin_user_valid($_POST['user'], $_POST['pass']);
        break;
    default:
        echo "Invalid ajax call!";
        exit();
}