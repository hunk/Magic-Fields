<?php

class RCCWP_SWFUpload
{
	function Body($inputName, $fileType, $isCanvas = 0, $urlInputSize = false) {
		global $mf_domain;
		include_once('RCCWP_Options.php');

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

		$iframePath = MF_URI."RCCWP_upload.php?input_name=".urlencode($inputName)."&type=$fileType&imageThumbID=img_thumb_$inputName&canvas=$isCanvas".$inputSizeParam ;
		?>
			<div id='upload_iframe_<?php echo $inputName?>'>
				<iframe id='upload_internal_iframe_<?php echo $inputName?>' src='<?php echo $iframePath;?>' frameborder='' scrolling='no' style="border-width: 0px; height: <?php echo $iframeHeight ?>px; width: <?php echo $iframeWidth ?>px;vertical-align:top;"></iframe>
			</div>
			<table border="0">
				<tr >
					<td style="border-bottom-width: 0px;padding: 0; padding-bottom:32px;"><label for="upload_url"><?php _e('Or URL', $mf_domain); ?>:</label></td>
					<td style="border-bottom-width: 0px">
						<input id="upload_url_<?php echo $inputName ?>"
							name="upload_url_<?php echo $inputName ?>"
							type="text"
							size="<?php echo $urlInputSize ?>"
							/>
						<input type="button" onclick="uploadurl('<?php echo $inputName ?>','<?php echo $fileType ?>')" value="Upload" class="button" style="width:70px"/>
					</td>
				</tr>
			</table>
		<?php
	}
}
