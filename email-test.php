<?php
/**
 * Plugin Name: Email Test
 * Description: Test your site's email to make sure emails are being sent.
 * Version: 0.1.0
 * Author: Frank Corso
 * Author URI: https://frankcorso.me
 * Plugin URI: https://frankcorso.me
 * Text Domain: email-test
 *
 * @author Frank Corso
 */

// Exits if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SA_Email_Test' ) ) {
	/**
	 * The plugin's main class
	 *
	 * @since 0.1.0
	 */
	class SA_Email_Test {

		/**
		 * Initializes our plugin
		 *
		 * @since 0.1.0
		 */
		public static function init() {
			self::load_hooks();
		}

		/**
		 * Adds in any plugin-wide hooks
		 *
		 * @since 0.1.0
		 */
		public static function load_hooks() {
			add_action( 'admin_menu', array( __CLASS__, 'setup_admin_menu' ) );
		}

		/**
		 * Sets up our page in the admin menu
		 *
		 * @since 0.1.0
		 */
		public static function setup_admin_menu() {
			add_management_page( 'Email Test', 'Email Test', 'manage_options', 'email-test', array( __CLASS__, 'generate_admin_page' ) );
		}

		/**
		 * Generates our admin page
		 *
		 * @since 0.1.0
		 */
		public static function generate_admin_page() {
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			wp_enqueue_style( 'email_test_styles', plugins_url( 'email-test.css', __FILE__ ) );

			// Only perform our email test once the form is submitted.
			if ( isset( $_POST['email-test'] ) && wp_verify_nonce( $_POST['email-test-nonce'], 'test-email' ) ) {
				$success = true;
				$error   = '';
				try {
					self::email_test( $_POST['email-test'] );
				} catch ( Exception $e ) {
					$success = false;
					$error   = $e->getMessage();
				}
				
			}
			?>
			<div class="wrap">
				<h2>Email Test</h2>
				<?php
				// Display results of the test.
				if ( isset( $_POST['email-test'] ) ) {
					?>
					<section class="email-test-results notice <?php echo true === $success ? 'notice-success' : 'notice-error'; ?>">
						<h2>Your Email Test Results</h2>
						<?php
						if ( true === $success ) {
							?>
							<p>Your email was marked as sent by WordPress. Now, go check your email to see if the email was received.</p>
							<?php
						} else {
							?>
							<p><?php echo esc_html( $error ); ?></p>
							<?php
						}
						?>
					</section>
					<?php
				}
				?>
				<section class="email-test-form">
					<h2>Run an Email Test</h2>
					<p>Not sure if your site is sending email correctly? Use this form to try to send an email to yourself.</p>
					<form method="POST" action="">
						<label for="email-test">What email address should we send the test email to?</label>
						<input id="email-test" type="email" name="email-test">
						<?php wp_nonce_field( 'test-email', 'email-test-nonce' ); ?>
						<p class="submit">
							<button type="submit" class="button button-primary">Send test email</button>
						</p>
					</form>
				</section>
			</div>
			<?php
		}

		/**
		 * Attempts to send an email from WordPress to supplied email address
		 *
		 * @param string $email_address The email address to send an email to.
		 * @throws Exception If email fails, the exception will contain more details for why it failed.
		 * @since 0.1.0
		 */
		public static function email_test( $email_address ) {
			if ( ! is_email( $email_address ) ) {
				throw new Exception( 'The email provided was not valid.' );
			}
		
			// Prepare our transient for error catching.
			set_transient( 'sa_wp_mail_failed_reason', '', 60 );
		
			// Prepare our email.
			$to = sanitize_email( $email_address );
			$subj = 'Email Test';
		
			// Add our function to catch any errors, send the email, and then immediately remove our function.
			add_action( 'wp_mail_failed', array( __CLASS__, 'catch_email_errors' ) );
			$success = wp_mail($to, $subj, 'This is a test email from your site!');
			remove_action( 'wp_mail_failed', array( __CLASS__, 'catch_email_errors' ) );
		
			// See if our error reason was updated due to wp_mail_failed error.
			$reason = get_transient( 'sa_wp_mail_failed_reason' );
			if ( ! empty( $reason ) ) {
				throw new Exception( "The email was not sent. WordPress provided this reason: $reason" );
			}
		
			// If not, determine success based on bool returned from wp_mail.
			if ( false === $success ) {
				throw new Exception( 'The email was not sent. WordPress provided no reason for the error.' );
			}
		}

		/**
		 * Attempts to catch errors from our test email
		 *
		 * @param WP_Error $wp_error
		 * @since 0.1.0
		 */
		public static function catch_email_errors( $wp_error ) {
			if ( ! is_wp_error( $wp_error ) ) {
				return;
			}
			$error = $wp_error->get_error_message('wp_mail_failed');
			set_transient( 'sa_wp_mail_failed_reason', $error, 60 );
		}
	}

	SA_Email_Test::init();
}
?>