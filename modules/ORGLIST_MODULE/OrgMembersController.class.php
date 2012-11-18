<?php
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
 *	@DefineCommand(
 *		command     = 'orgranks',
 *		accessLevel = 'guild',
 *		description = 'Show guild members sorted by guild rank',
 *		help        = 'orgranks.txt'
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
	public $chatBot;
	
	/** @Inject */
	public $buddylistManager;

	/** @Inject */
	public $text;
	
	/** @Inject */
	public $util;
	
	/** @Inject */
	public $playerManager;
	
	/** @Inject */
	public $guildManager;
	
	/**
	 * @HandlesCommand("orgmembers")
	 * @Matches("/^orgmembers$/i")
	 */
	public function orgmembers1Command($message, $channel, $sender, $sendto, $args) {
		if ($this->chatBot->vars["my_guild_id"] == "") {
			$msg = "The Bot needs to be in a org to show the orgmembers.";
			$sendto->reply($msg);
			return;
		}

		$data = $this->db->query("SELECT * FROM org_members_<myname> o LEFT JOIN players p ON (o.name = p.name AND p.dimension = '<dim>') WHERE `mode` != 'del' ORDER BY o.name");
		$count = count($data);
		if ($count == 0) {
			$msg = "No members recorded.";
			$sendto->reply($msg);
			return;
		}

		$currentLetter = "";
		$blob = '';
		forEach ($data as $row) {
			if ($row->name[0] != $currentLetter) {
				$currentLetter = $row->name[0];
				$blob .= "\n\n<header2>$currentLetter<end>\n";
			}
			
			if ($this->buddylistManager->is_online($row->name) == 1) {
				$logged_off = " :: <highlight>Last logoff:<end> <green>Online<end>";
			} else if ($row->logged_off != "0") {
				$logged_off = " :: <highlight>Last logoff:<end> " . $this->util->date($row->logged_off);
			} else {
				$logged_off = " :: <highlight>Last logoff:<end> <orange>Unknown<end>";
			}

			$prof = $this->util->get_profession_abbreviation($row->profession);

			$blob .= "<tab><highlight>$row->name<end> (Lvl $row->level/<green>$row->ai_level<end>/$prof/$row->guild_rank)$logged_off\n";
		}

		$msg = $this->text->make_blob("<myguild> has $count members currently.", $blob);
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("orgmembers")
	 * @Matches("/^orgmembers ([a-z0-9-]+)$/i")
	 */
	public function orgmembers2Command($message, $channel, $sender, $sendto, $args) {
		if (preg_match("^[0-9]+$", $args[1])) {
			$guild_id = $args[1];
		} else {
			// Someone's name.  Doing a whois to get an orgID.
			$name = ucfirst(strtolower($args[1]));
			$whois = $this->playerManager->get_by_name($name);

			if ($whois === null) {
				$msg = "Could not find character info for $name.";
				$sendto->reply($msg);
				return;
			} else if ($whois->guild_id == 0) {
				$msg = "Character <highlight>$name<end> does not seem to be in an org.";
				$sendto->reply($msg);
				return;
			} else {
				$guild_id = $whois->guild_id;
			}
		}

		$msg = "Getting guild info. Please wait...";
		$sendto->reply($msg);

		$org = $this->guildManager->get_by_id($guild_id);
		if ($org === null) {
			$msg = "Error in getting the Org info. Either org does not exist or AO's server was too slow to respond.";
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
	
	/**
	 * @HandlesCommand("orgranks")
	 * @Matches("/^orgranks$/i")
	 */
	public function orgranks1Command($message, $channel, $sender, $sendto, $args) {
		if ($this->chatBot->vars["my_guild_id"] == "") {
			$msg = "The bot does not belong to an org.";
		} else {
			$sql = "SELECT * FROM org_members_<myname> o LEFT JOIN players p ON (o.name = p.name AND p.dimension = '<dim>') WHERE `mode` != 'del' ORDER BY `guild_rank_id` ASC, o.name ASC";
			$data = $this->db->query($sql);
			$orgname = "<myguild>";
			
			$count = count($data);
			if ($count == 0) {
				$msg = "No org members found.";
			} else {
				$blob = $this->formatOrgRankList($data);

				$msg = $this->text->make_blob("Org ranks for '<myguild>' ($count)", $blob);
			}
		}
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("orgranks")
	 * @Matches("/^orgranks ([a-z0-9-]+)$/i")
	 */
	public function orgranks2Command($message, $channel, $sender, $sendto, $args) {
		if (preg_match("/^[0-9]+$/", $args[1])) {
			$guild_id = $args[1];
		} else {
			// Someone's name.  Doing a whois to get an orgID.
			$name = ucfirst(strtolower($args[1]));
			$whois = $this->playerManager->get_by_name($name);

			if ($whois === null) {
				$msg = "Could not find character info for $name.";
				$sendto->reply($msg);
				return;
			} else if ($whois->guild_id == 0) {
				$msg = "Character <highlight>$name<end> does not seem to be in an org.";
				$sendto->reply($msg);
				return;
			} else {
				$guild_id = $whois->guild_id;
			}
		}

		$msg = "Getting guild info. Please wait...";
		$sendto->reply($msg);

		$org = $this->guildManager->get_by_id($guild_id);
		if ($org === null) {
			$msg = "Error in getting the Org info. Either org does not exist or AO's server was too slow to respond.";
		} else {
			$sql = "SELECT * FROM players WHERE guild_id = ? AND dimension = '<dim>' ORDER BY guild_rank_id ASC, name ASC";
			$data = $this->db->query($sql, $guild_id);
			$orgname = $org->orgname;
			
			$count = count($data);
			if ($count == 0) {
				$msg = "No org members found.";
			} else {
				$blob = $this->formatOrgRankList($data);

				$msg = $this->text->make_blob("Org ranks for '$orgname' ($count)", $blob);
			}
		}
		$sendto->reply($msg);
	}
	
	public function formatOrgRankList($data) {
		$blob = '';

		$current_rank_id = '';
		forEach ($data as $row) {
			if ($current_rank_id != $row->guild_rank_id) {
				$current_rank_id = $row->guild_rank_id;
				$blob .= "\n<header2>{$row->guild_rank}<end>\n";
			}

			$blob .= "<tab><highlight>{$row->name} (Level {$row->level}";
			if ($row->ai_level > 0) {
				$blob .= "<green>/{$row->ai_level}<end>";
			}
			$blob .= ", {$row->gender} {$row->breed} {$row->profession})<end>";

			if (isset($row->logged_off)) {
				if ($this->buddylistManager->is_online($row->name) == 1) {
					$logged_off = "<green>Online<end>";
				} else if ($row->logged_off != "0") {
					$logged_off = $this->util->date($row->logged_off);
				} else {
					$logged_off = "<orange>Unknown<end>";
				}
				$blob .= " :: Last logoff: $logged_off";
			}

			$blob .= "\n";
		}
		
		return $blob;
	}
}

