<?php
/**
 * Authors: 
 *  - Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'findorg', 
 *		accessLevel = 'all', 
 *		description = "Find orgs by name", 
 *		help        = 'findorg.txt'
 *	)
 */
class FindOrgController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;
	
	/** @Inject */
	public $db;

	/** @Inject */
	public $chatBot;
	
	/** @Inject */
	public $text;

	/**
	 * @HandlesCommand("findorg")
	 * @Matches("/^findorg (.+) (\d)$/i")
	 * @Matches("/^findorg (.+)$/i")
	 */
	public function findOrgCommand($message, $channel, $sender, $sendto, $args) {
		$guild_name = $args[1];

		$dimension = $this->chatBot->vars['dimension'];
		if (count($args) == 3) {
			$dimension = $args[2];
		}

		$sql = "SELECT DISTINCT guild, guild_id, CASE WHEN guild_id = '' THEN 0 ELSE 1 END AS sort FROM players WHERE guild LIKE ? AND dimension = ? ORDER BY sort DESC, guild ASC LIMIT 30";
		$data = $this->db->query($sql, '%'.$guild_name.'%', $dimension);
		if (count($data) > 0) {
			$blob = '';

			forEach ($data as $row) {
				if ($row->guild_id != 0) {
					$whoisorg = $this->text->make_chatcmd('Whoisorg', "/tell <myname> whoisorg {$row->guild_id} $dimension");
					if ($dimension == $this->chatBot->vars['dimension']) {
						$orglist = $this->text->make_chatcmd('Orglist', "/tell <myname> orglist {$row->guild_id}");
						$orgranks = $this->text->make_chatcmd('Orgranks', "/tell <myname> orgranks {$row->guild_id}");
						$orgmembers = $this->text->make_chatcmd('Orgmembers', "/tell <myname> orgmembers {$row->guild_id}");
						$tower_attacks = $this->text->make_chatcmd('Tower Attacks', "/tell <myname> attacks org {$row->guild}");
						$tower_victories = $this->text->make_chatcmd('Tower Victories', "/tell <myname> victory org {$row->guild}");
						$blob .= "{$row->guild} ({$row->guild_id}) [$whoisorg] [$orglist] [$orgranks] [$orgmembers] [$tower_attacks] [$tower_victories]\n";
					} else {
						$blob .= "{$row->guild} ({$row->guild_id}) [$whoisorg]\n";
					}
				} else {
					$blob .= "{$row->guild}\n";
				}
			}

			$msg = $this->text->make_blob("Org Search Results for '{$guild_name}' on RK{$dimension}", $blob);
		} else {
			$msg = "No matches found.";
		}
		$sendto->reply($msg);
	}
}

