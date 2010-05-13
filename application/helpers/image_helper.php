<?php

	function resizedImageURL($path, $width = false, $height = false, $maintain_ratio = false, $master_dim = false, $crop_position = false) {
		$return_url = site_url('assetmanager/imagesizer/'.rawurlencode(rawurlencode(rawurlencode($path))));
		
		if ($width && is_integer($width)) $return_url .= '/'.$width;
		else $return_url .= '/false';
		
		if ($height && is_integer($height)) $return_url .= '/'.$height;
		else $return_url .= '/false';
		
		if ($maintain_ratio && is_bool($maintain_ratio)) $return_url .= '/'.$maintain_ratio;
		else $return_url .= '/false';
		
		if ($master_dim && in_array($master_dim, array('width','height','auto'))) $return_url .= '/'.$master_dim;
		else $return_url .= '/auto';
		
		if ($crop_position && in_array($crop_position, array('top','middle','bottom'))) $return_url .= '/'.$crop_position;
		else $return_url .= '/false';
	
		return $return_url;
	}

?>