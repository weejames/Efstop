<ul class="ImageSet clearfix">
    
    <h2 class="indent" id="currentBoxTitle">Viewing &ndash; <span class="title"><?=$lightbox->boxtitle;?></span></h2>
	
	<input type="hidden" name="lightboxid" id="lightboxid" class="lightboxid" value="<?=$lightbox->id;?>" />
 
    
    <?php if ($images):?>
    
    <?php foreach($images as $image):?>
    <li>
        <a href="<?=base_url().'image_store/preview/'.$image->thumbname;?>" class="thumb thickbox" style="background-image: url('<?=base_url().'image_store/thumbs/'.$image->thumbname;?>')" title="<?=$image->title.": ".$image->description;?>"><?=$image->title;?></a>
		<?php if ($imageDownload):?>
       	<a href="<?=site_url($package.'/guestaccess/download/'.$guestkey.'/'.$image->id);?>">Download High Resolution</a><br />
        <a href="<?=base_url().'image_store/preview/'.$image->previewname;?>">Download Low Resolution</a>
    	<?php endif;?>
    </li>
    <?php endforeach;?>

    <?php else:?>
	<p class="notice">There are no images in this lightbox at the moment.</p>
    <?php endif;?>
    
</ul>