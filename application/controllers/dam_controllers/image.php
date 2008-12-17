<?php 

if (file_exists(APPPATH.'controllers/dam.php')) include_once(APPPATH.'controllers/dam.php');

class Image extends DAM {
	
	protected $package = 'dam_controllers';
	protected $current_module = 'Upload Images';
	private $s3bucket = ''; // TODO: make a config setting
	private $local_image_path = ''; // TODO: make a config setting
	
	public function Image() {
		$login = 1;
		if ( stristr ( $_SERVER["HTTP_USER_AGENT"], 'Flash' ) ) $login = 0;
		parent::DAM($login); 
		
		$this->load->helper('image_helper');
		$this->view_data['page_title'] .= ' &middot; Images';
	}
	
	public function index() {
		$this->viewAll();
    }
    
    public function viewAll() {
    	$this->load->model('imagemodel');
    	$this->load->model('lightboxmodel');
    	$this->load->model('tagsmodel');
    	$this->load->library('getpaging');
    	
    	$limit_per_page = 20;
    	
    	if ($this->tags) $tagged = $this->tagsmodel->searchTagged($this->tags, 'images');
    	else $tagged = false;
    	
    	$tagged_norm = array();
    	if ($tagged) foreach ($tagged as $tag) $tagged_norm[] = $tag->itemid;
    	
    	if (!$this->tag_string) $images = $this->imagemodel->find(array('accountid' => $this->authentication->getAccountId() ), $this->getpaging->getStartRow(), $limit_per_page, "datecreated DESC");
    	else $images = $this->imagemodel->find(array('accountid' => $this->authentication->getAccountId(), 'id' => $tagged_norm ), $this->getpaging->getStartRow(), $limit_per_page, "datecreated DESC");
    	
    	$config['base_url']     = site_url('image/'.$this->tag_string);
		$config['total_rows']   = $this->imagemodel->table_record_count;
		$config['per_page']     = $limit_per_page;

		$this->getpaging->initialize($config);
		$this->view_data['page_links'] = $this->getpaging->create_links();

		$imageids = array();
		if ($images) foreach ($images as $image) $imageids[] = $image->id;
		
		if (!$this->tag_string) $imagetags = $this->imagemodel->getTagList($this->authentication->getAccountId());
		else $imagetags = $this->imagemodel->getTagList($this->authentication->getAccountId(), $imageids);

		
		$this->view_data['showing_string'] = 'Showing '.($this->getpaging->getStartRow() + 1).' to '. min ($this->imagemodel->table_record_count, ($this->getpaging->getStartRow() + $limit_per_page)) .' of '.$this->imagemodel->table_record_count;
		
		$this->view_data['imagetags'] = $imagetags;

		$this->view_data['lightboxes'] = $this->lightboxmodel->getMyLightboxes($this->authentication->getUserId());
		
		$this->view_data['images'] = $images;
    	
    	$this->view_data['intro_notification'] = $this->notifications->renderNotification('images');
    	
    	$this->layout->buildPage('images/viewall', $this->view_data);
    }
    
    public function uploadProcess() {
    	$this->load->library('s3');
    	
    	$this->load->model('imagemodel');
    	
    	$images = $this->imagemodel->find( array('s3upload' => 0), 0, 1);
    	
    	foreach($images as $key => $image) {
    		$filePath = $this->local_image_path.$image->filename;
    	
			//check if file exists
			if (file_exists($filePath)) {
				
				//attempt to upload to s3.
				if ($data = file_get_contents($filePath)) {
					$transfer = new s3();
					echo $this->s3->putObject($image->filename, $data, $this->s3bucket);
				
					if (1) {
						$data = null;
						$data->s3upload = 1;
						
						$this->imagemodel->modify($image->id, $data);
					}

				
				}				
				
			} else echo 'File Doesn\'t exist';

		}
			
    }
    
    public function download($imagetype, $imageid) {
    	$this->load->model('imagemodel');
    	
    	$imagedata = $this->imagemodel->retrieve_by_pkey($imageid);
    	
    	//check if its here or on s3
    	if ($imagedata->s3upload && $imagetype == 'full') {
    		$this->load->library('s3');
    		
    		//$location = $this->s3->getPrivateURL($this->s3bucket, $imagedata->filename);
    		
    		//$this->s3->getObjectInfo($this->s3bucket, $imagedata->filename);
    		
    		$data = $this->s3->getObject($imagedata->filename, $this->s3bucket);
    		
    		$ct = $this->s3->getResponseContentType();
    		$cl = $this->s3->getResponseContentLength();
    		
    		//header("Location: ".$s3URL, TRUE, 307);
    		header("Content-Type: ".$ct);
			header("Content-Disposition: attachment; filename=\"".basename($imagedata->filename)."\";" );
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: ".$cl);
			
			flush();
			
			echo $data;
    	} else {
    		header("Content-Type: ".mime_content_type($this->local_image_path.$imagedata->filename));
			header("Content-Disposition: attachment; filename=\"".basename($this->local_image_path.$imagedata->filename)."\";" );
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: ".filesize($this->local_image_path.$imagedata->filename));
			$data = file_get_contents($this->local_image_path.$imagedata->filename);
			echo $data;
    	}
    	exit();
    }
    
