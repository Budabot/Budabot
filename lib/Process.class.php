<?php

/**
 * The Process class executes new processes. 
 */
class Process {
	
	private $processResource;
	private $command = '';
	private $descriptorspec = array();
	private $pipes = array();
	private $workingDir = null;
	
	/**
	 * Class constructor.
	 */
	public function __construct() {
		$this->reset();
	}
	
	/**
	 * Sets the command which should be excuted.
	 */
	public function setCommand($command) {
		$this->command = $command;
	}

	/**
	 * Sets how the process's pipes should be handled.
	 */
	public function setDescriptorspec($spec) {
		$this->descriptorspec = $spec;
	}

	/**
	 * Sets how the process's pipes should be handled.
	 */
	public function setWorkingDir($spec) {
		$this->workingDir = $spec;
	}

	/**
	 * Calling this method will start the process.
	 *
	 * Call stop() to terminate the process.
	 */
	public function start() {
		// do nothing if process is already running
		if ($this->isRunning()) {
			return true;
		}
		
		$this->reset();
		
		// start the process
		$this->processResource = proc_open(
			$this->command,
			$this->descriptorspec,
			$this->pipes,
			$this->workingDir,
			null, // use same env environment as current script
			array( 'bypass_shell' => true )
		);

		return is_resource($this->processResource);
	}
	
	/**
	 * Calling this method terminates the running process.
	 */
	public function stop() {
		if ($this->isRunning()) {
			$this->reset();
		}
	}
	
	/**
	 * Returns true if the process is running.
	 */
	public function isRunning() {
		if (is_resource($this->processResource)) {
			$status = proc_get_status($this->processResource);
			if ($status) {
				return $status['running'];
			}
		}
		return false;
	}
	
	/**
	 * Closes handles and terminates the running
	 * process if any and resets values back to default.
	 */
	private function reset() {

		// close any open pipes
		forEach($this->pipes as $pipe) {
			fclose($pipe);
		}

		// on linux proc_open() runs the program always inside
		// sh-shell, no matter if bypass_shell is set or not, 
		// so we need to kill also the shell's child processes in
		// order to succesfully kill the main process
		// code from: http://www.php.net/manual/en/function.proc-terminate.php#81353
		if (function_exists('posix_kill')) {
			$status = @proc_get_status($this->processResource);
			if($status !== false && $status['running'] == true) {
				//get the parent pid of the process we want to kill
				$ppid = $status['pid'];
				//use ps to get all the children of this process, and kill them
				$pids = preg_split('/\s+/', `ps -o pid --no-heading --ppid $ppid`);
				foreach($pids as $pid) {
					if(is_numeric($pid)) {
						posix_kill($pid, 9); //9 is the SIGKILL signal
					}
				}
			}
		}

		// close handles
		@proc_terminate($this->processResource);
		@proc_close($this->processResource);
		
		// reset values
		$this->processResource = null;
		$this->pipes = array();
	}
}
