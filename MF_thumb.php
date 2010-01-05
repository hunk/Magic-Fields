<?php
/*
	Magic Fields Thumb class
	
	This class is based in the TimThumb script created by Tim McDaniels and Darren Hoyt with tweaks by Ben Gillbanks
	http://code.google.com/p/timthumb/

	MIT License: http://www.opensource.org/licenses/mit-license.php

	Paramters
	---------
	w: width
	h: height
	zc: zoom crop (0 or 1)
	q: quality (default is 75 and max is 100)
*/
class mfthumb{

	function generate($image,$target,$new_name,$params){
		$default = array(
							"w"		=>	100, //width
							"h"	=>	0, //height
							"q"	=>	85, //quality
							"zc"	=>	0 //zoom crop
						);
						
		$params = array_merge($default,$params);
						
		// sort out image source
		$src = $image;
		if($src == "" || strlen($src) <= 3) {
			displayError("no image specified");
		}				
		
		// clean params before use
		$src = $this->cleanSource($src);
		
		// get properties
		$new_width 		= preg_replace("/[^0-9]+/", "", $params['w']);
		$new_height 	= preg_replace("/[^0-9]+/", "", $params['h']);
		$zoom_crop 		= preg_replace("/[^0-9]+/", "", $params['zc']);
		$quality 		= preg_replace("/[^0-9]+/", "", $params['q']);

		if ($new_width == 0 && $new_height == 0) {
			$new_width = 100;
			$new_height = 100;
		}
		
		// get mime type of src
		$mime_type = $this->mime_type($src);
		
		ini_set('memory_limit', "30M");
		
		// make sure that the src is gif/jpg/png
		if(!$this->valid_src_mime_type($mime_type)) {
			displayError("Invalid src mime type: " .$mime_type);
		}
		
		// check to see if GD function exist
		if(!function_exists('imagecreatetruecolor')) {
			displayError("GD Library Error: imagecreatetruecolor does not exist");
		}
		
		if(strlen($src) && file_exists($src)) {
			
			// open the existing image
			$image = $this->open_image($mime_type, $src);
			if($image === false) {
				displayError('Unable to open image : ' . $src);
			}
			
			// Get original width and height
			$width = imagesx($image);
			$height = imagesy($image);
			
			// don't allow new width or height to be greater than the original
			if( $new_width > $width ) {
				$new_width = $width;
			}
			if( $new_height > $height ) {
				$new_height = $height;
			}
			
			// generate new w/h if not provided
			if( $new_width && !$new_height ) {
				
				$new_height = $height * ( $new_width / $width );
				
			} elseif($new_height && !$new_width) {
				
				$new_width = $width * ( $new_height / $height );
				
			} elseif(!$new_width && !$new_height) {
				
				$new_width = $width;
				$new_height = $height;
				
			}
			
			// create a new true color image
			$canvas = imagecreatetruecolor( $new_width, $new_height );
			imagealphablending($canvas, false);
			// Create a new transparent color for image
			$color = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
			// Completely fill the background of the new image with allocated color.
			imagefill($canvas, 0, 0, $color);
			// Restore transparency blending
			imagesavealpha($canvas, true);
			
			if( $zoom_crop ) {
				
				$src_x = $src_y = 0;
				$src_w = $width;
				$src_h = $height;
				
				$cmp_x = $width  / $new_width;
				$cmp_y = $height / $new_height;	
				
				
				// calculate x or y coordinate and width or height of source
				
				if ( $cmp_x > $cmp_y ) {
					
					$src_w = round( ( $width / $cmp_x * $cmp_y ) );
					$src_x = round( ( $width - ( $width / $cmp_x * $cmp_y ) ) / 2 );
					
				} elseif ( $cmp_y > $cmp_x ) {
					
					$src_h = round( ( $height / $cmp_y * $cmp_x ) );
					$src_y = round( ( $height - ( $height / $cmp_y * $cmp_x ) ) / 2 );
					
				}
				
				imagecopyresampled( $canvas, $image, 0, 0, $src_x, $src_y, $new_width, $new_height, $src_w, $src_h );
				
			} else {
				
				// copy and resize part of an image with resampling
				imagecopyresampled( $canvas, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height );
				
			}

			// output image to browser based on mime type
			$this->show_image($mime_type, $canvas, $target,$quality,$new_name);
			
			// remove image from memory
			imagedestroy($canvas);
			
		} else {
			
			if(strlen($src)) {
				displayError("image " . $src . " not found");
			} else {
				displayError("no source specified");
			}
			
		}
	}
		
	/**
	 * 
	 */
	function show_image($mime_type, $image_resized, $cache_dir,$quality,$name = '') {
		
		if(empty($name)){
			// check to see if we can write to the cache directory
			$name = date('YmdHis');
		}
		
		$name .= ".png";
	
		$is_writable = 0;
		$cache_file_name = $cache_dir . '/' . $name;

		$quality = floor($quality * 0.09);

		imagepng($image_resized, $cache_file_name, $quality);
	}

