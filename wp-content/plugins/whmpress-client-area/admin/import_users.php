<?php
if (!is_file(realpath("../../../../wp-load.php"))) {
    echo "<h1>" . __("Can't find wordpress library", "whmpress") . "</h1>";
    exit;
}
include_once realpath("../../../../wp-load.php");
if (!user_can(wp_get_current_user(), 'administrator')) {
    echo "<h1>" . __("You'r not logged in as Administrator", "whmpress") . "</h1>";
    exit;
}
set_time_limit(0);
echo '<code>';

$WHMP = new WHMPress_Client_Area;
$r = $wp_hasher->CheckPassword('lahore', '$2y$10$7RxfbnogVtxakcKwE3EXM.qeQxhZ7RKA/gX2nZrKrAeADUhm7uX9m');
//$r = password_verify('lahore', '$2y$10$7RxfbnogVtxakcKwE3EXM.qeQxhZ7RKA/gX2nZrKrAeADUhm7uX9m');
var_dump($r);
exit;

echo "<b>Getting users from " . $WHMP->whmcs_url . "</b><br>";
flush();

$users = $WHMP->get_whmcs_users();
echo $users["totalresults"] . " users found.<br>";
flush();

$role = get_option('whmcs_wordpress_role');
if (empty($role)) $role = 'subscriber';

foreach ($users['clients']['client'] as $user) {
    echo $user['email'] . " > ";
    flush();

    $is_user = get_user_by('email', $user['email']);
    if (!$is_user) {
        $userdata = array(
            'user_login' => $user['email'],
            'user_email' => $user['email'],
            'user_pass' => 'Farash..88',
            'first_name' => $user['firstname'],
            'last_name' => $user['lastname'],
            'display_name' => $user['firstname'] . " " . $user['lastname'],
            'description' => "User created by WHMCS Client Area",
            'user_registered' => $user['datecreated'],
            'role' => $role
        );

        $user_id = wp_insert_user($userdata);
        echo "User created: ";
        if (!is_wp_error($user_id)) {
            echo "<span style='color:#0c0'>OK</span>";
            $pass = $WHMP->get_whmcs_password_hash($user['email']);
            $data = array("user_pass" => $pass);
            $resp = $wpdb->update($wpdb->prefix . "users", $data, array("ID" => $user_id));
            if (!$resp) {
                echo " > <span style='color:#c00'>Password Not Updated</span>";
            } else {
                echo " > <span style='color:#0c0'>Password Updated</span>";
            }
        } else {
            echo "<span style='color:#c00'>Not Created</span>";
        }
    } else {
        echo "<span style='color:#c00'>User already exists!</span>";
    }
    echo "<br>";
    flush();
}