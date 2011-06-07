<?php
include_once('RCCWP_CustomWritePanel.php');

class RCCWP_CustomWritePanelPage
{
	function Content($customWritePanel = null)
	{
	  // add the new expanded column, if it's not there already (Traversal)
		RCCWP_Application::AddColumnIfNotExist(MF_TABLE_PANELS, "expanded", $column_attr = "tinyint NOT NULL DEFAULT 1 after type" );


		global $mf_domain,$wpdb;
		$customWritePanelName = "";
		$customWritePanelDescription = "";
		$customWritePanelExpanded = 1;
		$write_panel_category_ids = array();
		$defaultTagChecked = 'checked="checked"';
		$customWritePanelAllFieldIds = NULL;
		$customThemePage = NULL;
		$showPost = true;
		$customParentPage = NULL;
		$customWritePanelCategoryIds = NULL;
		if ($customWritePanel != null)
		{
			$customWritePanelName = $customWritePanel->name;
			$customWritePanelDescription = $customWritePanel->description;
		  $customWritePanelExpanded = $customWritePanel->expanded;
			$customWritePanelDisplayOrder = $customWritePanel->display_order;
			$customWritePanelType = $customWritePanel->type;
			if ($customWritePanelType == 'page') $showPost = false;
			$customWritePanelCategoryIds = RCCWP_CustomWritePanel::GetAssignedCategoryIds($customWritePanel->id);
                        foreach($customWritePanelCategoryIds as $key => $c ){
                          if((int)$c != 0){
                            $tc = get_category($c);
                            $customWritePanelCategoryIds[$key] = $tc->slug;
                          }
                        }
			$customWritePanelStandardFieldIds = RCCWP_CustomWritePanel::GetStandardFields($customWritePanel->id);
			$customWritePanelAllFieldIds = RCCWP_CustomWritePanel::Get($customWritePanel->id);
			
			if ($customWritePanelType == 'page'){
				$customThemePage = RCCWP_CustomWritePanel::GetThemePage($customWritePanel->name);
				$customParentPage = RCCWP_CustomWritePanel::GetParentPage($customWritePanel->name);
			}
			$defaultTagChecked = '';

                        
			?>
			<input type="hidden" name="custom-write-panel-id" value="<?php echo $customWritePanel->id?>" />
			<?php
		}
		
  		?>


		<table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="6">
		<tbody>
		<tr valign="top">
			<th scope="row"><?php _e('Placement', $mf_domain); ?></th>
				<td>
				<!-- START :: Javascript for Image/Photo' Css Class -->
				<script type="text/javascript" language="javascript">
					jQuery(document).ready( function() {
							<?php if ($showPost){ ?>
								showHide("mf_forpost", "mf_forpage");
							<?php } else { ?>
								showHide("mf_forpage", "mf_forpost");
							<?php } ?>
						});
						
					function showHide(showClassID, hideClassID)
					{
						jQuery( function($) {
							$("."+showClassID).css("display","");
							$("."+hideClassID).css("display","none");
							});
					}
				</script>
				<!-- END :: Javascript for Image/Photo' Css Class -->
				<input type="radio" name="radPostPage" id="radPostPage" value="post" <?php if(empty($custoWritePanelType) || $customWritePanelType == 'post'){?> checked="checked" <?php } ?> onclick='showHide("mf_forpost", "mf_forpage");' /> <strong><?php _e('Post', $mf_domain); ?> </strong> &nbsp; &nbsp; &nbsp; 
				<input type="radio" name="radPostPage" id="radPostPage" value="page" <?php if(!empty($customWritePanelType)  && $customWritePanelType == 'page'){?> checked="checked" <?php } ?> onclick='showHide("mf_forpage", "mf_forpost");' /> <strong><?php _e('Page', $mf_domain); ?></strong>
			</td>
		</tr>


		<tr valign="top">
			<th scope="row"  align="right"><?php _e('Name', $mf_domain); ?>:</th>
			<td>
				<input name="custom-write-panel-name" id="custom-write-panel-name" size="40" type="text" value="<?php echo $customWritePanelName?>" />
			</td>
		</tr>

	
		<tr valign="top"  id="catText" class="mf_forpost">
			<th scope="row"  align="right"><div id="catLabel" style="display:inline;"><?php _e('Assigned Categories', $mf_domain); ?>:</div></th>
			<td>
				
				<?php
				$cats = get_categories( "get=all" );
				RCCWP_CustomWritePanelPage::PrintNestedCats( $cats, 0, 0, $customWritePanelCategoryIds );
				?>
				
			</td>
		</tr>
		
		<tr valign="top"  id="catText" class="mf_forpage">
			<th scope="row"  align="right"><div id="catLabel" style="display:inline;"><?php _e('Assigned Theme', $mf_domain); ?>:</div></th>
			<td>
				
				<select name="page_template" id="page_template">
					<option value='default'><?php _e('Default Template'); ?></option>
					<?php $themes_defaults = get_page_templates();
					$theme_select=NULL;
					foreach($themes_defaults as $v => $k) {
					$theme_select=NULL;
						if ($customWritePanelType == 'page'){
							if($customThemePage == $k){ $theme_select='SELECTED';}
						}?>
					<option value='<?php echo $k?>' <?php echo $theme_select; ?> ><?php echo $v?></option>
					<?php } ?>
					<?php  ?>
				</select>
		
			</td>
		</tr>
		
		<tr valign="top"  id="catText" class="mf_forpage">
			<th scope="row"  align="right"><div id="catLabel" style="display:inline;"><?php _e('Page Parent', $mf_domain); ?>:</div></th>
			<td>
			<?php 
			wp_dropdown_pages(array('selected' => $customParentPage, 'name' => 'parent_id', 'show_option_none' => __('Main Page (no parent)'), 'sort_column'=> 'menu_order, post_title','option_none_value' => -1)); ?>
			</td>
		</tr>
		
		<tr>
			<th><?php _e('Quantity',$mf_domain);?></th>
			<td>
				<?php 
				if(isset($customWritePanel->id) && !empty($customWritePanel->id))
				{
					if ($customWritePanelAllFieldIds->single == 0)
					{
						$multiple_checked='checked="checked"';
						$single_checked='';
					}else{
						$single_checked='checked="checked"';
						$multiple_checked='';
					}
				}else{
					$multiple_checked='checked="checked"';
					$single_checked='';
				}
				?>
				<input type="radio" name="single" id="radPostPage" value="1" <?php echo $single_checked?>  /> <strong><?php _e('Single', $mf_domain); ?> </strong> &nbsp; &nbsp; &nbsp; 
				<input type="radio" name="single" id="radPostPage" value="0" <?php echo $multiple_checked?>  /> <strong><?php _e('Multiple', $mf_domain); ?></strong>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row" align="right"><?php _e('Standard Fields', $mf_domain); ?>:</th>
			<td>
				<?php 
					global $STANDARD_FIELDS, $wp_version;
					foreach ($STANDARD_FIELDS as $field) :
						if ($field->excludeVersion <= substr($wp_version, 0, 3)) continue;
						if ($field->isAdvancedField) continue;
						
						$checked = "";
						$classes = "";
						if ($customWritePanel != null)
						{
							if (in_array($field->id, $customWritePanelStandardFieldIds))
							{
								$checked = "checked=\"checked\"";
							}
						}
						else
						{
							if ($field->defaultChecked)
							{
								$checked = "checked=\"checked\""; 
							}
						}
						
						if ($field->forPost && !$field->forPage) $classes = $classes . " mf_forpost";
						if ($field->forPage && !$field->forPost) $classes = $classes . " mf_forpage";
				?>
					<div class="<?php echo $classes?>"> 
						<input type="checkbox" name="custom-write-panel-standard-fields[]" value="<?php echo $field->id?>" <?php echo $checked?> /> 
						<?php echo $field->displayName?> 
						<br />
					</div>
				<?php
					endforeach;
				?>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row" align="right"><?php _e('Advanced Fields', $mf_domain); ?>:</th>
			<td>
				<?php 
					global $STANDARD_FIELDS, $wp_version;
					foreach ($STANDARD_FIELDS as $field) :
						if ($field->excludeVersion <= substr($wp_version, 0, 3)) continue;
						if (!$field->isAdvancedField) continue;
						
						$checked = "";
						$classes = "";
						if ($customWritePanel != null)
						{
							if (in_array($field->id, $customWritePanelStandardFieldIds))
							{
								$checked = "checked=\"checked\"";
							}
						}
						else
						{
							if ($field->defaultChecked)
							{
								$checked = "checked=\"checked\""; 
							}
						}
						if ($field->forPost && !$field->forPage) $classes = $classes . " mf_forpost";
						if ($field->forPage && !$field->forPost) $classes = $classes . " mf_forpage";
						
				?>
					<div class="<?php echo $classes?>"> 
						<input type="checkbox" name="custom-write-panel-standard-fields[]" value="<?php echo $field->id?>" <?php echo $checked?> /> 
						<?php echo $field->displayName?> 
						<br />
					</div>
				<?php
					endforeach;
				?>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row" align="right"><?php _e('Order', $mf_domain); ?>:</th>
			<?php 
				if(empty($customWritePanelDisplayOrder)){
					$customWritePanelDisplayOrder = "";
				}
			?>
			<td><input name="custom-write-panel-order" id="custom-write-panel-order" size="2" type="text" value="<?php echo $customWritePanelDisplayOrder?>" /></td>
		</tr>
		
		<tr>
			<th scope="row" align="right"><?php _e('Top Level Fields Expanded', $mf_domain); ?>:</th>
			<td><input name="custom-write-panel-expanded" id="custom-write-panel-expanded" type="checkbox" value="1" <?php echo $customWritePanelExpanded == 0 ? '': ' checked="checked" ' ?> />&nbsp;<?php _e('Display the full expanded group editing interface instead of the summary for fields created at the top level (fields not inside a group)', $mf_domain); ?>
			  <br /><small><?php _e('Note: the group can still be collapsed by the user, this just determines the default state on load')?></td>
		</tr>


		</tbody>
		</table>
		
		<?php
	}
	
