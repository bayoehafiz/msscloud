<?php
/*
 * This file handle wca_login_form's ajax requests.
 */

if ( ! isset( $ajax_request ) ) {
	echo __( "Invalid ajax call", "whmpress" );
	exit;
}
if ( ! isset( $_POST['action'] ) ) {
	echo __( "Invalid ajax call", "whmpress" );
	exit;
}

$sync_direction = get_option( "sync_direction" );
$priority       = get_option( "whmcs_both_ways_priority" );
if ( empty( $priority ) ) {
	$priority = "whmcs";
}
extract( $_POST );

if ( ! isset( $rememberme ) ) {
	$rememberme = false;
}
if ( ! isset( $username ) && isset( $log ) ) {
	$username = $log;
}
if ( ! isset( $password ) && isset( $pwd ) ) {
	$password = $pwd;
}
if ( ! isset( $username ) ) {
	_e( "Username is missing", "whmpress" );
	wp_die();
}
if ( ! isset( $password ) ) {
	_e( "Password is missing", "whmpress" );
	wp_die();
}

## Checking if provided username is an admin user.
## If yes then ignore sync.
if ( $this->is_wp_user( $username ) ) {
	$user = get_user_by( "login", $username );
	if ( ! $user ) {
		$user = get_user_by( "email", $username );
	}

	if ( is_super_admin( $user->ID ) ) {
		$wp_login = $this->wp_login( $username, $password );
		if ( $wp_login == "OK" ) {
			echo json_encode(
				array(
					"action" => "redirect",
					"goto"   => ! empty( $redirect_to ) ? $redirect_to : get_admin_url()
				)
			);
		} else {
			echo $wp_login;
		}
		wp_die();
	}
}

