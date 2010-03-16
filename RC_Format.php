<?php

class RC_Format {

	function TextToSql($value) {
		$value = trim($value);
		$sql = $value == '' ? 'NULL' : "'$value'";
		return $sql;
	}
	
	function TrimArrayValues(&$value, $key) {
		$value = trim($value);
	}
}
