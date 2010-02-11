<?php
/**
 *  Custom Field Object
 */
class PanelFields{
	var $id;
	var $displayName;
	var $cssId;
	var $defaultChecked;
	var $isAdvancedField;
	var $forPost;
	var $forPage;
	var $excludeVersion;
	
	function PanelFields($id, $displayName, $cssId, $defaultChecked, $isAdvancedField, $forPost, $forPage, $excludeVersion){
		$this->id = $id;	
		$this->displayName = $displayName;
		$this->cssId = $cssId;
		$this->defaultChecked = $defaultChecked;
		$this->isAdvancedField = $isAdvancedField;
		$this->forPost = $forPost;
		$this->forPage = $forPage;
		$this->excludeVersion = $excludeVersion;
	}
}
