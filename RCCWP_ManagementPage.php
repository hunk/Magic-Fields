<?php

include_once('RCCWP_Application.php');

class RCCWP_ManagementPage
{
	function AssignCustomWritePanel()
	{
		global $mf_domain;
		$postId = (int)$_GET['assign-custom-write-panel'];
		$customWritePanels = RCCWP_CustomWritePanel::GetCustomWritePanels();
		$customWritePanelOptions = RCCWP_Options::Get();
		$message = 'The Post that you\'re about to edit is not associated with any Custom Write Panel.';
		?>
		
		<div id="message" class="updated"><p><?php _e($message); ?></p></div>
		
		<div class="wrap">
		<h2><?php _e('Assign Custom Write Panel'); ?></h2>
		
		<form action="" method="post" id="assign-custom-write-panel-form">
		
		<table class="optiontable">
		<tbody>
		<tr valign="top">
			<th scope="row"><?php _e('Custom Write Panel', $mf_domain); ?>:</th>
			<td>
				<select name="custom-write-panel-id" id="custom-write-panel-id">
					<option value=""><?php _e('(None)', $mf_domain); ?></option>
				<?php
				$defaultCustomWritePanel = $customWritePanelOptions['default-custom-write-panel'];
				foreach ($customWritePanels as $panel) :
					$selected = $panel->id == $defaultCustomWritePanel ? 'selected="selected"' : '';
				?>
					<option value="<?php echo $panel->id?>" <?php echo $selected?>><?php echo $panel->name?></option>
				<?php
				endforeach;
				?>
				</select>
			</td>
		</tr>
		</tbody>
		</table>
		
		<input type="hidden" name="post-id" value="<?php echo $postId?>" />
		<p class="submit" >
			<input name="edit-with-no-custom-write-panel" type="submit" value="<?php _e("Don't Assign Custom Write Panel", $mf_domain); ?>" />
			<input name="edit-with-custom-write-panel" type="submit" value="<?php _e('Edit with Custom Write Panel', $mf_domain); ?>" />
		</p>
		
		</form>
		
		</div>
		
		<?php
	}
	
	function GetCustomFieldEditUrl($customWriteModuleId, $customGroupId, $customFieldId)
	{
		$url = '?page=' . 'Magic_FieldsManageModules' . '&edit-custom-field=' . $customFieldId . '&custom-group-id=' . $customGroupId . '&custom-write-module-id='. $customWriteModuleId ;
		return $url;
	}
	
	function GetCustomFieldDeleteUrl($customGroupId, $customFieldId)
	{
		$url = '?page=' . 'Magic_FieldsManageModules' . '&delete-custom-field=' . $customFieldId . '&custom-group-id=' . $customGroupId;
		return $url;
	}

	function GetModuleDuplicateEditUrl($customWriteModuleId, $duplicateId)
	{
		$url = '?page=' . 'Magic_FieldsManageModules' . '&edit-module-duplicate=' . $duplicateId . '&module-duplicate-id=' . $duplicateId . '&custom-write-module-id='. $customWriteModuleId ;
		return $url;
	}
	
	function GetModuleDuplicateDeleteUrl($customWriteModuleId, $duplicateId)
	{
		$url = '?page=' . 'Magic_FieldsManageModules' . '&delete-module-duplicate=' . $duplicateId . '&module-duplicate-id=' . $duplicateId . '&custom-write-module-id='. $customWriteModuleId ;
		return $url;
	}
	
	function GetCustomWritePanelEditUrl($customWritePanelId)
	{
		$url = '?page=' . urlencode(MF_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'RCCWP_Menu.php') . '&view-custom-write-panel=' . $customWritePanelId . '&custom-write-panel-id=' . $customWritePanelId;
		return $url;
	}
	
	
	
	function GetCustomWritePanelDeleteUrl($customWritePanelId)
	{
		$url = '?page=' . urlencode(MF_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'RCCWP_Menu.php') . '&delete-custom-write-panel=' . $customWritePanelId . '&custom-write-panel-id=' . $customWritePanelId;
		return $url;
	}

	function GetCustomWriteModuleEditUrl($moduleId)
	{
		$url = '?page=' . 'Magic_FieldsManageModules' . '&view-custom-write-module=' . $moduleId . '&custom-write-module-id=' . $moduleId;
		return $url;
	}
	
	function GetCustomWriteModuleDeleteUrl($moduleId)
	{
		$url = '?page=' . 'Magic_FieldsManageModules' . '&delete-custom-write-module=' . $moduleId . '&custom-write-module-id=' . $moduleId;
		return $url;
	}


	function GetCustomGroupEditUrl($groupId, $moduleId)
	{
		$url = '?page=' . urlencode(MF_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'RCCWP_Menu.php') . '&mf_action=view-custom-group&custom-group-id=' . $groupId. '&custom-write-module-id=' . $moduleId;
		return $url;
	}
	
	function GetCustomGroupDeleteUrl($groupId)
	{
		$url = '?page=' . 'Magic_FieldsManageModules' . '&delete-custom-group=' . $groupId . '&custom-group-id=' . $groupId;
		return $url;
	}

	function GetCustomPanelModuleDeleteUrl($customWritePanelId, $panelModuleId)
	{
		$url = '?page=' . urlencode(MF_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'RCCWP_Menu.php') . '&delete-custom-panel-module=' . $panelModuleId . '&custom-write-panel-id=' . $customWritePanelId;
		return $url;
	}
		
	/**
	 * Generates a url containing the write panel id and the action
	 *
	 * @return unknown
	 */
	function GetCustomWritePanelGenericUrl($mfAction, $customWritePanelId = null)
	{
		if (empty($customWritePanelId) && isset($_REQUEST['custom-write-panel-id'])){
			$customWritePanelId = $_REQUEST['custom-write-panel-id'];
		}
			
		if (!empty($customWritePanelId)){
			$url = RCCWP_ManagementPage::GetPanelPage() . "&custom-write-panel-id=$customWritePanelId&mf_action=$mfAction";
		}
		else{
			$url = RCCWP_ManagementPage::GetPanelPage() . "&mf_action=$mfAction";
		}
		
		return $url;
	}
	
	function GetPanelPage(){
		return '?page=MagicFieldsMenu';
	}
}
