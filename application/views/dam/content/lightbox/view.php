<?php if ($setAccess):?>    
<div id="AccessControls" class="clearfix" name="accesscontrols">
    <a name="accesscontrols"></a>   
    <h2>Access Controls</h2>
    <?=form_open($package.'/lightbox/setAccess/'.$lightbox->id, array('id' => 'accessControl'));?>
    
    <fieldset class="accessControls">
        <legend>Set Access Permissions</legend>
        <dl>
            <dt><p>Who do you want to provide access to?</p></dt>
            <dd><label><input type="radio" value="group" name="whoto" id="whoto_group" checked="checked" class="checkbox" /> A Group</label><br />
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
						<?php foreach ($userslist as $user):?>
							<option value="<?=$user->id;?>"><?=$user->firstname;?> <?=$user->lastname;?></option>
						<?php endforeach;?>
					</select>
                </div>
	            <label><input type="radio" value="guest" name="whoto" id="whoto_guest" class="checkbox" /> A Guest</label>
				<div class="guest">
					<input type="text" name="emailaddress" value="" id="emailaddress" title="Enter guest email address..." /> 
                </div>
            </dd>
            
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
    <p>The following users currently have access to this lightbox.</p>
    
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
<?php endif;?>


<ul class="Lightbox clearfix">
	
    <h2 class="indent" id="currentBoxTitle">Lightbox &ndash; <span class="title"><?=$lightbox->boxtitle;?></span> 
	    <?php if ($setAccess):?>   
	    <a href="#" class="renamebox">Rename</a> <a href="<?=site_url($package.'/lightbox/delete/'.$lightbox->id);?>" class="deleteLink">Delete</a>
		<?php endif;?>
	</h2>

	<input type="hidden" name="lightboxid" id="lightboxid" class="lightboxid" value="<?=$lightbox->id;?>" />
    
    <?php if ($images):?>
    <?php foreach($images as $image):?>
    <li>
        <?php if ($fullImageView):?>
        <?php /*<a href="<?=site_url($package.'/image/viewImage/'.$image->id.'/fromLightbox'.$lightbox->id);?>"><?=$image->title;?></a>*/ ?>
        <a href="<?=site_url($package.'/image/viewImage/'.$image->id.'/fromLightbox'.$lightbox->id);?>" class="thumb" title="<?=$image->title."\r\n\r\n".$image->description;?>"><img src="<?=resizedImageURL('image_store/1500s/'.$image->previewname, 160, 160, true);?>" /></a>
        <?php else:?>
        <span class="ImageCode"><?=$image->imagecode;?></span>
        <a class="thumb" style="background-image: url('<?=base_url().'image_store/thumbs/'.$image->thumbname;?>')"></a>

        <a class="standardLink" href="<?=base_url().'image_store/preview/'.$image->previewname;?>">Download Low Res</a>
        <br /><a class="standardLink" href="<?=base_url().'image_store/'.$image->previewname;?>">Download High Res</a>

    <?php endif;?>
    
    <?php if($modifyLightbox):?>
    
    <?=form_open($package.'/lightbox/removeFromLightbox/'.$lightbox->id);?>
        <input class="delFromLb button" type="submit" name="submit" value="Remove from Lightbox" />
        <input type="hidden" name="lightboxid" value="<?=$lightbox->id;?>" />
        <input type="hidden" name="imageid" value="<?=$image->id;?>" />
    <?=form_close();?>
    <?php endif;?>
    </li>
    <?php endforeach;?>

    <?php else:?>
<p class="notice">There are currentlly no images in this lightbox.</p>
    <?php endif;?>
</ul>

    
