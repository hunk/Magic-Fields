<?php

/**
 * Magic Fields's debug Class 
 *
 *  @author David Valdez <me@gnuget.org>
 *  @package Magic Fields
 *  @subpackage  tools
 */
class Debug
{
	/**
	 * Writes log info to a file
	 * @param $msg string the message to write out
	 * @param $path string the location to write the messages
	 * @return null
	 */
	function log($msg,$path = "") {
		if(empty($path)){
			$path = dirname(__FILE__)."/../tmp/debug/";
		}

		if(!is_string($msg)){
			$msg = print_r($msg,true);
		}

		$fp = fopen($path.'magic_fields.log', 'a+');
		$date = gmdate( 'Y-m-d H:i:s' );
		fwrite($fp, "$date - $msg\n");
		fclose($fp);
	 }
}

//wrapper for print_r with tag pre
if (!function_exists('pr')) {
	function pr($data){
		echo "<pre>";
		print_r($data);
		echo "</pre>";
	}
}
