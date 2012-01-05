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
  
class RCCWP_WritePostPage  {

  function mf_category_order($cats,$parent=0,$depth = 0,$resp = array() ){
    foreach($cats as $k => $cat){
      if($cat->parent == $parent){
        $term_id = $cat->term_id;
        $resp[$term_id]->term_id = $term_id;
        $resp[$term_id]->name = sprintf('%s%s',str_repeat('&nbsp;', $depth * 4),$cat->slug);
        unset($cats[$k]);
        $resp = RCCWP_WritePostPage::mf_category_order($cats,$term_id,$depth+1,$resp);
      }
    }
    return $resp;
  }

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
      
        foreach($assignedCategoryIds as $key => $cat){
          if((int)$cat == 0){
            $tc = get_category_by_slug($cat);
            $assignedCategoryIds[$key] = $tc->cat_ID;
          }
        }

        $assignedCategoryIds = apply_filters('mf_extra_categories',$assignedCategoryIds);
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
                var nonce_ajax_upload = "<?php echo wp_create_nonce('once_ajax_uplooad') ?>";
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
						
		$slider_js = MF_URI.'js/ui.slider.js';
		//load slider for wp31 
		if(is_wp31()) $slider_js = MF_URI.'js/jquery.ui.slider.js';
		//loading  jquery ui slider
		wp_enqueue_script(	'slider',
							$slider_js,
							array('jquery','jquery-ui-core')
							,NULL,true
						);
						
