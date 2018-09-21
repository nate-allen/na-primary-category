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
	 *
	 * @return void
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
	 * @return void
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
				'primary_category'    => 'test',
				'yoast_seo_installed' => is_plugin_active( 'wordpress-seo/wp-seo.php' ),
				'user_has_permission' => $this->user_can_set_primary_category(),
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
	 * @return void
	 */
	public function add_fields_to_submitbox( $post ) {
		// Don't add fields if the current user can't set primary category
		if ( ! $this->user_can_set_primary_category() ) {
			return;
		}

		// Assign variables for the view
		$this->assign( 'post_id', $post->ID );
		$this->assign( 'primary_category', '4' );

		// Display the view
		$this->the_view( 'admin/submitbox-fields' );
	}

	/**
	 * Determines if the current user can set the primary category
	 *
	 * @return bool
	 */
	protected function user_can_set_primary_category() {
		global $post;

		/**
		 * Filters the default setting for determining if current user has permission to
		 * set the primary category for a post.
		 *
		 * By default, this is determined by the edit_post capability, but you can use this
		 * filter to create your own rules.
		 *
		 * @param bool Whether the user can set primary category
		 */
		return apply_filters( 'user_can_set_primary_category', current_user_can( 'edit_posts', $post->ID ) );
	}
}
