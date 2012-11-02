<?php
/**
 * @Instance("towers")
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'towerstats',
 *		accessLevel = 'all',
 *		description = 'Show how many towers each faction has lost',
 *		help        = 'towerstats.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'attacks',
 *      alias       = 'battles',
 *		accessLevel = 'all',
 *		description = 'Show the last Tower Attack messages',
 *		help        = 'attacks.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'forcescout',
 *		accessLevel = 'guild',
 *		description = 'Add tower info to watch list (bypasses some of the checks)',
 *		help        = 'scout.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'lc',
 *		accessLevel = 'all',
 *		description = 'Show status of towers',
 *		help        = 'lc.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'opentimes',
 *		accessLevel = 'guild',
 *		description = 'Show status of towers',
 *		help        = 'scout.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'penalty',
 *		accessLevel = 'all',
 *		description = 'Show orgs in penalty',
 *		help        = 'penalty.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'remscout',
 *		accessLevel = 'guild',
 *		description = 'Remove tower info from watch list',
 *		help        = 'scout.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'scout',
 *		accessLevel = 'guild',
 *		description = 'Add tower info to watch list',
 *		help        = 'scout.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'victory',
 *		accessLevel = 'all',
 *		description = 'Show the last Tower Battle results',
 *		help        = 'victory.txt'
 *	)
 */
