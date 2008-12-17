<?php  
if (file_exists(APPPATH.'controllers/dam.php')) include_once(APPPATH.'controllers/dam.php');

class TagOrganiser extends DAM {
	
	protected $package = 'dam_controllers';
	
	public function TagOrganiser() {
		parent::DAM();

		$this->view_data['page_title'] .= ' - Tag Organiser';
	
	}
	
	public function index() {
		$this->load->model('tagsmodel');
		
		//data should be available from the constructor but if not et the tags list.
		if ( !isset($this->view_data['systemTags'])) {
			$this->load->model('imagesetmodel');
			$this->view_data['systemSets'] = $this->imagesetmodel->getImageSetsForUser($this->authentication->getUserId());
		
			if ($this->view_data['systemSets']) {
				$imagesetids = array();
				foreach($this->view_data['systemSets'] as $set) $imagesetids[] = $set->id;
			} else $imagesetids = false;
		
		
			$this->load->model('imagemodel');
			if ($imagesetids) $this->view_data['systemTags'] = $this->imagemodel->getTagList($imagesetids);
			else $this->view_data['systemTags'] = false;
		}
		
		$tagarray = array();
		
		if ($this->view_data['systemTags']) {
			foreach($this->view_data['systemTags'] as $tag) {
				$tagarray[] = $tag->tag_id;
			}
		}
		
		$tagsAndCollections = $this->tagsmodel->getTagsAndCollections( $tagarray );
		
		$this->view_data['tagsAndCollections'] = $tagsAndCollections;
		$this->view_data['collections'] = $this->tagsmodel->getCollections();
		
		$this->layout->buildPage('/tags/organiser', $this->view_data);
    }

	public function addCollection() {
		$this->load->model('tagsmodel');
		
		$data = null;
		$data->collection = $this->input->post('collection');
		
		$this->tagsmodel->addCollection($data);
		
		redirect($this->package.'/tagorganiser/');
	}
    
	public function putInCollection( $tagid = 0) {
		$collectionid = $this->input->post('collectionid');
		
		if ($tagid && $collectionid) {
			$this->load->model('tagsmodel');
			
			$this->tagsmodel->addToCollection($tagid, $collectionid);
			
		}
		
		redirect($this->package.'/tagorganiser/');
	}
}
?>