<?php
include_once('RCCWP_CustomGroup.php');

class RCCWP_CustomGroupPage
{
	function Content($customGroup = null)
	{
		global $mf_domain;
		$customGroupName = "";
		if (isset($_GET['custom-write-panel-id']) )
			$customWritePanelId = $_GET['custom-write-panel-id'];
		if (isset($_POST['custom-write-panel-id']) )
			$customWritePanelId = $_POST['custom-write-panel-id'];

		if ($customGroup != null)
		{
			$customGroupName = $customGroup->name;
			$customGroupDuplicate = $customGroup->duplicate;
			$customGroupAtRight = $customGroup->at_right;
		}
		
  		?>
		<?php if($customWritePanelId) { ?>
  			<input type="hidden" name="custom-write-panel-id" value="<?php echo $customWritePanelId?>">
		<?php } ?>

		<table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="6">
		<tbody>

		<tr valign="top">
			<th scope="row"  align="right"><?php _e('Name', $mf_domain); ?>:</th>
			<td><input name="custom-group-name" id="custom-group-name" size="40" type="text" value="<?php echo $customGroupName?>" /></td>
		</tr>

		<tr>
			<th scope="row" align="right"><?php _e('Duplication', $mf_domain); ?>:</th>
			<td><input name="custom-group-duplicate" id="custom-group-duplicate" type="checkbox" value="1" <?php echo $customGroupDuplicate == 0 ? "":"checked" ?> />&nbsp;<?php _e('The group can be duplicated', $mf_domain); ?></td>
		</tr>

		<?php
		if (!isset($customGroup)) :
		?>
		<tr>
			<th scope="row" align="right"><?php _e('Custom Fields', $mf_domain); ?>:</th>
			<td><?php _e('Add custom fields later by editing this custom group.', $mf_domain); ?></td>
		</tr>
		<?php
		endif;
		?>
		</tbody>
		</table>
        <br />
		
		<?php
	}
	
	function Edit()
	{
		global $mf_domain;
		$customGroup = RCCWP_CustomGroup::Get((int)$_REQUEST['custom-group-id']);
		?>
		<div class="wrap">
		
		<h2><?php _e('Edit Group', $mf_domain); ?> - <?php echo $customGroup->name?></h2>
		
		<form action="<?php echo RCCWP_ManagementPage::GetCustomWritePanelGenericUrl('submit-edit-custom-group')."&custom-group-id={$customGroup->id}"?>" method="post" id="edit-custom-group-form">
		
		<?php
		RCCWP_CustomGroupPage::Content($customGroup);
		?>
		
		<p class="submit" >
			<a style="color:black" href="<?php echo RCCWP_ManagementPage::GetCustomWritePanelGenericUrl('cancel-edit-custom-group')?>" class="button"><?php _e('Cancel', $mf_domain); ?></a> 
			<input type="submit" id="submit-edit-custom-group" value="<?php _e('Update', $mf_domain); ?>" />
		</p>
		</form>
		
		</div>
		<br />
		<?php
	}
	
	function GetCustomFields($customGroupId)
	{
		global $wpdb;
		$sql = "SELECT cf.id, cf.name, tt.name AS type, cf.description, cf.display_order, co.options, co.default_option AS default_value, tt.has_options, cp.properties, tt.has_properties, tt.allow_multiple_values FROM " . MF_TABLE_GROUP_FIELDS .
			" cf LEFT JOIN " . MF_TABLE_CUSTOM_FIELD_OPTIONS . " co ON cf.id = co.custom_field_id" .
			" LEFT JOIN " . MF_TABLE_CUSTOM_FIELD_PROPERTIES . " cp ON cf.id = cp.custom_field_id" .
			" JOIN " . MF_TABLE_CUSTOM_FIELD_TYPES . " tt ON cf.type = tt.id" . 
			" WHERE group_id = " . $customGroupId .
			" ORDER BY cf.display_order";
		$results =$wpdb->get_results($sql);
		if (!isset($results))
			$results = array();
		
		for ($i = 0; $i < $wpdb->num_rows; ++$i)
		{
			$results[$i]->options = unserialize($results[$i]->options);
			$results[$i]->properties = unserialize($results[$i]->properties);
			$results[$i]->default_value = unserialize($results[$i]->default_value);
		}
		
		return $results;
	}
}
?>
