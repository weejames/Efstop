<!DOCTYPE html>
<html>
<head>
	<title><?=$page_title;?></title>
	<?=style($page_css);?>
	<link rel="shortcut icon" href="<?=base_url();?>/assets/dam/images/favicon.ico" />
	<script type="text/javascript" charset="utf-8">
		sessionID = '<?=$this->db_session->sessionid();?>';
	</script>
	<?=script($page_js);?>
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

	<div id="ContentContainer">
		<?php if ($this->authentication->isLoggedIn()): ?>
		
		<ul id="Navotron" class="clearfix">
			<li class="crumb"><a class="root" href="<?=site_url('image')?>"><?=$account->account_name;?>'s Library</a></li>
			<?php $cur_string = ''; foreach($tag_array as $key => $tag): $cur_string .= $tag.'/'; ?>
				<li class="crumb <?php if ( count($tag_array) - 1 == $key ):?>last<?php endif;?>"><a href="<?=site_url('image/'.$cur_string);?>"><?=ucfirst($tag);?></a></li>
			<?php endforeach;?>
			<?php if ($imagetags):?>
			<li><span class="add">Filter by Tag</span>
				<div class="tagpicker" style="display: none;">
					<ul class="alpha clearfix">
						<li><a href="#">All</a></li>
						<li><a href="#">#</a></li>
						<li><a href="#">A</a></li>
						<li><a href="#">B</a></li>
						<li><a href="#">C</a></li>
						<li><a href="#">D</a></li>
						<li><a href="#">E</a></li>
						<li><a href="#">F</a></li>
						<li><a href="#">G</a></li>
						<li><a href="#">H</a></li>
						<li><a href="#">I</a></li>
						<li><a href="#">J</a></li>
						<li><a href="#">K</a></li>
						<li><a href="#">L</a></li>
						<li><a href="#">M</a></li>
						<li><a href="#">N</a></li>
						<li><a href="#">O</a></li>
						<li><a href="#">P</a></li>
						<li><a href="#">Q</a></li>
						<li><a href="#">R</a></li>
						<li><a href="#">S</a></li>
						<li><a href="#">T</a></li>
						<li><a href="#">U</a></li>
						<li><a href="#">V</a></li>
						<li><a href="#">W</a></li>
						<li><a href="#">X</a></li>
						<li><a href="#">Y</a></li>
						<li><a href="#">Z</a></li>
					</ul>
					<ul class="tags clearfix">
					<?php foreach($imagetags as $key => $tag):?>
						<?php if( !in_array( $tag->tag, $tag_array) ): if (strlen($tag_string)) $alt_tag_string = '/'.$tag_string; else $alt_tag_string = ''; ?>
							<li><a href="<?=site_url('image'.$alt_tag_string.'/'.$tag->tag);?>"><?=ucfirst($tag->tag);?></a></li>
						<?php endif;?>
					<?php endforeach;?>
					</ul>
				</div>
			</li>
			<?php endif;?>
		</ul>
		
		<?php if($tag_array):?>
		<p>
			<a href="" class="addall">Add all of these images to the current lightbox</a></p>
			<input type="hidden" id="tag_string" value="<?=$tag_string;?>" />
		<?php endif;?>
		<?php endif;?>