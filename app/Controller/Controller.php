<?php
/**
 * Base Controller for extending
 *
 * @package Primary_Category\Controller
 */

namespace Primary_Category\Controller;

use Primary_Category\Core\View;
use Primary_Category\Model\Config_Model;

class Controller extends View {

	protected $config;

	public function __construct( $month = null, $year = null ) {
		$config       = new Config_Model();
		$this->config = $config;
	}
}