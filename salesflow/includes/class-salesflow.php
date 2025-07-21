<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Salesflow {

	/**
	 * The single instance of the class.
	 *
	 * @var Salesflow
	 */
	protected static $_instance = null;

	/**
	 * Main Salesflow Instance.
	 *
	 * Ensures only one instance of Salesflow is loaded or can be loaded.
	 *
	 * @static
	 * @return Salesflow - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Salesflow Constructor.
	 */
	public function __construct() {
		$this->define_constants();
		$this->includes();
		$this->init_hooks();
	}

	/**
	 * Define Salesflow Constants.
	 */
	private function define_constants() {
		define( 'SALESFLOW_PLUGIN_FILE', __FILE__ );
		define( 'SALESFLOW_PLUGIN_BASENAME', plugin_basename( SALESFLOW_PLUGIN_FILE ) );
		define( 'SALESFLOW_VERSION', '1.0.0' );
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 */
	public function includes() {
		// Include any necessary files here.
	}

	/**
	 * Hook into actions and filters.
	 */
	private function init_hooks() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
	}

	public function admin_menu() {
		add_menu_page(
			'Salesflow',
			'Salesflow',
			'manage_options',
			'salesflow',
			array( $this, 'admin_page' ),
			'dashicons-chart-line',
			6
		);
	}

	public function admin_page() {
		wp_enqueue_script( 'salesflow-app', plugin_dir_url( __FILE__ ) . '../dist/bundle.js', array(), '1.0.0', true );
		echo '<div id="salesflow-app"></div>';
	}
}
