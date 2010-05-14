<?php
/**
 * This class content all  type of fields for the panels
 */
class RCCWP_WritePostPage 
{
	function ApplyWritePanelAssignedCategoriesOrTemplate(){
		global $CUSTOM_WRITE_PANEL,$post,$wp_version;
		
		if(substr($wp_version, 0, 3) < 3.0){
		  $check = "draft";
	  }else{
	    $check = "auto-draft";
	  }
		if($post->post_status == $check){
	
			if($post->post_type == "post"){
				$assignedCategoryIds = RCCWP_CustomWritePanel::GetAssignedCategoryIds($CUSTOM_WRITE_PANEL->id);
				?>
				<script type="text/javascript">
					var mf_categories = new Array(<?php echo '"'.implode('","',$assignedCategoryIds).'"' ?>); 
				</script>
				<?php
				wp_enqueue_script(	'magic_set_categories',
					MF_URI.'js/custom_fields/categories.js',
					array('jquery')
				);
			}else{
				$customParentPage = RCCWP_CustomWritePanel::GetParentPage($CUSTOM_WRITE_PANEL->name);
				$customThemePage = RCCWP_CustomWritePanel::GetThemePage($CUSTOM_WRITE_PANEL->name);
				?>
				<script type="text/javascript">
					var mf_parent = <?php printf("'%s'",$customParentPage); ?>;
					var mf_theme = <?php printf("'%s'",$customThemePage); ?>; 
				</script>
				<?php
				wp_enqueue_script(	'magic_set_categories',
					MF_URI.'js/custom_fields/template.js',
					array('jquery')
				);
			}
		}	
	}

	function FormError(){
		global $mf_domain;
		if (RCCWP_Application::InWritePostPanel()){
			echo "<div id='mf-publish-error-message' class='error' style='display:none;'><p><strong>".__("Post was not published - ",$mf_domain)."</strong> ".__("You have errors in some fields, please check the fields below.",$mf_domain)."</p></div>";	
		}
	}

	function CustomFieldsCSS(){
	?>
	<link 
			rel="stylesheet" 
			href="<?php echo MF_URI;?>css/base.css" 
			type="text/css" media="screen" charset="utf-8"
	/>
	<link
			rel="stylesheet"
			href="<?php echo MF_URI;?>css/datepicker/ui.datepicker.css"
			type="text/css" media="screen" charset="utf-8"
	/>
	<link rel="stylesheet" type="text/css" href="<?php echo MF_URI; ?>js/markitup/skins/markitup/style.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo MF_URI; ?>js/markitup/sets/html/style.css" />
	<?php
	}
		
	function CustomFieldsJavascript(){
	  global $CUSTOM_WRITE_PANEL;
  ?>
	<script type="text/javascript">
		var mf_path = "<?php echo MF_URI ?>" ;
	</script>
	<?php
	
		wp_enqueue_script('jquery-ui-sortable');
		
		//loading  jquery ui datepicker
		wp_enqueue_script(	'datepicker',
							MF_URI.'js/ui.datepicker.js',
							array('jquery','jquery-ui-core')
						);
					
		//loading core of the datepicker
		wp_enqueue_script(	'mf_datepicker',
							MF_URI.'js/custom_fields/datepicker.js'
						);
						
		//loading  jquery ui slider
		wp_enqueue_script(	'slider',
							MF_URI.'js/ui.slider.js',
							array('jquery','jquery-ui-core')
						);
						
		//loading  js for color picker
		wp_enqueue_script(	'sevencolorpicker',
							MF_URI.'js/sevencolorpicker.js'
						);
							
		//loading the code for delete images
		wp_enqueue_script(	'mf_colorpicker',
							MF_URI.'js/custom_fields/colorpicker.js'
						)
						;				
		//loading the code for delete images
		wp_enqueue_script(	'mf_image',
							MF_URI.'js/custom_fields/image.js'
						);
						
		//loading handler for upload files
		wp_enqueue_script( 'mf_upload',
							MF_URI.'js/upload.js'
						);
		
		//loading handler for metadata
		wp_enqueue_script( 'mf_metadata',
							MF_URI.'js/jquery.metadata.js'
						);
		///loading handler for validate
		wp_enqueue_script( 'mf_validate',
							MF_URI.'js/jquery.validate.pack.js'
						);
		//loading the code for validation
		wp_enqueue_script( 'mf_validate_fields',
							MF_URI.'js/custom_fields/validate.js'
						);
		$hide_visual_editor = RCCWP_Options::Get('hide-visual-editor');
		if ($hide_visual_editor == '' || $hide_visual_editor ==  0){
		//loading the code for textarea in validation
		wp_enqueue_script( 'mf_editor_validate',
							MF_URI.'js/custom_fields/editor_validate.js'
						);
		}
		
		//markitup
		wp_enqueue_script('markitup',
  	  MF_URI.'js/markitup/jquery.markitup.pack.js',
  		array('jquery')
  	);
  	
  	wp_enqueue_script('markitup_set_markdown',
  	  MF_URI.'js/markitup/sets/html/set.js',
  	  array('markitup')
    );
    
  	wp_enqueue_script('markitup_setup',
  	  MF_URI.'js/markitup/jquery.markitup.setup.js',
  		array('markitup')
  	);
  	
  	//load script for custom magicfields
  	if (file_exists(MF_UPLOAD_FILES_DIR.DIRECTORY_SEPARATOR."js".DIRECTORY_SEPARATOR."magic_fields.js")) {
  	  wp_enqueue_script('custom_magic_fields',
    	  MF_FILES_URI.'js/magic_fields.js'
    	);
	  }
	  //load script for custom write panel
	  if (file_exists(MF_UPLOAD_FILES_DIR.DIRECTORY_SEPARATOR."js".DIRECTORY_SEPARATOR.$CUSTOM_WRITE_PANEL->capability_name.".js")) {
  	   wp_enqueue_script('custom_wp'.$CUSTOM_WRITE_PANEL->capability_name,
      	  MF_FILES_URI.'js/'.$CUSTOM_WRITE_PANEL->capability_name.'.js'
      	);
	  }
	  
  		
	}	
	
