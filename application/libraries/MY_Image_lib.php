<?php

class MY_Image_lib extends CI_Image_lib {

		/**
	 * Image Process Using ImageMagick
	 *
	 * This function will resize, crop or rotate
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */		
	function image_process_imagemagick($action = 'resize')
	{
		//  Do we have a vaild library path?
		if ($this->library_path == '')
		{
			$this->set_error('imglib_libpath_invalid');
			return FALSE;
		}
				
		if ( ! eregi("convert$", $this->library_path))
		{
			if ( ! eregi("/$", $this->library_path)) $this->library_path .= "/";
		
			$this->library_path .= 'convert';
		}
		
		// Execute the command
		$cmd = $this->library_path." -quality ".$this->quality;
	
		if ($action == 'crop')
		{
			$cmd .= " -crop ".$this->width."x".$this->height."+".$this->x_axis."+".$this->y_axis." \"$this->full_src_path\" \"$this->full_dst_path\" 2>&1";
		}
		elseif ($action == 'rotate')
		{
			switch ($this->rotation_angle)
			{
				case 'hor' 	: $angle = '-flop';
					break;
				case 'vrt' 	: $angle = '-flip';
					break;
				default		: $angle = '-rotate '.$this->rotation_angle;
					break;
			}			
		
			$cmd .= " ".$angle." \"$this->full_src_path\" \"$this->full_dst_path\" 2>&1";
		}
		else  // Resize
		{
			$imageinfo = getimagesize($this->full_src_path);
			
			if (!isset($imageinfo['channels'])) $imageinfo['channels'] = 4;
			switch ($imageinfo['channels']) {
				case 3:
					$cmd .= " -resize ".$this->width."x".$this->height." \"$this->full_src_path\" \"$this->full_dst_path\" 2>&1";
				break;
				default:
					$cmd .= " -resize ".$this->width."x".$this->height." \"$this->full_src_path\" -profile /usr/share/color/icc/Adobe\ ICC\ Profiles/CMYK\ Profiles/USWebCoatedSWOP.icc -profile /usr/share/color/icc/Adobe\ ICC\ Profiles/RGB\ Profiles/AdobeRGB1998.icc \"$this->full_dst_path\" 2>&1";
				break;
			} 
			
		}
	
		$retval = 1;
		
		@exec($cmd, $output, $retval);

		//	Did it work?	
		if ($retval > 0)
		{
			$this->set_error('imglib_image_process_failed');
			return FALSE;
		}
		
		// Set the file to 777
		@chmod($this->full_dst_path, 0777);
		
		return TRUE;
	}

}

?>