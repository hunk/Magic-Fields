<?php
class RC_Format
{
	function BoolToSql($value)
	{
		$sql = $value == true ? "'true'" : "'false'";
		return $sql;
	}
	
	function GetInputName($fieldName)
	{
		$name = 'rc_cwp_meta_' . str_replace(' ', '_', $fieldName);
		$name = attribute_escape(str_replace('.', '$DOT$', $name));
		return $name;
	}
	
	function GetFieldName($inputName)
	{
		$fieldName = str_replace('rc_cwp_meta_', '', $inputName);
		//$fieldName = str_replace('_', ' ', $fieldName);
		$fieldName = str_replace('$DOT$', '.', $fieldName);
		return $fieldName;
	}
	
	function TextToSql($value)
	{
		$value = trim($value);
		$sql = $value == '' ? 'NULL' : "'$value'";
		return $sql;
	}
	
	function TrimArrayValues(&$value, $key)
	{
		$value = trim($value);
	}
}
?>