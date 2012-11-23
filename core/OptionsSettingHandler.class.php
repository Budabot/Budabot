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
		$options = explode(";", $this->row->options);
		if ($this->row->intoptions != '') {
			$intoptions = explode(";", $this->row->intoptions);
			if (in_array($newValue, $intoptions)) {
				return $newValue;
			} else {
				throw new Exception("This is not a correct option for this setting.");
			}
		} else {
			if (in_array($newValue, $options)) {
				return $newValue;
			} else {
				throw new Exception("This is not a correct option for this setting.");
			}
		}
	}
}