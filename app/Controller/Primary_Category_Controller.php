<?php
/**
 * Primary Category Controller Class
 *
 * @package PrimaryCategory\Controller
 */

namespace Primary_Category\Controller;

use Primary_Category\Model\Primary_Category_Model;

class Primary_Category_Controller extends Controller {

	/**
	 * Register the actions and filters
	 *
	 * @return void
	 */
	public function register_actions() {
		add_action( 'init', array( $this, 'register_taxonomy' ) );
		add_action( 'save_post', array( $this, 'update_primary_category' ) );
		add_action( 'pre_get_posts', array( $this, 'handle_custom_query_parameter' ) );
	}

	/**
	 * Register the primary category taxonomy
	 *
	 * The primary category taxonomy is a private taxonomy that is used to keep track
	 * of which category is the primary one. We use a taxonomy to keep track of this
	 * because it's the most efficient way to query posts, unlike post meta
	 *
	 * @return void
	 */
	public function register_taxonomy() {
		$primary_category_model = new Primary_Category_Model();

		register_taxonomy( 'na_primary_category', array( 'post' ), $primary_category_model->args );
	}

	/**
	 * Update the primary category when the post is saved
	 *
	 * @param $post_id int Post ID
	 */
	public function update_primary_category( $post_id ) {
		$primary_category_model = new Primary_Category_Model();

		// Skip revisions and autosaves
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $post_id ) ) {
			return;
		}

		// Verify the nonce
		if ( ! isset( $_POST['_na_primary_category_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['_na_primary_category_nonce'] ), "set_primary_category_{$post_id}" ) ) {
			return;
		}

		// Check if user has ability to edit primary category
		if ( ! $primary_category_model->user_can_set_primary_category( $post_id ) ) {
			return;
		}

		// Check if the primary category is set
		if ( ! isset( $_POST['na_primary_category_id'] ) ) {
			return;
		}

		// Set the primary category
		wp_set_object_terms( $post_id, sanitize_text_field( $_POST['na_primary_category_id'] ), 'na_primary_category' );
	}


	/**
	 * Handle the 'primary_category' argument in WP_Query
	 *
	 * @param $query
	 */
	public function handle_custom_query_parameter( $query ) {
		if ( is_admin() || ! $query->is_main_query() || $query->is_search ) {
			return;
		}

		if ( ! isset( $query->query_vars['primary_category'] ) ) {
			return;
		}

		$tax_query = array(
			array(
				'taxonomy' => 'na_primary_category',
				'field'    => 'slug',
				'terms'    => $query->query_vars['primary_category'],
			),
		);

		$query->set( 'tax_query', $tax_query );
	}
}