	function ApplyCustomWritePanelHeader() {
		global $CUSTOM_WRITE_PANEL;
		global $mf_domain;
		
		// Validate	 capability
		require_once ('RCCWP_Options.php');
		$assignToRole = RCCWP_Options::Get('assign-to-role');
		$requiredPostsCap = 'edit_posts';
		$requiredPagesCap = 'edit_pages';

		if ($assignToRole == 1){
			$requiredPostsCap = $CUSTOM_WRITE_PANEL->capability_name;
			$requiredPagesCap = $CUSTOM_WRITE_PANEL->capability_name;
		}

		if ($CUSTOM_WRITE_PANEL->type == "post")
			$requiredCap = $requiredPostsCap;
		else
			$requiredCap = $requiredPagesCap;
		
		if (!current_user_can($requiredCap)) wp_die( __('You do not have sufficient permissions to access this custom write panel.',$mf_domain) );

		?>
		
		<script type="text/javascript">
			var mf_path = "<?php echo MF_URI ?>" ;
			var JS_MF_FILES_PATH = '<?php echo MF_FILES_URI ?>';
			var swf_authentication = "<?php if ( function_exists('is_ssl') && is_ssl() ) echo $_COOKIE[SECURE_AUTH_COOKIE]; else echo $_COOKIE[AUTH_COOKIE]; ?>" ;
			var swf_nonce = "<?php echo wp_create_nonce('media-form'); ?>" ;
			var lan_editor = "<?php echo ( '' == get_locale() ) ? 'en' : strtolower( substr(get_locale(), 0, 2) ); ?>";
		</script>
 		<script type="text/javascript" src="<?php echo MF_URI?>js/groups.js"></script>
		
		<script type="text/javascript">
			var JS_MF_FILES_PATH   = '<?php echo MF_FILES_URI ?>';
			var wp_root            = "<?php echo get_bloginfo('wpurl');?>";
			var mf_path            = "<?php echo MF_URI; ?>";
			var mf_relative        = "<?php echo MF_URI_RELATIVE;?>";
			var phpthumb           = "<?php echo PHPTHUMB;?>";
			var swf_authentication = "<?php if ( function_exists('is_ssl') && is_ssl() ) echo $_COOKIE[SECURE_AUTH_COOKIE]; else echo $_COOKIE[AUTH_COOKIE]; ?>" ;
			var swf_nonce          = "<?php echo wp_create_nonce('media-form'); ?>" ;
			var lan_editor = "<?php echo ( '' == get_locale() ) ? 'en' : strtolower( substr(get_locale(), 0, 2) ); ?>";
		</script>

		<?php
		//change title
		global $post,$title;
		if($post->ID == 0){
			$blu = RCCWP_CustomWritePanel::Get($CUSTOM_WRITE_PANEL->id);
			if($post->post_type == "post"){ $name_title = "Post";}
			else{$name_title = "Page";}
			$title="Write ".$name_title." >> " .$blu->name;
		}else{
			$blu = RCCWP_CustomWritePanel::Get($CUSTOM_WRITE_PANEL->id);
			if($post->post_type == "post"){ $name_title = "Post";}
			else{$name_title = "Page";}
			$title="Edit ".$name_title." >> " .$blu->name;
		}

		
		// Show/Hide Panel fields
	 
		global $STANDARD_FIELDS;
		
		$standardFields = RCCWP_CustomWritePanel::GetStandardFields($CUSTOM_WRITE_PANEL->id);
		
		$hideCssIds = array();
		
		foreach($STANDARD_FIELDS as $standardField){
			if (!in_array($standardField->id, $standardFields)){
				foreach($standardField->cssId as $cssID)
					array_push($hideCssIds, $cssID);
			}
		}
		
		if (empty($hideCssIds))
			return;
		
		array_walk($hideCssIds, create_function('&$item1, $key', '$item1 = "#" . $item1;'));
		$hideCssIdString = implode(', ', $hideCssIds);
		?>
		
		<style type="text/css">
			<?php echo $hideCssIdString?> {display: none !important;}
		</style>
		
		<?php
	}
	
	
	
	/**
	 * Drawing our  custom fields
	 */
	function CustomFieldCollectionInterface(){
		global $CUSTOM_WRITE_PANEL,$wpdb,$mf_domain,$post;
		
		if(empty($CUSTOM_WRITE_PANEL)){
			return false;
		}
		
		//getting information of the CustomWrite Panel
		$groups = RCCWP_CustomWritePanel::GetCustomGroups($CUSTOM_WRITE_PANEL->id);

		foreach($groups as $group){
			
			//Only is drawed the group if has at least one field
			$hasfields = RCCWP_CustomGroup::HasCustomfields($group->id);
			if(!$hasfields){
				continue;
			}
			
			if($group->name == "__default"){
				$name = "Magic Fields Custom Fields";
			}else{
				$name = $group->name;
			}	
			
			add_meta_box(
						'panel_'.$group->id,
						$name,
						array('RCCWP_WritePostPage','metaboxContent'),
						$CUSTOM_WRITE_PANEL->type,
						'normal',
						'high',
						$group
						);
		}
		
	}
	function metaboxContent($temp,$group) {
		global $mf_domain;
		global $wpdb;
		global $post;
		global $CUSTOM_WRITE_PANEL;
		
		//we are passing the group_id in the args of the add_meta_box
		$group = $group['args'];
		
			//render the elements
			$customFields = RCCWP_CustomGroup::GetCustomFields($group->id);
			
			//when will be edit the  Post
			if(isset( $_REQUEST['post'] ) && count($customFields) > 0){
				//using the first field name we can know 
				//the order  of the groups
				$firstFieldName = $customFields[0]->name;

				$order = RCCWP_CustomField::GetOrderDuplicates($_REQUEST['post'],$firstFieldName);
				?> 
				<div class="write_panel_wrapper"  id="write_panel_wrap_<?php echo $group->id;?>"><?php
				
				//build the group duplicates 
				foreach($order as $key => $element){
					RCCWP_WritePostPage::GroupDuplicate($group,$element,$key,false);
				}
				//knowing what is the biggest duplicate group
				if(!empty($order)){
					$tmp =  $order;
					sort($tmp);
					$top = $tmp[count($tmp) -1];
				}else{
					$top = 0;
				}
				?>
				<input type='hidden' name='g<?php echo $group->id?>counter' id='g<?php echo $group->id?>counter' value='<?php echo $top ?>' />
				</div>
			<?php
			}else{
			?>
				<div class="write_panel_wrapper" id="write_panel_wrap_<?php echo $group->id;?>">
				<?php
					RCCWP_WritePostPage::GroupDuplicate($group,1,1,false);
					$gc = 1;
				?>
				<input type='hidden' name='g<?php echo $group->id;?>counter' id='g<?php echo $group->id?>counter' value='<?php echo $gc?>' />
				</div>
			<?php 
		   
		   }
	}