	function PrintNestedCats( $cats, $parent = 0, $depth = 0, $customWritePanelCategoryIds ) {
		foreach ($cats as $cat) : 
			if( $cat->parent == $parent ) {
                          $checked = "";
				if (@in_array($cat->slug, $customWritePanelCategoryIds))
				{
					$checked = "checked=\"checked\"";
				}
				echo str_repeat('&nbsp;', $depth * 4);
?>					<input type="checkbox" name="custom-write-panel-categories[]" value="<?php echo $cat->slug?>" <?php echo $checked?> /> <?php echo $cat->cat_name ?> <br/>
<?php				
			RCCWP_CustomWritePanelPage::PrintNestedCats( $cats, $cat->term_id, $depth+1, $customWritePanelCategoryIds );
			}
		endforeach;
	}				

	function Edit()
	{
		global $mf_domain;
		$customWritePanel = RCCWP_CustomWritePanel::Get((int)$_REQUEST['custom-write-panel-id']);
		?>
		<div class="wrap">
		
		<h2><?php _e('Edit', $mf_domain); ?> <?php echo $customWritePanel->name ?> <?php _e('Write Panel', $mf_domain); ?></h2>
		
		<form action="<?php echo RCCWP_ManagementPage::GetCustomWritePanelGenericUrl('submit-edit-custom-write-panel')?>" method="post" id="submit-edit-custom-write-panel">
		
		<?php
		RCCWP_CustomWritePanelPage::Content($customWritePanel);
		?>
		
		<p class="submit" >
			<a  style="color:black" href="<?php echo RCCWP_ManagementPage::GetCustomWritePanelGenericUrl('cancel-edit-custom-write-panel')?>" class="button"><?php _e('Cancel', $mf_domain); ?></a> 
			<input type="submit" id="submit-edit-custom-write-panel" value="<?php _e('Update', $mf_domain); ?>" />
		</p>
		</form>
		
		</div>
		
		<?php
	}
	
