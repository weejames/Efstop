<?php

if (file_exists(APPPATH.'controllers/dam.php')) include_once(APPPATH.'controllers/dam.php');

class DAMUsers extends DAM {

	protected $package = 'dam_controllers';
	protected $current_module = 'Users';
	
	public function __construct() {
		parent::__construct();
		$this->view_data['page_title'] .= ' - User Administration';
		//$this->view_data['showSearch'] = false;
		
		if (!$this->authentication->isUserType(array('super', 'admin'))) redirect('');
	}

	public function index() {
		$this->browse();
	}

	public function browse($start = 0) {
		$this->load->model('usersmodel');
		$this->load->library('pagination');
		$this->load->helper('url');
		
		$limit_per_page = 20;

		if ($this->authentication->isUserType('super')) {
			$this->view_data['users_list'] = $this->usersmodel->getUsersByGroupSet($start, $limit_per_page, false);
			$this->view_data['viewBy'] = 'set';
		} else {
			$groupsets = $this->usersmodel->getGroupsetsForUser($this->authentication->getUserId());
			
			$sets = array();
			if ($groupsets) foreach($groupsets as $set) $sets[] = $set->id;
			
			$this->view_data['users_list'] = $this->usersmodel->getUsersInGroupsets($sets , $start, $limit_per_page, false);
			$this->view_data['viewBy'] = 'user';
		}
		
		foreach ($this->view_data['users_list'] as $key => $user) {
		
			switch($user->usertype) {
				case "admin":
					$usersets = $this->usersmodel->getGroupsetsForUser($user->id);
					$this->view_data['users_list'][$key]->groupsets = $usersets;
				break;
				case "limited":
					$usergrps = $this->usersmodel->getGroupsForUser($user->id);
					$this->view_data['users_list'][$key]->groups = $usergrps;
				break;
			}
		}

		$config['base_url']     = site_url('dam_controllers/damusers');   // or just /users/
		$config['total_rows']   = $this->usersmodel->table_record_count;
		$config['per_page']     = $limit_per_page;

		$this->pagination->initialize($config);

		$this->view_data['page_links'] = $this->pagination->create_links();

		$this->layout->buildPage('/users/usersgrid', $this->view_data);


	}

	public function add() {
		$this->load->model('usersmodel');
		$submit = $this->input->post('Submit');
	
		//presubmit checks
		if ( $submit != false ) {
			$data = $this->_get_form_values('add');
			
			if ( $this->usersmodel->find( array('username' => $data->username)) ) {
				//username in use
				$submit = false;
				$this->view_data['flasherror'] = 'This username is already in use, please select a different one and try again.';
			} else if ( $this->usersmodel->find( array('emailaddress' => $data->emailaddress)) ) {
				//email in use
				$submit = false;
				$this->view_data['flasherror'] = 'This emailaddress already has an account associated with it.';
			}
			
		}
	
		if ( $submit != false ) {
			//encrypt password
			$data->password = md5($data->password);
			$data->accountid = $this->authentication->getAccountId();
			//stops any users creating usertypes they arent allowed to.
			//reverts back to limited user type.
			
			if ($data->usertype == 'super' && !$this->authentication->isUserType('super') ) $data->usertype = 'limited';
			else if ($data->usertype == 'admin' && !$this->authentication->isUserType( array('super', 'admin') ) ) $data->usertype = 'limited';

			$newid = $this->usersmodel->add((array)$data);
			
			//creating admin user - so add the groupsets they are involved with.
			if ($data->usertype == 'admin') $this->usersmodel->setGroupsets($newid, $this->input->post('groupsetsid'));
			//creating limited user - so add the groups they are involved with.
			else if ($data->usertype == 'limited') $this->usersmodel->setGroups($newid, $this->input->post('groupsid'));
			
			$this->db_session->set_flashdata('newid', $newid);
			$this->db_session->set_flashdata('notification', 'Your new user was created successfully.');
			
			
			redirect($this->package.'/damusers/', 'location');
		} else {
			$this->view_data['user'] = $this->_clear_form();
			
			$this->view_data['groupsets'] = $this->usersmodel->getGroupsets();
			$this->view_data['usersets'] = false;
			
			$this->view_data['groups'] = $this->usersmodel->getGroupsets($this->authentication->getUserId());
			
			$this->view_data['selectGroupset'] = true;
			$this->view_data['usergroups'] = array();
			
			$this->view_data['action'] = 'add';
			$this->layout->buildPage('/users/usersdetails', $this->view_data);
		}
	}

