<?
if (file_exists(APPPATH.'models/basemodel.php')) include_once(APPPATH.'models/basemodel.php');

class ImageSetModel extends BaseModel {

	public function __construct() {
		parent::__construct();
		$this->setModel('imagesets');
		$this->load->database();
	}
	
	public function getImageSetsForUser($userid, $limit = 0) {
		//get usertype for userid
		$sql = "SELECT usertype FROM users U WHERE id = ".intval($userid)." LIMIT 1";
		
		$rs = $this->db->query($sql)->result();

		if (count($rs) && $rs[0]->usertype == 'super') {
			$sql = "SELECT I.id, I.setname
					FROM imagesets I";
		} else {
			//access is limited so only get imagesets this user is allowed to get
			$sql = "SELECT Z.* FROM
					(SELECT I.id, I.setname
					FROM imagesets I LEFT JOIN imagesets_access IA ON (I.id = IA.imagesetsid AND IA.usersid = ".intval($userid).")
					WHERE I.creatorid = ".intval($userid)." 
						OR IA.usersid = ".intval($userid)."

					UNION

					SELECT I.id, I.setname
					FROM imagesets I INNER JOIN imagesets_access IA ON (I.id = IA.imagesetsid )
						LEFT JOIN users_groups UG ON (IA.groupsid = UG.groupsid AND UG.usersid = ".intval($userid)." )
					WHERE UG.usersid = ".intval($userid)."

					UNION

					SELECT I.id, I.setname
					FROM imagesets I INNER JOIN imagesets_access IA ON (I.id = IA.imagesetsid )
						LEFT JOIN groups G ON (IA.groupsid = G.id)
						LEFT JOIN users_groupsets UG ON (G.groupsetsid = UG.groupsetsid AND UG.usersid = ".intval($userid)." )
					WHERE UG.usersid = ".intval($userid) .") Z ";
		}
		
		if ($limit) $sql .= " LIMIT ".$limit;
		
		$rs = $this->db->query($sql)->result();
		
		if (count($rs)) return $rs;
		else return false;
	}
	
	public function canUserAccessImageset($userid, $imagesetid) {
		//get usertype for userid
		$sql = "SELECT usertype FROM users U WHERE id = ".intval($userid)." LIMIT 1";
		
		$rs = $this->db->query($sql)->result();

		if (count($rs) && $rs[0]->usertype == 'super') {
			return true;
		} else {	
		
			$sql = "SELECT IA.full
					FROM imagesets_access IA
					WHERE IA.imagesetsid = ".intval($imagesetid)."
						AND IA.usersid = ".intval($userid)."
				
					UNION

					SELECT IA.full
					FROM imagesets_access IA
						LEFT JOIN users_groups UG ON (IA.groupsid = UG.groupsid)
					WHERE IA.imagesetsid = ".intval($imagesetid)."
					AND IA.usersid IS NULL
					AND UG.usersid = ".intval($userid)."
				
					UNION
				
					SELECT IA.full
					FROM imagesets_access IA
						LEFT JOIN groups G ON (IA.groupsid = G.id)
						LEFT JOIN users_groupsets UG ON (G.groupsetsid = UG.groupsetsid )
					WHERE IA.imagesetsid = ".intval($imagesetid)."
						AND IA.usersid IS NULL
						AND UG.usersid = ".intval($userid)."
					";
					
			$rs = $this->db->query($sql)->result();

			if (count($rs)) return true;
			else return false;
			
		}
	}
	
	public function userHasFullAccessToImageset($userid, $imagesetid) {
		//get usertype for userid
		$sql = "SELECT usertype FROM users U WHERE id = ".intval($userid)." LIMIT 1";
		
		$rs = $this->db->query($sql)->result();

		if (count($rs) && $rs[0]->usertype == 'super') {
			return true;
		} else {
		
			$sql = "SELECT IA.full
					FROM imagesets_access IA
					WHERE IA.imagesetsid = ".intval($imagesetid)."
						AND IA.usersid = ".intval($userid)."
				
					UNION

					SELECT IA.full
					FROM imagesets_access IA
						LEFT JOIN users_groups UG ON (IA.groupsid = UG.groupsid)
					WHERE IA.imagesetsid = ".intval($imagesetid)."
						AND IA.usersid IS NULL
						AND UG.usersid = ".intval($userid)."

					UNION

					SELECT IA.full
					FROM imagesets_access IA
						LEFT JOIN groups G ON (IA.groupsid = G.id)
						LEFT JOIN users_groupsets UG ON (G.groupsetsid = UG.groupsetsid )
					WHERE IA.imagesetsid = ".intval($imagesetid)."
						AND IA.usersid IS NULL
						AND UG.usersid = ".intval($userid)."
				
					ORDER BY full DESC	
					LIMIT 1
					";
					
			$rs = $this->db->query($sql)->result();

			if (count($rs) && $rs[0]->full) return true;
			else return false;
		}
	}
	
	public function whoHasAccessToImageset($imagesetid) {
		$sql = "SELECT L.*, LA.*, U.firstname, U.lastname, '' AS grouptitle
				FROM imagesets L LEFT JOIN imagesets_access LA ON (L.id = LA.imagesetsid)
					LEFT JOIN users U ON (U.id = LA.usersid)
				WHERE LA.imagesetsid = ".intval($imagesetid)."
					AND LA.groupsid IS NULL
					
				UNION
				
				SELECT I.*, IA.*, '' AS firstname, '' AS lastname, G.grouptitle
				FROM imagesets I INNER JOIN imagesets_access IA ON (I.id = IA.imagesetsid)
				LEFT JOIN groups G ON (IA.groupsid = G.id)
				WHERE IA.imagesetsid = ".intval($imagesetid)."
					AND IA.usersid IS NULL";
				
		$rs = $this->db->query($sql)->result();

		if (count($rs)) return $rs;
		else return false;
	}
	
	public function delete($setid) {		
		$sql = "DELETE Z, X FROM imagesets Z LEFT JOIN imagesets_access X ON (Z.id = X.imagesetsid)
				WHERE Z.id = ".intval($setid);
				
		$this->db->query($sql);
		
		$sql = "UPDATE images
				SET imagesetid = 0
				WHERE imagesetid = ".intval($setid);
				
		$this->db->query($sql);
		return true;
	}
	
/*
	ALTER TABLE `imagesets` ADD `updaterid` int(11) DEFAULT NULL ;
	*/	
	
}
?>