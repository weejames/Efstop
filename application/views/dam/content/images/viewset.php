<?=$intro_notification;?>

<ul class="ImageSet clearfix">
     <?php if ($setAccess):?><a style="float: right; margin: 5px 5px 0 0;" href="#AccessControls" title="Set access for this imageset">Jump to Access Controls</a><?php endif;?>
    <h2 class="indent" id="currentSetTitle">Viewing Set &ndash; <span class="title"><?=$settitle;?></span><?php if ($setAccess):?> <a href="#" class="renameset" title="Rename this imageset">Rename</a> <a href="<?=site_url($package.'/image/deleteImageSet/'.$imageset->id);?>" title="Delete this image set" class="deleteLink">Delete</a><?php endif;?></h2>
     <input type="hidden" name="imagesetid" value="<?=$imageset->id;?>" class="imagesetid" />
    
    <?php if ($images):?> 
    <?php foreach($images as $key => $image):?>
    <li>
          <a class="thumb" style="background-image: url('<?=base_url().'image_store/thumbs/'.$image->thumbname;?>')" href="<?=site_url($package.'/image/viewImage/'.$image->id.'/'.$returnTo);?>" title="<?=$image->title."\r\n\r\n".$image->description;?>"><?=$image->title;?></a>
          <?php if($lightboxes):?>
          <?=form_open($package.'/image/addToLightbox/'.$image->id, array('class' => 'lightboxselect', 'id' => 'lightboxselect'.$key ) );?>
          
          <select name="lightboxid">
                <?php foreach($lightboxes as $lightbox):?>
                <option value="<?=$lightbox->id;?>" <?php if($lastlightbox == (int)$lightbox->id):?>selected="selected"<?php endif;?>><?=$lightbox->boxtitle;?></option>
                <?php endforeach;?>
          </select>
          
          <input type="hidden" name="imageid" value="<?=$image->id;?>" class="imageid" />
          <input class="add2Lb button" type="submit" name="submit" value="Add to Lightbox" />
          
          <?=form_close();?>
          <?php endif;?>
    </li>
    <?php endforeach;?>

    <?php else:?>
		<p class="notice">There are no images in this set at the moment.  <?php if ($setAccess):?>Start <a href="<?=site_url('dam_controllers/image/upload');?>">uploading</a> them now.<?php endif;?></p>
	<?php endif;?>


</ul>


<?php if ($setAccess):?>
<div id="AccessControls" class="clearfix">
<h2>Access Controls</h2>
<?=form_open($package.'/image/setSetAccess/'.$imageset->id, array('class' => 'propertiesForm', 'id' => 'accessControl'));?>
<fieldset class="accessControls">
	<legend>Set Access To Imageset</legend>
	<dl>
		<dt><label for="groups">Who do you want to provide access to?</label></dt>
		<dd>
		<label><input type="radio" value="group" name="whoto" id="whoto_group" checked="checked" class="checkbox" /> A Group</label><br />
		<div class="group">
		<select id="groups" name="groups">
			<option value="">None</option>
			<?php foreach ($groupslist as $group):?>
				<option value="<?=$group->id;?>"> <?=$group->grouptitle;?></option>
			<?php endforeach;?>
		</select>
		</div>
		
		<label><input type="radio" value="user" name="whoto" id="whoto_user" class="checkbox" /> A User</label><br />
		<div class="user">
		<select id="user" name="user">
			<option value="">None</option>
			<?php foreach ($userslist as $user):?>
				<option value="<?=$user->id;?>"> <?=$user->firstname;?> <?=$user->lastname;?></option>
			<?php endforeach;?>
		</select></dd>
		</div>
		
		 <fieldset class="accessControls">
                <legend>Access Type</legend>
                    <label><input type="radio" value="read" name="access" id="access_read" checked="checked" class="checkbox" />Read Only</label>
                    <label><input type="radio" value="full" name="access" id="access_full" class="checkbox" />Full</label>
            </fieldset>

		<dt>&nbsp;</dt>
		<dd><input type="submit" class="submit button" value="Set Access" /></dd>
	</dl>
</fieldset>
<?=form_close();?>

<?php if($currentAccess):?>
	<h4>Current Access</h4>
	<p>The following users currently have access to this imageset.</p>
	
	<table>
	
        <thead>
            <th scope="col" class="odd">User</th>
            <th scope="col">Granted</th>
            <th class="odd">Access</th>
            <th class="hide"></th>
        </thead>
        
        <tbody>
        
	<?php foreach($currentAccess as $row => $user):?>
		<tr>
			<td class="odd"><?php if ($user->usersid):?><?=$user->firstname;?> <?=$user->lastname;?><?php elseif(strlen($user->grouptitle)):?><?=$user->grouptitle;?><?php endif;?></td>
			<td><?=date('d/m/Y', strtotime($user->datecreated));?></td>
			<td class="odd"><?php if ($user->full):?>Full<?php else:?>Limited<?php endif;?></td>
			<td class="button">
                <?=form_open($package.'/image/removeSetAccess/'.$imageset->id);?>
					<input name="accessid" type="hidden" value="<?=$user->id;?>" />
					<input type="submit" name="submit" value="Remove" class="button" />
                <?=form_close();?>
            </td>
		</tr>
	<?php endforeach;?>
        </tbody>
        
	</table>

<?php else:?>
	<p>You can let other people get access to this image set by using the set access form.  You can provide access to a group, allowing everyone in that group to see the images within or you can give access to just one person.</p>
<p>Giving someone full access means they too will be able to delete or rename the image set and provide access to other users.  It also means they'll be able to delete any images contained within and edit the details of those images.</p> 
<p>Read Only access will allow someone to view the images in this set and include those images in their lightboxes, but they wont be able to edit it's details or move it to another imageset.</p>

<?php endif;?>


<?php endif;?>
</div>