<?php

/**
 * Authors: 
 *  - Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'silence', 
 *		accessLevel = 'mod', 
 *		description = 'Silence commands in a particular channel', 
 *		help        = 'silence.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'unsilence', 
 *		accessLevel = 'mod', 
 *		description = 'Unsilence commands in a particular channel', 
 *		help        = 'silence.txt'
 *	)
 */
class SilenceController extends AutoInject {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;
	
	private $silencedCommands = array();
	
	/**
	 * @HandlesCommand("silence")
	 * @Matches("/^silence (.+) (.+)$/i")
	 */
	public function silenceCommand($message, $channel, $sender, $sendto, $args) {
		$command = strtolower($args[1]);
		$channel = strtolower($args[2]);
		
		$data = $this->commandManager->get($command, $channel);
		if (count($data) == 0) {
			$msg = "Could not find command <highlight>$command<end> for channel <highlight>$channel<end>.";
		} else if ($this->isSilencedCommand($data[0])){
			$msg = "Command <highlight>$command<end> for channel <highlight>$channel<end> has already been silenced.";
		} else {
			$this->addSilencedCommand($data[0], $command, $channel);
			$msg = "Command <highlight>$command<end> for channel <highlight>$channel<end> has been silenced.";
		}
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("unsilence")
	 * @Matches("/^unsilence (.+) (.+)$/i")
	 */
	public function unsilenceCommand($message, $channel, $sender, $sendto, $args) {
		$command = strtolower($args[1]);
		$channel = strtolower($args[2]);
		
		$data = $this->commandManager->get($command, $channel);
		if (count($data) == 0) {
			$msg = "Could not find command <highlight>$command<end> for channel <highlight>$channel<end>.";
		} else if (!$this->isSilencedCommand($data[0])){
			$msg = "Command <highlight>$command<end> for channel <highlight>$channel<end> has not been silenced.";
		} else {
			$this->removeSilencedCommand($data[0]);
			$msg = "Command <highlight>$command<end> for channel <highlight>$channel<end> has been unsilenced.";
		}
		$sendto->reply($msg);
	}
	
	public function nullCommand($message, $channel, $sender, $sendto, $args) {
		$this->logger->log('DEBUG', "Silencing command '$message' for channel '$channel'");
	}
	
	public function addSilencedCommand($row, $command, $channel) {
		$filename = "SilenceController.nullCommand";
		$this->commandManager->activate($channel, $filename, $command, 'all');
		$this->silencedCommands []= $row;
	}
	
	public function isSilencedCommand($row) {
		return in_array($row, $this->silencedCommands)
	}
	
	public function removeSilencedCommand($row) {
		$this->commandManager->activate($row->type, $row->file, $row->cmd, $row->admin);
		forEach ($this->silencedCommands as $key => $cmd) {
			if ($cmd->type == $row->type && $cmd->cmd == $row->cmd) {
				unset($this->silencedCommands[$key]);
				break;
			}
		}
	}

	/**
	 * @Event("connect")
	 * @Description("Overwrite command handlers for silenced commands")
	 */
	public function overwriteCommandHandlersEvent($eventObj) {
		
	}
}

?>
