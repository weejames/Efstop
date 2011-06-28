<?php 

class API extends MY_Controller {

	private $valid_formats = array('json');
	private $format = false;

	public function __construct() {
		parent::__construct();
	}
	
	public function rest($format = false, $method = false) {
		if ($format && in_array($format, $this->valid_formats )) $this->format = $format;
		else $this->_respond( array('state' => 'error', 'method' => $method, 'format' => $format, 'message' => 'Invalid format requested - must be one of ('.implode(', ', $this->valid_formats).').') );
		
		if ($method) {
			$normalised_method = strtolower(str_replace('.', '', $method)); 
		
			if ($normalised_method && method_exists($this, $normalised_method)) $this->_respond( array('state' => 'ok', 'method' => $method, 'format' => $format, 'data' => $this->$normalised_method() ) );
			else echo 0;
		} else $this->_respond( array('state' => 'error', 'method' => $method, 'format' => $format, 'message' => 'Invalid method requested.') );

	}
	
	private function efstoplightboxgetlightboxes() {
		$this->load->model('lightboxmodel');
		
		$data->lightboxes = $this->lightboxmodel->find();
		
		return $data;
	}
	
	private function _respond($data) {
		include_once('Zend/Json.php');
		switch ($this->format) {
			case "json":
			default:
				echo Zend_Json::encode($data);
			break;
		}
	}

}

?>