<?php

class Admin extends Controller {
    protected $theme = 'admin';
    protected $view_data = array();
    protected $package;
    protected $current_module;
	protected $ajax;
    
	public function Admin($requirelogin = 1) {
		parent::Controller();
		if (defined('ENABLE_PROFILER') && ENABLE_PROFILER) $this->output->enable_profiler();
		
		//set admin theme.  we can move this to a database in the future if needed
		if ($this->config->item('admin_theme')) $this->theme = $this->config->item('admin_theme');
		
		
        $this->load->library('authentication');
		$this->layout->resetTheme($this->theme);
		
		$this->ajax = $this->input->post('ajax');
		
		$this->view_data['ajax'] = $this->ajax;
		
		$this->view_data['package'] = $this->package;
		
		$this->view_data['page_css'] = array('reset-min','common','adminlayout','components','datepicker', 'jquery.tablesorter');
		$this->view_data['page_js'] = array('jquery-1.1.4.pack', 'interface', 'datepicker', 'cmxforms' ,'jquery.metadata', 'jquery.validate', 'jquery.multifile', 'jquery.tablesorter', 'local_init', 'init');
		
		$this->view_data['showQuickaccess'] = false;
		
		if ($this->ajax) $this->view_data['showContent'] = false;
		else $this->view_data['showContent'] = true;
		
		$this->view_data['hidemenu'] = false;
		
		$this->view_data['flashmessage'] = $this->db_session->flashdata('flashmessage');
		$this->view_data['flasherror'] = $this->db_session->flashdata('flasherror');
		$this->view_data['newid'] = $this->db_session->flashdata('newid');

		$this->view_data['clientname'] = $this->config->item('company_name');
		
		$this->view_data['activesection'] = $this->current_module;
		
		$this->view_data['page_title'] = '';
		
		switch(APPTYPE) {
			case "crm":
				$this->view_data['modules'] = array( 'Clients' => site_url('clients/'), 'Projects' => site_url('projects/'));
			break;
			case "cms":
				$this->view_data['modules'] = array( 'Home' => site_url('/cms'), 'All Content' => site_url('cms/content'), 'User Administration' => site_url('cms/useradmin'), 'Site Configuration' => site_url('cms/siteconfiguration'),);
			break;
		}
		
		//if (!$this->_checkAccess()) show_404();
		
		if ($requirelogin) {
			$this->db_session->set_userdata('target_url', $this->uri->uri_string());
			$this->_checkAuthentication();
		}
    }

    public function help($topic = '') {
    	if (!strlen($topic)) $topic = 'main';
    	
    	$this->current_module = 'Help';
    	$this->view_data['activesection'] = $this->current_module;
    	
    	$this->layout->buildPage( 'help/' . strtolower(get_class($this)) . '/' . $topic, $this->view_data);
    }
    
    public function addField_ajax() {
    	$this->load->model('basemodel');
    	$this->load->model('friendly_urlsmodel');

    	$fieldname = $this->input->post('field', TRUE);
    	$contenttype = $this->input->post('contenttype', TRUE);
    	$value = $this->input->post('value', TRUE);
    	
    	$this->basemodel->setModel($contenttype);
    	
    	if ( !$this->basemodel->find(array($fieldname => $value)) ) {    	
			$newid = $this->basemodel->add(array($fieldname => $value));
			
			//create furl for item.
			$friendly_url_data = array();
			$friendly_url_data['friendly_url'] = url_title($value);
			$friendly_url_data['content_type'] = $contenttype;
			$friendly_url_data['contentid'] = $newid;
			
			$fieldid = $this->friendly_urlsmodel->add($friendly_url_data);
	
			echo($newid);
		
		} else echo 0;
		
		die();
    }
    
    //checks if the current session user is logged in.
    protected function _checkAuthentication() {
    	if (!$this->authentication->isLoggedIn()) redirect('', 'location');
    }
    
    //checks if the application is allowed to access the module it is requesting
    private function _checkAccess() {
    	global $allowedModules;

    	if ( !isset($allowedModules) || array_key_exists(strtolower(get_class($this)), $allowedModules)) {
    		return true;
    	} else return false;
    }
}

?>
