<?php

include_once('RCCWP_CustomGroup.php');

class RCCWP_CustomGroupPage
{
	function Content($customGroup = null)
	{
	  
	  // add the new expanded column, if it's not there already (Traversal)
		RCCWP_Application::AddColumnIfNotExist(MF_TABLE_PANEL_GROUPS, "expanded", $column_attr = "tinyint after duplicate" );

		global $mf_domain;
		$customGroupName = $customGroupDuplicate = $customGroupExpanded = "";
		if (isset($_GET['custom-write-panel-id']) )
			$customWritePanelId = $_GET['custom-write-panel-id'];
		if (isset($_POST['custom-write-panel-id']) )
			$customWritePanelId = $_POST['custom-write-panel-id'];

		if ($customGroup != null)
		{
			$customGroupName = $customGroup->name;
			$customGroupDuplicate = $customGroup->duplicate;
			$customGroupExpanded = $customGroup->expanded;
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

		<tr>
			<th scope="row" align="right"><?php _e('Show as Expanded', $mf_domain); ?>:</th>
			<td><input name="custom-group-expanded" id="custom-group-expanded" type="checkbox" value="1" <?php echo $customGroupExpanded == 0 ? '': ' checked="checked" ' ?> />&nbsp;<?php _e('Display the full expanded group editing interface instead of the group summary', $mf_domain); ?>
			  <br /><small><?php _e('Note: the group can still be collapsed by the user, this just determines the default state on load')?></td>
		</tr>

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
}
