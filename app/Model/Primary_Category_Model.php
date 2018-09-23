<?php
/**
 * Primary Category Model
 *
 * @package Primary_Category\Model
 */

namespace Primary_Category\Model;

class Primary_Category_Model {
	/**
	 * Plugin configuration
	 *
	 * @var Config_Model
	 */
	protected $config;

	/**
	 * Taxonomy arguments
	 *
	 * @var array
	 */
	public $args = array();

	/**
	 * Primary Category Model constructor
	 */
	public function __construct() {
		// Set the plugin configuration
		$this->config = new Config_Model();

		// Set the taxonomy arguments
		$this->set_args();
	}


	/**
	 * Get the primary category id
	 *
	 * @param $post_id
	 * @since 1.0.0
	 * @return int|bool Primary category ID if it exists, false if there is no primary category
	 */
	public function get_primary_category_id( $post_id = 0 ) {
		if ( empty( $post_id ) ) {
			global $post;

			$post_id = $post->ID;
		}

		$terms = get_the_terms( $post_id, 'na_primary_category' );

		return $terms ? absint( $terms[0]->slug ) : false;
	}

	/**
	 * Set the taxonomy arguments
	 *
	 * This is non-public taxonomy, not shown in the admin
	 *
	 * @since 1.0.0
	 */
	protected function set_args() {
		$default_args = array(
			'label'        => esc_html__( 'Primary Category', 'na-primary-category' ),
			'hierarchical' => true,
			'public'       => false,
		);

		/**
		 * Filters the default taxonomy arguments for the primary_category taxonomy.
		 *
		 * @param array $default_args Default arguments
		 * @since 1.0.0
		 */
		$this->args = apply_filters( 'primary_category_taxonomy_args', $default_args );
	}

	/**
	 * Determines if the current user can set the primary category
	 *
	 * @param int $post_id The ID of the post to check if a user can edit the primary category for
	 * @since 1.0.0
	 * @return bool
	 */
	public function user_can_set_primary_category( $post_id = 0 ) {
		if ( empty( $post_id ) ) {
			global $post;

			$post_id = $post->ID;
		}

		/**
		 * Filters the default setting for determining if current user has permission to
		 * set the primary category for a post.
		 *
		 * By default, this is determined by the edit_post capability, but you can use this
		 * filter to create your own rules.
		 *
		 * @param bool          Whether the user can set primary category
		 * @param int  $post_id Post ID
		 * @since 1.0.0
		 */
		return apply_filters( 'user_can_set_primary_category', current_user_can( 'edit_posts', $post_id ), $post_id );
	}
}
