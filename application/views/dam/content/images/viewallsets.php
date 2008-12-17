<?=$intro_notification;?>

<div id="ImageSets">
    <h2 class="indent">Image Sets <a href="#" class="createImageSet">Add New</a></h2>
    
    
    <ul class="ImageSetsContainer clearfix">
<?php if ($imagesets):?>
    
<?php foreach($imagesets as $imageset):?>
        <li class="ImageSetsContainer">
            <a href="<?=site_url('dam_controllers/image/viewSet/'.$imageset->id);?>"><?=$imageset->setname;?></a>
            <small><?php if ($imageset->images) echo count($imageset->images); else echo 0;?> Image<?php if ($imageset->images === false || count($imageset->images) != 1) echo 's';?></small>
            <hr />
                <ul class="clearfix">
                <?php if ($imageset->images):?>
                <?php foreach($imageset->images as $key => $image):?> 
                    <li><a style="background-image: url('<?=base_url().'image_store/thumbs/'.$image->thumbname;?>');" href="<?=site_url('dam_controllers/image/viewSet/'.$imageset->id);?>"></a></li>
                <?php if ($key >= 2) break; endforeach;?>
                <?php endif;?>
                </ul> 
    	</li>
<?php endforeach;?>
    
<?php else:?>
    <p class="notice">No imagesets?  Why not add one so you can start uploading images?</p>
<?php endif;?>
</ul>
</div>