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
}