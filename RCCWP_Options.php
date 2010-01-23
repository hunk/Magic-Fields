<?php
/**
 *  This class is used for  Manages all the options related With Magic Fields
 * 
 */
class RCCWP_Options {
	/**
	 *  Update the options of Magic Fields
	 *
	 *  @params array $options is a  array with the options of Magic Fields
	 */
	function Update($options) {
		$options = serialize($options);
		update_option(RC_CWP_OPTION_KEY, $options);
	}
	
	/**
	 *  Get  the options of magic fields
	 *
	 *  if is not specified a key  is return a array with all the options of magic fields
	 *  
	 *  @param string $key is the name of the option. 
	 *
	 */
	function Get($key = null) {
		if (get_option(RC_CWP_OPTION_KEY) == "") return "";
		if (is_array(get_option(RC_CWP_OPTION_KEY)))
			$options = get_option(RC_CWP_OPTION_KEY);
		else
			$options = unserialize(get_option(RC_CWP_OPTION_KEY));

		if (!empty($key)){
		  if( isset($options[$key]) ) return $options[$key];
			return false;
		}else{
			return $options;
		}
	}

	/**
	 *  Save a new value in the options
	 * 
	 *  @param string  $key  is the name of the option to will be updated
	 *  @param string $val is the new value of the option
	 */
	function Set($key, $val) {
		$options = RCCWP_Options::Get();
		$options[$key] = $val;
		RCCWP_Options::Update($options);
	}
}
