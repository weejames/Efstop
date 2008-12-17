<?php

function processImage($path, $filename, $targetpath, $targetfilename) {

	$CI =& get_instance();
	$CI->load->library('image_lib');
	
	if (file_exists($path.$filename) ) {
		$image_data = null;
	
		$config = array();
	
		//generate thumbnail0
		$config['image_library'] = 'ImageMagick';
		$config['source_image'] = $path.$filename;
		$config['new_image'] = $targetpath.$targetfilename;
		$config['maintain_ratio'] = true;
		$config['width'] = 1500;
		$config['height'] = 1500;
		$config['quality'] = 100;
		$config['library_path'] = '/usr/bin/convert';
		
		$image_data->exif = serialize( exif_read_data ( $path.$filename, 'ANY_TAG' , true , false ) );
		
		$CI->image_lib->initialize($config); 
		
		if (!$CI->image_lib->resize()) {
			echo $CI->image_lib->display_errors();
		}
		$CI->image_lib->clear();
	
		return $image_data;
	} else return false;

}

?>