    public function reprocess_images() {
    	$this->load->model('imagemodel');
    	$this->load->helper('process_helper');
    	
    	$images = $this->imagemodel->find();
    	
    	foreach ($images as $image) {
    		if (!file_exists($this->local_image_path.'1500s/'.$image->previewname)) {
    			$file_data = processImage($this->local_image_path, $image->filename, $this->local_image_path.'1500s/', $image->previewname);
    			if ($file_data) $this->imagemodel->modify($image->id, $file_data);
    		}
    	}
    	
    }
    
    public function upload($flashupload = 0, $sessionid = '') {
    	$submit = $this->input->post('submit');

    	if ($flashupload) {
    		$this->load->model('imagemodel');
    		$submit = true;
    		$userdata = $this->imagemodel->getCreatorId($sessionid);
    	}
    	
    	if ($submit) {
    		$config['upload_path'] = $this->local_image_path;
			$config['allowed_types'] = 'gif|jpg|png|psd|tif';
		
			$this->load->library('upload', $config);
    		
			$fieldname = 'imageupload';
			if ($flashupload) $fieldname = 'Filedata';
			
			$fileUploaded = $this->upload->do_upload($fieldname);
				
			if ($fileUploaded) {
				$this->load->model('imagemodel');
				$this->load->library('imagecolorextract');
				
				$uploadData = $this->upload->data();
				
				$image_data->filename 	= $uploadData['file_name'];
				$image_data->title 		= $uploadData['orig_name'];
				$image_data->filesize 	= $uploadData['file_size'];
				
				//check if we're uploading a psd
				if ($uploadData['file_ext'] == '.psd') {
					$this->load->library('psdreader');
					
					//generate image from psd and save as jpeg.
					$resource = $this->psdreader->imagecreatefrompsd($this->local_image_path.$uploadData['file_name']);
					imagejpeg($resource, $this->local_image_path.'1500s/'.$uploadData['raw_name'].'.jpg');
					
					//get size before we make it smaller
					$sizes = getimagesize($this->local_image_path.'1500s/'.$uploadData['raw_name'].'.jpg');
					
					$image_data->width = $sizes[0];
					$image_data->height = $sizes[1];
					
					//generate thumbnail
					$config['image_library'] = 'GD2';
					$config['source_image'] = $this->local_image_path.'1500s/'.$uploadData['raw_name'].'.jpg';
					$config['maintain_ratio'] = true;
					$config['width'] = 1500;
					$config['height'] = 1500;
					$config['quality'] = 100;
					
					$image_data->previewname = $uploadData['raw_name'].'.jpg';
					
					$this->load->library('image_lib', $config);
					$this->image_lib->resize();

				} else {
					$sizes = getimagesize($this->local_image_path.$uploadData['file_name']);
					
					$image_data->width = $sizes[0];
					$image_data->height = $sizes[1];
	
					//generate thumbnail
					$config['image_library'] = 'ImageMagick';
					$config['source_image'] = $this->local_image_path.$uploadData['file_name'];
					$config['new_image'] = $this->local_image_path.'1500s/'.$uploadData['raw_name'].'.jpg';
					$config['maintain_ratio'] = true;
					$config['width'] = 1500;
					$config['height'] = 1500;
					$config['quality'] = 100;
					$config['library_path'] = '/usr/bin/convert';
					
					$image_data->previewname = $uploadData['raw_name'].'.jpg';
					
					$image_data->exif = serialize( exif_read_data ( $this->local_image_path.$uploadData['file_name'], 'ANY_TAG' , true , false ) );
					
					
					$this->load->library('image_lib', $config);
					$this->image_lib->resize();
				}

				$image_data->datecreated = date('Y-m-d H:i:s');
				
				
				if (!$flashupload) $image_data->creatorid = $this->authentication->getUserId();
				else $image_data->creatorid = $userdata['id'];
				
				
				if ($image_data->width > $image_data->height) $image_data->orientation = 'L';
				else if ($image_data->height > $image_data->width) $image_data->orientation = 'P';
				else $image_data->orientation = 'S';
				
				$colourExtract = $this->imagecolorextract->getColors($this->local_image_path.'1500s/'.$image_data->previewname);
				
				if (!$flashupload) $image_data->accountid = $this->authentication->getAccountId();
				else $image_data->accountid = $userdata['accountid'];
				
				$newimageid = $this->imagemodel->add($image_data);
				
				$loopcount = 0;
				foreach ($colourExtract as $colour => $count) {
					$colorid = $this->imagemodel->getColourId($colour);
					
					if ($loopcount <= 10) $this->imagemodel->setImageColour($newimageid, $colorid, $count);
					
					$loopcount ++;
					if ($loopcount >= 10) break;
				}
				
				$this->db_session->set_flashdata('newid', $newimageid);
				
				if (!$flashupload) redirect($this->package.'/image/viewOrphans');
				
			} else {
				$this->db_session->set_flashdata('flasherror', $this->upload->display_errors());
			}
			if ($flashupload) echo ' ';	
    	} else {
			$this->layout->buildPage('images/upload', $this->view_data);
    	}
    }

