<?php

// use wp-load. Normally right here, but if it's not...
if( file_exists('../../../wp-load.php') )
{
	require_once('../../../wp-load.php');
	$loaded = true;
} // ...then look over here
elseif( file_exists('./mf-config.php') )
{
	include_once('./mf-config.php');
	require_once(MF_WP_LOAD);
	$loaded = true;
}

if( $loaded !== true ){
	die('Could not load wp-load.php, edit/add mf-config.php and define MF_WP_LOAD to point to a valid wp-load file.');
}



global $mf_domain,  $wpdb;
if (!(is_user_logged_in() &&
      (current_user_can('edit_posts') || current_user_can('edit_published_pages'))))
	die(__("Authentication failed!",$mf_domain));
?>

<html>
<head>

<?php
//Change the nameinput magicfields[type][id gruop index][id field index] => magicfields_{type}_{id group index}_{if field index}
function changeNameInput($inputName){
	
	$patterns  = array('/\[/','/\]/');
	$replacements = array('_','');
	return preg_replace($patterns,$replacements,$inputName);
	
}

if (isset($_POST['fileframe'])){

  if ( empty($_POST) || !wp_verify_nonce($_POST['checking'],'nonce_upload_file') ){
    print 'Sorry, your nonce did not verify.';
    exit;
  }
	$operationSuccess = "false";
	
	//type of upload
	if(!empty($_POST['type'])){
		if ($_POST['type'] == '1'){
			$acceptedExts = "image";
		}elseif ($_POST['type'] == '2'){
			$acceptedExts = "audio"; 
		}
	}else{
		$acceptedExts = "file";
	}
	
	// A file is uploaded
	if (isset($_FILES['file']) && (!empty($_FILES['file']['tmp_name'])))  // file was send from browser
	{
		
		if ($_FILES['file']['error'] == UPLOAD_ERR_OK)   { //no error
			$special_chars = array (' ','`','"','\'','\\','/'," ","#","$","%","^","&","*","!","~","‘","\"","’","'","=","?","/","[","]","(",")","|","<",">",";","\\",",","+","-");
			$filename = str_replace($special_chars,'',$_FILES['file']['name']);
			$filename = time() . $filename;

      $action_mf_file = $_FILES['file'];

			@move_uploaded_file( $_FILES['file']['tmp_name'], MF_FILES_PATH . $filename );
			@chmod(MF_FILES_PATH . $filename, 0644);
    

		  $result_msg = '<span class="mf-upload-success">'.__("Successful upload",$mf_domain).'!</span>' ;
			
			//Checking the mimetype of the file
			if(valid_mime($_FILES['file']['type'],$acceptedExts)){
				$operationSuccess = "true";

        $action_mf_file['tmp_name'] = MF_FILES_PATH . $filename;
        do_action( 'mf_after_upload_file', $action_mf_file );
			}else{
				$operationSuccess = "false";
				
				//deleting unaccepted file
				$file_delete = MF_FILES_PATH.$filename;
				unlink($file_delete);
				
			}
		}elseif ($_FILES['file']['error'] == UPLOAD_ERR_INI_SIZE){
		  $result_msg = '<span class="mf-upload-error">'.__('The uploaded file exceeds the maximum upload limit',$mf_domain).'!</span>';
		}else{ 
		  $result_msg = '<span class="mf-upload-error">'.__("Upload Unsuccessful",$mf_domain).'!</span>';
		}
	}

	// If operation is success, make sure the file was created properly
	
	if ($operationSuccess == "true"){
		if ($fp_check_file = @fopen(MF_FILES_PATH . $filename, 'rb')) {
			fclose($fp_check_file);
		}else{
			$operationSuccess = "false";
			$result_msg = __("Failed to upload the file!",$mf_domain);
		}
	}else{
		$result_msg = '<span class="mf-upload-error">'.__("Upload Unsuccessful!",$mf_domain).'</span>';
	}
?>

	<script type="text/javascript" charset="utf-8">		
		
		<?php
			$idField = changeNameInput($_POST['input_name']);
		?>
		
		// The code that runs after the file is uploaded
		var par = window.parent.document;
		var iframe = par.getElementById('upload_internal_iframe_<?php echo $idField;?>');
		par.getElementById('upload_progress_<?php echo $idField;?>').innerHTML = '<?php echo $result_msg?>';
		iframe.style.display="";

		if ( "<?php echo $operationSuccess;?>" == "true"){
			
			old_file = par.getElementById("<?php echo $idField; ?>").value;
      if(old_file != '' ){
        delete_field = par.getElementById("magicfields_remove_files").value;
				if(delete_field != ''){
          par.getElementById("magicfields_remove_files").value = delete_field+"|||"+old_file;
        }else{
					par.getElementById("magicfields_remove_files").value = old_file;
        }
      }
					
			
			par.getElementById("<?php echo $idField; ?>").value = "<?php echo $filename?>";
			//Set image
			<?php
		    //$newImagePath = PHPTHUMB.'?&w=150&h=120&src='.MF_FILES_URI.$filename;

        if($acceptedExts == "image") {
          $newImagePath = aux_image($filename,'w=150&h=120&zc=1');
        }else{
          $newImagePath = "";
        }
				
				if (isset($_POST['imageThumbID'])){ 
			?>
				if( par.getElementById('<?php echo $_POST['imageThumbID']; ?>') )
				{ 
					par.getElementById('<?php echo $_POST['imageThumbID']; ?>').src = "<?php echo $newImagePath;?>";
					
					var b = "&nbsp;<strong><a href='#remove' class='remove' id='remove-<?php echo $idField;?>'>Delete</a></strong>";

					par.getElementById("photo_edit_link_<?php echo $idField; ?>").innerHTML = b ;
				}
			<?php } ?>
		}
	</script>
<?php
}
?>

