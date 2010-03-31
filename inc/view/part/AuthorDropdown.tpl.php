
<select name="ea_author" id="ea-author">

<?php 
if ($selected_author == $ea_account_status['account_author'] && $selected_author!=null)
{
	?>
	<option selected="selected" value="<?php echo $ea_account_status['account_author']?>"><?php echo $ea_account_status['account_author']?></option>
	<?php 
}else{
	?>
	<option value="<?php echo $ea_account_status['account_author']?>"><?php echo $ea_account_status['account_author']?></option>
	<?php 
}

if($ea_account_status['alternate']['author'])
{
	foreach($ea_account_status['alternate']['author'] as $ea_alternate_author)
	{
		if ($selected_author == $ea_alternate_author && $selected_author!=null)
		{
			?>
			<option selected="selected" value="<?php echo $ea_alternate_author?>"><?php echo $ea_alternate_author?></option>
			<?php
		}
		else
		{
			?>
			<option value="<?php echo $ea_alternate_author?>"><?php echo $ea_alternate_author?></option>
			<?php
		}
	}
}
?>
</select>