switch ( $_POST['action'] ) {
	case "wca_login":
		if ( get_option( "whmcs_enable_sync" ) <> "1" ) {
			## If Enable WHMCS WP-Sync is not enabled.
			$wp_login = $this->wp_login( $username, $password );
			if ( $wp_login == "OK" ) {
				echo json_encode(
					array(
						"action" => "redirect",
						"goto"   => ! empty( $redirect_to ) ? $redirect_to : get_admin_url()
					)
				);
			} else {
				echo $wp_login;
			}
			wp_die();
		} else if ( $sync_direction == "1" ) {
			$is_whmcs_user = $this->authenticate_whmcs_user( $username, $password );

			if ( $is_whmcs_user == "OK" ) {
				## If WHMCS user is valid
				$is_wp_user = $this->is_wp_user( $username );

				if ( ! $is_wp_user ) {
					## Create WP user.
					$role = get_option( 'whmcs_wordpress_role' );
					if ( empty( $role ) ) {
						$role = 'subscriber';
					}
					$w_user   = $this->get_whmcs_user( $username );
					$userdata = array(
						'user_login'   => $username,
						'user_email'   => $username,
						'user_pass'    => $password,
						'first_name'   => $w_user['firstname'],
						'last_name'    => $w_user['lastname'],
						'display_name' => $w_user['fullname'],
						'description'  => __( "User created by WHMCS Client Area", "whmpress" ),
						'role'         => $role
					);
					$user_id  = wp_insert_user( $userdata );

					if ( ! is_wp_error( $user_id ) ) {
						$this->update_wp_user_metas( $user_id, $w_user );
						$this->start_session();
						$_SESSION['whmcs_wp_password'] = $password;
					}
				}

				## Logging in WP user
				$login = $this->wp_login( $username, $password, $rememberme );
				if ( $login <> "OK" ) {
					$user = get_user_by( "login", $username );
					if ( ! $user ) {
						$user = get_user_by( "email", $username );
					}

					$this->wp_set_password( $password, $user->ID );
				}

				/*$url = $this->whmp_http("dologin");
				$this->read_remote_url($url, array("username" => $username, "password" => $password), array(), false);*/
				$this->whmcs_login( $username, $password );

				$_current_url = $this->get_client_area_page_id();
				if ( is_numeric( $_current_url ) ) {
					$_current_url = get_page_link( $_current_url );
				}
				echo json_encode(
					array(
						"action" => "redirect",
						"goto"   => ! empty( $redirect_to ) ? $redirect_to : $_current_url
					)
				);
			} else {
				echo __( $is_whmcs_user, "whmpress" );
				exit;
			}
		} else if ( $sync_direction == "2" ) {
			$wp_login = $this->wp_login( $username, $password, $rememberme );

			if ( $wp_login <> "OK" ) {
				echo $wp_login;
				exit;
			}

			if ( ! $this->is_whmcs_user( $username ) ) {
				$user = wp_get_current_user();

				## If WHMCS user doesn't exists, then create WHMCS user.
				$this->create_whmcs_user_by_wp( $username, $password );
			} else if ( $this->authenticate_whmcs_user( $username, $password ) <> "OK" ) {
				$this->whmcs_set_password( $username, $password );
			}

			$this->whmcs_login( $username, $password );

			echo json_encode(
				array(
					"action" => "redirect",
					"goto"   => ! empty( $redirect_to ) ? $redirect_to : get_admin_url()
				)
			);
		} else if ( $sync_direction == "3" ) {
			$is_wp_user_exists = $this->is_wp_user( $username );
			if ( $is_wp_user_exists ) {
				$user = get_user_by( "login", $username );
				if ( ! $user ) {
					$user = get_user_by( "email", $username );
				}
			}

			if ( $priority == "whmcs" ) {
				## If priority is WHMCS
				if ( $this->authenticate_whmcs_user( $username, $password ) == "OK" ) {
					## If WHMCS user is authenticated.
					if ( $is_wp_user_exists ) {
						wp_set_password( $password, $user->ID );
					} else {
						$this->create_wp_user_from_whmcs( $username, $password );
					}

					$this->wp_login( $username, $password, $rememberme );
				} else {
					## If WHMCS user is not authenticated and also WP not authenticated.
					$wp_login = $this->wp_login( $username, $password, $rememberme );
					if ( $wp_login <> "OK" ) {
						echo $wp_login;
						wp_die();
					}

					if ( ! $this->is_whmcs_user( $username ) ) {
						## If WHMCS user doesn't exists.!
						$this->create_whmcs_user_by_wp( $username, $password );
					} else {
						## Set WHMCS user password
						$this->whmcs_set_password( $username, $password );
					}
				}
				$this->whmcs_login( $username, $password );

				$_current_url = $this->get_client_area_page_id();
				if ( is_numeric( $_current_url ) ) {
					$_current_url = get_page_link( $_current_url );
				}
				echo json_encode(
					array(
						"action" => "redirect",
						"goto"   => ! empty( $redirect_to ) ? $redirect_to : $_current_url
					)
				);
			} else {
				## If priority is WP
				$wp_login = $this->wp_login( $username, $password, $rememberme );
				if ( $wp_login == "OK" ) {
					if ( ! $this->is_whmcs_user( $username ) ) {
						## If WHMCS user doesn't exists, Create it.
						$this->create_whmcs_user_by_wp( $username, $password );
					} else {
						$this->whmcs_set_password( $username, $password );
					}

					$this->whmcs_login( $username, $password );
				} else {
					$whmcs_login = $this->authenticate_whmcs_user( $username, $password );
					if ( $whmcs_login <> "OK" ) {
						echo $whmcs_login;
						wp_die();
					}

					if ( ! $this->is_wp_user( $username ) ) {
						$this->create_wp_user_from_whmcs( $username, $password );
					} else {
						wp_set_password( $password, $user->ID );
					}

					if ( ! $this->is_whmcs_user( $username ) ) {
						$this->create_whmcs_user_by_wp( $username, $password );
					} else {
						$this->whmcs_set_password( $username, $password );
					}

					$this->wp_login( $username, $password, $rememberme );
					$this->whmcs_login( $username, $password );
				}

				echo json_encode(
					array(
						"action" => "redirect",
						"goto"   => ! empty( $redirect_to ) ? $redirect_to : get_admin_url()
					)
				);
			}
		}
}
wp_die();