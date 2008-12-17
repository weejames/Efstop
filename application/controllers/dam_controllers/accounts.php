<?php

if (file_exists(APPPATH.'controllers/dam.php')) include_once(APPPATH.'controllers/dam.php');

class LightBox extends DAM {
	
	protected $package = 'dam_controllers';
	protected $current_module = 'View Lightboxes';
	
	public function LightBox() {
		parent::DAM();

		//this is only accessible by super users.
		
		if (!$this->authentication->isUserType('super')) redirect('');

		$this->view_data['page_title'] .= ' - Accounts';
		
	}
	
	public function index() {
		$this->browse();
	}

	public function browse($start = 0) {
		$this->load->model('accountsmodel');
		$this->load->library('pagination');
		$this->load->helper('url');
		
		$limit_per_page = 50;

		$this->view_data['accounts_list'] = $this->accountsmodel->find( null, $start, $limit_per_page);

		$config['base_url']     = site_url($this->package.'accounts');
		$config['total_rows']   = $this->accountsmodel->table_record_count;
		$config['per_page']     = $limit_per_page;

		$this->pagination->initialize($config);

		$this->view_data['page_links'] = $this->pagination->create_links();

		$this->layout->buildPage('/accounts/accountsgrid', $this->view_data);


	}

	public function add() {
		$this->load->model('usersmodel');
		$submit = $this->input->post('Submit');
	
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

	public function delete() {
		$idField = $this->uri->segment(3);

		$this->load->model('usersmodel');
		$ok = $this->usersmodel->delete_by_pkey($idField);

		$this->load->helper('url');
		redirect('/users/', 'location');

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
	
	private function _clear_form() {
		$data->id			= '';
		$data->username		= '';
		$data->password		= '';
		$data->emailaddress	= '';
		$data->firstname	= '';
		$data->lastname		= '';
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