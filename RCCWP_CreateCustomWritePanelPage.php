<?php

include_once('RCCWP_CustomWritePanelPage.php');

class RCCWP_CreateCustomWritePanelPage
{
	function Main()
	{
		global $mf_domain;
		?>

		<div class="wrap">

		<h2><?php _e('Create Custom Write Panel',$mf_domain); ?></h2>
		
		<form action="<?php echo RCCWP_ManagementPage::GetCustomWritePanelGenericUrl('finish-create-custom-write-panel')?>" method="post" id="create-new-write-panel-form">
		
		<?php RCCWP_CustomWritePanelPage::Content(); ?>
		
		<p class="submit" >
			<a style="color:black" href="<?php echo RCCWP_ManagementPage::GetCustomWritePanelGenericUrl('cancel-create-custom-write-panel')?>" class="button"><?php _e('Cancel'); ?></a>
			<input type="submit" id="finish-create-custom-write-panel" value="<?php _e('Finish'); ?>" />
		</p>
		
		</form>

		</div>
		<br />
		<?php
	}
}
