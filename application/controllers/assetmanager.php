<?php

class AssetManager extends Controller {
	private $echo_string = '';
	private $used_js = array();
	 
	public function AssetManager() {
        parent::Controller();
    }

    public function index() {
    }
    
    public function css($theme = '', $filelist = '') {
    	$this->load->helper('file');		
		
		// get list of files.
    	$files = explode(',', $filelist);
    	
    	foreach ($files as $key => $filename) {
    		//check app folder for file, then share, then resources, then resources share
			if (file_exists('assets/'.$theme.'/css/'.$filename.'.css')) $this->echo_string .= "/*$filename.css APP*/\r\n\r\n". read_file('assets/'.$theme.'/css/'.$filename.'.css') ."\r\n\r\n";
			else if (file_exists('assets/share/css/'.$filename.'.css')) $this->echo_string .= "/*$filename.css APP SHARE*/\r\n\r\n". read_file('assets/share/css/'.$filename.'.css') ."\r\n\r\n";
			else $this->echo_string .= "/*$filename.css NOT FOUND*/\r\n\r\n";
    	}
    	
    	$this->echo_string = preg_replace('/RESOURCE\((.*)\)/', 'url('.site_url('assetmanager/image/'.$theme.'/$1') .')', $this->echo_string, -1);
		$this->echo_string = preg_replace('/BASEURL/', base_url(), $this->echo_string, -1);

        header('Content-type: text/css');
		header("Cache-Control: must-revalidate");
		$offset = 24 * 60 * 60 ;
		$ExpStr = "Expires: " .
		gmdate("D, d M Y H:i:s",
		time() + $offset) . " GMT";
		header($ExpStr);
		
    	echo $this->echo_string;die();
    }
    
	public function js($theme = '', $filelist = '') {
		$this->load->helper('file');

		// get list of files.
    	$files = explode(',', $filelist);
    	
    	$this->echo_string = "baseURL = '".base_url()."';\r\n\r\n";
		$this->echo_string .= "siteURL = '".site_url()."';\r\n\r\n";
    	//$this->echo_string .= "sessionID = '".$this->db_session->sessionid()."';\r\n\r\n";
    	
    	foreach ($files as $key => $filename) {
    		if (!array_key_exists($filename, $this->used_js)) {
				//check app folder for file, then share, then resources, then resources share
				if (file_exists('assets/'.$theme.'/script/'.$filename.'.js')) $this->echo_string .= "/*$filename.js*/\r\n\r\n". read_file('assets/'.$theme.'/script/'.$filename.'.js') ."\r\n\r\n";
				else if (file_exists('assets/share/script/'.$filename.'.js')) $this->echo_string .= "/*$filename.js*/\r\n\r\n". read_file('assets/share/script/'.$filename.'.js') ."\r\n\r\n";
				else $this->echo_string .= "/*$filename.js NOT FOUND*/\r\n\r\n";
				$this->used_js[$filename] = $filename;
    		}
    	}
    	
		header("Content-type: text/javascript; charset: UTF-8");
		$offset = 60 * 60 ;
		$ExpStr = "Expires: " .
		gmdate("D, d M Y H:i:s",
		time() + $offset) . " GMT";
		header($ExpStr);
		
    	echo $this->echo_string;die();
    }
    
	public function image($theme = '', $filename = '') {
		$this->load->helper('file');		
		
		$filebits = explode('.', $filename);
		$filepath = false;

		if (file_exists('assets/'.$theme.'/images/'.$filename)) $filepath = 'assets/'.$theme.'/images/'.$filename;
		else if (file_exists('assets/share/images/'.$filename)) $filepath = 'assets/share/images/'.$filename;
		
		if ($filepath != false && strlen($filepath)) {
			
			switch( $filebits[1] ) {
				case "gif": $ctype="image/gif"; break;
				case "png": $ctype="image/png"; break;
				case "jpeg":
				case "jpg": $ctype="image/jpg"; break;
				default: $ctype="application/force-download";
			}
			
			header("Content-Type: $ctype");
			header("Content-Disposition: attachment; filename=\"".basename($filepath)."\";" );
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: ".filesize($filepath));
			readfile($filepath);
			
			header("Pragma: public"); // required
			//header("Cache-Control: must-revalidate");
			$offset = 7 * 24 * 60 * 60 ;
			$ExpStr = "Expires: " .
			gmdate("D, d M Y H:i:s",
			time() + $offset) . " GMT";
			header($ExpStr);
		}
		die();
    }
    
