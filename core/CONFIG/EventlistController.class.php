<?php
/**
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command       = 'eventlist',
 *		accessLevel   = 'guild',
 *		description   = 'Shows a list of all events on the bot',
 *		help          = 'eventlist.txt',
 *		defaultStatus = '1'
 *	)
 */
class EventlistController {

	/** @Inject */
	public $text;

	/** @Inject */
	public $db;

	/**
	 * This command handler shows a list of all events on the bot.
	 * Additionally, event type can be provided to show only events of that type.
	 *
	 * @HandlesCommand("eventlist")
	 * @Matches("/^eventlist$/i")
	 * @Matches("/^eventlist (.+)$/i")
	 */
	public function eventlistCommand($message, $channel, $sender, $sendto, $args) {
		if (isset($args[1])) {
			$eventType = str_replace("'", "''", $args[1]);
			$cmdSearchSql = "WHERE type LIKE '%{$eventType}%'";
		}
	
		$sql = "
			SELECT
				type,
				description,
				module,
				file,
				status
			FROM
				eventcfg_<myname>
			$cmdSearchSql
			ORDER BY
				type ASC";
		$data = $this->db->query($sql);
	
		if (count($data) > 0) {
			$blob = '';
			forEach ($data as $row) {
				$on = $this->text->make_chatcmd('ON', "/tell <myname> config event $row->type $row->file enable all");
				$off = $this->text->make_chatcmd('OFF', "/tell <myname> config event $row->type $row->file disable all");
	
				if ($row->status == 1) {
					$status = "<green>Enabled<end>";
				} else {
					$status = "<red>Disabled<end>";
				}
	
				if ($row->description != '') {
					$blob .= "$row->type [$row->module] ($status): $on  $off - ($row->description)\n";
				} else {
					$blob .= "$row->type [$row->module] ($status): $on  $off\n";
				}
			}
	
			$msg = $this->text->make_blob("Event List", $blob);
		} else {
			$msg = "No events could be found for event type '$args[1]'.";
		}
		$sendto->reply($msg);
	}
}