	function GetAssignedCategoriesString($customWritePanel)
	{
		$results = RCCWP_CustomWritePanel::GetAssignedCategories($customWritePanel);
		$str = '';
		foreach ($results as $r)
		{
			$str .= $r->cat_name . ', ';	
		}
		$str = substr($str, 0, strlen($str) - 2); // deletes last comma and whitespace
		return $str;
	}
	
	function GetStandardFieldsString($customWritePanel)
	{
		$results = RCCWP_CustomWritePanel::GetStandardFields($customWritePanel);
		foreach ($results as $r)
		{
			$str .= $r->name . ', ';	
		}
		$str = substr($str, 0, strlen($str) - 2); // deletes last comma and whitespace
		return $str;
	}
	
	
	/**
	 * View groups/fields of a write panel
	 *
	 */
	function View()
	{
		global $mf_domain;	

		$customWritePanelId = (int)$_REQUEST['custom-write-panel-id'];

		$customWritePanels = RCCWP_CustomWritePanel::GetCustomWritePanels();
		$customWritePanel = RCCWP_CustomWritePanel::Get($customWritePanelId);
		$custom_groups = RCCWP_CustomWritePanel::GetCustomGroups($customWritePanelId);
		
		
		// get default group id
		foreach ($custom_groups as $group){
			if ($group->name == '__default' && $group->panel_id == $customWritePanelId){ // traversal added extra condition to handle globals
				$customDefaultGroupId = $group->id;
				break;
			}
		}
		
		?>

		<script type="text/javascript" language="javascript">
			function confirmBeforeDelete()
			{
				return confirm("<?php _e('Are you sure you want to delete this custom Field?', $mf_domain); ?>");
			}
			
			function confirmBeforeDeleteGroup()
			{
				return confirm("<?php _e('Are you sure you want to delete this field group?\n\nNote: All fields that are part of this group will also be deleted!', $mf_domain); ?>");
			}
		</script>
		<div class="wrap">

		<form action="<?php echo RCCWP_ManagementPage::GetPanelPage() . "&mf_action=view-custom-write-panel"?>" method="post"  id="posts-filter" name="SelectWritePanel">
			<h2>
				<?php echo $customWritePanel->name?>
				<span style="font-size:small">
					&nbsp; &nbsp;
					<a href="<?php echo RCCWP_ManagementPage::GetCustomWritePanelGenericUrl('edit-custom-write-panel', $customWritePanel->id); ?>" ><?php _e('Edit', $mf_domain); ?></a>|
					<a href="<?php echo RCCWP_ManagementPage::GetCustomWritePanelGenericUrl('delete-custom-write-panel', $customWritePanel->id); ?>" onclick="return confirmBeforeDelete();"><?php _e('Delete', $mf_domain); ?></a>|
					<a href="<?php echo RCCWP_ManagementPage::GetCustomWritePanelGenericUrl('export-custom-write-panel', $customWritePanel->id); ?>" ><?php _e('Export', $mf_domain); ?></a>
				</span>
			</h2>
			<p id="post-search" style="margin-top:6px">
				<strong>
					<?php _e('Choose a Write Panel', $mf_domain)?>
					<select name="custom-write-panel-id" style="margin-top:-2px" onchange="document.SelectWritePanel.submit()">
						<?php
						foreach ($customWritePanels as $panel) :
						?>
							<option <?php echo ($customWritePanelId==$panel->id?' selected ':''); ?> value="<?php echo $panel->id?>"><?php echo $panel->name;?></option>
						<?php
						endforeach;
						?>
					</select>
				</strong>
				<a href="<?php echo RCCWP_ManagementPage::GetCustomWritePanelGenericUrl('create-custom-group')?>" class="button-secondary">+ <?php _e('Create a Group', $mf_domain)?></a>
				<a href="<?php echo RCCWP_ManagementPage::GetCustomWritePanelGenericUrl('create-custom-field')."&custom-group-id=$customDefaultGroupId"?>" class="button-secondary">+ <?php _e('Create a Field', $mf_domain)?></a>
			</p>
		</form>
    <br class="clear"/>
    <?php if(isset($_GET['save_order']) && $_GET['saved_order'] == "true"):?>
      <div id="message" class="updated">
        Saved Order.
      </div>
    <?php endif; ?>

 		<?php
	  foreach ($custom_groups as $group) :
    ?> 
      <?php if($group->name == "__default"):?>
        <h2>Magic Fields</h2>
      <?php else:?> 
       <h2 class="mf-no-default-group"><a href="<?php echo RCCWP_ManagementPage::GetCustomWritePanelGenericUrl('edit-custom-group')."&custom-group-id={$group->id}"?>"><?php echo $group->name?></a></strong>
          <span class="mf_add_group_field">(<a href="<?php echo RCCWP_ManagementPage::GetCustomWritePanelGenericUrl('create-custom-field')."&custom-group-id={$group->id}"?>"><?php _e('create field',$mf_domain); ?></a>)</span>
          <span class="mf_delete_group_field">(<a onclick="return confirmBeforeDeleteGroup();" href="<?php echo RCCWP_ManagementPage::GetCustomWritePanelGenericUrl('delete-custom-group')."&custom-group-id={$group->id}"?>"><?php _e('delete',$mf_domain); ?></a>)</span>

       </h2>
      <?php endif;?>
		<form action="<?php echo RCCWP_ManagementPage::GetCustomWritePanelGenericUrl('save-fields-order')?>" method="post"  id="posts-filter" name="ImportWritePanelForm" enctype="multipart/form-data">
  	<table cellpadding="3" cellspacing="3" width="100%" class="widefat">
  		<thead>
	  		<tr>
          <th width="5%"></th>
	  			<th width="20%" scope="col"><?php _e('Label', $mf_domain)?></th>
	  			<th width="35%" scope="col"><?php _e('Name (Order)', $mf_domain)?></th>
	  			<th width="20%" scope="col"><?php _e('Type', $mf_domain)?></th>
					<th width="20%" scope="col"><?php _e('Actions', $mf_domain)?></th>
				</tr>
  		</thead>
  		<tbody class="sortable">
      <?php
  	  		RCCWP_CustomWritePanelPage::DisplayGroupFields($group->id);
	  	?>
  		</tbody>
  		</table>
	    <?php endforeach;?>
		</div>
		<br />
    <input type="submit" name="save_submit" value="<?php _e('Save Order',$mf_domain);?>" id="save_order" />
    </form>
		<?php
	}
	
