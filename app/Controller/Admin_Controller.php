<?php
/**
 * Admin Controller Class
 *
 * @package PrimaryCategory\Controller
 */

namespace Primary_Category\Controller;

class Admin_Controller extends Controller {

	public function register_actions() {
		add_action( 'admin_print_styles-post-new.php', array( $this, 'load_assets' ) );
		add_action( 'admin_print_styles-post.php', array( $this, 'load_assets' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts_and_styles' ) );
		add_action( 'wp_dashboard_setup', array( $this, 'register_salesforce_sync_widget' ) );
		add_action( 'wp_ajax_force_sync', array( $this, 'force_sync' ) );
	}

	public function load_assets() {
		wp_enqueue_style( 'primary-category-admin' );
	}

	public function enqueue_admin_scripts_and_styles() {
		wp_enqueue_script( 'primary-category-admin', $this->config->get( 'js_url' ) . 'primary-category-admin.js', array( 'jquery' ), '1.0.0', true );

		wp_localize_script(
			'primary-category-admin',
			'primary_category_admin',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
			)
		);
	}
}
