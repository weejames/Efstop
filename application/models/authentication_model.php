<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Portal Model Class
 *
 * @package		Ocean70 CMS
 * @subpackage	Authentication
 * @category	Authentication
 * @author		James Constable
 * @copyright	Copyright (c) 2007, ocean70.com All rights reserved.
 */
class Authentication_model extends MY_Model {

	public function Authentication_model() {
		parent::__construct();
		$this->load->database();
	}
	
	public function isUser($username, $password = false, $emailaddress = false) {
		$sql = "SELECT U.id ".
				"FROM users U ".
				"WHERE deleted = 0 AND active = 1";

		if ($username) $sql .= " AND U.username = ".$this->db->escape($username);
		
		if ($password) $sql .= " AND password = ".$this->db->escape(md5($password));
		
		if ($emailaddress) $sql .= " AND emailaddress = ".$this->db->escape($emailaddress);
		
		$sql .= " LIMIT 1";
		
		$rs = $this->db->query($sql)->result();
		
		if ($rs) return $rs[0];
		else return false;
	}
	
	public function getUserDetails($username, $emailaddress = false) {
		$sql = "SELECT U.* ".
				"FROM users U ".
				"WHERE 1 = 1 ";
				
		if ($username) $sql .= " AND U.username = ".$this->db->escape($username);
		if ($emailaddress) $sql .= " AND U.emailaddress = ".$this->db->escape($emailaddress);
		
		$sql .= " LIMIT 1";
		
		$rs = $this->db->query($sql)->result();
		
		if ($rs) return $rs[0];
		else return false;
	}
	
	public function getUserGroups($userid) {
		$sql = "SELECT G.groupname, G.grouptitle ".
				"FROM users U LEFT JOIN users_groups UG ON (U.id = UG.usersid) ".
				"	LEFT JOIN groups G ON (UG.groupsid = G.id) ".
				"WHERE U.id = ".intval($userid)." ";
		
		return $this->db->query($sql)->result();
	}
	
	public function getUserProfile($username, $emailaddress = false) {
		$sql = "SELECT U.username, U.lastlogin, U.datecreated, U.firstname, U.lastname, U.profiletype, U.emailaddress ".
				"FROM users U ".
				"WHERE 1 = 1 ";
		
		if ($username) $sql .= " U.username = ".$this->db->escape($username)." ";
		if ($emailaddress) $sql .= " U.emailaddress = ".$this->db->escape($emailaddress)." ";
		
		return $this->db->query($sql)->result_array();
		
	}

	function modify($keyvalue, $data) {
		// Build up the SQL query string
		$where = "id = $keyvalue";
		$sql = $this->db->update_string('users', $data, $where);

		$query = $this->db->query($sql);
	}
}

// EOF
?>