class TowerController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;

	/** @Inject */
	public $playfieldController;

	/** @Inject */
	public $playerManager;

	/** @Inject */
	public $text;

	/** @Inject */
	public $setting;

	/** @Inject */
	public $chatBot;

	/** @Inject */
	public $db;

	/** @Inject */
	public $util;
	
	/** @Inject */
	public $levelController;

	/** @Logger */
	public $logger;

	private $attackListeners = array();

	/**
	 * @Setting("tower_attack_spam")
	 * @Description("Layout types when displaying tower attacks")
	 * @Visibility("edit")
	 * @Type("options")
	 * @Options("off;compact;normal;full")
	 * @Intoptions("0;1;2;3")
	 * @AccessLevel("mod")
	 */
	public $defaultTowerAttackSpam = "1";

	/**
	 * @Setting("tower_faction_def")
	 * @Description("Display certain factions defending")
	 * @Visibility("edit")
	 * @Type("options")
	 * @Options("none;clan;neutral;clan+neutral;omni;clan+omni;neutral+omni;all")
	 * @Intoptions("0;1;2;3;4;5;6;7")
	 * @AccessLevel("mod")
	 */
	public $defaultTowerFactionDef = "7";

	/**
	 * @Setting("tower_faction_atk")
	 * @Description("Display certain factions attacking")
	 * @Visibility("edit")
	 * @Type("options")
	 * @Options("none;clan;neutral;clan+neutral;omni;clan+omni;neutral+omni;all")
	 * @Intoptions("0;1;2;3;4;5;6;7")
	 * @AccessLevel("mod")
	 */
	public $defaultTowerFactionAtk = "7";

	/**
	 * @Setting("tower_page_size")
	 * @Description("Number of results to display for victory/attacks")
	 * @Visibility("edit")
	 * @Type("options")
	 * @Options("5;10;15;20;25")
	 * @AccessLevel("mod")
	 */
	public $defaultTowerPageSize = "15";
	
	/**
	 * @Setting("check_close_time_on_scout")
	 * @Description("Check that close time is within one hour of last victory on site")
	 * @Visibility("edit")
	 * @Type("options")
	 * @Options("true;false")
	 * @Intoptions("1;0")
	 * @AccessLevel("mod")
	 */
	public $defaultCheckCloseTimeOnScout = "1";
	
	/**
	 * @Setting("check_guild_name_on_scout")
	 * @Description("Check that guild name has attacked or been attacked before")
	 * @Visibility("edit")
	 * @Type("options")
	 * @Options("true;false")
	 * @Intoptions("1;0")
	 * @AccessLevel("mod")
	 */
	public $defaultCheckGuildNameOnScout = "1";

	/**
	 * Adds listener callback which will be called when tower attacks occur.
	 */
	public function registerAttackListener($callback, $data = null) {
		if (!is_callable($callback)) {
			$this->logger->log('ERROR', 'Given callback is not valid.');
			return;
		}
		$listener = new StdClass();
		$listener->callback = $callback;
		$listener->data = $data;
		$this->attackListeners []= $listener;
	}

	/**
	 * @Setup
	 * This handler is called on bot startup.
	 */
	public function setup() {
		$this->db->loadSQLFile($this->moduleName, 'tower_attack');
		$this->db->loadSQLFile($this->moduleName, 'scout_info');
		$this->db->loadSQLFile($this->moduleName, 'tower_site');
	}

	/**
	 * This command handler shows the last tower attack messages.
	 *
	 * @HandlesCommand("attacks")
	 * @Matches("/^attacks (\d+)$/i")
	 * @Matches("/^attacks$/i")
	 */
	public function attacksCommand($message, $channel, $sender, $sendto, $args) {
		$this->attacksCommandHandler($args[1], '', '', $sendto);
	}

	/**
	 * This command handler shows the last tower attack messages by site number
	 * and optionally by page.
	 *
	 * @HandlesCommand("attacks")
	 * @Matches("/^attacks (?!org|player)([a-z0-9]+) (\d+) (\d+)$/i")
	 * @Matches("/^attacks (?!org|player)([a-z0-9]+) (\d+)$/i")
	 */
	public function attacks2Command($message, $channel, $sender, $sendto, $args) {
		$playfield = $this->playfieldController->get_playfield_by_name($args[1]);
		if ($playfield === null) {
			$msg = "Please enter a valid playfield.";
			$sendto->reply($msg);
			return;
		}
	
		$tower_info = $this->get_tower_info($playfield->id, $args[2]);
		if ($tower_info === null) {
			$msg = "Invalid site number.";
			$sendto->reply($msg);
			return;
		}
	
		$cmd = "$args[1] $args[2] ";
		$search = "WHERE a.`playfield_id` = {$tower_info->playfield_id} AND a.`site_number` = {$tower_info->site_number}";
		$this->attacksCommandHandler($args[3], $search, $cmd, $sendto);
	}

	/**
	 * This command handler shows the last tower attack messages where given
	 * org has been an attacker or defender.
	 *
	 * @HandlesCommand("attacks")
	 * @Matches("/^attacks org (.+) (\d+)$/i")
	 * @Matches("/^attacks org (.+)$/i")
	 */
	public function attacksOrgCommand($message, $channel, $sender, $sendto, $args) {
		$cmd = "org $args[1] ";
		$value = str_replace("'", "''", $args[1]);
		$search = "WHERE a.`att_guild_name` LIKE '$value' OR a.`def_guild_name` LIKE '$value'";
		$this->attacksCommandHandler($args[2], $search, $cmd, $sendto);
	}

	/**
	 * This command handler shows the last tower attack messages where given
	 * player has been as attacker.
	 *
	 * @HandlesCommand("attacks")
	 * @Matches("/^attacks player (.+) (\d+)$/i")
	 * @Matches("/^attacks player (.+)$/i")
	 */
	public function attacksPlayerCommand($message, $channel, $sender, $sendto, $args) {
		$cmd = "player $args[1] ";
		$value = str_replace("'", "''", $args[1]);
		$search = "WHERE a.`att_player` LIKE '$value'";
		$this->attacksCommandHandler($args[2], $search, $cmd, $sendto);
	}

	/**
	 * This command handler adds tower info to watch list (bypasses some of the checks).
	 *
	 * @HandlesCommand("forcescout")
	 * @Matches("/^(scout|forcescout) ([a-z0-9]+) ([0-9]+) ([0-9]{1,2}:[0-9]{2}:[0-9]{2}) ([0-9]+) ([a-z]+) (.*)$/i")
	 */
	public function forcescoutCommand($message, $channel, $sender, $sendto, $args) {
		if (strtolower($args[1]) == 'forcescout') {
			$skip_checks = true;
		} else {
			$skip_checks = false;
		}
	
		$playfield_name = $args[2];
		$site_number = $args[3];
		$closing_time = $args[4];
		$ct_ql = $args[5];
		$faction = ucfirst(strtolower($args[6]));
		$guild_name = $args[7];
	
		if ($faction != 'Omni' && $faction != 'Neutral' && $faction != 'Clan') {
			$msg = "Valid values for faction are: 'Omni', 'Neutral', and 'Clan'.";
			$sendto->reply($msg);
			return;
		}
	
		$playfield = $this->playfieldController->get_playfield_by_name($playfield_name);
		if ($playfield === null) {
			$msg = "Invalid playfield.";
			$sendto->reply($msg);
			return;
		}
	
		$tower_info = $this->get_tower_info($playfield->id, $site_number);
		if ($tower_info === null) {
			$msg = "Invalid site number.";
			$sendto->reply($msg);
			return;
		}
	
		if ($ct_ql < $tower_info->min_ql || $ct_ql > $tower_info->max_ql) {
			$msg = "$playfield->short_name $tower_info->site_number can only accept Control Tower of ql {$tower_info->min_ql}-{$tower_info->max_ql}";
			$sendto->reply($msg);
			return;
		}
	
		$closing_time_array = explode(':', $closing_time);
		$closing_time_seconds = $closing_time_array[0] * 3600 + $closing_time_array[1] * 60 + $closing_time_array[2];
	
		if (!$skip_checks && $this->setting->get('check_close_time_on_scout') == 1) {
			$last_victory = $this->get_last_victory($tower_info->playfield_id, $tower_info->site_number);
			if ($last_victory !== null) {
				$victory_time_of_day = $last_attack->time % 86400;
				if ($victory_time_of_day > $closing_time_seconds) {
					$victory_time_of_day -= 86400;
				}
	
				if ($closing_time_seconds - $victory_time_of_day > 3600) {
					$check_blob .= "- <green>Closing time<end> The closing time you have specified is more than 1 hour after the site was destroyed.";
					$check_blob .= " Please verify that you are using the closing time and not the gas change time and that the closing time is correct.\n\n";
				}
			}
		}
	
		if (!$skip_checks && $this->setting->get('check_guild_name_on_scout') == 1) {
			if (!$this->check_guild_name($guild_name)) {
				$check_blob .= "- <green>Org name<end> The org name you entered has never attacked or been attacked.\n\n";
			}
		}
	
		if ($check_blob) {
			$check_blob .= "Please correct these errors, or, if you are sure the values you entered are correct, use !forcescout to bypass these checks";
			$msg = $this->text->make_blob('Scouting problems', $check_blob);
		} else {
			$this->add_scout_site($playfield->id, $site_number, $closing_time_seconds, $ct_ql, $faction, $guild_name, $sender);
			$msg = "Scout info has been updated.";
		}
		$sendto->reply($msg);
	}

	/**
	 * This command handler shows status of towers.
	 *
	 * @HandlesCommand("lc")
	 * @Matches("/^lc$/i")
	 */
	public function lcCommand($message, $channel, $sender, $sendto, $args) {
		$sql = "SELECT * FROM playfields WHERE `id` IN (SELECT DISTINCT `playfield_id` FROM tower_site) ORDER BY `short_name`";
		$data = $this->db->query($sql);

		$blob = '';
		forEach ($data as $row) {
			$baseLink = $this->text->make_chatcmd($row->long_name, "/tell <myname> lc $row->short_name");
			$blob .= "$baseLink <highlight>($row->short_name)<end>\n";
		}
		$msg = $this->text->make_blob('Land Control Index', $blob);
		$sendto->reply($msg);
	}

	/**
	 * This command handler shows status of towers.
	 *
	 * @HandlesCommand("lc")
	 * @Matches("/^lc ([0-9a-z]+)$/i")
	 */
	public function lc2Command($message, $channel, $sender, $sendto, $args) {
		$playfield_name = strtoupper($args[1]);
		$playfield = $this->playfieldController->get_playfield_by_name($playfield_name);
		if ($playfield === null) {
			$msg = "Playfield '$playfield_name' could not be found";
			$sendto->reply($msg);
			return;
		}

		$sql = "SELECT *, t1.playfield_id, t1.site_number FROM tower_site t1
			JOIN playfields p ON (t1.playfield_id = p.id)
			WHERE t1.playfield_id = ?";
	
		$data = $this->db->query($sql, $playfield->id);
		$blob = '';
		forEach ($data as $row) {
			$blob .= "<pagebreak>" . $this->formatSiteInfo($row) . "\n\n";
		}

		$msg = $this->text->make_blob("All Bases in $playfield->long_name", $blob);

		$sendto->reply($msg);
	}

	/**
	 * This command handler shows status of towers.
	 *
	 * @HandlesCommand("lc")
	 * @Matches("/^lc ([0-9a-z]+) ([0-9]+)$/i")
	 */
	public function lc3Command($message, $channel, $sender, $sendto, $args) {
		$playfield_name = strtoupper($args[1]);
		$playfield = $this->playfieldController->get_playfield_by_name($playfield_name);
		if ($playfield === null) {
			$msg = "Playfield '$playfield_name' could not be found";
			$sendto->reply($msg);
			return;
		}

		$site_number = $args[2];
		$sql = "SELECT *, t1.playfield_id, t1.site_number FROM tower_site t1
			JOIN playfields p ON (t1.playfield_id = p.id)
			WHERE t1.playfield_id = ? AND t1.site_number = ?";

		$row = $this->db->queryRow($sql, $playfield->id, $site_number);
		if ($row !== null) {
			$blob = $this->formatSiteInfo($row) . "\n\n";

			// show last attacks and victories
			$sql = "
				SELECT
					t.i,
					a.*,
					v.*,
					a2.playfield_id AS win_playfield_id,
					a2.site_number AS win_site_number,
					COALESCE(a.time, v.time) dt
				FROM
					(SELECT 0 AS i UNION ALL SELECT 1 AS i) t
					LEFT JOIN tower_attack_<myname> a
						ON 0 = t.i AND a.playfield_id = ? AND a.site_number = ?
					LEFT JOIN tower_victory_<myname> v
						ON 1 = t.i AND v.attack_id IS NOT NULL
					LEFT JOIN tower_attack_<myname> a2
						ON v.attack_id = a2.id
				WHERE
					(a2.playfield_id = ? OR a2.playfield_id IS NULL)
					AND (a2.site_number = ? OR a2.site_number IS NULL)
					AND (a.id IS NOT NULL OR v.id IS NOT NULL)
				ORDER BY
					dt DESC
				LIMIT 10";
			$data = $this->db->query($sql, $playfield->id, $site_number, $playfield->id, $site_number);
			forEach ($data as $row) {
				if ($row->i == 0) {
					// attack
					$blob .= "<$row->att_faction>$row->att_guild_name<end> attacked <$row->def_faction>$row->def_guild_name<end>\n";
				} else {
					// victory
					$blob .= "<$row->win_faction>$row->win_guild_name<end> won against <$row->lose_faction>$row->lose_guild_name<end>\n";
				}
			}

			$msg = $this->text->make_blob("$playfield->short_name $site_number", $blob);
		} else {
			$msg = "Invalid site number.";
		}

		$sendto->reply($msg);
	}

	/**
	 * @HandlesCommand("opentimes")
	 * @Matches("/^opentimes$/i")
	 */
	public function opentimesCommand($message, $channel, $sender, $sendto, $args) {
		$sql = "
			SELECT
				guild_name,
				sum(ct_ql) AS total_ql
			FROM
				scout_info
			GROUP BY
				guild_name
			ORDER BY
				guild_name ASC";
		$data = $this->db->query($sql);
		$contractQls = array();
		forEach ($data as $row) {
			$contractQls[$row->guild_name] = $row->total_ql;
		}
	
		$sql = "
			SELECT
				*
			FROM
				tower_site t
				JOIN scout_info s ON (t.playfield_id = s.playfield_id AND s.site_number = t.site_number)
				JOIN playfields p ON (t.playfield_id = p.id)
			ORDER BY
				guild_name ASC,
				ct_ql DESC";
		$data = $this->db->query($sql);
	
		if (count($data) > 0) {
			$blob = '';
			$currentGuildName = '';
			forEach ($data as $row) {
				if ($row->guild_name != $currentGuildName) {
					$contractQl = $contractQls[$row->guild_name];
					$contractQl = ($contractQl * 2);
					$faction = strtolower($row->faction);
	
					$blob .= "\n<u><$faction>$row->guild_name<end></u> (Total Contract QL: $contractQl)\n";
					$currentGuildName = $row->guild_name;
				}
				$gas_level = $this->getGasLevel($row->close_time);
				$gas_change_string = "$gas_level->color $gas_level->gas_level - $gas_level->next_state in " . gmdate('H:i:s', $gas_level->gas_change) . "<end>";
	
				$site_link = $this->text->make_chatcmd("$row->short_name $row->site_number", "/tell <myname> lc $row->short_name $row->site_number");
				$open_time = $row->close_time - (3600 * 6);
				if ($open_time < 0) {
					$open_time += 86400;
				}
	
				$blob .= "$site_link <white>- {$row->min_ql}-{$row->max_ql}, $row->ct_ql CT, $gas_change_string [by $row->scouted_by]<end>\n";
			}
	
			$msg = $this->text->make_blob("Scouted Bases", $blob);
		} else {
			$msg = "No sites currently scouted.";
		}
		$sendto->reply($msg);
	}

	/**
	 * This command handler shows orgs in penalty.
	 *
	 * @HandlesCommand("penalty")
	 * @Matches("/^penalty$/i")
	 * @Matches("/^penalty ([a-z0-9]+)$/i")
	 */
	public function penaltyCommand($message, $channel, $sender, $sendto, $args) {
		if (count($args) == 2) {
			$budatime = $args[1];
		} else {
			$budatime = '2h';  // default to 2 hours
		}
		
		$time = $this->util->parseTime($budatime);
		if ($time < 1) {
			$msg = "You must enter a valid time parameter.";
			$sendto->reply($msg);
			return;
		}

		$penaltyTimeString = $this->util->unixtime_to_readable($time, false);
	
		$data = $this->getSitesInPenalty(time() - $time);
	
		if (count($data) > 0) {
			$blob = '';
			$current_faction = '';
			forEach ($data as $row) {
				if ($current_faction != $row->att_faction) {
					$blob .= "\n<header2> ::: {$row->att_faction} ::: <end>\n";
					$current_faction = $row->att_faction;
				}
				$timeString = $this->util->unixtime_to_readable(time() - $row->penalty_time, false);
				$blob .= "<{$row->att_faction}>{$row->att_guild_name}<end> - <white>$timeString ago<end>\n";
			}
			$msg = $this->text->make_blob("Orgs in penalty ($penaltyTimeString)", $blob);
		} else {
			$msg = "There are no orgs who have attacked or won battles in the past $penaltyTimeString.";
		}
		$sendto->reply($msg);
	}

	/**
	 * This command handler removes tower info to watch list.
	 *
	 * @HandlesCommand("remscout")
	 * @Matches("/^remscout ([a-z0-9]+) ([0-9]+)$/i")
	 */
	public function remscoutCommand($message, $channel, $sender, $sendto, $args) {
		$playfield_name = $args[1];
		$site_number = $args[2];
	
		$playfield = $this->playfieldController->get_playfield_by_name($playfield_name);
		if ($playfield === null) {
			$msg = "Invalid playfield.";
			$sendto->reply($msg);
			return;
		}
	
		$tower_info = $this->get_tower_info($playfield->id, $site_number);
		if ($tower_info === null) {
			$msg = "Invalid site number.";
			$sendto->reply($msg);
			return;
		}
	
		$num_rows = $this->rem_scout_site($playfield->id, $site_number);
	
		if ($num_rows == 0) {
			$msg = "Could not find a scout record for {$playfield->short_name} {$site_number}.";
		} else {
			$msg = "{$playfield->short_name} {$site_number} removed successfully.";
		}
		$sendto->reply($msg);
	}

	/**
	 * This command handler adds tower info to watch list.
	 *
	 * @HandlesCommand("scout")
	 * @Matches("/^(scout|forcescout) ([a-z0-9]+) ([0-9]+) ([0-9]{1,2}:[0-9]{2}:[0-9]{2}) ([0-9]+) ([a-z]+) (.*)$/i")
	 */
	public function scoutCommand($message, $channel, $sender, $sendto, $args) {
		if (strtolower($args[1]) == 'forcescout') {
			$skip_checks = true;
		} else {
			$skip_checks = false;
		}
	
		$playfield_name = $args[2];
		$site_number = $args[3];
		$closing_time = $args[4];
		$ct_ql = $args[5];
		$faction = ucfirst(strtolower($args[6]));
		$guild_name = $args[7];
	
		if ($faction != 'Omni' && $faction != 'Neutral' && $faction != 'Clan') {
			$msg = "Valid values for faction are: 'Omni', 'Neutral', and 'Clan'.";
			$sendto->reply($msg);
			return;
		}
	
		$playfield = $this->playfieldController->get_playfield_by_name($playfield_name);
		if ($playfield === null) {
			$msg = "Invalid playfield.";
			$sendto->reply($msg);
			return;
		}
	
		$tower_info = $this->get_tower_info($playfield->id, $site_number);
		if ($tower_info === null) {
			$msg = "Invalid site number.";
			$sendto->reply($msg);
			return;
		}
	
		if ($ct_ql < $tower_info->min_ql || $ct_ql > $tower_info->max_ql) {
			$msg = "$playfield->short_name $tower_info->site_number can only accept Control Tower of ql {$tower_info->min_ql}-{$tower_info->max_ql}";
			$sendto->reply($msg);
			return;
		}
	
		$closing_time_array = explode(':', $closing_time);
		$closing_time_seconds = $closing_time_array[0] * 3600 + $closing_time_array[1] * 60 + $closing_time_array[2];
	
		if (!$skip_checks && $this->setting->get('check_close_time_on_scout') == 1) {
			$last_victory = $this->get_last_victory($tower_info->playfield_id, $tower_info->site_number);
			if ($last_victory !== null) {
				$victory_time_of_day = $last_attack->time % 86400;
				if ($victory_time_of_day > $closing_time_seconds) {
					$victory_time_of_day -= 86400;
				}
	
				if ($closing_time_seconds - $victory_time_of_day > 3600) {
					$check_blob .= "- <green>Closing time<end> The closing time you have specified is more than 1 hour after the site was destroyed.";
					$check_blob .= " Please verify that you are using the closing time and not the gas change time and that the closing time is correct.\n\n";
				}
			}
		}
	
		if (!$skip_checks && $this->setting->get('check_guild_name_on_scout') == 1) {
			if (!$this->check_guild_name($guild_name)) {
				$check_blob .= "- <green>Org name<end> The org name you entered has never attacked or been attacked.\n\n";
			}
		}
	
		if ($check_blob) {
			$check_blob .= "Please correct these errors, or, if you are sure the values you entered are correct, use !forcescout to bypass these checks";
			$msg = $this->text->make_blob('Scouting problems', $check_blob);
		} else {
			$this->add_scout_site($playfield->id, $site_number, $closing_time_seconds, $ct_ql, $faction, $guild_name, $sender);
			$msg = "Scout info has been updated.";
		}
		$sendto->reply($msg);
	}

	/**
	 * This command handler shows how many towers each faction has lost.
	 *
	 * @HandlesCommand("towerstats")
	 * @Matches("/^towerstats (.+)$/i")
	 * @Matches("/^towerstats$/i")
	 */
	public function towerStatsCommand($message, $channel, $sender, $sendto, $args) {
		if (count($args) == 2) {
			$budatime = $args[1];
		} else {
			$budatime = "1d";
		}

		$time = $this->util->parseTime($budatime);
		if ($time < 1) {
			$msg = "You must enter a valid time parameter.";
			$sendto->reply($msg);
			return;
		}

		$timeString = $this->util->unixtime_to_readable($time);

		$blob = '';

		$sql = "SELECT
				att_faction,
				COUNT(att_faction) AS num
			FROM
				tower_attack_<myname>
			WHERE
				`time` >= ?
			GROUP BY
				att_faction
			ORDER BY
				num DESC";

		$data = $this->db->query($sql, time() - $time);
		forEach ($data as $row) {
			$blob .= "<{$row->att_faction}>{$row->att_faction}<end> have attacked <highlight>{$row->num}<end> times.\n";
		}
		if (count($data) > 0) {
			$blob .= "\n";
		}

		$sql = "SELECT
				lose_faction,
				COUNT(lose_faction) AS num
			FROM
				tower_victory_<myname>
			WHERE
				`time` >= ?
			GROUP BY
				lose_faction
			ORDER BY
				num DESC";

		$data = $this->db->query($sql, time() - $time);
		forEach ($data as $row) {
			$blob .= "<{$row->lose_faction}>{$row->lose_faction}<end> have lost <highlight>{$row->num}<end> tower sites.\n";
		}

		if ($blob == '') {
			$msg = "No tower attacks or victories have been recorded.";
		} else {
			$msg = $this->text->make_blob("Tower Stats for the Last $timeString", $blob);
		}
		$sendto->reply($msg);
	}

	/**
	 * This command handler shows the last tower battle results.
	 *
	 * @HandlesCommand("victory")
	 * @Matches("/^victory (\d+)$/i")
	 * @Matches("/^victory$/i")
	 */
	public function victoryCommand($message, $channel, $sender, $sendto, $args) {
		$this->victoryCommandHandler($args[1], $search, "", $sendto);
	}

	/**
	 * This command handler shows the last tower battle results.
	 *
	 * @HandlesCommand("victory")
	 * @Matches("/^victory (?!org|player)([a-z0-9]+) (\d+) (\d+)$/i")
	 * @Matches("/^victory (?!org|player)([a-z0-9]+) (\d+)$/i")
	 */
	public function victory2Command($message, $channel, $sender, $sendto, $args) {
		$playfield = $this->playfieldController->get_playfield_by_name($args[1]);
		if ($playfield === null) {
			$msg = "Invalid playfield.";
			$sendto->reply($msg);
			return;
		}
	
		$tower_info = $this->get_tower_info($playfield->id, $args[2]);
		if ($tower_info === null) {
			$msg = "Invalid site number.";
			$sendto->reply($msg);
			return;
		}
	
		$cmd = "$args[1] $args[2] ";
		$search = "WHERE a.`playfield_id` = {$tower_info->playfield_id} AND a.`site_number` = {$tower_info->site_number}";
		$this->victoryCommandHandler($args[3], $search, $cmd, $sendto);
	}

	/**
	 * This command handler shows the last tower battle results.
	 *
	 * @HandlesCommand("victory")
	 * @Matches("/^victory org (.+) (\d+)$/i")
	 * @Matches("/^victory org (.+)$/i")
	 */
	public function victoryOrgCommand($message, $channel, $sender, $sendto, $args) {
		$cmd = "org $args[1] ";
		$value = str_replace("'", "''", $args[1]);
		$search = "WHERE v.`win_guild_name` LIKE '$value' OR v.`lose_guild_name` LIKE '$value'";
		$this->victoryCommandHandler($args[2], $search, $cmd, $sendto);
	}

	/**
	 * This command handler shows the last tower battle results.
	 *
	 * @HandlesCommand("victory")
	 * @Matches("/^victory player (.+) (\d+)$/i")
	 * @Matches("/^victory player (.+)$/i")
	 */
	public function victoryPlayerCommand($message, $channel, $sender, $sendto, $args) {
		$cmd = "player $args[1] ";
		$value = str_replace("'", "''", $args[1]);
		$search = "WHERE a.`att_player` LIKE '$value'";
		$this->victoryCommandHandler($args[2], $search, $cmd, $sendto);
	}

	/**
	 * This event handler record attack messages.
	 *
	 * @Event("towers")
	 * @Description("Record attack messages")
	 */
	public function attackMessagesEvent($eventObj) {
		if (preg_match("/^The (Clan|Neutral|Omni) organization (.+) just entered a state of war! (.+) attacked the (Clan|Neutral|Omni) organization (.+)'s tower in (.+) at location \\((\\d+),(\\d+)\\)\\.$/i", $eventObj->message, $arr)) {
			$att_side = ucfirst(strtolower($arr[1]));  // comes across as a string instead of a reference, so convert to title case
			$att_guild = $arr[2];
			$att_player = $arr[3];
			$def_side = ucfirst(strtolower($arr[4]));  // comes across as a string instead of a reference, so convert to title case
			$def_guild = $arr[5];
			$playfield_name = $arr[6];
			$x_coords = $arr[7];
			$y_coords = $arr[8];
		} else if (preg_match("/^(.+) just attacked the (Clan|Neutral|Omni) organization (.+)'s tower in (.+) at location \(([0-9]+), ([0-9]+)\).(.*)$/i", $eventObj->message, $arr)) {
			$att_player = $arr[1];
			$def_side = ucfirst(strtolower($arr[2]));  // comes across as a string instead of a reference, so convert to title case
			$def_guild = $arr[3];
			$playfield_name = $arr[4];
			$x_coords = $arr[5];
			$y_coords = $arr[6];
		} else {
			return;
		}
		
		// regardless of what the player lookup says, we use the information from the
		// attack message where applicable because that will always be most up to date
		$whois = $this->playerManager->get_by_name($att_player);
		if ($whois === null) {
			$whois = new stdClass;
			$whois->type = 'npc';
		}
		if (isset($att_side)) {
			$whois->faction = $att_side;
		}
		if (isset($att_guild)) {
			$whois->guild = $att_guild;
		}
		// in case it's not a player who causes attack message (pet, mob, etc)
		$whois->name = $att_player;
		
		$playfield = $this->playfieldController->get_playfield_by_name($playfield_name);
		$closest_site = $this->get_closest_site($playfield->id, $x_coords, $y_coords);

		$defender = new StdClass();
		$defender->faction   = $def_side;
		$defender->guild     = $def_guild;
		$defender->playfield = $playfield;
		$defender->site      = $closest_site;

		forEach ($this->attackListeners as $listener) {
			call_user_func($listener->callback, $whois, $defender, $listener->data);
		}

		if ($closest_site === null) {
			$this->logger->log('error', "ERROR! Could not find closest site: ({$playfield_name}) '{$playfield->id}' '{$x_coords}' '{$y_coords}'");
			$more = "[<red>UNKNOWN AREA!<end>]";
		} else {
		
			$this->record_attack($whois, $def_side, $def_guild, $x_coords, $y_coords, $closest_site);
			$this->logger->log('debug', "Site being attacked: ({$playfield_name}) '{$closest_site->playfield_id}' '{$closest_site->site_number}'");
		
			// Beginning of the 'more' window
			$link = "Attacker: <highlight>";
			if ($whois->firstname) {
				$link .= $whois->firstname . " ";
			}
		
			$link .= '"' . $att_player . '"';
			if ($whois->lastname)  {
				$link .= " " . $whois->lastname;
			}
			$link .= "<end>\n";
		
			if ($whois->breed) {
				$link .= "Breed: <highlight>$whois->breed<end>\n";
			}
			if ($whois->gender) {
				$link .= "Gender: <highlight>$whois->gender<end>\n";
			}
		
			if ($whois->profession) {
				$link .= "Profession: <highlight>$whois->profession<end>\n";
			}
			if ($whois->level) {
				$level_info = $this->levelController->get_level_info($whois->level);
				$link .= "Level: <highlight>{$whois->level}/<green>{$whois->ai_level}<end> ({$level_info->pvpMin}-{$level_info->pvpMax})<end>\n";
			}
		
			$link .= "Alignment: <highlight>$whois->faction<end>\n";
		
			if ($whois->guild) {
				if ($whois->faction == "Omni") {
					$link .= "Detachment: <highlight>$whois->guild\n";
				} else {
					$link .= "Clan: <highlight>$whois->guild<end>\n";
				}
				if ($whois->guild_rank) {
					$link .= "Organization Rank: <highlight>$whois->guild_rank<end>\n";
				}
			}
		
			$link .= "\n";
		
			$link .= "Defender: <highlight>$def_guild<end>\n";
			$link .= "Alignment: <highlight>$def_side<end>\n\n";
		
			$base_link = $this->text->make_chatcmd("{$playfield->short_name} {$closest_site->site_number}", "/tell <myname> lc {$playfield->short_name} {$closest_site->site_number}");
			$attack_waypoint = $this->text->make_chatcmd("{$x_coords}x{$y_coords}", "/waypoint {$x_coords} {$y_coords} {$playfield->id}");
			$link .= "Playfield: <highlight>{$base_link} ({$closest_site->min_ql}-{$closest_site->max_ql})<end>\n";
			$link .= "Location: <highlight>{$closest_site->site_name} ({$attack_waypoint})<end>\n";
		
			$more = "[".$this->text->make_blob("more", $link, 'Advanced Tower Info')."]";
		}
		
		$targetorg = "<".strtolower($def_side).">".$def_guild."<end>";
		
		// Starting tower message to org/private chat
		$msg .= "<font color=#FF67FF>[";
		
		// tower_attack_spam >= 2 (normal) includes attacker stats
		if ($this->setting->get("tower_attack_spam") >= 2) {
		
			if ($whois->profession == "") {
				$msg .= "<".strtolower($whois->faction).">$att_player<end> (Unknown";
			} else {
				if (!$whois->guild){
					$msg .= "<".strtolower($whois->faction).">$att_player<end>";
				} else {
					$msg .= "<font color=#AAAAAA>$att_player<end>";
				}
				$msg .= " (level <font color=#AAAAAA>$whois->level<end>";
				if ($whois->ai_level) {
					$msg .= "/<green>$whois->ai_level<end>";
				}
				$msg .= ", $whois->breed <font color=#AAAAAA>$whois->profession<end>";
			}
		
			if (!$whois->guild) {
				$msg .= ")";
			} else if (!$whois->guild_rank) {
				$msg .= "<".strtolower($whois->faction).">$whois->guild<end>)";
			} else {
				$msg .= ", $whois->guild_rank of <".strtolower($whois->faction).">$whois->guild<end>)";
			}
		
		} else if ($whois->guild) {
			$msg .= "<".strtolower($whois->faction).">$whois->guild<end>";
		} else {
			$msg .= "<".strtolower($whois->faction).">$att_player<end>";
		}
		
		$msg .= " attacked ".$targetorg."] ";
		
		// tower_attack_spam >= 3 (full) includes location.
		if ($this->setting->get("tower_attack_spam") >= 3) {
			if ($closest_site) {
				$site_number = "<font color=#AAAAAA>#".$closest_site->site_number."<end>";
			}
			$msg .= "[".$playfield->short_name." $site_number (".$x_coords." x ".$y_coords.")] ";
		}
		
		$msg .= "$more<end>";
		
		$d = $this->setting->get("tower_faction_def");
		$a = $this->setting->get("tower_faction_atk");
		$s = $this->setting->get("tower_attack_spam");
		
		if (($s > 0 && (
			(strtolower($def_side) == "clan"    && ($d & 1)) ||
			(strtolower($def_side) == "neutral" && ($d & 2)) ||
			(strtolower($def_side) == "omni"    && ($d & 4)) ||
			(strtolower($whois->faction) == "clan"    && ($a & 1)) ||
			(strtolower($whois->faction) == "neutral" && ($a & 2)) ||
			(strtolower($whois->faction) == "omni"    && ($a & 4)) ))) {
		
			$this->chatBot->sendGuild($msg, true);
		}
	}

	/**
	 * This event handler record victory messages.
	 *
	 * @Event("towers")
	 * @Description("Record victory messages")
	 */
	public function victoryMessagesEvent($eventObj) {
		if (preg_match("/^The (Clan|Neutral|Omni) organization (.+) attacked the (Clan|Neutral|Omni) (.+) at their base in (.+). The attackers won!!$/i", $eventObj->message, $arr)) {
			$win_faction = $arr[1];
			$win_guild_name = $arr[2];
			$lose_faction = $arr[3];
			$lose_guild_name = $arr[4];
			$playfield_name = $arr[5];
		} else if (preg_match("/^Notum Wars Update: The (clan|neutral|omni) organization (.+) lost their base in (.+).$/i", $eventObj->message, $arr)) {
			$win_faction = '';
			$win_guild_name = '';
			$lose_faction = ucfirst($arr[1]);  // capitalize the faction name to match the other messages
			$lose_guild_name = $arr[2];
			$playfield_name = $arr[3];
		} else {
			return;
		}
		
		$playfield = $this->playfieldController->get_playfield_by_name($playfield_name);
		if ($playfield === null) {
			$this->logger->log('error', "Could not find playfield for name '$playfield_name'");
			return;
		}
		
		$last_attack = $this->get_last_attack($win_faction, $win_guild_name, $lose_faction, $lose_guild_name, $playfield->id);
		if ($last_attack !== null) {
			$this->rem_scout_site($last_attack->playfield_id, $last_attack->site_number);
		} else {
			$last_attack = new stdClass;
			$last_attack->att_guild_name = $win_guild_name;
			$last_attack->def_guild_name = $lose_guild_name;
			$last_attack->att_faction = $win_faction;
			$last_attack->def_faction = $lose_faction;
			$last_attack->playfield_id = $playfield->id;
			$last_attack->id = 'NULL';
		}
		
		$this->record_victory($last_attack);
	}

	private function attacksCommandHandler($page_label, $search, $cmd, $sendto) {
		if (is_numeric($page_label) == false) {
			$page_label = 1;
		} else if ($page_label < 1) {
			$msg = "You must choose a page number greater than 0";
			$sendto->reply($msg);
			return;
		}

		$page_size = $this->setting->get('tower_page_size');
		$start_row = ($page_label - 1) * $page_size;

		$sql =
			"SELECT
				*
			FROM
				tower_attack_<myname> a
				LEFT JOIN playfields p ON (a.playfield_id = p.id)
				LEFT JOIN tower_site s ON (a.playfield_id = s.playfield_id AND a.site_number = s.site_number)
			$search
			ORDER BY
				a.`time` DESC
			LIMIT
				$start_row, $page_size";

		$data = $this->db->query($sql);
		if (count($data) == 0) {
			$msg = "No tower attacks found.";
		} else {
			$links = array();
			if ($page_label > 1) {
				$links['Previous Page'] = '/tell <myname> attacks ' . ($page_label - 1);
			}
			$links['Next Page'] = "/tell <myname> attacks {$cmd}" . ($page_label + 1);

			$blob = "The last $page_size Tower Attacks (page $page_label)\n\n";
			$blob .= $this->text->make_header_links($links) . "\n\n";

			forEach ($data as $row) {
				$blob .= "Time: " . $this->util->date($row->time) . "\n";
				if ($row->att_faction == '') {
					$att_faction = "unknown";
				} else {
					$att_faction = strtolower($row->att_faction);
				}

				if ($row->def_faction == '') {
					$def_faction = "unknown";
				} else {
					$def_faction = strtolower($row->def_faction);
				}

				if ($row->att_profession == 'Unknown') {
					$blob .= "Attacker: <{$att_faction}>{$row->att_player}<end> ({$row->att_faction})\n";
				} else if ($row->att_guild_name == '') {
					$blob .= "Attacker: <{$att_faction}>{$row->att_player}<end> ({$row->att_level}/<green>{$row->att_ai_level}<end> {$row->att_profession}) ({$row->att_faction})\n";
				} else {
					$blob .= "Attacker: {$row->att_player} ({$row->att_level}/<green>{$row->att_ai_level}<end> {$row->att_profession}) <{$att_faction}>{$row->att_guild_name}<end> ({$row->att_faction})\n";
				}

				$base = $this->text->make_chatcmd("{$row->short_name} {$row->site_number}", "/tell <myname> lc {$row->short_name} {$row->site_number}");
				$base .= " ({$row->min_ql}-{$row->max_ql})";

				$blob .= "Defender: <{$def_faction}>{$row->def_guild_name}<end> ({$row->def_faction})\n";
				$blob .= "Site: $base\n\n";
			}
			$msg = $this->text->make_blob("Tower Attacks", $blob);
		}

		$sendto->reply($msg);
	}

	private function victoryCommandHandler($page_label, $search, $cmd, $sendto) {
		if (is_numeric($page_label) == false) {
			$page_label = 1;
		} else if ($page_label < 1) {
			$msg = "You must choose a page number greater than 0";
			$sendto->reply($msg);
			return;
		}

		$page_size = $this->setting->get('tower_page_size');
		$start_row = ($page_label - 1) * $page_size;

		$sql = "
			SELECT
				*,
				v.time AS victory_time,
				a.time AS attack_time
			FROM
				tower_victory_<myname> v
				LEFT JOIN tower_attack_<myname> a ON (v.attack_id = a.id)
				LEFT JOIN playfields p ON (a.playfield_id = p.id)
				LEFT JOIN tower_site s ON (a.playfield_id = s.playfield_id AND a.site_number = s.site_number)
			{$search}
			ORDER BY
				`victory_time` DESC
			LIMIT
				$start_row, $page_size";

		$data = $this->db->query($sql);
		if (count($data) == 0) {
			$msg = "No Tower results found.";
		} else {
			$links = array();
			if ($page_label > 1) {
				$links['Previous Page'] = '/tell <myname> victory ' . ($page_label - 1);
			}
			$links['Next Page'] = "/tell <myname> victory {$cmd}" . ($page_label + 1);

			$blob = "The last $page_size Tower Results (page $page_label)\n\n";
			$blob .= $this->text->make_header_links($links) . "\n\n";
			forEach ($data as $row) {
				$blob .= "Time: " . $this->util->date($row->victory_time) . "\n";

				if (!$win_side = strtolower($row->win_faction)) {
					$win_side = "unknown";
				}
				if (!$lose_side = strtolower($row->lose_faction)) {
					$lose_side = "unknown";
				}

				if ($row->playfield_id != '' && $row->site_number != '') {
					$base = $this->text->make_chatcmd("{$row->short_name} {$row->site_number}", "/tell <myname> lc {$row->short_name} {$row->site_number}");
					$base .= " ({$row->min_ql}-{$row->max_ql})";
				} else {
					$base = "Unknown";
				}

				$blob .= "Winner: <{$win_side}>{$row->win_guild_name}<end> (".ucfirst($win_side).")\n";
				$blob .= "Loser: <{$lose_side}>{$row->lose_guild_name}<end> (".ucfirst($lose_side).")\n";
				$blob .= "Site: $base\n\n";
			}
			$msg = $this->text->make_blob("Tower Victories", $blob);
		}

		$sendto->reply($msg);
	}

	private function get_tower_info($playfield_id, $site_number) {
		$sql = "
			SELECT
				*
			FROM
				tower_site t
			WHERE
				`playfield_id` = ?
				AND `site_number` = ?
			LIMIT 1";

		return $this->db->queryRow($sql, $playfield_id, $site_number);
	}

	private function find_sites_in_playfield($playfield_id) {
		$sql = "SELECT * FROM tower_site WHERE `playfield_id` = ?";

		return $this->db->query($sql, $playfield_id);
	}

	private function get_closest_site($playfield_id, $x_coords, $y_coords) {
		$sql = "
			SELECT
				*,
				((x_distance * x_distance) + (y_distance * y_distance)) radius
			FROM
				(SELECT
					playfield_id,
					site_number,
					min_ql,
					max_ql,
					x_coord,
					y_coord,
					site_name,
					(x_coord - {$x_coords}) as x_distance,
					(y_coord - {$y_coords}) as y_distance
				FROM
					tower_site
				WHERE
					playfield_id = ?) t
			ORDER BY
				radius ASC
			LIMIT 1";

		return $this->db->queryRow($sql, $playfield_id);
	}

	private function get_last_attack($att_faction, $att_guild_name, $def_faction, $def_guild_name, $playfield_id) {
		$time = time() - (7 * 3600);

		$sql = "
			SELECT
				*
			FROM
				tower_attack_<myname>
			WHERE
				`att_guild_name` = ?
				AND `att_faction` = ?
				AND `def_guild_name` = ?
				AND `def_faction` = ?
				AND `playfield_id` = ?
				AND `time` >= ?
			ORDER BY
				`time` DESC
			LIMIT 1";

		return $this->db->queryRow($sql, $att_guild_name, $att_faction, $def_guild_name, $def_faction, $playfield_id, $time);
	}

	private function record_attack($whois, $def_faction, $def_guild_name, $x_coords, $y_coords, $closest_site) {
		$sql = "
			INSERT INTO tower_attack_<myname> (
				`time`,
				`att_guild_name`,
				`att_faction`,
				`att_player`,
				`att_level`,
				`att_ai_level`,
				`att_profession`,
				`def_guild_name`,
				`def_faction`,
				`playfield_id`,
				`site_number`,
				`x_coords`,
				`y_coords`
			) VALUES (
				?,
				?,
				?,
				?,
				?,
				?,
				?,
				?,
				?,
				?,
				?,
				?,
				?
			)";

		return $this->db->exec($sql, time(), $whois->guild, $whois->faction, $whois->name, $whois->level, $whois->ai_level, $whois->profession,
			$def_guild_name, $def_faction, $closest_site->playfield_id, $closest_site->site_number, $x_coords, $y_coords);
	}

	private function find_all_scouted_sites() {
		$sql =
			"SELECT
				*
			FROM
				scout_info s
				JOIN tower_site t
					ON (s.playfield_id = t.playfield_id AND s.site_number = t.site_number)
				JOIN playfields p
					ON (s.playfield_id = p.id)
			ORDER BY
				guild_name, ct_ql";

		return $this->db->query($sql);
	}

	private function get_last_victory($playfield_id, $site_number) {
		$sql = "
			SELECT
				*
			FROM
				tower_victory_<myname> v
				JOIN tower_attack_<myname> a ON (v.attack_id = a.id)
			WHERE
				a.`playfield_id` = ?
				AND a.`site_number` >= ?
			ORDER BY
				v.`time` DESC
			LIMIT 1";

		return $this->db->queryRow($sql, $playfield_id, $site_number);
	}

	private function record_victory($last_attack) {
		$sql = "
			INSERT INTO tower_victory_<myname> (
				`time`,
				`win_guild_name`,
				`win_faction`,
				`lose_guild_name`,
				`lose_faction`,
				`attack_id`
			) VALUES (
				?,
				?,
				?,
				?,
				?,
				?
			)";

		return $this->db->exec($sql, time(), $last_attack->att_guild_name, $last_attack->att_faction, $last_attack->def_guild_name, $last_attack->def_faction, $last_attack->id);
	}

	private function add_scout_site($playfield_id, $site_number, $close_time, $ct_ql, $faction, $guild_name, $scouted_by) {
		$this->db->begin_transaction();

		$this->db->exec("DELETE FROM scout_info WHERE `playfield_id` = ? AND `site_number` = ?", $playfield_id, $site_number);

		$sql = "
			INSERT INTO scout_info (
				`playfield_id`,
				`site_number`,
				`scouted_on`,
				`scouted_by`,
				`ct_ql`,
				`guild_name`,
				`faction`,
				`close_time`
			) VALUES (
				?,
				?,
				?,
				?,
				?,
				?,
				?,
				?
			)";

		$numrows = $this->db->exec($sql, $playfield_id, $site_number, time(), $scouted_by, $ct_ql, $guild_name, $faction, $close_time);

		if ($numrows == 0) {
			$this->db->rollback();
		} else {
			$this->db->commit();
		}

		return $numrows;
	}

	private function rem_scout_site($playfield_id, $site_number) {
		$sql = "DELETE FROM scout_info WHERE `playfield_id` = ? AND `site_number` = ?";

		return $this->db->exec($sql, $playfield_id, $site_number);
	}

	private function check_guild_name($guild_name) {
		$sql = "SELECT * FROM tower_attack_<myname> WHERE `att_guild_name` LIKE ? OR `def_guild_name` LIKE ? LIMIT 1";

		$data = $this->db->query($sql, $guild_name, $guild_name);
		if (count($data) === 0) {
			return false;
		} else {
			return true;
		}
	}

	private function getSitesInPenalty($time) {
		$sql = "
			SELECT att_guild_name, att_faction, MAX(IFNULL(t2.time, t1.time)) AS penalty_time
			FROM tower_attack_<myname> t1
				LEFT JOIN tower_victory_<myname> t2 ON t1.id = t2.id
			WHERE
				att_guild_name <> ''
				AND (t2.time IS NULL AND t1.time > ?)
				OR t2.time > ?
			GROUP BY att_guild_name, att_faction
			ORDER BY att_faction ASC, penalty_time DESC";
		return $this->db->query($sql, $time, $time);
	}
	
	private function getGasLevel($close_time) {
		$current_time = time() % 86400;

		$site = new stdClass();
		$site->current_time = $current_time;
		$site->close_time = $close_time;

		if ($close_time < $current_time) {
			$close_time += 86400;
		}

		$time_until_close_time = $close_time - $current_time;
		$site->time_until_close_time = $time_until_close_time;

		if ($time_until_close_time < 3600 * 1) {
			$site->gas_change = $time_until_close_time;
			$site->gas_level = '5%';
			$site->next_state = 'closes';
			$site->color = "<orange>";
		} else if ($time_until_close_time < 3600 * 6) {
			$site->gas_change = $time_until_close_time;
			$site->gas_level = '25%';
			$site->next_state = 'closes';
			$site->color = "<green>";
		} else {
			$site->gas_change = $time_until_close_time - (3600 * 6);
			$site->gas_level = '75%';
			$site->next_state = 'opens';
			$site->color = "<red>";
		}

		return $site;
	}
	
	private function formatSiteInfo($row) {
		$waypointLink = $this->text->make_chatcmd($row->x_coord . "x" . $row->y_coord, "/waypoint {$row->x_coord} {$row->y_coord} {$row->playfield_id}");
		$attacksLink = $this->text->make_chatcmd("Recent attacks on this base", "/tell <myname> attacks {$row->short_name} {$row->site_number}");
		$victoryLink = $this->text->make_chatcmd("Recent victories on this base", "/tell <myname> victory {$row->short_name} {$row->site_number}");

		$blob = "Short name: <highlight>{$row->short_name} {$row->site_number}<end>\n";
		$blob .= "Long name: <highlight>{$row->site_name}, {$row->long_name}<end>\n";
		$blob .= "Level range: <highlight>{$row->min_ql}-{$row->max_ql}<end>\n";
		$blob .= "Centre coordinates: $waypointLink\n";
		$blob .= $attacksLink . "\n";
		$blob .= $victoryLink;
		
		return $blob;
	}
}

?>