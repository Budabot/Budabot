<?php

require_once 'SettingHandler.class.php';

class SettingHandler {
	/** @Inject */
	public $text;

	protected $row;

	public function __construct($row) {
		$this->row = $row;
	}

	/**
	 * @return String
	 */
	public function displayValue() {
		if ($this->row->intoptions != "") {
			$options = explode(";", $this->row->options);
			$intoptions = explode(";", $this->row->intoptions);
			$intoptions2 = array_flip($intoptions);
			$key = $intoptions2[$this->row->value];
			return "<highlight>{$options[$key]}<end>";
		} else {
			return "<highlight>" . htmlspecialchars($this->row->value) . "<end>";
		}
	}
	
	/**
	 * @return String or false if no options are available
	 */
	public function getOptions() {
		if ($this->row->options != '') {
			$options = explode(";", $this->row->options);
		}
		if ($this->row->intoptions != '') {
			$intoptions = explode(";", $this->row->intoptions);
			$options_map = array_combine($intoptions, $options);
		}
		if ($options) {
			$msg = "Predefined Options:\n";
			if ($intoptions) {
				forEach ($options_map as $key => $label) {
					$save_link = $this->text->make_chatcmd('Select', "/tell <myname> settings save {$this->row->name} {$key}");
					$msg .= "<tab> <highlight>{$label}<end> ({$save_link})\n";
				}
			} else {
				forEach ($options as $char) {
					$save_link = $this->text->make_chatcmd('Select', "/tell <myname> settings save {$this->row->name} {$char}");
					$msg .= "<tab> <highlight>{$char}<end> ({$save_link})\n";
				}
			}
		}
		return $msg;
	}
	
	/**
	 * @return String of new value or false if $newValue is invalid
	 */
	public function save($newValue) {
		return $newValue;
	}
}