    public function set_properties($imageid) {
    	$this->load->model('imagemodel');
    	$this->load->model('imagesetmodel');
		$this->load->model('tagsmodel');
    	
		$submit = $this->input->post('submit');
    	
    	$image = $this->imagemodel->retrieve_by_pkey($imageid);
    	
    	if (!$image) {
    		$this->db_session->set_flashdata('flasherror', 'The image you\'re looking for doesn\'t exist.');
    		redirect('');
    	}
    	
    	if ($submit) {
    		//set basic properties
    		$image_data->title = $this->input->post('title');
    		$image_data->description = $this->input->post('description');
    		$image_data->imagecode = $this->input->post('imagecode');
    		$image_data->processed = 1;
    		
    		$images = $this->imagemodel->find( array(), 0, 1, ' id DESC' );
    		
    		if ($images) $count = ($images[0]->id) + 1;
    		else $count = '1';
    		
			$image_data->imagecode = 'EFS' . "-" .  $count;
    		
    		$this->imagemodel->modify($imageid, $image_data);
    		
    		//process tags
			$taglist = $this->input->post('keywords');
			
			$this->setTags($imageid, $taglist);
    		
			$this->db_session->set_flashdata('newid', $imageid);
			$this->db_session->set_flashdata('flashmessage', 'Image information successfully updated!');
			
			$this->imagemodel->updateSearch($imageid);
			
			redirect($this->package.'/image/viewImage/'.$imageid);
    	} else {
    		$this->view_data['image'] = $image;
    		
    		$this->view_data['imagesets'] = $this->imagesetmodel->getImageSetsForUser($this->authentication->getUserId());
    		
			$tags = $this->tagsmodel->getTagged($imageid, 'images');
			
			$tagstring = '';
			
			if ($tags) {
				$tag_array = array();
				foreach ($tags as $tag) {
					$tag_array[] = $tag->tag;
				}
				$tagstring = join(', ', $tag_array);
			}
			
			$this->view_data['keywords'] = $tagstring;
			$this->view_data['imagesetid'] = $this->db_session->userdata('lastimageset');
			
    		$this->layout->buildPage('images/set_properties', $this->view_data);
    	}
    }
    
    public function comment($imageid) {
    	$this->load->model('imagemodel');
    	
		$submit = $this->input->post('submit');
    	
    	$image = $this->imagemodel->retrieve_by_pkey($imageid);
    	
    	if (!$image) {
    		$submit = false;
    		$this->db_session->set_flashdata('flasherror', 'The image you\'re looking for doesn\'t exist.');
    		redirect('');
    	}
    	
    	if ($submit) {
    		$data = null;
    		$data->imageid = $image->id;
    		$data->comment = $this->input->post('comment');
    		$data->userid = $this->authentication->getUserId();
    		$data->datecreated = date("y-m-d H:i:s");
    		
    		$this->imagemodel->addComment($imageid, $data);
    	}
    	
    	redirect($this->package.'/image/viewImage/'.$imageid);
    }
    
    public function viewOrphans() {
    	$this->load->model('imagemodel');
    	
    	$submit = $this->input->post('submit');
    	
    	if ($submit) {
			$imageids = $this->input->post('imageid');
			$keywords = $this->input->post('keywords');		
			
			$setCount = 0;
			$delCount = 0;
			
			foreach ($imageids as $key => $image) {
				$image_data = null;
				
				$delete = $this->input->post('delete_'.$image);
				
				if (!$delete) {
				
					$image_data->title = $this->input->post('title_'.$image);
					$image_data->description = $this->input->post('description_'.$image);
					$image_data->imagecode = $this->input->post('imagecode_'.$image);
					$image_data->processed = 1;
					
					$this->imagemodel->modify($image, $image_data);
				
					//process tags
					$taglist = $this->input->post('keywords_'.$image);
					$this->setTags($image, $keywords.', '.$taglist);
					
					$this->imagemodel->updateSearch($image); 
					$setCount ++;
				
				} else {
					$this->delete($image, '', false);
					$delCount ++;
				}
				
			}
			
			$msg = "";
			
			if ($setCount) {
				$msg .= $setCount." image";
				if ($setCount !== 1) $msg .= 's';
				$msg .= " had their properties set. ";
			}			
			if ($delCount) {
				$msg .= $delCount." image";
				if ($delCount !== 1) $msg .= 's have';
				else $msg .= ' has';
				$msg .= " been deleted.";
			}
			
			
			$this->db_session->set_flashdata('flashmessage', $msg);
			
			redirect('image');
		} else {
		    	
			$images = $this->imagemodel->find( array('processed' => 0, 'creatorid' => $this->authentication->getUserId()) );
    	
    		if (!$images) redirect();
    	
    		$this->view_data['images'] = $images;
    	
    		$this->layout->buildPage('images/orphans', $this->view_data);
    		
    	}
    }
    
