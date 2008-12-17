<div id="Lightboxes" class="all">
	<h2>Lightboxes <a href="#" class="createLightbox">Add New</a></h2>

<ul class="container clearfix">
<?php if ($lightboxes):?>
	
<?php foreach($lightboxes as $lightbox):?>
		<li class="Lightbox clearfix">
		<small><?php if ($lightbox->images) echo count($lightbox->images); else echo 0;?> Image<?php if ($lightbox->images === false || count($lightbox->images) != 1) echo 's';?></small>
		<a href="<?=site_url($package.'/lightbox/viewBox/'.$lightbox->id);?>"><?=$lightbox->boxtitle;?></a>
		
		<hr />
			<ul class="clearfix">
			<?php if ($lightbox->images):?>
			<?php foreach($lightbox->images as $curimg => $image):?>
				<li class="clearfix">
            		<a class="thumb"><img src="<?=resizedImageURL('image_store/1500s/'.$image->previewname, 120, 120, true);?>"></a>
				</li>
			<?php if ($curimg >= 2) break; endforeach; endif;?>
			</ul>
		</li>
<?php endforeach;?>

<?php else: ?>
	<p class="notice">At the moment you don't have any lightboxes.  Lightboxes all you to group together subsets of images from any of the image sets you have access to and send them to other efstop users or guests outwith your organisation.</p>
<?php endif; ?>
</ul>