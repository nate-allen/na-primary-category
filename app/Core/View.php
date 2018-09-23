<?php
/**
 * View base class
 *
 * @package PrimaryCategory
 */

namespace Primary_Category\Core;

class View {
	/**
	 * Variables for substitution in templates
	 *
	 * @var array Variables passed to the view
	 */
	protected $variables = array();

	/**
	 * Assign variable for substitution in templates
	 *
	 * @param string $variable Name variable to assign
	 * @param mixed  $value    Value variable for assign
	 * @since 1.0.0
	 */
	public function assign( $variable, $value ) {
		$this->variables[ $variable ] = $value;
	}

	/**
	 * Echos the view
	 *
	 * Useful for meta boxes because the markup needs to be echoed
	 *
	 * @param string $file     File to get HTML string
	 * @param string $view_dir View directory
	 * @since 1.0.0
	 */
	public function the_view( $file, $view_dir = null ) {
		foreach ( $this->variables as $key => $value ) {
			${$key} = $value;
		}

		$view_dir  = isset( $view_dir ) ? $view_dir : PRIMARY_CATEGORY_PATH . 'views/';
		$view_file = $view_dir . $file . '.php';

		if ( ! file_exists( $view_file ) ) {
			return;
		}

		include_once( $view_file );
	}

	/**
	 * Returns the view
	 *
	 * Useful for shortcodes because the markup needs to be returned
	 *
	 * @param string $file     File to get HTML string
	 * @param string $view_dir View directory
	 * @since 1.0.0
	 * @return string $html HTML output as string
	 */
	public function get_view( $file, $view_dir = null ) {
		foreach ( $this->variables as $key => $value ) {
			${$key} = $value;
		}

		$view_dir  = isset( $view_dir ) ? $view_dir : PRIMARY_CATEGORY_PATH . 'views/';
		$view_file = $view_dir . $file . '.php';
		if ( ! file_exists( $view_file ) ) {
			return '';
		}

		ob_start();
		include( $view_file );
		$thread = ob_get_contents();
		ob_end_clean();
		$html = $thread;

		$this->init_assignments();

		return $html;
	}

	/**
	 * Resets the variables
	 *
	 * @since 1.0.0
	 */
	protected function init_assignments() {
		$this->variables = array();
	}
}