    public function viewSets() {
    	$this->viewAll();
    	/*
    	$this->load->model('imagemodel');
    	$this->load->model('imagesetmodel');
    	$this->load->model('usersmodel');
    	
    	$this->view_data['imagesets'] = $this->imagesetmodel->getImageSetsForUser($this->authentication->getUserId());
    	
    	$this->view_data['intro_notification'] = $this->notifications->renderNotification('imagesets');
    	
    	//process imagesets
		if ($this->view_data['imagesets']) {
			$imagesetids = array();
			foreach($this->view_data['imagesets'] as $key => $set) {
				$this->view_data['imagesets'][$key]->images = $this->imagemodel->find(array('imagesetid' => $set->id), 0, null, "datecreated DESC");
			}
		} else $imagesetids = false;
		
		$this->view_data['orphans'] = $this->imagemodel->find( array('imagesetid' => 0, 'creatorid' => $this->authentication->getUserId()) );
    
    	$this->layout->buildPage('images/viewallsets', $this->view_data);*/
    }
    
    public function viewSet($imagesetid) {
    	$this->viewAll();
    	
    	/*$this->load->model('imagemodel');
    	$this->load->model('lightboxmodel');
    	$this->load->model('imagesetmodel');
    	$this->load->model('usersmodel');
    	
    	$setData = $this->imagesetmodel->retrieve_by_pkey($imagesetid);
    	
    	//check if the user is allowed to view images in this set first!
    	if ($setData->creatorid != $this->authentication->getUserId() && !$this->imagesetmodel->canUserAccessImageset($this->authentication->getUserId(), $imagesetid)) redirect('');
    	
    	$this->view_data['images'] = $this->imagemodel->find(array('imagesetid' => $imagesetid));		
		$this->view_data['lightboxes'] = $this->lightboxmodel->getMyLightboxes($this->authentication->getUserId());
		$this->view_data['settitle'] = $setData->setname;
		$this->view_data['imageset'] = $setData;
		
		
		if ($this->authentication->isUserType('super')) {
			$this->view_data['userslist'] = $this->usersmodel->getUsersByGroupSet();
		} else {
			$groupsets = $this->usersmodel->getGroupsetsForUser($this->authentication->getUserId());

			$sets = array();
			if ($groupsets) foreach($groupsets as $set) $sets[] = $set->id;

			$this->view_data['userslist'] = $this->usersmodel->getUsersInGroupsets($sets);
		}

		if ($this->authentication->isUserType('super')) $this->view_data['groupslist'] = $this->usersmodel->getGroups();
		else $this->view_data['groupslist'] = $this->usersmodel->getGroupsForUser($this->authentication->getUserId());
		
    	$this->view_data['returnTo'] = "fromSet".$imagesetid;
    	
    	if ($setData->creatorid == $this->authentication->getUserId() || $this->imagesetmodel->userHasFullAccessToImageset($this->authentication->getUserId(), $imagesetid)) {
    		$this->view_data['setAccess'] = true;
    		$this->view_data['currentAccess'] = $this->imagesetmodel->whoHasAccessToImageset($imagesetid);
    	} else $this->view_data['setAccess'] = false; //true if use is creator of set or has FULL access.
    	
    	$this->view_data['intro_notification'] = $this->notifications->renderNotification('imageset');
    	
    	$this->layout->buildPage('images/viewset', $this->view_data);*/
    }
    
    public function delete($imageid, $location = '', $redirect = true) {
    	$this->load->model('imagemodel');
    	$this->load->model('imagesetmodel');
    	$this->load->model('tagsmodel');
    	$this->load->model('lightboxmodel');
    	
    	$image = $this->imagemodel->retrieve_by_pkey($imageid);
    	
    	if ( $image && ($image->creatorid == $this->authentication->getUserId() || $this->imagesetmodel->userHasFullAccessToImageset($this->authentication->getUserId(), $image->imagesetid)) ) {
    		unlink('./image_store/'.$image->filename);
    		unlink('./image_store/preview/'.$image->previewname);
    		unlink('./image_store/thumbs/'.$image->thumbname);

    		$this->imagemodel->delete_by_pkey($imageid);
    		$this->tagsmodel->deTag($imageid, 'images');
    		$this->imagemodel->removeImageColours($imageid);
    		$this->lightboxmodel->removeImageFromLightbox($imageid);
    		
    		if ($redirect) {
				$this->db_session->set_flashdata('flashmessage', $image->title.' has been deleted');
				redirect('');
    		}
    		
    	} else {
    		if ($redirect) {
				$this->db_session->set_flashdata('flasherror', 'You don\'t have permission to delete this image.  Shame on you.');
				redirect($this->package.'/image/viewImage/'.$imageid.'/'.$location);
			}
    	}
    
    }
    
