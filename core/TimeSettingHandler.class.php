<?php

require_once 'SettingHandler.class.php';

class TimeSettingHandler extends SettingHandler {
	/** @Inject */
	public $util;
	
	public function __construct($row) {
		parent::__construct($row);
	}

	/**
	 * @return String
	 */
	function displayValue() {
		return "<highlight>" . $this->util->unixtime_to_readable($this->row->value) . "<end>";
	}
	
	/**
	 * @return String
	 */
	function getDescription() {
		$msg = "For this setting you must enter a time value. See <a href='chatcmd:///tell <myname> help budatime'>budatime</a> for info on the format of the 'time' parameter.\n\n";
		$msg .= "To change this setting:\n\n";
		$msg .= "<highlight>/tell <myname> settings save {$this->row->name} 'time'<end>\n\n";
		return $msg;
	}
	
	/**
	 * @return String of new value or false if $newValue is invalid
	 */
	function save($newValue) {
	
	}
}