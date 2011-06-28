<?
if (file_exists(APPPATH.'models/basemodel.php')) include_once(APPPATH.'models/basemodel.php');

class ImageModel extends BaseModel {

	public function __construct() {
		parent::__construct();
		$this->setModel('images');
		$this->load->database();
	}
	public function getImagesInLightbox($lightboxid, $count = 0) {
		$sql = "SELECT I.*
				FROM images I RIGHT JOIN images_lightbox IL ON (I.id = IL.imagesid)
				WHERE IL.lightboxid = ".intval($lightboxid);
				
        if ($count) $sql .= " LIMIT ".$count;
		
		$rs = $this->db->query($sql)->result();
		
		if (count($rs)) return $rs;
		else return false;
	}
	
	public function getTagList($accountid, $imageids = 0) {
		$sql = "SELECT TG.tag_id, T.tag, C.collection
				FROM tags T INNER JOIN tagged TG ON (T.id = TG.tag_id AND TG.itemtype = 'images') 
						INNER JOIN images I ON (TG.itemid = I.id AND I.accountid = ".intval($accountid).")
						LEFT JOIN collections_tags CT ON (T.id = CT.tags_id) LEFT JOIN collections C ON (CT.collections_id = C.id) ";

		if (is_array($imageids)) $sql .= " WHERE I.id IN (".implode($imageids, ',').")";
						
		$sql .= " GROUP BY TG.tag_id
				ORDER BY T.tag";
		$rs = $this->db->query($sql)->result();

		if (count($rs)) return $rs;
		else return false;
	}
	
	public function searchTermMatch($searchphrase) {
		
		$sql = "SELECT I.id as itemid, 'images' as itemtype
				FROM imagesearch I
				WHERE MATCH (title,description) AGAINST('".$searchphrase."')
					OR title LIKE '%".$searchphrase."%' 
					OR description LIKE '%".$searchphrase."%' ";
		
		$rs = $this->db->query($sql)->result();
		
		if (count($rs)) return $rs;
		else return false;
	}
	
	public function getColourId($colorcode) {
		$sql = "SELECT id
				FROM colours
				WHERE colorcode = ".$this->db->escape($colorcode)."
				LIMIT 1";
				
		$rs = $this->db->query($sql)->result();
		
		if (count($rs)) return $rs[0]->id;
		else {
			$sql = $this->db->insert_string('colours', array('colorcode' => $colorcode));

			$query = $this->db->query($sql);

			return $this->db->insert_id();
		}		
	}
	
	public function setImageColour($imageid, $colorid, $colorcount) {
		$sql = $this->db->insert_string('colours_image', array('coloursid' => $colorid, 'imagesid' => $imageid, 'quantity' => $colorcount));

		$query = $this->db->query($sql);

		return $this->db->insert_id();	
	}
	
	public function addComment($imageid, $commentdata) {
		$sql = $this->db->insert_string('comments', $commentdata);

		$query = $this->db->query($sql);

		return $this->db->insert_id();	
	}
	
	public function getComments($imageid) {
		$sql = "SELECT C.*, U.firstname, U.lastname, U.emailaddress
				FROM comments C LEFT JOIN users U ON (C.userid = U.id)
				WHERE C.imageid = ".intval($imageid)."
				ORDER BY datecreated ASC";

		$rs = $this->db->query($sql)->result();
		if (count($rs)) return $rs;
		else return false;	
	}
	
	public function removeImageColours($imageid) {
		$sql = "DELETE FROM colours_image WHERE imagesid = ".intval($imageid);

		$query = $this->db->query($sql);

		return $this->db->insert_id();	
	}
	
	public function getColours($imageid) {
		$sql = "SELECT CI.quantity, CI.coloursid, C.colorcode
				FROM colours_image CI LEFT JOIN colours C ON (CI.coloursid = C.id)
				WHERE CI.imagesid = ".intval($imageid)."
				ORDER BY CI.quantity DESC";
				
		$rs = $this->db->query($sql)->result();
		if (count($rs)) return $rs;
		else return false;
	}
	
	public function updateSearch($imageid) {
		//check if image already in search
		$sql = "SELECT * FROM imagesearch WHERE id = ".intval($imageid);
		
		$rs = $this->db->query($sql)->result();
		
		if (count($rs)) {
			$sql = "UPDATE imagesearch, images
			 		SET imagesearch.title = images.title,
						imagesearch.description = images.description
					WHERE images.id = ".intval($imageid)."
						AND imagesearch.id = ".intval($imageid);
		} else {
			$sql = "INSERT INTO imagesearch (id, title, description)
					SELECT id, title, description
					FROM images
					WHERE id = ".intval($imageid);
		}
		$this->db->query($sql);
		return true;
	}
	
	public function getCreatorId($sessionid) {
		$sql = "SELECT session_data
				FROM ci_sessions
				WHERE session_id = ".$this->db->escape($sessionid);
				
		$rs = $this->db->query($sql)->result();
		
		if (count($rs)) return unserialize($rs[0]->session_data);
		else return false;
	}

/*
	ALTER TABLE `images` ADD `updaterid` int(11) DEFAULT NULL ;
	*/
}
?>