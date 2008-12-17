<ul class="ImageSet clearfix">
    <h2 class="indent"><?=$settitle;?></h2>
    <?php if ($images):?>
    <?php foreach($images as $key => $image):?>
	<li>
        <a class="thumb" style="background-image: url('<?=resizedImageURL('image_store/1500s/'.$image->previewname, 160, 160, true, 'width', 'middle');?>')" href="<?=site_url($package.'/image/viewImage/'.$image->id.'/'.$returnTo);?>"><?=$image->title;?></a>
	
		  <?php if($lightboxes):?>
		  <?=form_open($package.'/image/addToLightbox/'.$image->id, array('class' => 'lightboxselect', 'id' => 'lightboxselect'.$key));?>
			
			<select name="lightboxid" id="lightboxid">
                <?php foreach($lightboxes as $lightbox):?>
                <option value="<?=$lightbox->id;?>"><?=$lightbox->boxtitle;?></option>
                <?php endforeach;?>
			</select>
			
			<input class="add2Lb button" type="submit" name="submit" value="Add To Light Box" />
		
		<?=form_close();?>
		<?php endif;?>
	</li>
	<?php endforeach;?>


    <?php else:?>
    <p>Unfortunately no images matched what you were looking for.  Try altering you search terms or making them less specific.</p>
    <?php endif;?>
</ul>

<?php if (!$savedsearch):?>
    <?=form_open($package.'/image/saveSearch/', array('class' => 'propertiesForm'));?>
    <fieldset class="boxWithPadding">
        <legend>Save Search?</legend>
        <p>Enter the name you want to save these search terms under and it will be made availible on your dashboard to easily access this set of images in the future.</p>
        <label for="searchtitle">Save As..</label>
        <input type="text" name="searchtitle" id="searchtitle" />
        <input type="submit" value="Save This Search" name="submit" class="button" />
    </fieldset>
    <?=form_close();?>
<?php endif;?>
