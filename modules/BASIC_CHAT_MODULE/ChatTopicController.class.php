<?php

namespace Budabot\User\Modules;

/**
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'topic', 
 *		accessLevel = 'all', 
 *		description = 'Shows Topic', 
 *		help        = 'topic.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'topic .+', 
 *		accessLevel = 'rl', 
 *		description = 'Changes Topic', 
 *		help        = 'topic.txt'
 *	)
 */
class ChatTopicController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;
	
	/** @Inject */
	public $chatBot;
	
	/** @Inject */
	public $settingManager;
	
	/** @Inject */
	public $util;
	
	/** @Inject */
	public $chatRallyController;

	/** @Inject */
	public $chatLeaderController;

	/**
	 * @Setting("topic")
	 * @Description("Topic for Priv Channel")
	 * @Visibility("noedit")
	 * @Type("text")
	 */
	public $defaultTopic = '';

	/**
	 * @Setting("topic_setby")
	 * @Description("Character who set the topic")
	 * @Visibility("noedit")
	 * @Type("text")
	 */
	public $defaultTopicSetBy = '';

	/**
	 * @Setting("topic_time")
	 * @Description("Time the topic was set")
	 * @Visibility("noedit")
	 * @Type("text")
	 */
	public $defaultTopicTime = '';

	/**
	 * This command handler shows topic.
	 * @HandlesCommand("topic")
	 * @Matches("/^topic$/i")
	 */
	public function topicCommand($message, $channel, $sender, $sendto, $args) {
		if ($this->settingManager->get('topic') == '') {
			$msg = 'No topic set.';
		} else {
			$msg = $this->buildTopicMessage();
		}

		$sendto->reply($msg);
	}

	/**
	 * This command handler clears topic.
	 * @HandlesCommand("topic .+")
	 * @Matches("/^topic clear$/i")
	 */
	public function topicClearCommand($message, $channel, $sender, $sendto, $args) {
		if (!$this->chatLeaderController->checkLeaderAccess($sender)) {
			$sendto->reply("You must be Raid Leader to use this command.");
			return;
		}

		$this->setTopic($sender, "");
		$msg = "Topic has been cleared.";
		$sendto->reply($msg);
	}

	/**
	 * This command handler sets topic.
	 * @HandlesCommand("topic .+")
	 * @Matches("/^topic (.+)$/i")
	 */
	public function topicSetCommand($message, $channel, $sender, $sendto, $args) {
		if (!$this->chatLeaderController->checkLeaderAccess($sender)) {
			$sendto->reply("You must be Raid Leader to use this command.");
			return;
		}

		$this->setTopic($sender, $args[1]);
		$msg = "Topic has been updated.";
		$sendto->reply($msg);
	}

	/**
	 * @Event("logOn")
	 * @Description("Shows topic on logon of members")
	 */
	public function logonEvent($eventObj) {
		if ($this->settingManager->get('topic') == '') {
			return;
		}
		if (isset($this->chatBot->guildmembers[$eventObj->sender]) && $this->chatBot->isReady()) {
			$msg = $this->buildTopicMessage();
			$this->chatBot->sendTell($msg, $eventObj->sender);
		}
	}

	/**
	 * @Event("joinPriv")
	 * @Description("Shows topic when someone joins the private channel")
	 */
	public function joinPrivEvent($eventObj) {
		if ($this->settingManager->get('topic') == '') {
			return;
		}
		$msg = $this->buildTopicMessage();
		$this->chatBot->sendTell($msg, $eventObj->sender);
	}
	
	public function setTopic($name, $msg) {
		$this->settingManager->save("topic_time", time());
		$this->settingManager->save("topic_setby", $name);
		$this->settingManager->save("topic", $msg);

		if (empty($msg)) {
			$this->chatRallyController->clear();
		}
	}

	/**
	 * Builds current topic information message and returns it.
	 */
	public function buildTopicMessage() {
		$date_string = $this->util->unixtimeToReadable(time() - $this->settingManager->get('topic_time'), false);
		$topic = $this->settingManager->get('topic');
		$set_by = $this->settingManager->get('topic_setby');
		$msg = "Topic: <highlight>{$topic}<end> [set by <highlight>{$set_by}<end>][<highlight>{$date_string} ago<end>]";
		return $msg;
	}
}
