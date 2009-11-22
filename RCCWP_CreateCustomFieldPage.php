<?php

include_once('RCCWP_CustomField.php');

class RCCWP_CreateCustomFieldPage
{
	function Main()
	{
		global $FIELD_TYPES,$mf_domain;
		$customGroupID = $_REQUEST['custom-group-id'];
		?>
  	
  		<div class="wrap">
	  	
  		<h2><?php _e("Create Custom Field", $mf_domain); ?></h2>
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
			<th scope="row"><?php _e("Name", $mf_domain); ?>:</th> 
			<td>  
				<input name="custom-field-name" id="custom-field-name" size="40" type="text" />
				<input type="hidden" id="custom-field-name_hidden" name="custom-field-name_hidden" onchange="copyField();">
				<p>
					<?php _e('Type a unique name for the field, the name must be unique among all fields 
					in this panel. The name of the field is the key by which you can retrieve 
					the field value later.',$mf_domain);?>
					
				</p>
			</td>
		</tr>

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
			<th scope="row"><?php _e('Help text',$mf_domain); ?>:</th>
			<td>
				<input name="custom-field-helptext" id="custom-field-helptext" size="40" type="text" /><br/><small><?php _e('If set, this will be displayed in a tooltip next to the field label', $mf_domain); ?></small></td>
		</tr>
		
		<tr valign="top">
			<th scope="row"><?php _e("Can be duplicated", $mf_domain); ?>:</th>
			<td><input name="custom-field-duplicate" id="custom-field-duplicate" type="checkbox" value="1" <?php echo $custom_field->duplicate==1 ? "checked":"" ?>/></td>
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
						if(name == "Image")
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
		?>
		
		<div class="wrap">
		
		<h2><?php _e("Create Custom Field", $mf_domain);?></h2>
		
		<form action="<?php echo RCCWP_ManagementPage::GetCustomWritePanelGenericUrl('finish-create-custom-field')?>" method="post" id="continue-create-new-field-form">
		
		<input type="hidden" name="custom-group-id" 	value="<?php echo $_POST['custom-group-id']?>" />
		<input type="hidden" name="custom-field-name" 		value="<?php echo htmlspecialchars($_POST['custom-field-name'])?>" />
		<input type="hidden" name="custom-field-description" 	value="<?php echo htmlspecialchars($_POST['custom-field-description'])?>" />
		<input type="hidden" name="custom-field-duplicate" value="<?php echo htmlspecialchars($_POST['custom-field-duplicate'])?>" />
		<input type="hidden" name="custom-field-order" 		value="<?php echo $_POST['custom-field-order']?>" />
		<input type="hidden" name="custom-field-required" 		value="<?php echo $_POST['custom-field-required']?>" />
		<input type="hidden" name="custom-field-type" 		value="<?php echo $_POST['custom-field-type']?>" />
		<input type="hidden" name="custom-field-helptext" 		value="<?php echo $_POST['custom-field-helptext']?>" />

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


		<?php if( $current_field->has_properties && $current_field->name == 'Image' ) : ?>
		<tr valign="top">
			<th scope="row"><?php _e('Options', $mf_domain);?>:</th>
			<td>
				<?php _e('Max Height', $mf_domain);?>: <input type="text" name="custom-field-photo-height" id="custom-field-photo-height"/>
				<?php _e('Max Width', $mf_domain);?>: <input type="text" name="custom-field-photo-width" id="custom-field-photo-width" />
				<?php _e('Custom', $mf_domain);?>: <input type="text" name="custom-field-custom-params" id="custom-field-custom-params" />
				<div style="color:blue;text-decoration:underline;"
					onclick="div=document.getElementById('params');div.style.display='';"
					>
					<?php _e('Custom Options List', $mf_domain);?>
				</div>
				<div id="params"
					style="display:none;"
					onclick="this.style.display='none';">
					<pre><?php echo param_list();  ?></pre>
				</div>
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

function param_list()
{
	return ' 
	 src = filename of source image
	 new = create new image, not thumbnail of existing image.
	       Requires "w" and "h" parameters set.
	       [ex: &new=FF0000|75] - red background, 75% opacity
	       Set to hex color string of background. Opacity is
	       optional (defaults to 100% opaque).
	   w = max width of output thumbnail in pixels
	   h = max height of output thumbnail in pixels
	  wp = max width for portrait images
	  hp = max height for portrait images
	  wl = max width for landscape images
	  hl = max height for landscape images
	  ws = max width for square images
	  hs = max height for square images
	   f = output image format ("jpeg", "png", or "gif")
	   q = JPEG compression (1=worst, 95=best, 75=default)
	  sx = left side of source rectangle (default = 0)
	       (values 0 &lt; sx &lt; 1 represent percentage)
	  sy = top side of source rectangle (default = 0)
	       (values 0 &lt; sy &lt; 1 represent percentage)
	  sw = width of source rectangle (default = fullwidth)
	       (values 0 &lt; sw &lt; 1 represent percentage)
	  sh = height of source rectangle (default = fullheight)
	       (values 0 &lt; sh &lt; 1 represent percentage)
	  zc = zoom-crop. Will auto-crop off the larger dimension
	       so that the image will fill the smaller dimension
	       (requires both "w" and "h", overrides "iar", "far")
	       Set to "1" or "C" to zoom-crop towards the center,
	       or set to "T", "B", "L", "R", "TL", "TR", "BL", "BR"
	       to gravitate towards top/left/bottom/right directions
	       (requies ImageMagick for values other than "C" or "1")
	  bg = background hex color (default = FFFFFF)
	  bc = border hex color (default = 000000)
	fltr = filter system. Call as an array as follows:
	       - "brit" (Brightness) [ex: &fltr[]=brit|&lt;value]
	         where &lt;value is the amount +/- to adjust brightness
	         (range -255 to 255)
	         Availble in PHP5 with bundled GD only.
	       - "cont" (Constrast) [ex: &fltr[]=cont|&lt;value&gt;]
	         where &lt;value is the amount +/- to adjust contrast
	         (range -255 to 255)
	         Availble in PHP5 with bundled GD only.
	       - "gam" (Gamma Correction) [ex:
	         &fltr[]=gam|&lt;value&gt;]
	         where &lt;value&gt; can be a number &gt;0 to 10+ (default 1.0)
	         Must be &gt;0 (zero gives no effect). There is no max,
	         although beyond 10 is pretty useless. Negative
	         numbers actually do something, maybe not quite the
	         desired effect, but interesting nonetheless.
	       - "sat" (SATuration) [ex: &fltr[]=sat|&lt;value&gt;]
	         where &lt;value&gt; is a number between zero (no change)
	         and -100 (complete desaturation = grayscale), or it
	         can be any positive number for increased saturation.
	       - "ds" (DeSaturate) [ex: &fltr[]=ds|&lt;value&gt;]
	         is an alias for "sat" except values are inverted
	         (positive values remove color, negative values boost
	         saturation)
	       - "gray" (Grayscale) [ex: &fltr[]=gray]
	         remove all color from image, make it grayscale
	       - "th" (Threshold) [ex: &fltr[]=th|&lt;value&gt;]
	         makes image greyscale, then sets all pixels brighter
	         than &lt;value&gt; (range 0-255) to white, and all pixels
	         darker than &lt;value&gt; to black
	       - "rcd" (Reduce Color Depth) [ex:
	         &fltr[]=rcd|&lt;c&gt;|&lt;d&gt;]
	         where &lt;c&gt; is the number of colors (2-256) you want
	         in the output image, and &lt;d&gt; is "1" for dithering
	         (deault) or "0" for no dithering
	       - "clr" (Colorize) [ex:
	         &fltr[]=clr|&lt;value&gt;|&lt;color&gt;]
	         where &lt;value&gt; is a number between 0 and 100 for the
	         amount of colorization, and &lt;color&gt; is the hex color
	         to colorize to.
	       - "sep" (Sepia) [ex:
	         &fltr[]=sep|&lt;value&gt;|&lt;color&gt;]
	         where &lt;value&gt; is a number between 0 and 100 for the
	         amount of colorization (default=50), and &lt;color&gt; is
	         the hex color to colorize to (default=A28065).
	         Note: this behaves differently when applied by
	         ImageMagick, in which case 80 is default, and lower
	         values give brighter/yellower images and higher
	         values give darker/bluer images
	       - "usm" (UnSharpMask) [ex:
	         &fltr[]=usm|&lt;a&gt;|&lt;r&gt;|&lt;t&gt;]
	         where &lt;a&gt; is the amount (default = 80), &lt;r&gt; is the
	         radius (default = 0.5), &lt;t&gt; is the threshold
	         (default = 3).
	       - "blur" (Blur) [ex: &fltr[]=blur|&lt;radius&gt;]
	         where (0 &lt; &lt;radius&gt; &lt; 25) (default = 1)
	       - "gblr" (Gaussian Blur) [ex: &fltr[]=gblr]
	         Availble in PHP5 with bundled GD only.
	       - "sblr" (Selective Blur) [ex: &fltr[]=gblr]
	         Availble in PHP5 with bundled GD only.
	       - "smth" (Smooth) [ex: &fltr[]=smth|&lt;value&gt;]
	         where &lt;value&gt; is the weighting value for the matrix
	         (range -10 to 10, default 6)
	         Availble in PHP5 with bundled GD only.
	       - "lvl" (Levels)
	         [ex: &fltr[]=lvl|&lt;channel&gt;|&lt;method&gt;|&lt;threshol&gt;d
	         where &lt;channel&gt; can be one of "r", "g", "b", "a" (for
	         Red, Green, Blue, Alpha respectively), or "*" for all
	         RGB channels (default) based on grayscale average.
	         ImageMagick methods can support multiple channels
	         (eg "lvl|rg|3") but internal methods cannot (they will
	         use first character of channel string as channel)
	         &lt;method&gt; can be one of:
	         0=Internal RGB;
	         1=Internal Grayscale;
	         2=ImageMagick Contrast-Stretch (default)
	         3=ImageMagick Normalize (may appear over-saturated)
	         &lt;threshold&gt; is how much of brightest/darkest pixels
	         will be clipped in percent (default = 0.1%)
	         Using default parameters (&fltr[]=lvl) is similar to
	         Auto Contrast in Adobe Photoshop.
	       - "wb" (White Balance) [ex: &fltr[]=wb|&lt;c&gt;]
	         where &lt;c&gt; is the target hex color to white balance
	         on, this color is what "should be" white, or light
	         gray. The filter attempts to maintain brightness so
	         any gray color can theoretically be used. If &lt;c&gt; is
	         omitted the filter guesses based on brightest pixels
	         in each of RGB
	         OR &lt;c&gt; can be the percent of white clipping used
	         to calculate auto-white-balance (default = 0.1%)
	         NOTE: "wb" in default settings already gives an effect
	         similar to "lvl", there is usually no need to use "lvl"
	         if "wb" is already used.
	       - "hist" (Histogram)
	         [ex: &fltr[]=hist|&lt;b&gt;|&lt;c&gt;|&lt;w&gt;|&lt;h&gt;|&lt;a&gt;|&lt;o&gt;|&lt;x&gt;|&lt;y&gt;]
	         Where &lt;b&gt; is the color band(s) to display, from back
	         to front (one or more of "rgba*" for Red Green Blue
	         Alpha and Grayscale respectively);
	         &lt;c&gt; is a semicolon-seperated list of hex colors to
	         use for each graph band (defaults to FF0000, 00FF00,
	         0000FF, 999999, FFFFFF respectively);
	         &lt;w&gt; and &lt;h&gt; are the width and height of the overlaid
	         histogram in pixels, or if &lt;= 1 then percentage of
	         source image width/height;
	         &lt;a&gt; is the alignment (same as for "wmi" and "wmt");
	         &lt;o&gt; is opacity from 0 (transparent) to 100 (opaque)
	             (requires PHP v4.3.2, otherwise 100% opaque);
	         &lt;x&gt; and &lt;y&gt; are the edge margin in pixels (or percent
	             if 0 &lt; (x|y) &lt; 1)
	       - "over" (OVERlay/underlay image) overlays an image on
	         the thumbnail, or overlays the thumbnail on another
	         image (to create a picture frame for example)
	         [ex: &fltr[]=over|&lt;i&gt;|&lt;u&gt;|&lt;m&gt;|&lt;o&gt;]
	         where &lt;i&gt; is the image filename; &lt;u&gt; is "0" (default)
	         for overlay the image on top of the thumbnail or "1"
	         for overlay the thumbnail on top of the image; &lt;m&gt; is
	         the margin - can be absolute pixels, or if &lt; 1 is a
	         percentage of the thumbnail size [must be &lt; 0.5]
	         (default is 0 for overlay and 10% for underlay);
	         &lt;o&gt; is opacity (0 = transparent, 100 = opaque)
	             (requires PHP v4.3.2, otherwise 100% opaque);
	         (thanks raynerape�gmail*com, shabazz3�msu*edu)
	       - "wmi" (WaterMarkImage)
	         [ex: &fltr[]=wmi|&lt;f&gt;|&lt;a&gt;|&lt;o&gt;|&lt;x&gt;|&lt;y&gt;|&lt;r&gt;] where
	         &lt;f&gt; is the filename of the image to overlay;
	         &lt;a&gt; is the alignment (one of BR, BL, TR, TL, C,
	             R, L, T, B, *) where B=bottom, T=top, L=left,
	             R=right, C=centre, *=tile)
	             *or*
	             an absolute position in pixels (from top-left
	             corner of canvas to top-left corner of overlay)
	             in format {xoffset}x{yoffset} (eg: "10x20")
	             note: this is center position of image if &lt;&gt;x
	             and &lt;y&gt; are set
	         &lt;o&gt; is opacity from 0 (transparent) to 100 (opaque)
	             (requires PHP v4.3.2, otherwise 100% opaque);
	         &lt;x&gt; and &lt;y&gt; are the edge (and inter-tile) margin in
	             pixels (or percent if 0 &lt; (x|y) &lt; 1)
	             *or*
	             if &lt;a&gt; is absolute-position format then &lt;x&gt; and
	             &lt;y&gt; represent maximum width and height that the
	             watermark image will be scaled to fit inside
	         &lt;r&gt; is rotation angle of overlaid watermark
	       - "wmt" (WaterMarkText)
	         [ex: &fltr[]=wmt|&lt;t&gt;|&lt;s&gt;|&lt;a&gt;|&lt;c&gt;|&lt;f&gt;|&lt;o&gt;|&lt;m&gt;|&lt;n&gt;|&lt;b&gt;|&lt;O&gt;|&lt;x&gt;]
	         where:
	         &lt;t&gt; is the text to use as a watermark;
	             URLencoded Unicode HTMLentities must be used for
	               characters beyond chr(127). For example, the
	               "eighth note" character (U+266A) is represented
	               as "&#9834;" and then urlencoded to "%26%239834%3B"
	             Any instance of metacharacters will be replaced
	             with their calculated value. Currently supported:
	               ^Fb = source image filesize in bytes
	               ^Fk = source image filesize in kilobytes
	               ^Fm = source image filesize in megabytes
	               ^X  = source image width in pixels
	               ^Y  = source image height in pixels
	               ^x  = thumbnail width in pixels
	               ^y  = thumbnail height in pixels
	               ^^  = the character ^
	         &lt;s&gt; is the font size (1-5 for built-in font, or point
	             size for TrueType fonts);
	         &lt;a&gt; is the alignment (one of BR, BL, TR, TL, C, R, L,
	             T, B, * where B=bottom, T=top, L=left, R=right,
	             C=centre, *=tile);
	             *or*
	             an absolute position in pixels (from top-left
	             corner of canvas to top-left corner of overlay)
	             in format {xoffset}x{yoffset} (eg: "10x20")
	         &lt;c&gt; is the hex color of the text;
	         &lt;f&gt; is the filename of the TTF file (optional, if
	             omitted a built-in font will be used);
	         &lt;o&gt; is opacity from 0 (transparent) to 100 (opaque)
	             (requires PHP v4.3.2, otherwise 100% opaque);
	         &lt;m&gt; is the edge (and inter-tile) margin in percent;
	         &lt;n&gt; is the angle
	         &lt;b&gt; is the hex color of the background;
	         &lt;O&gt; is background opacity from 0 (transparent) to
	             100 (opaque)
	             (requires PHP v4.3.2, otherwise 100% opaque);
	         &lt;x&gt; is the direction(s) in which the background is
	             extended (either "x" or "y" (or both, but both
	             will obscure entire image))
	             Note: works with TTF fonts only, not built-in
	       - "flip" [ex: &fltr[]=flip|x   or   &fltr[]=flip|y]
	         flip image on X or Y axis
	       - "ric" [ex: &fltr[]=ric|&lt;x&gt;|&lt;y&gt;]
	         rounds off the corners of the image (to transparent
	         for PNG output), where &lt;x&gt; is the horizontal radius
	         of the curve and &lt;y&gt; is the vertical radius
	       - "elip" [ex: &fltr[]=elip]
	         similar to rounded corners but more extreme
	       - "mask" [ex: &fltr[]=mask|filename.png]
	         greyscale values of mask are applied as the alpha
	         channel to the main image. White is opaque, black
	         is transparent.
	       - "bvl" (BeVeL) [ex:
	         &fltr[]=bvl|&lt;w&gt;|&lt;c1&gt;|&lt;c2&gt;]
	         where &lt;w&gt; is the bevel width, &lt;c1&gt; is the hex color
	         for the top and left shading, &lt;c2&gt; is the hex color
	         for the bottom and right shading
	       - "bord" (BORDer) [ex:
	         &fltr[]=bord|&lt;w&gt;|&lt;rx&gt;|&lt;ry&gt;|&lt;&gt;c
	         where &lt;w&gt; is the width in pixels, &lt;rx&gt;
		 and &lt;ry&gt; are
	         horizontal and vertical radii for rounded corners,
	         and &lt;c&gt; is the hex color of the border
	       - "fram" (FRAMe) draws a frame, similar to "bord" but
	         more configurable
	         [ex: &fltr[]=fram|&lt;w1&gt;|&lt;w2&gt;|&lt;c1&gt;|&lt;c2&gt;|&lt;c3&gt;]
	         where &lt;w1&gt; is the width of the main border,
		 &lt;w2&gt; is
	         the width of each side of the bevel part, &lt;c1&gt; is the
	         hex color of the main border, &lt;c2&gt; is the highlight
	         bevel color, &lt;c3&gt; is the shadow bevel color
	       - "drop" (DROP shadow)
	         [ex: &fltr[]=drop|&lt;d&gt;|&lt;w&gt;|&lt;clr&gt;|&lt;a&gt;]
	         where &lt;d&gt; is distance from image to shadow,
		 &lt;w&gt; is
	         width of shadow fade (not yet implemented),
		 &lt;clr&gt; is
	         the hex color of the shadow, and &lt;a&gt; is the angle of
	         the shadow (default=225)
	       - "crop" (CROP image)
	         [ex:
		 &fltr[]=crop|&lt;l&gt;|&lt;r&gt;|&lt;t&gt;|&lt;b&gt;]
	         where &lt;l&gt; is the number of pixels to crop from the left
	         side of the resized image; &lt;r&gt;, &lt;t&gt;,
		 &lt;b&gt; are for right,
	         top and bottom respectively. Where (0 &lt; x &lt; 1) the
	         value will be used as a percentage of width/height.
	         Left and top crops take precedence over right and
	         bottom values. Cropping will be limited such that at
	         least 1 pixel of width and height always remains.
	       - "rot" (ROTate)
	         [ex: &fltr[]=rot|&lt;a&gt;|&lt;b&gt;]
	         where &lt;a&gt; is the rotation angle in degrees;
		 &lt;b&gt; is the
	         background hex color. Similar to regular "ra" parameter
	         but is applied in filter order after regular processing
	         so you can rotate output of other filters.
	       - "size" (reSIZE)
	         [ex: &fltr[]=size|&lt;x&gt;|&lt;y&gt;|&lt;s&gt;]
	         where &lt;x&gt; is the horizontal dimension in pixels,
		 &lt;y&gt; is
	         the vertical dimension in pixels, &lt;s&gt; is boolean whether
	         to stretch (if 1) or resize proportionately (0, default)
	         &lt;x&gt; and &lt;y&gt; will be interpreted as percentage of current
	         output image size if values are (0 &lt; X &lt; 1)
	         NOTE: do NOT use this filter unless absolutely neccesary.
	         It is only provided for cases where other filters need to
	         have absolute positioning based on source image and the
	         resultant image should be resized after other filters are
	         applied. This filter is less efficient than the standard
	         resizing procedures.
	       - "stc" (Source Transparent Color)
	         [ex: &fltr[]=stc|&lt;c&gt;|&lt;n&gt;|<x&gt;]
	         where <c&gt; is the hex color of the target color to be made
	         transparent; <n&gt; is the minimum threshold in percent (all
	         pixels within <n&gt;% of the target color will be 100%
	         transparent, default <n&gt;=5); <x&gt; is the maximum threshold
	         in percent (all pixels more than <x&gt;% from the target
	         color will be 100% opaque, default <x&gt;=10); pixels between
	         the two thresholds will be partially transparent.
	md5s = MD5 hash of the source image -- if this parameter is
	       passed with the hash of the source image then the
	       source image is not checked for existance or
	       modification and the cached file is used (if
	       available). If "md5s" is passed an empty string then
	       phpThumb.php dies and outputs the correct MD5 hash
	       value.  This parameter is the single-file equivalent
	       of "cache_source_filemtime_ignore_*" configuration
	       paramters
	 xto = EXIF Thumbnail Only - set to only extract EXIF
	       thumbnail and not do any additional processing
	  ra = Rotate by Angle: angle of rotation in degrees
	       positive = counterclockwise, negative = clockwise
	  ar = Auto Rotate: set to "x" to use EXIF orientation
	       stored by camera. Can also be set to "l" or "L"
	       for landscape, or "p" or "P" for portrait. "l"
	       and "P" rotate the image clockwise, "L" and "p"
	       rotate the image counter-clockwise.
	 sfn = Source Frame Number - use this frame/page number for
	       multi-frame/multi-page source images (GIF, TIFF, etc)
	 aoe = Output Allow Enlarging - override the setting for
	       $CONFIG["output_allow_enlarging"] (1=on, 0=off)
	       ("far" and "iar" both override this and allow output
	       larger than input)
	 iar = Ignore Aspect Ratio - disable proportional resizing
	       and stretch image to fit "h" & "w" (which must both
	       be set).  (1=on, 0=off)  (overrides "far")
	 far = Force Aspect Ratio - image will be created at size
	       specified by "w" and "h" (which must both be set).
	       Alignment: L=left,R=right,T=top,B=bottom,C=center
	       BL,BR,TL,TR use the appropriate direction if the
	       image is landscape or portrait.
	 dpi = Dots Per Inch - input DPI setting when importing from
	       vector image format such as PDF, WMF, etc
	 sia = Save Image As - default filename to save generated
	       image as. Specify the base filename, the extension
	       (eg: ".png") will be automatically added
	maxb = MAXimum Byte size - output quality is auto-set to
	       fit thumbnail into "maxb" bytes  (compression
	       quality is adjusted for JPEG, bit depth is adjusted
	       for PNG and GIF)
	down = filename to save image to. If this is set the
	       browser will prompt to save to this filename rather
	       than display the image
	
		';
}
