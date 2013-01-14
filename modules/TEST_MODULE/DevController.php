<?php

/**
 * Authors: 
 *	- Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'listen', 
 *		accessLevel = 'admin', 
 *		description = 'Have the bot listen for commands in a channel', 
 *		help        = 'listen.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'unlisten', 
 *		accessLevel = 'admin', 
 *		description = 'Have the bot stop listening for commands in a channel', 
 *		help        = 'listen.txt'
 *	)
 */
class DevController extends AutoInject {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;
	
	private $listen = null;
	
	/**
	 * @Setup
	 */
	public function setup() {
		
	}
	
	/**
	 * @HandlesCommand("listen")
	 * @Matches("/^listen (.+)$/i")
	 */
	public function listenCommand($message, $channel, $sender, $sendto, $args) {
		$name = ucfirst(strtolower($args[1]));
		$uid = $this->chatBot->get_uid($name);
		if (!$uid) {
			$msg = "<highlight>$name<end> does not exist.";
			$sendto->reply($msg);
			return;
		}
		
		$this->listen = $name;

		$msg = "Now listening to channel <highlight>$name<end>.";
		$sendto->reply($msg);
	}
	
	/**
	 * @Event("extpriv")
	 * @Description("Listen for commands in an external private channel")
	 */
	public function externalPrivateChannelCommandEvent($eventObj) {
		if ($this->listen == $eventObj->channel) {
			$message = $eventObj->message;
			if ($message[0] == $this->settingManager->get("symbol") && strlen($message) > 1) {
				$type = 'priv';
				$sender = $eventObj->sender;
				$message = substr($message, 1);
				$sendto = new PrivateChannelCommandReply($this->chatBot, $eventObj->channel);
				$this->commandManager->process($type, $message, $sender, $sendto);
			}
		}
	}
}
