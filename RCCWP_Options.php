<?php

class RCCWP_Options
{
	function Delete()
	{
		delete_option(RC_CWP_OPTION_KEY);
	}
	
	function Update($options)
	{
		$options = serialize($options);
		update_option(RC_CWP_OPTION_KEY, $options);
	}
	
	function Get($key = null)
	{
		if (get_option(RC_CWP_OPTION_KEY) == "") return "";
		if (is_array(get_option(RC_CWP_OPTION_KEY)))
			$options = get_option(RC_CWP_OPTION_KEY);
		else
			$options = unserialize(get_option(RC_CWP_OPTION_KEY));

		if (!empty($key)){
			return $options[$key];
		}else{
			return $options;
		}
	}

	function Set($key, $val)
	{
		$options = RCCWP_Options::Get();
		$options[$key] = $val;
		RCCWP_Options::Update($options);
	}
}
