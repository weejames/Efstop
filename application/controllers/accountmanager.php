<?php 

class AccountManager extends Controller {

	public function __construct() {
		parent::Controller();
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