	function DisplayGroupFields($customGroupId, $intended = false) {
		global $mf_domain;
		$custom_fields = RCCWP_CustomGroup::GetCustomFields($customGroupId);
		foreach ($custom_fields as $field) :
			if( isset( $field->properties['strict-max-length'] ) && $field->properties['strict-max-length'] == 1 ) {
				if( $field->type == 'Multiline Textbox' ) {
					$maxlength = ' <sup class="help_text strict">[max:'.( $field->properties['width']*$field->properties['height'] ).']</sup>';
				}else {
					$maxlength = ' <sup class="help_text strict">[max:'.$field->properties['size'].']</sup>';
				}
			}else {
				$maxlength = '';
			}
		?>
			<tr>
        <td>
          <a  id="field_<?php echo $field->id; ?>"  class="handler" href="javascript:void();"><img src="<?php echo MF_URI; ?>/images/mf_arrows.png"></a>
          <input type="hidden" name="mf_order[<?php print $customGroupId;?>][]" value="<?php echo $field->id; ?>" />
        </td>
				<td><a href="<?php echo RCCWP_ManagementPage::GetCustomWritePanelGenericUrl('edit-custom-field')."&custom-field-id=$field->id"?> " ><?php if ($intended){ ?><img align="top" src="<?php echo MF_URI; ?>images/arrow_right.gif" alt=""/> <?php } ?><?php echo $field->description . $maxlength?></a><?php if( $field->required_field == 1 ) echo ' <span class="required">*</span>'; ?></td>
		  		<td><tt><?php echo $field->name.' <span style="color: #999;">('.$field->display_order.')</span>';?></tt><?php
				if( $field->type == 'Textbox' && isset( $field->properties['size'] ) ) { echo ' <sup class="help_text">['.$field->properties['size'].']</sup>'; }
				if( $field->type == 'Multiline Textbox' && isset( $field->properties['height'] ) && isset( $field->properties['width'] ) ) { echo ' <sup class="help_text">['.$field->properties['height']. '&times;'. $field->properties['width'] .']</sup>'; };
				?></td>
				<td><?php echo $field->type?><?php
				if( $field->type == 'Multiline Textbox' && isset( $field->properties['hide-visual-editor'] ) && $field->properties['hide-visual-editor'] == 1 ) { echo ' <sup class="help_text">[simple]</sup>'; }
				?></td>
		  	<td><a onclick="return confirmBeforeDelete();" href="<?php echo RCCWP_ManagementPage::GetCustomWritePanelGenericUrl('delete-custom-field')."&custom-field-id=$field->id"?>" >&times; <?php _e('Delete',$mf_domain); ?></a></td>
			</tr>
		<?php
		endforeach;
	}

