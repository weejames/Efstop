<?
if (file_exists(APPPATH.'models/basemodel.php')) include_once(APPPATH.'models/basemodel.php');

class UsersModel extends BaseModel {
	/**
	 * MODULE NAME   : usersmodel.php
	 *
	 * DESCRIPTION   : Users model controller
	 *
	 * MODIFICATION HISTORY
	 *   V1.5   2007-09-11 11:35	  - James Constable		- Modified to user Basemodel.
	 *														- Groups/Groupsets Added
	 *   V1.0   2007-04-18 03:10 PM   - James Constable     - Created
	 *
	 * @package             users
	 * @subpackage          Users model component Class
	 * @author              James Constable
	 * @copyright           Copyright (c) 2007 oceanseventy
	 * @since               Version 1.0
	 * @filesource
	 */

	public function __construct() {
		parent::__construct();
		$this->setModel('users');
	}

	public function getUserActivity() {
		$sql = "SELECT S.* ".
				" FROM ci_sessions S ".
				" ORDER BY last_activity DESC"; 
		
		$rs = $this->db->query($sql)->result_array();

		foreach ($rs as $key => $row) {
			if (strlen($row['session_data'])) {
				$sessionData = @unserialize($row['session_data']);
				$rs[$key]['sessionData'] = $sessionData;
			} else $rs[$key]['sessionData'] = false;
			
		}
		
		return ($rs);
	}
	
	public function getRecentReferrers() {
		$sql = "SELECT S.referrer, S.last_activity 
				FROM ci_sessions S 
				WHERE S.referrer != '0'
					AND S.referrer != ''
					AND S.referrer IS NOT NULL
				GROUP BY S.referrer
				ORDER BY S.last_activity DESC
				LIMIT 5"; 
		
		$rs = $this->db->query($sql)->result();
		
		if (count($rs)) return $rs;
		else return false;
	}

	public function getUsersByGroupSet($start = 0, $limit_per_page = 0) {
		$sql = "SELECT U.*, G.setname
				FROM users U INNER JOIN users_groupsets UG ON (U.id = UG.usersid) LEFT JOIN groupsets G ON (UG.groupsetsid = G.id)
				WHERE U.deleted = 0
				
				UNION
				
				SELECT U.*, GS.setname
				FROM users U INNER JOIN users_groups UG ON (U.id = UG.usersid)
					LEFT JOIN groups G ON (UG.groupsid = G.id)
					LEFT JOIN groupsets GS ON (G.groupsetsid = GS.id)
				WHERE U.deleted = 0
				
				UNION
				
				SELECT U.*, ' Super Users' AS setname
				FROM users U
				WHERE U.usertype = 'super'
					AND U.deleted = 0
				
				ORDER BY setname";
				
		$rs = $this->db->query($sql)->result();		

		if (count($rs)) return $rs;
		else return false;
	}
	
	public function getUsersInGroupSets($groupsets, $start = 0, $limit_per_page = 0) {
		if (!$groupsets) return false;
		
		$sql = "SELECT U.*, GS.setname
				FROM users U INNER JOIN users_groups UG ON (U.id = UG.usersid)
					INNER JOIN groups G ON (UG.groupsid = G.id)
					INNER JOIN groupsets GS ON (G.groupsetsid = GS.id AND GS.id IN (".implode(',', $groupsets).") )
				WHERE U.deleted = 0
				
				UNION
				
				SELECT U.*, GS.setname
				FROM users U INNER JOIN users_groupsets UG ON (U.id = UG.usersid)
					INNER JOIN groupsets GS ON (UG.groupsetsid = GS.id AND GS.id IN (".implode(',', $groupsets).") )
				WHERE U.deleted = 0
				
				
				GROUP BY U.id
				ORDER BY lastname";
				
		$rs = $this->db->query($sql)->result();		

		if (count($rs)) return $rs;
		else return false;
	}

	public function getUsersInGroup($groupname, $activeonly = 1, $usertype = 'cms', $start = 0, $limit_per_page = 0) {		
		$sql = "SELECT U.*, G.groupname, G.grouptitle
				FROM users U INNER JOIN users_groups UG ON (U.id = UG.usersid)
					INNER JOIN groups G ON (UG.groupsid = G.id AND G.groupname = ".$this->db->escape($groupname).")
				WHERE U.deleted = 0 
					AND U.profiletype = ".$this->db->escape($usertype);
		
		if ($activeonly) $sql .= " AND U.active = 1 ";
		
		$sql .= " ORDER BY lastname";
				
		$rs = $this->db->query($sql)->result();		

		if (count($rs)) return $rs;
		else return false;
	}

