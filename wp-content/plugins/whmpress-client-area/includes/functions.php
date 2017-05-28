<?php
if (!function_exists('whmp_get_currency')) {
    function whmp_get_currency($curency_id = "0")
    {
        if (!class_exists('WHMPress')) return "0";
        $curency_id = (int)$curency_id;
        if (empty($curency_id)) {
            $W = new WHMPress_Client_Area();
            $W->start_session();
            if (isset($_SESSION["currency"]) && !empty($_SESSION["currency"])) return $_SESSION["currency"];
            return whmp_get_default_currency_id();
        } else {
            return $curency_id;
        }
    }
}
if (!function_exists('whmp_get_default_currency_id')) {
    function whmp_get_default_currency_id()
    {
        if (!class_exists('WHMPress')) return '';

        $currency = get_option("whmpress_default_currency");
        if (!empty($currency) && is_numeric($currency)) return $currency;

        global $wpdb;
        $Q = "SELECT `id` FROM `" . $wpdb->prefix . "whmpress_currencies` WHERE `default`='1'";
        return $wpdb->get_var($Q);
    }
}