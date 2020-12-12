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

		public static function init() {
			self::load_dependencies();
			self::load_hooks();
		}

		public static function load_dependencies() {

		}

		public static function load_hooks() {

		}
	}

	add_action( 'plugins_loaded', array( 'SA_Email_Test', 'init' ) );
}
?>