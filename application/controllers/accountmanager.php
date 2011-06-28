<?php 

class AccountManager extends MY_Controller {

	public function __construct() {
		parent::__construct();
	}
	
	public function index() {
	
	}
	
	public function monitorAccounts() {
		$this->load->model('accountsmodel');
		
		//get expired accounts
		$expired = $this->accountsmodel->getExpiredAccounts();
		
		//notify adminsitrators
		if ($expired) {
			foreach ($expired as $account) {
				$this->accountsmodel->disableAccount($account->id);
				//send an email here to admin users of account.		
			}
		}
	}
	

}

?>