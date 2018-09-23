<?php
/**
 * File related helper methods
 *
 * @package PrimaryCategory
 */

namespace Primary_Category\Helper;

class Files {

	/**
	 * Find path names matching a pattern in a folder and its subfolders
	 *
	 * @param string $pattern Pattern passed to glob
	 * @param int    $flags   Optional flags that can be passed to glob
	 * @since 1.0.0
	 * @return array Path names
	 */
	public static function glob_recursive( $pattern, $flags = 0 ) {
		$files = glob( $pattern, $flags );

		foreach ( glob( dirname( $pattern ) . '/*', GLOB_ONLYDIR | GLOB_NOSORT ) as $dir ) {
			$files = array_merge( $files, self::glob_recursive( $dir . '/' . basename( $pattern ), $flags ) );
		}

		return $files;
	}
}