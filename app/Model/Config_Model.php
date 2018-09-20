<?php
/**
 * Configuration model
 *
 * @package PrimaryCategory
 */

namespace Primary_Category\Model;

class Config_Model {

	protected $config;

	protected $properties = array();

	/**
	 * Config_Model constructor
	 */
	public function __construct() {
		$this->setup_plugin_config();
	}

	/**
	 * Sets the default configuration
	 *
	 * @return bool|mixed
	 */
	protected function setup_plugin_config() {
		$config = wp_cache_get( 'Config_Model', 'primary-category' );

		if ( false !== $config ) {
			return $config;
		}

		$this->set( 'plugin_base_name', plugin_basename( PRIMARY_CATEGORY_FILE ) );

		$plugin_headers = get_file_data(
			PRIMARY_CATEGORY_FILE,
			array(
				'plugin_name'      => 'Plugin Name',
				'plugin_uri'       => 'Plugin URI',
				'description'      => 'Description',
				'author'           => 'Author',
				'version'          => 'Version',
				'author_uri'       => 'Author URI',
				'textdomain'       => 'Text Domain',
				'text_domain_path' => 'Domain Path',
			)
		);

		$this->import( $plugin_headers );

		$this->set( 'css_url', PRIMARY_CATEGORY_PLUGIN_URL . 'dist/css/' );
		$this->set( 'js_url', PRIMARY_CATEGORY_PLUGIN_URL . 'dist/js/' );
		$this->set( 'images_url', PRIMARY_CATEGORY_PLUGIN_URL . 'dist/images/' );

		wp_cache_set( 'Config_Model', $config, 'primary-category' );

		return $this->config;
	}

	/**
	 * Returns the value of the configuration
	 *
	 * @param $name string Configuration to get
	 *
	 * @return bool|mixed
	 */
	public function get( $name ) {
		if ( isset( $this->properties[ $name ] ) ) {
			return $this->properties[ $name ];
		}

		return false;
	}

	/**
	 * Sets a configuration
	 *
	 * @param $name  string The name of the configuration
	 * @param $value string The value of the configuration
	 *
	 * @return void
	 */
	public function set( $name, $value ) {
		$this->properties[ $name ] = $value;
	}

	/**
	 * Sets multiple configurations at a time
	 *
	 * @param $var array|object Collection of configurations to set
	 *
	 * @return bool
	 */
	public function import( $var ) {
		if ( ! is_array( $var ) && ! is_object( $var ) ) {
			return false;
		}

		foreach ( $var as $name => $value ) {
			$this->properties[ $name ] = $value;
		}

		return true;
	}
}
