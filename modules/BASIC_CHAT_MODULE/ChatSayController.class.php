<?php

/**
 * The ChatSayController class allows user to send messages to either org
 * channel or to private (guest) channel.
 *
 * Commands this class contains:
 *	@DefineCommand(
 *		command     = 'say',
 *		accessLevel = 'all',
 *		description = 'Sends message to org chat or private chat'
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
}
