<?php

namespace budabot\user\modules;

use \budabot\core\AutoInject;

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
	
	const NULL_COMMAND_HANDLER = "SilenceController.nullCommand";
	
	/**
	 * @Setup
	 */
	public function setup() {
		$this->db->loadSQLFile($this->moduleName, "silence_cmd");
	}
	
	/**
	 * @HandlesCommand("silence")
	 * @Matches("/^silence$/i")
	 */
	public function silenceCommand($message, $channel, $sender, $sendto, $args) {
		$sql = "SELECT * FROM silence_cmd_<myname> ORDER BY cmd, channel";
		$data = $this->db->query($sql);
		if (count($data) == 0) {
			$msg = "No commands have been silenced.";
		} else {
			$blob = '';
			forEach ($data as $row) {
				$unsilenceLink = $this->text->make_chatcmd("Unsilence", "/tell <myname> unsilence $row->cmd $row->channel");
				$blob .= "<highlight>$row->cmd<end> ($row->channel) - $unsilenceLink\n";
			}
			$msg = $this->text->make_blob("Silenced Commands", $blob);
		}
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("silence")
	 * @Matches("/^silence (.+) (.+)$/i")
	 */
	public function silenceAddCommand($message, $channel, $sender, $sendto, $args) {
		$command = strtolower($args[1]);
		$channel = strtolower($args[2]);
		
		$data = $this->commandManager->get($command, $channel);
		if (count($data) == 0) {
			$msg = "Could not find command <highlight>$command<end> for channel <highlight>$channel<end>.";
		} else if ($this->isSilencedCommand($data[0])){
			$msg = "Command <highlight>$command<end> for channel <highlight>$channel<end> has already been silenced.";
		} else {
			$this->addSilencedCommand($data[0]);
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
	
	public function addSilencedCommand($row) {
		$this->commandManager->activate($row->type, self::NULL_COMMAND_HANDLER, $row->cmd, 'all');
		$sql = "INSERT INTO silence_cmd_<myname> (cmd, channel) VALUES (?, ?)";
		$this->db->exec($sql, $row->cmd, $row->type);
	}
	
	public function isSilencedCommand($row) {
		$sql = "SELECT * FROM silence_cmd_<myname> WHERE cmd = ? AND channel = ?";
		$row = $this->db->queryRow($sql, $row->cmd, $row->type);
		return $row !== null;
	}
	
	public function removeSilencedCommand($row) {
		$this->commandManager->activate($row->type, $row->file, $row->cmd, $row->admin);
		$sql = "DELETE FROM silence_cmd_<myname> WHERE cmd = ? AND channel = ?";
		$this->db->exec($sql, $row->cmd, $row->type);
	}

	/**
	 * @Event("connect")
	 * @Description("Overwrite command handlers for silenced commands")
	 */
	public function overwriteCommandHandlersEvent($eventObj) {
		$sql = "SELECT * FROM silence_cmd_<myname>";
		$data = $this->db->query($sql);
		forEach ($data as $row) {
			$this->commandManager->activate($row->channel, self::NULL_COMMAND_HANDLER, $row->cmd, 'all');
		}
	}
}

?>
