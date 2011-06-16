<?php

class RCCWP_SWFUpload
{
	function Body($inputName, $fileType, $isCanvas = 0, $urlInputSize = false) {
		global $mf_domain;
		include_once('RCCWP_Options.php');
		
		$idField = RCCWP_WritePostPage::changeNameInput($inputName);

		if (!$urlInputSize) $urlInputSize = 20;

		if ($isCanvas==0) {
			$iframeInputSize = $urlInputSize;
			$iframeWidth = 380;
			$iframeHeight = 40;
			$inputSizeParam  = '';
		}else{
			$isCanvas = 1;
			$iframeWidth = 150;
			$iframeHeight = 60;
			$iframeInputSize = 3;
			$inputSizeParam = "&inputSize=$iframeInputSize";
		}

		$iframePath = MF_URI."RCCWP_upload.php?input_name=".urlencode($inputName)."&type=$fileType&imageThumbID=img_thumb_$idField&canvas=$isCanvas".$inputSizeParam ;
		?>
      <?php if (RCCWP_Options::Get('use-standard-uploader')) : ?>
			<div id='upload_iframe_<?php echo $idField;?>' class="iframeload { iframe: { id: 'upload_internal_iframe_<?php echo $idField ?>', src: '<?php echo $iframePath;?>', height: <?php echo $iframeHeight ?>, width: <?php echo $iframeWidth ?> } }">
			</div>
      <?php else: ?>
			<div id='upload_ajax_<?php echo $idField;?>' class="ajaxupload { lang: { upload_error: '<?php echo __("Upload Failed", $mf_domain) ?>', upload_success: '<?php echo __("Successful Upload", $mf_domain) ?>', upload: '<?php echo __("Choose File...", $mf_domain) ?>', replace: '<?php echo __("Replace File...", $mf_domain) ?>', drop: '<?php echo __("drop file here to upload", $mf_domain)?>' }}">
      </div>
      <?php endif; ?>
			<table border="0">
				<tr >
					<td style="border-bottom-width: 0px; padding: 0"><label for="upload_url"><?php _e('Or URL', $mf_domain); ?>:</label></td>
					<td style="border-bottom-width: 0px; padding-left: 4px;">
						<input id="upload_url_<?php echo $idField;  ?>"
							name="upload_url_<?php echo $inputName ?>"
							type="text"
							size="<?php echo $urlInputSize ?>"
							class="mf-upload-url" />
                                                        <input type="button" onclick="uploadurl('<?php echo $idField  ?>','<?php echo $fileType ?>','<?php echo wp_create_nonce("nonce_url_file") ?>')" value="Upload" class="button" style="width:70px"/>
					</td>
				</tr>
			</table>
		<?php
	}
}
