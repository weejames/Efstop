<div class="ViewImage">
<h2><?=$image->title;?> <small><?=$image->imagecode;?></small></h2>

	<div class="imageInformation clearfix">
	<?=form_open($package.'/image/set_properties/'.$image->id, array('class' => 'propertiesForm'));?>

		<fieldset>
			<legend>Change Details</legend>
                <label for="title">Title</label></dt>
                <input type="text" name="title" id="title" value="<?=$image->title;?>">
                <label for="description">Description</label>
                <textarea name="description" id="description"><?=$image->description;?></textarea>
		</fieldset>
		
		<fieldset>
			<legend>Organisation</legend>
                <label for="keywords">Tags</label>
                <p>Seperate tags words or groups of words with a comma.</p>
                <textarea name="keywords" id="keywords"><?=$keywords;?></textarea>
				
		</fieldset>
		
        <input class="button" type="submit" name="submit" value="Submit" />

	<?=form_close();?>
	
	</div>
<?php if ($image->orientation == 'P') $imagewidth = ($image->width / $image->height) * 700; else $imagewidth = 700;?>	
	<div id="imageHolder" class="clearfix" style="width: <?=$imagewidth;?>px">

		<ul>
			
			<li class="image" "width: min-height: 200px"><img src="<?=resizedImageURL('image_store/1500s/'.$image->previewname, 700, 700, true);?>" /><p class="uploader">Uploaded by <?=$image->creator;?></p></li>
		</ul>		
		<?=form_close();?>

	</div>