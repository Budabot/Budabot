<?php

namespace Budabot\User\Modules;

/**
 * Authors: 
 *  - Tyrence (RK2)
 *  - Mindrila (RK1)
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = "inactivemem", 
 *		accessLevel = "guild", 
 *		description = "Check for inactive members", 
 *		help        = "inactivemem.txt"
 *	)
 */
class InactiveMemberController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;
	
	/** @Inject */
	public $db;
	
	/** @Inject */
	public $text;
	
	/** @Inject */
	public $util;
	
	/**
	 * @Setup
	 */
	public function setup() {

	}

	/**
	 * @HandlesCommand("inactivemem")
	 * @Matches("/^inactivemem ([a-z0-9]+)/i")
	 */
	public function inactivememCommand($message, $channel, $sender, $sendto, $args) {
		$time = $this->util->parseTime($args[1]);
		if ($time < 1) {
			$msg = "You must enter a valid time parameter.";
			$sendto->reply($msg);
			return;
		}

		$timeString = $this->util->unixtimeToReadable($time, false);
		$time = time() - $time;

		$data = $this->db->query("SELECT * FROM org_members_<myname> o LEFT JOIN alts a ON o.name = a.alt WHERE `mode` != 'del' AND `logged_off` < ?  ORDER BY o.name", $time);

		if (count($data) == 0) {
			$sendto->reply("There are no members in the org roster.");
			return;
		}

		$numinactive = 0;
		$highlight = 0;

		$blob = "Org members who have not logged off since <highlight>{$timeString}<end> ago.\n\n";

		forEach ($data as $row) {
			$logged = 0;
			$main = $row->main;
			if ($row->main != "") {
				$data1 = $this->db->query("SELECT * FROM alts a JOIN org_members_<myname> o ON a.alt = o.name WHERE `main` = ?", $row->main);
				forEach ($data1 as $row1) {
					if ($row1->logged_off > $time) {
						continue 2;
					}

					if ($row1->logged_off > $logged) {
						$logged = $row1->logged_off;
						$lasttoon = $row1->name;
					}
				}
			}

			$numinactive++;
			$alts = $this->text->makeChatcmd("Alts", "/tell <myname> alts {$row->name}");
			$logged = $row->logged_off;
			$lasttoon = $row->name;
			$lastseen = ($row->logged_off == 0) ? "never" : $this->util->date($logged);

			$player = $row->name."; Main: $main; [{$alts}]\nLast seen on [$lasttoon] on " . $lastseen . "\n\n";
			if ($highlight == 1) {
				$blob .= "<highlight>$player<end>";
				$highlight = 0;
			} else {
				$blob .= $player;
				$highlight = 1;
			}
		}
		$msg = $this->text->makeBlob("$numinactive Inactive Org Members", $blob);
		$sendto->reply($msg);
	}
}

