<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Layout Model Class
 *
 * @package		YATS 1.2 -- The Layout Library
 * @subpackage	Models
 * @category	Template
 * @author		Mario Mariani
 * @copyright	Copyright (c) 2006-2007, mariomariani.net All rights reserved.
 * @license		http://svn.mariomariani.net/yats/trunk/license.txt
 */



class Layout_model extends MY_Model {
	public $theme;

	public function __construct() {
		parent::__construct();

		$this->layout = get_instance();
		$this->layout->config->load('layout');
		$this->common = $this->layout->config->item('layout_default') . "/" . $this->layout->config->item('layout_commons');
		$this->theme  = $this->layout->config->item('layout_default') . "/" . $this->layout->config->item('layout_content');
	}
	
}

// EOF


?>