		//loading the code for delete images
		wp_enqueue_script(	'mf_colorpicker',
							MF_URI.'js/custom_fields/colorpicker.js'
						);				
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
							MF_URI.'js/jquery.colorpicker.js'
						);


    //load affix plugin (for tooltips)
		wp_enqueue_script(	'jqueryaffix', 
							MF_URI.'js/jquery.affix.min.js'
						);
					
    //load reveal plugin (for tooltips)
		wp_enqueue_script(	'jqueryreveal', 
							MF_URI.'js/jquery.reveal.min.js'
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
    
    wp_enqueue_script('valums_file_uploader',
  	  MF_URI.'js/valumsfileuploader.js'
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
        $lang = get_bloginfo('language');
        $name = "Magic Fields Custom Fields";

        if($lang == "en-US"){
  				$name = Inflect::singularize($CUSTOM_WRITE_PANEL->name);
        }
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


    $mf_post_id = apply_filters('mf_source_post_data', $_REQUEST['post']);
		
		//we are passing the group_id in the args of the add_meta_box
		$group = $group['args'];
		
			//render the elements
			$customFields = RCCWP_CustomGroup::GetCustomFields($group->id);

			//when will be edit the  Post
			if(isset( $mf_post_id ) && count($customFields) > 0){
				//using the first field name we can know 
				//the order  of the groups
				$firstFieldName = $customFields[0]->name;

				$order = RCCWP_CustomField::GetOrderDuplicates($mf_post_id,$firstFieldName);
				?> 
				<div class="write_panel_wrapper"  id="write_panel_wrap_<?php echo $group->id;?>">
				
				<?php if ($group->duplicate) : ?>
				
        <div class="mf-group-controls">
          <div class="mf-group-count"></div>
          <div class="buttons">
            <a href="#" class="mf-expand-all-button"><?php _e('Expand All', $mf_domain);?></a>
            <a href="#" class="mf-collapse-all-button"><?php _e('Collapse All', $mf_domain);?></a>
          </div>
        
        </div>
        
        <?php endif; ?>
        
        <div class="mf-group-save-warning"><?php _e("Note: to save your changes you must also <strong>Publish</strong> or <strong>Update</strong>",$mf_domain);?><?php //echo $post->post_type;?>.</div> 

        
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

      <?php if ($group->duplicate) : ?>
				
        <div class="mf-group-controls">
          <div class="mf-group-count"></div>
          <div class="buttons">
            <a href="#" class="mf-expand-all-button">Expand All</a>
            <a href="#" class="mf-collapse-all-button">Collapse All</a>
          </div>
        
        </div>
        
        <?php endif; ?>
        
        <div class="mf-group-save-warning"><?php _e("Note: to save your changes you must also <strong>Publish</strong> or <strong>Update</strong>",$mf_domain);?><?php //echo $post->post_type;?>.</div> 


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
 		global $CUSTOM_WRITE_PANEL;

		$ex_class = $customGroup->expanded ? "mf-group-expanded" : '';

    if ($customGroup->name == "__default") {
      // for the default group (top level), check the expand flag on the WRITE PANEL instead
      $ex_class = $CUSTOM_WRITE_PANEL->expanded ? "mf-group-expanded" : '';
    }
    
    $mf_post_id = apply_filters('mf_source_post_data', $_REQUEST['post']);
		//getting the custom fields
		$customFields = RCCWP_CustomGroup::GetCustomFields($customGroup->id);
		
		//if don't have fields then finish
		if (count($customFields) == 0) return;

		require_once("RC_Format.php");
		if( $customGroup->duplicate != 0 ){ 
			$add_class_rep="mf_duplicate_group";}else{$add_class_rep="";
		}
		?>
		<div class="magicfield_group <?php echo $add_class_rep;?> <?php echo $ex_class ?>" id="freshpostdiv_group_<?php 
			
			echo $customGroup->id.'_'.$groupCounter;?>">
			<a id="collapse_<?php echo $customGroup->id."Duplicate"."_".$customGroup->id."_".$order;?>" class="collapse_button" href="javascript:void(0);">Collapse</a>

      <div class="mf-group-loading"><?php _e('Loading Data&hellip;', $mf_domain);?></div>
      
      <div>
			<div class="inside">
			<div class="mf-fields">
				<?php	
					foreach ($customFields as $field) {

						$customFieldName = $field->name;
						$customFieldTitle = esc_attr($field->description);
						$groupId  = $customGroup->id;
						$inputName = $field->id."_".$groupCounter."_1_".$groupId."_".$customFieldName;
						
						if(isset($mf_post_id)){
							$fc = RCCWP_CustomField::GetFieldDuplicates($mf_post_id,$field->name,$groupCounter);
							$fields_order =  RCCWP_CustomField::GetFieldsOrder($mf_post_id,$field->name,$groupCounter);
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
      </div>
	    <!-- /.mf-fields -->

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
							<a class ="delete_duplicate_button { lang: { confirm: '<?php _e("Are you sure?", $mf_domain) ?>' } }" href="javascript:void(0);" id="delete_duplicate-freshpostdiv_group_<?php echo $customGroup->id.'_'.$groupCounter; ?>"><span><?php _e('Remove', $mf_domain); ?></span> <?php echo $sgn ?></a>
						<?php else:?> 
							<a id="add_duplicate_<?php echo $customGroup->id."Duplicate"."_".$customGroup->id."_".$order;?>" class="duplicate_button" href="javascript:void(0);" title="<?php _e('Note: hold down the SHIFT key as you click to collapse this item before the new item is added', $mf_domain); ?>"><span><?php _e('Add Another', $mf_domain);?></span> <?=$sgn?></a>
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
		$customFieldTitle = esc_attr($customField->description);
		$customFieldHelp = $customField->help_text; // htmlentities($customField->help_text,ENT_COMPAT,'UTF-8');
		$groupId = $customGroup_id;
		$inputCustomName = $customFieldId."_".$groupCounter."_".$fieldCounter."_".$groupId."_".$customFieldName; // Create input tag name
		
		
		$inputName = "magicfields[{$customFieldName}][{$groupCounter}][{$fieldCounter}]";
 		if( $fieldCounter > 1 && $customField->duplicate == 0 ) return ;
 		if( $fieldCounter > 1) $titleCounter = " (<span class='counter_{$customFieldName}_{$groupCounter}'>$fieldCounter</span>)";

 		$field_group = RCCWP_CustomGroup::Get($customField->group_id);
 		
		/* 
		 * Add the lang attribute if last part of the field name matches defined languages
		 * 
		 * define( 'ADMIN_LANGS', 'en|fr|de' );
		 * example: field name 'the_about_text_en' matches 'en' and sets ' lang="en"'
		 *
		 */  
		if( defined( 'ADMIN_LANGS' ) ) {
			$customFieldNameParts = explode( '_', $customFieldName );
			$lang_switch = ( preg_match( '/'.ADMIN_LANGS.'/', $customFieldNameParts[ sizeof( $customFieldNameParts) - 1 ] ) ) ? ' lang="'.$customFieldNameParts[ sizeof( $customFieldNameParts) - 1 ].'"' : '';
		}else {
			$lang_switch = '';
		}
		
		if( isset( $customField->properties['strict-max-length'] ) && $customField->properties['strict-max-length'] == 1 ) {
			$fieldMaxLengthClass = ' maxlength';
		}else {
			$fieldMaxLengthClass = '';
		}

		
    
    $fieldCustomClass = "mf-field-$customFieldName"; // allows some special styling in wordpress filters
    
    $duplicateClass = "";
    if ($fieldCounter > 1) {
      $duplicateClass = "mf-field-duplicate";
    }
    
		?>
		<div class="mf-field <?php echo $duplicateClass ?> <?php echo $fieldCustomClass ?> mf-t-<?php echo strtolower(str_replace(" ","-",$customField->type)); ?> <?php echo str_replace(" ","_",$customField->type); echo $fieldMaxLengthClass; ?>" id="row_<?php echo $inputCustomName?>"<?php echo $lang_switch;?>>
			<div class="mf-field-title">
			<label for="<?php echo $inputCustomName?>">
				<?php
					if(empty($titleCounter)){
						$titleCounter = "";
					}
				?>
				<span class="name"><?php echo $customFieldTitle?><em><?php echo $titleCounter ?></em></span>
				<?php
				if( $customField->required_field == 1 ) { ?> <span class="required">*</span><?php }
				if (!empty($customFieldHelp)) {?>
					<small class="tip"><?php _e("what's this?",$mf_domain);?><span class="field_help"><?php echo $customFieldHelp; ?></span></small>
				<?php }
				if( isset( $customField->properties['strict-max-length'] ) && $customField->properties['strict-max-length'] == 1 ) {
					if( $customField->type == 'Multiline Textbox' ) {
						$charsRemainingSize = $customField->properties['height']*$customField->properties['width'];
					}else {
						$charsRemainingSize = $customField->properties['size'];
					}
				?><small class="remaining"><?php _e( 'Characters left', $mf_domain )?>: <span class="charsRemaining" title="<?php _e('Characters left', $mf_domain); ?>"><?=$charsRemainingSize?></span></small><?php
				}
				?>
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
  				case 'Image (Upload Media)' :
    				RCCWP_WritePostPage::MediaPhotoInterface($customField, $inputName, $groupCounter, $fieldCounter);
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
            <a href="javascript:void(0);" id="type_handler-<?php echo $inputCustomName ?>" class="typeHandler duplicate_field"><span><?php _e('Add Another', $mf_domain); ?></span> <?php echo $cfd ?></a>
					<?php } ?>
					<?php
				}
				else {
				?>
					<a class="delete_duplicate_field" href="javascript:void(0)" id="delete_field_repeat-<?php echo $inputCustomName?>"><span><?php _e('Remove', $mf_domain); ?></span> <?php echo $cfd ?></a>
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

    $mf_post_id = apply_filters('mf_source_post_data', $_REQUEST['post']);
		
		if (isset($mf_post_id))
		{
			$customFieldId = $customField->id;
			$value = RCCWP_CustomField::GetCustomFieldValues(true, $mf_post_id, $customField->name, $groupCounter, $fieldCounter);
			$checked = $value == 'true' ? 'checked="checked"' : '';
		}else{
			$checked = "";
		}
		?>
		<div class="mf_custom_field">
		<input  type="hidden" name="<?php echo $inputName?>_1" value="false" />
		<input tabindex="3" class="checkbox checkbox_mf" <?php if ($customField->required_field) echo 'validate="required:true"'; ?> name="<?php echo $inputName?>" value="true" id="<?php echo $idField;?>" type="checkbox" <?php echo $checked?> /></div>
		<?php if ($customField->required_field){ ?>
		<div class="mf_message_error"><label for="<?php echo $inputName?>" class="error_magicfields error block"><?php _e("This field is required",$mf_domain)?></label></div>
		<?php }
	}
	
	function CheckboxListInterface($customField, $inputName, $groupCounter, $fieldCounter) {

    $mf_post_id = apply_filters('mf_source_post_data', $_REQUEST['post']);

		$customFieldId = '';
		
		$defClass = '';
		
		$idField = RCCWP_WritePostPage::changeNameInput($inputName);
		
		$values = array();
		if (isset($mf_post_id)) {
			$customFieldId = $customField->id;
			$values = (array) RCCWP_CustomField::GetCustomFieldValues(false, $mf_post_id, $customField->name, $groupCounter, $fieldCounter);
		}else{
		  $defClass = "mf-default";
			$values = $customField->default_value;
		}
		
		?>
		
		<div class="mf_custom_field <?php echo $defClass ?>">
		<?php
		foreach ($customField->options as $option) :
			$checked = in_array($option, (array)$values) ? 'checked="checked"' : '';
			$option = esc_attr(trim($option));
		?>
		<label for="<?php echo $inputName.'_'.$option;?>" class="selectit mf-checkbox-list">
			<input tabindex="3" <?php if ($customField->required_field) echo 'validate="required:true"'; ?> class="checkbox_list_mf" id="<?php echo $inputName.'_'.$option;?>" name="<?php echo $inputName?>[]" value="<?php echo $option?>" type="checkbox" <?php echo $checked?> />
			
				<?php echo esc_attr($option)?>
			</label><br />
		
		<?php
		endforeach;
		?></div>
		<?php if ($customField->required_field){ ?>
			<div class="mf_message_error"><label for="<?php echo $inputName?>[]" class="error_magicfields error"><?php _e("This field is required",$mf_domain)?></label></div>
		<?php } ?>
		<?php
	}
	
	function DropdownListInterface($customField, $inputName, $groupCounter, $fieldCounter)
	{
		global $mf_domain;
		$customFieldId = '';

    $mf_post_id = apply_filters('mf_source_post_data', $_REQUEST['post']);
		
		$defClass = '';

		if (isset($mf_post_id)) {
			$customFieldId = $customField->id;
			$value = esc_attr(RCCWP_CustomField::GetCustomFieldValues(true, $mf_post_id, $customField->name, $groupCounter, $fieldCounter));
		} else {
		  $defClass = "mf-default";
			$value = $customField->default_value[0];
		}
		
		$requiredClass = "";
		if ($customField->required_field) $requiredClass = "field_required";
		?>
		<div class="mf_custom_field <?php echo $defClass ?>">
		<select tabindex="3" <?php if ($customField->required_field) echo 'validate="required:true"'; ?> class="<?php echo $requiredClass;?> listbox_mf" name="<?php echo $inputName?>">
			<option value=""><?php _e('--Select--', $mf_domain); ?></option>
		
		<?php
		foreach ($customField->options as $option) :
			$selected = $option == $value ? 'selected="selected"' : '';
			$option = esc_attr(trim($option));
		?>
			<option value="<?php echo $option?>" <?php echo $selected?>><?php echo $option?></option>
		<?php
		endforeach;
		?>
		
		</select>	</div>
		<?php if ($customField->required_field){ ?>
			<div class="mf_message_error"><label for="<?php echo $inputName?>" class="error_magicfields error"><?php _e("This field is required",$mf_domain)?></label></div>
		<?php }
	}
	
	


	//eeble
	function RelatedTypeInterface($customField, $inputName, $groupCounter, $fieldCounter) {
		global $mf_domain, $wpdb;
    $mf_post_id = apply_filters('mf_source_post_data', $_REQUEST['post']);

		$customFieldId = '';
		if (isset($mf_post_id)) {
			$customFieldId = $customField->id;
			$value = esc_attr(RCCWP_CustomField::GetCustomFieldValues(true, $mf_post_id, $customField->name, $groupCounter, $fieldCounter));
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
                }elseif($panel_id == -7){
                  $options=get_categories("hide_empty=0");
                  $options = RCCWP_WritePostPage::mf_category_order($options,0,0);
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
                }else{
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
	  	  
	  	  $panel = RCCWP_CustomWritePanel::Get($option->meta_value);
        
        if (!$panel_name) {
          $panel_name = "";
	  	    $display_panel_name = "";
        } else {
          
          if ($panel->single) {
  	  	    $display_panel_name = "";
          }
          else {
  	  	    $display_panel_name = "&nbsp;&nbsp;&nbsp;".Inflect::singularize($panel_name)." - ";
          }
        }
        
        if ($panel_name != "" && $panel_name != $last_panel_name) {
          if ($last_panel_name != "") {
            echo "</optgroup>";
          }

          if ($panel->single) {
            $last_panel_name = "";
          } else {
            echo '<optgroup label="'.Inflect::pluralize($panel_name).'">';
            $last_panel_name = $panel_name;
          }
        }
      }
      /* END TRAVERSAL ADDITION */
                  if( $panel_id == -7 ) {
                    $selected = $option->term_id == $value ? 'selected="selected"' : '';
                    ?>
                    <option value="<?php echo $option->term_id ?>" <?php echo $selected?>><?php echo $display_panel_name.$option->name ?></option><!-- TRAVERSAL UPDATE, adds display panel name as prefix -->
                       <?php      
                       }else {
      
                    $selected = $option->ID == $value ? 'selected="selected"' : '';
                    ?>
                    <option value="<?php echo $option->ID ?>" <?php echo $selected?>><?php echo $display_panel_name.$option->post_title ?></option><!-- TRAVERSAL UPDATE, adds display panel name as prefix -->
                       <?php
                       }
		endforeach;

    // TRAVERSAL ADDITION, closes optgroup 
		if ($last_panel_name != "") {
		  echo "</optgroup>";
	  }
		// END TRAVERSAL ADDITION 
		?>

		</select></div>
		<?php if ($customField->required_field){ ?>
			<div class="mf_message_error"><label for="<?php echo $inputName?>" class="error_magicfields error"><?php _e("This field is required",$mf_domain)?></label></div>
		<?php } ?>
		
		<?php
	}
	
	function ListboxInterface($customField, $inputName, $groupCounter, $fieldCounter) {
		$idField = RCCWP_WritePostPage::changeNameInput($inputName);
		$customFieldId = '';
		$defClass = "";
    $mf_post_id = apply_filters('mf_source_post_data', $_REQUEST['post']);

		if (isset($mf_post_id)){
			$customFieldId = $customField->id;
			$values = (array) RCCWP_CustomField::GetCustomFieldValues(false, $mf_post_id, $customField->name, $groupCounter, $fieldCounter);
			
		}else{
			$values = $customField->default_value;
		  $defClass = "mf-default";
		}
		
		$inputSize = (int)$customField->properties['size'];
		$requiredClass = "mf_listbox";
		if ($customField->required_field) $requiredClass = "mf_listbox field_required";
		?>
		<div class="mf_custom_field <?php echo $defClass ?>">
		<select <?php if ($customField->required_field) echo 'validate="required:true"'; ?> class="<?php echo $requiredClass;?> listbox_mf"  tabindex="3" id="<?php echo $idField;?>" name="<?php echo $inputName?>[]" multiple size="<?php echo $inputSize?>" style="height: auto;">
		
		<?php
		foreach ($customField->options as $option) {
			if(!empty($option)){
				$selected = in_array($option, (array)$values) ? 'selected="selected"' : '';
				$option = esc_attr(trim($option));
		?>
			<option value="<?php echo $option?>" <?php echo $selected?>><?php echo $option?></option>	
		<?php
			}
		}
		?>
		</select></div>
			<?php if ($customField->required_field){ ?>
				<div class="mf_message_error"><label for="<?php echo $inputName?>" class="error_magicfields error"><?php _e("This field is required",$mf_domain)?></label></div>
			<?php } ?>
		
		<?php
	}
	
	function MultilineTextboxInterface($customField, $inputName, $groupCounter, $fieldCounter){
		$customFieldId = '';
    $mf_post_id = apply_filters('mf_source_post_data', $_REQUEST['post']);
		
		$idField = RCCWP_WritePostPage::changeNameInput($inputName);
		
		if( isset($mf_post_id) ){
			$customFieldId = $customField->id;
			$value = RCCWP_CustomField::GetCustomFieldValues(true, $mf_post_id, $customField->name, $groupCounter, $fieldCounter);
			if( isset($customField->properties['hide-visual-editor']) && !(int)$customField->properties['hide-visual-editor']){
                          if( !RCCWP_Options::Get('dont-remove-tmce') ){
                            $value = apply_filters('the_editor_content', $value);
                          }
			}
		}else{
			$value = "";
		}

                $value = apply_filters('mf_multiline_value',$value,$groupCounter,$fieldCounter);
		
		$inputHeight = (int)$customField->properties['height'];
		$inputWidth = (int)$customField->properties['width'];
		$hideEditor = @(int)$customField->properties['hide-visual-editor'];
		
		if( isset( $customField->properties['strict-max-length'] ) && $customField->properties['strict-max-length'] == 1 ) {
			$maxlength = ' maxlength="'. ($customField->properties['height'] * $customField->properties['width']) .'"';
		}else {
			$maxlength = '';
		}
		
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
		
		<div style="display: none" id="wp-<?php echo $idField ?>-media-buttons">
			<?php 
			// WP 3.3 changed here, so you need the media buttons on the editor for the tinyMCE plugin to work
			require_once( ABSPATH . 'wp-admin/includes/media.php' ) ?>
			<?php media_buttons( $idField ) ?>
		</div>
		
		<textarea  <?php if ($customField->required_field) echo 'validate="required:true"'; ?> class="<?php echo $requiredClass;?> <?php echo $classEditor; ?> <?php echo $pre_text ?>" tabindex="3"  id="<?php echo $idField; ?>" name="<?php echo $inputName?>" rows="<?php echo $inputHeight?>" cols="<?php echo $inputWidth?>"<?php echo $maxlength?>><?php echo $value?></textarea>
<?php
if( isset( $customField->properties['strict-max-length'] ) && $customField->properties['strict-max-length'] == 1 ) {
?>		<script language="javascript">
			jQuery(document).ready(function(){			
				var maximal = parseInt(jQuery('#<?php echo $idField; ?>').attr('maxlength'));
				var actual = parseInt(jQuery('#<?php echo $idField; ?>').val().length);
				jQuery('#<?php echo $idField; ?>').parents(".mf-field").find('.charsRemaining').html(maximal - actual);
			});
		</script>
<?php
}
?>
		</div><?php if (!$hideEditor){?></div><?php } ?>
		<?php if ($customField->required_field){ ?>
			<div class="mf_message_error"><label for="<?php echo $idField; ?>" class="error_magicfields error"><?php _e("This field is required",$mf_domain)?></label></div>
		<?php } ?>
		
	<?php
	}
	
	function TextboxInterface($customField, $inputName, $groupCounter, $fieldCounter){
		$customFieldId = '';
    $mf_post_id = apply_filters('mf_source_post_data', $_REQUEST['post']);
		
		$idField = RCCWP_WritePostPage::changeNameInput($inputName);
		
		if (isset($mf_post_id)) {
			$customFieldId = $customField->id;
			$value = esc_attr(RCCWP_CustomField::GetCustomFieldValues(true, $mf_post_id, $customField->name, $groupCounter, $fieldCounter));
		}else{
			$value = "";
		}

    $value = apply_filters('mf_textbox_value',$value,$groupCounter,$fieldCounter);
   
    $requiredClass= '';
		$inputSize = (int)$customField->properties['size'];
		if ($customField->required_field) $requiredClass = "field_required";
		
		// If the field is at right, set a constant width to the text box
		$field_group = RCCWP_CustomGroup::Get($customField->group_id);
		if ($field_group->at_right){
			if ($inputSize>14) $inputSize = 14;
		}

		if( isset( $customField->properties['strict-max-length'] ) && $customField->properties['strict-max-length'] == 1 ) {
			$maxlength = ' maxlength="'.$customField->properties['size'].'"';
		}else {
			$maxlength = '';
		}

		?>
		<div class="mf_custom_field">
		<input <?php if ($customField->required_field) echo 'validate="required:true"'; ?> class="<?php echo $requiredClass;?> textboxinterface" tabindex="3" id="<?php echo $idField ?>" name="<?php echo $inputName?>" value="<?php echo $value?>" type="text" size="<?php echo $inputSize?>"<?php echo $maxlength?> />
<?php
if( isset( $customField->properties['strict-max-length'] ) && $customField->properties['strict-max-length'] == 1 ) {
?>		<script language="javascript">
			jQuery(document).ready(function(){			
				var maximal = parseInt(jQuery('#<?php echo $idField; ?>').attr('maxlength'));
				var actual = parseInt(jQuery('#<?php echo $idField; ?>').val().length);
				jQuery('#<?php echo $idField; ?>').parents(".mf-field").find('.charsRemaining').html(maximal - actual);
			});
		</script>
<?php
}
?>
		</div>
			<?php if ($customField->required_field){ ?>
				<div class="mf_message_error"><label for="<?php echo $inputName?>" class="error_magicfields error"><?php _e("This field is required",$mf_domain)?></label></div>
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
    $mf_post_id = apply_filters('mf_source_post_data', $_REQUEST['post']);
		
		$customFieldId = '';
		$freshPageFolderName = (dirname(plugin_basename(__FILE__)));
		$requiredClass = "";
		if ($customField->required_field) $requiredClass = "field_required";

		if (isset($mf_post_id)) {
			$customFieldId = $customField->id;
			$value = esc_attr(RCCWP_CustomField::GetCustomFieldValues(true, $mf_post_id, $customField->name, $groupCounter, $fieldCounter));
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
		
		<p class="error_msg_txt upload-msg" id="upload_progress_<?php echo $idField;?>" style="display:none;"></p>
		<script type="text/javascript"> 
			//this script is for remove the  file  related  to the post (using ajax)
      //@todo is neccessary refactor remove_file, the audio type file and the type file use exactly the same function.

			remove_file = function(){
				if(confirm("<?php _e('Are you sure?', $mf_domain); ?>")){
					//get  the name to the file
          pattern = /remove\-([a-z0-9\-\_]+)/i;
					id = jQuery(this).attr("id");
          id = pattern.exec(id);
          id = id[1];
					file = jQuery('#'+id).val();
					
					jQuery('#'+id).closest(".mf-field").find(".ajax-upload-list").html('');
					
          //@ the file SHOULD be removed AFTER to save the post not inmediately
					jQuery.get('<?php echo MF_URI;?>RCCWP_removeFiles.php',{'action':'delete','file':file},
								function(message){
									jQuery('#actions-'+id).empty();
									jQuery('#remove-'+id).empty();
									jQuery('#'+id).val("");
								});

				}
			};


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
			<div class="mf_message_error"><label for="<?php echo $inputName?>" class="error_magicfields error"><?php _e("This field is required",$mf_domain)?></label></div>
		<?php }
	}

	function PhotoInterface($customField, $inputName, $groupCounter, $fieldCounter) {
		global $mf_domain;
		
		$idField = RCCWP_WritePostPage::changeNameInput($inputName);
    $mf_post_id = apply_filters('mf_source_post_data', $_REQUEST['post']);
		
		if(!empty($mf_post_id)){
			$hidValue = RCCWP_CustomField::GetCustomFieldValues(true,$mf_post_id, $customField->name, $groupCounter, $fieldCounter);
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
      $value = aux_image($hidValue,'w=150&h=120&zc=1');
			$value = "<img src='{$value}' id='{$imageThumbID}'/>";
		}
?>
		<p 	class="error_msg_txt upload-msg" id="upload_progress_<?php echo $idField;?>" style="display:none;">
		</p>	

		<div class="image_layout">

		<div class="image_photo">
		  <div class="image_wrap">
			<?php echo $value;?>
      </div>
		<div id="photo_edit_link_<?php echo $idField ?>" class="photo_edit_link"> 
			<?php
				if(isset($mf_post_id)){	
					echo '<a href="'.MF_FILES_URI.$hidValue.'" target="_blank">View</a>&nbsp;&nbsp;|&nbsp;&nbsp;<strong><a href="#remove" class="remove" id="remove-'.$idField.'">'.__("Delete",$mf_domain).'</a></strong>';
				}
			?>
		</div>
		</div>
		<div class="image_input">
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
		
	  </div>
	  <!-- /.image_layout -->
	  
		<div style="clear: both; height: 1px;"> </div>
			<?php if ($customField->required_field){ ?>
				<div class="mf_message_error"><label for="<?php echo $inputName?>" class="error_magicfields error"><?php _e("This field is required",$mf_domain)?></label></div>
			<?php
			} ?>

		<?php
	}
	
	function RadiobuttonListInterface($customField, $inputName, $groupCounter, $fieldCounter){
		$customFieldId = '';
    $defClass = "";
    $mf_post_id = apply_filters('mf_source_post_data', $_REQUEST['post']);

		if (isset($mf_post_id)) {
			$value = esc_attr(RCCWP_CustomField::GetCustomFieldValues(true, $mf_post_id, $customField->name, $groupCounter, $fieldCounter));
		}
		else
		{
			$value = $customField->default_value[0];
		  $defClass = "mf-default";
		}
		?>
		<div class="mf_custom_field <?php echo $defClass ?>">
		<?php
		foreach ($customField->options as $option) :
			$checked = $option == $value ? 'checked="checked"' : '';
			$option = esc_attr(trim($option));
		?>
			<label for="<?php echo $inputName.'_'.$option;?>" class="selectit">
				<input tabindex="3" <?php if ($customField->required_field) echo 'validate="required:true"'; ?> id="<?php echo $inputName.'_'.$option?>" name="<?php echo $inputName?>" value="<?php echo $option?>" type="radio" <?php echo $checked?>/>
				<?php echo $option?>
			</label>
		<?php
		endforeach; ?>
		</div>
		<?php if ($customField->required_field){ ?>
		<div class="mf_message_error"><label for="<?php echo $inputName?>" class="error_magicfields error"><?php _e("This field is required",$mf_domain)?></label></div>
		<?php
		}
	}

	function DateInterface($customField, $inputName, $groupCounter, $fieldCounter) {
		global $wpdb;
		$customFieldId = '';
    $mf_post_id = apply_filters('mf_source_post_data', $_REQUEST['post']);
		
		$idField = RCCWP_WritePostPage::changeNameInput($inputName);
		
		if (isset($mf_post_id)) {
			$customFieldId = $customField->id;
			$value = esc_attr(RCCWP_CustomField::GetCustomFieldValues(true, $mf_post_id, $customField->name, $groupCounter, $fieldCounter));
			
			$raw_value = $value;
			
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
			  <?php if ($customField->required_field) echo 'validate="required:true"'; ?>	
		readonly="readonly" />
		
		<input 	id="date_field_<?php echo $idField; ?>" 
				name="<?php echo $inputName?>" 
				value="<?php echo $raw_value?>" type="hidden" 
		/>
		<input 	type="button" 
				value="Pick..." 
				id="pick_<?php echo $idField; ?>" 
				class="datebotton_mf button" 
		/>
		<input 	type="button" 
				id="today_<?php echo $idField; ?>"
				value="Today" 
				class="todaybotton_mf button"
		/>
		<input 	type="button" 
				id="blank_<?php echo $idField; ?>"
				value="Blank" 
				class="blankBotton_mf button"
		/>
		<input 	type="hidden"
				value="<?php echo $today;?>"
				id="tt_<?php echo $idField; ?>"
				class="todaydatebutton_mf button"
		/>
		<input 	type="hidden"
				value="<?php echo date("Y-m-d");?>"
				id="tt_raw_<?php echo $idField; ?>"
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
    $mf_post_id = apply_filters('mf_source_post_data', $_REQUEST['post']);

		if ($customField->required_field) $requiredClass = "field_required";
		
		if (isset($mf_post_id)) {
			$customFieldId = $customField->id;
			$valueOriginal = RCCWP_CustomField::GetCustomFieldValues(true, $mf_post_id, $customField->name, $groupCounter, $fieldCounter);
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
		<p class="error_msg_txt upload-msg" id="upload_progress_<?php echo $idField;?>" style="display:none;"></p>
		<script type="text/javascript">
			//this script is for remove the audio file using ajax
			remove_audio = function(){
				if(confirm("<?php _e('Are you sure?', $mf_domain); ?>")){
					//get the name to the image
				  //id = jQuery(this).attr('id').split("-")[1];
          pattern = /remove\-([a-z0-9\-\_]+)/i;
					id = jQuery(this).attr("id");
          id = pattern.exec(id);
          id = id[1];

					file = jQuery('#'+id).val(); 
          jQuery('#'+id).closest(".mf-field").find(".ajax-upload-list").html('');
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
		
		<div class="mf-audio-value-actions">
		<?php 
		if( !empty($$valueOriginalRelative)){ 
			echo '<div class="mf-audio-value">'.$value.'</div>'; 
			echo "<div id='actions-{$idField}' class='actions-audio'><a href='javascript:void(0);' id='remove-{$idField}' class='remove-audio'>".__("Delete",$mf_domain)."</a></div>";
		} else {
			echo '<div class="mf-audio-value"></div>'; 
			echo "<div id='actions-{$idField}' class='actions-audio' style='display: none'><a href='javascript:void(0);' id='remove-{$idField}' class='remove-audio'>".__("Delete",$mf_domain)."</a></div>";
	  }
	  
		if(empty($valueOriginalRelative)){
			$valueOriginalRelative = '';
		}
		?>
	  </div>
	  <!-- /.mf-audio-value-actions -->
	  
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
			<div class="mf_message_error"><label for="<?php echo $inputName?>" class="error_magicfields error"><?php _e("This field is required",$mf_domain)?>.audio</label></div>
		<?php }
		
	}
	
	function ColorPickerInterface($customField, $inputName, $groupCounter, $fieldCounter,$fieldValue = NULL){
		
		$idField = RCCWP_WritePostPage::changeNameInput($inputName);
    $requiredClass="";
    $mf_post_id = apply_filters('mf_source_post_data', $_REQUEST['post']);

  	if ($customField->required_field) $requiredClass = "field_required";

		if($fieldValue){
			$value=$fieldValue;
		}else{
			if(!empty($mf_post_id)){
				$value = esc_attr(RCCWP_CustomField::GetCustomFieldValues(true, $mf_post_id, $customField->name, $groupCounter, $fieldCounter));
			}else{
				$value = '';
			}
		}
		?>
		<input  <?php if ($customField->required_field) echo 'validate="required:true"'; ?>  id="<?php echo $idField; ?>" name="<?php echo $inputName?>" value="<?php echo $value?>" class="mf_color_picker <?php print $requiredClass;?>" />
    <button class="mf-color-clear">Clear</button>
    <div class="mf_clear"></div>
		<div id="mf-cp-<?php echo $idField; ?>" class="mf-cp { el: '#<?php echo $idField; ?>' }"></div>
		<?php
	}
	
	function SliderInterface($customField, $inputName, $groupCounter, $fieldCounter,$fieldValue = NULL){
		
    $defClass = '';
    $mf_post_id = apply_filters('mf_source_post_data', $_REQUEST['post']);

		$idField = RCCWP_WritePostPage::changeNameInput($inputName);
		
		$customFieldId = $customField->id;
		if(!empty($mf_post_id)){
		$value = esc_attr(RCCWP_CustomField::GetCustomFieldValues(true, $mf_post_id, $customField->name, $groupCounter, $fieldCounter));
		}else{
			$value = 0;
      $defClass = 'mf-default';
		}

		if($fieldValue){
			$value=$fieldValue;
		}else{
			if(!empty($mf_post_id)){
				$value = esc_attr(RCCWP_CustomField::GetCustomFieldValues(true, $mf_post_id, $customField->name, $groupCounter, $fieldCounter));
			}else{
				$value = 0;
        $defClass = 'mf-default';
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
			<div id='slider_<?php echo $idField; ?>' class="mf_custom_field  <?php echo $defClass ?> ui-slider-2">
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
    $mf_post_id = apply_filters('mf_source_post_data', $_REQUEST['post']);

    if (isset($mf_post_id)) {
  	  $customFieldId = $customField->id;
  		$value = esc_attr(RCCWP_CustomField::GetCustomFieldValues(true, $mf_post_id, $customField->name, $groupCounter, $fieldCounter));
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
  	  <div class="mf_message_error"><label for="<?php echo $inputName?>" class="error_magicfields error"><?php _e("This field is required",$mf_domain)?></label></div>
  	<?php } ?>
  	<?php
  }
  
  function MediaPhotoInterface($customField, $inputName, $groupCounter, $fieldCounter) {
  		global $mf_domain,$post;

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
  			$path = PHPTHUMB."?src=";
  			$info = wp_get_attachment_image_src($hidValue,'original');
  			$path_image_media = $info[0];
  			$value  = $path.$path_image_media."&w=150&h=120&zc=1";
  			$value  = "<img src='{$value}' id='{$imageThumbID}'/>";
  		}
  ?>
  		<p 	class="error_msg_txt" id="upload_progress_<?php echo $idField;?>" style="visibility:hidden;height:0px">
  		</p>	
  		<div id="image_photo" style="width:150px; float: left">
  			<?php echo $value;?>
  		<div id="photo_edit_link_<?php echo $idField ?>" class="photo_edit_link"> 
  			<?php
  				if($hidValue){	
  					echo "&nbsp;<strong><a href='#remove' class='remove_media' id='remove-{$idField}'>".__("Remove Image",$mf_domain)."</a></strong>";
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
  				<?php $thumb_class= 'thickbox1';
  				if(is_wp30()) $thumb_class= 'thickbox';
  				?>

  			<a class="<?php echo $thumb_class; ?> update_field_media_upload" id="thumb_<?php echo $idField ?>" href="media-upload.php?post_id=<?php echo $post->ID; ?>&#038;type=image&#038;TB_iframe=1" ><?php _e('Set Image',$mf_domain); ?></a>
  			</div>
  			<?php
  			if(!is_wp30()):
  			?>
  			<script>
  			jQuery(document).ready(function(){
        	tb_init('a#thumb_<?php echo $idField ?>');
        	jQuery('a#thumb_<?php echo $idField ?>').click( function(){
        	  window.mf_field_id = jQuery(this).attr('id');
        	});
        });
  			</script>
  			<?php
  			endif;
  			?>
  		</div>

  		<div style="clear: both; height: 1px;"> </div>
  			<?php if ($customField->required_field){ ?>
  				<div class="mf_message_error"><label for="<?php echo $inputName?>" class="error_magicfields error"><?php _e("This field is required",$mf_domain)?></label></div>
  			<?php
  			} ?>

  		<?php
  	}
  
	
	

  function CreateAttributesBox() {
    global $mf_domain;
  
    add_meta_box('mfattributespage', __('Magic Fields Attributes',$mf_domain), array('RCCWP_WritePostPage','attributesBoxContentPage'), 'page', 'side', 'core');
    add_meta_box('mfattributespost', __('Magic Fields Attributes',$mf_domain), array('RCCWP_WritePostPage','attributesBoxContentPost'), 'post', 'side', 'core');
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
    		  $items[$panel->id] = "{ panel_theme: '".$panel_theme."', template_name: '".addslashes($templates_by_filename[$panel_theme])."', parent_page: '".$parent_page."', parent_page_title: '".addslashes($parent_page_title)."' }";

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