	/**
	 * 
	 */
	function open_image($mime_type, $src) {

		if(stristr($mime_type, 'gif')) {

			$image = imagecreatefromgif($src);

		} elseif(stristr($mime_type, 'jpeg')) {

			@ini_set('gd.jpeg_ignore_warning', 1);
			$image = imagecreatefromjpeg($src);

		} elseif( stristr($mime_type, 'png')) {

			$image = imagecreatefrompng($src);

		}

		return $image;
	}


	/**
	 * determine the file mime type
	 */
	function mime_type($file) {

		if (stristr(PHP_OS, 'WIN')) { 
			$os = 'WIN';
		} else { 
			$os = PHP_OS;
		}

		$mime_type = '';

		if (function_exists('mime_content_type')) {
			$mime_type = mime_content_type($file);
		}

		// use PECL fileinfo to determine mime type
		if (!$this->valid_src_mime_type($mime_type)) {
			if (function_exists('finfo_open')) {
				$finfo = finfo_open(FILEINFO_MIME);
				$mime_type = finfo_file($finfo, $file);
				finfo_close($finfo);
			}
		}

		// try to determine mime type by using unix file command
		// this should not be executed on windows
	    if (!$this->valid_src_mime_type($mime_type) && $os != "WIN") {
			if (preg_match("/FREEBSD|LINUX/", $os)) {
				$mime_type = trim(@shell_exec('file -bi "' . $file . '"'));
			}
		}

		// use file's extension to determine mime type
		if (!$this->valid_src_mime_type($mime_type)) {

			// set defaults
			$mime_type = 'image/png';
			// file details
			$fileDetails = pathinfo($file);
			$ext = strtolower($fileDetails["extension"]);
			// mime types
			$types = array(
	 			'jpg'  => 'image/jpeg',
	 			'jpeg' => 'image/jpeg',
	 			'png'  => 'image/png',
	 			'gif'  => 'image/gif'
	 		);

			if (strlen($ext) && strlen($types[$ext])) {
				$mime_type = $types[$ext];
			}

		}

		return $mime_type;

	}

	/**
	 * 
	 */
	function valid_src_mime_type($mime_type) {

		if (preg_match("/jpg|jpeg|gif|png/i", $mime_type)) {
			return true;
		}

		return false;

	}

	/**
	 * tidy up the image source url
	 */
	function cleanSource($src) {

		// remove slash from start of string
		//if(strpos($src, "/") == 0) {
		//	$src = substr($src, -(strlen($src) - 1));
		//}
		// remove http/ https/ ftp
		$src = preg_replace("/^((ht|f)tp(s|):\/\/)/i", "", $src);
		// remove domain name from the source url
		$host = $_SERVER["HTTP_HOST"];
		$src = str_replace($host, "", $src);
		$host = str_replace("www.", "", $host);
		$src = str_replace($host, "", $src);

		// don't allow users the ability to use '../' 
		// in order to gain access to files below document root

		// src should be specified relative to document root like:
		// src=images/img.jpg or src=/images/img.jpg
		// not like:
		// src=../images/img.jpg
		$src = preg_replace("/\.\.+\//", "", $src);

		// get path to image on file system
		//$src = $this->get_document_root($src) . '/' . $src;	

		return $src;

	}

	/**
	 * 
	 */
	function get_document_root ($src) {
		// check for unix servers
		if(@file_exists($_SERVER['DOCUMENT_ROOT'] . '/' . $src)) {
			return $_SERVER['DOCUMENT_ROOT'];
		}

		// check from script filename (to get all directories to timthumb location)
		$parts = array_diff(explode('/', $_SERVER['SCRIPT_FILENAME']), explode('/', $_SERVER['DOCUMENT_ROOT']));
		$path = $_SERVER['DOCUMENT_ROOT'] . '/';
		foreach ($parts as $part) {
			$path .= $part . '/';
			if (file_exists($path . $src)) {
				return $path;
			}
		}	

		// special check for microsoft servers
		if(!isset($_SERVER['DOCUMENT_ROOT'])) {
	    	$path = str_replace("/", "\\", $_SERVER['ORIG_PATH_INFO']);
	    	$path = str_replace($path, "", $_SERVER['SCRIPT_FILENAME']);

	    	if( @file_exists( $path . '/' . $src ) ) {
	    		return $path;
	    	}
		}	
		displayError('file not found ' . $src);
	}

	/**
	 * generic error message
	 */
	function displayError($errorString = '') {

		header('HTTP/1.1 400 Bad Request');
		die($errorString);

	}
}
?>