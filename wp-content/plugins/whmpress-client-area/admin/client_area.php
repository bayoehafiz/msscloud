<?php
/**
 * @package Admin
 * @todo    Clienat Area Addon page for WHMpress admin panel
 */

global $wpdb;
global $whmp_options;
$whmp_ca = new WHMPress_Client_Area();
add_thickbox();

$settings_file = str_replace("\\", "/", get_stylesheet_directory()) . "/whmpress/" . basename(get_stylesheet_directory()) . ".ini";
if (!is_file($settings_file)) {
    $settings_file = str_replace("\\", "/", WHMP_CA_PATH) . "/themes/" . basename(get_stylesheet_directory()) . ".ini";
}
?>

<style>
    .tr2 {
        display: none;
    }

    .tr1 {

    }
</style>

<div class="full_page_loader">
    <div class="whmp_loader"><?php _e("Loading", "whmpress") ?>...</div>
</div>

<div class="wrap"><?php
	if (is_file($settings_file)) {
        $Data = parse_ini_file($settings_file, true);
        $theme = wp_get_theme();
        ?>
        <div class="updated">
            <form method="post" action="<?php echo WHMP_CA_URL; ?>/includes/apply_settings.php" name="whmp_form">
                <input type="hidden" name="import_settings">
                <input type="hidden" name="file" value="<?php echo $settings_file ?>">
                <p>
                    <?php
                    $current_theme = "(<b>" . $theme->Name . "</b>)";
                    printf(__('This plugin comes pre-configured for your current theme %1s.
            The look and feel of WHMCS client area have been adjusted to match %2s.', 'whmpress'), $current_theme, $current_theme);
                    echo "<br>";
                    printf(__('To further adjust the settings click the button(s) below.', 'whmpress'));
                    ?>
                    <br><br>
                    <button class="button" onclick="ImportSettings('')">
                        <i><?php _e('Adjust All Settings', 'whmpress'); ?></i></button>
                    <?php if (is_array($Data)) foreach ($Data as $k => $v): ?>
                        <button class="button button-primary"
                                onclick='ImportSettings("<?php echo $k ?>")'><?php echo $k ?></button>
                    <?php endforeach; ?>
                </p>
            </form>
        </div>
	<script>
            function ImportSettings(Section) {
                jQuery("input[name=import_settings]").val(Section);
                document.whmp_form.submit();
            }
        </script><?php
	} ?>

	<h2 class="whmp-main-title"><span
            class="whmp-title">WHMpress</span> <?php _e("Client Area Settings", "whmpress") ?></h2><?php

	if ($whmp_ca->is_whmpress_activated()) {
        $WHMPress = new WHMPress();
        $version = $whmp_ca->get_whmrepss_version();
        if (version_compare($version, "2.9.5", "<")) {
            echo "<div class='error'><p>Your <b>WHMPress</b> is not updated, Please update your <b>WHMPress 2.9.5</b> to or later</p></div>";
        }
    }
    $lang = $whmp_ca->get_current_language();
    $v = get_option("whmp_langs");

    if (empty($v[$lang])) {
        $link = rtrim(get_admin_url(), "/") . "/admin.php?page=whmp_client_area";
        echo '
            <div class="error">
                <p>' . __("No <b>WHMCS client area page</b> is selected. Click <a href='$link'>here</a> to visit <b>Client Area</b> Settings page.", 'whmpress') . '</p>
            </div>';
    }

    if ($whmp_ca->is_whmpress_activated() && !$WHMPress->WHMpress_synced()): ?>
    <div class="error">
            <p>
                <b>WHMPress Error</b>
                <?php echo __("WHMCS is not Synced", "whmpress") ?> <a
                    href="admin.php?page=whmp-sync"><?php echo __("Please Sync WHMCS", "whmpress") ?></a>.
            </p>
        </div><?php
    endif;

    if (isset($_GET["settings-updated"]) && $_GET["settings-updated"] == "true") {
        if (function_exists('whmpress_ca_activation')) {
            whmpress_ca_activation();
        }

        echo "<div class='updated'><p><b>Success</b><br />Settings saved.</p></div>";
    }
    ?>

    <form method="post" action="options.php"><?php
	    settings_fields('whmp_whmcs_settings');
        do_settings_sections('whmp_whmcs_settings'); ?>

        <input type="hidden" name="whmp_show_admin_notice1" value="1">
        <div class="settings-wrap">
            <div id="whmp-ca-tabs" class="tab-container">
                <ul class='etabs'>
                    <li class='tab'><a href='#general'><?php _e("General", "whmpress") ?></a></li>
                    <li class='tab'><a href="#advanced"><?php _e("Advanced", "whmpress") ?></a></li>
                    <li class='tab'><a href="#seo"><?php _e("SEO", "whmpress") ?></a></li>
                    <li class='tab'><a href="#sync"><?php _e("Sync WHMCS-WP", "whmpress") ?></a></li>
                    <li class='tab'><a href="#sso"><?php _e("SSO", "whmpress") ?></a></li>
                    <li class='tab'><a href="#debug"><?php _e("Debug Info", "whmpress") ?></a></li>
                </ul>

                <div id="general"><?php
	                $whmp_langs = get_option('whmp_langs');
                    if (is_array($whmp_langs)) {
                        foreach($whmp_langs as $kl=>$llang) {
                            if ($kl<>$whmp_ca->get_current_language()) {
                                echo "<input type='hidden' name='whmp_langs[{$kl}]' value='{$llang}'>\n";
                            }
                        }
                    } ?>
                    <table class="form-table">
                        <?php if (!$whmp_ca->is_whmpress_activated()) { ?>
                            <tr valign="top">
                                <th scope="row" style="width:30%;">
                                    <?php echo __("WHMCS URL", "whmpress"); ?>
                                </th>
                                <td>
                                    <input required="required" type="url" name="whmcs_main_url"
                                           placeholder="<?php echo __("WHMCS URL", "whmpress") ?>"
                                           value="<?php echo esc_attr($whmp_ca->get_whmcs_url()); ?>"
                                           style="width: 100%;">
                                    <p class="description"
                                       id="tagline-whmcs_url"><?php _e("Where is your WHMCS installation exists?", "whmpress"); ?></p>
                                </td>
                            </tr>
                        <?php } else { ?>
                            <tr valign="top">
                                <th scope="row">
                                    <label
                                        for="whmcs_main_url">
                                        <?php echo __("WHMCS URL", "whmpress"); ?> (<b
                                            style="color:#c00"><?php _e("This option is controlled by WHMPress", "whmpress"); ?></b>)
                                    </label>
                                </th>
                                <td>
                                    <input readonly="readonly" type="url" name="whmcs_main_url" id="whmcs_main_url"
                                           placeholder="<?php echo __("WHMCS URL", "whmpress") ?>"
                                           value="<?php echo esc_attr($whmp_ca->get_whmcs_url()); ?>"
                                           style="width: 100%;">
                                    <p class="description"
                                       id="tagline-whmcs_main_url"><?php _e("Where is your WHMCS installation exists?", "whmpress"); ?></p>
                                </td>
                            </tr>
                        <?php } ?>
                        
                        <?php if ($whmp_ca->get_current_language()<>"all") { ?>
                        <tr>
                            <th scope="row" style="width:30%"><label
                                    for="client_area_page_url"><?php _e("WP Page for WHMCS Client Area URL (" . $whmp_ca->get_current_language() . ")", "whmpress"); ?></label>
                            </th>
                            <td>
                                <?php $pages = $whmp_ca->get_all_pages(); ?>
                                <select name="whmp_langs[<?php echo $whmp_ca->get_current_language() ?>]"
                                        id="client_area_page_url">
                                    <option value="">-- <?php echo esc_attr(__('Select page', 'whmpress')); ?>--
                                    </option>
                                    <?php
                                    $name = "whmp_langs[" . $whmp_ca->get_current_language() . "]";
                                    $v = get_option("whmp_langs");

                                    if (isset($v[$whmp_ca->get_current_language()])) {
                                        $v = $v[$whmp_ca->get_current_language()];
                                    } else {
                                        $v = get_option("client_area_page_url");
                                    }
                                    foreach ($pages as $page) {
                                        if (is_numeric($v)) {
                                            if ($v == $page["ID"]) {
                                                $S = "selected=selected";
                                            } else $S = "";
                                        } else {
                                            if (get_post_status($page["ID"])!==false) {
                                                if ($v == get_page_link($page["ID"])) {
                                                    $S = "selected=selected";
                                                } else $S = "";
                                            } else $S = "";
                                        }
                                        $option = '<option ' . $S . ' value="' . $page["ID"] . '">';
                                        $option .= $page["post_title"] . " (" . $page["ID"] . ")";
                                        $option .= '</option>' . "\n";
                                        echo $option;
                                    } ?>
                                </select>
                                <?php
                                if ($v <> "" || !is_null($v)) {
                                    echo "&nbsp;<a target='_blank' href='";
                                    if (is_numeric($v)) {
                                        if (get_post_status($v)!==false) {
                                            echo get_page_link($v);
                                        } else {
                                            echo $v;
                                        }
                                    } else {
                                        echo $v;
                                    }
                                    echo "'>Visit saved page</a>";
                                }
                                ?>
                                <p class="description"
                                   id="tagline-whmp_langs"><?php _e("Where you have placed [whmpress_client_area] shortcode", "whmpress"); ?></p>
                            </td>
                        </tr>
                        <?php } ?>
                        <tr>
                            <th scope="row"><label
                                    for="remove_whmcs_logo"><?php _e("Hide Logo", "whmpress"); ?></label>
                            </th>
                            <td>
                                <select name="remove_whmcs_logo" type="text" id="remove_whmcs_logo">
                                    <option value="no">No</option>
                                    <option
                                        value="yes" <?php echo (get_option("remove_whmcs_logo") == "1" || get_option("remove_whmcs_logo") == "yes") ? "selected" : ""; ?>>
                                        Yes
                                    </option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label
                                    for="whmp_remove_top_bar"><?php _e("Hide Top bar", "whmpress"); ?></label>
                            </th>
                            <td>
                                <select name="whmp_remove_top_bar" type="text" id="whmp_remove_top_bar">
                                    <option value="no">No</option>
                                    <option
                                        value="yes" <?php echo (get_option("whmp_remove_top_bar") == "1" || get_option("whmp_remove_top_bar") == "yes") ? "selected" : ""; ?>>
                                        Yes
                                    </option>
                                </select>
                                <p class="description"
                                   id=""><?php _e("It will also hide logo", "whmpress"); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label
                                    for="remove_whmcs_menu"><?php _e("Hide WHMCS Menu", "whmpress"); ?></label>
                            </th>
                            <td>
                                <select name="remove_whmcs_menu" type="text" id="remove_whmcs_menu">
                                    <option value="no">No</option>
                                    <option
                                        value="yes" <?php echo (get_option("remove_whmcs_menu") == "1" || get_option("remove_whmcs_menu") == "yes") ? "selected" : ""; ?>>
                                        Yes
                                    </option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label
                                    for="remove_breadcrumb"><?php _e("Hide Breadcrumb", "whmpress"); ?></label>
                            </th>
                            <td>
                                <select name="remove_breadcrumb" type="text" id="remove_breadcrumb">
                                    <option value="no">No</option>
                                    <option
                                        value="yes" <?php echo (get_option("remove_breadcrumb") == "1" || get_option("remove_breadcrumb") == "yes") ? "selected" : ""; ?>>
                                        Yes
                                    </option>
                                </select>
                            </td>
                        </tr>
                        <!--<tr>
                            <th scope="row"><label
                                    for="remove_powered_by"><?php /*_e("Remove Powered by WHMCS link", "whmpress"); */ ?></label>
                            </th>
                            <td>
                                <select name="remove_powered_by" type="text" id="remove_powered_by">
                                    <option value="no">No</option>
                                    <option
                                        value="yes" <?php /*echo (get_option("remove_powered_by") == "1" || get_option("remove_powered_by") == "yes") ? "selected" : ""; */ ?>>
                                        Yes
                                    </option>
                                </select>
                            </td>
                        </tr>-->
                        <tr>
                            <th scope="row"><label
                                    for="whmp_hide_currency_select"><?php _e("Hide WHMCS Currency", "whmpress"); ?></label>
                            </th>
                            <td>
                                <select name="whmp_hide_currency_select" type="text" id="whmp_hide_currency_select">
                                    <option value="no">No</option>
                                    <option
                                        value="yes" <?php echo (get_option("whmp_hide_currency_select") == "1" || get_option("whmp_hide_currency_select") == "yes") ? "selected" : ""; ?>>
                                        Yes
                                    </option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label
                                    for="whmpca_custom_css"><?php _e("Custom CSS", "whmpress"); ?></label>
                            </th>
                            <td>
                                <textarea style="width:100%;height:300px" name="whmpca_custom_css" type="text"
                                          id="whmpca_custom_css"><?php echo esc_attr(get_option("whmpca_custom_css")) ?></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td><?php submit_button(); ?></td>
                        </tr>
                    </table>
                </div>
                <div id="advanced">
                    <table class="form-table">
                        <?php foreach ($whmp_options[1] as $key => $ar): ?>

                            <tr valign="top">
                                <th scope="row" style="width:30%;"><?php _e($ar["label"], "whmpress") ?></th>
                                <td>
                                    <?php switch ($ar["type"]) {
                                        case "pages":
                                            ?><select name="<?php echo $key ?>">
                                            <option
                                                value=""><?php echo esc_attr(__('Select page', 'whmpress')); ?></option>
                                            <?php
                                            $pages = get_pages();
                                            foreach ($pages as $page) {
                                                if ( get_post_status($page->ID)!==false ) {
                                                    $S = (esc_attr(get_option($key)) == get_page_link($page->ID)) ? "selected=selected" : "";
                                                } else {
                                                    $S = "";
                                                }
                                                if ( get_post_status($page->ID)!==false ) {
                                                    $option = '<option ' . $S . ' value="' . get_page_link($page->ID) . '">';
                                                } else {
                                                    $option = '<option ' . $S . ' value="">';
                                                }
                                                $option .= $page->post_title . " (" . $page->ID . ")";
                                                $option .= '</option>' . "\n";
                                                echo $option;
                                            } ?>
                                            </select><?php
                                            break;
                                        case "textarea":
                                            ?><textarea style="width: 100%;" rows="10"
                                                        name="<?php echo $key ?>"><?php echo esc_attr(get_option($key)) ?></textarea><?php
                                            break;
                                        case "noyes":
                                            ?><select name="<?php echo $key ?>">
                                            <option value="no"><?php _e("No", "whmpress") ?></option>
                                            <option
                                                value="yes" <?php echo strtolower(get_option($key)) == "yes" ? "selected=selected" : "" ?>><?php _e("Yes", "whmpress") ?></option>
                                            </select> <?php if (isset($ar["later_message"])) echo $ar["later_message"];
                                            if (isset($ar["helper"])) {
                                                echo "<div style='padding:5px;color:#cc0000'>" . $ar["helper"] . "</div>";
                                            }
                                            break;
                                        case "yesno":
                                            ?><select name="<?php echo $key ?>">
                                            <option value="yes"><?php _e("Yes", "whmpress") ?></option>
                                            <option
                                                value="no" <?php echo strtolower(get_option($key)) == "no" ? "selected=selected" : "" ?>><?php _e("No", "whmpress") ?></option>
                                            </select> <?php if (isset($ar["later_message"])) echo $ar["later_message"];
                                            break;
                                        case "select":
                                            ?><select name="<?php echo $key ?>">
                                            <?php foreach ($ar["data"] as $k => $v) { ?>
                                            <option <?php echo strtolower(get_option($key)) == $k ? "selected=selected" : "" ?>
                                                value="<?php echo $k ?>"><?php echo $v ?></option>
                                        <?php } ?>
                                            </select> <?php if (isset($ar["later_message"])) echo $ar["later_message"];
                                            break;
                                        case "number":
                                            ?><input min="10" type="number"
                                                     placeholder="<?php _e($ar["label"], "whmpress") ?>"
                                                     style="width: 100%;" name="<?php echo $key ?>"
                                                     value="<?php echo esc_attr(get_option($key)) ?>"/><?php
                                            break;
                                        case "text":
                                            ?><input type="text"
                                            <?php if (isset($ar["no_placeholder"]) && $ar["no_placeholder"] <> "1"): ?>
                                            placeholder="<?php _e($ar["label"], "whmpress") ?>"
                                        <?php endif; ?> style="width: 100%;" name="<?php echo $key ?>"
                                                     value="<?php echo esc_attr(get_option($key)) ?>"/><?php
                                            break;
                                    } ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <?php
                        $theme_wca_folder = get_template_directory() . "/WHMpress_Client_Area/";
                        $disable1 = false;
                        if (!is_dir($theme_wca_folder)) {
                            $disable1 = true;
                        } else {
                            if ($whmp_ca->count_folders($theme_wca_folder) == 0)
                                $disable1 = true;
                        }
                        $path = basename(get_template_directory());
                        $path = WHMP_CA_PATH . "themes/" . $path;
                        $disable2 = false;
                        if (!is_dir($path)) $disable2 = true;

                        $message1 = basename(get_template_directory()) . __(' Templates by Theme Author', 'whmpress');
                        if ($disable1) $message1 .= " " . __("(Not found)", "whmpress");

                        $message2 = basename(get_template_directory()) . __(' Templates by WHMpress', 'whmpress');
                        if ($disable2) $message2 .= " " . __("(Not found)", "whmpress");
                        ?>
                        <tr valign="top">
                            <th scope="row" style="width:30%;"><?php _e("Templates to Use", "whmpress") ?></th>
                            <td>
                                <select name="whmcs_load_sytle_orders">
                                    <option
                                        value=""><?php _e('Generic Templates (Works with any theme)', 'whmpress'); ?></option>
                                    <option <?php echo $disable1 ? 'disabled="disabled"' : ''; ?> <?php echo get_option("whmcs_load_sytle_orders") == "author" ? "selected=selected" : "" ?>
                                        value="author"><?php echo $message1; ?></option>
                                    <option <?php echo $disable2 ? 'disabled="disabled"' : ''; ?> <?php echo get_option("whmcs_load_sytle_orders") == "whmpress" ? "selected=selected" : "" ?>
                                        value="WHMpress_Client_Area"><?php echo $message2; ?></option>
                                </select>
                                <p class="description">
                                    <b>Note:</b> <?php _e("Matching Pricing Tables and other templates for your active theme are available. To use them select appropriate option", "whmpress"); ?>
                                </p>
                                <span style="color: #CC0000;">
                            </span>
                            </td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td><?php submit_button(); ?></td>
                        </tr>
                    </table>
                </div>
                <div id="seo">
                    <table class="form-table">
                        <tr>
                            <td colspan="2"><label><input name="whmp_seo_enable_urls" onchange="ED(this)"
                                                          type="checkbox"
                                                          value="1" <?php echo get_option("whmp_seo_enable_urls") == "1" ? "checked='checked'" : ""; ?>>
                                    Enable Custom Titles for following URLs</label></td>
                        </tr>
                        <tr>
                            <th>URL</th>
                            <th>Title</th>
                        </tr>
                        <?php
                        $enabled = get_option("whmp_seo_enable_urls") == "1" ? true : false;
                        $main_url = $whmp_ca->get_client_area_page();
                        global $whmp_seo_urls;
                        foreach ($whmp_seo_urls as $file):
                            $url = $whmp_ca->set_url($main_url, $file);
                            $dval = ucwords($file);
                            if ($dval == "Serverstatus") $dval = __('Server Status', 'whmpress');
                            else if ($dval == "Domainchecker") $dval = __('Domain Checker', 'whmpress');
                            else if ($dval == "Submitticket") $dval = __('Submit Ticket', 'whmpres');
                            else if ($dval == "Clientarea") $dval = __('Client Area', 'whmpress');
                            else if ($dval == "Pwreset") $dval = __('Lost Password Reset', 'whmpress');
                            ?>
                            <tr valign="top">
                                <td scope="row"><a href="<?php echo $url ?>" target="_blank"><?php echo $url ?></a>
                                </td>
                                <td>
                                    <input class="url_title" <?php echo $enabled ? "" : "readonly='readonly'" ?>
                                           type="text" placeholder="" style="width: 100%;"
                                           name="whmp_seo_<?php echo $file ?>"
                                           value="<?php echo esc_attr(get_option("whmp_seo_" . $file)) == "" ? $dval : esc_attr(get_option("whmp_seo_" . $file)); ?>">
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <tr>
                            <td>&nbsp;</td>
                            <td><?php submit_button(); ?></td>
                        </tr>
                    </table>
                </div>
                <div id="sync">
                    <div class="head"><?php echo __("Sync WHMCS Users", "whmpress"); ?></div>
                    <table class="form-table">
                        <tbody>
                        <tr>
                            <th scope="row"></th>
                            <td>
                                <fieldset>
                                    <legend class="screen-reader-text">
                                        <span><?php _e("Enable sync", "whmpress"); ?></span></legend>
                                    <label for="whmcs_enable_sync">
                                        <input name="whmcs_enable_sync" type="checkbox" id="whmcs_enable_sync"
                                               value="1" <?php echo get_option("whmcs_enable_sync") == "1" ? "checked" : ""; ?>>
                                        <?php _e("Enable WHMCS-WP sync", "whmpress"); ?>
                                    </label>
                                </fieldset>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label
                                    for="blogname"><?php _e("Type URL when user logout", "whmpress"); ?></label></th>
                            <td><input name="whmp_logout_url" type="text" id="whmp_logout_url"
                                       class="regular-text"
                                       value="<?php echo esc_attr(get_option("whmp_logout_url")) ?>"
                                       placeholder="<?php _e("Logout URL", "whmpress"); ?>"></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="header"><?php _e("WHMCS User Info", "whmpress"); ?></td>
                        </tr>
                        <tr>
                            <th scope="row"><label
                                    for="blogname"><?php _e("WHMCS Admin user", "whmpress"); ?></label></th>
                            <td><input name="whmcs_sso_admin_user" type="text" id="whmcs_sso_admin_user"
                                       class="regular-text"
                                       value="<?php echo esc_attr(get_option("whmcs_sso_admin_user")) ?>"
                                       placeholder="<?php _e("WHMCS Admin user", "whmpress"); ?>"></td>
                        </tr>
                        <tr>
                            <th scope="row"><label
                                    for="blogname"><?php _e("WHMCS Admin password", "whmpress"); ?></label></th>
                            <td><input name="whmcs_sso_admin_pass" type="password" id="whmcs_sso_admin_pass"
                                       class="regular-text"
                                       value="<?php echo esc_attr(get_option("whmcs_sso_admin_pass")); ?>"
                                       placeholder="<?php _e("WHMCS Admin password", "whmpress"); ?>">
                                &nbsp;(<a id="test_whmcs" href="#">Test WHMCS Authentication</a>)
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>
                                <div class="whmcs_warning">
                                    <p>Please make sure you have allowed IP of this server in WHMCS > Setup > Security >
                                        <b>API IP</b>
                                        Access Restriction > <b>Add IP</b>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="header"><?php _e("Synchronization Settings", "whmpress"); ?></td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e("Sync. Direction", "whmpress"); ?></th>
                            <td>
                                <fieldset>
                                    <legend class="screen-reader-text">
                                        <span><?php _e("WHMCS to WP", "whmpress"); ?></span></legend>
                                    <label><input type="radio" name="sync_direction" value="1" checked="checked">
                                        <span
                                            class="date-time-text format-i18n"><?php _e("WHMCS to WP", "whmpress"); ?>
                                    </label>
                                    <br>
                                    <label><input type="radio" name="sync_direction"
                                                  value="2" <?php echo get_option("sync_direction") == "2" ? "checked" : ""; ?>>
                                        <span
                                            class="date-time-text format-i18n"><?php _e("WP to WHMCS", "whmpress"); ?></span>
                                    </label><br>
                                    <label><input type="radio" name="sync_direction"
                                                  value="3" <?php echo get_option("sync_direction") == "3" ? "checked" : ""; ?>>
                                        <span
                                            class="date-time-text format-i18n"><?php _e("Both Ways", "whmpress"); ?></span>
                                    </label><br>
                                </fieldset>
                            </td>
                        </tr>
                        <tr class="tr0"
                            style="<?php echo get_option("sync_direction") <> "3" ? "display:none" : ""; ?>">
                            <th scope="row"><label
                                    for="whmcs_both_ways_priority"><?php _e("Priority", "whmpress") ?></label>
                            </th>
                            <td>
                                <legend class="screen-reader-text">
                                    <span><?php _e("Priority", "whmpress"); ?></span></legend>
                                <label><input type="radio" name="whmcs_both_ways_priority"
                                              value="whmcs" checked="checked">
                                    <span
                                        class="date-time-text format-i18n"><?php _e("WHMCS", "whmpress"); ?>
                                </label><br>
                                <label><input type="radio" name="whmcs_both_ways_priority" value="wp"
                                        <?php echo get_option("whmcs_both_ways_priority") == "wp" ? "checked" : ""; ?>>
                                    <span
                                        class="date-time-text format-i18n"><?php _e("WordPress", "whmpress"); ?>
                                </label><br>
                            </td>
                        </tr>
                        <tr class="tr1">
                            <td colspan="2" class="header2"><?php _e("WHMCS to WP", "whmpress"); ?></td>
                        </tr>
                        <tr class="tr1">
                            <th scope="row"><label
                                    for="whmcs_wordpress_role"><?php _e("WordPress Role", "whmpress") ?></label>
                            </th>
                            <td>
                                <select name="whmcs_wordpress_role" id="whmcs_wordpress_role">
                                    <?php wp_dropdown_roles(get_option("whmcs_wordpress_role")); ?>
                                </select>
                            </td>
                        </tr>
                        <tr class="tr1">
                            <th scope="row"><?php _e("Login Name", "whmpress"); ?></th>
                            <td>
                                <fieldset>
                                    <legend class="screen-reader-text">
                                        <span><?php _e("First Name + Last Name", "whmpress"); ?></span></legend>
                                    <label><input type="radio" name="whmcs_sso_login_name_type"
                                                  value="email" checked="checked">
                                        <span
                                            class="date-time-text format-i18n"><?php _e("User email as username (Recommended)", "whmpress"); ?>
                                    </label><br>
                                    <label><input type="radio" name="whmcs_sso_login_name_type" value="fnln"
                                            <?php echo get_option("whmcs_sso_login_name_type") == "fnln" ? "checked" : ""; ?>>
                                        <span
                                            class="date-time-text format-i18n"><?php _e("First Name + Last Name", "whmpress"); ?>
                                    </label><br>
                                </fieldset>
                            </td>
                        </tr>
                        <tr class="tr1">
                            <th scope="row"></th>
                            <td>
                                <div class="whmcs_warning">
                                    <p><?php _e("WordPress has user fields which is mandotary, how will you like to make that user fields.", "whmpress"); ?></p>
                                </div>
                            </td>

                        </tr>
                        <tr class="tr1">
                            <th scope="row"><?php _e("Create WHMCS profile fields in WP", "whmpress"); ?></th>
                            <td>
                                <select name="whmcs_create_wp_fields">
                                    <option value="0">No</option>
                                    <option <?php echo get_option("whmcs_create_wp_fields") == "1" ? "selected" : ""; ?>
                                        value="1">Yes
                                    </option>
                                </select>
                            </td>
                        </tr>
                        <tr class="tr2">
                            <td colspan="2" class="header2"><?php _e("WP to WHMCS", "whmpress"); ?></td>
                        </tr>
                        <tr class="tr2">
                            <th scope="row"><?php _e("How to handle Address & Phone fields", "whmpress"); ?></th>
                            <td>
                                <fieldset>
                                    <legend class="screen-reader-text">
                                    </legend>
                                    <label><input type="radio" name="whmcs_sso_handle_fields" value="dummy"
                                                  checked="checked">
                                        <span
                                            class="date-time-text format-i18n"><?php _e("Fill with dummy data", "whmpress"); ?>
                                    </label><br>
                                    <label><input type="radio" name="whmcs_sso_handle_fields"
                                                  value="disable_in_whmcs" <?php echo get_option("whmcs_sso_handle_fields") == "disable_in_whmcs" ? "checked" : "" ?>>
                                        <span
                                            class="date-time-text format-i18n"><?php _e("I have disable these fields in WHMCS", "whmpress"); ?>
                                    </label><br>
                                    <!--<label><input type="radio" name="whmcs_sso_handle_fields"
                                                      value="map_in_whmcs" <?php /*echo get_option("whmcs_sso_handle_fields") == "map_in_whmcs" ? "checked" : "" */ ?>>
                                            <span
                                                class="date-time-text format-i18n"><?php /*_e("I have matching custom fields that I will map with WHMCS", "whmpress"); */ ?>
                                        </label>
                                        <br>-->
                                </fieldset>
                            </td>
                        </tr>
                        <tr class="tr2">
                            <th scope="row"></th>
                            <td>
                                <div class="whmcs_warning">
                                    <p>
                                        <?php _e("By default WHMCS requires Client Address and Phone Number for user creation, while this WP defaults do not have this information.<br>You can handle this situation in two ways.", "whmpress"); ?>
                                        <br>
                                    <ol>
                                        <li><?php _e("Let Client Area to fill in dummy Address & Phone numbers.", "whmpress"); ?></li>
                                        <li><?php _e("Disable these fields from WHMCS.", "whmpress"); ?></li>
                                        <!--<li><?php /*_e("If you are having these fields in WP custom fields, you can use Advance Settings here.", "whmpress"); */ ?></li>-->
                                    </ol>
                                    </p>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="text-align: center;">
                                <button class="button button-primary">Save Settings</button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div id="sso">
                    <div class="head"><?php echo __("Single Sign-On (SSO) with WHMCS", "whmpress"); ?></div>
                    <table class="form-table">
                        <tbody>
                        <tr>
                            <th scope="row"></th>
                            <td>
                                <fieldset>
                                    <legend class="screen-reader-text">
                                        <span><?php _e("Enable SSO", "whmpress"); ?></span></legend>
                                    <label for="whmcs_enable_sso">
                                        <input name="whmcs_enable_sso" type="checkbox" id="whmcs_enable_sso"
                                               value="1" <?php echo get_option("whmcs_enable_sso") == "1" ? "checked" : ""; ?>>
                                        <?php _e("Enable SSO", "whmpress"); ?>
                                    </label>
                                </fieldset>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"></th>
                            <td>
                                <fieldset>
                                    <legend class="screen-reader-text">
                                        <span><?php _e("Hide WP Admin Bar", "whmpress"); ?></span></legend>
                                    <label for="whmcs_hide_wp_admin_bar">
                                        <input name="whmcs_hide_wp_admin_bar" type="checkbox" id="whmcs_hide_wp_admin_bar"
                                               value="1" <?php echo get_option("whmcs_hide_wp_admin_bar") == "1" ? "checked" : ""; ?>>
                                        <?php _e("Hide WP Admin Bar", "whmpress"); ?>
                                    </label>
                                </fieldset>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="text-align: center;">
                                <button class="button button-primary">Save Settings</button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <!--<div style="text-align: center">
                            <a href="<?php /*echo WHMP_CA_URL */ ?>admin/import_users.php?keepThis=true&TB_iframe=true&width=600&height=550" class="thickbox button button-primary"><?php /*_e("Import Users from WHMCS"); */ ?></a>
                        </div>-->

                </div>
                <div id="debug">
                    <div style="text-align: center;">
                        <input onclick="LoadDebug()" type="button" value="Generate Debug Info"
                               class="button button-primary"/>
                    </div>
                    <br/>
                    <div id="output"></div>
                </div>
            </div>
        </div>
    </form>
</div>

<script type="text/javascript">
    function LoadDebug() {
        jQuery(".full_page_loader").show();
        jQuery("#output").html("<center><?php _e("Loading", "whmpress"); ?> ....</center>");
        jQuery.post(ajaxurl, {action: "whmp_debug"}, function (data) {
            jQuery(".full_page_loader").hide();
            jQuery("#output").html(data);
        });
    }
    jQuery(document).ready(function () {
        jQuery(document).on("click", "#test_whmcs", function (e) {
            e.preventDefault();
            jQuery(".full_page_loader").show();
            jQuery.post(ajaxurl, {
                action: 'wca_admin_ajax',
                do: 'authenticate',
                user: jQuery("#whmcs_sso_admin_user").val(),
                pass: jQuery("#whmcs_sso_admin_pass").val()
            }, function (data) {
                jQuery(".full_page_loader").hide();
                if (data == "OK") {
                    alert("<?php echo __("Congrats! WHMCS username and password is correct", "whmpress"); ?>");
                } else {
                    alert(data);
                }
            });

        });
        jQuery('#whmp-ca-tabs').easytabs();
        jQuery(document).on("change", "input[name=sync_direction]", function () {
            val = jQuery(this).val();
            if (val == 1) {
                jQuery(".tr1").show();
                jQuery(".tr0, .tr2").hide();
            } else if (val == 2) {
                jQuery(".tr2").show();
                jQuery(".tr0, .tr1").hide();
            } else if (val == 3) {
                jQuery(".tr0, .tr1, .tr2").show();
            }
        });
        <?php if (get_option("sync_direction") == "2") { ?>
        jQuery(".tr1").hide();
        jQuery(".tr2").show();
        <?php } else if (get_option("sync_direction") == "3") { ?>
        jQuery(".tr1, .tr2").show();
        <?php } else { ?>
        jQuery(".tr2").hide();
        jQuery(".tr1").show();
        <?php } ?>
    });
    function RemoveCacheFiles() {
        if (!confirm("Are you sure you want to delete cached files?\n\nThis action can't be un done.")) return false;
        jQuery(".full_page_loader").show();
        data = "action=remove_cache_whmp";
        jQuery.post(ajaxurl, data, function (response) {
            if (response.substr(0, 2) == "OK") {
                jQuery("#files").text(response.substr(2));
            } else {
                alert(response);
            }
            jQuery(".full_page_loader").hide();
        });
    }
    function ED(tthis) {
        if (jQuery(tthis).is(":checked")) {
            jQuery(".url_title").removeAttr("readonly");
        } else {
            jQuery(".url_title").attr("readonly", "readonly");
        }
    }
    function Remove(tthis) {
        jQuery(tthis).parent().parent().remove();
    }
</script>