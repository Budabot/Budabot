<?php

/**
 * @Instance
 *
 * Authors:
 *  - Legendadv (RK2)
 *  - Derroylo (RK2)
 *  - Marebone (RK2)
 *
 * The ChatSayController class allows user to send messages to either org
 * channel or to private (guest) channel.
 *
 * Commands this class contains:
 *	@DefineCommand(
 *		command     = 'say',
 *		accessLevel = 'all',
 *		description = 'Sends message to org chat or private chat',
 *		help        = 'say.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'tell',
 *		accessLevel = 'rl',
 *		description = 'Repeats a message 3 times',
 *      help        = 'tell.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'cmd',
 *		accessLevel = 'rl',
 *		description = 'Creates a highly visible message',
 *      help        = 'cmd.txt'
 *	)
 */
class ChatSayController {

	/** @Inject */
	public $chatBot;

	/**
	 * This command handler sends message to org chat.
	 * @HandlesCommand("say")
	 * @Matches("/^say org (.+)$/si")
	 */
	public function sayOrgCommand($message, $channel, $sender, $sendto, $args) {
		$this->chatBot->sendGuild("$sender: $args[1]");
	}

	/**
	 * This command handler sends message to private channel.
	 * @HandlesCommand("say")
	 * @Matches("/^say priv (.+)$/si")
	 */
	public function sayPrivCommand($message, $channel, $sender, $sendto, $args) {
		$this->chatBot->sendPrivate("$sender: $args[1]");
	}

	/**
	 * This command handler creates a highly visible message.
	 * @HandlesCommand("cmd")
	 * @Matches("/^cmd (.+)$/i")
	 */
	public function cmdCommand($message, $channel, $sender, $sendto, $args) {
		$msg = "\n<yellow>---------------------\n<red>$args[1]<end>\n<yellow>---------------------";

		if ($channel == 'msg') {
			$this->chatBot->sendGuild($msg, true);
			$this->chatBot->sendPrivate($msg, true);
		} else {
			$sendto->reply($msg, true);
		}
	}

	/**
	 * This command handler repeats a message 3 times.
	 * @HandlesCommand("tell")
	 * @Matches("/^tell (.+)$/i")
	 */
	public function tellCommand($message, $channel, $sender, $sendto, $args) {
		if ($channel == 'guild' || $channel == 'msg') {
			$this->chatBot->sendGuild("<yellow>$args[1]<end>", true);
			$this->chatBot->sendGuild("<yellow>$args[1]<end>", true);
			$this->chatBot->sendGuild("<yellow>$args[1]<end>", true);
		}

		if ($channel == 'priv' || $channel == 'msg') {
			$this->chatBot->sendPrivate("<yellow>$args[1]<end>", true);
			$this->chatBot->sendPrivate("<yellow>$args[1]<end>", true);
			$this->chatBot->sendPrivate("<yellow>$args[1]<end>", true);
		}
	}

}