  function save_order_fields() {
    global $wpdb;
    if(!empty($_POST) && is_numeric($_GET['custom-write-panel-id'])){
      foreach($_POST['mf_order'] as $group_id => $group) {
        foreach($group as $order => $field_id ) {
            if(is_numeric($group_id) && is_numeric($field_id) && is_numeric($order)) {
              $wpdb->update(MF_TABLE_GROUP_FIELDS,array('display_order' => $order),array('id' =>  $field_id),array('%d'),array('%d'));
            }
        }
      }
    }

    wp_safe_redirect(
      add_query_arg(
        'saved_order',
        'true',
        wp_get_referer()
      )
    );
  }
	
	function Import()
	{
		global $mf_domain;	
		include_once('RCCWP_CustomWritePanel.php');
		
		if(isset($_FILES['import-write-panel-file']) && !empty($_FILES['import-write-panel-file']['tmp_name']) ) {
			$filePath = $_FILES['import-write-panel-file']['tmp_name'];
		}else{
			die(__('Error uploading file!', $mf_domain));
		}
		
		if(isset($_REQUEST['overwrite-existing'])) {
			$overwrite = true;
		}

		$writePanelName = basename($_FILES['import-write-panel-file']['name'], ".pnl");
		$panelID = RCCWP_CustomWritePanel::Import($filePath, $writePanelName, $overwrite);
		unlink($filePath);
		
		
		echo "<div class='wrap'><h3>".__("The Write Panel was imported successfuly.",$mf_domain)."</h3>";
		echo '<p><a href="' . RCCWP_ManagementPage::GetCustomWritePanelGenericUrl('view-custom-write-panel', $panelID).'">'.__('Click here',$mf_domain).' </a> '.__('to edit the write panel.',$mf_domain).'</p>';
		echo "</div>";
		
	}
	
