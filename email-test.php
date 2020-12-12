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
			self::load_dependencies();
			self::load_hooks();
		}

		/**
		 * Loads all other plugin files
		 *
		 * @since 0.1.0
		 */
		public static function load_dependencies() {

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
			add_management_page( 'Email Test', 'Email Test', 'manage_options', 'email-test', '' );
		}
	}

	SA_Email_Test::init();
}
?>