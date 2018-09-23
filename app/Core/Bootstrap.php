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

	/**
	 * @var Config_Model The plugin configuration
	 */
	protected $config;

	/**
	 * @var array The plugin controllers
	 */
	protected $controllers = array();

	/**
	 * Bootstrap constructor
	 */
	public function __construct() {
		$this->config = new Config_Model();
	}

	/**
	 * Default setup routine
	 *
	 * @since 1.0.0
	 */
	public function run() {
		// Load the textdomain
		$this->load_textdomain();

		// Load the controllers in the Controller directory
		$this->load_controllers();

		// Register actions for each controller
		$this->register_actions();

		// Register scripts and styles
		add_action( 'init', array( $this, 'register_scripts_and_styles' ) );
		add_action( 'admin_init', array( $this, 'register_scripts_and_styles' ) );

		// The plugin is ready!
		do_action( 'primary_category_ready', $this );
	}

	/**
	 * Load the textdomain to make the plugin translatable
	 *
	 * @since 1.0.0
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
	 * @since 1.0.0
	 */
	protected function load_controllers() {
		$namespace = $this->config->get( 'namespace' );

		foreach ( Files::glob_recursive( PRIMARY_CATEGORY_PATH . 'app/Controller/*.php' ) as $file ) {
			preg_match( '/\/Controller\/(.+)\.php/', $file, $matches, PREG_OFFSET_CAPTURE );

			$name  = str_replace( '/', '\\', $matches[1][0] );
			$class = '\\' . $namespace . '\\Controller\\' . $name;

			$this->controllers[ $name ] = new $class;
		}
	}

	/**
	 * Register all the hooks/filters for each controller
	 *
	 * @since 1.0.0
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
	 * @since 1.0.0
	 */
	public function register_scripts_and_styles() {
		if ( is_admin() ) {
			wp_register_script( 'primary-category-admin', $this->config->get( 'js_url' ) . 'primary-category-admin.min.js', array( 'jquery' ), $this->config->get( 'version' ), true );
			wp_register_style( 'primary-category-admin', $this->config->get( 'css_url' ) . 'primary-category-admin.min.css', array(), $this->config->get( 'version' ) );
		}
	}

}