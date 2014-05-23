<?php

namespace Budabot\User\Modules;

/**
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'assist',
 *		accessLevel = 'all',
 *		description = 'Shows the assist macro',
 *		help        = 'assist.txt',
 *      alias       = 'callers'
 *	)
 *	@DefineCommand(
 *		command     = 'assist .+', 
 *		accessLevel = 'rl',
 *		description = 'Sets a new assist',
 *		help        = 'assist.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'heal',
 *      alias       = 'healassist',
 *		accessLevel = 'all',
 *		description = 'Creates/shows an Doc Assist macro',
 *		help        = 'healassist.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'heal .+',
 *		accessLevel = 'rl',
 *		description = 'Sets a new Doc assist',
 *		help        = 'healassist.txt'
 *	)
 */
class ChatAssistController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;
	
	/** @Inject */
	public $chatBot;
	
	/** @Inject */
	public $text;
	
	/**
	 * Contains the assist macro message.
	 */
	private $assistMessage;

	/**
	 * Contains the heal assist macro message.
	 */
	private $healMessage;

	/**
	 * @HandlesCommand("assist")
	 * @Matches("/^assist$/i")
	 */
	public function assistCommand($message, $channel, $sender, $sendto, $args) {
		if (!isset($this->assistMessage)) {
			$msg = "No assist set.";
			$sendto->reply($msg);
			return;
		} else {
			$sendto->reply($this->assistMessage);

			// send message 2 more times (3 total) if used in private channel
			if ($channel == "priv") {
				$sendto->reply($this->assistMessage);
				$sendto->reply($this->assistMessage);
			}
		}
	}
	
	/**
	 * @HandlesCommand("assist .+")
	 * @Matches("/^assist clear$/i")
	 */
	public function assistClearCommand($message, $channel, $sender, $sendto, $args) {
		$this->assistMessage = null;
		$sendto->reply("Assist has been cleared.");
	}

	/**
	 * @HandlesCommand("assist .+")
	 * @Matches("/^assist (.+)$/i")
	 */
	public function assistSetCommand($message, $channel, $sender, $sendto, $args) {
		$nameArray = explode(' ', $args[1]);
		
		if (count($nameArray) == 1) {
			$name = ucfirst(strtolower($args[1]));
			$uid = $this->chatBot->get_uid($name);
			if (!$uid) {
				$msg = "Character <highlight>$name<end> does not exist.";
				$sendto->reply($msg);
			} else if ($channel == "priv" && !isset($this->chatBot->chatlist[$name])) {
				$msg = "Character <highlight>$name<end> is not in this bot.";
				$sendto->reply($msg);
			}

			$link = $this->text->make_chatcmd("Click here to make an assist $name macro", "/macro $name /assist $name");
			$this->assistMessage = $this->text->make_blob("Assist $name Macro", $link);
		} else {
			forEach ($nameArray as $key => $name) {
				$name = ucfirst(strtolower($name));
				$uid = $this->chatBot->get_uid($name);
				if (!$uid) {
					$msg = "Character <highlight>$name<end> does not exist.";
					$sendto->reply($msg);
				} else if ($channel == "priv" && !isset($this->chatBot->chatlist[$name])) {
					$msg = "Character <highlight>$name<end> is not in this bot.";
					$sendto->reply($msg);
				}

				$nameArray[$key] = "/assist $name";
			}

			// reverse array so that the first character will be the primary assist, and so on
			$nameArray = array_reverse($nameArray);
			$this->assistMessage = '/macro assist ' . implode(" \\n ", $nameArray);
		}

		$sendto->reply($this->assistMessage);

		// send message 2 more times (3 total) if used in private channel
		if ($channel == "priv") {
			$sendto->reply($this->assistMessage);
			$sendto->reply($this->assistMessage);
		}
	}

	/**
	 * @HandlesCommand("heal")
	 * @Matches("/^heal$/i")
	 */
	public function healCommand($message, $channel, $sender, $sendto, $args) {
		if (!isset($this->healMessage)) {
			$msg = "No heal assist set.";
			$sendto->reply($msg);
			return;
		} else {
			$sendto->reply($this->healMessage);

			// send message 2 more times (3 total) if used in private channel
			if ($channel == "priv") {
				$sendto->reply($this->healMessage);
				$sendto->reply($this->healMessage);
			}
		}
	}
	
	/**
	 * @HandlesCommand("heal .+")
	 * @Matches("/^heal clear$/i")
	 */
	public function healClearCommand($message, $channel, $sender, $sendto, $args) {
		$this->assistMessage = null;
		$sendto->reply("Heal assist has been cleared.");
	}

	/**
	 * @HandlesCommand("heal .+")
	 * @Matches("/^heal (.+)$/i")
	 */
	public function healSetCommand($message, $channel, $sender, $sendto, $args) {
		$nameArray = explode(' ', $args[1]);

		if (count($nameArray) == 1) {
			$name = ucfirst(strtolower($args[1]));
			$uid = $this->chatBot->get_uid($name);

			if (!$uid) {
				$msg = "Character <highlight>$name<end> does not exist.";
				$sendto->reply($msg);
			}

			$link = $this->text->make_chatcmd("Click here to make a heal assist macro", "/macro heal /assist $name");
			$this->healMessage = $this->text->make_blob("Heal Assist Macro", $link);
		} else {
			forEach ($nameArray as $key => $name) {
				$name = ucfirst(strtolower($name));
				$uid = $this->chatBot->get_uid($name);

				if (!$uid) {
					$msg = "Character <highlight>$name<end> does not exist.";
					$sendto->reply($msg);
				}
				$nameArray[$key] = "/assist $name";
			}

			// reverse array so that the first character will be the primary assist, and so on
			$nameArray = array_reverse($nameArray);
			$this->healMessage = '/macro heal ' . implode(" \\n ", $nameArray);
		}

		$sendto->reply($this->healMessage);

		// send message 2 more times (3 total) if used in private channel
		if ($channel == "priv") {
			$sendto->reply($this->healMessage);
			$sendto->reply($this->healMessage);
		}
	}
}
