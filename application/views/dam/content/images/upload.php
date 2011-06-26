<div id="upload">
    <h2>Image Upload</h2>

	<p>Supported file formats are JPEG, PNG, GIF, PSD and TIFF at filesizes up to X MB.</p>
	<p>Once added to the queue click Upload to begin transferring the files from your computer to efstop. Do not navigate away from this page before the Upload has completed.</p>
	<p>When the upload process has complete you will be able to edit and view the properties of your images.</p>

	<?=form_open_multipart($package.'/image/upload');?>
	<fieldset id="fileUpload">
	
		<label for="imageupload">Image to Upload</label>
		<input type="file" name="imageupload" id="imageupload" />

		<input class="button" type="submit" name="submit" id="submit" value="Upload" />
		
	</fieldset>
	<?=form_close();?>
</div>