<?php
/**
 * Admin Controller Class
 *
 * @package PrimaryCategory\Controller
 */

namespace Primary_Category\Controller;

use Primary_Category\Model\Primary_Category_Model;

class Admin_Controller extends Controller {

	/**
	 * Register the actions and filters
	 */
	public function register_actions() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts_and_styles' ), 10, 1 );
		add_action( 'post_submitbox_misc_actions', array( $this, 'add_fields_to_submitbox' ) );
	}

	/**
	 * Enqueue JavaScript and CSS on the admin post edit screen
	 *
	 * Scripts and styles are registered in Primary_Category\Core\Bootstrap
	 *
	 * @param string $hook_suffix The current admin page.
	 * @since 1.0.0
	 */
	public function enqueue_admin_scripts_and_styles( $hook_suffix ) {
		// Bail early if this isn't the post edit screen
		if ( 'post-new.php' !== $hook_suffix && 'post.php' !== $hook_suffix ) {
			return;
		}

		$primary_category_model = new Primary_Category_Model();

		wp_enqueue_style( 'primary-category-admin' );

		wp_enqueue_script( 'primary-category-admin' );

		// Variables and translated text for the JavaScript
		wp_localize_script(
			'primary-category-admin',
			'primary_category_admin',
			array(
				'yoast_seo_installed' => is_plugin_active( 'wordpress-seo/wp-seo.php' ),
				'user_has_permission' => $primary_category_model->user_can_set_primary_category(),
				'i18n'                => array(
					'primary'      => esc_attr__( 'Primary', 'na-primary-category' ),
					'make_primary' => esc_attr__( 'Make Primary', 'na-primary-category' ),
				),
			)
		);
	}

	/**
	 * Add hidden field to the post submitbox
	 *
	 * The hidden field stores the primary category term ID
	 *
	 * @since 1.0.0
	 */
	public function add_fields_to_submitbox( $post ) {
		$primary_category_model = new Primary_Category_Model();

		// Don't add fields if the current user can't set primary category
		if ( ! $primary_category_model->user_can_set_primary_category() ) {
			return;
		}

		// Assign variables for the view
		$this->assign( 'post_id', $post->ID );
		$this->assign( 'primary_category', $primary_category_model->get_primary_category_id( $post->ID ) );

		// Display the view
		$this->the_view( 'admin/submitbox-fields' );
	}
}
