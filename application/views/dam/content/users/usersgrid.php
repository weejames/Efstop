
<h2>User Management <a href="<?=site_url('dam_controllers/damusers/add');?>">Add New</a></h2>

<?php if($users_list):?>

<div class="halfcolumn">

<table class="tablesorter usersgrid" border="0" cellpadding="2" cellspacing="1" width="100%">
	<tbody>
		<?php $setname = ''; foreach ($users_list as $key => $user):?>
			<?php if ($key == ceil(count($users_list) / 2)):?>
				</tbody>
			</table>
			</div>
			
			<div class="halfcolumn">
			<table class="tablesorter usersgrid" border="0" cellpadding="2" cellspacing="1" width="100%">
			<tbody>
				
			<?php $setName = ''; endif;?>
			
			<?php if ($viewBy == 'set' && $setname != $user->setname):?>
				<tr>
					<th colspan="4"><?=$user->setname;?></th>
				</tr>
			<?php endif; $setname = $user->setname;?>
			
			<tr id="row_<?=$user->id;?>" <?php if ($user->id == $newid):?>class="newrow" <?php endif;?>>
				
				<td <?php if (!$user->active) echo 'class="inactive"';?>><a href="<?=site_url($package.'/damusers/modify/'.$user->id);?>"><img src="<?="http://www.gravatar.com/avatar.php?size=60&default=".urlencode('/assets/default/images/defaultperson.png')."&gravatar_id=".md5(trim($user->emailaddress));?>" width="60" height="60" /></a> <a href="<?=site_url($package.'/damusers/modify/'.$user->id);?>"><?= $user->firstname." ".$user->lastname; ?></a> <?php if (!$user->active):?><em>Inactive</em><?php endif;?>
				<?php if ($user->lastlogin):?><br /><em>last login:</em> <?= date('jS M \'y', strtotime($user->lastlogin)); ?><?php endif;?>
				<br />
				<?php if($user->usertype == 'super') echo 'Super User'; else if ($user->usertype == 'admin') echo 'Administrator'; else echo 'Limited';?>
				</td>
				<td class="actions"><?php if ($user->active):?><a href="<?=site_url($package.'/damusers/deactivate/'.$user->id);?>">Deactivate</a><?php else: ?><a href="<?=site_url($package.'/damusers/activate/'.$user->id);?>">Activate</a><?php endif;?><br /><a href="<?=site_url($package.'/damusers/delete/'.$user->id);?>" class="deleteLink">Delete</a></td>
			</tr>
		<?php endforeach; ?>
	
	</tbody>
</table>

</div>

<?php else:?>

	<p>No users?  Why not <a href="<?=site_url('dam_controllers/damusers/add');?>">add one</a>.</p>

<?php endif;?>