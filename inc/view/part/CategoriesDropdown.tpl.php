<select name="ea_category" id="ea-category">

<?php foreach($ea_categories as $ea_category)
{
	if ($selected_category==$ea_category['category']['name'])
	{
		?>
	<option selected="selected" value="<?php echo $ea_category['category']['name']; ?>" style="font-weight:bold;"><?php echo $ea_category['category']['name']; ?></option>
		<?php
	}
	else
	{
		?>
	<option value="<?php echo $ea_category['category']['name']; ?>" style="font-weight:bold;"><?php echo $ea_category['category']['name']; ?></option>
		<?php
	}
	foreach($ea_category['category']['subcategory'] as $ea_subcategory)
	{
		if(!isset($ea_subcategory)) continue;
		if ($selected_category==$ea_subcategory && $selected_category!="")
		{
			?>
			<option selected="selected" value="<?php echo $ea_category['category']['name'].':'.$ea_subcategory; ?>">&rsaquo; <?php echo $ea_subcategory; ?></option>
			<?php
		}
		else if ($ea_subcategory != '')
		{
			?>
			<option value="<?php echo $ea_category['category']['name'].':'.$ea_subcategory; ?>">&rsaquo; <?php echo $ea_subcategory; ?></option>
			<?php			
		}
	}
}?>
</select>