	public function viewImage($imageid, $location = '') {
    	$this->load->model('imagemodel');
		$this->load->model('imagesetmodel');
    	$this->load->model('tagsmodel');
    	$this->load->model('lightboxmodel');
    	
		$image = $this->imagemodel->retrieve_by_pkey($imageid);
	
    	if (!$image) {
    		$this->db_session->set_flashdata('flasherror', 'The image you\'re looking for doesn\'t exist.');
    		redirect('');
    	}

    	$comments = $this->imagemodel->getComments($image->id);
		    	
		$tags = $this->tagsmodel->getTagged($image->id, 'images');
    	
		if ($tags) {
			$this->view_data['taglist'] = $this->load->view('dam/content/tags/imagetaglist', array('tags' => $tags, 'image' => $image, 'package' => $this->package), true);
			
		} else $this->view_data['taglist'] = false;
    	
    	$myLightboxes = $this->lightboxmodel->getMyLightboxes($this->authentication->getUserId());
    	
    	
    	
    	$this->view_data['lightboxes'] = $myLightboxes;
    	$this->view_data['location'] = $location;
    	
    	$this->view_data['colours'] = $this->imagemodel->getColours($imageid);
    	
    	
		if ( $image->creatorid == $this->authentication->getUserId() || $this->authentication->isUserType('admin') ) $this->view_data['changeDetails'] = true;
		else $this->view_data['changeDetails'] = false;

    	if (substr($location, 0, 7) == 'fromSet') {
    		$locationData = substr($location, 7, strlen($location) - 7);
    		$location = "fromSet";
    	} else if (substr($location,0, 12) == 'fromLightbox') {
    		$locationData = substr($location, 12, strlen($location) - 12);
    		$location = "fromLightbox";
    	}

    	switch ($location) {
    		case "fromSearch":
    			$this->view_data['backURL'] = site_url('dam_controllers/image/search');
    			$this->view_data['backLink'] = "&lt; back to search results";
    			$terms = $this->getSearchTerms();
			
				$searchTerms = $terms->searchTerms;
				$searchTags = $terms->searchTags;
				$searchOrientation = $terms->searchOrientation;
				$searchImagesets = $terms->searchImagesets;
				
				$this->view_data['searchKeyword'] = $searchTerms;
				$this->view_data['searchTags'] = $searchTags;
				$this->view_data['searchOrientation'] = $searchOrientation;
				$this->view_data['searchImagesets'] = $searchImagesets;
				
				$allimages = $this->performSearch($searchTerms, $searchTags, $searchOrientation, $searchImagesets);
    		break;
    		case "fromLightbox":
    			$this->view_data['backURL'] = site_url('dam_controllers/lightbox/viewBox/'.$locationData);
    			$this->view_data['backLink'] = "&lt; back to lightbox";
    			
    			$allimages = $this->imagemodel->getImagesInLightbox($locationData);
    			
    		break;
    		case "fromSet":
    		default:
    			$this->view_data['backURL'] = false;
    			$this->view_data['backLink'] = false;
    			
    			if ($this->tags) $tagged = $this->tagsmodel->searchTagged($this->tags, 'images');
				else $tagged = false;
				
				$tagged_norm = array();
				if ($tagged) foreach ($tagged as $tag) $tagged_norm[] = $tag->itemid;
				
				if (!$this->tag_string) $allimages = $this->imagemodel->find(array('accountid' => $this->authentication->getAccountId() ), null, null, "datecreated DESC");
				else $allimages = $this->imagemodel->find(array('accountid' => $this->authentication->getAccountId(), 'id' => $tagged_norm ), null, null, "datecreated DESC");
    			
    		break;
    	}
    	
    	$previous = false;
    	$next = false;
    	
    	if ($allimages) {
    		
    		foreach($allimages as $key => $currimage) {
    			
    			if ($currimage->id == $image->id) {
					if ( ($key - 1) >= 0) $previous = $allimages[$key - 1];
					else $previous = false;
    			
    				if ( ($key + 1) < count($allimages) ) $next = $allimages[$key + 1];
					else $next = false;
    			}
    			
    		}
    		
    	} else {
    		$previous = false;
    		$next = false;
    	}
    	
    	$this->view_data['previous'] = $previous;
    	$this->view_data['next'] = $next;
    	$this->view_data['image'] = $image;
    	$this->view_data['comments'] = $comments;
    	
    	$this->layout->buildPage('images/viewimage', $this->view_data);
    }
    
	public function refreshTags($imageid) {
    	$this->load->model('imagemodel');
    	$this->load->model('tagsmodel');
    	
		$image = $this->imagemodel->retrieve_by_pkey($imageid);
    	$this->view_data['image'] = $image;
    	
		$tags = $this->tagsmodel->getTagged($imageid, 'images');
    	
		if ($tags) $this->load->view('dam/content/tags/imagetaglist', array('tags' => $tags, 'image' => $image, 'package' => $this->package));
	}


	public function ajax_search() {
		$this->load->model('imagemodel');
		$this->load->model('imagesetmodel');
		
		include ('Zend/Json.php');
		
		$searchTerms = $this->input->post('searchterms');
		
		$found = false;
		
		//check keyword search
		if ($searchTerms) {
			$textSearch = $this->imagemodel->searchTermMatch($searchTerms);
			$imgids = array();
			if ($textSearch) foreach($textSearch as $item) $imgids[] = $item->itemid;
		} else $imgids = false;
		
		
		if ($imgids) {
			$criteria->id = $imgids;
			$criteria->accountid = $this->authentication->getAccountId();
			
			$matched = $this->imagemodel->find( (array)$criteria );
			
			if ($matched) foreach ($matched as $key => $image) $matched[$key]->image_src = resizedImageURL('image_store/1500s/'.$image->previewname, 120, 120, true);
			
			if ($matched) echo Zend_Json::encode((array)$matched);
			else echo Zend_Json::encode(array());
		} else echo Zend_Json::encode(array());
		exit();
	}

