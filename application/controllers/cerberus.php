<?php

if (file_exists(APPPATH.'controllers/admin.php')) include_once(APPPATH.'controllers/admin.php');

class Cerberus extends Admin {
    
	public function Cerberus() {
        parent::Admin(false);
        $this->load->library('authentication');
        $this->load->helper('cookie');
        
		$this->layout->resetTheme('dam');

		
        $this->view_data['page_title'] = 'login';
        
        if ($this->config->item('email_login') === true) $this->email_login = true;
		else $this->email_login = false;
		
		$this->view_data['email_login'] = $this->email_login;
    }

    public function index() {	
		$this->login();
    }
    
    public function login($lastaction = '') {	
    	$target_url = $this->db_session->userdata('target_url');

		$submit = $this->input->post('submit');
		
		if ($submit) {
		
			$emailaddress = $this->input->post('emailaddress');
			$password = $this->input->post('password');
			$openid_identifier = $this->input->post('openid_identifier');
			
			if ( $emailaddress && $password) {	
				$this->authentication->logout();
				$this->authentication->login(false, $password, $emailaddress);
				
				
				if ($this->authentication->isLoggedIn()) {
					$this->load->model('accountsmodel');

					$account = $this->accountsmodel->retrieve_by_pkey($this->authentication->getAccountId());

					if ($account->disabled) {
						$this->authentication->logout();
						$this->db_session->set_flashdata('flasherror', 'Your account has been disabled.  If you think this is a mistake please contact support.');
						redirect('cerberus/login');
					} else {
					
						$this->load->model('authentication_model');    	
						
						$userupdate = array();
						$userupdate['lastlogin'] = date('Y-m-d H:i:s');
						$userupdate['resetbee'] = '';
						$this->authentication_model->modify($this->authentication->getUserId(), $userupdate);
						
						set_cookie('emailaddress', $this->authentication->getUserEmailaddress(), 86500);
						
						if ($target_url) redirect($target_url);
						else redirect($this->input->post('takemeto', true));

					}					
				} else {
					$this->db_session->set_flashdata('flasherror', 'No account was found matching the details provided.');
					redirect('cerberus/login');
				}
			} elseif ($openid_identifier) {
				require_once "Zend/OpenId/Consumer.php";
				$consumer = new Zend_OpenId_Consumer();
				
				set_cookie('openid_identifier', $openid_identifier, 86500);
				
				if (!$consumer->login($openid_identifier, site_url('cerberus/openid_verify'))) {
					$this->db_session->set_flashdata('flasherror', 'Unable to verify OpenId identifier.');
					redirect('cerberus/login');
				}
			} else {
				$this->db_session->set_flashdata('flasherror', 'No account was found matching the details provided.');
				redirect('cerberus/login');
			}
			
		}

 		$this->view_data['logintargets']['Application'] = 'dam';
		$this->view_data['page_css'] = array('reset-min', 'layout', 'common');
		$this->view_data['showSearch'] = false;
		$this->view_data['showMenu'] = false;
		$this->view_data['page_js'] = array();
        $this->view_data['hidemenu'] = true;
        $this->view_data['showQuickaccess'] = false;
        
        $emailaddress = get_cookie('emailaddress');

        $this->view_data['emailaddress'] = $emailaddress;
	    
	    $this->layout->buildPage('cerberus/login', $this->view_data);
    }
    
    public function openid_verify(){
    	require_once "Zend/OpenId/Consumer.php";
		$consumer = new Zend_OpenId_Consumer();
		
		$openid_identifier = get_cookie('openid_identifier');
		
		//verify openid response
		if ($consumer->verify($_GET, $openid_identifier)) {
			
			//check if user is in database!
			$this->authentication->logout();
        	$this->authentication->login(false, false, false, $openid_identifier);
        	
        	if ($this->authentication->isLoggedIn()) {
        		$this->load->model('accountsmodel');
        		
        		$account = $this->accountsmodel->retrieve_by_pkey($this->authentication->getAccountId());
					
				if ($account->disabled) {
					$this->authentication->logout();
					$this->db_session->set_flashdata('flasherror', 'Your account has been disabled.  If you think this is a mistake please contact support.');
					redirect('cerberus/login');
				} else {
					$this->load->model('authentication_model');    	
					
					$userupdate = array();
					$userupdate['lastlogin'] = date('Y-m-d H:i:s');
					$userupdate['resetbee'] = '';
					$this->authentication_model->modify($this->authentication->getUserId(), $userupdate);
					
					set_cookie('emailaddress', '', 0);
					
					if ($target_url) redirect($target_url);
					else redirect($this->input->post('takemeto', true));
				}      			
        	} else {
        		$this->db_session->set_flashdata('flasherror', 'No account was found matching the OpenId identifier provided.');
				redirect('cerberus/login');
        	}
			
		} else {
			redirect('cerberus/login');
		};
    }
    
	public function logout() {	
        $this->authentication->logout();
        if ($this->authentication->getProfileType() != 'cms') redirect('');
        else redirect('cerberus/login/loggedout');
    }
    
    public function checkuser_ajax() {
    	if ($this->authentication->isUser($_POST['username'])) echo 1;
    	else echo 0;
    	die();
    }
    
