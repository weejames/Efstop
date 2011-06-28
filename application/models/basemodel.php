<?
class BaseModel extends MY_Model {
	/**
	 * MODULE NAME   : BaseModel.php
	 *
	 * DESCRIPTION   : base model controller
	 *
	 * MODIFICATION HISTORY
	 *   V1.0   2007-05-23 15:10   - James Constable     - Created
	 *
	 * @package             BaseModel
	 * @subpackage          Base model component Class
	 * @author              James Constable
	 * @copyright           Copyright (c) 2006-2007 ocean70.com
	 * @filesource
	 */

	public $table_record_count;
	protected $db_table = '';
	
	public function __construct() {
		parent::__construct();
		$this->obj = get_instance();
		$this->load->database();
	}

	public function setModel($model) {
		$this->db_table = $model;
	}
	
	public function getPublished($start = null, $count = null, $orderby = null) {
		$sql = "SELECT T.*, F.friendly_url
				FROM ".$this->db_table." T LEFT JOIN friendly_urls F ON (T.id = F.contentid AND F.content_type = ".$this->db->escape($this->db_table).")
				WHERE T.active = 1 AND T.deleted = 0
					AND (T.publishon <= NOW() OR T.publishon IS NULL OR T.publishon = '0000-00-00 00:00:00')
					AND (T.publishoff >= NOW() OR T.publishoff IS NULL OR T.publishoff = '0000-00-00 00:00:00') ";
					
		$order_clause = '';
		if ($orderby) {
			if ( is_string($orderby) ) {
				$order_clause = " ORDER BY " . $orderby;
			} elseif ( is_array($orderby) ) {
				if ( count($orderby) > 0 ) {
					foreach ($orderby as $field => $value) {
						$orderby_list[] = " $field " .$this->db->escape($value) . " ";
					}
					$order_clause = " ORDER BY " . join(' , ', $orderby_list );
				}
			}
		}
		
		$limit_clause = '';
		if ($count) {
			if ($count && $start) {
				$limit_clause = " LIMIT $start, $count ";
			}
			else {
				$limit_clause = " LIMIT $count ";
			}
		}
		
		$sql .= $order_clause . $limit_clause;
		
		$rs = $this->db->query($sql)->result();

		if (count($rs)) return $rs;
		else return false;
	}
	
