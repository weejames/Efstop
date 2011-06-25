<?=$intro_notification;?>

<div id="ImageSets">
	<?php if ($images):?>
	<div class="PageLinks">
	<p><?=$showing_string;?></p>
	</div>
	<?php if ($page_links):?>
	<div class="PageLinks">
		<?=$page_links;?>
	</div>
	<?php endif; ?>
	<ul class="ImageSet clearfix">
		
		<?php foreach($images as $key => $image):?> 
		<li>
			<a class="thumb" href="<?=site_url('image/'.$tag_string.'/'.$image->id);?>" title="<?=$image->title."\r\n\r\n".$image->description;?>"><img src="<?=resizedImageURL('image_store/1500s/'.$image->previewname, 1000, 160, true);?>" /></a>

			<?php if($lightboxes):?>
			<?=form_open($package.'/image/addToLightbox/'.$image->id, array('class' => 'lightboxselect', 'id' => 'lightboxselect'.$key ) );?>

			<select name="lightboxid">
				<?php foreach($lightboxes as $lightbox):?>
				<option value="<?=$lightbox->id;?>" <?php if($lastlightbox == (int)$lightbox->id):?>selected="selected"<?php endif;?>><?=$lightbox->boxtitle;?></option>
				<?php endforeach;?>
			</select>

			<input type="hidden" name="imageid" value="<?=$image->id;?>" class="imageid" />
			<input class="add2Lb button" type="submit" name="submit" value="Add to Lightbox" />
			<?=form_close();?>
			<?php endif;?>
		</li>
		<?php endforeach;?>
		
	</ul> 

	<div class="PageLinks">
	<p><?=$showing_string;?></p>
	</div>
	<?php if ($page_links):?>
	<div class="PageLinks">
		<?=$page_links;?>
	</div>
	<?php endif; ?>
	<?php else:?>
		<p>No images? Try <a href="<?=site_url('upload');?>">uploading</a> some from your collection.</p>
	<?php endif;?>
</div>