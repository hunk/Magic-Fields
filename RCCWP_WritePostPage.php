<?php
/**
 * This class content all  type of fields for the panels
 */
 
 // traversal
	function RelatedTypeFieldsFilter($fields) {
    return "*";
  }

  function RelatedTypeWhereFilter($where) {
    echo $where;
    return $where;
  }
  
  function RelatedTypeOrderByFilter($orderby) {
	  global $wpdb;
	  $orderby = "$wpdb->postmeta.meta_value,$wpdb->posts.post_title";
    return $orderby;
  }
  
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

	<link rel="stylesheet" type="text/css" href="<?php echo MF_URI;?>js/colorpicker/css/colorpicker.css" media="screen" charset="utf-8" />

	<link rel="stylesheet" type="text/css" href="<?php echo MF_URI;?>css/jscrollpane.css" media="screen" charset="utf-8" />
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
						
    //loading jquery mousewheel
		wp_enqueue_script(	'mousewheel',
							MF_URI.'js/jquery.mousewheel.js'
						);

    //loading jquery mousewheel intent
		wp_enqueue_script(	'mwheelintent',
							MF_URI.'js/jquery.mwheelintent.js'
						);

    //loading jquery scrollpane (group summaries)
		wp_enqueue_script(	'jscrollpane',
							MF_URI.'js/jquery.jscrollpane.js'
						);

    //loading jquery scrollpane (group summaries)
		wp_enqueue_script(	'windowopen',
							MF_URI.'js/jquery.windowopen.min.js'
						);

    //loading jquery template plugin
		wp_enqueue_script(	'tmpl',
							MF_URI.'js/jquery.tmpl.js'
						);

    //loading jquery colorpicker plugin
		wp_enqueue_script(	'jquerycolorpicker', 
							MF_URI.'js/jquery.colorpicker.min.js'
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
			$title="Edit " .Inflect::singularize($blu->name);
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
		$groups = RCCWP_CustomWritePanel::GetCustomGroups($CUSTOM_WRITE_PANEL->id, "id");

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
				<div class="write_panel_wrapper"  id="write_panel_wrap_<?php echo $group->id;?>">
				
        <div class="mf-group-save-warning">Note: to save your changes you must also <strong>Publish or Update</strong> this <?php echo $post->post_type?>.</div> 

				<?php
				
				
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
			<a id="collapse_<?php echo $customGroup->id."Duplicate"."_".$customGroup->id."_".$order;?>" class="collapse_button" href="javascript:void(0);" title="Note: you can also double click a panel anywhere to collapse">Collapse</a>

      <div class="mf-group-loading">Loading Data&hellip;</div>
      
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
			<?php
				if( $customGroup->duplicate != 0 ){
				  $sgn = Inflect::singularize($customGroup->name);
			?>
			
			<div class="mf_toolbox">
				<span class="mf_counter sortable_mf" id="counter_<?php echo $customGroup->id;?>_<?php echo $groupCounter;?>"><?php echo $order;?></span>
				<span class="hndle sortable_mf row_mf">&nbsp;</span>

				<span class="mf_toolbox_controls">

					<?php
						if($groupCounter != 1):
						?>
							<a class ="delete_duplicate_button" href="javascript:void(0);" id="delete_duplicate-freshpostdiv_group_<?php echo $customGroup->id.'_'.$groupCounter; ?>"><?php _e('Remove '.$sgn, $mf_domain); ?></a>
						<?php else:?> 
							<a id="add_duplicate_<?php echo $customGroup->id."Duplicate"."_".$customGroup->id."_".$order;?>" class="duplicate_button" href="javascript:void(0);">Add Another <?=$sgn?></a>
					   <?php endif;?> 
				</span>
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
		<div class="mf-field mf-t-<?php echo strtolower(str_replace(" ","-",$customField->type)); ?> <?php echo str_replace(" ","_",$customField->type); ?>" id="row_<?php echo $inputCustomName?>">
			<div class="mf-field-title">
			<label for="<?php echo $inputCustomName?>">
				<?php
					if(empty($titleCounter)){
						$titleCounter = "";
					}
				?>
				<span class="name"><?php echo $customFieldTitle?><em><?php echo $titleCounter ?></em></span>
				<?php if (!empty($customFieldHelp)) {?>
					<small class="tip">(what's this?)<span class="field_help"><?php echo $customFieldHelp; ?></span></small>
				<?php } ?>
				
			</label>
			</div>
			<!-- /.mf-field-title -->
			
			<div>
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
				
				?>
				
				<div class="mf-duplicate-controls">
			  <?php
    
				$cfd = Inflect::singularize($customField->description);
					
				if($fieldCounter == 1) {
					?>
					<?php if($customField->duplicate != 0 ){ ?>
            <a href="javascript:void(0);" id="type_handler-<?php echo $inputCustomName ?>" class="typeHandler duplicate_field"><?php _e('Add Another '.$cfd, $mf_domain); ?></a>
					<?php } ?>
					<?php
				}
				else {
				?>
					<a class="delete_duplicate_field" href="javascript:void(0)" id="delete_field_repeat-<?php echo $inputCustomName?>"><?php _e('Remove '.$cfd, $mf_domain); ?></a>
				<?php
				}
				?>
  		  </div>
  		  <!-- ./title-controls -->


		</div>
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
		<label for="<?php echo $inputName.'_'.$option;?>" class="selectit mf-checkbox-list">
			<input tabindex="3" <?php if ($customField->required_field) echo 'validate="required:true"'; ?> class="checkbox_list_mf" id="<?php echo $inputName.'_'.$option;?>" name="<?php echo $inputName?>[]" value="<?php echo $option?>" type="checkbox" <?php echo $checked?> />
			
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
	  
		global $mf_domain, $wpdb;
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
		if ($customField->required_field) { $requiredClass = "field_required"; }
		?>
		<div class="mf_custom_field">
		<select tabindex="3" <?php if ($customField->required_field) echo 'validate="required:true"'; ?> class="<?php echo $requiredClass;?> listbox_mf" name="<?php echo $inputName?>">
			<option value=""><?php _e('--Select--', $mf_domain); ?></option>
		
		<?php
		
    $pn_cache = array(); // setup a panel name cache (so we only look up the panel name ONCe for each panel ID)
    
		if($panel_id == -4){
			$options=get_posts("post_type=post&numberposts=-1&order=ASC&orderby=title");
		}elseif($panel_id == -3){
			$options=get_posts("post_type=page&numberposts=-1&order=ASC&orderby=title");
		}elseif($panel_id == -2){
				$options=get_posts("post_type=post&meta_key=_mf_write_panel_id&numberposts=-1&order=ASC&orderby=title");
		}elseif($panel_id == -1){
					$options=get_posts("post_type=page&meta_key=_mf_write_panel_id&numberposts=-1&order=ASC&orderby=title");
		}elseif($panel_id == -6){
			$options=get_posts("post_type=any&numberposts=-1");
    }elseif($panel_id == -5){
      
      remove_filter('posts_where', array('RCCWP_Query','ExcludeWritepanelsPosts'));
      add_filter('posts_fields', 'RelatedTypeFieldsFilter');
      add_filter('posts_orderby', 'RelatedTypeOrderByFilter');
      
      $options = get_posts( array( 
        'suppress_filters' => false, 
        'post_type' => 'any', 
        'meta_key' =>  '_mf_write_panel_id',
        'nopaging' => true,
        'order' => 'ASC'
      ));
      
      remove_filter('posts_fields', 'RelatedTypeFieldsFilter');
      remove_filter('posts_orderby', 'RelatedTypeOrderByFilter');
      add_filter('posts_where', array('RCCWP_Query','ExcludeWritepanelsPosts'));
    }
		else{
			$options=get_posts("post_type=any&meta_key=_mf_write_panel_id&numberposts=-1&meta_value=$panel_id&order=ASC&orderby=title");
		}
		
		$last_panel_name = ""; // traversal (for grouping)

		foreach ($options as $option) :

  /* TRAVERSAL ADDITION - Adds grouping of related type fields when all write panels are listed -- */
      $panel_name = "";
		  $display_panel_name = "";
		  
		  if ( $panel_id == -5 || $panel_id == -2 || $panel_id == -1 ) {
		  	
		  	$panel_name = $pn_cache[$option->meta_value];
		  	
		  	if (!$panel_name) {
		  	  // look it up
		  	  $panel_name = $wpdb->get_var("SELECT `name` FROM ".MF_TABLE_PANELS." WHERE id = ".$option->meta_value);
          if ($panel_name) {
            $pn_cache[$option->meta_value] = $panel_name;
          }
	  	  }
	  	  
	  	  
        if (!$panel_name) {
          $panel_name = "";
	  	    $display_panel_name = Inflect::singularize($panel_name);
        } else {
  	  	  $display_panel_name = Inflect::singularize($panel_name)." - ";
        }
        
        if ($panel_name != "" && $panel_name != $last_panel_name) {
          if ($last_panel_name != "") {
            echo "</optgroup>";
          }

          echo '<optgroup label="'.Inflect::pluralize($panel_name).'">';
          $last_panel_name = $panel_name;
        }
      }
      /* END TRAVERSAL ADDITION */
      
			$selected = $option->ID == $value ? 'selected="selected"' : '';
		?>
			<option value="<?php echo $option->ID ?>" <?php echo $selected?>><?php echo $display_panel_name.$option->post_title ?></option><!-- TRAVERSAL UPDATE, adds display panel name as prefix -->
		<?php
		endforeach;

    // TRAVERSAL ADDITION, closes optgroup 
		if ($last_panel_name != "") {
		  echo "</optgroup>";
	  }
		// END TRAVERSAL ADDITION 
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
		</div><?php if (!$hideEditor){?></div><?php } ?>
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
		
		<p class="error_msg_txt" id="upload_progress_<?php echo $idField;?>" style="display:none;"></p>
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
		
		<div class="mf-file-links">
		  
		<?php if( $valueRelative ){ 
				echo '<span id="actions-'.$idField.'"><a href="'.$value.'" target="_blank" class="mf-file-view">'.__("View Current",$mf_domain).'</a></span>'; 
				echo '<a href="javascript:void(0);" id="remove-'.$idField.'" class="mf-file-delete">'.__("Delete",$mf_domain).'</a>';
			} 
		?>
		</div>
		<!-- /.mf-file-links -->
		
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
		<p 	class="error_msg_txt" id="upload_progress_<?php echo $idField;?>" style="display:none;">
		</p>	
		<div class="image_photo" style="width:150px; float: left">
			<?php echo $value;?>
		<div id="photo_edit_link_<?php echo $idField ?>" class="photo_edit_link"> 
			<?php
				if(isset($_REQUEST['post'])){	
					echo "&nbsp;<strong><a href='#remove' class='remove' id='remove-{$idField}'>".__("Delete",$mf_domain)."</a></strong>";
				}
			?>
		</div>
		</div>
		<div class="image_input" style="padding-left: 170px;">
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
			<label for="<?php echo $inputName.'_'.$option;?>" class="selectit">
				<input tabindex="3" <?php if ($customField->required_field) echo 'validate="required:true"'; ?> id="<?php echo $inputName.'_'.$option?>" name="<?php echo $inputName?>" value="<?php echo $option?>" type="radio" <?php echo $checked?>/>
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
		<p class="error_msg_txt" id="upload_progress_<?php echo $idField;?>" style="display:none;"></p>
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
				$value = '';
			}
		}
		?>
		<input id="<?php echo $idField; ?>" name="<?php echo $inputName?>" value="<?php echo $value?>" class="mf_color_picker" />
    <button class="mf-color-clear">Clear</button>
		<div id="mf-cp-<?php echo $idField; ?>" class="mf-cp { el: '#<?php echo $idField; ?>' }"></div>
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
			<div id='slider_<?php echo $idField; ?>' class="ui-slider-2">
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
	
	

  function CreateAttributesBox() {
  
    add_meta_box('mfattributespage', __('Magic Fields Attributes'), array('RCCWP_WritePostPage','attributesBoxContentPage'), 'page', 'side', 'core');
    add_meta_box('mfattributespost', __('Magic Fields Attributes'), array('RCCWP_WritePostPage','attributesBoxContentPost'), 'post', 'side', 'core');
  }
  
  
  

  function attributesBoxContentPage($post) {
    
    global $wpdb;
    
    $single_panel = FALSE;
    $panel_id = get_post_meta($post->ID, "_mf_write_panel_id", TRUE);
    
    if ($panel_id) {
      $panel =  RCCWP_CustomWritePanel::Get($panel_id);
    }
      
  ?>
    <p><strong><?php _e('Write Panel') ?></strong></p>
    <label class="screen-reader-text" for="parent_id"><?php _e('Write Panel') ?></label>
    <?php 
  
      // get a list of the write panels 
  
        $customWritePanels = RCCWP_CustomWritePanel::GetCustomWritePanels();
    		$promptEditingPost = RCCWP_Options::Get('prompt-editing-post');
        
        
        $templates_by_filename = array();
			  $templates = get_page_templates();
        // get the reverse map
        
        foreach ($templates as $name => $file) {
          $templates_by_filename[$file] = $name;
        }
        
        
        ?>
    		<select name="rc-cwp-change-custom-write-panel-id" id="rc-cwp-change-custom-write-panel-id">
          <option value="-1"><?php _e('(None)', $mf_domain); ?></option>
          
    		<?php
			
		    $items = array();
		    
    		foreach ($customWritePanels as $panel) :
    			$selected = $panel->id == $panel_id ? 'selected="selected"' : '';
		      $panel_theme = RCCWP_CustomWritePanel::GetThemePage($panel->name);
    		  $parent_page = RCCWP_CustomWritePanel::GetParentPage($panel->name);
    		  
    		  if ($parent_page != '') {
    		    $pp = get_page( $parent_page );
    		    
    		    if ($pp) {
    		      $parent_page_title = $pp->post_title;
            }
          
  		    }
  		    
  		    $allow = $panel->type == "page";
  		    
  		    if ($panel->single && $panel->id != $panel_id) {
  		      // check to see if there are any posts with this panel already. If so, we can't allow it to be used.
            $sql = "SELECT COUNT(*) FROM $wpdb->postmeta WHERE $wpdb->postmeta.meta_value = ".$panel->id." AND $wpdb->postmeta.meta_key = '_mf_write_panel_id'";
		        $count = $wpdb->get_var($sql);
            $allow = $count == 0;
		      }
  		    
  		    if ($allow) :  // cannot change to "single" panels
    		?>
    			<option value="<?php echo $panel->id?>" <?php echo $selected?>><?php echo $panel->name?></option>
    		<?php
    		  $items[$panel->id] = "{ panel_theme: '".$panel_theme."', template_name: '".addslashes($templates_by_filename[$panel_theme])."', parent_page: '".$parent_page."', parent_page_title: '".$parent_page_title."' }";

          endif;
          
    		endforeach;
    		?>
    		</select>

        <script type="text/javascript">
        var mf_panel_items = { "-1" : { panel_theme: '', template_name: '', parent_page: '', parent_page_title: '' } };
        
        <?php foreach ($items as $key => $value) : ?> 
        mf_panel_items[<?php echo $key ?>] = <?php echo $value; ?>; 
        <?php endforeach; ?>
        
        </script>


    <div id="rc-cwp-set-buttons">
      <p><?php _e('Note: Custom fields and groups associated with the selected write panel will be only be displayed once you have saved this page or post.')?></p>
      <p><?php _e('Before saving you may also like to set the <strong>Template</strong> and/or <strong>Parent</strong> in the <strong>Page Attributes</strong> panel to match the defaults for the selected write panel (recommended)') ?></p>
      <div class="inside">
        <input class="button" type="button" id="rc-cwp-set-page-template" value="<?php _e('Set Page Template') ?>" />
        <input class="button" type="button" id="rc-cwp-set-page-parent" value="<?php _e('Set Page Parent') ?>" />
      </div>
      <div class="mf-panel-info">
      <h5 class="mf-hd-panel-info">Defaults for the selected write panel</h5>
      <p>
        Template: <span id="mf-page-template-display"></span><br />
        Parent: <span id="mf-page-parent-display"></span>
      </p>
    </div>
    
  </div>
    <?php
  }


  function attributesBoxContentPost($post) {
    
    global $wpdb;
    
    $single_panel = FALSE;
    
    $panel_id = get_post_meta($post->ID, "_mf_write_panel_id", TRUE);
    
    if ($panel_id) {
      $panel =  RCCWP_CustomWritePanel::Get($panel_id);
    }
      
  ?>
    <p><strong><?php _e('Write Panel') ?></strong></p>
    <label class="screen-reader-text" for="parent_id"><?php _e('Write Panel') ?></label>
    <?php 
  
      // get a list of the write panels 
      $customWritePanels = RCCWP_CustomWritePanel::GetCustomWritePanels();
        
        ?>
    		<select name="rc-cwp-change-custom-write-panel-id" id="rc-cwp-change-custom-write-panel-id">
          <option value="-1"><?php _e('(None)', $mf_domain); ?></option>
    		<?php
			
		    $items = array();
		    
    		foreach ($customWritePanels as $panel) :
    			$selected = $panel->id == $panel_id ? 'selected="selected"' : '';
  		    $allow = $panel->type == "post";
  		    
  		    if ($panel->single && $panel->id != $panel_id) {
  		      // check to see if there are any posts with this panel already. If so, we can't allow it to be used.
            $sql = "SELECT COUNT(*) FROM $wpdb->postmeta WHERE $wpdb->postmeta.meta_value = ".$panel->id." AND $wpdb->postmeta.meta_key = '_mf_write_panel_id'";
		        $count = $wpdb->get_var($sql);
            $allow = $count == 0;
		      }
  		    
  		    if ($allow) :  // cannot change to "single" panels
    		  ?>
    			<option value="<?php echo $panel->id?>" <?php echo $selected?>><?php echo $panel->name?></option>
    		  <?php
          endif;
          
    		endforeach;
    		?>
    		</select>
    <?php
  }
  
	
	//Change the nameinput magicfields[type][id gruop index][id field index] => magicfields_{type}_{id group index}_{if field index}
	function changeNameInput($inputName){
		
		$patterns  = array('/\[/','/\]/');
		$replacements = array('_','');
		return preg_replace($patterns,$replacements,$inputName);
		
	}
  	
	
}
