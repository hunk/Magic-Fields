<?php
/**
 * This class content all  type of fields for the panels
 */
class RCCWP_WritePostPage {
    
    function ApplyCustomWritePanelAssignedCategories($content){ 
		global $CUSTOM_WRITE_PANEL;
		global $post,$title;
		
		$assignedCategoryIds = RCCWP_CustomWritePanel::GetAssignedCategoryIds($CUSTOM_WRITE_PANEL->id);
		$customThemePage = RCCWP_CustomWritePanel::GetThemePage($CUSTOM_WRITE_PANEL->name);
		
		if($_GET['custom-write-panel-id']){
		    foreach ($assignedCategoryIds as $categoryId)
		    {
			$toReplace = 'id="in-category-' . $categoryId . '"';
			$replacement = $toReplace . ' checked="checked"';
			$content = str_replace($toReplace, $replacement, $content);
		    }
		}
		//set default theme page
		if($post->ID == 0){
			$toReplace = "value='".$customThemePage."'";
			$replacement = "value='".$customThemePage."'" . ' SELECTED"';
			$content = str_replace($toReplace, $replacement, $content);
		}
		return $content;
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
	<?php
	}
		
	function CustomFieldsJavascript(){
		//loading  jquery ui datepicker
		wp_enqueue_script(	'datepicker',
							MF_URI.'js/ui.datepicker.js',
							array('jquery','jquery-ui-core')
						);
				
		//loading core of the datepicker
		wp_enqueue_script(	'mf_datepicker',
							MF_URI.'js/custom_fields/datepicker.js'
						);
						
		//loading Prototype framework
		wp_enqueue_script('prototype');
						
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
		</script>
 		<script type="text/javascript" src="<?php echo MF_URI?>js/groups.js"></script>
        
		<script type="text/javascript">
				function isset(  ) {
					// http://kevin.vanzonneveld.net
					// +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
					// +   improved by: FremyCompany
					// *     example 1: isset( undefined, true);
					// *     returns 1: false
					// *     example 2: isset( 'Kevin van Zonneveld' );
					// *     returns 2: true
					
					var a=arguments; var l=a.length; var i=0;
					
					while ( i!=l ) {
						if (typeof(a[i])=='undefined') { 
						return false; 
						} else { 
						i++; 
						}
					}
					
					return true;
				}
            
            // -------------
			// Edit Photo functions
			function prepareUpdatePhoto(inputName){	
				jQuery('#'+inputName+'_dorename').val(1);
				return true;
			}
		</script>
		
		<script type="text/javascript">
			var JS_MF_FILES_PATH = '<?php echo MF_FILES_URI ?>';
			var wp_root         = "<?php echo get_bloginfo('wpurl');?>";
			var mf_path    = "<?php echo MF_URI; ?>";
			var mf_relative = "<?php echo MF_URI_RELATIVE;?>";
			var phpthumb        = "<?php echo PHPTHUMB;?>";
			var swf_authentication = "<?php if ( function_exists('is_ssl') && is_ssl() ) echo $_COOKIE[SECURE_AUTH_COOKIE]; else echo $_COOKIE[AUTH_COOKIE]; ?>" ;
			var swf_nonce = "<?php echo wp_create_nonce('media-form'); ?>" ;
		</script>
		<script type="text/javascript" src="<?php echo MF_URI; ?>js/swfcallbacks.js" ></script>

		<script type="text/javascript">
				function isset(  ) {
					// http://kevin.vanzonneveld.net
					// +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
					// +   improved by: FremyCompany
					// *     example 1: isset( undefined, true);
					// *     returns 1: false
					// *     example 2: isset( 'Kevin van Zonneveld' );
					// *     returns 2: true
					
					var a=arguments; var l=a.length; var i=0;
					
					while ( i!=l ) {
						if (typeof(a[i])=='undefined') { 
						return false; 
						} else { 
						i++; 
						}
					}
					
					return true;
				}
				
			/**
			 * Pay Attention on this.
			 */
			function checkForm(event){
				var stopPublish = false;
				jQuery('input.field_required','textarea.field_required').each(
						function(inputField){
                            <?php  
		                        $hide_visual_editor = RCCWP_Options::Get('hide-visual-editor');
                                if ($hide_visual_editor == '' || $hide_visual_editor ==  0):
                            ?>
                                re = new RegExp(".*_multiline");
                                if(re.match(inputField.id)){
                                    inputField.value = tinyMCE.get(inputField.id).getContent();
                                }

                            <?php endif;?>

							if ($F(inputField) == "" &&
								!(Object.isElement($(inputField.id+"_last")) && $F(inputField.id+"_last") != "")	){
								stopPublish = true;

								// Update row color
								if (isset($("row_"+inputField.id).style))
									$("row_"+inputField.id).style.backgroundColor = "#FFEBE8";

								// Update iframe color if it exists
								if (Object.isElement($("upload_internal_iframe_"+inputField.id))){
								  	if ($("upload_internal_iframe_"+inputField.id).contentDocument) {
								    	// For FF
								    	$("upload_internal_iframe_"+inputField.id).contentDocument.body.style.backgroundColor = "#FFEBE8"; 
								  	} else if ($("upload_internal_iframe_"+inputField.id).contentWindow) {
									    // For IE5.5 and IE6
									    $("upload_internal_iframe_"+inputField.id).contentWindow.document.body.style.backgroundColor = "#FFEBE8";
								    }
								}
									
								$("fieldcellerror_"+inputField.id).style.display = "";
								$("fieldcellerror_"+inputField.id).innerHTML = "ERROR: Field can not be empty";
							}
							else{
								$("fieldcellerror_"+inputField.id).style.display = "none";
								if (isset($("row_"+inputField.id).style))
									$("row_"+inputField.id).style.backgroundColor = "";
									
								// Update iframe color if it exists
								if (Object.isElement($("upload_internal_iframe_"+inputField.id))){
								  	if ($("upload_internal_iframe_"+inputField.id).contentDocument) {
								    	// For FF
								    	$("upload_internal_iframe_"+inputField.id).contentDocument.body.style.backgroundColor = "#EAF3FA"; 
								  	} else if ($("upload_internal_iframe_"+inputField.id).contentWindow) {
									    // For IE5.5 and IE6
									    $("upload_internal_iframe_"+inputField.id).contentWindow.document.body.style.backgroundColor = "#EAF3FA";
								    }
								}
									
							}
						}
					);
				if (stopPublish){
					$("mf-publish-error-message").style.display = "";
					Event.stop(event);
					return false;
				}
				
				return true;
			}

			Event.observe(window, 'load', function() {
				Event.observe('post', 'submit', checkForm);
			});
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
				?>
                    <?php RCCWP_WritePostPage::GroupDuplicate($group,$element,$key,false);?>
                   <?php 
				}
                ?>
                <?php 
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
                <input type="hidden" name="rc-custom-write-panel-verify-key" id="rc-custom-write-panel-verify-key" value="<?php echo wp_create_nonce('rc-custom-write-panel')?>" />
		        <input type="hidden" name="rc-cwp-custom-write-panel-id" value="<?php echo $CUSTOM_WRITE_PANEL->id?>" />
                </div>
            <?php
			}else{
               
            ?>
                <div class="write_panel_wrapper" id="write_panel_wrap_<?php echo $group->id;?>">
                <?php
             		      RCCWP_WritePostPage::GroupDuplicate($group,1,1,false) ;
                          $gc = 1;
                ?>
                <input type='hidden' name='g<?php echo $group->id?>counter' id='g<?php echo $group->id?>counter' value='<?php echo $gc?>' />
           		<input type='hidden' name="rc-custom-write-panel-verify-key" id="rc-custom-write-panel-verify-key" value="<?php echo wp_create_nonce('rc-custom-write-panel')?>" />
		        <input type='hidden' name="rc-cwp-custom-write-panel-id" value="<?php echo $CUSTOM_WRITE_PANEL->id;?>" />
                </div>
            <?php 
           
           }
	}

    /**
     * This method and   groupduplicated  will be merged in nexts commits
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
if( $customGroup->duplicate != 0 ){ $add_class_rep="mf_duplicate_group";}else{$add_class_rep="";}
		?>
		<div class="magicfield_group <?php echo $add_class_rep;?>" id="freshpostdiv_group_<?php echo
		 $customGroup->id.'_'.$groupCounter;?>">	
            <div>
            <div class="inside">
			    <?php	
	        		foreach ($customFields as $field) {

		        		$customFieldName = RC_Format::GetInputName(attribute_escape($field->name));
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
	    	
				<span class="mf_counter" id="counter_<?php echo $customGroup->id;?>_<?php echo $groupCounter;?>">
					(<?php echo $order;?>)
				</span>
				<span class="hndle sortable_mf row_mf">
					<img title="Order" src="<?php echo MF_URI;?>/images/move.png"/>
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
		$customFieldName = RC_Format::GetInputName(attribute_escape($customField->name));
		$customFieldTitle = attribute_escape($customField->description);
        $groupId =  $customGroup_id;
		$inputName = $customFieldId."_".$groupCounter."_".$fieldCounter."_".$groupId."_".$customFieldName; // Create input tag name
 		if( $fieldCounter > 1 && $customField->duplicate == 0 ) return ;
 		if( $fieldCounter > 1) $titleCounter = " ($fieldCounter)";
 		
 		$field_group = RCCWP_CustomGroup::Get($customField->group_id);

		?>
		<div class="mf-field" id="row_<?php echo $inputName?>">
			<label for="<?php echo $inputName?>">
				<?php
					if(empty($titleCounter)){
						$titleCounter = "";
					}
					
				?>
				<?php echo $customFieldTitle.$titleCounter?>
			</label>
			<span>
				<p class="error_msg_txt" id="fieldcellerror_<?php echo $inputName?>" style="display:none"></p>
				<?php		
					switch ($customField->type)
					{
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
						default:
							;
					}
				if($fieldCounter == 1)
				{
					?>
					<?php if($customField->duplicate != 0 ){ ?>
					<br />
					
					 <a class ="typeHandler" href="javascript:void(0);" id="type_handler-<?php echo $inputName ?>" > 
						<img class="duplicate_image"  src="<?php echo MF_URI; ?>images/duplicate.png" alt="<?php _e('Add field duplicate', $mf_domain); ?>"/>  <?php _e('Duplicate', $mf_domain); ?>
					</a>
					<?php } ?>
					 
					<?php
				}
				else
				{	
				?>
					<br />
					
					<a class ="delete_duplicate_field" href="javascript:void(0)" id="delete_field_repeat-<?php echo $inputName?>"> 
						<img class="duplicate_image"  src="<?php echo MF_URI; ?>images/delete.png" alt="<?php _e('Remove field duplicate', $mf_domain); ?> "/> <?php _e('Remove', $mf_domain); ?> 
					</a>
				<?php
				}
				?>
				<input type="hidden" name="rc_cwp_meta_keys[]" value="<?php echo $inputName?>" />
		</span>
		</div>
	<?php
	}
	
	function CheckboxInterface($customField, $inputName, $groupCounter, $fieldCounter)
	{
		$customFieldId = '';
		if (isset($_REQUEST['post']))
		{
			$customFieldId = $customField->id;
			$value = RCCWP_CustomField::GetCustomFieldValues(true, $_REQUEST['post'], $customField->name, $groupCounter, $fieldCounter);
			$checked = $value == 'true' ? 'checked="checked"' : '';
		}else{
			$checked = "";
		}
		?>
		
		<input  type="hidden" name="<?php echo $inputName?>" value="false" />
		<input tabindex="3" class="checkbox checkbox_mf" name="<?php echo $inputName?>" value="true" id="<?php echo $inputName?>" type="checkbox" <?php echo $checked?> />
		
		<?php
	}
	
	function CheckboxListInterface($customField, $inputName, $groupCounter, $fieldCounter)
	{
		$customFieldId = '';
		$values = array();
		if (isset($_REQUEST['post']))
		{
			$customFieldId = $customField->id;
			$values = (array) RCCWP_CustomField::GetCustomFieldValues(false, $_REQUEST['post'], $customField->name, $groupCounter, $fieldCounter);
		}else{
			$values = $customField->default_value;
		}
		?>
		
		
		<?php
		foreach ($customField->options as $option) :
			$checked = in_array($option, (array)$values) ? 'checked="checked"' : '';
			$option = attribute_escape(trim($option));
		?>
		
		    <input tabindex="3" class="checkbox_list_mf" id="<?php echo $option?>" name="<?php echo $inputName?>[]" value="<?php echo $option?>" type="checkbox" <?php echo $checked?> />
			<label for="<?php echo $inputName;?>" class="selectit mf-checkbox-list">
				<?php echo attribute_escape($option)?>
			</label><br />
		
		<?php
		endforeach;
		?>
			
		
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
		
		if ($customField->required_field) $requiredClass = "field_required";
		?>
		
		<select tabindex="3"  class="<?php echo $requiredClass;?> listbox_mf" name="<?php echo $inputName?>">
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
		
		</select>	
		
		
		<?php
	}
	
	function ListboxInterface($customField, $inputName, $groupCounter, $fieldCounter) {

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
		<select  class="<?php echo $requiredClass;?> listbox_mf"  tabindex="3" id="<?php echo $inputName?>" name="<?php echo $inputName?>[]" multiple size="<?php echo $inputSize?>" style="height: 6em;">
		
		<?php
		foreach ($customField->options as $option) :
			if(!empty($option)):
				$selected = in_array($option, (array)$values) ? 'selected="selected"' : '';
				$option = attribute_escape(trim($option));
				
		?>
			
			<option value="<?php echo $option?>" <?php echo $selected?>><?php echo $option?></option>
			
		<?php
			endif;
		endforeach;
		?>
		
		</select>
		
		
		<?php
	}
	
	function MultilineTextboxInterface($customField, $inputName, $groupCounter, $fieldCounter)
	{
		$customFieldId = '';
		
		if (isset($_REQUEST['post']))
		{
			$customFieldId = $customField->id;
			$value = RCCWP_CustomField::GetCustomFieldValues(true, $_REQUEST['post'], $customField->name, $groupCounter, $fieldCounter);
			$value = apply_filters('the_editor_content', $value);

		}else{
			$value = "";
		}
		
		$inputHeight = (int)$customField->properties['height'];
		$inputWidth = (int)$customField->properties['width'];
		if ($customField->required_field) $requiredClass = "field_required";
		
		$hide_visual_editor = RCCWP_Options::Get('hide-visual-editor');
		if ($hide_visual_editor == '' || $hide_visual_editor == 0){
		?>
		<script type="text/javascript">
			jQuery(document).ready(function(){	 
			    tinyMCE.execCommand('mceAddControl', true, "<?php echo $inputName?>");
			});

			function add_editor(id){
			    tinyMCE.execCommand('mceAddControl', false, id);
			}
			
			function del_editor(id){
			    tinyMCE.execCommand('mceRemoveControl', false, id);
			}
			
			</script>
		<?php } ?>
		<?php if ($hide_visual_editor == '' || $hide_visual_editor == 0){ ?>
		<div class="tab_multi_mf">
		    <a onclick="del_editor('<?php echo $inputName?>');" class="edButtonHTML_mf">HTML</a>		
		    <a onclick="add_editor('<?php echo $inputName?>');" class="edButtonHTML_mf" >Visual</a>
		</div>
		<?php } ?>
		
		<div class="mul_mf">
		<textarea  class="<?php echo $requiredClass;?>" tabindex="3"  id="<?php echo $inputName?>" name="<?php echo $inputName?>" rows="<?php echo $inputHeight?>" cols="<?php echo $inputWidth?>"><?php echo $value?></textarea>
		</div>
		
	<?php
	}
	
	function TextboxInterface($customField, $inputName, $groupCounter, $fieldCounter)
	{
		$customFieldId = '';
		
		if (isset($_REQUEST['post'])) {
			$customFieldId = $customField->id;
			$value = attribute_escape(RCCWP_CustomField::GetCustomFieldValues(true, $_REQUEST['post'], $customField->name, $groupCounter, $fieldCounter));
		}else{
			$value = "";
        }

		$inputSize = (int)$customField->properties['size'];
		if ($customField->required_field) $requiredClass = "field_required";
		
		// If the field is at right, set a constant width to the text box
		$field_group = RCCWP_CustomGroup::Get($customField->group_id);
		if ($field_group->at_right){
			if ($inputSize>14) $inputSize = 14;
		}
		?>
		
		<input class="<?php echo $requiredClass;?> textboxinterface" tabindex="3" id="<?php echo $inputName?>" name="<?php echo $inputName?>" value="<?php echo $value?>" type="text" size="<?php echo $inputSize?>" />
		
		<?php
	}
	


    /**
     * File Field
     *
     */
	function FileInterface($customField, $inputName, $groupCounter, $fieldCounter)
	{
		global $mf_domain;
		$customFieldId = '';
		$freshPageFolderName = (dirname(plugin_basename(__FILE__)));
		if ($customField->required_field) $requiredClass = "field_required";

		if (isset($_REQUEST['post']))
		{
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
		
		<p class="error_msg_txt" id="upload_progress_<?php echo $inputName?>" style="visibility:hidden;height:0px"></p>
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
                jQuery("#remove-<?php echo $inputName;?>").click(remove_file);

            });
        </script>
		
		<?php if( $valueRelative ){ 
                echo "<span id='actions-{$inputName}'>(<a href='{$value}' target='_blank'>".__("View Current",$mf_domain)."</a>)</span>"; 
                echo "&nbsp;<a href='javascript:void(0);' id='remove-{$inputName}'>".__("Delete",$mf_domain)."</a>";
            } 
        ?>
			
		<input tabindex="3" 
			id="<?php echo $inputName?>" 
			name="<?php echo $inputName?>" 
			type="hidden"
			class="<?php echo $requiredClass;?>" 
			size="46"
			value="<?php echo $valueRelative?>"
			/>
		
		<?php
		include_once( "RCCWP_SWFUpload.php" ) ;
		RCCWP_SWFUpload::Body($inputName, 0, $is_canvas, $urlInputSize) ;
	}


	function PhotoInterface($customField, $inputName, $groupCounter, $fieldCounter) {
		global $mf_domain;
		$customFieldId 	= ''; // <---- ¿?
		$filepath 		= $inputName . '_filepath'; /// <---- ¿?
		$noimage 		= ""; // <---- if no exists image? 

		if ($customField->required_field) $requiredClass = "field_required";
		$imageThumbID = "";
		$imageThumbID = "img_thumb_".$inputName; 


		if (isset($_REQUEST['post'])) {
			$customFieldId = $customField->id;
			$value = RCCWP_CustomField::GetCustomFieldValues(true, $_REQUEST['post'], $customField->name, $groupCounter, $fieldCounter);

            $path = PHPTHUMB."?src=".MF_FILES_URI;
			$valueRelative = $value;
			$value = $path.$value;
			if(!(strpos($value, 'http') === FALSE))
				$hidValue = str_replace('"', "'", $valueRelative);
			$value = "<img src='".$value."' class='magicfields' />"; 
		} else if( !empty($customField->value)){
            $path = PHPTHUMB."?src=".MF_FILES_PATH;
            $valueRelative = $customField->value;
            $value  = $path.$customField->value;

            if(!(strpos($value, 'http') === FALSE)){
    		    $hidValue = str_replace('"', "'", $valueRelative);
	    	    $value = "<img src='".$value."' class='magicfields' />";
            }


        }else{
			$noimage = "<img src='".MF_URI."images/noimage.jpg' id='".$imageThumbID."'/>";
		}
		
		if(!empty($valueRelative) && $valueRelative == '') {
			$noimage = "<img src='".MF_URI."images/noimage.jpg' id='".$imageThumbID."'/>";
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

		<p class="error_msg_txt" id="upload_progress_<?php echo $inputName?>" style="visibility:hidden;height:0px"></p>
		

        <!--- This Script is for remove the image -->
	    <script type="text/javascript">
            remove_photo = function(){
                if(confirm("<?php _e('Are you sure?', $mf_domain); ?>")){
                        //get the  name to the image
                        id = jQuery(this).attr('id').split("-")[1];
                        image = jQuery('#'+id).val();
                        jQuery.get('<?php echo MF_URI;?>RCCWP_removeFiles.php',{'action':'delete','file':image},
                                    function(message){
                                        if(message == "true"){
                                            photo = "img_thumb_" + id;
                                            jQuery("#"+photo).attr("src","<?php echo MF_URI."images/noimage.jpg"?>");
                                            jQuery("#photo_edit_link_"+id).empty();
                                            jQuery("#"+id).val("");

                                        }
                                    });
                    }
            }

            jQuery(document).ready(function(){
                jQuery(".remove").live('click',remove_photo);
            });
        </script>
        <!-- Here finish -->


		<div id="image_photo" style="width:150px;">
		
			<?php
				if(!empty($valueRelative) && $valueRelative != "") { 
					if(!(strpos($value, '<img src') === FALSE)) {
						$valueLinkArr = explode("'", $value);
						$valueLink = $valueLinkArr[1];
						
					

						if(!(strpos($value, '&sw') === FALSE)) {
							// Calculating Image Width/Height
							$arrSize = explode("=",$value);
							$arrSize1 = explode("&",$arrSize[3]);
							$arrSize2 = explode("&",$arrSize[4]);

							$imageWidth = $arrSize1[0];
							$imageHeight = $arrSize2[0];
							// END

							$valueArr = explode("&sw", $value);
							$valueArr = explode("'", $valueArr[1]);
							$value = str_replace("&sw".$valueArr[0]."'", "&sw".$valueArr[0]."&w=150&h=120' align='center' id='".$imageThumbID."'", $value);
						} else if(!(strpos($value, '&w') === FALSE)) {
							// Calculating Image Width/Height
							$arrSize = explode("=",$value);
							$arrSize1 = explode("&",$arrSize[3]);
							$arrSize2 = explode("'",$arrSize[4]);

							$imageWidth = $arrSize1[0];
							$imageHeight = $arrSize2[0];
							// END

							$valueArr = explode("&", $value);
							$valueArr = explode("'", $valueArr[2]);
							$value = str_replace($valueArr[0], "&w=150&h=120' align='left' id='".$imageThumbID."'", $value);
						} else {
							// Calculating Image Width/Height
							if(!empty($params)){
							$arrSize = explode("&",$params);
							$arrSize1 = explode("=",$arrSize[1]);
							$arrSize2 = explode("=",$arrSize[2]);
							}else{
								$arrSize = '';
								$arrSize1 = array('','');
								$arrSize2 = array('','');
							}
							
							$imageWidth = $arrSize1[1];
							$imageHeight = $arrSize2[1];
							// END

							$valueArr = explode("'", $value);
							$value = str_replace($valueArr[1], $valueArr[1]."&w=150' id='".$imageThumbID."' align='", $value);
						}
						
						echo '<a style="display: block;margin-left: auto;margin-right: auto " href="' . $valueLink . '" target="_blank">' . $value .'</a>';
						}
					}else{
						$valueLink = '';
					}
					echo $noimage;
					$arrSize = explode("phpThumb.php?src=",$valueLink);
					
					if(!empty($arrSize[1])){
						$fileLink = $arrSize[1];
					}else{
						$fileLink = '';
					}
					
					$andPos = strpos($fileLink,"?");
					
					
					if ($andPos === FALSE)	 $andPos = strpos($fileLink,"&");
				
					// Remove & parameters from file path
					if ($andPos>0)	$fileLink = substr($fileLink, 0, $andPos);
				
					$ext = substr($fileLink, -3, 3);	
	    ?>	
		
		<div id="photo_edit_link_<?php echo $inputName ?>" class="photo_edit_link"> 
			
				<?php
				if(isset($_REQUEST['post']) && $hidValue != '')
				{ 	
                   echo "&nbsp;<strong><a href='#remove' class='remove' id='remove-{$inputName}'>".__("Delete",$mf_domain)."</a></strong>";               
				}
				?>			
		    </div>
		</div>
		<br />
		<div id="image_input">
			<?php
				if(empty($requiredClass)){
					$requiredClass ='';
				}
			?>		
			<input tabindex="3" 
				id="<?php echo $inputName?>" 
				name="<?php echo $inputName?>" 
				type="hidden" 
				class="<?php echo $requiredClass;?>"
				size="46"
				value="<?php echo $hidValue?>"
				/>
			
			<?php
			include_once( "RCCWP_SWFUpload.php" ) ;
			RCCWP_SWFUpload::Body($inputName, 1, $is_canvas, $urlInputSize) ;
			?>

		</div>
		
		<input type="hidden" name="rc_cwp_meta_photos[]" value="<?php echo $inputName?>" 	/>
		<input type="hidden" name="<?php echo $inputName?>_dorename" id="<?php echo $inputName?>_dorename" value="0" />
		

		<!-- Used to store name of URL Field -->
		<!--<input type="hidden" name="parent_text_<?php echo $countImageThumbID; ?>" id="parent_text_<?php echo $countImageThumbID; ?>" value="<?php echo $filepath; ?>"/>
		<input type="hidden" name="hidImgValue<?php echo $countImageThumbID; ?>" id="hidImgValue<?php echo $countImageThumbID; ?>" value="<?php echo $inputName; ?>_last" />-->

		<?php
	}
	
	function RadiobuttonListInterface($customField, $inputName, $groupCounter, $fieldCounter)
	{
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
		
		<?php
		foreach ($customField->options as $option) :
			$checked = $option == $value ? 'checked="checked"' : '';
			$option = attribute_escape(trim($option));
		?>
			<label for="<?php echo $inputName;?>" class="selectit">
				<input tabindex="3" id="<?php echo $option?>" name="<?php echo $inputName?>" value="<?php echo $option?>" type="radio" <?php echo $checked?>/>
				<?php echo $option?>
			</label><br />
		<?php
		endforeach;
		?>
		
		<?php
	}

	function DateInterface($customField, $inputName, $groupCounter, $fieldCounter) {
		global $wpdb;
		$customFieldId = '';
		
		if (isset($_REQUEST['post']))
		{
			$customFieldId = $customField->id;
			$value = attribute_escape(RCCWP_CustomField::GetCustomFieldValues(true, $_REQUEST['post'], $customField->name, $groupCounter, $fieldCounter));
			
			$value = date($customField->properties['format'],strtotime($value));
			
		} else { 
			$value =  date($customField->properties['format']);
		}
		
		$dateFormat = $customField->properties['format'];
		
		
		$field_group = RCCWP_CustomGroup::Get($customField->group_id);
		$inputSize = 25;
		if ($field_group->at_right){
			$inputSize = 15;
		}
?>	
		<div id="format_date_field_<?php echo $inputName;?>" style="display:none"><?php echo $dateFormat;?></div>
			
		<input 	id="display_date_field_<?php echo $inputName?>"
		 		value="<?php echo $value?>" 
				type="text" 
				size="<?php echo $inputSize?>" 
				class="datepicker_mf"   
		READONLY/>
		
		<input 	id="date_field_<?php echo $inputName?>" 
				name="<?php echo $inputName?>" 
				value="<?php echo $value?>" type="hidden" 
		/>
		<input 	type="button" 
				value="Pick..." 
				id="pick_<?php echo $inputName;?>" 
				class="datebotton_mf"
		/>
		<input 	type="button" 
				id="today_<?php echo $inputName;?>"
				value="Today" 
				class="todaybotton_mf"
		/>

		<input 
				type="hidden" 
				name="rc_cwp_meta_date[]" 
				value="<?php echo $inputName?>" 	
		/>
		<?php
	}


    /**
     * Audio  field
     *
     *
     */
	function AudioInterface($customField, $inputName, $groupCounter, $fieldCounter){
		global $mf_domain;
        $customFieldId = '';
		$freshPageFolderName = (dirname(plugin_basename(__FILE__))); 
		if ($customField->required_field) $requiredClass = "field_required";
		
		if (isset($_REQUEST['post']))
		{
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
				$value = stripslashes(trim("\<div  id='obj-{$inputName}' style=\'width:260px;padding-top:3px;\'\>\<object classid=\'clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\' codebase='\http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0\' width=\'95%\' height=\'20\' wmode=\'transparent\' \>\<param name=\'movie\' value=\'".MF_URI."js/singlemp3player.swf?file=".urlencode($valueOriginal)."\' wmode=\'transparent\' /\>\<param name=\'quality\' value=\'high\' wmode=\'transparent\' /\>\<embed src=\'".MF_URI."js/singlemp3player.swf?file=".urlencode($valueOriginal)."' width=\'100\%\' height=\'20\' quality=\'high\' pluginspage=\'http://www.macromedia.com/go/getflashplayer\' type=\'application/x-shockwave-flash\' wmode=\'transparent\' \>\</embed\>\</object\>\</div\><br />"));			
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
		<p class="error_msg_txt" id="upload_progress_<?php echo $inputName?>" style="visibility:hidden;height:0px"></p>
		<script type="text/javascript">
            //this script is for remove the audio file using ajax
            remove_audio = function(){
                if(confirm("<?php _e('Are you sure?', $mf_domain); ?>")){
                    //get the name to the image
                    id = jQuery(this).attr('id').split("-")[1];
                    file = jQuery('#'+id).val(); 
                    jQuery.get('<?php echo MF_URI;?>RCCWP_removeFiles.php',{'action':'delete','file':file},
                                function(message){
                                    if(message =="true"){
                                        jQuery('#obj-'+id).empty();
                                        jQuery('#actions-'+id).empty();
                                    }

                                });
                }                           
            }

            jQuery(document).ready(function(){
                jQuery("#remove-<?php echo $inputName;?>").click(remove_audio);
            });
        </script>
		<?php if( !empty($$valueOriginalRelative)){ 
                                                echo $value; 
                                                echo "<div id='actions-{$inputName}'><a href='javascript:void(0);' id='remove-{$inputName}'>".__("Delete",$mf_domain)."</a></div>";
                                            } 
			if(empty($valueOriginalRelative)){
				$valueOriginalRelative = '';
			}
		?>
		
		
		<input tabindex="3" 
			id="<?php echo $inputName?>" 
			name="<?php echo $inputName?>" 
			type="hidden" 
			class="<?php echo $requiredClass;?>"
			size="46"
			value="<?php echo $$valueOriginalRelative?>"	
			/>
    
		<?php
        //adding the  SWF upload 
		include_once( "RCCWP_SWFUpload.php" ) ;
		RCCWP_SWFUpload::Body($inputName, 2, $is_canvas, $urlInputSize) ;
		
	}
	
	function ColorPickerInterface($customField, $inputName, $groupCounter, $fieldCounter,$fieldValue = NULL){
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
		<script type='text/javascript' src='<?php echo MF_URI?>js/sevencolorpicker.js'></script>
		<script type="text/javascript">
			jQuery('document').ready(function(){
				jQuery('#<?php echo $inputName?>').SevenColorPicker();
			});
		</script>
		<input  id="<?php echo $inputName?>" name="<?php echo $inputName?>" value="<?php echo $value?>"  />
		<?php
	}
	
	function SliderInterface($customField, $inputName, $groupCounter, $fieldCounter,$fieldValue = NULL){
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
		
		if(!$customField->properties['min']){
			$customField->properties['min']=0;
		}
		if(!$value){
			$value=$customField->properties['min'];
		}
		if(!$customField->properties['max']){
			$customField->properties['max']=100;
		}
		if(!$customField->properties['step']){
			$customField->properties['step']=0;
		}
		global $wp_version;
		if($wp_version <= 2.7){ ?>
		<link rel="stylesheet" href="<?php echo MF_URI?>css/flora.slider.css" type="text/css" media="screen" title="Flora (Default)">
		<script type="text/javascript" src="<?php echoMF_URI?>js/ui.slider.js"></script>
		<?php }else{ ?>
			<link rel="stylesheet" href="<?php echo MF_URI?>css/base/ui.all.css" type="text/css" media="screen" />
			<script type="text/javascript" src="<?php echo MF_URI?>js/ui.core_WP28.js"></script>
			<script type="text/javascript" src="<?php echo MF_URI?>js/ui.slider_WP28.js"></script>
		<?php } ?>
			<script>
				jQuery('document').ready(function(){
					jQuery('#slider_<?php echo $inputName?>').slider({range: false, value: <?php echo $value?> , min: <?php echo $customField->properties['min']?>, max: <?php echo $customField->properties['max']?>, stepping: <?php echo $customField->properties['step']?>,
					handles: [ {start: <?php echo $value?>, stepping: <?php echo $customField->properties['step']?>,min: <?php echo $customField->properties['min']?>, max: <?php echo $customField->properties['max']?>, id: 'slider_<?php echo $inputName?>'} ]
					

								,'slide': function(e, ui){ 
	                    jQuery('#slide_value_<?php echo $inputName?>').empty();
									jQuery('#slide_value_<?php echo $inputName?>').append(ui.value);
									jQuery('#<?php echo $inputName?>').val(ui.value);
	            }

									});

				});
				
			
			</script>
	
		<style>
		.slider_numeber_show{
			margin-top: -16px;
			padding-left: 3px;
		}
		</style>
			<div id='slider_<?php echo $inputName?>' class='ui-slider-2' style="margin:40px;">
				<div class='ui-slider-handle'><div class="slider_numeber_show" id="slide_value_<?php echo $inputName?>">
				<?php echo $value?>
				</div></div>	
			</div>
			<input  type="hidden" id="<?php echo $inputName?>" name="<?php echo $inputName?>" value="<?php echo $value?>"  />		
		<?php
	}

}

?>