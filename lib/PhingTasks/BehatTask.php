<?php

/**
 * Runs Behat.
 *
 * @author Marebone
 */
class BehatTask extends Task {
	private $workingDir = '.';
	private $executable = 'behat';

	/**
	 * Sets path to Behat's executable.
	 */
	function setExecutable($path) {
		$this->executable = realpath($path);
	}
	
	/**
	 * Sets path to the working directory where the behat will be ran.
	 */
	function setWorkingDir($dir) {
		$this->workingDir = realpath($dir);
	}

	/**
	 * The main entry point.
	 *
	 * @throws BuildException
	 */
	function main() {
		if (!$this->workingDir) {
			throw new BuildException("Working dir path is invalid");
		}
		if (!$this->executable) {
			throw new BuildException("Executable path is incorrect");
		}
		$pipes = array();
		$process = proc_open($this->executable, array(), $pipes, $this->workingDir);

		if (!is_resource($process)) {
			throw new BuildException("Failed to start Behat");
		}

		// test if the behat failed or not
		$code = proc_close($process);
		if ($code) {
			throw new BuildException("Behat reported failure");
		}
	}
}
