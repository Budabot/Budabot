<?php
/**
 * Authors: 
 *	- Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'whoisorg',
 *		accessLevel = 'all',
 *		description = 'Display org info',
 *		help        = 'whoisorg.txt'
 *	)
 */
class WhoisOrgController {

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
	
	/** @Inject */
	public $util;
	
	/** @Inject */
	public $playerManager;
	
	/** @Inject */
	public $guildManager;
	
	/**
	 * @HandlesCommand("whoisorg")
	 * @Matches("/^whoisorg ([a-z0-9-]+) (\d)$/i")
	 * @Matches("/^whoisorg ([a-z0-9-]+)$/i")
	 */
	public function whoisorgCommand($message, $channel, $sender, $sendto, $args) {
		$dimension = $this->chatBot->vars['dimension'];
		if (count($args) == 3) {
			$dimension = $args[2];
		}
		
		if (preg_match("/^[0-9]+$/", $args[1])) {
			$org_id = $args[1];
		} else {
			// Someone's name.  Doing a whois to get an orgID.
			$name = ucfirst(strtolower($args[1]));
			$whois = $this->playerManager->get_by_name($name, $dimension);

			if ($whois === null) {
				$msg = "Could not find character info for $name.";
				$sendto->reply($msg);
				return;
			} else if ($whois->guild_id == 0) {
				$msg = "Character <highlight>$name<end> does not seem to be in an org.";
				$sendto->reply($msg);
				return;
			} else {
				$org_id = $whois->guild_id;
			}
		}

		$msg = "Getting Org info. Please stand by...";
		$sendto->reply($msg);

		$org = $this->guildManager->get_by_id($org_id, $dimension);
		if ($org === null) {
			$msg = "Error in getting the Org info. Either the org does not exist or AO's server was too slow to respond.";
			$sendto->reply($msg);
			return;
		}

		$num_adv = 0;
		$num_agent = 0;
		$num_crat = 0;
		$num_crat = 0;
		$num_enf = 0;
		$num_eng = 0;
		$num_fix = 0;
		$num_keep = 0;
		$num_ma = 0;
		$num_mp = 0;
		$num_nt = 0;
		$num_shade = 0;
		$num_sol = 0;
		$num_trad = 0;
		$lvl_min = 220;
		$lvl_max = 1;

		$num_members = count($org->members);
		forEach ($org->members as $member) {
			if ($member->guild_rank_id == 0) {
				$president_name = $member->name;
				$president_prof = $member->profession;
				$president_lvl = $member->level;
				$president_gender = $member->gender;
				$president_breed = $member->breed;
				$faction = $member->faction;
			}
			$lvl_tot += $member->level;

			if ($lvl_min > $member->level) {
				$lvl_min = $member->level;
			}

			if ($lvl_max < $member->level) {
				$lvl_max = $member->level;
			}

			switch ($member->profession) {
				case "Adventurer":
					$num_adv++;
					break;
				case "Agent":
					$num_agent++;
					break;
				case "Bureaucrat":
					$num_crat++;
					break;
				case "Doctor":
					$num_doc++;
					break;
				case "Enforcer":
					$num_enf++;
					break;
				case "Engineer":
					$num_eng++;
					break;
				case "Fixer":
					$num_fix++;
					break;
				case "Keeper":
					$num_keep++;
					break;
				case "Martial Artist":
					$num_ma++;
					break;
				case "Meta-Physicist":
					$num_mp++;
					break;
				case "Nano-Technician":
					$num_nt++;
					break;
				case "Shade":
					$num_shade++;
					break;
				case "Soldier":
					$num_sol++;
					break;
				case "Trader":
					$num_trad++;
					break;
			}
		}
		$lvl_avg = round($lvl_tot/$num_members);

		$link = "<header2>General Info<end>\n";
		$link .= "<highlight>Faction:<end> $faction\n";
		$link .= "<highlight>Lowest lvl:<end> $lvl_min\n";
		$link .= "<highlight>Highest lvl:<end> $lvl_max\n";
		$link .= "<highlight>Average lvl:<end> $lvl_avg\n\n";

		$link .= "<header2>President<end>\n";
		$link .= "<highlight>Name:<end> $president_name\n";
		$link .= "<highlight>Profession:<end> $president_prof\n";
		$link .= "<highlight>Level:<end> $president_lvl\n";
		$link .= "<highlight>Gender:<end> $president_gender\n";
		$link .= "<highlight>Breed:<end> $president_breed\n\n";

		$link .= "<header2>Members<end>\n";
		$link .= "<highlight>Number of Members:<end> $num_members\n";
		$link .= "<highlight>Adventurer:<end> $num_adv (".round(($num_adv*100)/$num_members, 1)."% of total)\n";
		$link .= "<highlight>Agents:<end> $num_agent (".round(($num_agent*100)/$num_members, 1)."% of total)\n";
		$link .= "<highlight>Bureaucrats:<end> $num_crat (".round(($num_crat*100)/$num_members, 1)."% of total)\n";
		$link .= "<highlight>Doctors:<end> $num_doc (".round(($num_doc*100)/$num_members, 1)."% of total)\n";
		$link .= "<highlight>Enforcers:<end> $num_enf (".round(($num_enf*100)/$num_members, 1)."% of total)\n";
		$link .= "<highlight>Engineers:<end> $num_eng (".round(($num_eng*100)/$num_members, 1)."% of total)\n";
		$link .= "<highlight>Fixers:<end> $num_fix (".round(($num_fix*100)/$num_members, 1)."% of total)\n";
		$link .= "<highlight>Keepers:<end> $num_keep (".round(($num_keep*100)/$num_members, 1)."% of total)\n";
		$link .= "<highlight>Martial Artists:<end> $num_ma (".round(($num_ma*100)/$num_members, 1)."% of total)\n";
		$link .= "<highlight>Meta-Physicists:<end> $num_mp (".round(($num_mp*100)/$num_members, 1)."% of total)\n";
		$link .= "<highlight>Nano-Technicians:<end> $num_nt (".round(($num_nt*100)/$num_members, 1)."% of total)\n";
		$link .= "<highlight>Shades:<end> $num_shade (".round(($num_shade*100)/$num_members, 1)."% of total)\n";
		$link .= "<highlight>Soldiers:<end> $num_sol (".round(($num_sol*100)/$num_members, 1)."% of total)\n";
		$link .= "<highlight>Traders:<end> $num_trad (".round(($num_trad*100)/$num_members, 1)."% of total)\n";
		$msg = $this->text->make_blob("Org Info $org->orgname", $link);

		$sendto->reply($msg);
	}
}

