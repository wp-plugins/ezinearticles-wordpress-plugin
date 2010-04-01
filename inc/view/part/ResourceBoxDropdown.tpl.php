
<br/><select name="ea_resourcebox" id="ea-resourcebox">
<?php 
foreach($ea_account_status as $ea_index => $ea_temp_status)
{
	foreach($ea_temp_status as $ea_resource_box_key => $ea_resource_box_value)
	{
		if($ea_resource_box_key == "resource")
		{
			if ($selected_resource_box == $ea_resource_box_value['name'] && $selected_resource_box!=null)
			{
				?><option selected="selected" value="<?php echo $ea_resource_box_value['name']?>" label="<?php echo $selected_text ?>"><?php echo $ea_resource_box_value['name']?></option><?php
			}
			else
			{
				?><option value="<?php echo $ea_resource_box_value['name']?>" label="<?php echo $ea_resource_box_value['body']?>"><?php echo $ea_resource_box_value['name']?></option><?php
			}
		}
	}
}?>
</select>