	/**
	 * 
	 * @param object $customGroup
	 * @param integer $groupCounter
	 * @param boolean $fromAjax
	 *
	 */ 
	function GroupDuplicate($customGroup, $groupCounter,$order,$fromAjax=true){
		global $mf_domain;
 
		//getting the custom fields
		$customFields = RCCWP_CustomGroup::GetCustomFields($customGroup->id);
		
		//if don't have fields then finish
		if (count($customFields) == 0) return;

		require_once("RC_Format.php");
		if( $customGroup->duplicate != 0 ){ 
			$add_class_rep="mf_duplicate_group";}else{$add_class_rep="";
		}
		?>
		<div class="magicfield_group <?php echo $add_class_rep;?>" id="freshpostdiv_group_<?php 
			echo $customGroup->id.'_'.$groupCounter;?>">
			<div>
			<div class="inside">
				<?php	
					foreach ($customFields as $field) {

						$customFieldName = $field->name;
						$customFieldTitle = attribute_escape($field->description);
						$groupId  = $customGroup->id;
						$inputName = $field->id."_".$groupCounter."_1_".$groupId."_".$customFieldName;
						
						if(isset($_REQUEST['post'])){
							$fc = RCCWP_CustomField::GetFieldDuplicates($_REQUEST['post'],$field->name,$groupCounter);
							$fields_order =  RCCWP_CustomField::GetFieldsOrder($_REQUEST['post'],$field->name,$groupCounter);
							foreach($fields_order as $element){
								RCCWP_WritePostPage::CustomFieldInterface($field->id,$groupCounter,$element,$customGroup->id); 
							}   
						}else{
							RCCWP_WritePostPage::CustomFieldInterface($field->id,$groupCounter,1,$customGroup->id);
							$fc = 1;
						}


						if(!empty($fields_order)){
							$tmp =  $fields_order;
							sort($tmp);
							$top = $tmp[count($tmp) -1];
						}else{
							$top = 1;
						}

					?>
					<span style="display:none" id="<?php echo "c".$inputName."Duplicate"?>">
						<input type="text" name="c<?php echo $inputName ?>Counter" id="c<?php echo $inputName ?>Counter" value='<?php echo $top ?>' /> 
					</span>
				<?php } ?>
			<br />
			<?php
				if( $customGroup->duplicate != 0 ){
			?>
			<div class="mf_toolbox">
				<span class="hndle sortable_mf row_mf">
					<img title="Order" src="<?php echo MF_URI;?>/images/move.png"/>
				</span>
				<span class="mf_counter" id="counter_<?php echo $customGroup->id;?>_<?php echo $groupCounter;?>">
					(<?php echo $order;?>)
				</span>
				<span class="add_mf">
					<?php
						if($groupCounter != 1):?>
							<a class ="delete_duplicate_button" href="javascript:void(0);" id="delete_duplicate-freshpostdiv_group_<?php echo $customGroup->id.'_'.$groupCounter; ?>"> 
								<img class="duplicate_image"  src="<?php echo MF_URI; ?>images/delete.png" alt="<?php _e('Remove field duplicate', $mf_domain); ?>"/><?php _e('Remove Group', $mf_domain); ?>
							</a>
						<?php else:?> 
							<a id="add_duplicate_<?php echo $customGroup->id."Duplicate"."_".$customGroup->id."_".$order;?>" class="duplicate_button" href="javascript:void(0);"> 
								<img class="duplicate_image" src="<?php echo MF_URI; ?>images/duplicate.png" alt="<?php _e('Add group duplicate', $mf_domain); ?>" title="Duplicate Field"/>
							</a>
					   <?php endif;?> 
				</span>
				<br style="height:2px"/>
			</div>
			<?php
				  }
			?>
			</div>
			</div> 
			<input type="hidden" name="order_<?php echo $customGroup->id?>_<?php echo $groupCounter;?>" id="order_<?php echo $customGroup->id?>_<?php echo $groupCounter;?>" value="<?php echo $order?>" />
		</div>
		<?php
	}
	
