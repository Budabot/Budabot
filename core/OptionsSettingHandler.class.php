<?php

require_once 'SettingHandler.class.php';

class OptionsSettingHandler extends SettingHandler {
	
	public function __construct($row) {
		parent::__construct($row);
	}
	
	/**
	 * @return String
	 */
	function getDescription() {
		$msg = "For this setting you must choose one of the options from the list below.\n\n";
		return $msg;
	}
	
	/**
	 * @return String of new value or false if $newValue is invalid
	 */
	function save($newValue) {
	
	}
}