<?php

require_once 'SettingHandler.class.php';

class NumberSettingHandler extends SettingHandler {

	public function __construct($row) {
		parent::__construct($row);
	}
	
	/**
	 * @return String
	 */
	function getDescription() {
		$msg = "For this setting you can set any number.\n";
		$msg .= "To change this setting: \n\n";
		$msg .= "<highlight>/tell <myname> settings save {$this->row->name} 'number'<end>\n\n";
		return $msg;
	}
	
	/**
	 * @return String of new value or false if $newValue is invalid
	 */
	function save($newValue) {
	
	}
}