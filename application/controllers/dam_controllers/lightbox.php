<?php 

if (file_exists(APPPATH.'controllers/dam.php')) include_once(APPPATH.'controllers/dam.php');

class LightBox extends DAM {
	
	protected $package = 'dam_controllers';
	protected $current_module = 'View Lightboxes';
	
	public function __construct() {
		parent::__construct();

		$this->view_data['page_title'] .= ' - Light Boxes';
		
	}
	
	public function index() {
		$this->load->model('lightboxmodel');
		
		$this->view_data['lightboxes'] = $this->lightboxmodel->getMyLightboxes($this->authentication->getUserId());
		
		//process lightboxes
		if ($this->view_data['lightboxes']) {
			foreach($this->view_data['lightboxes'] as $key => $box) {
				$this->view_data['lightboxes'][$key]->images = $this->imagemodel->getImagesInLightbox($box->id);
			}
		}
		
		
		$this->layout->buildPage('lightbox/home', $this->view_data);
    }

    public function viewBox($lightboxid) {
    	$this->load->model('lightboxmodel');
    	$this->load->model('imagemodel');
    	$this->load->model('usersmodel');
    	
    	$this->view_data['lightbox'] = $this->lightboxmodel->retrieve_by_pkey($lightboxid);
    	
		if (!$this->view_data['lightbox']) redirect('');
		
		$this->view_data['images'] = $this->imagemodel->getImagesInLightbox($lightboxid);
    	
		if ($this->authentication->isUserType('super')) {
			$this->view_data['userslist'] = $this->usersmodel->getUsersByGroupSet();
		} else {
			$groupsets = $this->usersmodel->getGroupsetsForUser($this->authentication->getUserId());

			$sets = array();
			if ($groupsets) foreach($groupsets as $set) $sets[] = $set->id;

			$this->view_data['userslist'] = $this->usersmodel->getUsersInGroupsets($sets);
		}
    	
		if ($this->authentication->isUserType('super')) $this->view_data['groupslist'] = $this->usersmodel->getGroups();
		else $this->view_data['groupslist'] = $this->usersmodel->getGroupsForUser($this->authentication->getUserId());

    	$this->view_data['fullImageView'] = true;
    	$this->view_data['setAccess'] = false;
    	$this->view_data['modifyLightbox'] = false;
    	
    	if ($this->view_data['lightbox']->creatorid == $this->authentication->getUserId() || $this->lightboxmodel->userHasFullAccessToLightbox($this->authentication->getUserId(), $lightboxid) ) {
    		$this->view_data['setAccess'] = true;
    		$this->view_data['modifyLightbox'] = true;
    		$this->view_data['currentAccess'] = $this->lightboxmodel->whoHasAccessToLightbox($lightboxid);
    	} 
    	
    	$this->layout->buildPage('lightbox/view', $this->view_data);
    }
	
	public function ajax_lightboxes() {
		$this->load->model('lightboxmodel');
		
		$lightboxes = $this->lightboxmodel->getMyLightboxes($this->authentication->getUserId());
		
		if ($lightboxes) {
			foreach ($lightboxes as $key => $box) {
				if ( $box->id == $this->db_session->userdata('lastlightbox') ) $lightboxes[$key]->selected = true;
				else $lightboxes[$key]->selected = false;
			}
		}
		
		include ('Zend/Json.php');
    	
    	echo Zend_Json::encode((array)$lightboxes);
    	exit();
	}
	
	public function ajax_lightboxcontents($lightboxid) {
		$this->load->model('lightboxmodel');
    	$this->load->model('imagemodel');
    	
    	$lightbox = $this->lightboxmodel->retrieve_by_pkey($lightboxid);
    	
    	$images = $this->imagemodel->getImagesInLightbox($lightboxid);
    	
    	if ($images) {
    		foreach($images as $key => $image) {
    			$images[$key]->display = resizedImageURL('image_store/1500s/'.$image->previewname, 60, 60, true, 'width', 'middle');
    			$images[$key]->url = site_url('image/'.$image->id);
    		}
    	}
    	
    	$this->db_session->set_userdata('lastlightbox', $lightboxid);
    	    	
    	include ('Zend/Json.php');
    	
    	echo Zend_Json::encode($images);
    	exit();
	}
	
	public function create() {
		$this->load->model('lightboxmodel');
		
		$boxdata = null;
		
		$boxdata->boxtitle = $this->input->post('boxtitle');
		$boxdata->creatorid = $this->authentication->getUserId();
		$boxdata->datecreated = date('Y-m-d H:i:s');

		$newid = $this->lightboxmodel->add($boxdata);
		
		if (!$this->ajax) {
			$this->db_session->set_flashdata('newid', $newimageid);
			$this->db_session->set_flashdata('flashmessage', 'Lightbox Created');
		
			redirect('dam');
		} else {
			include ('Zend/Json.php');
			echo Zend_Json::encode(array('lightboxid' => $newid, 'boxtitle' => $boxdata->boxtitle));
			exit();
		}
	}
	
