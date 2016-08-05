<?php

namespace Budabot\User\Modules;

/**
 * @Instance
 *
 * Commands this class contains:
 *	@DefineCommand(
 *		command     = 'check',
 *		accessLevel = 'all',
 *		description = 'Checks who of the raidgroup is in the area',
 *      help        = 'check.txt'
 *	)
 */
class ChatCheckController {

	/** @Inject */
	public $db;

	/** @Inject */
	public $text;

	const CHANNEL_TYPE = "priv";

	/**
	 * This command handler checks who of the raidgroup is in the area.
	 * @HandlesCommand("check")
	 * @Matches("/^check$/i")
	 */
	public function checkAllCommand($message, $channel, $sender, $sendto, $args) {
		$data = $this->db->query("SELECT name FROM online WHERE added_by = '<myname>' AND channel_type = ?", self::CHANNEL_TYPE);
		forEach ($data as $row) {
			$content .= " \\n /assist $row->name";
		}

		$list = $this->text->make_chatcmd("Check Players", "/text AssistAll: $content");
		$msg = $this->text->makeBlob("Check Players In Vicinity", $list);
		$sendto->reply($msg);
	}
}