	public function modify($idField) {
		$this->load->model('usersmodel');
		$this->load->helper('url');
		
		$submit = $this->input->post('Submit');
		
		if ( $submit != false ) {			
			$data = $this->_get_form_values();
			
			if (strlen($data->password)) $data->password = md5($data->password);
			else unset($data->password);
			
			//stops any users creating usertypes they arent allowed to.
			//reverts back to limited user type.
			if ($data->usertype == 'super' && !$this->authentication->isUserType('super') ) unset($data->usertype);
			else if ($data->usertype == 'admin' && !$this->authentication->isUserType( array('super', 'admin') ) ) unset($data->usertype);
			
			$this->usersmodel->modify($data->id, (array)$data);

			//creating admin user - so add the groupsets they are involved with. - only super users can do this
			if ($this->authentication->isUserType('super') && $data->usertype == 'admin') $this->usersmodel->setGroupsets($data->id, $this->input->post('groupsetsid'));
			//creating limited user - so add the groups they are involved with.
			else if ($data->usertype == 'limited') $this->usersmodel->setGroups($data->id, $this->input->post('groupsid'));

			$this->db_session->set_flashdata('newid', $data->id);
			$this->db_session->set_flashdata('notification', 'The user was updated successfully.');
			
			redirect($this->package.'/damusers/', 'location');
		} else {

			$this->view_data['user'] = $this->usersmodel->retrieve_by_pkey($idField);
			$this->view_data['action'] = 'modify';
			
			
			$this->view_data['groupsets'] = $this->usersmodel->getGroupsets($this->authentication->getUserId());
			$usersets = $this->usersmodel->getGroupsetsForUser($idField);
			$this->view_data['usersets'] = array();
			
			if ($usersets) foreach($usersets as $set) $this->view_data['usersets'][$set->id] = 1; 
			
			$this->view_data['groups'] = $this->usersmodel->getGroupsets($this->authentication->getUserId());
			$usergrps = $this->usersmodel->getGroupsForUser($idField);
			
			$this->view_data['usergroups'] = array();
			if ($usergrps) foreach($usergrps as $grp) $this->view_data['usergroups'][$grp->id] = 1;
			
			//if ($this->authentication->isUserType('admin')) $this->view_data['selectGroupset'] = true;
			//else 
			$this->view_data['selectGroupset'] = true;
			
			$this->layout->buildPage('/users/usersdetails', $this->view_data);
		}
	}

	public function addGroupSet() {
		$this->load->model('basemodel');
		$this->basemodel->setModel('groupsets');
		
		$data = null;
		$data->setname = $this->input->post('setname');
		
		if(strlen($data->setname)) {
			$gsid = $this->basemodel->add($data);
			
			$grdata = null;
			$grdata->groupsetsid = $gsid;
			$grdata->grouptitle = $data->setname;
			$grdata->groupname = url_title($grdata->grouptitle);
			
			$this->basemodel->setModel('groups');
			$groupid = $this->basemodel->add($grdata);
			
			if (!$this->ajax) redirect('dam_controllers/damusers/add');
			else {
				include ('./Zend/Json.php');
				echo Zend_Json::encode(array('groupsetid' => $gsid, 'setname' => $data->setname, 'groupid' => $groupid, 'grouptitle' => $data->setname));
				exit();
			}
			
		}
	}
	
