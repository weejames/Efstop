<?
if (file_exists(APPPATH.'models/basemodel.php')) include_once(APPPATH.'models/basemodel.php');
else include_once(RESPATH.'models/basemodel.php');

class TagsModel extends BaseModel {
	/**
	 * MODULE NAME   : TagsModel.php
	 *
	 * DESCRIPTION   : Tags model controller
	 *
	 * MODIFICATION HISTORY
	 *   V1.0   2007-07-05 16:15   - James Constable     - Created
	 *
	 * @package             TagsModel
	 * @subpackage          TagsModel model component Class
	 * @author              James Constable
	 * @copyright           Copyright (c) 2007 ocean70.com
	 * @since               Version 1.0
	 * @filesource
	 */

	public $table_record_count;

	public function TagsModel() {
		parent::BaseModel();
		$this->setModel('tags');
		$this->load->database();
	}

	public function tagExists($tag) {
		$sql = "SELECT T.tag, T.id
				FROM tags T
				WHERE T.tag = ".$this->db->escape($tag)."";
		
		$rs = $this->db->query($sql)->result();
		
		if (count($rs)) return $rs[0]->id;
		else return false;
	}
	
	public function getTagsAndCollections( $taglimiter = array() ) {
		$sql = "SELECT C.collection, T.tag, T.id
				FROM tags T LEFT JOIN collections_tags CT ON (T.id = CT.tags_id)
					LEFT JOIN collections C ON (CT.collections_id = C.id)
				WHERE 1 = 1 ";
		
		if(is_array($taglimiter) && count($taglimiter)) $sql .= "AND T.id IN (".implode(',', $taglimiter).") ";
			
		$sql .=	"ORDER BY C.collection ASC, T.tag ASC ";
		
		$rs = $this->db->query($sql)->result();
		
		if (count($rs)) return $rs;
		else return false;
	}
	
	public function getCollections() {
		$sql = "SELECT C.id, C.collection
				FROM collections C
				ORDER BY C.collection ASC ";
		
		$rs = $this->db->query($sql)->result();

		if (count($rs)) return $rs;
		else return false;
	}
	
	public function addCollection( $data ) {
		// Build up the SQL query string
		$sql = $this->db->insert_string('collections', $data);

		$query = $this->db->query($sql);

		return $this->db->insert_id();
	}
	
	public function addToCollection( $tagid, $collectionid ) {
		$sql = "SELECT * FROM collections_tags WHERE tags_id = ".intval($tagid)." AND collections_id = ".intval($collectionid);
		
		$rs = $this->db->query($sql)->result();

		if (!count($rs)) {
			$sql = "INSERT INTO collections_tags SET tags_id = ".intval($tagid).", collections_id = ".intval($collectionid);
			 $this->db->query($sql);
		}
		
		return true;
	}
	
	public function getTagged($itemid, $itemtype) {
		$sql = "SELECT DISTINCT TG.tag_id, T.tag
				FROM tagged TG RIGHT JOIN tags T ON (TG.tag_id = T.id)
				WHERE TG.itemtype = ".$this->db->escape($itemtype);
		if ($itemid > 0) $sql .= " AND TG.itemid = ".intval($itemid);

		$sql .= " ORDER BY T.tag";
		
		$rs = $this->db->query($sql)->result();
		
		if (count($rs)) return $rs;
		else return false;
	}
	
	public function searchTagged($tag, $itemtype = '') {
		if (is_array($tag)) {
			
			$queries = array();
			
			foreach($tag as $key => $ttag ) {
				
				$queries[$key] = $this->searchTagged($ttag, $itemtype);
				
			}

			if (count($queries) > 1) {
				
				foreach ($queries as $key => $value) {
					foreach($value as $row => $item) {
						$newqueries[$key][$row] = (int)$item->itemid;
					}
				}
				
				$resultsarray = array();
				
				$eval_string = "\$resultarray = array_intersect(";

				foreach($newqueries as $key => $dataArray) {
					if ($key > 0) $eval_string .= ", ";
					$eval_string .= "\$newqueries[".$key."]";
				}

				$eval_string .= ");";

				eval($eval_string);

				if (count($resultarray)) {


				$sql = "SELECT DISTINCT T.itemid, T.itemtype
						FROM tagged T
						WHERE T.itemid IN (".$this->db->escape(implode(',', $resultarray)).")";
			
					$rs = $this->db->query($sql)->result();
					
					if (count($rs)) return $rs;
					else return false;
					
				} else return false;
				
			} else return $queries[0];
			
		} else {
		
			$sql = "SELECT DISTINCT T.itemid, T.itemtype
					FROM tagged T LEFT JOIN tags T2 ON (T.tag_id = T2.id) ";
		
			$sql .=	" WHERE 1 = 1 ";
			if (strlen($itemtype)) $sql .= " AND T.itemtype = ".$this->db->escape($itemtype);
		
			if (is_numeric($tag)) $sql .= " AND T2.id = ".intval($tag);
			else if (is_string($tag)) $sql .= " AND (T2.tag = ".$this->db->escape($tag)." OR T2.taguri = ".$this->db->escape($tag).") ";
		
			$sql .= " ORDER BY T.itemtype ASC";
		
			$rs = $this->db->query($sql)->result();
		
			if (count($rs)) return $rs;
			else return false;
		
		}
		
	}
	
	public function tagItem($itemid, $itemtype, $tags) {
		
		if (is_array($tags)) {
			//remove existing tags for this item
			$clear_sql = "DELETE FROM tagged WHERE itemid = $itemid AND itemtype = ".$this->db->escape($itemtype);
			$this->db->query($clear_sql);
			
			if ($tags) {
				foreach ($tags as $key => $tag) {
					$sql = "INSERT INTO tagged SET itemid = $itemid, tag_id = $tag, itemtype = ".$this->db->escape($itemtype);
					$this->db->query($sql);
				}
			}
		} else if (is_numeric($tags)) {
			//check if this has been tagged before
			$sql = "SELECT id FROM tagged WHERE itemid = $itemid AND tag_id = $tags AND itemtype = ".$this->db->escape($itemtype);
			$rs = $this->db->query($sql)->result();
			
			//if not - add tag
			if (count($rs) == 0) {
				$sql = "INSERT INTO tagged SET itemid = $itemid, tag_id = ".intval($tags).", itemtype = ".$this->db->escape($itemtype);
				$this->db->query($sql);
			}
		}
		
		return true;
	}
	
	public function deTag($itemid, $itemtype, $tagid = 0) {
		
		$clear_sql = "DELETE FROM tagged
					WHERE itemid = ".intval($itemid)."
						AND itemtype = ".$this->db->escape($itemtype);
	
		if ($tagid) $clear_sql .= " AND tag_id = ".intval($tagid);
		
		$this->db->query($clear_sql);
		
		return true;
	}
	
}
/*
 * Relevant DB tables
 * 
 CREATE TABLE `tags` (
  `id` int(11) NOT NULL auto_increment,
  `tag` varchar(100) default NULL,
  `taguri` varchar(100) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `tagged` (
  `id` int(11) NOT NULL auto_increment,
  `itemid` int(11) default NULL,
  `itemtype` varchar(50) default NULL,
  `tag_id` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `collections_tags` (
  `id` int(11) NOT NULL auto_increment,
  `collections_id` int(11) default NULL,
  `tags_id` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `collections` (
  `id` int(11) NOT NULL auto_increment,
  `collection` varchar(100) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 */
?>