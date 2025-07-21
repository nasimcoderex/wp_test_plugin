<?php
/**
 * Plugin Name: Salesflow
 * Plugin URI:  https://example.com/
 * Description: A funnel creation system for WordPress.
 * Version:     1.0.0
 * Author:      Your Name
 * Author URI:  https://example.com/
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: salesflow
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Returns the main instance of Salesflow.
 *
 * @return Salesflow
 */
function salesflow() {
	return Salesflow::instance();
}

// Include the main Salesflow class.
if ( ! class_exists( 'Salesflow' ) ) {
	include_once dirname( __FILE__ ) . '/includes/class-salesflow.php';
}

// Initialize the plugin.
add_action( 'plugins_loaded', 'salesflow' );
