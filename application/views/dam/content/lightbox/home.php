<div id="Lightboxes" class="all">
	<h2>My Lightboxes</h2>

	<ul class="container clearfix">
	<?php if ($lightboxes):?>
		
	<?php foreach($lightboxes as $lightbox):?>
		<li class="Lightbox clearfix">

			<h3><a href="<?=site_url($package.'/lightbox/viewBox/'.$lightbox->id);?>"><?=$lightbox->boxtitle;?></a></h3>

			<ul class="clearfix">
			<?php if ($lightbox->images):?>
			<?php foreach($lightbox->images as $curimg => $image):?>
				<li>
					<img src="<?=resizedImageURL('image_store/1500s/'.$image->previewname, 1000, 120, true);?>">
				</li>
			<?php if ($curimg >= 2) break; endforeach; endif;?>
			</ul>
		</li>
	<?php endforeach;?>

	<?php else: ?>
		<p class="notice">At the moment you don't have any lightboxes. Lightboxes allow you to group together subsets of images from any of the image sets you have access to and send them to other efstop users or guests outwith your organisation.</p>
	<?php endif; ?>
	</ul>