<?=$intro_notification;?>

<div id="Lightboxes" class="clearfix">
    <h2 class="indent">Lightboxes <a href="<?=site_URL('dam_controllers/lightbox');?>">View All</a> </h2>
    
	 <?php if ($lightboxes):?>
    <ul class="clearfix">
		<?php foreach($lightboxes as $lightbox):?>
				<li>
					<small><?php if ($lightbox->images) echo count($lightbox->images); else echo 0;?> Image<?php if ($lightbox->images === false || count($lightbox->images) !== 1) echo 's';?></small>
					<a class="red" href="<?=site_url('dam_controllers/lightbox/viewBox/'.$lightbox->id);?>"><?=$lightbox->boxtitle;?></a>
					
					<?php if($lightbox->images): foreach($lightbox->images as $curbox => $image):?> 
					<a class="thumb" href="<?=site_url('dam_controllers/lightbox/viewBox/'.$lightbox->id);?>"><img src="<?=resizedImageURL('image_store/1500s/'.$image->previewname, 100, 100, true);?>" /></a>
					<?php if ($curbox >= 3) break;endforeach; endif;?>
				</li>
		<?php endforeach;?>
	</ul>
	<?php else:?>
		<div class="nolightboxes"><p class="notice">No lightboxes? Why not add one so you can start creating image collections?</p></div>
	<?php endif;?>
</div>


<div id="RecentUploads" class="clearfix">
    <h2 class="indent">Images <a href="<?=site_URL('image');?>">View All</a></h2>
<?php if($images):?>
      <ul class="clearfix">
        <?php foreach($images as $key => $image):?>
        <li>  
          <a style="background-image: url('<?=resizedImageURL('image_store/1500s/'.$image->previewname, 120, 120, true);?>');" href="<?=site_url('image/'.$image->id);?>"><?=$image->title; ?></a>
        </li>
        <?php endforeach;?>
      </ul>
<?php else:?>      
	<div class="noimages">
		<p>At the moment no images have been uploaded to your account.</p>
		<p>Head on over to the <a href="<?=site_url();?>">upload</a> page to get started.</p>
	</div>
<?php endif;?>
</div>