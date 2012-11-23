<?php

require_once 'SettingHandler.class.php';

class TextSettingHandler extends SettingHandler {

	public function __construct($row) {
		parent::__construct($row);
	}
	
	/**
	 * @return String
	 */
	function getDescription() {
		$msg = "For this setting you can enter any text you want (max. 255 chararacters).\n";
		$msg .= "To change this setting:\n\n";
		$msg .= "<highlight>/tell <myname> settings save {$this->row->name} 'text'<end>\n\n";
		return $msg;
	}
	
	/**
	 * @return String of new value or false if $newValue is invalid
	 */
	function save($newValue) {
		if (strlen($newValue) > 255) {
			throw new Exception("Your text can not be longer than 255 characters.");
		} else {
			return $newValue;
		}
	}
}