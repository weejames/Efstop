<?
if (file_exists(APPPATH.'models/basemodel.php')) include_once(APPPATH.'models/basemodel.php');

class NotificationsModel extends BaseModel {

	public function __construct() {
		parent::__construct();
		$this->setModel('notifications');
		$this->load->database();
	}

	public function dismissNotification($notificationid, $userid) {
		if (!$this->isDismissed($notificationid, $userid)) {
			$sql = "INSERT INTO notifications_dismiss SET usersid = ".intval($userid).", notificationsid = ".intval($notificationid);
					
			$this->db->query($sql);
		}
				
		return true;
	}
	
	public function isDismissed($notificationid, $userid) {
		$sql = "SELECT * FROM notifications_dismiss WHERE usersid = ".intval($userid)." AND notificationsid = ".intval($notificationid);
		
		$rs = $this->db->query($sql)->result();
		
		if ($rs) return true;
		else return false;
	}
	
	public function getNotification($notification_ident) {
		$sql = "SELECT * FROM notifications WHERE notification_ident = ".$this->db->escape($notification_ident);
				
		$rs = $this->db->query($sql)->result();
		
		if ($rs) return $rs[0];
		else return false;
	}

}
?>