    public function search($savedSearchId = 0) {
    	$this->load->model('tagsmodel');
    	$this->load->model('imagemodel');
    	$this->load->model('lightboxmodel');
		
		
		$this->view_data['settitle'] = "Search Results";
		
		$submit = $this->input->post('submit');
		
		$savedsearch = $this->input->post('savedsearch');
		if ($savedsearch) $savedSearchId = $savedsearch;
		
		
    	$resultarray = array();
    	
    	$this->view_data['savedsearch'] = false;
    	
    	if ($submit) {
			//search on
			$searchTerms = $this->input->post('searchterms');
			$searchTags = $this->input->post('tags');
			$searchOrientation = $this->input->post('orientation');
			$searchImagesets = $this->input->post('imagesets');
			
			$this->storeSearchTerms();
			
		} else if ($savedSearchId) {
			//someone viewing saved search
			$this->load->model('basemodel');
			$this->basemodel->setModel('savedsearches');
			
			$terms = $this->basemodel->retrieve_by_pkey($savedSearchId);
			
			
			
			if (!$terms) {
				var_dump($terms);
				die();
				redirect('');
			}
			$searchTerms = $terms->searchTerms;
			$searchTags = unserialize($terms->searchTags);
			$searchOrientation = $terms->searchOrientation;
			$searchImagesets = unserialize($terms->searchImagesets);
			
			$this->view_data['settitle'] = "Saved Search - ".$terms->searchtitle;
			$this->view_data['savedsearch'] = true;
		} else {
			$terms = $this->getSearchTerms();
			
			$searchTerms = $terms->searchTerms;
			$searchTags = $terms->searchTags;
			$searchOrientation = $terms->searchOrientation;
			$searchImagesets = $terms->searchImagesets;
		}
		
		$this->view_data['searchKeyword'] = $searchTerms;
		$this->view_data['searchTags'] = $searchTags;
		$this->view_data['searchOrientation'] = $searchOrientation;
		$this->view_data['searchImagesets'] = $searchImagesets;
		
    	$this->view_data['images'] = $this->performSearch($searchTerms, $searchTags, $searchOrientation, $searchImagesets);
    	
    	$this->view_data['lightboxes'] = $this->lightboxmodel->getMyLightboxes($this->authentication->getUserId());
    	
    	$this->view_data['returnTo'] = "fromSearch";
    	
    	$this->view_data['setAccess'] = false;
    	
    	$this->layout->buildPage('images/viewsearch', $this->view_data);
    }
    
    private function performSearch($searchTerms = false, $searchTags = false, $searchOrientation = false, $searchImagesets = false) {
    	$this->load->model('imagemodel');
    	$this->load->model('tagsmodel');
    	
    	$found = array();
		
		//check keyword search
		if ($searchTerms) {
			$textSearch = $this->imagemodel->searchTermMatch($searchTerms);
			if ($textSearch) {
				$found[] = array();
				foreach ($textSearch as $item) {
					$found[count($found) -1][] = $item->itemid;	
				}
			} else $found = false;
		}
		
		//check tags selected		
		if ($searchTags) {
			foreach($searchTags as $tag) {
				$tagged = $this->tagsmodel->searchTagged($tag, 'images');
				
				if ($tagged) {
					$found[] = array();
					foreach ($tagged as $item) {
						$found[count($found) -1][] = $item->itemid;	
					}
				} else $found[] = false;
				
				
			}
		}
		
		$resultarray = array();
		
		//get the intersection of all search arrays to find the common images.
		if (count($found) > 1) {
			$eval_string = "\$resultarray = array_intersect(";
			
			foreach($found as $key => $dataArray) {
				if ($key > 0) $eval_string .= ", ";
				$eval_string .= "\$found[".$key."]";
			}
			
			$eval_string .= ");";
			
			eval($eval_string);
		} else if (count($found) == 1) $resultarray = $found[0];
		
		$criteria = null;
		if ( ($searchTags || $searchTerms) && count($resultarray)) $criteria->id = $resultarray;
		else if ($searchTags) $criteria->id = 0;
		
		if(strlen($searchOrientation)) $criteria->orientation = $searchOrientation;
		
		//get imagesets this user can access
		$imagesets = $this->imagesetmodel->getImageSetsForUser($this->authentication->getuserId());
		
		$criteria->imagesetid = array();
		
		foreach ($imagesets as $set) {
			//if imagesets are limited by search.. remove others.			
			if($searchImagesets && array_search($set->id, $searchImagesets) !== false) $criteria->imagesetid[] = $set->id;
			elseif (!$searchImagesets) $criteria->imagesetid[] = $set->id;
		}	
		
    	$images = $this->imagemodel->find( (array)$criteria );
    	
    	return $images;
    }
    
    
    public function tagImage($imageid) {
    	$taglist = $this->input->post('keywords');
    	$this->setTags($imageid, $taglist);
    	
    	if (!$this->ajax) redirect($this->package.'/image/viewImage/'.$imageid);
		else echo $imageid;
	}
	
	public function removeTag($imageid, $tagid) {
    	$this->load->model('tagsmodel');
    	
    	$this->tagsmodel->deTag($imageid, 'images', $tagid);
    	
    	if (!$this->ajax) redirect($this->package.'/image/viewImage/'.$imageid);
		else echo $imageid;
	}
	
