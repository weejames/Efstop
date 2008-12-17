<?php 
if (file_exists(APPPATH.'controllers/dam.php')) include_once(APPPATH.'controllers/dam.php');

class GuestAccess extends DAM {
	
	public $package = 'dam_controllers';
	private $s3bucket = ''; // TODO: make a config setting
	private $local_image_path = ''; // TODO: make a config setting
	
	public function GuestAccess() {
		parent::DAM(false);

		$this->view_data['page_title'] .= ' - Guest Lightbox Access';		
	}
	
	public function viewLightbox($guestkey = '') {
		$this->load->model('lightboxmodel');
		$this->load->model('imagemodel');
		$this->load->model('basemodel');
		
		$this->view_data['page_js'] = array('jquery-1.2.3.pack', 'jquery.thickbox3-packed', 'guest');
		$this->view_data['page_css'][] = 'thickbox3';
		
		$this->basemodel->setModel('lightbox_access');
		
		$accessDetails = $this->basemodel->find( array('guestkey' => $guestkey) );
		
		$this->view_data['guestkey'] = $guestkey;
		
		if ($accessDetails) {
			$lightbox = $this->lightboxmodel->retrieve_by_guestkey($guestkey);
		
			if (!$accessDetails[0]->full) {
		
				$twoWeeksFromCreationDate = strtotime($accessDetails[0]->datecreated) + (14 * 24 * 60 * 60);
			
				if ($lightbox === false || $twoWeeksFromCreationDate < strtotime("now") ) {
					$this->db_session->set_flashdata('flashmessage', 'Your access for this lightbox has expired');
					redirect('');
				}
			
				$this->view_data['imageDownload'] = false;
			
			} else $this->view_data['imageDownload'] = true;
			
			$this->view_data['lightbox'] = $lightbox;
			$this->view_data['images'] = $this->imagemodel->getImagesInLightbox($lightbox->id);
			$this->view_data['loggedInUser'] = $accessDetails[0]->emailaddress;


			$this->view_data['showSearch'] = false;
			$this->view_data['showMenu'] = false;
			
			$this->layout->buildPage('lightbox/guestview', $this->view_data);
			
		} else redirect('');
    }
    
	public function download($guestkey, $imageid) {
    	$this->load->model('imagemodel');
    	$this->load->model('basemodel');
    	
    	$this->basemodel->setModel('lightbox_access');
		
		$accessDetails = $this->basemodel->find( array('guestkey' => $guestkey) );
		
		if (!$accessDetails[0]->full) redirect('');
    	
    	$imagedata = $this->imagemodel->retrieve_by_pkey($imageid);
    	
    	//check if its here or on s3
    	if ($imagedata->s3upload) {
    		$this->load->library('s3');
    		
    		//$location = $this->s3->getPrivateURL($this->s3bucket, $imagedata->filename);
    		
    		//$this->s3->getObjectInfo($this->s3bucket, $imagedata->filename);
    		
    		$data = $this->s3->getObject($imagedata->filename, $this->s3bucket);
    		
    		$ct = $this->s3->getResponseContentType();
    		$cl = $this->s3->getResponseContentLength();
    		
    		//header("Location: ".$s3URL, TRUE, 307);
    		header("Content-Type: ".$ct);
			header("Content-Disposition: attachment; filename=\"".basename($imagedata->filename)."\";" );
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: ".$cl);
			
			flush();
			
			echo $data;
    	} else {
    		header("Content-Type: ".mime_content_type($this->local_image_path.$imagedata->filename));
			header("Content-Disposition: attachment; filename=\"".basename($this->local_image_path.$imagedata->filename)."\";" );
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: ".filesize($this->local_image_path.$imagedata->filename));
			$data = file_get_contents($this->local_image_path.$imagedata->filename);
			echo $data;
    	}
    	exit();
    }
    
}
?>