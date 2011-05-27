<?php

include_once('RCCWP_CustomField.php');

class RCCWP_CreateCustomFieldPage
{
	function Main()
	{
		global $FIELD_TYPES,$mf_domain;
		$customGroupID = $_REQUEST['custom-group-id'];

    if (isset($customGroupID)) {
      $group = RCCWP_CustomGroup::Get($customGroupID);
      
      ?>
      
      <script type="text/javascript">
        
      var mf_create_field = true;

      var mf_group_info = {
        'name' : '<?php echo stripslashes($group->name) ?>',
        'safe_name' : '<?php echo sanitize_title_with_dashes($group->name) ?>',
        'singular_safe_name' : '<?php echo sanitize_title_with_dashes(Inflect::singularize($group->name)) ?>'
      };
      
      </script>
      
      <?php
    }
    
		?>
  	
  	  
  		<div class="wrap">
	  	
  		<h2><?php _e("Create Custom Field", $mf_domain); ?> <?php if ($group && $group->name != "__default") { _e("In Group", $mf_domain); echo " <em>".$group->name."</em>"; } ?></h2>
  		<br class="clear" />
  		<?php
		if (isset($_GET['err_msg'])) :
			switch ($_GET['err_msg']){
				case -1:
				?>
				<div class="error"><p> <?php _e('A field with the same name already exists in this write panel. Please choose a different name.',$mf_domain);?></p></div>
				<?php
				}
		endif;
		?>
  			
  	<form action="<?php echo RCCWP_ManagementPage::GetCustomWritePanelGenericUrl('continue-create-custom-field')?>" method="post" name="create_custom_field_form" id="create-custom-field-form" onsubmit="return checkEmpty();" autocomplete="off">

		<?php if(isset($_GET['custom-group-id']) && !empty($_GET['custom-group-id'])) { ?>
  			<input type="hidden" name="custom-group-id" value="<?php echo $_GET['custom-group-id']?>">
		<?php } ?>
		<?php if(isset($_POST['custom-group-id']) && !empty($_POST['custom-group-id'])) { ?>
  			<input type="hidden" name="custom-group-id" value="<?php echo $_POST['custom-group-id']?>">
		<?php } ?>
		
		
		<table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="6">
		<tbody>

		<tr valign="top">
			<th scope="row"><?php _e("Label", $mf_domain); ?>:</th>
			<td>
				<input name="custom-field-description" id="custom-field-description" size="40" type="text" />
				<p>
					<?php _e('Type a label for the field. The label of the field is displayed
					beside the field in Write Panel page.',$mf_domain); ?>
				</p>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row"><?php _e("Name", $mf_domain); ?>:</th> 
			<td>  
				<input name="custom-field-name" id="custom-field-name" size="40" type="text" />
				<input type="hidden" id="custom-field-name_hidden" name="custom-field-name_hidden" onchange="copyField();" />

				<p>
					<?php _e('Type a unique name for the field, the name must be unique among all fields 
					in this panel. The name of the field is the key by which you can retrieve 
					the field value later.',$mf_domain);?>
					
				</p>
			</td>
		</tr>
		
		<tr valign="top">
			<th scope="row"><?php _e('Help text',$mf_domain); ?>:</th>
			<td>
				<input name="custom-field-helptext" id="custom-field-helptext" size="40" type="text" /><br/><small><?php _e('If set, this will be displayed in a tooltip next to the field label', $mf_domain); ?></small></td>
		</tr>
		
		<tr valign="top">
			<th scope="row"><?php _e("Can be duplicated", $mf_domain); ?>:</th>
			<td><input name="custom-field-duplicate" id="custom-field-duplicate" type="checkbox" value="1" /></td>
		</tr>

		<tr valign="top">
			<th scope="row"><?php _e("Order", $mf_domain); ?>:</th>
			<td><input type="text" name="custom-field-order" id="custom-field-order" size="2" value="0" /></td>
		</tr>
		

		<tr valign="top">
			<th scope="row"><?php _e("Required", $mf_domain); ?>:</th>
			<td>
				<select name="custom-field-required" id="custom-field-required">
					<option value="0" selected="selected"><?php _e('Not Required - can be empty',$mf_domain); ?></option>
					<option value="1"><?php _e('Required - can not be empty',$mf_domain); ?></option>
				</select>
			</td>
		</tr>
				
		<tr valign="top">
			<th scope="row"><?php _e("Type", $mf_domain); ?>:</th>
			<td>

				<!-- START :: Javascript for Image/Photo' Css Class and for check -->
				<script type="text/javascript" language="javascript">
					submitForm = false;
					function fun(name)
					{
						if(name == "Image" || name == 'Image (Upload Media)')
						{
							document.getElementById('divLbl').style.display = 'inline';
							document.getElementById('divCSS').style.display = 'inline';
						}
						else
						{
							document.getElementById('divLbl').style.display = 'none';
							document.getElementById('divCSS').style.display = 'none';
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
					$checked =  $field->name == "Textbox" ? 'checked="checked"' : '';
				?>
					<label><input name="custom-field-type" value="<?php echo $field->id?>" type="radio" <?php echo $checked?> onclick='fun("<?php echo $field->name?>");' /> <!-- Calling Javascript Function -->
					<?php echo $field->name?></label><br />
				<?php
				endforeach;
				?>
			</td>
		</tr>
		<!-- START :: For Image/Photo' Css -->
		<tr valign="top">
			<th scope="row"><div id="divLbl" style="display:none"><?php _e('Css Class',$mf_domain);?>:</div></th>
			<td>
				<div id="divCSS" style="display:none">
				<input name="custom-field-css" id="custom-field-css" size="40" type="text" value="magicfields" />
				</div>
			</td>
		</tr>
		<!-- END :: For Image/Photo' Css -->
		</tbody>
		</table>
		
		
	  	<p class="submit" >
  			<a style="color:black" href="<?php echo RCCWP_ManagementPage::GetCustomWritePanelGenericUrl('cancel-create-custom-field')."&custom-group-id=$customGroupID"?>" class="button"><?php _e('Cancel',$mf_domain); ?></a>
  			<input type="submit" id="continue-create-custom-field" value='<?php _e("Continue",$mf_domain); ?>'  onclick="submitForm=true;"/>
  		</p>
	  	
  		</form>
	  	
  		</div>
	  	
  		<?php	
	}
	
	function SetOptions()
	{
		global $mf_domain;
		$current_field = RCCWP_CustomField::GetCustomFieldTypes($_POST['custom-field-type']);
		$customGroupID = $_REQUEST['custom-group-id'];
		$default = array(
		  'custom-group-id' => '',
		  'custom-field-name' => '',
		  'custom-field-description' => '',
		  'custom-field-duplicate' => '',
		  'custom-field-order' => '',
		  'custom-field-required' => '',
		  'custom-field-type' => '',
		  'custom-field-helptext' => '',
		);
		$values = array_merge($default,$_POST);
		
		?>
		
		<div class="wrap">
		
		<h2><?php _e("Create Custom Field", $mf_domain);?></h2>
		
		<form action="<?php echo RCCWP_ManagementPage::GetCustomWritePanelGenericUrl('finish-create-custom-field')?>" method="post" id="continue-create-new-field-form">
		
		<input type="hidden" name="custom-group-id" 	value="<?php echo $values['custom-group-id']?>" />
		<input type="hidden" name="custom-field-name" 		value="<?php echo htmlspecialchars($values['custom-field-name'])?>" />
		<input type="hidden" name="custom-field-description" 	value="<?php echo htmlspecialchars($values['custom-field-description'])?>" />
		<input type="hidden" name="custom-field-duplicate" value="<?php echo htmlspecialchars($values['custom-field-duplicate'])?>" />
		<input type="hidden" name="custom-field-order" 		value="<?php echo $values['custom-field-order']?>" />
		<input type="hidden" name="custom-field-required" 		value="<?php echo $values['custom-field-required']?>" />
		<input type="hidden" name="custom-field-type" 		value="<?php echo $values['custom-field-type']?>" />
		<input type="hidden" name="custom-field-helptext" 		value="<?php echo $values['custom-field-helptext']?>" />

		<!-- Hidden value for Image/Photo' Css Class-->
		<input type="hidden" name="custom-field-css" value="<?php echo $_POST['custom-field-css']?>" />
    
    
    
		<h3><?php echo $current_field->name?></h3>
		
		<table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="6">
		<tbody>
		
		<?php
		if ($current_field->has_properties == "true") :
		?>
		
		<?php 
		if (in_array($current_field->name, array('Textbox', 'Listbox'))) : 
			if ($current_field->name == 'Textbox')
				$size = 25;
			else if ($current_field->name == 'Listbox')
				$size = 3;
		?>
		<tr valign="top">
			<th scope="row"><?php _e('Size', $mf_domain); ?>:</th>
			<td><input type="text" name="custom-field-size" id="custom-field-size" size="2" value="<?php echo $size?>" /></td>
		</tr>
		<?php if ($current_field->name == 'Textbox'){  ?>
		<tr valign="top">
			<th scope="row"><?php _e('Evaluate Max Length',$mf_domain); ?>:</th>
			<td><input name="strict-max-length" id="strict-max-length" value="1" type="checkbox" ></td>
		</tr>
		<?php } ?>
		<?php endif; ?>
		
		<?php 
		if (in_array($current_field->name, array('Multiline Textbox'))) : 
			$height = 3;
			$width = 23;
		?>
		<tr valign="top">
			<th scope="row"><?php _e('Height', $mf_domain); ?>:</th>
			<td><input type="text" name="custom-field-height" id="custom-field-height" size="2" value="<?php echo $height?>" /></td>
		</tr>	
		<tr valign="top">
			<th scope="row"><?php _e('Width', $mf_domain); ?>:</th>
			<td><input type="text" name="custom-field-width" id="custom-field-width" size="2" value="<?php echo $width?>" /></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e('Hide Visual Editor for this field', $mf_domain); ?>:</th>
			<td><input name="hide-visual-editor" id="hide-visual-editor" value="1" type="checkbox"></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e('Evaluate Max Length',$mf_domain); ?>:</th>
			<td><input name="strict-max-length" id="strict-max-length" value="1" type="checkbox" ><br/><small><?php _e('If set, Hide Visual Editor for this field',$mf_domain); ?></small></td>
		</tr>
		<?php endif; ?>
		
		<?php 
		if (in_array($current_field->name, array('Slider'))) : 
			$min_val = 0;
			$max_val = 10;
			$step = 1;
		?>
		<tr valign="top">
			<th scope="row"><?php _e('Value min', $mf_domain); ?>:</th>
			<td><input type="text" name="custom-field-slider-min" id="custom-field-slider-min" size="2" value="<?php echo $min_val?>" /></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e('Value max', $mf_domain);?>:</th>
			<td><input type="text" name="custom-field-slider-max" id="custom-field-slider-max" size="2" value="<?php echo $max_val?>" /></td>
		</tr>		
		<tr valign="top">
			<th scope="row"><?php _e('Stepping', $mf_domain);?>:</th>
			<td><input type="text" name="custom-field-slider-step" id="custom-field-slider-step" size="2" value="<?php echo $step?>" /></td>
		</tr>
		<?php endif; ?>
		
		<?php
		//eeble
		if (in_array($current_field->name, array('Related Type'))) :
			$customWritePanels = RCCWP_CustomWritePanel::GetCustomWritePanels();
		?>
		<tr valign="top">
			<th scope="row"><?php _e('Related Type Panel', $mf_domain); ?>:</th>
			<td><select name="custom-field-related-type-panel-id" id="custom-field-related-type-panel-id">
                                <option value="-7">All Categories</option>
				<option value="-6">All Posts and Pages</option>
				<option value="-5">All Posts and Pages with Write Panel</option>
				<option value="-4">All Post</option>
				<option value="-3">All Page</option>
				<option value="-2">All Post with Write Panel</option>
				<option value="-1">All Page with Write Panel</option>
				<?php foreach ($customWritePanels as $panel): ?>
					<option value="<?php echo $panel->id ?>"><?php echo $panel->name ?></option>
				<?php endforeach; ?>
			</select></td>
		</tr>
		<?php endif; ?>

				
		<?php
		endif; // has_properties
		?>
		
		<?php
		if ($current_field->has_options == "true") :
		?>		
		<tr valign="top">
			<th scope="row"><?php _e('Options', $mf_domain);?>:</th>
			<td>
				<textarea name="custom-field-options" id="custom-field-options" rows="2" cols="38"></textarea><br />
				<em><?php _e('Separate each option with a newline.', $mf_domain);?></em>
			</td>
		</tr>	
		<tr valign="top">
			<th scope="row"><?php _e('Default Value', $mf_domain);?>:</th>
			<td>
				<?php
				if ($current_field->allow_multiple_values == "true") :
				?>
				<textarea name="custom-field-default-value" id="custom-field-default-value" rows="2" cols="38"></textarea><br />
				<em><?php _e('Separate each value with a newline.', $mf_domain);?></em>
				<?php
				else :
				?>				
				<input type="text" name="custom-field-default-value" id="custom-field-default-value" size="25" />
				<?php
				endif;
				?>
			</td>
		</tr>
		<?php endif; ?>


		<?php if( $current_field->has_properties && ($current_field->name == 'Image' || $current_field->name == 'Image (Upload Media)' ) ) : ?>
		<tr valign="top">
			<th scope="row"><?php _e('Options', $mf_domain);?>:</th>
			<td>
				<?php _e('Max Height', $mf_domain);?>: <input type="text" name="custom-field-photo-height" id="custom-field-photo-height"/>
				<?php _e('Max Width', $mf_domain);?>: <input type="text" name="custom-field-photo-width" id="custom-field-photo-width" />
				<?php _e('Custom', $mf_domain);?>: <input type="text" name="custom-field-custom-params" id="custom-field-custom-params" />
			</td>
		</tr>
		<?php endif; ?>

		<!-- Date Custom Field -->
		<?php if( $current_field->has_properties && $current_field->name == 'Date' ) : ?>
		<tr valign="top">
			<th scope="row"><?php _e('Options', $mf_domain);?>:</th>
			<td>
			<?php _e('Format', $mf_domain);?>:	<select name="custom-field-date-format" id="custom-field-date-format">
					<option value="m/d/Y">4/20/2008</option>
					<option value="l, F d, Y">Sunday, April 20, 2008</option>
					<option value="F d, Y">April 20, 2008</option>
					<option value="m/d/y">4/20/08</option>
					<option value="Y-m-d">2008-04-20</option>
					<option value="d-M-y">20-Apr-08</option>
					<option value="m.d.Y">4.20.2008</option>
					<option value="m.d.y">4.20.08</option>
				</select>
			</td>
		</tr>
		<?php endif; ?>
		<!-- Date Custom Field -->
		</tbody>
		</table>
		
		<p class="submit" >
			<a style="color:black" href="<?php echo RCCWP_ManagementPage::GetCustomWritePanelGenericUrl('cancel-create-custom-field')."&custom-group-id=$customGroupID"?>" class="button"><?php _e('Cancel', $mf_domain); ?></a> 
			<input type="submit" id="finish-create-custom-field" value="<?php _e('Finish', $mf_domain); ?>" />
		</p>
		</form>
		</div>
		
		<?php
	}
} //end class