	/**
	 * @todo Add documentation
	 */
	function CustomFieldInterface($customFieldId, $groupCounter=1, $fieldCounter=1,$customGroup_id=0){
		global $mf_domain;
		require_once("RC_Format.php");
		$customField = RCCWP_CustomField::Get($customFieldId);
		$customFieldName = $customField->name;
		$customFieldTitle = attribute_escape($customField->description);
		$customFieldHelp = htmlentities($customField->help_text,ENT_COMPAT,'UTF-8');
		$groupId = $customGroup_id;
		$inputCustomName = $customFieldId."_".$groupCounter."_".$fieldCounter."_".$groupId."_".$customFieldName; // Create input tag name
		
		$inputName = "magicfields[{$customFieldName}][{$groupCounter}][{$fieldCounter}]";
 		if( $fieldCounter > 1 && $customField->duplicate == 0 ) return ;
 		if( $fieldCounter > 1) $titleCounter = " (<span class='counter_{$customFieldName}_{$groupCounter}'>$fieldCounter</span>)";

 		$field_group = RCCWP_CustomGroup::Get($customField->group_id);

		?>
		<div class="mf-field <?php echo str_replace(" ","_",$customField->type); ?>" id="row_<?php echo $inputCustomName?>">
			<label for="<?php echo $inputCustomName?>">
				<?php
					if(empty($titleCounter)){
						$titleCounter = "";
					}
				?>
				<?php echo $customFieldTitle.$titleCounter?>
				<?php if (!empty($customFieldHelp)) {?>
					<small class="tip">(what's this?)<span class="field_help"><?php echo $customFieldHelp; ?></span></small>
				<?php } ?>
			</label>
			<span>
				<p class="error_msg_txt" id="fieldcellerror_<?php echo $inputCustomName?>" style="display:none"></p>
				<?php		
				switch ($customField->type) {
					case 'Textbox' :
						RCCWP_WritePostPage::TextboxInterface($customField, $inputName, $groupCounter, $fieldCounter);
						break;
					case 'Multiline Textbox' :
						RCCWP_WritePostPage::MultilineTextboxInterface($customField, $inputName, $groupCounter, $fieldCounter);
						break;
					case 'Checkbox' :
						RCCWP_WritePostPage::CheckboxInterface($customField, $inputName, $groupCounter, $fieldCounter);
						break;
					case 'Checkbox List' :
						RCCWP_WritePostPage::CheckboxListInterface($customField, $inputName, $groupCounter, $fieldCounter);
						break;
					case 'Radiobutton List' :
						RCCWP_WritePostPage::RadiobuttonListInterface($customField, $inputName, $groupCounter, $fieldCounter);
						break;
					case 'Dropdown List' :
						RCCWP_WritePostPage::DropdownListInterface($customField, $inputName, $groupCounter, $fieldCounter);
						break;
					case 'Listbox' :
						RCCWP_WritePostPage::ListboxInterface($customField, $inputName, $groupCounter, $fieldCounter);
						break;
					case 'File' :
						RCCWP_WritePostPage::FileInterface($customField, $inputName, $groupCounter, $fieldCounter);
						break;
					case 'Image' :
						RCCWP_WritePostPage::PhotoInterface($customField, $inputName, $groupCounter, $fieldCounter);
						break;
					case 'Date' :
						RCCWP_WritePostPage::DateInterface($customField, $inputName, $groupCounter, $fieldCounter);
						break;
					case 'Audio' :
						RCCWP_WritePostPage::AudioInterface($customField, $inputName, $groupCounter, $fieldCounter);
						break;
					case 'Color Picker' :
						RCCWP_WritePostPage::ColorPickerInterface($customField, $inputName, $groupCounter, $fieldCounter);
						break;
					case 'Slider' :
						RCCWP_WritePostPage::SliderInterface($customField, $inputName, $groupCounter, $fieldCounter);
						break;
					case 'Related Type' :
						RCCWP_WritePostPage::RelatedTypeInterface($customField, $inputName, $groupCounter, $fieldCounter);
						break;
				  case 'Markdown Textbox' :
  					RCCWP_WritePostPage::MarkdownTextboxInterface($customField, $inputName, $groupCounter, $fieldCounter);
  					break;
					default:
						;
				}
				if($fieldCounter == 1) {
					?>
					<?php if($customField->duplicate != 0 ){ ?>
					<br />
					
					 <a class ="typeHandler" href="javascript:void(0);" id="type_handler-<?php echo $inputCustomName ?>" > 
						<img class="duplicate_image"  src="<?php echo MF_URI; ?>images/duplicate.png" alt="<?php _e('Add field duplicate', $mf_domain); ?>"/>  <?php _e('Duplicate', $mf_domain); ?>
					</a>
					<?php } ?>
					<?php
				}
				else
				{
				?>
					<br />
					<a class ="delete_duplicate_field" href="javascript:void(0)" id="delete_field_repeat-<?php echo $inputCustomName?>"> 
						<img class="duplicate_image"  src="<?php echo MF_URI; ?>images/delete.png" alt="<?php _e('Remove field duplicate', $mf_domain); ?> "/> <?php _e('Remove', $mf_domain); ?> 
					</a>
				<?php
				}
				?>
		</span>
		</div>
	<?php
	}
	
	function CheckboxInterface($customField, $inputName, $groupCounter, $fieldCounter) {
		$customFieldId = '';
		$idField = RCCWP_WritePostPage::changeNameInput($inputName);
		
		if (isset($_REQUEST['post']))
		{
			$customFieldId = $customField->id;
			$value = RCCWP_CustomField::GetCustomFieldValues(true, $_REQUEST['post'], $customField->name, $groupCounter, $fieldCounter);
			$checked = $value == 'true' ? 'checked="checked"' : '';
		}else{
			$checked = "";
		}
		?>
		<div class="mf_custom_field">
		<input  type="hidden" name="<?php echo $inputName?>_1" value="false" />
		<input tabindex="3" class="checkbox checkbox_mf" <?php if ($customField->required_field) echo 'validate="required:true"'; ?> name="<?php echo $inputName?>" value="true" id="<?php echo $idField;?>" type="checkbox" <?php echo $checked?> /></div>
		<?php if ($customField->required_field){ ?>
		<div class="mf_message_error"><label for="<?php echo $inputName?>" class="error_magicfields error block">This field is required.</label></div>
		<?php }
	}
	
	function CheckboxListInterface($customField, $inputName, $groupCounter, $fieldCounter) {
		$customFieldId = '';
		
		$idField = RCCWP_WritePostPage::changeNameInput($inputName);
		
		$values = array();
		if (isset($_REQUEST['post'])) {
			$customFieldId = $customField->id;
			$values = (array) RCCWP_CustomField::GetCustomFieldValues(false, $_REQUEST['post'], $customField->name, $groupCounter, $fieldCounter);
		}else{
			$values = $customField->default_value;
		}
		
		?>
		
		<div class="mf_custom_field">
		<?php
		foreach ($customField->options as $option) :
			$checked = in_array($option, (array)$values) ? 'checked="checked"' : '';
			$option = attribute_escape(trim($option));
		?>
		<label for="<?php echo $inputName;?>" class="selectit mf-checkbox-list">
			<input tabindex="3" <?php if ($customField->required_field) echo 'validate="required:true"'; ?> class="checkbox_list_mf" id="<?php echo $option?>" name="<?php echo $inputName?>[]" value="<?php echo $option?>" type="checkbox" <?php echo $checked?> />
			
				<?php echo attribute_escape($option)?>
			</label><br />
		
		<?php
		endforeach;
		?></div>
		<?php if ($customField->required_field){ ?>
			<div class="mf_message_error"><label for="<?php echo $inputName?>[]" class="error_magicfields error">This field is required.</label></div>
		<?php } ?>
		<?php
	}
	
	function DropdownListInterface($customField, $inputName, $groupCounter, $fieldCounter)
	{
		global $mf_domain;
		$customFieldId = '';
		if (isset($_REQUEST['post']))
		{
			$customFieldId = $customField->id;
			$value = attribute_escape(RCCWP_CustomField::GetCustomFieldValues(true, $_REQUEST['post'], $customField->name, $groupCounter, $fieldCounter));
		}
		else
		{
			$value = $customField->default_value[0];
		}
		
		$requiredClass = "";
		if ($customField->required_field) $requiredClass = "field_required";
		?>
		<div class="mf_custom_field">
		<select tabindex="3" <?php if ($customField->required_field) echo 'validate="required:true"'; ?> class="<?php echo $requiredClass;?> listbox_mf" name="<?php echo $inputName?>">
			<option value=""><?php _e('--Select--', $mf_domain); ?></option>
		
		<?php
		foreach ($customField->options as $option) :
			$selected = $option == $value ? 'selected="selected"' : '';
			$option = attribute_escape(trim($option));
		?>
			<option value="<?php echo $option?>" <?php echo $selected?>><?php echo $option?></option>
		<?php
		endforeach;
		?>
		
		</select>	</div>
		<?php if ($customField->required_field){ ?>
			<div class="mf_message_error"><label for="<?php echo $inputName?>" class="error_magicfields error">This field is required.</label></div>
		<?php }
	}
	
	//eeble
	function RelatedTypeInterface($customField, $inputName, $groupCounter, $fieldCounter)
	{
		global $mf_domain;
		$customFieldId = '';
		if (isset($_REQUEST['post']))
		{
			$customFieldId = $customField->id;
			$value = attribute_escape(RCCWP_CustomField::GetCustomFieldValues(true, $_REQUEST['post'], $customField->name, $groupCounter, $fieldCounter));
		}
		else
		{
			$value = $customField->default_value[0];
		}
		
		//get id of related type / panel
		$panel_id = (int)$customField->properties['panel_id'];
		
		$requiredClass = "";
		if ($customField->required_field) $requiredClass = "field_required";
		?>
		<div class="mf_custom_field">
		<select tabindex="3" <?php if ($customField->required_field) echo 'validate="required:true"'; ?> class="<?php echo $requiredClass;?> listbox_mf" name="<?php echo $inputName?>">
			<option value=""><?php _e('--Select--', $mf_domain); ?></option>
		
		<?php
		if($panel_id == -4){
			$options=get_posts("post_type=post&numberposts=-1&order=ASC&orderby=title");
		}elseif($panel_id == -3){
			$options=get_posts("post_type=page&numberposts=-1&order=ASC&orderby=title");
		}elseif($panel_id == -2){
				$options=get_posts("post_type=post&meta_key=_mf_write_panel_id&numberposts=-1&order=ASC&orderby=title");
		}elseif($panel_id == -1){
					$options=get_posts("post_type=page&meta_key=_mf_write_panel_id&numberposts=-1&order=ASC&orderby=title");
		}else{
			$options=get_posts("post_type=any&meta_key=_mf_write_panel_id&numberposts=-1&meta_value=$panel_id&order=ASC&orderby=title");
		}
		
		foreach ($options as $option) :
			$selected = $option->ID == $value ? 'selected="selected"' : '';
		?>
			<option value="<?php echo $option->ID ?>" <?php echo $selected?>><?php echo $option->post_title ?></option>
		<?php
		endforeach;
		?>
		
		</select></div>
		<?php if ($customField->required_field){ ?>
			<div class="mf_message_error"><label for="<?php echo $inputName?>" class="error_magicfields error">This field is required.</label></div>
		<?php } ?>
		
		<?php
	}
	
	function ListboxInterface($customField, $inputName, $groupCounter, $fieldCounter) {
		$idField = RCCWP_WritePostPage::changeNameInput($inputName);
		$customFieldId = '';
		if (isset($_REQUEST['post'])){
			$customFieldId = $customField->id;
			$values = (array) RCCWP_CustomField::GetCustomFieldValues(false, $_REQUEST['post'], $customField->name, $groupCounter, $fieldCounter);
			
		}else{
			$values = $customField->default_value;
		}
		
		$inputSize = (int)$customField->properties['size'];
		$requiredClass = "mf_listbox";
		if ($customField->required_field) $requiredClass = "mf_listbox field_required";
		?>
		<div class="mf_custom_field">
		<select <?php if ($customField->required_field) echo 'validate="required:true"'; ?> class="<?php echo $requiredClass;?> listbox_mf"  tabindex="3" id="<?php echo $idField;?>" name="<?php echo $inputName?>[]" multiple size="<?php echo $inputSize?>" style="height: auto;">
		
		<?php
		foreach ($customField->options as $option) {
			if(!empty($option)){
				$selected = in_array($option, (array)$values) ? 'selected="selected"' : '';
				$option = attribute_escape(trim($option));
		?>
			<option value="<?php echo $option?>" <?php echo $selected?>><?php echo $option?></option>	
		<?php
			}
		}
		?>
		</select></div>
			<?php if ($customField->required_field){ ?>
				<div class="mf_message_error"><label for="<?php echo $inputName?>" class="error_magicfields error">This field is required.</label></div>
			<?php } ?>
		
		<?php
	}
	
	function MultilineTextboxInterface($customField, $inputName, $groupCounter, $fieldCounter){
		$customFieldId = '';
		
		$idField = RCCWP_WritePostPage::changeNameInput($inputName);
		
		if( isset($_REQUEST['post']) ){
			$customFieldId = $customField->id;
			$value = RCCWP_CustomField::GetCustomFieldValues(true, $_REQUEST['post'], $customField->name, $groupCounter, $fieldCounter);
			if(!(int)$customField->properties['hide-visual-editor']){
				$value = apply_filters('the_editor_content', $value);
			}
		}else{
			$value = "";
		}
		
		$inputHeight = (int)$customField->properties['height'];
		$inputWidth = (int)$customField->properties['width'];
		$hideEditor = (int)$customField->properties['hide-visual-editor'];
		
		$requiredClass = "";
		if ($customField->required_field) $requiredClass = "field_required";
		
		$pre_text='';
		
		$hide_visual_editor = RCCWP_Options::Get('hide-visual-editor');
		if ($hide_visual_editor == '' || $hide_visual_editor == 0 ){
			if(!$hideEditor){
			$pre_text="pre_editor"; ?>
		<div class="mf_custom_field">
		<div class="tab_multi_mf">
			<a onclick="del_editor('<?php echo $idField; ?>');" class="edButtonHTML_mf">HTML</a>		
			<a onclick="add_editor('<?php echo $idField; ?>');" class="edButtonHTML_mf" >Visual</a>
		</div>
		<?php } } 
		$classEditor = 'mf_editor';
		if($hideEditor){
			$classEditor = '';
			$pre_text='';
		} ?>
		<div class="mul_mf">
		<textarea  <?php if ($customField->required_field) echo 'validate="required:true"'; ?> class="<?php echo $requiredClass;?> <?php echo $classEditor; ?> <?php echo $pre_text ?>" tabindex="3"  id="<?php echo $idField; ?>" name="<?php echo $inputName?>" rows="<?php echo $inputHeight?>" cols="<?php echo $inputWidth?>"><?php echo $value?></textarea>
		</div></div>
		<?php if ($customField->required_field){ ?>
			<div class="mf_message_error"><label for="<?php echo $idField; ?>" class="error_magicfields error">This field is required.</label></div>
		<?php } ?>
		
	<?php
	}
	
	function TextboxInterface($customField, $inputName, $groupCounter, $fieldCounter){
		$customFieldId = '';
		
		$idField = RCCWP_WritePostPage::changeNameInput($inputName);
		
		if (isset($_REQUEST['post'])) {
			$customFieldId = $customField->id;
			$value = attribute_escape(RCCWP_CustomField::GetCustomFieldValues(true, $_REQUEST['post'], $customField->name, $groupCounter, $fieldCounter));
		}else{
			$value = "";
		}

    $requiredClass= '';
		$inputSize = (int)$customField->properties['size'];
		if ($customField->required_field) $requiredClass = "field_required";
		
		// If the field is at right, set a constant width to the text box
		$field_group = RCCWP_CustomGroup::Get($customField->group_id);
		if ($field_group->at_right){
			if ($inputSize>14) $inputSize = 14;
		}
		?>
		<div class="mf_custom_field">
		<input <?php if ($customField->required_field) echo 'validate="required:true"'; ?> class="<?php echo $requiredClass;?> textboxinterface" tabindex="3" id="<?php echo $idField ?>" name="<?php echo $inputName?>" value="<?php echo $value?>" type="text" size="<?php echo $inputSize?>" />
		</div>
			<?php if ($customField->required_field){ ?>
				<div class="mf_message_error"><label for="<?php echo $inputName?>" class="error_magicfields error">This field is required.</label></div>
			<?php } ?>
		<?php
	}
	


	/**
	 * File Field
	 *
	 */
	function FileInterface($customField, $inputName, $groupCounter, $fieldCounter) {
		global $mf_domain;
		
		$idField = RCCWP_WritePostPage::changeNameInput($inputName);
		
		$customFieldId = '';
		$freshPageFolderName = (dirname(plugin_basename(__FILE__)));
		$requiredClass = "";
		if ($customField->required_field) $requiredClass = "field_required";

		if (isset($_REQUEST['post'])) {
			$customFieldId = $customField->id;
			$value = attribute_escape(RCCWP_CustomField::GetCustomFieldValues(true, $_REQUEST['post'], $customField->name, $groupCounter, $fieldCounter));
			$path = MF_FILES_URI;
			$valueRelative = $value;
			$value = $path.$value;
		}else{
			$valueRelative = '';
		}
		
		// If the field is at right, set a constant width to the text box
		$field_group = RCCWP_CustomGroup::Get($customField->group_id);
		$urlInputSize = false;
		$is_canvas = 0;
		if ($field_group->at_right){
			$urlInputSize = 5;
			$is_canvas = 1;
		}

		?>
		
		<p class="error_msg_txt" id="upload_progress_<?php echo $idField;?>" style="visibility:hidden;height:0px"></p>
		<script type="text/javascript"> 
			//this script is for remove the  file  related  to the post (using ajax)
			remove_file = function(){
				if(confirm("Are you sure?")){
					//get  the name to the file
					id = jQuery(this).attr("id").split("-")[1];
					file = jQuery('#'+id).val();
					jQuery.get('<?php echo MF_URI;?>RCCWP_removeFiles.php',{'action':'delete','file':file},
								function(message){
									jQuery('#actions-'+id).empty();
									jQuery('#remove-'+id).empty();
									jQuery('#'+id).val("");
								});

				}
			}


			jQuery(document).ready(function(){
				jQuery("#remove-<?php echo $idField;?>").click(remove_file);

			});
		</script>
		
		<?php if( $valueRelative ){ 
				echo "<span id='actions-{$idField}'>(<a href='{$value}' target='_blank'>".__("View Current",$mf_domain)."</a>)</span>"; 
				echo "&nbsp;<a href='javascript:void(0);' id='remove-{$idField}'>".__("Delete",$mf_domain)."</a>";
			} 
		?>
		<div class="mf_custom_field">	
		<input tabindex="3" 
			id="<?php echo $idField?>" 
			name="<?php echo $inputName?>" 
			type="hidden"
			class="<?php echo $requiredClass;?>" 
			size="46"
			value="<?php echo $valueRelative?>"
			<?php if ($customField->required_field) echo 'validate="required:true"'; ?>
			/>
		
		<?php
		include_once( "RCCWP_SWFUpload.php" ) ;
		RCCWP_SWFUpload::Body($inputName, 0, $is_canvas, $urlInputSize) ;?>
		</div>
		<?php if ($customField->required_field){ ?>
			<div class="mf_message_error"><label for="<?php echo $inputName?>" class="error_magicfields error">This field is required.</label></div>
		<?php }
	}

	function PhotoInterface($customField, $inputName, $groupCounter, $fieldCounter) {
		global $mf_domain;
		
		$idField = RCCWP_WritePostPage::changeNameInput($inputName);
		
		if(!empty($_GET['post'])){
			$hidValue = RCCWP_CustomField::GetCustomFieldValues(true,$_GET['post'], $customField->name, $groupCounter, $fieldCounter);
		}else{
			$hidValue = '';
		}
		
		$filepath	= $inputName . '_filepath';
		//The Image is required?
		$requiredClass = "";
		if ($customField->required_field) $requiredClass = "field_required";
		
		$imageThumbID = "img_thumb_".$idField; 
		$value = "<img src='".MF_URI."images/noimage.jpg' id='{$imageThumbID}'/>";

		if( !empty($hidValue)){
			$path = PHPTHUMB."?src=".MF_FILES_URI;
			$valueRelative = $hidValue;
			$value  = $path.$hidValue."&w=150&h=120&zc=1";
			$value  = "<img src='{$value}' id='{$imageThumbID}'/>";
		}
?>
		<p 	class="error_msg_txt" id="upload_progress_<?php echo $idField;?>" style="visibility:hidden;height:0px">
		</p>	
		<div id="image_photo" style="width:150px; float: left">
			<?php echo $value;?>
		<div id="photo_edit_link_<?php echo $idField ?>" class="photo_edit_link"> 
			<?php
				if(isset($_REQUEST['post'])){	
					echo "&nbsp;<strong><a href='#remove' class='remove' id='remove-{$idField}'>".__("Delete",$mf_domain)."</a></strong>";
				}
			?>
		</div>
		</div>
		<div id="image_input" style="padding-left: 170px;">
	<?php
	if(empty($requiredClass)){
		$requiredClass ='';
	}
	?>		
			<div class="mf_custom_field">
			<input tabindex="3" 
				id="<?php echo $idField?>" 
				name="<?php echo $inputName;?>" 
				type="hidden" 
				class="<?php echo $requiredClass;?>"
				size="46"
				value="<?php echo $hidValue?>"
				<?php if ($customField->required_field) echo 'validate="required:true"'; ?>
				/>
			
			<?php
			include_once( "RCCWP_SWFUpload.php" ) ;
			RCCWP_SWFUpload::Body($inputName, 1, 0,false);
			?>
			</div>
		</div>
		
		<div style="clear: both; height: 1px;"> </div>
			<?php if ($customField->required_field){ ?>
				<div class="mf_message_error"><label for="<?php echo $inputName?>" class="error_magicfields error">This field is required.</label></div>
			<?php
			} ?>

		<?php
	}
	
	function RadiobuttonListInterface($customField, $inputName, $groupCounter, $fieldCounter){
		$customFieldId = '';
		
		if (isset($_REQUEST['post']))
		{
			$value = attribute_escape(RCCWP_CustomField::GetCustomFieldValues(true, $_REQUEST['post'], $customField->name, $groupCounter, $fieldCounter));
		}
		else
		{
			$value = $customField->default_value[0];
		}
		?>
		<div class="mf_custom_field">
		<?php
		foreach ($customField->options as $option) :
			$checked = $option == $value ? 'checked="checked"' : '';
			$option = attribute_escape(trim($option));
		?>
			<label for="<?php echo $inputName;?>" class="selectit">
				<input tabindex="3" <?php if ($customField->required_field) echo 'validate="required:true"'; ?> id="<?php echo $option?>" name="<?php echo $inputName?>" value="<?php echo $option?>" type="radio" <?php echo $checked?>/>
				<?php echo $option?>
			</label>
		<?php
		endforeach; ?>
		</div>
		<?php if ($customField->required_field){ ?>
		<div class="mf_message_error"><label for="<?php echo $inputName?>" class="error_magicfields error">This field is required</label></div>
		<?php
		}
	}

	function DateInterface($customField, $inputName, $groupCounter, $fieldCounter) {
		global $wpdb;
		$customFieldId = '';
		
		$idField = RCCWP_WritePostPage::changeNameInput($inputName);
		
		if (isset($_REQUEST['post'])) {
			$customFieldId = $customField->id;
			$value = attribute_escape(RCCWP_CustomField::GetCustomFieldValues(true, $_REQUEST['post'], $customField->name, $groupCounter, $fieldCounter));
			
			if(!empty($value)){
				$value = date($customField->properties['format'],strtotime($value));
			}else{	
				$value =  "";//date($customField->properties['format']);
			}		
		} else { 
			$value =  "";//date($customField->properties['format']);
		}
		
		$dateFormat = $customField->properties['format'];
		$today = date($dateFormat);
		
		$field_group = RCCWP_CustomGroup::Get($customField->group_id);
		$inputSize = 25;
		if ($field_group->at_right){
			$inputSize = 15;
		}
		?>
		<div id="format_date_field_<?php echo $idField; ?>" style="display:none"><?php echo $dateFormat;?></div>
			
		<input 	id="display_date_field_<?php echo $idField; ?>"
		 		value="<?php echo $value?>" 
				type="text" 
				size="<?php echo $inputSize?>" 
				class="datepicker_mf"   
		READONLY/>
		
		<input 	id="date_field_<?php echo $idField; ?>" 
				name="<?php echo $inputName?>" 
				value="<?php echo $value?>" type="hidden" 
		/>
		<input 	type="button" 
				value="Pick..." 
				id="pick_<?php echo $idField; ?>" 
				class="datebotton_mf"
		/>
		<input 	type="button" 
				id="today_<?php echo $idField; ?>"
				value="Today" 
				class="todaybotton_mf"
		/>
		<input 	type="button" 
				id="blank_<?php echo $idField; ?>"
				value="Blank" 
				class="blankBotton_mf"
		/>
		<input 	type="hidden"
				value="<?php echo $today;?>"
				id="tt_<?php echo $idField; ?>"
				class="todaydatebutton_mf"
		/>
		<input 
				type="hidden"
				name="rc_cwp_meta_date[]" 
				value="<?php echo $idField; ?>" 	
		/>
		<?php
	}


	/**
	 * Audio  field
	 */
	function AudioInterface($customField, $inputName, $groupCounter, $fieldCounter){
		global $mf_domain;
		
		$idField = RCCWP_WritePostPage::changeNameInput($inputName);
		$customFieldId = '';
		$freshPageFolderName = (dirname(plugin_basename(__FILE__))); 
		$requiredClass = "";
		if ($customField->required_field) $requiredClass = "field_required";
		
		if (isset($_REQUEST['post'])) {
			$customFieldId = $customField->id;
			$valueOriginal = RCCWP_CustomField::GetCustomFieldValues(true, $_REQUEST['post'], $customField->name, $groupCounter, $fieldCounter);
			$path = MF_FILES_URI;
			if(empty($valueOriginal)){
				$valueOriginal = '';
			}
			
			if(empty($valueOriginalRelative)){
				$valueOriginalRelative = '';
			}
			
			$$valueOriginalRelative = $valueOriginal;
			$valueOriginal = $path.$valueOriginal;
			if (!empty($valueOriginal))
				$value = stripslashes(trim("\<div  id='obj-{$idField}' style=\'width:260px;padding-top:3px;\'\>\<object classid=\'clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\' codebase='\http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0\' width=\'95%\' height=\'20\' wmode=\'transparent\' \>\<param name=\'movie\' value=\'".MF_URI."js/singlemp3player.swf?file=".urlencode($valueOriginal)."\' wmode=\'transparent\' /\>\<param name=\'quality\' value=\'high\' wmode=\'transparent\' /\>\<embed src=\'".MF_URI."js/singlemp3player.swf?file=".urlencode($valueOriginal)."' width=\'100\%\' height=\'20\' quality=\'high\' pluginspage=\'http://www.macromedia.com/go/getflashplayer\' type=\'application/x-shockwave-flash\' wmode=\'transparent\' \>\</embed\>\</object\>\</div\><br />"));			
		}
		
		// If the field is at right, set a constant width to the text box
		$field_group = RCCWP_CustomGroup::Get($customField->group_id);
		$urlInputSize = false;
		$is_canvas = 0;
		if ($field_group->at_right){
			$urlInputSize = 5;
			$is_canvas = 1;
		}

		?>
		<p class="error_msg_txt" id="upload_progress_<?php echo $idField;?>" style="visibility:hidden;height:0px"></p>
		<script type="text/javascript">
			//this script is for remove the audio file using ajax
			remove_audio = function(){
				if(confirm("<?php _e('Are you sure?', $mf_domain); ?>")){
					//get the name to the image
					id = jQuery(this).attr('id').split("-")[1];
					file = jQuery('#'+id).val(); 
					jQuery.get('<?php echo MF_URI;?>RCCWP_removeFiles.php',{'action':'delete','file':file},
								function(message){
									//if(message =="true"){
										jQuery('#obj-'+id).empty();
										jQuery('#actions-'+id).empty();
										jQuery('#'+id).val("");
									//}
								});
				}						   
			}

			jQuery(document).ready(function(){
				jQuery("#remove-<?php echo $idField;?>").click(remove_audio);
			});
		</script>
		<?php 
		if( !empty($$valueOriginalRelative)){ 
			echo $value; 
			echo "<div id='actions-{$idField}'><a href='javascript:void(0);' id='remove-{$idField}'>".__("Delete",$mf_domain)."</a></div>";
		} 
		if(empty($valueOriginalRelative)){
			$valueOriginalRelative = '';
		}
		?>
		<div class="mf_custom_field">
		<input tabindex="3" 
			id="<?php echo $idField?>" 
			name="<?php echo $inputName?>" 
			type="hidden" 
			class="<?php echo $requiredClass;?>"
			size="46"
			value="<?php echo $$valueOriginalRelative?>"
			<?php if ($customField->required_field) echo 'validate="required:true"'; ?>	
			/>
	
		<?php
		// adding the SWF upload 
		include_once( "RCCWP_SWFUpload.php" ) ;
		RCCWP_SWFUpload::Body($inputName, 2, $is_canvas, $urlInputSize);?>
		</div>
		<?php if ($customField->required_field){ ?>
			<div class="mf_message_error"><label for="<?php echo $inputName?>" class="error_magicfields error">This field is required.audio</label></div>
		<?php }
		
	}
	
	function ColorPickerInterface($customField, $inputName, $groupCounter, $fieldCounter,$fieldValue = NULL){
		
		$idField = RCCWP_WritePostPage::changeNameInput($inputName);
		if($fieldValue){
			$value=$fieldValue;
		}else{
			if(!empty($_REQUEST['post'])){
				$value = attribute_escape(RCCWP_CustomField::GetCustomFieldValues(true, $_REQUEST['post'], $customField->name, $groupCounter, $fieldCounter));
			}else{
				$value = '#c0c0c0';
			}
		}
		?>
		<input  id="<?php echo $idField; ?>" name="<?php echo $inputName?>" value="<?php echo $value?>" class="mf_color_picker" />
		<?php
	}
	
	function SliderInterface($customField, $inputName, $groupCounter, $fieldCounter,$fieldValue = NULL){
		
		$idField = RCCWP_WritePostPage::changeNameInput($inputName);
		
		$customFieldId = $customField->id;
		if(!empty($_REQUEST['post'])){
		$value = attribute_escape(RCCWP_CustomField::GetCustomFieldValues(true, $_REQUEST['post'], $customField->name, $groupCounter, $fieldCounter));
		}else{
			$value = 0;
		}

		if($fieldValue){
			$value=$fieldValue;
		}else{
			if(!empty($_REQUEST['post'])){			
				$value = attribute_escape(RCCWP_CustomField::GetCustomFieldValues(true, $_REQUEST['post'], $customField->name, $groupCounter, $fieldCounter));
			}else{
				$value = 0;
			}
		}
		
		if(!$customField->properties['min']) $customField->properties['min']=0;
		if(!$value) $value=$customField->properties['min'];
		if(!$customField->properties['max']) $customField->properties['max']=100;
		if(!$customField->properties['step']) $customField->properties['step']=1;
		?>
			<script>
				jQuery('document').ready(function(){
					jQuery('#slider_<?php echo $idField; ?>').slider({
						range: false, 
						value: <?php echo $value?>, 
						min: <?php echo $customField->properties['min']?>, 
						max: <?php echo $customField->properties['max']?>, 
						step: <?php echo $customField->properties['step']?>,
						handles: [{
							start: <?php echo $value?>, 
							step: <?php echo $customField->properties['step']?>,
							min: <?php echo $customField->properties['min']?>, 
							max: <?php echo $customField->properties['max']?>, 
							id: 'slider_<?php echo $idField; ?>'
							}],
						'slide': function(e, ui) {
								jQuery('#slide_value_<?php echo $idField; ?>').empty();
								jQuery('#slide_value_<?php echo $idField; ?>').append(ui.value);
								jQuery('#<?php echo $idField; ?>').val(ui.value);
							}
						});
				});
			</script>
			<div id='slider_<?php echo $idField; ?>' class='ui-slider-2' style="margin:40px;">
				<div class='ui-slider-handle'>
					<div class="slider_numeber_show" id="slide_value_<?php echo $idField; ?>">
						<?php echo $value?>
					</div>
				</div>	
			</div>
			<input  type="hidden" id="<?php echo $idField; ?>" name="<?php echo $inputName?>" value="<?php echo $value?>"  />		
		<?php
	}
	
	function MarkdownTextboxInterface($customField, $inputName, $groupCounter, $fieldCounter) {
    $customFieldId = '';

    if (isset($_REQUEST['post'])) {
  	  $customFieldId = $customField->id;
  		$value = attribute_escape(RCCWP_CustomField::GetCustomFieldValues(true, $_REQUEST['post'], $customField->name, $groupCounter, $fieldCounter));
  	}else{
  		$value = "";
  	}
    
    $requiredClass="";
  	if ($customField->required_field) $requiredClass = "field_required";
      ?>
  		<div class="mf_custom_field">
  		<?php 
  		print sprintf("<textarea %s class=\"%s markdowntextboxinterface\" id=\"%s\" name=\"%s\">%s</textarea>\n",
  			($customField->required_field)?'validate="required:true"':'',
  			$requiredClass,
  			$inputName,
  			$inputName,
  			$value
  		);
  	?>
  	</div>
  	<?php if ($customField->required_field){ ?>
  	  <div class="mf_message_error"><label for="<?php echo $inputName?>" class="error_magicfields error">This field is required.</label></div>
  	<?php } ?>
  	<?php
  }
	
	
	
	//Change the nameinput magicfields[type][id gruop index][id field index] => magicfields_{type}_{id group index}_{if field index}
	function changeNameInput($inputName){
		
		$patterns  = array('/\[/','/\]/');
		$replacements = array('_','');
		return preg_replace($patterns,$replacements,$inputName);
		
	}
  	
	
}