	public function getGroupsets($userid = 0) {
		$sql = "SELECT usertype FROM users WHERE id = ".intval($userid);
		$rs = $this->db->query($sql)->result();		
	
		if (count($rs) && $rs[0]->usertype == 'super') $userid = 0;
		
		$select = "SELECT GS.setname, G.* ";
		$from	= "FROM groupsets GS RIGHT JOIN groups G ON (GS.id = G.groupsetsid) ";
		if($userid) $from	.= "LEFT JOIN users_groupsets UG ON (GS.id = UG.groupsetsid) ";

		$where  = "WHERE 1 = 1 ";
		if($userid) $where  .= "	AND UG.usersid = ".intval($userid)." ";

		$order 	= "ORDER BY GS.setname, G.grouptitle ";
	
		$sql = $select.$from.$where.$order;
	
		$rs = $this->db->query($sql)->result();		
	
		if (count($rs)) return $rs;
		else return false;
	}
	
	public function getGroupsetsForUser($userid) {
		$select = " SELECT G.* ";
		$from	= " FROM groupsets G INNER JOIN users_groupsets UG ON (G.id = UG.groupsetsid AND UG.usersid = ".intval($userid)." ) ";
		$where  = " WHERE 1 = 1 ";
		$order 	= " ORDER BY setname";
		
		$sql = $select.$from.$where.$order;
	
		$rs = $this->db->query($sql)->result();		
	
		if (count($rs)) return $rs;
		else return false;
	}
	
	public function setGroupsets($userid, $groupsetsid) {
		//clear non-accessible sets
		$sql = "DELETE G FROM users_groupsets G
				WHERE G.usersid = ".intval($userid);

		$rs = $this->db->query($sql);
		
		foreach ($groupsetsid as $gsid) {
			$sql = "INSERT INTO users_groupsets
					SET usersid = ".intval($userid).",
						groupsetsid = ".intval($gsid);
						
			$query = $this->db->query($sql);
		}
	}
	
	public function setGroups($userid, $groupsid) {
		$sql = "DELETE FROM users_groups
				WHERE usersid = ".intval($userid);

		$rs = $this->db->query($sql);
		
		foreach ($groupsid as $gid) {
			$sql = "INSERT INTO users_groups
					SET usersid = ".intval($userid).",
						groupsid = ".intval($gid);
						
			$query = $this->db->query($sql);
		}
	}
	
	public function getGroups($groupsetid = 0) {
		if ($this->hasGroupsets() ) {		
			$select = "SELECT GS.setname, G.* ";
			$from =	"FROM groupsets GS RIGHT JOIN groups G ON (GS.id = G.groupsetsid) ";
			$where = "WHERE 1 = 1 ";
			
			if ($groupsetid) {
				$where .= " AND GS.id = ".intval($groupsetid);
			}
			
			$order = " ORDER BY GS.setname, G.grouptitle";
		
		} else {
			$select = "SELECT G.* ";
			$from =	"FROM groups G ";
			$where = "WHERE 1 = 1 ";
			$order = " ORDER BY G.grouptitle";
		}
		
		$sql = $select.$from.$where.$order;
	
		$rs = $this->db->query($sql)->result();		
	
		if (count($rs)) return $rs;
		else return false;
	}
	
	public function getGroupsForUser($userid) {
		$sql = "SELECT G.* 
		 		FROM groups G INNER JOIN users_groups UG ON (G.id = UG.groupsid AND UG.usersid = ".intval($userid)." ) ";
		
		if ($this->hasGroupsets()) $sql .= " UNION
				
				SELECT G.* 
				FROM groups G INNER JOIN users_groupsets UG ON (G.groupsetsid = UG.groupsetsid AND UG.usersid = ".intval($userid)." ) ";

	
		$rs = $this->db->query($sql)->result();		
	
		if (count($rs)) return $rs;
		else return false;
	}
	
	public function hasGroupsets() {
		$sql = "SHOW TABLES LIKE 'groupsets'";
		
		$rs = $this->db->query($sql)->result();
		
		if (count($rs)) return $rs;
		else return false;
	}
}
/*
	ALTER TABLE `users` ADD `creatorid` int(11) DEFAULT NULL ;
	ALTER TABLE `users` ADD `updaterid` int(11) DEFAULT NULL ;
	
	ALTER TABLE `users` ADD `resetbee` varchar(50) DEFAULT NULL ;
	CREATE TABLE `users_groupsets` (
	  `id` int(11) NOT NULL auto_increment,
	  `usersid` int(11) default NULL,
	  `groupsetsid` int(11) default NULL,
	  PRIMARY KEY  (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	
	CREATE TABLE `groupsets` (
  `id` int(11) NOT NULL auto_increment,
  `setname` varchar(30) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `groups` ADD `groupsetsid` int(11) DEFAULT NULL ;
	
	*/
?>