<?php

namespace Budabot\User\Modules;

/**
 * Authors: 
 *	- Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'orgmembers',
 *		accessLevel = 'guild',
 *		description = 'Show guild members sorted by name',
 *		help        = 'orgmembers.txt'
 *	)
 */
class OrgMembersController {

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
	public $playerManager;
	
	/** @Inject */
	public $guildManager;
	
	/**
	 * @HandlesCommand("orgmembers")
	 * @Matches("/^orgmembers (\d+)$/i")
	 */
	public function orgmembers2Command($message, $channel, $sender, $sendto, $args) {
		$guild_id = $args[1];

		$msg = "Getting org info...";
		$sendto->reply($msg);

		$org = $this->guildManager->get_by_id($guild_id);
		if ($org === null) {
			$msg = "Error in getting the org info. Either org does not exist or AO's server was too slow to respond.";
			$sendto->reply($msg);
			return;
		}

		$sql = "SELECT * FROM players WHERE guild_id = ? AND dimension = '<dim>' ORDER BY name ASC";
		$data = $this->db->query($sql, $guild_id);
		$numrows = count($data);

		$blob = '';

		$currentLetter = '';
		forEach ($data as $row) {
			if ($currentLetter != $row->name[0]) {
				$currentLetter = $row->name[0];
				$blob .= "\n\n<header2>$currentLetter<end>\n";
			}

			$blob .= "<tab><highlight>{$row->name}, {$row->guild_rank} (Level {$row->level}";
			if ($row->ai_level > 0) {
				$blob .= "<green>/{$row->ai_level}<end>";
			}
			$blob .= ", {$row->gender} {$row->breed} {$row->profession})<end>\n";
		}

		$msg = $this->text->make_blob("Org members for '$org->orgname' ($numrows)", $blob);
		$sendto->reply($msg);
	}
}

