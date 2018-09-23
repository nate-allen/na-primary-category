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

	/**
	 * Controller constructor
	 */
	public function __construct() {
		$config       = new Config_Model();
		$this->config = $config;
	}
}
