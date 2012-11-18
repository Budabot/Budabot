<?php

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
	 * @Matches("/^check all$/i")
	 */
	public function checkAllCommand($message, $channel, $sender, $sendto, $args) {
		$data = $this->db->query("SELECT name FROM online WHERE added_by = '<myname>' AND channel_type = ?", self::CHANNEL_TYPE);
		forEach ($data as $row) {
			$content .= " \\n /assist $row->name";
		}

		$list = $this->text->make_chatcmd("Click here to check who is here", "/text AssistAll: $content");
		$msg = $this->text->make_blob("Check on all", $list);
		$sendto->reply($msg);
	}

	/**
	 * This command handler checks by profession of who of the raidgroup is in the area.
	 * @HandlesCommand("check")
	 * @Matches("/^check prof$/i")
	 */
	public function checkProfCommand($message, $channel, $sender, $sendto, $args) {
		$list = '';
		$data = $this->db->query("SELECT o.name, p.profession
			FROM online o LEFT JOIN players p ON (o.name = p.name AND p.dimension = '<dim>')
			WHERE added_by = '<myname>' AND channel_type = ? ORDER BY `profession` DESC", self::CHANNEL_TYPE);
		forEach ($data as $row) {
			$prof[$row->profession] .= " \\n /assist $row->name";
		}

		ksort($prof);

		forEach ($prof as $key => $value) {
			$list .= $this->text->make_chatcmd("Click here to check $key", "/text Assist $key: $value") . "\n";
		}

		$msg = $this->text->make_blob("Check by profession", $list);
		$sendto->reply($msg);
	}

	/**
	 * This command handler checks by organization of who of the raidgroup is in the area.
	 * @HandlesCommand("check")
	 * @Matches("/^check org$/i")
	 */
	public function checkOrgCommand($message, $channel, $sender, $sendto, $args) {
		$list = '';
		$data = $this->db->query("SELECT o.name, p.guild FROM online o LEFT JOIN
			players p ON (o.name = p.name AND p.dimension = '<dim>')
			WHERE added_by = '<myname>' AND channel_type = ? ORDER BY `guild` DESC", self::CHANNEL_TYPE);
		forEach ($data as $row) {
			if ($row->guild == "") {
				$org["Non orged"] .= " \\n /assist $row->name";
			} else {
				$org[$row->guild] .= " \\n /assist $row->name";
			}
		}

		ksort($org);

		forEach ($org as $key => $value) {
			$list .= $this->text->make_chatcmd("Click here to check $key", "/text Assist $key: $value") . "\n";
		}

		$msg = $this->text->make_blob("Check by Organization", $list);
		$sendto->reply($msg);
	}

}
