<?php

require_once 'phing/tasks/ext/git/GitLogTask.php';

class TimestampToFormatTask extends Task
{
	private $outputProperty;
	private $format;
	private $timestamp;

	/**
	 * Sets the name of the property to use.
	 */
	function setOutputProperty($name) {
		$this->outputProperty = $name;
	}

	/**
	 * Sets the name of the property to use.
	 */
	function setFormat($format) {
		$this->format = $format;
	}

	/**
	 * Sets the name of the property to use.
	 */
	function setTimestamp($timestamp) {
		$this->timestamp = $timestamp;
	}

	public function main() {
	var_dump($this->timestamp);
		$value = gmdate($this->format, intval($this->timestamp));
		$this->project->setProperty($this->outputProperty, $value);
	}
}
