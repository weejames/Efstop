<div class="ViewImage clearfix">

<h2><?=$image->title;?> <small><?=$image->imagecode;?></small></h2>

<div class="imageInformation clearfix"> 
    
    <?php if($next || $previous):?>
    
    <ul class="NextPrev clearfix">
    <?php if($next): if (strlen($tag_string)) $alt_tag_string = $tag_string . '/'; else $alt_tag_string = '';?>
		<?php if (strlen($tag_string)):?>
		<li class="next">
			<a
			class="thumb"
			href="<?=site_url('image/'.$alt_tag_string.$next->id.'/'.$location);?>"
			title="Next Image: <?=$next->title;?>"
			style="background-image: url('<?=resizedImageURL('image_store/1500s/'.$next->previewname, 85, 85, true);?>')">
			</a>
		</li>
		<?php else:?>
		<li class="next">
			<a
			class="thumb"
			href="<?=site_url('dam_controllers/image/viewImage/'.$next->id.'/'.$location);?>"
			title="Next Image: <?=$next->title;?>"
			style="background-image: url('<?=resizedImageURL('image_store/1500s/'.$next->previewname, 85, 85, true);?>')">
			</a>
		</li>
		<?php endif;?>
	<?php endif;?>
    
	<?php if($previous): if (strlen($tag_string)) $alt_tag_string = $tag_string . '/'; else $alt_tag_string = '';?>
		<?php if (strlen($tag_string)):?>
			<li class="prev">
				<a
				class="thumb"
				href="<?=site_url('image/'.$alt_tag_string.$previous->id.'/'.$location);?>"
				title="Previous Image: <?=$previous->title;?>" 
				style="background-image: url('<?=resizedImageURL('image_store/1500s/'.$previous->previewname, 85, 85, true);?>')">
				</a>
			</li>
		<?php else:?>
			<li class="prev">
			<a
				class="thumb"
				href="<?=site_url($package.'/image/viewImage/'.$previous->id.'/'.$location);?>"
				title="Previous Image: <?=$previous->title;?>"
				style="background-image: url('<?=resizedImageURL('image_store/1500s/'.$previous->previewname, 85, 85, true);?>')">
				</a>
			</li>
		<?php endif;?>
	<?php endif;?>
	
	</ul>
 
	
	<?php endif;?>
	
	<p>
		<input type="hidden" name="imageid" value="<?=$image->id;?>" class="imageid" />
		<input class="button" type="submit" name="submit" value="Add To Current Lightbox" />
	</p>

	<h3>Downloads</h3>
		<ul>
			<li>
				<a href="<?=resizedImageURL('image_store/1500s/'.$image->previewname, 1000, 1000, true);?>">Medium Thumbnail (1000px)</a>
			</li>
			<li>
				<a href="<?=resizedImageURL('image_store/1500s/'.$image->previewname, 1500, 1500, true);?>">Large Thumbnail (1500px)</a>
			</li>
			<li>
				<a title="<?=$image->width;?>px * <?=$image->height;?>px" href="<?=site_url($package.'/image/download/full/'.$image->id);?>">Original Image</a>
			</li>
	</p>
	
 	<?php if($image->description):?>
    <h3>Description</h3>
	<p><?=$image->description;?></p>
	<?php endif;?>
	
	
	<h3>Dimensions</h3>
	<p><?=round(($image->filesize/1024), 2);?>mb</p>
	<p><?=$image->width;?>px * <?=$image->height;?>px</p>
	<p><?=round($image->width/300, 2);?>" * <?=round($image->height/300, 2);?>" at 300dpi</p>

	<?php if($changeDetails):?>
	<h3>Options</h3>
	<p>
		<a href="<?=site_url($package.'/image/set_properties/'.$image->id);?>">Modify Tags and Description</a>
	</p>
	<p class="deletelink">
		<a href="<?=site_url($package.'/image/delete/'.$image->id.'/'.$location);?>" class="red deleteLink">Delete This Image</a>
	</p>
	<?php endif;?>

    <div class="tagsContainer"> 
        <h3>Tags</h3>
            <ul class="taglist">
                <?=$taglist;?>
            </ul>
      
        <?=form_open($package.'/image/tagImage/'.$image->id.'/'.$location, array('class' => 'addkeywords'));?>
            <input type="text" name="keywords" id="keywords" /><input class="button" type="submit" name="submit" value="Add Tag" />
        <?=form_close();?>
    </div>
	
    <?php if ($colours):?>
    <h3>Image Colours</h3>
    <ul class="swatches">
		<?php foreach($colours as $colour):?>
        <li style="background-color: #<?=$colour->colorcode;?>;">#<?=$colour->colorcode;?></li>
		<?php endforeach;?>
    </ul>
    <a class="swatch" href="<?=site_url('dam_controllers/image/downloadPalette/'.$image->id);?>">Download Adobe Palette</a>
    <?php endif;?>

</div>
<?php if ($image->orientation == 'P') $imagewidth = ($image->width / $image->height) * 700; else $imagewidth = 700;?>
<div id="imageHolder" class="clearfix" style="width: <?=$imagewidth;?>px">
	
	<ul>
		<li class="image" style="min-height: 200px">
			<img src="<?=resizedImageURL('image_store/1500s/'.$image->previewname, 700, 700, true);?>" />
			<p class="uploader">Uploaded by <?=$image->creator;?></p>
		</li>
	</ul>
	
	<div id="Comments">	
		<h3>Add Comment</h3>
			<?=form_open('dam_controllers/image/comment/'.$image->id);?>
			<textarea name="comment"></textarea>
			<input type="hidden" name="imageid" value="<?=$image->id;?>" />
			<input class="button" type="submit" name="submit" value="Comment" />
			<?=form_close();?>

		<?php if($comments):?>

		<h3>Comments</h3>
		<ol>
		<?php foreach($comments as $key => $comment):?>
			<li class="clearfix">
				<h4 style="text-align: right;"><strong><?=$comment->firstname;?> <?=$comment->lastname;?></strong></h4>
				<img style="float: right" src="<?="http://www.gravatar.com/avatar.php?size=60&default=".urlencode('/assets/default/images/defaultperson.png')."&gravatar_id=".md5(trim($comment->emailaddress));?>" width="60" height="60" />
				<blockquote><span><?=nl2br($comment->comment);?></span></blockquote>
			</li>
		<?php endforeach;?>
		</ol>
		<?php endif;?>
	</div>

</div>