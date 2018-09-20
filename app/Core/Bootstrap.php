<?php
/**
 * Core plugin functionality
 *
 * @package PrimaryCategory
 */

namespace Primary_Category\Core;

use \Primary_Category\Model\Config_Model;
use \Primary_Category\Helper\Files;

class Bootstrap {

	protected $config      = array();
	protected $controllers = array();

	public function __construct() {
		$this->config = new Config_Model();
	}

	/**
	 * Default setup routine
	 *
	 * @return void
	 */
	public function run() {
		// Load the textdomain
		$this->load_textdomain();

		// Load the controllers in the Controller directory
		$this->load_controllers();

		// Register actions for each controller
		$this->register_actions();

		// Register scripts and styles
		add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts_and_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'register_scripts_and_styles' ) );

		// The plugin is ready!
		do_action( 'primary_category_ready', $this );
	}

	/**
	 * Load the textdomain to make the plugin translatable
	 *
	 * @return void
	 */
	protected function load_textdomain() {
		$textdomain_dir  = dirname( $this->config->get( 'plugin_base_name' ) );
		$textdomain_path = $textdomain_dir . $this->config->get( 'text_domain_path' );

		load_plugin_textdomain(
			'na-primary-category',
			false,
			$textdomain_path
		);
	}

	/**
	 * Load Controllers
	 *
	 * Loops over all php files in the Controllers directory and add them to
	 * the $controllers array
	 *
	 * @return void
	 */
	protected function load_controllers() {
		$namespace = $this->config->get( 'namespace' );

		foreach ( Files::glob_recursive( $this->config->get( 'plugin_path' ) . 'app/Controller/*.php' ) as $file ) {
			preg_match( '/\/Controller\/(.+)\.php/', $file, $matches, PREG_OFFSET_CAPTURE );

			$name  = str_replace( '/', '\\', $matches[1][0] );
			$class = '\\' . $namespace . '\\Controller\\' . $name;

			$this->controllers[ $name ] = new $class;
		}
	}

	/**
	 * Register all the hooks/filters for each controller
	 *
	 * @return void
	 */
	protected function register_actions() {
		foreach ( $this->controllers as $name => $class ) {
			if ( method_exists( $class, 'register_actions' ) ) {
				$class->register_actions();
			}
		}
	}

	/**
	 * Register JavaScript and CSS stylesheets
	 *
	 * @return void
	 */
	public function register_scripts_and_styles() {
		if ( is_admin() ) {
			wp_register_script( 'primary-category-admin', $this->config->get( 'js_url' ) . 'primary-category-admin.js', array( 'jquery' ), $this->config->get( 'version' ), true );
		}
	}

}