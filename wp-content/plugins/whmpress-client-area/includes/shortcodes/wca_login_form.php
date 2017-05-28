<?php
$shortcode_name = $args[2];
$args = $args[0];

$args = shortcode_atts( [
    'button_class' => '',
    'button_text' => "Login",
    'redirect_to' => '',
    'html_id' => '',
    'html_class' => 'whmpress wca_login_form',
    'html_template' => '',
], $args);
extract($args);

$WHMP = new WHMPress_Client_Area;

/*$blog_url = get_option("client_area_page_url");
if (is_numeric($blog_url)) $blog_url = get_page_link($blog_url);
else {
    if (substr($blog_url, 0, 4) != "http") $blog_url = get_bloginfo("url") . "/" . $blog_url;
}
$blog_url = rtrim($blog_url, "/");

if ($this->is_permalink()) {
    $url = $blog_url . "/dologin";
} else {
    $params = parse_url($blog_url);
    if (isset($params["query"]))
        $url = $blog_url . "&whmpca=dologin";
    else
        $url = $blog_url . "?whmpca=dologin";
}*/

$ID = !empty($html_id) ? "id='$html_id'" : "";
$CLASS = !empty($html_class) ? "class='$html_class'" : "";
$rand = wp_rand(1001, 9999);
if (empty($button_text)) $button_text = "Login";

$html_template = $WHMP->get_template_file($html_template, $shortcode_name);
$form_id = "Form_$rand";
$html_form = "
<div $CLASS $ID>
    <div class='wca_loading'>Loading&#8230;</div>
    <form method=\"post\" id='form_{$rand}' onsubmit='WCA_FORM_({$rand}); return false;'>
        <input type='hidden' name='action' value='wca_login'>
        <input type='hidden' name='redirect_to' value=\"{$redirect_to}\">
        <div class='wca_useranme'>
            <label>" . __("Email address", 'whmpress') . "</label>
            <input placeholder=\"" . __("Email address", 'whmpress') . "\" type=\"text\" name=\"username\">
        </div>
        <div class='wca_password'>
            <label>" . __("Password", 'whmpress') . "</label>
            <input placeholder=\"" . __("Password", 'whmpress') . "\" type=\"password\" name=\"password\">
        </div>
        <div class='wca_login_spinner'></div>
        <button class=\"wca_login_btn {$button_class}\">" . __($button_text, "whmpress") . "</button>
    </form>
</div>";

if (is_file($html_template)) {
    $vars = array(
        "action_url" => $url,
        "button_class" => $button_class,
        "button_text" => $button_text,
        "redirect_to_url" => $blog_url,
        "html_id" => $ID,
        "html_class" => $CLASS,
        "html_form" => $html_form,
        "random_id" => $rand
    );

    $TemplateArray = $WHMP->get_template_array($shortcode_name);
    foreach ($TemplateArray as $custom_field) {
        $vars[$custom_field] = isset($atts[$custom_field]) ? $atts[$custom_field] : "";
    }
    echo $WHMP->smarty_template($html_template, $vars);
} else {
    echo $html_form;
} ?>