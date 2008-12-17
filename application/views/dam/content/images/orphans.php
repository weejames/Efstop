<div class="boxWithPadding">

<h2>Set Image Properties</h2>

	<p>Any keyword tags you want to apply to <strong>all</strong> of the images should be entered in the global keywords section.  Remember to separate them with a comma ','.</p>

    <?=form_open($package.'/image/viewOrphans');?>
    <fieldset class="orphanProps">
        <div class="right">
			<label for="keywords">Global Keyword Tags</label>
			<input type="text" name="keywords" id="keywords"></textarea>
        </div>
	</fieldset>

	<ul class="orphans clearfix">
<?php foreach($images as $key => $image):?>
	<li>
      <a class="thumb" href="#" style="background-image: url('<?=resizedImageURL('image_store/1500s/'.$image->previewname, 150, 150, true);?>'); background-position: center center;"></a>
        	<input type="checkbox" name="delete_<?=$image->id;?>" id="delete_<?=$image->id;?>" class="checkbox" /> <label for="delete_<?=$image->id;?>" class="oops" title="Check this box to delete the image">Oops.. I didn't want to upload this.</label>
        
        
			<label for="title_<?=$image->id;?>">Title</label>
			<input type="text" name="title_<?=$image->id;?>" id="title_<?=$image->id;?>" value="<?=$image->title;?>">
			
			<label for="description_<?=$image->id;?>">Description</label>
			<textarea name="description_<?=$image->id;?>" id="description_<?=$image->id;?>"><?=$image->description;?></textarea>
			
			<label for="keywords_<?=$image->id;?>">Tags</label>
			<textarea name="keywords_<?=$image->id;?>" id="keywords_<?=$image->id;?>"></textarea>
			
    	</select>
	</li>
	<input type="hidden" name="imageid[]" value="<?=$image->id;?>" />
	<?php endforeach;?>
	</ul>
	<div style="clear:both;"></div>
	<input type="submit" name="submit" value="Save Details" class="button orphansubmit" />
	<?=form_close();?>
</div>