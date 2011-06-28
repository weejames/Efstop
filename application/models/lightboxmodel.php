<?
if (file_exists(APPPATH.'models/basemodel.php')) include_once(APPPATH.'models/basemodel.php');

class LightboxModel extends BaseModel {

	public function __construct() {
		parent::__construct();
		$this->setModel('lightbox');
		$this->load->database();
	}

	public function addImageToLightBox($imageid, $lightboxid, $creatorid, $datecreated) {
		$sql = "SELECT id FROM images_lightbox WHERE imagesid = ".intval($imageid)." AND lightboxid = ".intval($lightboxid);
		
		$rs = $this->db->query($sql)->result();

		if (count($rs)) return 0;
		else {
			$sql = "INSERT INTO images_lightbox SET imagesid = ".intval($imageid).", lightboxid = ".intval($lightboxid).", creatorid = ".intval($creatorid).", datecreated = ".$this->db->escape($datecreated);

			$this->db->query($sql);
			
			return 1;
		}
	}

	public function getMyLightboxes($userid, $limit = 0) {
		$sql = "SELECT DISTINCT Z.id, Z.boxtitle, Z.full FROM
		
				(SELECT DISTINCT L.id, L.boxtitle, 1 as full
				FROM lightbox L
				WHERE L.creatorid = ".intval($userid)."
				
				UNION

				SELECT L.id, L.boxtitle, LA.full
				FROM lightbox L INNER JOIN lightbox_access LA ON (L.id = LA.lightboxid )
					LEFT JOIN users_groups UG ON (LA.groupsid = UG.groupsid AND UG.usersid = ".intval($userid)." )
				WHERE UG.usersid = ".intval($userid)."

				UNION

				SELECT L.id, L.boxtitle, LA.full
				FROM lightbox L INNER JOIN lightbox_access LA ON (L.id = LA.lightboxid)
					LEFT JOIN groups G ON (LA.groupsid = G.id)
					LEFT JOIN users_groupsets UG ON (G.groupsetsid = UG.groupsetsid AND UG.usersid = ".intval($userid)." )
				WHERE UG.usersid = ".intval($userid)."
				
				ORDER BY boxtitle, full ) Z";
		
		$sql .= " GROUP BY Z.id";
		
		if ($limit) $sql .= " LIMIT ".$limit;
		
		$rs = $this->db->query($sql)->result();

		if (count($rs)) return $rs;
		else return false;
	}
	
	public function retrieve_by_guestkey($guestkey) {
		$sql = "SELECT DISTINCT L.id, L.boxtitle, LA.full, LA.datecreated
				FROM lightbox L LEFT JOIN lightbox_access LA ON (L.id = LA.lightboxid)
				WHERE LA.guestkey = ".$this->db->escape($guestkey)." 
				LIMIT 1";
		
		$rs = $this->db->query($sql)->result();

		if (count($rs)) return $rs[0];
		else return false;
	}
	
	public function userHasFullAccessToLightbox($userid, $lightboxid) {
		$sql = "SELECT LA.full
				FROM lightbox_access LA
				WHERE LA.lightboxid = ".intval($lightboxid)."
					AND LA.usersid = ".intval($userid)."
				
				UNION

				SELECT LA.full
				FROM lightbox L INNER JOIN lightbox_access LA ON (L.id = LA.lightboxid )
					LEFT JOIN users_groups UG ON (LA.groupsid = UG.groupsid AND UG.usersid = ".intval($userid)." )
				WHERE UG.usersid = ".intval($userid)."

				UNION

				SELECT LA.full
				FROM lightbox L INNER JOIN lightbox_access LA ON (L.id = LA.lightboxid)
					LEFT JOIN groups G ON (LA.groupsid = G.id)
					LEFT JOIN users_groupsets UG ON (G.groupsetsid = UG.groupsetsid AND UG.usersid = ".intval($userid)." )
				WHERE UG.usersid = ".intval($userid)."
					
				ORDER BY full DESC
				LIMIT 1";
					
		$rs = $this->db->query($sql)->result();

		if (count($rs) && $rs[0]->full) return true;
		else return false;
	}
	
	public function whoHasAccessToLightbox($lightboxid) {
		$sql = "SELECT L.*, LA.*, U.firstname, U.lastname, G.grouptitle
				FROM lightbox L LEFT JOIN lightbox_access LA ON (L.id = LA.lightboxid)
					LEFT JOIN users U ON (U.id = LA.usersid)
					LEFT JOIN groups G ON  (LA.groupsid = G.id)
				WHERE LA.lightboxid = ".intval($lightboxid);
				
		$rs = $this->db->query($sql)->result();

		if (count($rs)) return $rs;
		else return false;
	}
	
	public function removeImageFromLightbox($imageid, $lightboxid = 0) {
		$sql = "DELETE FROM images_lightbox WHERE imagesid = ".intval($imageid);
		
		if ($lightboxid) $sql.= " AND lightboxid = ".intval($lightboxid);
		
		$this->db->query($sql);
		return true;
	}
	
	public function delete($lightboxid) {
		$sql = "DELETE Z, X, Y FROM lightbox Z LEFT JOIN lightbox_access X ON (Z.id = X.lightboxid) LEFT JOIN images_lightbox Y ON (Z.id = Y.lightboxid)
				WHERE Z.id = ".intval($lightboxid);
				
		$this->db->query($sql);
		return true;
	}
/*
	ALTER TABLE `lightbox` ADD `updaterid` int(11) DEFAULT NULL ;
	
	ALTER TABLE `savedsearches` ADD `updaterid` int(11) DEFAULT NULL ;
	ALTER TABLE `savedsearches` ADD `creatorid` int(11) DEFAULT NULL ;
*/

}
?>