	public function find($filters = null, $start = null, $count = null, $orderby = null) {

		// Filter could be an array or filter values or an SQL string.
		$where_clause = '';
		if ($filters) {
			if ( is_string($filters) ) {
				$where_clause = $filters;
			}
			elseif ( is_array($filters) ) {
				// Build your filter rules
				if ( count($filters) > 0 ) {
					foreach ($filters as $field => $value) {
						if (is_array($value)) $filter_list[] = " T.$field IN (".implode(',', $value).") ";
						else if (substr($field, 0, 3) == 'md5') {
							$fieldparts = explode('|', $field);
							$filter_list[] = " md5(T.".$fieldparts[1].") = ".$this->db->escape($value)." ";
						} else if (substr($value, 0, 1) == '>') {
							$valueparts = explode('>', $value);
							$filter_list[] = " T.".$field." > ".$this->db->escape($valueparts[1])." ";
						} else if (substr($value, 0, 1) == '<') {
							$valueparts = explode('<', $value);
							$filter_list[] = " T.".$field." < ".$this->db->escape($valueparts[1])." ";
						} else if (substr($value, 0, 1) == '!') {
							$valueparts = explode('!', $value);
							$filter_list[] = " T.".$field." != ".$this->db->escape($valueparts[1])." ";
						} else	if (substr($field, 0, 5) == 'MONTH') {
								$fieldparts = explode('|', $field);
								$filter_list[] = " MONTH(T.".$fieldparts[1].") = '$value' ";
							} else if (substr($field, 0, 4) == 'YEAR') {
								$fieldparts = explode('|', $field);
								$filter_list[] = " YEAR(T.".$fieldparts[1].") = '$value' ";
							}else $filter_list[] = " T.$field = '$value' ";
					}
					$where_clause = ' WHERE ' . join(' AND ', $filter_list );
				}
			}

		}
		
		$order_clause = '';
		if ($orderby) {
			if ( is_string($orderby) ) {
				$order_clause = " ORDER BY " . $orderby;
			} elseif ( is_array($orderby) ) {
				if ( count($orderby) > 0 ) {
					foreach ($orderby as $field => $value) {
						$orderby_list[] = " $field " .$this->db->escape($value) . " ";
					}
					$order_clause = " ORDER BY " . join(' , ', $orderby_list );
				}
			}
		}
		
		$limit_clause = '';
		if ($count) {
			if ($count && $start) {
				$limit_clause = " LIMIT $start, $count ";
			}
			else {
				$limit_clause = " LIMIT $count ";
			}
		}

		// Build up the SQL query string and run the query
		$sql = "SELECT T.*, F.friendly_url
				FROM ".$this->db_table." T LEFT JOIN friendly_urls F ON (T.id = F.contentid AND F.content_type = ".$this->db->escape($this->db_table).")". $where_clause . $order_clause . $limit_clause;

		$this->table_record_count = $this->db->query("SELECT T.*, F.friendly_url
				FROM ".$this->db_table." T LEFT JOIN friendly_urls F ON (T.id = F.contentid AND F.content_type = ".$this->db->escape($this->db_table).")". $where_clause)->num_rows();

		$rs = $this->db->query($sql)->result();

		if (count($rs)) return $rs;
		else return false;

	}

	public function retrieve_by_friendlyurl($furl) {
		$sql = "SELECT T.*, F.friendly_url
				FROM ".$this->db_table." T LEFT JOIN friendly_urls F ON (T.id = F.contentid AND F.content_type = ".$this->db->escape($this->db_table).")
				WHERE F.friendly_url = ".$this->db->escape($furl)."
					AND T.deleted = 0
				LIMIT 1";
		
		$rs = $this->db->query($sql)->result();

		if (count($rs)) return $rs[0];
		else return false;
	}
	
	public function retrieve_by_pkey($idField) {
		$sql = "SELECT T.*, F.friendly_url, CONCAT_WS(' ', U.firstname, U.lastname) as creator, CONCAT_WS(' ', U2.firstname, U2.lastname) as updater
				FROM ".$this->db_table." T LEFT JOIN friendly_urls F ON (T.id = F.contentid AND F.content_type = ".$this->db->escape($this->db_table).")
						LEFT JOIN users U ON (T.creatorid = U.id)
						LEFT JOIN users U2 ON (T.updaterid = U2.id)
				WHERE T.id = ".intval($idField)."
				LIMIT 1";
		//, CONCAT_WS(' ', U.firstname, U.lastname) as creator, CONCAT_WS(' ', U2.firstname, U2.lastname) as updater
		//LEFT JOIN users U ON (T.creatorid = U.id)
		//			LEFT JOIN users U2 ON (T.updaterid = U2.id)

		$rs = $this->db->query($sql)->result();

		if (count($rs)) return $rs[0];
		else return false;
	}
	
	public function retrieve_by_field($field, $value) {
		$sql = "SELECT T.*, F.friendly_url
				FROM ".$this->db_table." T LEFT JOIN friendly_urls F ON (T.id = F.contentid AND F.content_type = ".$this->db->escape($this->db_table).")
				WHERE T.`".$field."` = ".$this->db->escape($value)."
				LIMIT 1";
		//, CONCAT_WS(' ', U.firstname, U.lastname) as creator, CONCAT_WS(' ', U2.firstname, U2.lastname) as updater
		//LEFT JOIN users U ON (T.creatorid = U.id)
		//			LEFT JOIN users U2 ON (T.updaterid = U2.id)

		$rs = $this->db->query($sql)->result();

		if (count($rs)) return $rs[0];
		else return false;
	}

	public function add( $data ) {
		// Build up the SQL query string
		$sql = $this->db->insert_string($this->db_table, $data);

		$query = $this->db->query($sql);

		return $this->db->insert_id();
	}

	public function modify($keyvalue, $data) {
		// Build up the SQL query string
		$where = "id = $keyvalue";
		$sql = $this->db->update_string($this->db_table, $data, $where);

		$query = $this->db->query($sql);

		return $query;
	}
	
	public function delete_where($data) {
		$this->db->delete($this->db_table, $data);
	}
	
	public function updateField($dataid, $fieldname, $fieldvalue) {
		$sql = "UPDATE ". $this->db_table ." SET $fieldname = ".$this->db->escape($fieldvalue )." WHERE id = ".intval($dataid);
		$rs = $this->db->query($sql);
	}
	
	public function delete_by_pkey($idField){
		$sql = "DELETE T, F
				FROM ".$this->db_table." T LEFT JOIN friendly_urls F ON (T.id = F.contentid AND F.content_type = ".$this->db->escape($this->db_table).")
				WHERE T.id = ".intval($idField);

		$query = $this->db->query($sql);

		return true;
	}
	
	public function pseudoDelete($idField) {
		return $this->modify($idField, array('deleted' => 1, 'dateupdated' => date('Y-m-d H:m:s') ));
	}
	
	public function pseudoUndelete($idField) {
		return $this->modify($idField, array('deleted' => 0, 'dateupdated' => date('Y-m-d H:m:s') ));
	}
}

?>