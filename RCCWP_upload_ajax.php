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

if (!(is_user_logged_in() &&
      (current_user_can('edit_posts') || current_user_can('edit_published_pages'))))
	die(__("Authentication failed!",$mf_domain));


/* checking nonce */
$nonce=$_GET['nonce_ajax'];
if (! wp_verify_nonce($nonce, 'once_ajax_uplooad') ){
  $result = array('error' => 'Sorry, your nonce did not verify.');
  echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
  die; 
}

/**
 * Handle file uploads via XMLHttpRequest
 */
class qqUploadedFileXhr {
    /**
     * Save the file to the specified path
     * @return boolean TRUE on success
     */
    function save($path) {    
        $input = fopen("php://input", "r");
        $temp = tmpfile();
        $realSize = stream_copy_to_stream($input, $temp);
        fclose($input);
        
        if ($realSize != $this->getSize()){            
            return false;
        }
        
        $target = fopen($path, "w");        
        fseek($temp, 0, SEEK_SET);
        stream_copy_to_stream($temp, $target);
        fclose($target);
        
        return true;
    }
    function getName() {
        return $_GET['qqfile'];
    }
    function getSize() {
        if (isset($_SERVER["CONTENT_LENGTH"])){
            return (int)$_SERVER["CONTENT_LENGTH"];            
        } else {
            //throw new Exception('Getting content length is not supported.');
        }      
    }   
}

/**
 * Handle file uploads via regular form post (uses the $_FILES array)
 */
class qqUploadedFileForm {  
    /**
     * Save the file to the specified path
     * @return boolean TRUE on success
     */
    function save($path) {
        if(!move_uploaded_file($_FILES['qqfile']['tmp_name'], $path)){
            return false;
        }
        return true;
    }
    function getName() {
        return $_FILES['qqfile']['name'];
    }
    function getSize() {
        return $_FILES['qqfile']['size'];
    }
}

class qqFileUploader {
    var $allowedExtensions = array();
    var $sizeLimit = 0;
    var $file;

    function qqFileUploader($allowedExtensions = array(), $sizeLimit = 0){
        $allowedExtensions = array_map("strtolower", $allowedExtensions);
            
        $this->allowedExtensions = $allowedExtensions;        
        $this->sizeLimit = $sizeLimit;       

        if (isset($_GET['qqfile'])) {
            $this->file = new qqUploadedFileXhr();
        } elseif (isset($_FILES['qqfile'])) {
            $this->file = new qqUploadedFileForm();
        } else {
            $this->file = false; 
        }
    }
    
    /**
     * Returns array('success'=>true) or array('error'=>'error message')
     */
    function handleUpload($uploadDirectory, $replaceOldFile = FALSE){
        if (!is_writable($uploadDirectory)){
            return array('error' => "Server error. Upload directory isn't writable.");
        }
        
        if (!$this->file){
            return array('error' => 'No files were uploaded.');
        }
        
        /*
        $size = $this->file->getSize();
        
        if ($size == 0) {
            return array('error' => 'File is empty');
        }
        
        if ($size > $this->sizeLimit) {
            return array('error' => 'File is too large');
        }
        */
        
        $pathinfo = pathinfo($this->file->getName());
        $filename = $pathinfo['filename'];
        
        // remove any special characters, since these can cause problems
        $special_chars = array (' ','`','"','\'','\\','/'," ","#","$","%","^","&","*","!","~","‘","\"","’","'","=","?","/","[","]","(",")","|","<",">",";","\\",",","+","-");
			  $filename = strtolower(str_replace($special_chars,'', $filename));

        //$filename = md5(uniqid());
        
        // convert the extension to lowercase to avoid problems
        $ext = strtolower($pathinfo['extension']);

        if($this->allowedExtensions && !in_array(strtolower($ext), $this->allowedExtensions)){
            $these = implode(', ', $this->allowedExtensions);
            return array('error' => 'File has an invalid extension, it should be one of '. $these . '.');
        }
        
        if(!$replaceOldFile){
            /// don't overwrite previous files that were uploaded
            while (file_exists($uploadDirectory . $filename . '.' . $ext)) {
                $filename .= rand(10, 99);
            }
        }
        
        if ($this->file->save($uploadDirectory . $filename . '.' . $ext)){
            @chmod($uploadDirectory . $filename . '.' . $ext, 0644);

            $uri = MF_FILES_URI.$filename . '.' . $ext;
            
            return array('success'=>true, 'ext' => $ext, 'thumb' => PHPTHUMB.'?&w=150&h=120&src='.$uri, 'file' => $filename. '.' . $ext ,'uri' => $uri);
        } else {
            return array('error'=> 'Could not save uploaded file.' .
                'The upload was cancelled, or server error encountered');
        }
        
    }    
}

// list of valid extensions, ex. array("jpeg", "xml", "bmp")
$allowedExtensions = array();

function fs_let_to_num($v){ //This function transforms the php.ini notation for numbers (like '2M') to an integer (2*1024*1024 in this case)
    $l = substr($v, -1);
    $ret = substr($v, 0, -1);
    switch(strtoupper($l)){
    case 'P':
        $ret *= 1024;
    case 'T':
        $ret *= 1024;
    case 'G':
        $ret *= 1024;
    case 'M':
        $ret *= 1024;
    case 'K':
        $ret *= 1024;
        break;
    }
    return $ret;
}

/*
// max file size in bytes
$ini_limit = ini_get('upload_max_filesize');

if (ini_limit == "") {
  // set the limit to 500MB, if we can't find it in INI
  $ini_limit = 500 * 1024 * 1024;
} else {
  // convert the number to bytes
  $ini_limit = fs_let_to_num($ini_limit);
}
*/

// TODO: In a future version, make this uploader honour PHP ini file size.
// for now, lets hardcode it to 10000M (essentially unlimited for a web site, who is uploading > 10GB files?)

$sizeLimit = 10000 * 1024 * 1024;

$uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
$result = $uploader->handleUpload(MF_FILES_PATH);
// to pass data through iframe you will need to encode all html tags
echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
