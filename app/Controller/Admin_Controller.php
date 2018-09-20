<?php
/**
 * Admin Controller Class
 *
 * @package PrimaryCategory\Controller
 */

namespace Primary_Category\Controller;

class Admin_Controller extends Controller {

	public function register_actions() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts_and_styles' ), 10, 1 );
	}

	/**
	 * Enqueue JavaScript and CSS for the admin post edit screen
	 *
	 * Scripts and styles are registered in Primary_Category\Core\Bootstrap
	 *
	 * @param string $hook_suffix The current admin page.
	 * @return void
	 */
	public function enqueue_admin_scripts_and_styles( $hook_suffix ) {
		// Bail early if this isn't the post edit screen
		if ( 'post-new.php' !== $hook_suffix && 'post.php' !== $hook_suffix ) {
			return;
		}

		wp_enqueue_style( 'primary-category-admin' );

		wp_enqueue_script( 'primary-category-admin', $this->config->get( 'js_url' ) . 'primary-category-admin.min.js', array( 'jquery' ), $this->config->get( 'version' ), true );

		wp_localize_script(
			'primary-category-admin',
			'primary_category_admin',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
			)
		);
	}
}
