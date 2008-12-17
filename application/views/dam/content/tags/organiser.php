<div class="boxWithPadding">
<h2>Organise Tags</h2>

<p>To make it easier to search through your photos, you can groups the tags youve applied to your images into collections.</p>


<?=form_open($package.'/tagorganiser/addCollection', array('class' => 'propertiesForm'));?>

<fieldset id="addCollection">
	<p>If the collection you're looking for isn't listed then add a new one by entering the name of it into the form field below and clicking 'Add Collection'.</p><br/>
	<label for="collection">Collection name</label>
	<input type="text" name="collection" id="collection" />
	<input class="button" type="submit" name="submit" value="Add Collection" /><br/>
</fieldset>

<?=form_close();?>

<div class="halfcolumn">

<table>
	<thead>
		<tr>
			<th>Tag</th>
			<th>Collection</th>
		</tr>
	</thead>
	<tbody>
<?php $current_collection = ''; foreach($tagsAndCollections as $key => $tag): ?>
		<?php if ($key == floor(count($tagsAndCollections) / 2)):?>
				</tbody>
			</table>
			</div>
			<div class="halfcolumn">
			<table>
				<thead>
					<tr>
						<th>Tag</th>
						<th>Collection</th>
					</tr>
				</thead>
				<tbody>
				<?php if ('' != (string)$tag->collection):?>
				<tr>
					<th colspan="2"><?=$tag->collection;?></th>
				</tr>
				<?php endif; ?>
		<?php endif;?>
		
		<?php if ((string)$current_collection != (string)$tag->collection):?>
		<tr>
			<th colspan="2"><?=$tag->collection;?></th>
		</tr>
		<?php endif; ?>
		
		
		
		<?php $current_collection = $tag->collection;?>
		
		
		<tr>
			<td><?=ucfirst($tag->tag);?></td>
			<td>
			
			<?=form_open($package.'/tagorganiser/putInCollection/'.$tag->id);?>
			
				<select name="collectionid" id="collectionid">
					<option value="">-- Select a Collection</option>
					<?php foreach($collections as $collection):?>
					<option value="<?=$collection->id;?>" <?php if ($tag->collection == $collection->collection):?>selected="selected"<?php endif;?>><?=$collection->collection;?></option>
					<?php endforeach;?>
				</select>
				
				<input class="button" type="submit" name="submit" value="go" />
			<?=form_close();?>
			</td>
		</tr>
<?php endforeach;?>
	</tbody>
</table>

</div>

<div style="clear:both;"></div>
</div>