    public function sitelogin() {		
    	
    	$username = $this->input->post('username', true);
    	$password = $this->input->post('password', true);
    	
    	$target = $this->input->post('target', true);
    	$errortarget = $this->input->post('errortarget', true);
    	
    	if ($username && $password) {	
        	$this->authentication->logout();
        	$this->authentication->login($_POST['username'], $_POST['password']);
        	
        	if ($this->authentication->isLoggedIn()) {
        		$this->load->model('authentication_model');    	
	        	
        		$userupdate = array();
        		$userupdate['lastlogin'] = date('Y-m-d H:i:s');
        		$this->authentication_model->modify($this->authentication->getUserId(), $userupdate);
        	} else {
        		$this->db_session->set_flashdata('flasherror', "No account was found matching the details you provided.");
        	}
        } else {
        	$this->db_session->set_flashdata('flasherror', "Please provide a username and password.");
        }
        
       redirect($errortarget);
    }

	 public function forgotpassword($sent = 0) {	
		$this->load->library('validation');
		
		//set up validation
		$rules['emailaddress']	= "trim|required|valid_email|callback__hasaccount";
		
		$this->validation->set_rules($rules);
		
		$this->validation->set_error_delimiters('', '');
		
		$fields['emailaddress'] = 'Email Address';

		$this->validation->set_fields($fields);
		
		$errortarget = $this->input->post('errortarget');
		$resettarget = $this->input->post('resettarget');
		
		$submit = $this->input->post('submit');
		
		if ($submit && $this->validation->run()) {
			$this->load->model('usersmodel');
		
			$emailaddress = $this->input->post('emailaddress');
			$target = $this->input->post('target');
		
			$user = $this->usersmodel->retrieve_by_field('emailaddress', $emailaddress);
		
			//generate reset bee and email the person
			$data = null;
			$data->resetbee = md5(uniqid(rand(), true));
			
			$this->usersmodel->modify($user->id, $data);	
			
			$mailMsg = "Hi ".$user->firstname."

Someone has requested a password reset for you at ".site_url().".  If it wasnt you then ignore this email.  Otherwise follow the link below to modify your password.

";

if ($resettarget) $mailMsg .= site_url($resettarget.'&resetbee='.$data->resetbee);
else $mailMsg .= site_url('cerberus/resetpassword/'.$data->resetbee);

$mailMsg .= "

Note:
If you are unable to open the link directly from your email, please copy and paste the URL into your browser.";
			
			
			$this->load->library('email');

			$this->email->from($this->config->item('company_email'), $this->config->item('company_name'));
			$this->email->to($emailaddress);
			
			$this->email->subject('Password Reset Request at '.site_url());
			$this->email->message($mailMsg);
			
			$this->email->send();
			
			$this->db_session->set_flashdata('flashmessage', "We've sent an email to you containing details on how to reset your password.");
			
			redirect($target);
		} elseif ($errortarget) {
			$this->db_session->set_flashdata('flasherror', $this->validation->error_string);
			redirect($errortarget);
		} else {
			$this->view_data['hidemenu'] = true;
	        $this->view_data['showQuickaccess'] = false;
	        $this->validation->set_error_delimiters('<p class="notification-error">', '</p>');
	        
			$this->layout->buildPage('cerberus/forgotpassword', $this->view_data);
		}
    }
    
    public function resetpassword($resetbee = 0) {
    	$this->load->model('usersmodel');
    	
    	$user = $this->usersmodel->retrieve_by_field('resetbee', $resetbee);
    	
    	if (!$user && $resetbee && strlen($resetbee)) redirect('');
    	else {
    		$this->load->library('validation');
    		
    		//set up validation
			$rules['newpassword']	= "trim|required|min_length[6]|max_length[16]";
			$rules['confirmpassword']	= "trim|required|matches[newpassword]";
			
			$this->validation->set_rules($rules);
			
			$this->validation->set_error_delimiters('', '');
			
			$fields['newpassword'] = 'Password';
			$fields['confirmpassword'] = 'Confirm Password';
	
			$this->validation->set_fields($fields);
    	
    		$submit = $this->input->post('submit');
		
			$errortarget = $this->input->post('errortarget');
		
			if ($submit && $this->validation->run()) {
    			$newpassword = $this->input->post('newpassword');
    			$target = $this->input->post('target');
    			
    			$data = null;
    			$data->resetbee = '';
    			$data->password = md5($newpassword);

    			$this->usersmodel->modify($user->id, $data);
    			
    			$this->db_session->set_flashdata('flashmessage', "Your password has been changed!");
			
				redirect($target);
    		} elseif ($errortarget) {
				$this->db_session->set_flashdata('flasherror', $this->validation->error_string);
				redirect($errortarget);
			} else {
    			$this->validation->set_error_delimiters('<p class="notification-error">', '</p>');
    			$this->view_data['hidemenu'] = true;
				$this->view_data['showQuickaccess'] = false;
				$this->view_data['resetbee'] = $resetbee;
				
				$this->layout->buildPage('cerberus/resetpassword', $this->view_data);
    		}
    	}
    	
    	
    }
    
    public function _hasaccount($emailaddress) {
    	$this->load->model('usersmodel');

    	$user = $this->usersmodel->retrieve_by_field('emailaddress', $emailaddress);
    	
    	if ($user) return true;
    	else {
    		$this->validation->set_message('_hasaccount', "Your email address doesn't have an account associated with it.");
    		return false;
    	}
    }

    
}

?>
