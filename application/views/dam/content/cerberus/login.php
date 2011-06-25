<div id="messagearea">
	<?php if (strlen($flashmessage)):?><p class="notification"><?=$flashmessage;?></p><?php endif;?>
	<?php if (strlen($flasherror)):?><p class="notification-error"><?=$flasherror;?></p><?php endif;?>
</div>

<?=form_open('cerberus/login', array('class' => 'loginform'));?>

<fieldset>
	
		<label class="required">Email Address
		
			<input type="text" id="emailaddress" name="emailaddress" value="<?=$emailaddress;?>" class="{required: true}" />
		</label>
		
		
		<label class="required">Password
		
			<input type="password" id="password" name="password" value="" class="{required:true}" />
			
		</label>
		
		<input type="submit" name="submit" value="Login" class="button" />
</fieldset>

<?=form_close();?>