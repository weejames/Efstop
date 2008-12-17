<?php
include_once('s3.class.php');

class amazonStorage {
	
	public function amazonStorage() {
	
	}
	
	/**
	 * Returns an image identifier representing the image obtained from the given filename, using only GD, returns an empty string on failure
	 *
	 * @param string $fileName
	 * @return image identifier
	 */
	
	public function getS3($options = null) {
		return new s3($options);
	}
	
}
?>