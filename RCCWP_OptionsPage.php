<?php

//loading javascript file by  the options page.
wp_enqueue_script( 'mf_options_page',
					MF_URI.'js/options.js'
				);

//loading javascript fiel by image medi
wp_enqueue_script( 'mf_media_upload_fiel',
					MF_URI.'js/custom_fields/media_image.js'
				);

include_once('RCCWP_Options.php');

class RCCWP_OptionsPage {

	function Main() {
		global $mf_domain;
		$customWritePanels = RCCWP_CustomWritePanel::GetCustomWritePanels();
		$customWritePanelOptions = RCCWP_Options::Get();

                //check dont-remove p and br
                if( !isset($customWritePanelOptions['dont-remove-tmce']) )
                  $customWritePanelOptions['dont-remove-tmce'] = 0;

                if( !isset($customWritePanelOptions['use-standard-uploader']) )
                  $customWritePanelOptions['use-standard-uploader'] = 0;

		if (function_exists('is_site_admin') && !is_site_admin()){
			update_option("Magic_Fields_notTopAdmin", true);
		}else{
			update_option("Magic_Fields_notTopAdmin", false);
		}
	?>
	<div class="wrap">

	<h2><?php _e('Magic Fields Options', $mf_domain); ?></h2>
	
	<form action="#" method="post" id="custom-write-panel-options-form">	
	
	<h3><?php _e('Write Panel Options', $mf_domain); ?></h3>
	<table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="6"> 
	
	<tr valign="top">
		<th scope="row"><?php _e('Condense Menu',$mf_domain);?></th>
		<td>
			<label for="condense-menu">
				<input name="condense-menu" id="condense-menu" value="1" <?php echo RCCWP_OptionsPage::GetCheckboxState( $customWritePanelOptions['condense-menu'] );?> type="checkbox"> &nbsp; <?php _e('This option removes the write panel from the main navigation and places them inside of the post and menu pages.');?></label>
			</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><?php _e('Hide non-standart content in Post Panel',$mf_domain);?></th>
		<td>
			<label for="hide-non-standart-content" >
			<input name="hide-non-standart-content" id="hide-non-standart-content" value="1" <?php echo RCCWP_OptionsPage::GetCheckboxState( $customWritePanelOptions['hide-non-standart-content'] );?> type="checkbox"> &nbsp; <?php _e('Hide posts made with Write panels in the edit section in the Post panel');?></label>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><?php _e('Hide Post Panel', $mf_domain); ?></th>
			<td>
			<label for="hide-write-post"> 
			<input name="hide-write-post" id="hide-write-post" value="1" <?php echo RCCWP_OptionsPage::GetCheckboxState( $customWritePanelOptions['hide-write-post'] )?> type="checkbox">
			&nbsp; <?php _e('Hide Wordpress Post panel', $mf_domain); ?></label> 
		</td>
		</tr>
 
	<tr valign="top">
		<th scope="row"><?php _e('Hide Page Panel', $mf_domain); ?></th>
		<td>
			<label for="hide-write-page"> 
			<input name="hide-write-page" id="hide-write-page" value="1" <?php echo RCCWP_OptionsPage::GetCheckboxState( $customWritePanelOptions['hide-write-page'] )?> type="checkbox">
			&nbsp; <?php _e('Hide Wordpress Page panel', $mf_domain); ?></label> 
 		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><?php _e('Hide Visual Editor (multiline)', $mf_domain); ?></th>
		<td>
			<label for="hide-visual-editor"> 
			<input name="hide-visual-editor" id="hide-visual-editor" value="1"  <?php echo RCCWP_OptionsPage::GetCheckboxState( $customWritePanelOptions['hide-visual-editor'] )?> type="checkbox">
			&nbsp; <?php _e('Hide Visual Editor (multiline)', $mf_domain); ?></label> 
 		</td>
	</tr>

        <tr valign="top">
          <th scope="row"><?php _e('Do not remove tags tmce. (multiline)', $mf_domain); ?></th>
          <td>
            <label for="dont-remove-tmce">
            <input name="dont-remove-tmce" id="dont-remove-tmce" value="1"  <?php echo RCCWP_OptionsPage::GetCheckboxState( $customWritePanelOptions['dont-remove-tmce'] )?> type="checkbox">
            &nbsp; <?php _e("Stop removing the &lt;p&gt; and &lt;br /&gt; tags when saving and show them in the HTML editor", $mf_domain); ?></label>
        </td>
</tr>

                                                                                             
  <tr valign="top">
		<th scope="row"><?php _e('Use Standard File Uploader (non-ajax)', $mf_domain); ?></th>
		<td>
			<label for="use-standard-uploader"> 
			<input name="use-standard-uploader" id="use-standard-uploader" value="1"  <?php echo RCCWP_OptionsPage::GetCheckboxState( $customWritePanelOptions['use-standard-uploader'] )?> type="checkbox">
			&nbsp; <?php _e('Try using the standard file uploader if the AJAX loader fails to upload to your server', $mf_domain); ?></label> 
 		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><?php _e('Editing Prompt', $mf_domain); ?></th>
		<td>
			<label for="prompt-editing-post"> 
			<input name="prompt-editing-post" id="prompt-editing-post" value="1" <?php echo RCCWP_OptionsPage::GetCheckboxState( $customWritePanelOptions['prompt-editing-post'] )?> type="checkbox"> 
			&nbsp; <?php _e('Prompt when editing a Post not created with Custom Write Panel.', $mf_domain); ?></label> 
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><?php _e('Assign to Role', $mf_domain); ?></th>
			<td>
			<label for="assign-to-role"> 
			<input name="assign-to-role" id="assign-to-role" value="1" <?php echo RCCWP_OptionsPage::GetCheckboxState( $customWritePanelOptions['assign-to-role'] )?> type="checkbox"> 
			&nbsp; <?php _e('This option will create a capability for each write panel such that the write panel is accessible by the Administrator only by default.
			 You can assign the write panel to other roles using ', $mf_domain); ?></label><a target="_blank" href="http://sourceforge.net/projects/role-manager">Role Manager Plugin</a>. 
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><?php _e('Default Panel', $mf_domain); ?></th>
		<td>
			<label for="default-custom-write-panel">
			<select name="default-custom-write-panel" id="default-custom-write-panel">
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
			</label>
		</td>
	</tr>

	</table>
	
	<h3><?php _e('Extra', $mf_domain); ?></h3>
	<table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="6"> 
		<tr valign="top">
			<th scope="row"><?php _e('Clear cache', $mf_domain); ?></th>
			<td>
				<label for="clear-cache-image-mf"> 
				<input name="clear-cache-image-mf" id="clear-cache-image-mf" value="1" type="checkbox">
			&nbsp; <?php _e('delete all image thumbs', $mf_domain); ?></label> 
			</td>
		</tr>
	</table>
	

	<h3><?php _e('Uninstall Magic Fields', $mf_domain); ?></h3>
	<table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="6"> 
		<tr valign="top">
			<th scope="row"><?php _e('Uninstall Magic Fields', $mf_domain); ?></th>
			<td>
				<input type="text" id="uninstall-custom-write-panel" name="uninstall-custom-write-panel" size="25" /><br />
				<label for="uninstall-custom-write-panel">
				&nbsp; <?php _e('Type <strong>uninstall</strong> into the textbox, click <strong>Update Options</strong>, and all the tables created by this plugin will be deleted', $mf_domain); ?></label>
			</td>
		</tr>
	</table>

	<p class="submit" ><input name="update-custom-write-panel-options" type="submit" value="<?php _e('Update Options', $mf_domain); ?>" /></p>
	
	</form>

	</div>
	
	<?php
	}

	function GetCheckboxState($optionValue) {
		if (empty($optionValue)){
			return '';
		} else  {
			return 'checked="checked"';
		}
	}
}
