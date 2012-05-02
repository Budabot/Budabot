<?php

/**
 * The ChatSay class allows user to send messages to either org channel or to
 * private (guest) channel.
 */
class ChatSay {
	
	/** @Inject */
	public $chatBot;
	
	/**
	 * @Subcommand("say org (.+)")
	 * @AccessLevel("all")
	 * @Description("Sends message to org chat")
	 * @Matches("/^say org (.+)$/si")
	 */
	public function sayOrgCommand($message, $channel, $sender, $sendto, $arr) {
		$this->chatBot->sendGuild("$sender: $arr[1]");
	}

	/**
	 * @Subcommand("say priv (.+)")
	 * @AccessLevel("all")
	 * @Description("Sends message to private channel")
	 * @Matches("/^say priv (.+)$/si")
	 */
	public function sayPrivCommand($message, $channel, $sender, $sendto, $arr) {
		$this->chatBot->sendPrivate("$sender: $arr[1]");
	}
}
