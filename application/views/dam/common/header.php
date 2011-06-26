<!DOCTYPE html>
<html>
<head>
	<title><?=$page_title;?></title>
	<?=style($page_css);?>
	<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/themes/base/jquery-ui.css" type="text/css" />
	<link rel="shortcut icon" href="<?=base_url();?>/assets/dam/images/favicon.ico" />
	<script type="text/javascript" charset="utf-8">
		sessionID = '<?=$this->db_session->sessionid();?>';
	</script>
</head>
<body>
<?php if(isset($loggedInUser) && strlen($loggedInUser)):?>
<div id="SuperHeaderWrapper">
	<div id="SuperHeader">
		
		<div class="UserOptions">
			<p>Hello, <?=$loggedInUser;?> &middot; <a href="<?=site_url('cerberus/logout');?>">Logout</a></p>
		</div>
		
		<h1><a href="<?=site_url();?>">efStop</a></h1>
		
		<?php if ($showMenu):?>
		<?php if ($modules):?>
		<ul id="TopNav">
		<?php foreach ($modules as $text => $link):?>
			<li class="<?php if ($activesection == $text):?>active<?php endif;?> <?php if ($link == end($modules)):?>last<?php endif;?>"><a href="<?=$link;?>"><?=$text;?></a></li>
		<?php endforeach;?>
		</ul>
		<?php endif;?>
		<?php endif;?>
		
		<?php if ($showSearch):?>
		<?=form_open('dam_controllers/image/search', array('class' => 'Search'));?>
		<label for="searchterms">Image Search</label>
		<input type="text" id="searchterms_ajax" name="searchterms" value="<?=$searchKeyword;?>" title="Keyword, Tag, Name..." />
		<input type="hidden" name="go" value="Go" />
		<?=form_close();?>
		<?php endif; ?>
		
		<div id="MessageArea">
		 <?php if (strlen($flashmessage)):?><p class="notification"><?=$flashmessage;?></p><?php endif;?>
		 <?php if (strlen($flasherror)):?><p class="notification-error"><?=$flasherror;?></p><?php endif;?>
		</div>
	</div>
</div>
<?php endif;?>

<div class="Wrapper">

	<div id="ContentContainer" class="clearfix">