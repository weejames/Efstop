<?php 

class SC extends Controller {

	public function __construct() {
		parent::Controller();
	}
	
	public function lightboxes() {
		$this->load->model('lightboxmodel');
		
		$data = null;
		
		$lightboxes = $this->lightboxmodel->find();
		
		$ids = array();
		
		foreach($lightboxes as $record) {
			$ids[] = $record->id;
		}
		
		$data->records = $lightboxes;
		$data->ids = $ids;
		
		$this->_respond($data);
	}
	
	public function image($action = 'list') {
		$this->load->model('imagemodel');
		
		$data = null;
		
		$images = $this->imagemodel->find();
		
		$ids = array();
		
		foreach($images as $record) {
			$ids[] = $record->id;
		}
		
		$data->records = $images;
		$data->ids = $ids;
		
		$this->_respond($data);
	}
	
	private function _respond($data) {
		include_once('Zend/Json.php');
		echo Zend_Json::encode($data);
	}

}

?>