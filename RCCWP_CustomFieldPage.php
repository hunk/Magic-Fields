<?php

class RCCWP_CustomFieldPage{
	
	function Edit(){
		
		global $FIELD_TYPES;
		global $mf_domain;
		$custom_field = RCCWP_CustomField::Get((int)$_GET['custom-field-id']);
		$customGroupID = $custom_field->group_id;	
		
		if (isset($customGroupID)) {
      $group = RCCWP_CustomGroup::Get($customGroupID);
      
      ?>
      
      <script type="text/javascript">
      
      var mf_create_field = false;
        
      var mf_group_info = {
        'name' : '<?php echo stripslashes($group->name) ?>',
        'safe_name' : '<?php echo sanitize_title_with_dashes($group->name) ?>',
        'singular_safe_name' : '<?php echo sanitize_title_with_dashes(Inflect::singularize($group->name)) ?>'
      };
      
      </script>
      
      <?php
    }
    
		if (in_array($custom_field->type, array('Image'))) $cssVlaue = $custom_field->CSS;
		
  		?>
	  	
  		<div class="wrap">
  		<h2><?php _e('Edit Custom Field',$mf_domain); ?> - <em><?php echo $custom_field->description ?></em> <?php if ($group && $group->name != "__default") { _e("In Group", $mf_domain); echo " <em>".$group->name."</em>"; } ?></h2>
  		
  		<br class="clear" />
  		<?php
		if (isset($_GET['err_msg'])) :
			switch ($_GET['err_msg']){
				case -1:
				?>
				<div class="error"><p> <?php _e('A field with the same name already exists in this write panel. Please choose a different name.',$mf_domain); ?></p></div>
				<?php
				}
		endif;
		?>
  		
	  	
  		<form action="<?php echo RCCWP_ManagementPage::GetCustomWritePanelGenericUrl('submit-edit-custom-field')."&custom-group-id=$customGroupID"?>" method="post" id="edit-custom-field-form"  onsubmit="return checkEmpty();">
  		<input type="hidden" name="custom-field-id" value="<?php echo $custom_field->id?>">
		
		
		<table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="6">
		<tbody>
		<tr valign="top">
			<th scope="row"><?php _e('Label',$mf_domain); ?>:</th>
			<td><input name="custom-field-description" id="custom-field-description" size="40" type="text" value="<?php echo htmlspecialchars($custom_field->description)?>" /></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e('Name',$mf_domain); ?>:</th>
			<td><input name="custom-field-name" id="custom-field-name" size="40" type="text" value="<?php echo htmlspecialchars($custom_field->name)?>" /><button id="bt-custom-field-name-suggest" class="button">Suggest</button></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e('Help text',$mf_domain); ?>:</th>
			<td><input name="custom-field-helptext" id="custom-field-helptext" size="40" type="text" value="<?php echo htmlspecialchars($custom_field->help_text)?>" /><br/><small><?php _e('If set, this will be displayed in a tooltip next to the field label',$mf_domain); ?></small></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e('Can be duplicated',$mf_domain); ?>:</th>
			<td><input name="custom-field-duplicate" id="custom-field-duplicate" type="checkbox" value="1" <?php echo $custom_field->duplicate==0 ? "":"checked" ?>/></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e('Order',$mf_domain); ?>:</th>
			<td>
				<input name="custom-field-order" id="custom-field-order" size="2" type="text" value="<?php echo $custom_field->display_order?>" />
			</td>	
		</tr>
		<?php if (in_array($custom_field->type_id, 
							array(  $FIELD_TYPES['textbox'],
									$FIELD_TYPES['multiline_textbox'],
									$FIELD_TYPES['checkbox'],
									$FIELD_TYPES['checkbox_list'],
									$FIELD_TYPES['radiobutton_list'],
									$FIELD_TYPES['dropdown_list'],
									$FIELD_TYPES['listbox'],
									$FIELD_TYPES['file'],
									$FIELD_TYPES['image'],
									$FIELD_TYPES['audio'],
									$FIELD_TYPES['related_type'],
									$FIELD_TYPES['Image (Upload Media)'],
									$FIELD_TYPES['markdown_textbox']
							))){  ?>
		<tr valign="top">
			<th scope="row"><?php _e('Required',$mf_domain); ?>:</th>
			<td>
				<select name="custom-field-required" id="custom-field-required">
					<option value="0" <?php echo ($custom_field->required_field == 0 ? 'selected="selected"' : ''); ?> ><?php _e('Not Required - can be empty',$mf_domain); ?></option>
					<option value="1" <?php echo ($custom_field->required_field == 1 ? 'selected="selected"' : ''); ?> ><?php _e('Required - can not be empty',$mf_domain); ?></option>
				</select>
			</td>	
		</tr>

		
		<?php } ?>
		<?php if (in_array($custom_field->type, array('Textbox', 'Listbox'))) : ?>
		<tr valign="top">
			<th scope="row"><?php _e('Size',$mf_domain); ?>:</th>
			<td><input type="text" name="custom-field-size" id="custom-field-size" size="2" value="<?php echo $custom_field->properties['size']?>" /></td>
		</tr>	
		<?php endif; ?>

		<?php if (in_array($custom_field->type, array('Multiline Textbox'))) : ?>
		<tr valign="top">
			<th scope="row"><?php _e('Height',$mf_domain); ?>:</th>
			<td><input type="text" name="custom-field-height" id="custom-field-height" size="2" value="<?php echo $custom_field->properties['height']?>" /></td>
		</tr>	
		<tr valign="top">
			<th scope="row"><?php _e('Width',$mf_domain); ?>:</th>
			<td><input type="text" name="custom-field-width" id="custom-field-width" size="2" value="<?php echo $custom_field->properties['width']?>" /></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e('Hide Visual Editor for this field', $mf_domain); ?>:</th>
			<td><input name="hide-visual-editor" id="hide-visual-editor" value="1" type="checkbox" <?php echo $custom_field->properties['hide-visual-editor']==0 ? "":"checked" ?> ></td>
		</tr>	
		<?php endif; ?>
		<?php if (in_array($custom_field->type_id, 
							array(  $FIELD_TYPES['textbox'],
									$FIELD_TYPES['multiline_textbox']
							))){  ?>
		<tr valign="top">
			<th scope="row"><?php _e('Evaluate Max Length',$mf_domain); ?>:</th>
			<td><input name="strict-max-length" id="strict-max-length" value="1" type="checkbox" <?php echo $custom_field->properties['strict-max-length']==0 ? "":"checked" ?> ><br/><small><?php _e('If set, Hide Visual Editor for this field',$mf_domain); ?></small></td>
		</tr>
		<?php } ?>

		<?php if (in_array($custom_field->type, array('Date'))) : ?>
		<tr valign="top">
			<th scope="row"><?php _e('Format',$mf_domain); ?>:</th>
			<td>
				<select name="custom-field-date-format" id="custom-field-date-format">
					<option value="m/d/Y" <?php if ($custom_field->properties['format'] == "m/d/Y" ) echo " selected ";?>>4/20/2008</option>
					<option value="l, F d, Y" <?php if ($custom_field->properties['format'] == "l, F d, Y" ) echo " selected ";?>>Sunday, April 20, 2008</option>
					<option value="F d, Y" <?php if ($custom_field->properties['format'] == "F d, Y" ) echo " selected ";?>>April 20, 2008</option>
					<option value="m/d/y" <?php if ($custom_field->properties['format'] == "m/d/y" ) echo " selected ";?>>4/20/08</option>
					<option value="Y-m-d" <?php if ($custom_field->properties['format'] == "Y-m-d" ) echo " selected ";?>>2008-04-20</option>
					<option value="d-M-y" <?php if ($custom_field->properties['format'] == "d-M-y" ) echo " selected ";?>>20-Apr-08</option>
					<option value="m.d.Y" <?php if ($custom_field->properties['format'] == "m.d.Y" ) echo " selected ";?>>4.20.2008</option>
					<option value="m.d.y" <?php if ($custom_field->properties['format'] == "m.d.y" ) echo " selected ";?>>4.20.08</option>
				</select>
			</td>
		</tr>	
		<?php endif; ?>
		
		<?php if (in_array($custom_field->type, array('Slider'))) : ?>	
		<tr valign="top">
			<th scope="row"><?php echo _e('Value min', $mf_domain)?>:</th>
			<td><input type="text" name="custom-field-slider-min" id="custom-field-slider-min" size="2" value="<?php echo $custom_field->properties['min']?>" /></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php echo _e('Value max', $mf_domain)?>:</th>
			<td><input type="text" name="custom-field-slider-max" id="custom-field-slider-max" size="2" value="<?php echo $custom_field->properties['max']?>" /></td>
		</tr>		
		<tr valign="top">
			<th scope="row"><?php echo _e('Stepping', $mf_domain)?>:</th>
			<td><input type="text" name="custom-field-slider-step" id="custom-field-slider-step" size="2" value="<?php echo $custom_field->properties['step']?>" /></td>
		</tr>
		<?php endif; ?>

		<?php 
		//eeble
		if (in_array($custom_field->type, array('Related Type'))) :
			$customWritePanels = RCCWP_CustomWritePanel::GetCustomWritePanels();
		?>
		<tr valign="top">
			<th scope="row"><?php _e('Related Type Panel', $mf_domain); ?>:</th>
			<td><select name="custom-field-related-type-panel-id" id="custom-field-related-type-panel-id">
                                <option value="-7" <?php if ($custom_field->properties['panel_id']== -7) echo 'selected' ?> >All Categories</option>
				<option value="-6" <?php if ($custom_field->properties['panel_id']== -6) echo 'selected' ?> >All Posts and Pages</option>
				<option value="-5" <?php if ($custom_field->properties['panel_id']== -5) echo 'selected' ?> >All Posts and Pages with Write Panel</option>
				<option value="-4" <?php if ($custom_field->properties['panel_id']== -4) echo 'selected' ?> >All Post</option>
				<option value="-3" <?php if ($custom_field->properties['panel_id']== -3) echo 'selected' ?> >All Page</option>
				<option value="-2" <?php if ($custom_field->properties['panel_id']== -2) echo 'selected' ?> >All Post with Write Panel</option>
				<option value="-1" <?php if ($custom_field->properties['panel_id']== -1) echo 'selected' ?> >All Page with Write Panel</option>
				<?php foreach ($customWritePanels as $panel): ?>
					<option value="<?php echo $panel->id ?>" <?php if ($custom_field->properties['panel_id']==$panel->id) echo 'selected' ?>><?php echo $panel->name ?></option>
				<?php endforeach; ?>
			</select></td>
		</tr>
		<?php endif; ?>

		<?php
		if ($custom_field->has_options == "true") :
			$options = implode("\n", (array)$custom_field->options);
		?>
		<tr valign="top">
			<th scope="row"><?php _e('Options',$mf_domain); ?>:</th>
			<td>
				<textarea name="custom-field-options" id="custom-field-options" rows="2" cols="38"><?php echo htmlspecialchars($options)?></textarea><br />
				<em><?php _e('Separate each option with a newline.',$mf_domain); ?></em>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e('Default Value',$mf_domain); ?>:</th>
			<td>
				<?php
				$default_value = implode("\n", (array)$custom_field->default_value);
				if ($custom_field->allow_multiple_values == "true") :
				?>
				<textarea name="custom-field-default-value" id="custom-field-default-value" rows="2" cols="38"><?php echo htmlspecialchars($default_value)?></textarea><br />
				<em><?php _e('Separate each value with a newline.',$mf_domain); ?></em>
				<?php
				else:
				?>
				<input type="text" name="custom-field-default-value" id="custom-field-default-value" size="25" value="<?php echo htmlspecialchars($default_value)?>" />
				<?php
				endif;
				?>
			</td>
		</tr>
		<?php
		endif;
		?>
		<tr valign="top">
			<th scope="row"><?php _e('Type',$mf_domain); ?>:</th>
			<td>

				<!-- START :: Javascript for Image/Photo' Css Class -->
				<script type="text/javascript" language="javascript">
					submitForm = false;
					function fun(name)
					{
						if(name == "Image")
						{
							document.getElementById('divCSS').style.display = 'inline';
							document.getElementById('divLbl').style.display = 'inline';
							document.getElementById('lblHeight').style.display = 'inline';
							document.getElementById('txtHeight').style.display = 'inline';
							document.getElementById('lblWidth').style.display = 'inline';
							document.getElementById('txtWidth').style.display = 'inline';
						}
						else
						{
							document.getElementById('divCSS').style.display = 'none';
							document.getElementById('divLbl').style.display = 'none';
							document.getElementById('lblHeight').style.display = 'none';
							document.getElementById('txtHeight').style.display = 'none';
							document.getElementById('lblWidth').style.display = 'none';
							document.getElementById('txtWidth').style.display = 'none';
						}
					}
					function checkEmpty()
					{
						if (submitForm && (document.getElementById('custom-field-name').value == "" || document.getElementById('custom-field-description').value == "")){
							alert("<?php _e('Please fill in the name and the label of the field',$mf_domain); ?>");	
							return false;
						}
						return true;
						
					}
				</script>
				<!-- END :: Javascript for Image/Photo' Css Class -->

				<?php
				$field_types = RCCWP_CustomField::GetCustomFieldTypes();
				foreach ($field_types as $field) :
					$checked = 
						$field->name == $custom_field->type ?
						'checked="checked"' : '';
				?>
					<label><input name="custom-field-type" value="<?php echo $field->id?>" type="radio" <?php echo $checked?> onclick='fun("<?php echo $field->name?>");'/>
					<?php echo $field->name?></label><br />
				<?php
				endforeach;
				?>
			</td>
		</tr>
		<!-- START :: For Image/Photo' Css -->
		<?php
		  if ( $custom_field->type == "Image" || $custom_field->type == "Image (Upload Media)" ){
		    $h = $w = $c = NULL; 
		    
		    if( $custom_field->type == "Image")
			    $isDisplay = $custom_field->type == "Image" ? 'display:inline;' : 'display:none;';

		    if( $custom_field->type == "Image (Upload Media)")
		      $isDisplay = $custom_field->type == "Image (Upload Media)" ? 'display:inline;' : 'display:none;';
		    			  
			  if( isset($custom_field->properties['params']) ){
			    preg_match('/w\=[0-9]+/',$custom_field->properties['params'],$match_w);
			    if($match_w){
				    $w=str_replace("w=",'',$match_w[0]);
				    $custom_field->properties['params']= str_replace("&".$match_w[0],"",$custom_field->properties['params']);
			    }
			
			    preg_match('/h\=[0-9]+/',$custom_field->properties['params'],$match_h);
			    if($match_h){
				    $h=str_replace("h=",'',$match_h[0]);
				    $custom_field->properties['params']= str_replace("&".$match_h[0],"",$custom_field->properties['params']);
			    }
			
			    if($custom_field->properties['params']){
				    if (substr($custom_field->properties['params'],0 ,1) == "&"){
					    $c = substr($custom_field->properties['params'], 1);
				    }
			    }
		  }
			
			  $cssVlaue = $custom_field->CSS;
		  
		?>
		<tr valign="top">
			<th scope="row"><span id="lblHeight" style="<?php echo $isDisplay;?>"><?php _e('Max Height',$mf_domain); ?>:</span></th>
			<td><span id="txtHeight" style="<?php echo $isDisplay;?>"><input type="text" name="custom-field-photo-height" id="custom-field-photo-height" size="3" value="<?php echo $h; ?>" /></span></td>
		</tr>	
		<tr valign="top">
			<th scope="row"><span id="lblWidth" style="<?php echo $isDisplay;?>"><?php _e('Max Width',$mf_domain); ?>:</span></th>
			<td><span id="txtWidth" style="<?php echo $isDisplay;?>"><input type="text" name="custom-field-photo-width" id="custom-field-photo-width" size="3" value="<?php echo $w; ?>" /></span></td>
		</tr>
		<tr valign="top">
			<th scope="row"><span id="lblWidth" style="<?php echo $isDisplay;?>"><?php _e('Custom',$mf_domain); ?>:</span></th>
			<td><span id="txtWidth" style="<?php echo $isDisplay;?>"><input type="text" name="custom-field-custom-params" id="custom-field-custom-params" value="<?php echo $c; ?>" /></span>
		
		</td>
		</tr>
		
		<tr valign="top">
			<th scope="row"><div id="divLbl" style="<?php echo $isDisplay;?>"><?php _e('Css Class',$mf_domain); ?>:</div></th>
			<td>
				<div id="divCSS" style="<?php echo $isDisplay;?>">
				<input name="custom-field-css" id="custom-field-css" size="40" type="text" value="<?php echo $cssVlaue?>" />
				</div>
			</td>
		</tr>
    <?php } ?>
		<!-- END :: For Image/Photo' Css -->		
		</tbody>
		</table>
		
		<input name="mf_action" type="hidden" value="submit-edit-custom-field" />
  		<p class="submit" >
  			<a style="color:black" href="<?php echo RCCWP_ManagementPage::GetCustomWritePanelGenericUrl('cancel-create-custom-field')."&custom-group-id=$customGroupID"?>" class="button"><?php _e('Cancel',$mf_domain); ?></a> 
  			<input type="submit" id="submit-edit-custom-field" value="<?php _e('Update',$mf_domain); ?>" onclick="submitForm=true;" />
  		</p>
	  	
  		</form>
	  	
  		</div>
	  	
  		<?php
	}
}
