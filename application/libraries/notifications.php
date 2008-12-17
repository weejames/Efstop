<?php

class Notifications {
	
	private $CI;
	
	public function Notifications() {
		$this->CI =& get_instance();
	}
	
	public function renderNotification($ident) {
		$this->CI->load->model('notificationsmodel');
		
		$notification = $this->CI->notificationsmodel->getNotification($ident);
		
		if ($notification && !$this->CI->notificationsmodel->isDismissed($notification->id, $this->CI->authentication->getUserId() ) ){
			
			return "<div class=\"notify\" id=\"".$ident."\"><h3>".$notification->notificationtitle."</h3>".$notification->notificationtext."</div>";
						
		} else return '';
	}
	
}
?>