<script language="javascript">
function upload(){
	// hide old iframe
	var par = window.parent.document;
	
	<?php
		$idField = changeNameInput($_GET['input_name']);
	?>
	

	var iframe = par.getElementById('upload_internal_iframe_<?php echo  $idField?>');
	iframe.style.display="none";

	// update progress
	par.getElementById('upload_progress_<?php echo $idField;?>').style.display = "block";
	par.getElementById('upload_progress_<?php echo $idField;?>').innerHTML = "Transferring ";

	setTimeout("transferring(0)",1000);
	
	// send 
	document.iform.submit();
	
}

function transferring(dots){
	
	newString = "Transferring ";
	for (var x=1; x<=dots; x++) {
		newString = newString + ".";
	} 
	
	var par = window.parent.document;

	// update progress
	if (par.getElementById('upload_progress_<?php echo $idField;?>').innerHTML.substring(0,5) != "Trans") return;
	par.getElementById('upload_progress_<?php echo $idField;?>').innerHTML = newString;
	if (dots == 4) dots = 0; else dots = dots + 1;
	setTimeout("transferring("+dots+")",1000) ;
	
}

</script>
<style>
body {
	padding: 0px;
	margin: 0px;
	vertical-align:top;
}
</style>
<link rel='stylesheet' href='<?php echo get_bloginfo('wpurl');?>/wp-admin/css/global.css' type='text/css' />
<link rel='stylesheet' href='<?php echo get_bloginfo('wpurl');?>/wp-admin/wp-admin.css' type='text/css' />
<link rel='stylesheet' href='<?php echo get_bloginfo('wpurl');?>/wp-admin/css/colors-fresh.css' type='text/css' />
<style>
body {
	background: transparent;
}

input.mf-file {
 background: #f8f8f8; 
}

label.label-file {
  font-size: 12px;
  padding-left: 2px;
}

</style>


</head>
<body>


<form name="iform" action="" method="post" enctype="multipart/form-data">

	<input type="hidden" name="fileframe" value="true" />
	
	<?php	
	if (isset($_GET['imageThumbID'])) {
		echo '<input type="hidden" name="imageThumbID" value="'.$_GET['imageThumbID'].'" />';
	}

	if (isset($_GET['inputSize'])){
		$inputSize = $_GET['inputSize'];
	}
	?>
	

	<table border="0">

		<tr>
			<?php if($_GET['canvas']!=0){ ?>
				<td width=17%><label for="file" class="label-file"><?php _e('File', $mf_domain); ?>:</label><br />
				<input id="file" type="file" name="file" onchange="upload()" size="<?php echo $inputSize; ?>" class="mf-file" /></td>
			<?php }else{ ?>
				<td width=17%><label for="file" class="label-file"><?php _e('File', $mf_domain); ?>:</label></td>
				<td><input id="file" type="file" name="file" onchange="upload()" class="mf-file" /></td>
			<?php } ?>
		</tr>

	</table>

<?php wp_nonce_field('nonce_upload_file','checking'); ?>	
	<input type="hidden" name="fileframe" value="true" />
	<input type="hidden" name="imgnum" />
	<input type="hidden" name="input_name" value="<?php echo $_GET["input_name"]?>" />
	<input type="hidden" name="type" value="<?php echo $_GET["type"]?>" />
</form>
</body>
</html>