	public function delete($lightboxid) {
		$this->load->model('lightboxmodel');
		
		$lightbox = $this->lightboxmodel->retrieve_by_pkey($lightboxid);
		
		if ($lightbox->creatorid == $this->authentication->getUserId() || $this->lightboxmodel->userHasFullAccessToLightbox($this->authentication->getUserId(), $lightboxid) ) {
			$this->lightboxmodel->delete($lightboxid);
			if (!$this->ajax) {
				$this->db_session->set_flashdata('flashmessage', 'Lightbox Deleted');
			
				redirect('/dam_controllers/lightbox');
			} else {
				include ('Zend/Json.php');
				echo Zend_Json::encode(array('deleted' => true));
				exit();
			}
		} else {
			if (!$this->ajax) {
				$this->db_session->set_flashdata('flasherror', 'You don\'t have permission to delete this lightbox');
			
				redirect('/dam_controllers/lightbox');
			} else {
				include ('Zend/Json.php');
				echo Zend_Json::encode(array('deleted' => false, 'error' => 'You don\'t have permission to delete this lightbox'));
				exit();
			}
		}
	}
	
	public function removeAccess($lightboxid) {
		$this->load->model('lightboxmodel');
		$this->load->model('basemodel');
		
		$lightbox = $this->lightboxmodel->retrieve_by_pkey($lightboxid);
		
		if ($lightbox->creatorid !== $this->authentication->getUserId() && !$this->lightboxmodel->userHasFullAccessToLightbox($this->authentication->getUserId(), $lightboxid) ) {
			$this->db_session->set_flashdata('flasherror', 'I can\'t remove that access as you don\'t have permission to remove it!');
			redirect($this->package.'/lightbox/viewBox/'.$lightboxid);
		}
		
		$accessid = $this->input->post('accessid');
		
		if ($accessid && intval($accessid)) {
			$this->basemodel->setModel('lightbox_access');
			$this->basemodel->delete_by_pkey($accessid);
			
			$this->db_session->set_flashdata('flashmessage', 'Access to this lightbox has now been removed.');
		}
		
		redirect($this->package.'/lightbox/viewBox/'.$lightboxid);
	}
	
	public function setAccess($lightboxid) {
		$this->load->model('lightboxmodel');
		$this->load->model('basemodel');
		
		//check that the user is allowed to set access controls for a lightbox.
		
		$lightbox = $this->lightboxmodel->retrieve_by_pkey($lightboxid);
		if ($lightbox->creatorid !== $this->authentication->getUserId() && !$this->lightboxmodel->userHasFullAccessToLightbox($this->authentication->getUserId(), $lightboxid) ) redirect($this->package.'/lightbox/viewBox/'.$lightboxid);
		
		$whoto = $this->input->post('whoto'); 
		$user = $this->input->post('user');
		$group = $this->input->post('groups');
		
		$data = null;
		
		if ($whoto == 'guest') {
			$emailaddress = $this->input->post('emailaddress');
			
			$data->lightboxid = $lightboxid;
			
			if ($this->input->post('access') == 'full') $data->full = 1;
			else $data->full = 0;
			
			$data->guestkey = md5(uniqid(rand(), true));
			$data->emailaddress = $emailaddress;
			
			$mailMsg = "You have been given access to a lightbox on the efstop. Lightboxes store project related images together. When you view this lightbox, you'll be able to see the images added by the creator.

If you want to view this lightbox, click the following link:

".site_url('dam_controllers/guestaccess/viewLightbox/'.$data->guestkey)."

Note:
If you are unable to open the link directly from your email, please copy and paste the URL into your browser.";
			
			
			$this->load->library('email');

			$this->email->from('notifications@thisisefstop.net', 'efstop');
			$this->email->to($emailaddress);
			
			$this->email->subject('efstop lightbox: '.$lightbox->boxtitle);
			$this->email->message($mailMsg);
			
			$this->email->send();
			
		} else {
			$data->lightboxid = $lightboxid;
			
			if ($this->input->post('access') == 'full') $data->full = 1;
			else $data->full = 0;
			
			if ($whoto == 'group' && $group) $data->groupsid = $group;
			else if ($whoto == 'user' && $user) $data->usersid = $user;
		}
		
		$data->datecreated = date('Y-m-d H:i:s');
		
		$this->basemodel->setModel('lightbox_access');
		$this->basemodel->add($data);
		
		$this->db_session->set_flashdata('flashmessage', 'Access to Lightbox set-up');
		
		redirect($this->package.'/lightbox/viewBox/'.$lightboxid);
	}
	
	public function setName($lightboxid) {
		$this->load->model('lightboxmodel');
		
		//check that the user is allowed to set access controls for a lightbox.
		
		$lightbox = $this->lightboxmodel->retrieve_by_pkey($lightboxid);
		if ($lightbox->creatorid !== $this->authentication->getUserId() ) redirect($this->package.'/lightbox/viewBox/'.$lightboxid);
	
		$data = null;
		$data->boxtitle = $this->input->post('boxtitle');
		
		$this->lightboxmodel->modify($lightboxid, $data);
		
		if (!$this->ajax) {
			$this->db_session->set_flashdata('flashmessage', 'Lightbox name changed');	
			redirect($this->package.'/lightbox/viewBox/'.$lightboxid);
		} else {
			echo 1;
			die();
		}
		
	}
	
	public function removeFromLightbox($lightboxid) {
		$this->load->model('lightboxmodel');
    	
		$imageid = $this->input->post('imageid');
		$lightboxid = $this->input->post('lightboxid');
		
		$this->lightboxmodel->removeImageFromLightbox($imageid, $lightboxid);
		
		redirect($this->package.'/lightbox/viewBox/'.$lightboxid);
	}
	
	public function setCurrentLightbox($lightboxid) {
		$this->db_session->set_userdata('lastlightbox', $lightboxid);
		echo 1;die();
	}
	
}
?>