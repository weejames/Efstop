<?php  
class Authentication {
	
	private $CI;
    /**
     * Constructor
     *
     * @access	public
     */    
    public function Authentication() {
        log_message('debug','Authentication class initialized');
        $this->CI =& get_instance();
        $this->CI->load->model('authentication_model');
    }
    
    // --------------------------------------------------------------------

	public function isLoggedIn() {
		return $this->CI->db_session->userdata('loggedin');
	}

	public function isUser($username, $password = '') {
		if ($this->CI->authentication_model->isUser($username, $password)) return true;
		else return false;
	}
	
	public function isInGroup($groupname){
		$groups = $this->CI->db_session->userdata('groups');
		foreach ($groups as $group) if ($groupname == $group->groupname) return true;
		return false;
	}
	
	public function getGroups(){
		return $this->CI->db_session->userdata('groups');
	}
	
	public function getUserFullName() {
		return $this->CI->db_session->userdata('firstname')." ".$this->CI->db_session->userdata('lastname');
	}
	
	public function getUsername() {
		return $this->CI->db_session->userdata('username');
	}
	
	public function getUserEmailaddress() {
		return $this->CI->db_session->userdata('emailaddress');
	}
	
	public function getUserId() {
		return $this->CI->db_session->userdata('id');
	}
	
	public function getProfileType() {
		return $this->CI->db_session->userdata('profiletype');
	}
	
	public function getAccountId() {
		return $this->CI->db_session->userdata('accountid');
	}
	
	public function getUserType() {
		return $this->CI->db_session->userdata('usertype');
	}
	
	public function isUserType($types) {		
		$usertype = $this->CI->db_session->userdata('usertype');
		
		if (is_array($types) && array_search($usertype, $types) !== false) return true;
		else if ($types == $usertype) return true;
		else return false;
	}
	
	public function logout() {
		return $this->CI->db_session->unset_userdata(array('loggedin' => 1 ,
					'username' => 1,
					'password' => 1,
					'emailaddress' => 1,
					'firstname' => 1,
					'lastname' => 1,
					'lastlogin' => 1,
					'datecreated' => 1,
					'profiletype' => 1,
					'accountid' => 0,
					'groups' => 1));
	}
	
	public function login($username, $password, $emailaddress = false, $openid_identifier = false) {
		if ( $username && $password && $this->CI->authentication_model->isUser($username, $password)) {
			$userDetails = $this->CI->authentication_model->getUserDetails($username);
		} else if ($emailaddress && $password && $this->CI->authentication_model->isUser(false, $password, $emailaddress)) {
			$userDetails = $this->CI->authentication_model->getUserDetails(false, $emailaddress);
		} else if ($openid_identifier && $this->CI->authentication_model->isUser(false, false, false, $openid_identifier)) {
			$userDetails = $this->CI->authentication_model->getUserDetails(false, false, $openid_identifier);
		} else return false;
		
		$this->CI->db_session->set_userdata((array)$userDetails);
		$this->CI->db_session->set_userdata('loggedin', true);
		
		$groups = $this->CI->authentication_model->getUserGroups($username);
		$this->CI->db_session->set_userdata('groups', $groups);

		return true;
	}
	
}

// EOF
?>