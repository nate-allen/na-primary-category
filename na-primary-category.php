<?php
/*
* Plugin Name: Primary Category
* Plugin URI: https://github.com/nate-allen/na-primary-category
* Description: Allows publishers to designate a primary category for posts, and query for posts by their primary category.
* Version: 1.2.0
* License: GPL-2.0+
* Author URI: https://github.com/nate-allen
* Text Domain: na-primary-category
*
* @package PrimaryCategory
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Globals
 */
// Define the main plugin file to make it easy to reference in subdirectories
if ( ! defined( 'PRIMARY_CATEGORY_FILE' ) ) {
	define( 'PRIMARY_CATEGORY_FILE', __FILE__ );
}

if ( ! defined( 'PRIMARY_CATEGORY_PATH' ) ) {
	define( 'PRIMARY_CATEGORY_PATH', trailingslashit( __DIR__ ) );
}

if ( ! defined( 'PRIMARY_CATEGORY_PLUGIN_URL' ) ) {
	define( 'PRIMARY_CATEGORY_PLUGIN_URL', trailingslashit( plugin_dir_url( __FILE__ ) ) );
}

/**
 * Autoload Classes
 */
include( PRIMARY_CATEGORY_PATH . 'app/Core/Psr4Autoloader.php' );
$loader = new \Primary_Category\Core\Psr4Autoloader();
$loader->addNamespace( 'Primary_Category', dirname( __FILE__ ) . '/app' );
$loader->register();

/***
 * Kick everything off when plugins are loaded
 */
add_action( 'plugins_loaded', 'primary_category_init' );

/**
 * Callback for starting the plugin.
 *
 * @wp-hook plugins_loaded
 *
 * @return void
 */
function primary_category_init() {
	$primary_category = new \Primary_Category\Core\Bootstrap();

	try {
		$primary_category->run();
	} catch ( Exception $e ) {
		wp_die( esc_html( $e->getMessage() ) );
	}
}
