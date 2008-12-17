<?php foreach($tags as $tag):?>
	<li><span><a href="<?=site_url('image/'.$tag->tag);?>"><?=ucwords($tag->tag);?></a></span> <a class="RemTag deleteLink" href="<?=site_url($package.'/image/removeTag/'.$image->id.'/'.$tag->tag_id);?>" title="Remove the <?=ucwords($tag->tag);?> tag from this image">Remove</a></li>
<?php endforeach;?>
