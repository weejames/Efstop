<?php

if (file_exists(APPPATH.'controllers/admin.php')) include_once(APPPATH.'controllers/admin.php');

class DAM extends Admin {

	protected $theme = 'dam';
	protected $current_module = 'Home';
	public $tags = array();
	public $tag_string = '';
	
	
	public function DAM($requirelogin = 1) {
		parent::__construct($requirelogin);
		$this->load->model('basemodel');
		$this->load->helper('image_helper');
		
		$this->view_data['page_title'] = 'efstop';
		$this->view_data['page_css'] = array('reset-min', 'autocomplete', 'layout', array('ie6' => 'ie6'), array('ie7' => 'ie7'));
		$this->view_data['page_js'] = array('jquery-1.2.6.pack', 'jquery.media.flash', 'jquery.autocomplete-1.4.1','init');
		
		$this->load->model('imagemodel');
		$this->view_data['systemTags'] = $this->imagemodel->getTagList($this->authentication->getAccountId());

		
		$this->view_data['showSearch'] = true;
		$this->view_data['showMenu'] = true;
		
		$this->view_data['searchKeyword'] = "";
		$this->view_data['searchTags'] = array();
		$this->view_data['searchOrientation'] = "";
		$this->view_data['searchImagesets'] = array();
		
		$this->view_data['modules'] = array('My Lightboxes' => site_URL('dam_controllers/lightbox'),
											'Image Library' => site_URL('dam_controllers/image'),
											'Image Upload' => site_URL('dam_controllers/image/upload'));
		
		if ($this->authentication->isUserType( array('super', 'admin') )) $this->view_data['modules']['User Management'] = site_URL('dam_controllers/damusers');
		
		$this->basemodel->setModel('savedsearches');
		$this->view_data['savedsearches'] = $this->basemodel->find( array('usersid' => $this->authentication->getuserId()) );
		
		$this->view_data['lastlightbox'] = $this->db_session->userdata('lastlightbox');
		$this->view_data['loggedInUser'] = $this->authentication->getUserFullName();
		
		/*accounts*/
		$this->load->model('accountsmodel');
		$account = $this->accountsmodel->retrieve_by_pkey($this->authentication->getAccountId());
		$this->view_data['account'] = $account;
		
		
		/*tabbed navigation handling*/
    	if ($this->uri->rsegment(1) == 'image'  && $this->uri->segment(1) != 'dam_controllers') foreach($this->uri->segment_array() as $key => $seg) if ($key > 1) $this->tags[] = $seg;
		if ($this->uri->rsegment(2) == 'viewImage') array_pop($this->tags);
    	
    	$this->tag_string = join('/', $this->tags);
		
		$this->view_data['imagetags'] = $this->imagemodel->getTagList($this->authentication->getAccountId(), false);
		$this->view_data['tag_string'] = $this->tag_string;
		$this->view_data['tag_array'] = $this->tags;
	}
	
	public function index() {
		$this->load->model('lightboxmodel');
		$this->load->model('imagesetmodel');
		$this->load->model('imagemodel');
		$this->load->model('basemodel');
		
		$this->view_data['lightboxes'] = $this->lightboxmodel->getMyLightboxes($this->authentication->getUserId(), 8);
		//$this->view_data['imagesets'] = $this->imagesetmodel->getImageSetsForUser($this->authentication->getUserId(), 6);
		
		//process imagesets
		/*if ($this->view_data['imagesets']) {
			$imagesetids = array();
			foreach($this->view_data['imagesets'] as $key => $set) {
				$this->view_data['imagesets'][$key]->images = $this->imagemodel->find(array('imagesetid' => $set->id), 0, null, "datecreated DESC");
				$imagesetids[] = $set->id;
			}
		} else $imagesetids = false;*/
		
		//process lightboxes
		if ($this->view_data['lightboxes']) {
			foreach($this->view_data['lightboxes'] as $key => $box) {
				$this->view_data['lightboxes'][$key]->images = $this->imagemodel->getImagesInLightbox($box->id);
			}
		}
		
		$this->view_data['orphans'] = $this->imagemodel->find( array('imagesetid' => 0, 'creatorid' => $this->authentication->getUserId()) );
		
		$this->view_data['images'] = $this->imagemodel->find(array('accountid' => $this->authentication->getAccountId()), 0, 12, "datecreated DESC");
		$this->view_data['returnTo'] = "fromHome";
		
		$this->view_data['intro_notification'] = $this->notifications->renderNotification('home_intro');
		
		$this->layout->buildPage('main/home', $this->view_data);
    }
    
    public function dismissNotification() {
    	$this->load->model('notificationsmodel');
    	
    	$ident = $this->input->post('notification');
    	
    	$notification = $this->notificationsmodel->getNotification($ident);
    	
    	if ($notification) $this->notificationsmodel->dismissNotification($notification->id, $this->authentication->getUserId());
    	
    	echo "";
    }
    
    protected function storeSearchTerms() {
    	$searchTerms = $this->input->post('searchterms');
		$tagCollection = $this->input->post('tags');
		$searchOrientation = $this->input->post('orientation');
		$searchImagesets = $this->input->post('imagesets');
		
		$this->db_session->set_userdata('searchTerms', $searchTerms);
		$this->db_session->set_userdata('searchTags', $tagCollection);
		$this->db_session->set_userdata('searchOrientation', $searchOrientation);
		$this->db_session->set_userdata('searchImagesets', $searchImagesets);
    }

	protected function clearSearchTerms() {
		$this->db_session->unset_userdata('searchTerms');
		$this->db_session->unset_userdata('searchTags');
		$this->db_session->unset_userdata('searchOrientation');
		$this->db_session->unset_userdata('searchImagesets');
	}
	
	protected function getSearchTerms() {
		$terms->searchTerms = $this->db_session->userdata('searchTerms');
		$terms->searchTags = $this->db_session->userdata('searchTags');
		$terms->searchOrientation = $this->db_session->userdata('searchOrientation');
		$terms->searchImagesets = $this->db_session->userdata('searchImagesets');

		return $terms;
	}
	
	protected function _checkPlan() {
		$this->load->model('accountmodel');
	}
	
	//checks if the current session user is logged in.
    protected function _checkAuthentication() {
    	if (!$this->authentication->isLoggedIn()) redirect('cerberus/login');
    }
}
?>
