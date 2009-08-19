<?php

	require_once('../../../wp-config.php');
	
	global $mf_domain;

	if ( ( isset($_SERVER['HTTPS']) && 'on' == strtolower($_SERVER['HTTPS']) ) && empty($_COOKIE[SECURE_AUTH_COOKIE]) && !empty($_REQUEST['auth_cookie']) )
		$_COOKIE[SECURE_AUTH_COOKIE] = $_REQUEST['auth_cookie'];
	elseif ( empty($_COOKIE[AUTH_COOKIE]) && !empty($_REQUEST['auth_cookie']) )
		$_COOKIE[AUTH_COOKIE] = $_REQUEST['auth_cookie'];
	unset($current_user);

	if (!(is_user_logged_in() && current_user_can('edit_posts')))
  		
		die(__('Athentication failed!',$mf_domain));

	$imagesExts = array('gif','jpg','png');
	$audiosExts = array('wav','mp3');
	
	$acceptedExtsString = "";
	
	function DownloadFile() {
        global $acceptedExtsString, $imagesExts, $audiosExts;
	
		$url = $_POST['upload_url'];
	
		// Prepare accpeted extensions
		$acceptedExts = array();
	
		if ('1' == $_POST['type'])
			$acceptedExts = $imagesExts;
		elseif ('2' == $_POST['type']) 	
			$acceptedExts = $audiosExts;
	
	
		//Retrieve file
		if ($fp_source = @fopen($url, 'rb')) {
			//Get target filename
			$exploded_url = explode( '.', $url );
	
			$ext = array_pop( $exploded_url );
	
			// Check extension
			if (false != $acceptedExts)
				if (false === array_search(strtolower($ext), $acceptedExts)){
					foreach($acceptedExts as $acceptedExt)
						if ($acceptedExtsString == "")
							$acceptedExtsString = $acceptedExt;
						else
							$acceptedExtsString = $acceptedExtsString." - ".$acceptedExt;
					return false;
				}
			
	
			$filename = time() . '_' . str_replace( 'rc_cwp_meta_', '', $_POST["input_name"]) . '.' . $ext;
			
			$directory = MF_FILES_PATH;
	
			$fp_dest = @fopen($directory . $filename,"wb");
			if ($fp_dest == false) return false;
	
			while(!feof($fp_source)) {
				set_time_limit(30);
				$readData = fread($fp_source, 1024*2);
				fwrite($fp_dest,$readData);	
			}
			fclose($fp_source) ;
			fclose($fp_dest) ;
			return $filename;
		}
		return false;
	}

	if (isset($_POST['upload_url']) && (!empty($_POST['upload_url'])))  // file was send from browser
	{
		if ( (substr($_POST['upload_url'],0,4) != "http") && (substr($_POST['upload_url'],0,3) != "ftp"))
			$_POST['upload_url'] = "http://".$_POST['upload_url'];

		$filename = DownloadFile();
		

		if (false == $filename) {
			if ($acceptedExtsString != "") $infoStr = ". Make sure the file ends with: $acceptedExtsString";
			$result_msg = "<font color='red'><b>".__("Upload Unsuccessful",$mf_domain)."!</b></font>";
		}
		else{
			$result_msg = "<font color='green'><b>".__("Successful upload",$mf_domain)."!</b></font>" ;
			$operationSuccess = "true";
		}
		
		echo $result_msg."*".$filename;
	}

if( isset($_FILES['async-upload'] ) )
{
	if ($_FILES['async-upload']['error'] == UPLOAD_ERR_OK)  // no error
	{
		$special_chars = array (' ','`','"','\'','\\','/'," ","#","$","%","^","&","*","!","~","‘","\"","’","'","=","?","/","[","]","(",")","|","<",">",";","\\",",");
		$filename = str_replace($special_chars,'',$_FILES['async-upload']['name']);
		$filename = time() . $filename;
		@move_uploaded_file( $_FILES['async-upload']['tmp_name'], MF_FILES_PATH . $filename );
		@chmod(MF_FILES_PATH . $filename, 0644);
		$result_msg = "<font color='green'><b>".__("Successful upload",$mf_domain)."!</b></font>" ;
		$operationSuccess = "true";
	}
	elseif ($_FILES['Filedata']['error'] == UPLOAD_ERR_INI_SIZE)
		$result_msg = __('The uploaded file exceeds the maximum upload limit',$mf_domain);
	else 
		$result_msg = "<font color='red'><b>".__("Upload Unsuccessful",$mf_domain)."!</b></font>";

	echo $result_msg."*".$filename;
}
?>