<?php
include_once('classPhpPsdReader.php');

class PsdReader {
	
	public function PsdReader() {
	
	}
	
	/**
	 * Returns an image identifier representing the image obtained from the given filename, using only GD, returns an empty string on failure
	 *
	 * @param string $fileName
	 * @return image identifier
	 */
	
	public function imagecreatefrompsd($fileName) {
		$psdReader = new PhpPsdReader($fileName);
		if (isset($psdReader->infoArray['error'])) return '';
		else return $psdReader->getImage();
	}
	
}
?>