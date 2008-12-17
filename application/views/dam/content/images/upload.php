<div id="upload" class="boxWithPadding">
    <h2>Upload An Image</h2>

	<ol>
        <li>
			<ul>
				<li>Press 'Add Files' to add image files to your upload queue.</li>
				<li>Supported file formats are JPEG, PNG, GIF, PSD and TIFF at filesizes upto 50MB.</li>
				<li>Once added to the queue click Upload to begin transferring the files from your computer to efstop. Do not navigate away from this page before the Upload has completed.</li>
				<li>When the upload process has complete you will be able to edit and view the properties of your images.</p></li>
			</ul>
		</li>
        
        <li>
        
        <?=form_open_multipart($package.'/image/upload');?>
                <fieldset id="fileUpload">
				
                <label for="imageupload">Select Your File</label>
				<input type="file" name="imageupload" id="imageupload" />
				
				<input type="submit" name="submit" id="submit" value="Upload Image" />
            </fieldset>
        <?=form_close();?>
        </li>
        
        <li><p class="uploaded">All your images are uploaded! <a href="<?=site_url($package.'/image/viewOrphans');?>">Edit their details</a>.</p></li>
    </ol>

 

</div>