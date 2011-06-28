<?php
if (file_exists(APPPATH.'models/basemodel.php')) include_once(APPPATH.'models/basemodel.php');

class AccountsModel extends BaseModel {

	public function __construct() {
		parent::__construct();
		$this->setModel('accounts');
		$this->load->database();
	}

	public function getTotalSpaceUsed($accountid, $includes3 = 1, $includelocal = 1) {
		$sql = "SELECT SUM(filesize) as totalsize
				FROM images
				WHERE accountid = 1";
				
		if (!$includes3 && $includelocal) {
			$sql .= " AND s3process = 0 ";
		} else if ($includes3 && !$includelocal) {
			$sql .= " AND s3process = 1 ";
		}
	
		$rs = $this->db->query($sql)->result();
		
		if ($rs) return $rs[0]->totalsize;
		else return 0;
		
	}
	
	
	public function getExpiredAccounts($date = false) {
		$sql = "SELECT A.*
				FROM accounts A
				WHERE A.enddate < ";
		
		if ($date) $sql .= $date;
		else $sql .= " NOW() ";
				
		$sql .=	"	AND A.disabled = 0";
					
		$rs = $this->db->query($sql)->result();
		
		if (count($rs)) return $rs;
		else return false;
	}
	
	public function disableAccount($accountid) {
		$sql = "UPDATE accounts SET disabled = 1 WHERE id = ".intval($accountid);
					
		$this->db->query($sql);
		
		return true;
	}
	
	// ALTER TABLE `users` ADD `openid_identifier` varchar(250) DEFAULT NULL ;
}
?>
