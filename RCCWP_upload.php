<?php
require( dirname(__FILE__) . '/../../../wp-config.php' );
global $mf_domain,  $wpdb;
if (!(is_user_logged_in() && current_user_can('edit_posts')))
	die(__("Athentication failed!",$mf_domain));
?>

<html>
<head>

<?php

if (isset($_POST['fileframe'])) 
{
	$operationSuccess = "false";
	// A file is uploaded
	if (isset($_FILES['file']) && (!empty($_FILES['file']['tmp_name'])))  // file was send from browser
	{
		
		if ($_FILES['file']['error'] == UPLOAD_ERR_OK)  // no error
		{
			$special_chars = array (' ','`','"','\'','\\','/'," ","#","$","%","^","&","*","!","~","‘","\"","’","'","=","?","/","[","]","(",")","|","<",">",";","\\",",");
			$filename = str_replace($special_chars,'',$_FILES['file']['name']);
			$filename = time() . $filename;
			@move_uploaded_file( $_FILES['file']['tmp_name'], MF_FILES_PATH . $filename );
			@chmod(MF_FILES_PATH . $filename, 0644);
			$result_msg = "<font color=\"green\"><b>".__("Successful upload!",$mf_domain)."</b></font>" ;

            //adding the image to  WP media
            $query = "INSERT INTO  ".$wpdb->prefix. 'posts  (
                                                                post_author,
                                                                post_date,
                                                                post_date_gmt,
                                                                post_content,
                                                                post_title,
                                                                post_status,
                                                                post_name,
                                                                post_modified,
                                                                post_modified_gmt,
                                                                guid,
                                                                post_type,
                                                                post_mime_type
                                                            ) VALUES
                                                            (
                                                                1, 
                                                                now(),
                                                                now(),
                                                                "'.$_FILES['file']['name'].'",
                                                                "'.$_FILES['file']['name'].'",
                                                                "inherit",
                                                                "'.$_FILES['file']['name'].'",
                                                                now(),
                                                                now(),
                                                                "'.MF_FILES_URI.$filename.'",
                                                                "attachment",
                                                                "'.$_FILES['file']['type'].'"
                                                            )';
             
            $wpdb->query($query);

			$operationSuccess = "true";
		}
		elseif ($_FILES['file']['error'] == UPLOAD_ERR_INI_SIZE)
			$result_msg = __('The uploaded file exceeds the maximum upload limit',$mf_domain);
		else 
			$result_msg = "<font color=\"red\"><b>".__("Upload Unsuccessful!",$mf_domain)."</b></font>";
	
	}

	// If operation is success, make sure the file was created properly
	if ($operationSuccess == "true"){
		if ($fp_check_file = @fopen(MF_FILES_PATH . $filename, 'rb')) {
			fclose($fp_check_file);
		}
		else{
			$operationSuccess = "false";
			$result_msg = __("Failed to upload the file!",$mf_domain);
		}
		
	}
?>

	<script language="javascript">
        
		// The code that runs after the file is uploaded
    	var par = window.parent.document;
		var iframe = par.getElementById('upload_internal_iframe_<?php echo $_POST["input_name"]?>');
		par.getElementById('upload_progress_<?php echo $_POST["input_name"]?>').innerHTML = '<?php echo $result_msg?>';
		iframe.style.display="";

		if (<?php echo $operationSuccess?>){
			par.getElementById("<?php echo $_POST["input_name"]?>").value = "<?php echo $filename?>";
			
			par.getElementById("<?php echo $_POST["input_name"];?>_deleted").value = 0;
			//Set image
			<?php
				//$newImagePath = MF_URI.'phpThumb.php
                $newImagePath = PHPTHUMB.'?&w=150&h=120&src='.MF_FILES_URI.$filename;
				
				if (isset($_POST['imageThumbID'])){ 
			?>
				if( par.getElementById('<?php echo $_POST['imageThumbID']; ?>') )
				{ 
					par.getElementById('<?php echo $_POST['imageThumbID']; ?>').src = "<?php echo $newImagePath;?>";
					
                    var b = "&nbsp;<strong><a href='#remove' class='remove' id='remove-<?php echo $_POST['input_name'];?>'>Delete</a></strong>";

					par.getElementById("photo_edit_link_<?php echo $_POST['input_name'] ?>").innerHTML = b ;
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

	var iframe = par.getElementById('upload_internal_iframe_<?php echo $_GET["input_name"]?>');
	iframe.style.display="none";

		
	// update progress
	par.getElementById('upload_progress_<?php echo $_GET["input_name"]?>').style.visibility = "visible";
	par.getElementById('upload_progress_<?php echo $_GET["input_name"]?>').style.height = "auto";
	par.getElementById('upload_progress_<?php echo $_GET["input_name"]?>').innerHTML = "Transferring ";


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
	if (par.getElementById('upload_progress_<?php echo $_GET["input_name"]?>').innerHTML.substring(0,5) != "Trans") return;
	par.getElementById('upload_progress_<?php echo $_GET["input_name"]?>').innerHTML = newString;
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
				<td width=17%><label for="file"><?php _e('File', $mf_domain); ?>:</label><br />
				<input id="file" type="file" name="file" onchange="upload()" size="<?php echo $inputSize; ?>"/></td>
			<?php }else{ ?>
				<td width=17%><label for="file"><?php _e('File', $mf_domain); ?>:</label></td>
				<td><input id="file" type="file" name="file" onchange="upload()" size="<?php echo $inputSize; ?>"/></td>
			<?php } ?>
		</tr>

	</table>

	
	<input type="hidden" name="fileframe" value="true" />
	<input type="hidden" name="imgnum" />
	<input type="hidden" name="input_name" value="<?php echo $_GET["input_name"]?>" />
	<input type="hidden" name="type" value="<?php echo $_GET["type"]?>" />
</form>
</body>
</html>