	public function addGroupToSet() {
		$this->load->model('basemodel');
		$this->basemodel->setModel('groups');
		
		$data = null;
		$data->groupsetsid = $this->input->post('groupsetid');
		$data->grouptitle = $this->input->post('grouptitle');
		$data->groupname = url_title($data->grouptitle);
		
		if(strlen($data->grouptitle) && intval($data->groupsetsid)) {
			$newgroupid = $this->basemodel->add($data);
		
			if (!$this->ajax) redirect('dam_controllers/damusers/add');
			else {
				include ('./Zend/Json.php');
				echo Zend_Json::encode(array('groupid' => $newgroupid, 'grouptitle' => $data->grouptitle, 'groupname' => $data->groupname));
				exit();
			}
		}
	}

	public function delete($idField) {
		$this->load->model('usersmodel');
		
		$user = $this->usersmodel->retrieve_by_pkey($idField);
		$curruser = $this->usersmodel->retrieve_by_pkey($this->authentication->getUserId());
		
		$canDelete = false;
		
		if ($curruser->usertype === 'super') $canDelete = true;
		elseif ($curruser->usertype === 'admin') {	
			
			$sets = $this->usersmodel->getGroupsetsForUser($idField);

			$cursets = $this->usersmodel->getGroupsetsForUser($this->authentication->getUserId());
			
			if ($sets && $cursets) {
			
				foreach ($sets as $key=>$value){
					if (!in_array($value,$cursets)){
						unset($sets[$key]);
					}
				}
				
				if ($sets) $canDelete = true;
			}
		}

		if ($canDelete) {
			
			$ok = $this->usersmodel->modify($idField, array('deleted' => 1));
			
			if (!$this->ajax) {
				$this->db_session->set_flashdata('flashmessage', $user->firstname .' '.$user->lastname.' was deleted.');
				redirect('dam_controllers/damusers/');
			} else {
				include ('./Zend/Json.php');
				echo Zend_Json::encode(array('userid' => $idField, 'deleted' => true, 'message' => $user->firstname .' '.$user->lastname.' was deleted.'));
				exit();
			}
			
		} else {
			if (!$this->ajax) {
				$this->db_session->set_flashdata('flasherror', 'You don\'t have permission to delete this user');
				redirect('dam_controllers/damusers/');
			} else {
				include ('./Zend/Json.php');
				echo Zend_Json::encode(array('userid' => $idField, 'deleted' => false, 'error' => 'You don\'t have permission to delete this user'));
				exit();
			}
		
		}
	}

	public function delete_ajax() {
		$this->load->model('usersmodel');

		$deleteItems = $this->input->post('delete');

		foreach ($deleteItems as $idField) {
			$data['deleted'] = 1;
			$this->usersmodel->modify($idField, $data);
		}
		echo 1; die();
	}

	public function undelete_ajax() {
		$this->load->model('usersmodel');

		$deleteItems = $this->input->post('delete');

		foreach ($deleteItems as $idField) {
			$data['deleted'] = 0;
			$this->usersmodel->modify($idField, $data);
		}
		echo 1; die();
	}
	
	
	public function activate($idField) {
		$this->load->model('usersmodel');
		
		$user = $this->usersmodel->retrieve_by_pkey($idField);
		$curruser = $this->usersmodel->retrieve_by_pkey($this->authentication->getUserId());
		
		$canSet = false;
		
		if ($curruser->usertype === 'super') $canSet = true;
		elseif ($curruser->usertype === 'admin') {	
			
			$sets = $this->usersmodel->getGroupsetsForUser($idField);

			$cursets = $this->usersmodel->getGroupsetsForUser($this->authentication->getUserId());
			
			if ($sets && $cursets) {
			
				foreach ($sets as $key=>$value){
					if (!in_array($value,$cursets)){
						unset($sets[$key]);
					}
				}
				
				if ($sets) $canSet = true;
			}
		}

		if ($canSet) {
			
			$ok = $this->usersmodel->modify($idField, array('active' => 1));
			
			if (!$this->ajax) {
				$this->db_session->set_flashdata('flashmessage', $user->firstname .' '.$user->lastname.' was activated.');
				redirect('dam_controllers/damusers/');
			} else {
				include ('./Zend/Json.php');
				echo Zend_Json::encode(array('userid' => $idField, 'deleted' => true, 'message' => $user->firstname .' '.$user->lastname.' was activated.'));
				exit();
			}
			
		} else {
			if (!$this->ajax) {
				$this->db_session->set_flashdata('flasherror', 'You don\'t have permission to activate this user');
				redirect('dam_controllers/damusers/');
			} else {
				include ('./Zend/Json.php');
				echo Zend_Json::encode(array('userid' => $idField, 'deleted' => false, 'error' => 'You don\'t have permission to activate this user'));
				exit();
			}
		
		}
	}
	