	public function addTaggedToLightbox(){
		$this->load->model('lightboxmodel');
		$this->load->model('tagsmodel');

		$lightboxid = $this->input->post('lightboxid');

		$lightbox = $this->lightboxmodel->retrieve_by_pkey($lightboxid);

		if ($lightbox->creatorid == $this->authentication->getUserId() || $this->lightboxmodel->userHasFullAccessToLightbox($this->authentication->getUserId(), $lightboxid) ) {

			//get tag string
			$tagstring = $this->input->post('tagstring');
			
			$tags = explode('/', $tagstring);
			
			$matching = $this->tagsmodel->searchTagged($tags, 'images');
			
			$added = 0;
			$dups = 0;
			
			foreach($matching as $image) {
			
				$data->imagesid = $image->itemid;
				$data->lightboxid = $lightbox->id;
				$data->creatorid = $this->authentication->getUserId();
				$data->datecreated = date('Y-m-d H:i:s');

				$ok = $this->lightboxmodel->addImageToLightbox($data->imagesid, $data->lightboxid, $data->creatorid, $data->datecreated);
			
				if ($ok) $added ++;
				else $dups ++;
			}
			
			$msg = '';
			$msg .= $added . ' images added to lightbox. ';
			if ($dups) $msg .= $dups . ' were duplicates.';
			
			$this->db_session->set_userdata('lastlightbox', $data->lightboxid);
			
			if (!$this->ajax) {
			
				$this->db_session->set_flashdata('flashmessage', $msg);
				redirect($this->package.'/image/viewLightbox/'.$lightboxid);
			
			} else {
				echo $msg;
			}
		} else {
			if (!$this->ajax) {
				$this->db_session->set_flashdata('flasherror','You don\'t have permission to add images to this lightbox.');
				redirect($this->package.'/image/viewImage/'.$imageid);
			} else {
				echo "You don't have permission to add images to this lightbox.";
			}
		}
	
	}
	
	public function addToLightbox($imageid = 0) {
		if ($imageid) {

			$this->load->model('lightboxmodel');
	
			$lightboxid = $this->input->post('lightboxid');
	
			$lightbox = $this->lightboxmodel->retrieve_by_pkey($lightboxid);
	
			if ($lightbox->creatorid == $this->authentication->getUserId() || $this->lightboxmodel->userHasFullAccessToLightbox($this->authentication->getUserId(), $lightboxid) ) {
	
				$data->imagesid = $imageid;
				$data->lightboxid = $lightbox->id;
				$data->creatorid = $this->authentication->getUserId();
				$data->datecreated = date('Y-m-d H:i:s');
				
				$ok = $this->lightboxmodel->addImageToLightbox($data->imagesid, $data->lightboxid, $data->creatorid, $data->datecreated);
				
				$this->db_session->set_userdata('lastlightbox', $data->lightboxid);
				
				if (!$this->ajax) {
				
					if ($ok) $this->db_session->set_flashdata('flashmessage','Image added to lightbox');
					else if (!$ok) $this->db_session->set_flashdata('flasherror','Image already in lightbox');
					redirect($this->package.'/image/viewImage/'.$imageid);
				
				} else {
					if ($ok) echo "Image added to lightbox";
					else if (!$ok) echo "Image already in lightbox";
				}
			} else {
				if (!$this->ajax) {
					$this->db_session->set_flashdata('flasherror','You don\'t have permission to add images to this lightbox.');
					redirect($this->package.'/image/viewImage/'.$imageid);
				} else {
					echo "You don't have permission to add images to this lightbox.";
				}
			}
		} else {
			if (!$this->ajax) {
					$this->db_session->set_flashdata('flasherror','Invalid image selected.');
					redirect($this->package.'/image/viewImage/'.$imageid);
				} else {
					echo "Invalid image selected.";
				}
		}
	}
	
	public function createImageSet() {
		$this->load->model('imagesetmodel');
		
		$setdata = null;
		
		$setdata->setname = $this->input->post('setname');
		$setdata->creatorid = $this->authentication->getUserId();
		$setdata->datecreated = date('Y-m-d H:i:s');

		$newid = $this->imagesetmodel->add($setdata);
		
		if (!$this->ajax) {
			$this->db_session->set_flashdata('newid', $newimageid);
			$this->db_session->set_flashdata('flashmessage', 'Image Set Created');
			
			redirect('dam');
		} else {
			include ('Zend/Json.php');
			echo Zend_Json::encode(array('imagesetid' => $newid, 'settitle' => $setdata->setname));
			exit();
		}
	}
	
	public function deleteImageSet($setid) {
		$this->load->model('imagesetmodel');
		
		$imageset = $this->imagesetmodel->retrieve_by_pkey($setid);
		
		if ($imageset->creatorid !== $this->authentication->getUserId() && !$this->imagesetmodel->userHasFullAccessToImageset($this->authentication->getUserId(), $imagesetid) ) {
			$this->db_session->set_flashdata('flasherror', 'It looks like you don\'t have permission to delete this image set!');
			redirect($this->package.'/image/viewSet/'.$setid);
		}
		
		$this->imagesetmodel->delete($setid);
		
		if (!$this->ajax) {
			$this->db_session->set_flashdata('flashmessage', 'Your image set has now been deleted.  Check and see if you have any images that are now unorganised.');	
			redirect($this->package.'/image/viewSets/');
		} else {
			echo 1;
			die();
		}
		
	}
	
