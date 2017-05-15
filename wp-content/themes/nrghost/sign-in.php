<?php
/**
 * Template Name: Sign In
 *
 * @package nrghost
 * @since 1.0.0
 *
 */

global $nrghost_opt;
$login_page_id = ( is_object( $nrghost_opt ) && $nrghost_opt->get_option( 'login-page' ) ) ? $nrghost_opt->get_option( 'login-page' ) : false;

get_header(); ?>

<div class="container-above-header"></div>
<div class="blocks-container">
	<div class="block login-register-page">
		<div class="container">
			<div class="row">

				<!-- ============LOGIN FORM START============= -->
				<?php

				$login = ( isset( $_GET['login'] ) ) ? $_GET['login'] : 0;

				if ( $login === "failed" ) {
					print '<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' . esc_html__( 'ERROR:', 'nrghost' ) . '</strong> ' . esc_html__( 'Invalid username and/or password.', 'nrghost' ) . '</div>';
				} elseif ( $login === "empty" ) {
					print '<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong>' . esc_html__( 'ERROR:', 'nrghost' ) . '</strong> ' . esc_html__( 'Username and/or Password is empty.', 'nrghost' ) . '</div>';
				}

				if ( isset( $_GET['recovery'] ) && $_GET['recovery'] == 'success' ) {
					print '<p class="login-msg rec-success"><strong>' . esc_html__( 'RECOVERY SUCCESSFULL:', 'nrghost' ) . '</strong> ' . esc_html__( 'Check your e-mail for the confirmation link.', 'nrghost' ) . '</div>';
				}

				if ( isset( $_GET['action'] ) && $_GET['action'] === 'register' ) {
					$register_error  = ( isset($_GET['reg_error'] ) ) ? $_GET['reg_error'] : 0;
					if ( $register_error === "username_exists" ) {
						print '<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong>' . esc_html__( 'REGISTRATION ERROR:', 'nrghost' ) . '</strong> ' . esc_html__( 'Username is already exists.', 'nrghost' ) . '</div>';
					} elseif ( $register_error === "username_invalid" ) {
						print '<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong>' . esc_html__( 'REGISTRATION ERROR:', 'nrghost' ) . '</strong> ' . esc_html__( 'Invalid username.', 'nrghost' ) . '</div>';
					} elseif ( $register_error === "username_empty" ) {
						print '<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong>' . esc_html__( 'REGISTRATION ERROR:', 'nrghost' ) . '</strong> ' . esc_html__( 'Username is empty.', 'nrghost' ) . '</div>';
					} elseif ( $register_error === "email_invalid" ) {
						print '<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong>' . esc_html__( 'REGISTRATION ERROR:', 'nrghost' ) . '</strong> ' . esc_html__( 'Invalid email.', 'nrghost' ) . '</div>';
					} elseif ( $register_error === "email_exists" ) {
						print '<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong>' . esc_html__( 'REGISTRATION ERROR:', 'nrghost' ) . '</strong> ' . esc_html__( 'Email is already exists.', 'nrghost' ) . '</div>';
					} elseif ( $register_error === "password_empty" ) {
						print '<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong>' . esc_html__( 'REGISTRATION ERROR:', 'nrghost' ) . '</strong> ' . esc_html__( 'Password is empty.', 'nrghost' ) . '</div>';
					} elseif ( $register_error === "password_mismatch" ) {
						print '<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong>' . esc_html__( 'REGISTRATION ERROR:', 'nrghost' ) . '</strong> ' . esc_html__( 'Password mismatch.', 'nrghost' ) . '</div>';
					}
				} elseif ( isset( $_GET['lostpassword'] ) && $_GET['lostpassword'] === 'true' ) {
					$lost_error  = ( isset($_GET['lost_error'] ) ) ? $_GET['lost_error'] : 0;
					if ( $lost_error === 'noemail' ) {
						print '<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong>' . esc_html__( 'RECOVERY PASSWORD ERROR:', 'nrghost' ) . '</strong> ' . esc_html__( 'Email is not found.', 'nrghost' ) . '</div>';
					} elseif ( $lost_error === 'nouser' ) {
						print '<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong>' . esc_html__( 'RECOVERY PASSWORD ERROR:', 'nrghost' ) . '</strong> ' . esc_html__( 'User is not found.', 'nrghost' ) . '</div>';
					} elseif ( $lost_error === 'emptyname' ) {
						print '<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong>' . esc_html__( 'RECOVERY PASSWORD ERROR:', 'nrghost' ) . '</strong> ' . esc_html__( 'Field "User" is empty.', 'nrghost' ) . '</div>';
					}
				}
				?>

				<?php if ( !is_user_logged_in() ) { ?>
					<?php if ( isset( $_GET['action'] ) && $_GET['action'] == 'register' ) { // REGISTER FORM ?>
					<div class="col-md-6 col-md-offset-3 wow fadeInUp">
						<?php 
						$color = '';
						if (is_object( $nrghost_opt ) && $nrghost_opt->get_option( 'register-form-bg-color' )) {
							$color = ' style="background-color: '.$nrghost_opt->get_option( 'register-form-bg-color' ).';';
						} 
						?>
						<div class="form-block"<?php print $color; ?>>

							<?php 
							if (is_object( $nrghost_opt ) && $nrghost_opt->get_option( 'register-form-icon' )) {
								$icon_form = $nrghost_opt->get_option( 'register-form-icon' );
							} else {
								$icon_form = get_template_directory_uri() . '/assets/img/icon-119.png';
							}
							?>
							<img class="img-circle form-icon" src="<?php print esc_attr($icon_form ); ?>" alt="" />

							<div class="form-wrapper">
								<div class="row">
									<div class="block-header">
										<?php if ( is_object( $nrghost_opt ) && $nrghost_opt->get_option( 'register-form-title' ) ) { ?><h2 class="title"><?php print $nrghost_opt->get_option( 'register-form-title' ); ?></h2><?php } ?>
										<?php if ( is_object( $nrghost_opt ) && $nrghost_opt->get_option( 'register-form-description' ) ) { ?><div class="text"><?php print $nrghost_opt->get_option( 'register-form-description' ); ?></div><?php } ?>
									</div>
								</div>
								<form name="registerform" action="<?php echo esc_url( get_permalink( $post->ID ) ); ?>" id="registerform" method="post">
									<div class="field-entry">
										<label for="user"><?php esc_html_e('Login *','nrghost'); ?></label>
										<input type="text" name="reg_log" required id="user" value="" />
									</div>
									<div class="field-entry">
										<label for="email"><?php esc_html_e('Email Address *','nrghost'); ?></label>
										<input type="email" name="reg_email" required id="email" value="" />
									</div>
									<div class="field-entry">
										<label for="pass"><?php esc_html_e('Your Password *','nrghost');?></label>
										<input type="password" name="reg_pwd" required id="pass" value="" />
									</div>
									<div class="field-entry">
										<label for="pass_repeat"><?php esc_html_e('Repeat Password *','nrghost'); ?></label>
										<input type="password" name="reg_pwd_rpt" required id="pass_repeat" value="" />
									</div>
									<div class="checkbox-entry checkbox required">
										<input type="checkbox">
										<?php 
										$term_text = 'I agree with the Terms and Conditions';
										$term_link = '#';

										if (is_object( $nrghost_opt )) {
											// term text
											if ($nrghost_opt->get_option( 'register-form-term-text' )) {
												$term_text = $nrghost_opt->get_option( 'register-form-term-text' );
											} 
											// term link
											if ($nrghost_opt->get_option( 'register-form-term-link' )) {
												$term_link = $nrghost_opt->get_option( 'register-form-term-link' );
											} 
										} 
										?>
										<label>
											<a target="blank" href="<?php print esc_url($term_link); ?>">
												<?php print( esc_html( $term_text ) ); ?>
											</a>
										</label>
									</div>
									<div class="checkbox-entry checkbox active">
										<input type="checkbox" name="rememberme" class="chek" id="checked" value="forever" checked>
										<label><?php esc_html_e('Remember Me','nrghost'); ?></label>
									</div>
									
									<input type="hidden" name="redirect_to" value="<?php echo esc_url( home_url( '/' ) ); ?>" />
									<input type="hidden" name="nrghost_register_nonce" value="<?php echo esc_attr( wp_create_nonce( 'nrghost-register-nonce' ) ); ?>"/>
									<div class="button"><?php esc_html_e('Submit','nrghost');?><input type="submit" name="wp-submit" id="wp-submit" value="" /></div>
								</form>
							</div>
						</div>

					</div>

					<?php } elseif ( isset( $_GET['lostpassword'] ) && $_GET['lostpassword'] == 'true' ) { // LOST PASSWORD FORM ?>

					<div class="col-md-6 col-md-offset-3 wow fadeInUp">
						<div class="form-block">
							<img class="img-circle form-icon" src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/img/icon-120.png" alt="" />

							<div class="form-wrapper">
								<div class="row">
									<div class="block-header">
										<?php if ( is_object( $nrghost_opt ) && $nrghost_opt->get_option( 'recovery-form-title' ) ) { ?><h2 class="title"><?php print $nrghost_opt->get_option( 'recovery-form-title' ); ?></h2><?php } ?>
										<?php if ( is_object( $nrghost_opt ) && $nrghost_opt->get_option( 'recovery-form-description' ) ) { ?><div class="text"><?php print $nrghost_opt->get_option( 'recovery-form-description' ); ?></div><?php } ?>
									</div>
								</div>
								<form name="lostpasswordform" action="<?php echo esc_url( home_url( '/wp-login.php?action=lostpassword' ) ); ?>" id="lostpasswordform" method="post">
									<div class="field-entry">
										<label for="user_login">Email or Login *</label>
										<input type="text" name="user_login" required id="user_login" value="" />
									</div>
									<input type="hidden" name="redirect_to" value="<?php echo esc_url( get_permalink( $post->ID ) ) . '/?recovery=success'; ?>" />
									<input type="hidden" name="nrghost_lostpass_nonce" value="<?php echo esc_attr( wp_create_nonce( 'nrghost-lostpass-nonce' ) ); ?>"/>
									<a class="simple-link" href="<?php echo esc_url( get_permalink( $login_page_id ) ); ?>"><span class="glyphicon glyphicon-chevron-right"></span>Login</a><br/>
									<a class="simple-link" href="<?php echo esc_url( get_permalink( $login_page_id ) ), '?action=register'; ?>"><span class="glyphicon glyphicon-chevron-right"></span>Register</a><br/>
									<div class="button">Reset<input type="submit" name="wp-submit" id="wp-submit" value="" /></div>
								</form>
							</div>
						</div>
					</div>

					<?php } else { // LOGIN FORM ?>

					<div class="col-md-6 col-md-offset-3 wow fadeInUp">
						<div class="form-block">
							<img class="img-circle form-icon" src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/img/icon-118.png" alt="" />

							<div class="form-wrapper">
								<div class="row">
									<div class="block-header">
										<?php if ( is_object( $nrghost_opt ) && $nrghost_opt->get_option( 'login-form-title' ) ) { ?><h2 class="title"><?php print $nrghost_opt->get_option( 'login-form-title' ); ?></h2><?php } ?>
										<?php if ( is_object( $nrghost_opt ) && $nrghost_opt->get_option( 'login-form-description' ) ) { ?><div class="text"><?php print $nrghost_opt->get_option( 'login-form-description' ); ?></div><?php } ?>
									</div>
								</div>
								<form name="loginform" action="<?php echo esc_url( home_url( '/wp-login.php' ) ); ?>" id="loginform" method="post">
									<div class="field-entry">
										<label for="user">Login *</label>
										<input type="text" name="log" required id="user" value="" />
									</div>
									<div class="field-entry">
										<label for="pass">Your Password *</label>
										<input type="password" name="pwd" required id="pass" value="" />
									</div>
									<div class="checkbox-entry checkbox">
										<input type="checkbox" name="rememberme" class="chek" id="checked" value="forever">
										<label>Remember Me</label>
									</div>
									<input type="hidden" name="redirect_to" value="<?php echo esc_url( home_url( '/' ) ); ?>" />
									<a class="simple-link" href="<?php echo esc_url( get_permalink( $login_page_id ) ), '?lostpassword=true'; ?>"><span class="glyphicon glyphicon-chevron-right"></span>Forgot Password?</a><br/>
									<a class="simple-link" href="<?php echo esc_url( get_permalink( $login_page_id ) ), '?action=register'; ?>"><span class="glyphicon glyphicon-chevron-right"></span>Register Now</a><br/>
									<div class="button">Login<input type="submit" value="" /></div>
								</form>
							</div>
						</div>
					</div>

					<?php } ?>
				<?php } else { ?>
				<div class="full-width logoin bg-white">
					<div class="container">
						<div class="row">
							<div class="already-logged">
								<h4>You are already logged in. Do you want to <a href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>">log out</a>?</h4>
							</div>
						</div>
					</div>
				</div>
				<?php } ?>

				<!-- ============LOGIN FORM END============= -->
			</div>
		</div>
	</div>
</div>

<?php get_footer(); ?>