<?=$intro_notification;?>

		<?php if ($this->authentication->isLoggedIn()): ?>
		<ul id="Navotron" class="clearfix">
			<li class="crumb"><a class="root" href="<?=site_url('image')?>"><?=$account->account_name;?>'s Library</a></li>
			<?php $cur_string = ''; foreach($tag_array as $key => $tag): $cur_string .= $tag.'/'; ?>
				<li class="crumb <?php if ( count($tag_array) - 1 == $key ):?>last<?php endif;?>"><a href="<?=site_url('image/'.$cur_string);?>"><?=ucfirst($tag);?></a></li>
			<?php endforeach;?>
			<?php if ($imagetags):?>
			<li><span class="add">Filter by Tag</span>
				<div class="tagpicker" style="display: none;">
					<ul class="alpha clearfix">
						<li><a href="#">All</a></li>
						<li><a href="#">#</a></li>
						<li><a href="#">A</a></li>
						<li><a href="#">B</a></li>
						<li><a href="#">C</a></li>
						<li><a href="#">D</a></li>
						<li><a href="#">E</a></li>
						<li><a href="#">F</a></li>
						<li><a href="#">G</a></li>
						<li><a href="#">H</a></li>
						<li><a href="#">I</a></li>
						<li><a href="#">J</a></li>
						<li><a href="#">K</a></li>
						<li><a href="#">L</a></li>
						<li><a href="#">M</a></li>
						<li><a href="#">N</a></li>
						<li><a href="#">O</a></li>
						<li><a href="#">P</a></li>
						<li><a href="#">Q</a></li>
						<li><a href="#">R</a></li>
						<li><a href="#">S</a></li>
						<li><a href="#">T</a></li>
						<li><a href="#">U</a></li>
						<li><a href="#">V</a></li>
						<li><a href="#">W</a></li>
						<li><a href="#">X</a></li>
						<li><a href="#">Y</a></li>
						<li><a href="#">Z</a></li>
					</ul>
					<ul class="tags clearfix">
					<?php foreach($imagetags as $key => $tag):?>
						<?php if( !in_array( $tag->tag, $tag_array) ): if (strlen($tag_string)) $alt_tag_string = '/'.$tag_string; else $alt_tag_string = ''; ?>
							<li><a href="<?=site_url('image'.$alt_tag_string.'/'.$tag->tag);?>"><?=ucfirst($tag->tag);?></a></li>
						<?php endif;?>
					<?php endforeach;?>
					</ul>
				</div>
			</li>
			<?php endif;?>
		</ul>
		
		<?php if($tag_array):?>
		<p>
			<a href="" class="addall">Add all of these images to the current lightbox</a></p>
			<input type="hidden" id="tag_string" value="<?=$tag_string;?>" />
		<?php endif;?>
		<?php endif;?>
		

<div id="ImageSets">
	<?php if ($images):?>
	<div class="PageLinks left">
	<p><?=$showing_string;?></p>
	</div>
	<?php if ($page_links):?>
	<div class="PageLinks right">
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

	<?php if ($page_links):?>
	<div class="PageLinks">
		<?=$page_links;?>
	</div>
	<?php endif; ?>
	<?php else:?>
		<p>No images? Try <a href="<?=site_url('upload');?>">uploading</a> some from your collection.</p>
	<?php endif;?>
</div>