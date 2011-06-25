<?php
class imageColorExtract {

    var $image;
	var $preview_size = 50;
	var $factor = 32;
	var $gamma_adjust = 15;

    function imageColorExtract() {
		
	}
	
    function getColors($imagePath)  {
		$this->image = $imagePath;
        if (isset($this->image)) {
            $size = GetImageSize($this->image);
            $scale=1;
            if ($size[0]>0)  $scale = min($this->preview_size/$size[0], $this->preview_size/$size[1]);
            
			if ($scale < 1)  {
                $width = floor($scale*$size[0]);
                $height = floor($scale*$size[1]);
            } else {
                $width = $size[0];
                $height = $size[1];
            }
			
            $image_resized = imagecreatetruecolor($width, $height);
			
            if ($size[2]==1)  $image_orig = imagecreatefromgif($this->image);
            else if ($size[2]==2) $image_orig = imagecreatefromjpeg($this->image);
            else if ($size[2]==3) $image_orig=imagecreatefrompng($this->image);
			
            imagecopyresampled($image_resized, $image_orig, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);
			
            $im = $image_resized;
            $imgWidth = imagesx($im);
            $imgHeight = imagesy($im);
			
            for ($y=0; $y < $imgHeight; $y++) {
                for ($x=0; $x < $imgWidth; $x++) {
                    $index = imagecolorat($im,$x,$y);
                    $Colors = imagecolorsforindex($im,$index);

                    $Colors['red']=intval((($Colors['red']) + $this->gamma_adjust)/$this->factor)*$this->factor;
				    $Colors['green']=intval((($Colors['green']) + $this->gamma_adjust)/$this->factor)*$this->factor;
                    $Colors['blue']=intval((($Colors['blue']) + $this->gamma_adjust)/$this->factor)*$this->factor;
                    
					if ($Colors['red'] >= 256) $Colors['red'] = 255 - $this->gamma_adjust;
                    if ($Colors['green'] >= 256) $Colors['green'] = 255 - $this->gamma_adjust;
                    if ($Colors['blue'] >= 256) $Colors['blue'] = 255 - $this->gamma_adjust;
					
                    $hexarray[] = substr("0".dechex($Colors['red']), -2).substr("0".dechex($Colors['green']), -2).substr("0".dechex($Colors['blue']), -2);
                }
            }
            $hexarray = array_count_values($hexarray);
            natsort($hexarray);
            $hexarray = array_reverse($hexarray,true);
            return $hexarray;

        }
    }
}
?>