	function ViewWritePanels()
	{
		global $mf_domain;	
		$customWritePanels = RCCWP_CustomWritePanel::GetCustomWritePanels(TRUE);
		?>

		<div class="wrap">

		<form action="<?php echo RCCWP_ManagementPage::GetCustomWritePanelGenericUrl('import-write-panel')?>" method="post"  id="posts-filter" name="ImportWritePanelForm" enctype="multipart/form-data">
			<h2><?php _e('Custom Write Panel',$mf_domain); ?></h2>
			<p id="post-search">					
				<input id="import-write-panel-file" name="import-write-panel-file" type="file" />
				<input id="overwrite-existing" name="overwrite-existing" type="checkbox"/> Overwrite existing panel
				<a href="#none" class="button-secondary" style="display:inline" onclick="document.ImportWritePanelForm.submit();"><?php _e('Import a Write Panel',$mf_domain); ?></a>
				<a href="<?php echo RCCWP_ManagementPage::GetCustomWritePanelGenericUrl('create-custom-write-panel'); ?>" class="button-secondary" style="display:inline">+ <?php _e('Create a Write Panel',$mf_domain); ?></a>
			</p>	
		</form>
				
		<br class="clear"/>
		
		<table cellpadding="3" cellspacing="3" width="100%" class="widefat">
			<thead>
				<tr>
					<th scope="col" width="40%"><?php _e('Name (Order)',$mf_domain); ?></th>
					<th width="10%"><?php _e('Id',$mf_domain); ?></th>
					<th width="10%"><?php _e('Type',$mf_domain); ?></th>
					<th width="40%" colspan="4" style="text-align:center"><?php _e('Actions',$mf_domain); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php
				foreach ($customWritePanels as $panel) :
				?>
					<tr>
						<td><?php echo $panel->name;?><?php if ($panel->name != '_Global'): echo ' <span style="color: #999;">('.$panel->display_order.')</span>' ?><?php endif; ?></td>
						<td><?php echo $panel->id ?></td>
						<td><?php echo ucwords( $panel->type ); if( $panel->single != 1 ) echo ' <sup class="multiple" title="Multiple Posts/Pages">[+]</sup>'; ?></td>
						<td><a href="<?php echo RCCWP_ManagementPage::GetCustomWritePanelGenericUrl('view-custom-write-panel', $panel->id)?>" ><?php _e('Edit Fields/Groups',$mf_domain) ?></a></td>
						<td><?php if ($panel->name != '_Global'): ?><a href="<?php echo RCCWP_ManagementPage::GetCustomWritePanelGenericUrl('edit-custom-write-panel', $panel->id)?>" ><?php _e('Edit Write Panel',$mf_domain) ?></a>&nbsp;<?php endif; ?></td>
						<td><?php if ($panel->name != '_Global'): ?><a href="<?php echo RCCWP_ManagementPage::GetCustomWritePanelGenericUrl('edit-custom-write-panel', $panel->id)?>" ><a href="<?php echo RCCWP_ManagementPage::GetCustomWritePanelGenericUrl('export-custom-write-panel', $panel->id); ?>" ><?php _e('Export',$mf_domain); ?></a><?php endif; ?></td>		
					</tr>
				<?php
				endforeach;
				?>
			</tbody>
		</table>
		<br />
		</div>
		<?php 
	}
	
}
?>
