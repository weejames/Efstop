<div class="boxWithPadding" style="position: relative;">

<?php if ($action == 'modify'): ?>
<h2>User Management - Update Details for <?= $user->firstname; ?> <?= $user->lastname; ?> </h2>
<p>Use the form below to update <?= $user->firstname; ?>'s details.</p>
<?php endif;?>

<?php if ($action == 'add'): ?>
<h2>User Management</h2>
<p>Use the form below to create a new user.</p>
<?php endif;?>

<p class="gravatar clearfix">
<img src="<?="http://www.gravatar.com/avatar.php?size=60&default=".urlencode('/assets/default/images/defaultperson.png')."&gravatar_id=".md5(trim($user->emailaddress));?>" width="60" height="60" title="Say Cheese!" /> <br />
Profile pictures are powered by <a href="http://www.gravatar.com">gravatar</a></p>

<p class="clearfix" style="padding-top: 40px;"><strong>Note:</strong> Fields that are marked with <span class="red">*</span> are required!</p>

<?=form_open($package.'/damusers/'.$action.'/'.$user->id, array('class' => 'usersdetailsForm clearfix'));?>

<input type="hidden" name="id" id="id" value="<?= $user->id; ?>" />

<fieldset class="userControl">

	<legend>User Control</legend>
	
		<label for="usertype" class="required"><h3>User Type</h3></label>
		<p>Select what type of user you would like this user to be.</p>
		
		<select name="usertype" id="usertype">
				<option value="limited" <?php if ($user->usertype == 'limited'):?>selected="selected"<?php endif;?>>Limited</option>
				<?php if($this->authentication->isUserType( array('admin', 'super') )):?><option value="admin" <?php if ($user->usertype == 'admin'):?>selected="selected"<?php endif;?>>Admin</option><?php endif;?>
				<?php if($this->authentication->isUserType( 'super' )):?><option value="super" <?php if ($user->usertype == 'super'):?>selected="selected"<?php endif;?>>Super User</option><?php endif;?>
			</select>

		<div class="grouplist">
			
			<?php if ($groups):?>
				<?php $curSet = ''; foreach($groups as $group):?>

				<?php if ($curSet != $group->setname):?>
					<label class="groupset" for="groupset_<?=$group->groupsetsid;?>"><?php if ($selectGroupset):?><input class="checkbox"  type="checkbox" name="groupsetsid[]" value="<?=$group->groupsetsid;?>" id="groupset_<?=$group->groupsetsid;?>" <?php if ($usersets && array_key_exists($group->groupsetsid, $usersets)):?>checked="checked"<?php endif;?> /><?php endif;?><strong><?=$group->setname;?></strong> <a href="#" class="addgroup">Add Group</a> </label>
					<?php endif; $curSet = $group->setname;?>
					
					<label class="group" for="groups_<?=$group->id;?>"><input class="checkbox push20" type="checkbox" name="groupsid[]" value="<?=$group->id;?>" id="groups_<?=$group->id;?>" <?php if ($usergroups && array_key_exists($group->id, $usergroups)):?>checked="checked"<?php endif;?> /><?=$group->grouptitle;?></label>
				<?php endforeach;?>
			<?php else:?>
					<label class="groupset"> No Groups <a href="#" class="addgroup">Add Group</a></label>
			<?php endif;?>
		
		</div>
		<?php if($this->authentication->isUserType( 'super' )):?>
		<div class="addgroupset">
			<label for="setname" class="required"><a href="#" class="addgroupset">Add new Group Set</a></label>
		</div>
		<?php endif;?>
		
		<label for="active" class="required"><h3>Active</h3></label>
		<p>Select whether this user is active or not.  Inactive users will not be able to log in to the site.</p>
		<select name="active" id="active">
				<option value="1" <?php if ($user->active):?>selected="selected"<?php endif;?>>Yes</option>
				<option value="0" <?php if (!$user->active):?>selected="selected"<?php endif;?>>No</option>
        </select>

</fieldset>



<fieldset class="loginInfo">
	<legend>Login Information</legend>
	
		<label for="emailaddress" class="required">Email address <span class="red">*</span>
			<p>You can only have 1 account per email address.  It'll be used to log you in to efstop.</p>
		<input type="text" name="emailaddress" id="emailaddress" value="<?= $user->emailaddress; ?>" class="{required:true,email:true}" />
		</label>
		
		<label for="password" <?php if ($action == 'add'): ?>class="required"<?php endif;?>>Password <?php if ($action == 'add'): ?><span class="red">*</span><?php endif;?>
			<?php if ($action == 'add'):?><p>Create a password that is unique but memorable.  It must be at least 5 characters long.</p><?php endif;?>
			<?php if ($action == 'modify'):?><p>You only need to supply a password if you wish to change the users password.</p><?php endif;?>
		<input type="password" name="password" id="password" class="{<?php if ($action == 'add'): ?>required:true,<?php endif;?>minLength:5}" />
		</label>
		
		<label for="confirm-password" <?php if ($action == 'add'): ?>class="required"<?php endif;?>>Confirm Password
		  <p>Enter the same value you entered for the password.</p>
		</label>

		<input type="password" name="confirm-password" id="confirm-password" class="{<?php if ($action == 'add'): ?>required:true,<?php endif;?>equalTo:'#password',minLength:5}" />
		
		<label for="openid_identifier" >OpenId Identifier
		  <p>Enter the same value you entered for the password.</p>
		</label>

		<input type="text" name="openid_identifier" id="openid_identifier" value="<?= $user->openid_identifier; ?>" />
	
</fieldset>

<fieldset class="personalInfo">
	<legend>Personal Information</legend>
		<label for="firstname" class="required">Firstname <span class="red">*</span></label>
		<input type="text" name="firstname" id="firstname" value="<?= $user->firstname; ?>" class="{required:true}" />
		<label for="lastname" class="required">Surname <span class="red">*</span></label>
		<input type="text" name="lastname" id="lastname" value="<?= $user->lastname; ?>" class="{required:true}" />
</fieldset>

<input class="submit button " type="submit" name="Submit" value="Save" class="savebutton button" />
<?=form_close();?>

</div>