		public function deactivate($idField) {
		$this->load->model('usersmodel');
		
		$user = $this->usersmodel->retrieve_by_pkey($idField);
		$curruser = $this->usersmodel->retrieve_by_pkey($this->authentication->getUserId());
		
		$canSet = false;
		
		if ($curruser->usertype === 'super') $canSet = true;
		elseif ($curruser->usertype === 'admin') {	
			
			$sets = $this->usersmodel->getGroupsetsForUser($idField);

			$cursets = $this->usersmodel->getGroupsetsForUser($this->authentication->getUserId());
			
			if ($sets && $cursets) {
			
				foreach ($sets as $key=>$value){
					if (!in_array($value,$cursets)){
						unset($sets[$key]);
					}
				}
				
				if ($sets) $canSet = true;
			}
		}

		if ($canSet) {
			
			$ok = $this->usersmodel->modify($idField, array('active' => 0));
			
			if (!$this->ajax) {
				$this->db_session->set_flashdata('flashmessage', $user->firstname .' '.$user->lastname.' was deactivated.');
				redirect('dam_controllers/damusers/');
			} else {
				include ('./Zend/Json.php');
				echo Zend_Json::encode(array('userid' => $idField, 'deleted' => true, 'message' => $user->firstname .' '.$user->lastname.' was deactivated.'));
				exit();
			}
			
		} else {
			if (!$this->ajax) {
				$this->db_session->set_flashdata('flasherror', 'You don\'t have permission to deactivate this user');
				redirect('dam_controllers/damusers/');
			} else {
				include ('./Zend/Json.php');
				echo Zend_Json::encode(array('userid' => $idField, 'deleted' => false, 'error' => 'You don\'t have permission to deactivate this user'));
				exit();
			}
		
		}
	}
	
	
	private function _clear_form() {
		$data->id			= '';
		$data->username		= '';
		$data->password		= '';
		$data->emailaddress	= '';
		$data->firstname	= '';
		$data->lastname		= '';
		$data->openid_identifier = '';
		$data->profiletype	= '';
		$data->usertype		= 'limited';
		$data->lastlogin	= '';
		$data->active		= 1;
		$data->datecreated	= '';
		$data->dateupdated	= '';
		$data->creator		= '';
		$data->updater		= '';

		return $data;

	}

	private function _get_form_values($action = null) {
		$data->id			= $this->input->post('id', TRUE);
		$data->username		= $this->input->post('username', TRUE);
		$data->password		= $this->input->post('password', TRUE);
		$data->emailaddress	= $this->input->post('emailaddress', TRUE);
		$data->firstname	= $this->input->post('firstname', TRUE);
		$data->lastname		= $this->input->post('lastname', TRUE);
		$data->profiletype	= $this->input->post('profiletype', TRUE);
		$data->usertype		= $this->input->post('usertype', TRUE);
		$data->openid_identifier = $this->input->post('openid_identifier', TRUE);
		
		$data->active		= $this->input->post('active', TRUE);
		
		if ( $action == 'add') {
			$data->datecreated = date('Y-m-d H:i:s');
			$data->creatorid = $this->authentication->getUserId();
		} else {
			$data->dateupdated = date('Y-m-d H:i:s');
			$data->updaterid = $this->authentication->getUserId();
		}

		return $data;

	}

}
?>