	private function setTags($imageid, $taglist) {
		if ($taglist) {
			$this->load->model('tagsmodel');
			
			$tagCollection = explode(',', $taglist);
			
			foreach ($tagCollection as $tag) {
				if (strlen(trim($tag))) {
					//check if tag already exists in tagset
					$tagid = $this->tagsmodel->tagExists(trim($tag));
					
					//if not already in db add tag
					if (!$tagid) $tagid = $this->tagsmodel->add(array('tag' => trim($tag)));
					
					//tag image
					$this->tagsmodel->tagItem($imageid ,'images' ,$tagid);
				}
			}
		}
	}
	
	public function removeSetAccess($imagesetid) {
		$this->load->model('imagesetmodel');
		$this->load->model('basemodel');
		
		$imageset = $this->imagesetmodel->retrieve_by_pkey($imagesetid);
		
		if ($imageset->creatorid !== $this->authentication->getUserId() && !$this->imagesetmodel->userHasFullAccessToImageset($this->authentication->getUserId(), $imagesetid) ) {
			$this->db_session->set_flashdata('flasherror', 'I can\'t remove that access as you don\'t have permission to remove it!');
			redirect($this->package.'/image/viewSet/'.$imagesetid);
		}
		
		$accessid = $this->input->post('accessid');
		
		if ($accessid && intval($accessid)) {
			$this->basemodel->setModel('imagesets_access');
			$this->basemodel->delete_by_pkey($accessid);
			
			$this->db_session->set_flashdata('flashmessage', 'Access to this image set has now been removed.');
		}
		
		redirect($this->package.'/image/viewSet/'.$imagesetid);
	
	}
	
	/*public function setSetAccess($imagesetid) {
		$this->load->model('imagesetmodel');
		$this->load->model('basemodel');
		
		//check that the user is allowed to set access controls for a lightbox.
		
		$imageset = $this->imagesetmodel->retrieve_by_pkey($imagesetid);
		if ($imageset->creatorid !== $this->authentication->getUserId() && !$this->imagesetmodel->userHasFullAccessToImageset($this->authentication->getUserId(), $imagesetid) ) redirect($this->package.'/image/viewSet/'.$imagesetid);
		
		$user = $this->input->post('user');
		$group = $this->input->post('groups');
	
		$data = null;
	
		$data->imagesetsid = $imagesetid;
		
		if ($this->input->post('access') == 'full') $data->full = 1;
		else $data->full = 0;
		
		if ($group) $data->groupsid = $group;
		else if ($user) $data->usersid = $user;

		$data->datecreated = date('Y-m-d H:i:s');
		
		$this->basemodel->setModel('imagesets_access');
		$this->basemodel->add($data);
		
		$this->db_session->set_flashdata('flashmessage', 'Access to Imageset set-up');
		
		redirect($this->package.'/image/viewSet/'.$imagesetid);
	}*/
	
	/*public function setSetName($imagesetid) {
		$this->load->model('imagesetmodel');
		
		//check that the user is allowed to set access controls for a lightbox.
		
		$imageset = $this->imagesetmodel->retrieve_by_pkey($imagesetid);
		if ($imageset->creatorid !== $this->authentication->getUserId() && !$this->imagesetmodel->userHasFullAccessToImageset($this->authentication->getUserId(), $imagesetid)) redirect($this->package.'/image/viewSet/'.$imagesetid);
		$data = null;
		$data->setname = $this->input->post('setname');
		
		$this->imagesetmodel->modify($imagesetid, $data);
		
		if (!$this->ajax) {
			$this->db_session->set_flashdata('flashmessage', 'Imageset name changed');	
			redirect($this->package.'/image/viewSet/'.$imagesetid);
		} else {
			echo 1;
			die();
		}
	}*/

	public function saveSearch() {
		$this->load->model('basemodel');
		$this->basemodel->setModel('savedsearches');
		
		$terms = $this->getSearchTerms();
		
		$terms->searchtitle = $this->input->post('searchtitle');
		
		$terms->searchTags = serialize($terms->searchTags);
		$terms->searchImagesets = serialize($terms->searchImagesets);
		
		$terms->usersid = $this->authentication->getUserId();
		
		$newid = $this->basemodel->add($terms);
		
		$this->db_session->set_flashdata('flashmessage', 'Search terms saved');
		$this->db_session->set_flashdata('newid', $newid);
		
		redirect($this->package.'/image/search/'.$newid);
	}
	
	public function downloadPalette($imageid) {
		$this->load->model('imagemodel');
		$colordata = $this->imagemodel->getColours($imageid);
		
		$colors = array();
		$names = array();
		
		foreach($colordata as $color) {
			$colors[] = $color->colorcode;
			$names[] = $color->colorcode;
		}
		
		$this->load->library('adobepalettegenerator');
		
		$ase = $this->adobepalettegenerator->mkASE($colors, $names);
		
		header("Content-Type: force-download"); 
		header("Content-Disposition: attachment; filename=\"palette.ase\""); 
		echo $ase;
		exit();
	}
	
}
?>