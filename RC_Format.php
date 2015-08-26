<?php

class RC_Format {

	public static function TextToSql($value) {
		$value = trim($value);
		$sql = $value == '' ? 'NULL' : "'$value'";
		return $sql;
	}
	
	public static function TrimArrayValues(&$value, $key) {
		$value = trim($value);
	}
}