    public function imagesizer($path, $width = false, $height = false, $maintain_ratio = true, $master_dim = 'auto', $crop_position = false) {
		$filepath = urldecode($path);
    	$filetype = end(explode(".", $filepath));
    	
    	$cachedfile = md5($filepath.(int)$width.(int)$height.(int)$maintain_ratio.$master_dim.$crop_position).'.'.$filetype;

    	if ($maintain_ratio == 'false') $maintain_ratio = false;
    	else $maintain_ratio = true;

    	if (file_exists('./'.$filepath)) {
			
			$cache_path = $this->config->item('cache_path');
			
			if (!strlen($cache_path)) $cache_path = 'image_store/cache/';
			
			if (!file_exists($cache_path.$cachedfile)) {
				//resize image
				$config['image_library'] = 'gd2';
				$config['source_image'] = './'.$filepath;
				$config['new_image'] = $cache_path.$cachedfile;
				$config['maintain_ratio'] = $maintain_ratio;
				$config['master_dim'] = $master_dim;
				if ($width) $config['width'] = $width;
				if ($height) $config['height'] = $height;
				
				$this->load->library('image_lib', $config);

				$this->image_lib->resize();
				
				//check if auto crop is set to allow us to cutthis image to size and where we should do it.
				if ($crop_position && in_array($crop_position, array('top','middle','bottom'))) {
				
					//get new images dimensions..
					$dimensions = getimagesize($cache_path.$cachedfile);
					
					
					if ($master_dim == 'width' && $dimensions[1] > $height) {
						$this->image_lib->clear();
						
						$config = array();
						$config['image_library'] = 'gd2';
						$config['source_image'] = $cache_path.$cachedfile;
						$config['x_axis'] = 0;
						$config['y_axis'] = 0;
						$config['maintain_ratio'] = false;
						$config['master_dim'] = $master_dim;
						$config['width'] = $width;
						$config['height'] = $height;
						
						if ($crop_position == 'top') $config['y_axis'] = 0;
						else if ($crop_position == 'middle') $config['y_axis'] = ($dimensions[1] - $height) / 2;
						else if ($crop_position == 'bottom') $config['y_axis'] = ($dimensions[1] - $height);
						
						$this->image_lib->initialize($config);
						
						$this->image_lib->crop();
					}
				
				}
				
			}
			
			if (file_exists($cache_path.$cachedfile)) $finalpath = $cache_path.$cachedfile;
			else $finalpath = './'.$filepath;
	
			header("Content-Type: image/jpg");//.mime_content_type($finalpath));
			header("Content-Disposition: filename=\"".basename($filepath)."\";" );
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: ".filesize($finalpath));
			
			header("Pragma: public"); // required
			$offset = 7 * 24 * 60 * 60 ;
			$ExpStr = "Expires: " .
			gmdate("D, d M Y H:i:s",
			time() + $offset) . " GMT";
			header($ExpStr);
	
			readfile($finalpath);
		} else {
			//show_404();
		}
		
		die();
    }
    
	private function gzip_accepted() {

	    if(isset($_SERVER['HTTP_ACCEPT_ENCODING']))
	        $HTTP_ACCEPT_ENCODING = $_SERVER['HTTP_ACCEPT_ENCODING'];
	    else return false;
	
	    if (strpos($HTTP_ACCEPT_ENCODING, 'gzip') === false) return false;
	    if (strpos($HTTP_ACCEPT_ENCODING, 'x-gzip') === false) {
	        return 'gzip';
	    } else {
	        return 'x-gzip';
	    }
	}
    
}

?>
