<div id="messagearea">
	<?php if (strlen($flashmessage)):?><p class="notification"><?=$flashmessage;?></p><?php endif;?>
	<?php if (strlen($flasherror)):?><p class="notification-error"><?=$flasherror;?></p><?php endif;?>
</div>

<?=form_open('cerberus/login', array('class' => 'loginform'));?>

<fieldset>
	
		<label title="Enter your emailaddress" class="required">Email Address
			<input type="text" id="emailaddress" name="emailaddress" value="<?=$emailaddress;?>" class="{required: true}" />
		</label>
		
		
		<label title="Enter your password" class="required">Password
			<input type="password" id="password" name="password" value="" class="{required:true}" />
			
		</label>	
		
		<p style="padding-top: 1em; margin-bottom: 0.5em;">Or login with OpenId</p>
		
		<label title="Enter your openid identifier" class="required">OpenId Identifier
			<input type="text" id="openid_identifier" name="openid_identifier" value="" class="{required:true}" />
			
		</label>
		
		<input type="submit" name="submit" value="Login" class="button" />
</fieldset>

<?=form_close();?>