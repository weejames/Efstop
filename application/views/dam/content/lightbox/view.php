<div id="MainContent">
	<h2>Lightbox: <?=$lightbox->boxtitle;?></h2>

	<input type="hidden" name="lightboxid" id="lightboxid" class="lightboxid" value="<?=$lightbox->id;?>" />

	<?php if ($images):?>
	<ul class="Lightbox clearfix">
	<?php foreach($images as $image):?>
		<li>
			<?php if ($fullImageView):?>
			<a href="<?=site_url($package.'/image/viewImage/'.$image->id.'/fromLightbox'.$lightbox->id);?>" class="thumb" title="<?=$image->title."\r\n\r\n".$image->description;?>"><img src="<?=resizedImageURL('image_store/1500s/'.$image->previewname, 1000, 160, true);?>" /></a>
			<?php else:?>

			<?php endif;?>

			<?php if($modifyLightbox):?>
			
			<?=form_open($package.'/lightbox/removeFromLightbox/'.$lightbox->id, array('class' => 'lightboxselect' ));?>
			<input class="delFromLb button" type="submit" name="submit" value="Remove" />
			<input type="hidden" name="lightboxid" value="<?=$lightbox->id;?>" />
			<input type="hidden" name="imageid" value="<?=$image->id;?>" />
			<?=form_close();?>
			<?php endif;?>
		</li>
	<?php endforeach;?>
	</ul>

	<?php else:?>
	<p class="notice">There are currentlly no images in this lightbox.</p>
	<?php endif;?>
</div>

<div id="Sidebar">
	<div id="LightboxOptions">
		<h3>Lightbox Options</h3>

		<?php if ($setAccess):?>
		<p><a href="" class="OpenLightboxSharing">Share this Lightbox</a></p>
		<?php if($currentAccess):?>
		<p><a href="" class="OpenLightboxAccess">Change Sharing Permissions</a></p>
		<?php endif;?>
		<p><a href="#" class="renamebox">Rename this Lightbox</a></p>
		<p><a href="<?=site_url($package.'/lightbox/delete/'.$lightbox->id);?>" class="deleteLink">Delete this Lightbox</a></p>
		<?php endif;?>
	</div>
<div>


<?php if ($setAccess):?>
<div style="display: none" id="LightboxSharingModal" title="Share this Lightbox">
	
    <?=form_open($package.'/lightbox/setAccess/'.$lightbox->id, array('id' => 'accessControl'));?>
    
    <fieldset>

		<p>Who do you want to share this Lightbox with?</p>
        
		<label><input type="radio" value="group" name="whoto" id="whoto_group" checked="checked" class="checkbox" /> A Group</label>
		
	    <label><input type="radio" value="user" name="whoto" id="whoto_user" class="checkbox" /> A User</label>
		
		<label><input type="radio" value="guest" name="whoto" id="whoto_guest" class="checkbox" /> A Guest</label>
		
		<div class="group">
			<select id="groups" name="groups">
				<option value="">None</option>
				<?php foreach ($groupslist as $group):?>
					<option value="<?=$group->id;?>"> <?=$group->grouptitle;?></option>
				<?php endforeach;?>
			</select>
		</div>
		
		<div class="user">
			<select id="user" name="user">
				<?php foreach ($userslist as $user):?>
					<option value="<?=$user->id;?>"><?=$user->firstname;?> <?=$user->lastname;?></option>
				<?php endforeach;?>
			</select>
		</div>
		
		<div class="guest">
			<input type="text" name="emailaddress" value="" id="emailaddress" title="Enter guest email address..." /> 
		</div>

		<p>What should they be allowed to do?</p>
		<label><input type="radio" value="read" name="access" id="access_read" checked="checked" class="checkbox" />View and download Lightbox images only</label><br />
		<label><input type="radio" value="full" name="access" id="access_full" class="checkbox" />Add and remove images from the Lightbox</label>
				
		<input type="submit" class="submit button" value="Set Access" />

    </fieldset>
    <?=form_close();?>
</div>
<?php endif;?>

<?php if($currentAccess):?>
<div style="display: none" id="LightboxAccessModal" title="Current Lightbox Access">
    <p>This Light is currently being shared with:</p>
    
	<table>
	
        <thead>
            <th scope="col">User</th>
            <th scope="col">Granted</th>
            <th>Access</th>
            <th class="hide"></th>
        </thead>
        
        <tbody>
        
		<?php foreach($currentAccess as $row => $user):?>
		<tr>
			<td><?php if ($user->usersid):?><?=$user->firstname;?> <?=$user->lastname;?><?php elseif(strlen($user->grouptitle)):?><?=$user->grouptitle;?><?php elseif(strlen($user->emailaddress)):?><span title="<?=$user->emailaddress;?>">Guest</span><?php endif;?></td>
			<td><?=date('d/m/Y', strtotime($user->datecreated));?></td>
			<td><?php if ($user->full):?>Full<?php else:?>Read Only<?php endif;?></td>
			<td class="button">
                <?=form_open($package.'/lightbox/removeAccess/'.$lightbox->id);?>
					<input name="accessid" type="hidden" value="<?=$user->id;?>" />
					<input type="submit" name="submit" value="Remove" class="button" />
                <?=form_close();?>
            </td>
		</tr>
		<?php endforeach;?>
        </tbody>
        
	</table>
	<?php endif;?>
</div>

</div>