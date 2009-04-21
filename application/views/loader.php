<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Loader File
 *
 * @package		YATS 1.2 -- The Layout Library
 * @subpackage	Views
 * @category	Template
 * @author		Mario Mariani
 * @copyright	Copyright (c) 2006-2007, mariomariani.net All rights reserved.
 * @license		http://svn.mariomariani.net/yats/trunk/license.txt
 */

if (file_exists(APPPATH."views/".$data['settings']['default'] . "/" . $data['settings']['commons'] . "header".EXT) || file_exists(RESPATH."views/".$data['settings']['default'] . "/" . $data['settings']['commons'] . "header".EXT)) $this->load->view($data['settings']['default'] . "/" . $data['settings']['commons'] . "header", $data);
else if (file_exists(APPPATH."views/".$data['settings']['shared'] . "/" . $data['settings']['commons'] . "header".EXT) || file_exists(RESPATH."views/".$data['settings']['shared'] . "/" . $data['settings']['commons'] . "header".EXT)) $this->load->view($data['settings']['shared'] . "/" . $data['settings']['commons'] . "header", $data);
else echo "Missing header.php";

if (file_exists(APPPATH."views/".$data['settings']['default'] . "/" . $data['settings']['content'] . "$view".EXT) || file_exists(RESPATH."views/".$data['settings']['default'] . "/" . $data['settings']['content'] . "$view".EXT)) $this->load->view($data['settings']['default'] . "/" . $data['settings']['content'] . "$view",  $data);
else if (file_exists(APPPATH."views/".$data['settings']['shared'] . "/" . $data['settings']['content'] . "$view".EXT) || file_exists(RESPATH."views/".$data['settings']['shared'] . "/" . $data['settings']['content'] . "$view".EXT)) $this->load->view($data['settings']['shared'] . "/" . $data['settings']['content'] . "$view",  $data);
else echo "Missing body";

if (file_exists(APPPATH."views/".$data['settings']['default'] . "/" . $data['settings']['commons'] . "footer".EXT) || file_exists(RESPATH."views/".$data['settings']['default'] . "/" . $data['settings']['commons'] . "footer".EXT)) $this->load->view($data['settings']['default'] . "/" . $data['settings']['commons'] . "footer", $data);
else if (file_exists(APPPATH."views/".$data['settings']['shared'] . "/" . $data['settings']['commons'] . "footer".EXT) || file_exists(RESPATH."views/".$data['settings']['shared'] . "/" . $data['settings']['commons'] . "footer".EXT)) $this->load->view($data['settings']['shared'] . "/" . $data['settings']['commons'] . "footer", $data);
else echo "Missing footer.php";
?>