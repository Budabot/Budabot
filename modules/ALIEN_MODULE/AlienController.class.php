<?php
/**
 * Authors: 
 *  - Blackruby (RK2),
 *  - Mdkdoc420 (RK2), 
 *  - Wolfbiter (RK1), 
 *  - Gatester (RK2), 
 *  - Marebone (RK2)
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'leprocs', 
 *		accessLevel = 'all', 
 *		description = "Shows each profession's LE procs", 
 *		help        = 'leprocs.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'ofabarmor',
 *		accessLevel = 'all', 
 *		description = 'Shows ofab armors available to a given profession and their VP cost', 
 *		help        = 'ofabarmor.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'ofabweapons',
 *		accessLevel = 'all', 
 *		description = 'Shows Ofab weapons, their marks, and VP cost', 
 *		help        = 'ofabweapons.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'bio',
 *		accessLevel = 'all', 
 *		description = "Identifies Solid Clump of Kyr'Ozch Bio-Material", 
 *		help        = 'bio.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'aigen',
 *		accessLevel = 'all', 
 *		description = 'Shows info about Alien City Generals', 
 *		help        = 'aigen.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'aiarmor',
 *		accessLevel = 'all', 
 *		description = 'Shows tradeskill process for Alien Armor', 
 *		help        = 'aiarmor.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'bioinfo',
 *		accessLevel = 'all', 
 *		description = 'Shows info about a particular bio type', 
 *		help        = 'bioinfo.txt'
 *	)
 */
class AlienController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;
	
	/** @Inject */
	public $commandAlias;

	/** @Inject */
	public $db;

	/** @Inject */
	public $text;
	
	/** @Inject */
	public $commandManager;

	/**
	 * This handler is called on bot startup.
	 * @Setup
	 */
	public function setup() {
		// TODO: Need annotation for command alias
		$this->commandAlias->register($this->moduleName, "bioinfo", "biotype");
		
		// load database tables from .sql-files
		$this->db->loadSQLFile($this->moduleName, 'leprocs');
		$this->db->loadSQLFile($this->moduleName, 'ofabarmor');
		$this->db->loadSQLFile($this->moduleName, 'ofabweapons');
		$this->db->loadSQLFile($this->moduleName, 'alienweapons');
	}

	/**
	 * This command handler shows menu of each profession's LE procs.
	 *
	 * @HandlesCommand("leprocs")
	 * @Matches("/^leprocs$/i")
	 */
	public function leprocsCommand($message, $channel, $sender, $sendto, $args) {
		print "leprocsCommand\n";
		$data = $this->db->query("SELECT DISTINCT profession FROM leprocs ORDER BY profession ASC");

		$blob = '';
		forEach ($data as $row) {
			$professionLink = $this->text->make_chatcmd($row->profession, "/tell <myname> leprocs $row->profession");
			$blob .= $professionLink . "\n";
		}
		$blob .= "\n\nProc info provided by Wolfbiter (RK1), Gatester (RK2)";

		$msg = $this->text->make_blob("LE Procs", $blob);
		$sendto->reply($msg);
	}
	
	/**
	 * This command handler shows the LE procs for a particular profession.
	 *
	 * @HandlesCommand("leprocs")
	 * @Matches("/^leprocs (.+)$/i")
	 */
	public function leprocsInfoCommand($message, $channel, $sender, $sendto, $args) {
		print "leprocsInfoCommand\n";
		$profession = Util::get_profession_name($args[1]);
		if ($profession == '') {
			$msg = "Please choose one of these professions: adv, agent, crat, doc, enf, eng, fix, keep, ma, mp, nt, sol, shade, or trader";
			$sendto->reply($msg);
			return;
		}

		$data = $this->db->query("SELECT * FROM leprocs WHERE profession LIKE ? ORDER BY proc_type ASC, research_lvl DESC", $profession);
		if (count($data) == 0) {
			$msg = "No procs found for profession '$profession'.";
		} else {
			$blob = '';
			$type = '';
			forEach ($data as $row) {
				if ($type != $row->proc_type) {
					$type = $row->proc_type;
					$blob .= "\n<tab><yellow>$type<end>\n";
				}

				$proc_trigger = "<green>" . substr($row->proc_trigger, 0, 3) . ".<end>";
				$blob .= "$row->name <orange>$row->modifiers<end> $proc_trigger $row->duration\n";
			}
			$blob .= "\n\nNote: Offensive procs have a 5% chance of firing every time you attack; Defensive procs have a 10% chance of firing every time something attacks you.";
			$blob .= "\n\nProc info provided by Wolfbiter (RK1), Gatester (RK2)";

			$msg = $this->text->make_blob("$profession LE Procs", $blob);
		}
		$sendto->reply($msg);
	}

	/**
	 * This command handler shows Ofab armors and VP cost.
	 *
	 * @HandlesCommand("ofabarmor")
	 * @Matches("/^ofabarmor$/i")
	 */
	public function ofabarmorCommand($message, $channel, $sender, $sendto, $args) {
		$qls = $this->db->query("SELECT DISTINCT ql FROM ofabarmorcost ORDER BY ql ASC");
		$data = $this->db->query("SELECT `type`, `profession` FROM ofabarmortype ORDER BY profession ASC");

		$blob = '';
		forEach ($data as $row) {
			$blob .= "<pagebreak>{$row->profession} - Type {$row->type}\n";
			forEach ($qls as $row2) {
				$ql_link = $this->text->make_chatcmd($row2->ql, "/tell <myname> ofabarmor {$row->profession} {$row2->ql}");
				$blob .= "[{$ql_link}] ";
			}
			$blob .= "\n\n";
		}

		$msg = $this->text->make_blob("Ofab Armor Bio-Material Types", $blob);
		$sendto->reply($msg);
	}

	/**
	 * This command handler shows list of ofab armors available to a given profession.
	 *
	 * @HandlesCommand("ofabarmor")
	 * @Matches("/^ofabarmor (.+)$/i")
	 */
	public function ofabarmorInfoCommand($message, $channel, $sender, $sendto, $args) {
		if (preg_match("/^(.+) (\\d+)$/i", $args[1], $arr)) {
			$ql = $arr[2];
		}
		else {
			$ql = 300;
		}
		$profession = Util::get_profession_name($arr[1]);

		if ($profession == '') {
			$msg = "Please choose one of these professions: adv, agent, crat, doc, enf, eng, fix, keep, ma, mp, nt, sol, shade, or trader";
			$sendto->reply($msg);
			return;
		}

		$typelist = $this->db->query("SELECT type FROM ofabarmortype WHERE profession = ?", $profession);
		$type = $typelist[0]->type;

		$data = $this->db->query("SELECT * FROM ofabarmor o1 LEFT JOIN ofabarmorcost o2 ON o1.slot = o2.slot WHERE o1.profession = ? AND o2.ql = ? ORDER BY upgrade ASC, name ASC", $profession, $ql);
		if (count($data) == 0) {
			return false;
		}

		$blob = '';
		$typeLink = $this->text->make_chatcmd("Kyr'Ozch Bio-Material - Type {$type}", "/tell <myname> bioinfo {$type}");
		$typeQl = round(.8 * $ql);
		$blob .= "Upgrade with $typeLink (minimum QL {$typeQl})\n\n";

		$qls = $this->db->query("SELECT DISTINCT ql FROM ofabarmorcost ORDER BY ql ASC");
		forEach ($qls as $row2) {
			if ($row2->ql == $ql) {
				$blob .= "[{$row2->ql}] ";
			} else {
				$ql_link = $this->text->make_chatcmd($row2->ql, "/tell <myname> ofabarmor {$profession} {$row2->ql}");
				$blob .= "[{$ql_link}] ";
			}
		}
		$blob .= "\n";

		$current_upgrade = $row->upgrade;
		forEach ($data as $row) {
			if ($current_upgrade != $row->upgrade) {
				$current_upgrade = $row->upgrade;
				$blob .= "\n";
			}
			$blob .=  $this->text->make_item($row->lowid, $row->highid, $ql, $row->name);

			if ($row->upgrade == 0 || $row->upgrade == 3) {
				$blob .= "  (<highlight>$row->vp<end> VP)";
				$total_vp = $total_vp + $row->vp;
			}
			$blob .= "\n";
		}
		$blob .= "\nVP Cost for full set: <highlight>$total_vp<end>";

		$msg = $this->text->make_blob("$profession Ofab Armor (QL $ql)", $blob);
		$sendto->reply($msg);
	}

	/**
	 * This command handler shows Ofab weapons and VP cost.
	 *
	 * @HandlesCommand("ofabweapons")
	 * @Matches("/^ofabweapons$/i")
	 */
	public function ofabweaponsCommand($message, $channel, $sender, $sendto, $args) {
		$qls = $this->db->query("SELECT DISTINCT ql FROM ofabweaponscost ORDER BY ql ASC");
		$data = $this->db->query("SELECT `type`, `name` FROM ofabweapons ORDER BY name ASC");

		$blob = '';
		forEach ($data as $row) {
			$blob .= "<pagebreak>{$row->name} - Type {$row->type}\n";
			forEach ($qls as $row2) {
				$ql_link = $this->text->make_chatcmd($row2->ql, "/tell <myname> ofabweapons {$row->name} {$row2->ql}");
				$blob .= "[{$ql_link}] ";
			}
			$blob .= "\n\n";
		}

		$msg = $this->text->make_blob("Ofab Weapons", $blob);
		$sendto->reply($msg);
	}

	/**
	 * This command handler shows all six marks of the Ofab weapon.
	 *
	 * @HandlesCommand("ofabweapons")
	 * @Matches("/^ofabweapons (.+)$/i")
	 */
	public function ofabweaponsInfoCommand($message, $channel, $sender, $sendto, $args) {
		if (preg_match("/^(.+) (\\d+)$/i", $args[1], $arr)) {
			$ql = $arr[2];
		}
		else {
			$ql = 300;
		}
		$weapon = ucfirst($arr[1]);

		$row = $this->db->queryRow("SELECT `type`, `vp` FROM ofabweapons w, ofabweaponscost c WHERE w.name = ? AND c.ql = ?", $weapon, $ql);
		if ($row === null) {
			return false;
		}

		$blob = '';
		$typeQl = round(.8 * $ql);
		$typeLink = $this->text->make_chatcmd("Kyr'Ozch Bio-Material - Type {$row->type}", "/tell <myname> bioinfo {$row->type} {$typeQl}");
		$blob .= "Upgrade with $typeLink (minimum QL {$typeQl})\n\n";

		$qls = $this->db->query("SELECT DISTINCT ql FROM ofabweaponscost ORDER BY ql ASC");
		forEach ($qls as $row2) {
			if ($row2->ql == $ql) {
				$blob .= "[{$row2->ql}] ";
			} else {
				$ql_link = $this->text->make_chatcmd($row2->ql, "/tell <myname> ofabweapons {$weapon} {$row2->ql}");
				$blob .= "[{$ql_link}] ";
			}
		}
		$blob .= "\n\n";

		for ($i = 1; $i <= 6; $i++) {
			$blob .=  $this->makeAlienWeapon($ql, "Ofab {$weapon} Mk {$i}");
			if ($i == 1) {
				$blob .= "  (<highlight>{$row->vp}<end> VP)";
			}
			$blob .= "\n";
		}

		$msg = $this->text->make_blob("Ofab $weapon (QL $ql)", $blob);
		$sendto->reply($msg);
	}

	/**
	 * This command handler identifies Solid Clump of Kyr'Ozch Bio-Material.
	 *
	 * @HandlesCommand("bio")
	 * @Matches("/^bio (.+)$/i")
	 */
	public function bioCommand($message, $channel, $sender, $sendto, $args) {
		$bio_regex = "<a href=\"itemref:\/\/(\\d+)\/(\\d+)\/(\\d+)\">Solid Clump of Kyr\'Ozch Bio-Material<\/a>";

		if (!preg_match("/^(( *${bio_regex})+)$/i", $args[1], $arr)) {
			return false;
		}

		$bios = explode("*", preg_replace("/> *</", ">*<", $arr[1]));
		$blob = '';
		forEach ($bios as $bio) {
			preg_match("/^${bio_regex}$/i", trim($bio), $arr2);
			$highid = $arr2[2];
			$ql = $arr2[3];
			switch ($highid) {
				case 247707:
				case 247708:
					$bioinfo = "1";
					$name = "Kyr'Ozch Bio-Material - Type 1";
					break;
				case 247709:
				case 247710:
					$bioinfo = "2";
					$name = "Kyr'Ozch Bio-Material - Type 2";
					break;
				case 247717:
				case 247718:
					$bioinfo = "3";
					$name = "Kyr'Ozch Bio-Material - Type 3 ";
					break;
				case 247711:
				case 247712:
					$bioinfo = "4";
					$name = "Kyr'Ozch Bio-Material - Type 4";
					break;
				case 247713:
				case 247714:
					$bioinfo = "5";
					$name = "Kyr'Ozch Bio-Material - Type 5";
					break;
				case 247715:
				case 247716:
					$bioinfo = "12";
					$name = "Kyr'Ozch Bio-Material - Type 12 ";
					break;
				case 247719:
				case 247720:
					$bioinfo = "13";
					$name = "Kyr'Ozch Bio-Material - Type 13";
					break;
				case 288699:
				case 288700:
					$bioinfo = "48";
					$name = "Kyr'Ozch Bio-Material - Type 48";
					break;
				case 247697:
				case 247698:
					$bioinfo = "76";
					$name = "Kyr'Ozch Bio-Material - Type 76";
					break;
				case 247699:
				case 247700:
					$bioinfo = "112";
					$name = "Kyr'Ozch Bio-Material - Type 112";
					break;
				case 247701:
				case 247702:
					$bioinfo = "240";
					$name = "Kyr'Ozch Bio-Material - Type 240";
					break;
				case 247703:
				case 247704:
					$bioinfo = "880";
					$name = "Kyr'Ozch Bio-Material - Type 880";
					break;
				case 247705:
				case 247706:
					$bioinfo = "992";
					$name = "Kyr'Ozch Bio-Material - Type 992";
					break;
				case 247102:
				case 247103:
					$bioinfo = "pristine";
					$name = "Pristine Kyr'Ozch Bio-Material";
					break;
				case 247104:
				case 247105:
					$bioinfo = "mutated";
					$name = "Mutated Kyr'Ozch Bio-Material";
					break;
				case 247764:
				case 254804:
					$bioinfo = "serum";
					$name = "Kyr'Ozch Viral Serum";
					break;
				default:
					$bioinfo = "";
					$name = "Unknown Bio-Material";
					continue;
			}

			$biotype_link = $this->text->make_chatcmd($name, "/tell <myname> bioinfo $bioinfo $ql");
			$blob .= $biotype_link . "\n\n";
		}

		if (count($bios) == 1) {
			// make the bot think they actually typed the !bioinfo command
			$this->commandManager->process($channel, "bioinfo $bioinfo $ql", $sender, $sendto);
		} else {
			$msg = $this->text->make_blob("Identified Bio-Materials", $blob);
			$sendto->reply($msg);
		}
	}

	/**
	 * This command handler shows info about Alien City Generals.
	 *
	 * @HandlesCommand("aigen")
	 * @Matches("/^aigen (ankari|ilari|rimah|jaax|xoch|cha)$/i")
	 */
	public function aigenCommand($message, $channel, $sender, $sendto, $args) {
		$gen = ucfirst(strtolower($args[1]));

		$blob = '';
		switch ($gen) {
			case "Ankari":
				$blob .= "<red>Low Evade/Dodge,low AR, casting Viral/Virral nukes.<blue> \nBoss of this type drops:\n\n<img src=rdb://100337></img>\n<a href='itemref://247145/247145/300'>Arithmetic Lead Viralbots</a>.\n<orange>(Nanoskill / Tradeskill)<blue>\n<img src=rdb://255705></img>\n<a href='itemref://247684/247684/300'>Kyr'Ozch Bio-Material - Type 1</a>\n<img src=rdb://255705></img>\n<a href='itemref://247685/247685/300'>Kyr'Ozch Bio-Material - Type 2</a>\n<img src=rdb://255705></img>\n<a href='itemref://288672/288673/300'>Kyr'Ozch Bio-Material - Type 48</a>";
				break;
			case "Ilari":
				$blob .= "<red>Low Evade/Dodge.<blue>\nBoss of this type drops:\n\n<img src=rdb://100337></img>\n<a href='itemref://247146/247146/300'>Spiritual Lead Viralbots</a>.<orange>\n(Nanocost / Nanopool / Max Nano)<blue>\n<img src=rdb://255705></img>\n<a href='itemref://247681/247681/300'>Kyr'Ozch Bio-Material - Type 992</a>\n<img src=rdb://255705></img>\n<a href='itemref://247679/247679/300'>Kyr'Ozch Bio-Material - Type 880</a>";
				break;
			case "Rimah":
				$blob .= "<red>Low Evade/Dodge.<blue>\nBoss of this type drops:\n\n<img src=rdb://100337></img>\n<a href='itemref://247143/247143/300'>Observant Lead Viralbots</a>.<orange>\n(Init / Evades)<blue>\n<img src=rdb://255705></img>\n<a href='itemref://247675/247675/300'>Kyr'Ozch Bio-Material - Type 112</a>\n<img src=rdb://255705></img>\n<a href='itemref://247678/247678/300'>Kyr'Ozch Bio-Material - Type 240</a>";
				break;
			case "Jaax":
				$blob .= "<red>High Evade, Low Dodge.<blue>\nBoss of this type drops:\n\n<img src=rdb://100337></img>\n<a href='itemref://247139/247139/300'>Strong Lead Viralbots</a>.<orange>\n(Melee / Spec Melee / Add All Def / Add Damage)\n<blue><img src=rdb://255705></img>\n<a href='itemref://247694/247694/300'>Kyr'Ozch Bio-Material - Type 3</a>\n<img src=rdb://255705></img>\n<a href='itemref://247688/247688/300'>Kyr'Ozch Bio-Material - Type 4</a>";
				break;
			case "Xoch":
				$blob .= "<red>High Evade/Dodge, casting Ilari Biorejuvenation heals.<blue>\nBoss of this type drops:\n\n<img src=rdb://100337></img>\n<a href='itemref://247137/247137/300'>Enduring Lead Viralbots</a>.<orange>\n(Max Health / Body Dev)<blue>\n<img src=rdb://255705></img>\n<a href='itemref://247690/247690/300'>Kyr'Ozch Bio-Material - Type 5</a>\n<img src=rdb://255705></img>\n<a href='itemref://247692/247692/300'>Kyr'Ozch Bio-Material - Type 12</a>";
				break;
			case "Cha":
				$blob .= "<red>High Evade/NR, Low Dodge.<blue>\nBoss of this type drops:\n\n<img src=rdb://100337></img>\n<a href='itemref://247141/247141/300'>Supple Lead Viralbots</a>.<orange>\n(Ranged / Spec Ranged / Add All Off)\n<img src=rdb://255705></img>\n<a href='itemref://247696/247696/300'>Kyr'Ozch Bio-Material - Type 13</a>\n<img src=rdb://255705></img>\n<a href='itemref://247674/247674/300'>Kyr'Ozch Bio-Material - Type 76</a>";
				break;
		}

		$msg = $this->text->make_blob("Info about General $gen", $blob);
		$sendto->reply($msg);
	}

	/**
	 * This command handler shows tradeskill process for Alien Armor.
	 *
	 * @HandlesCommand("aiarmor")
	 * @Matches("/^aiarmor (strong|supple|enduring|observant|arithmetic|spiritual|cc|cm|co|cp|cs|css|ss)$/i")
	 * @Matches("/^aiarmor (strong|supple|enduring|observant|arithmetic|spiritual|cc|cm|co|cp|cs|css|ss) (\d+)$/i")
	 * @Matches("/^aiarmor (\d+) (strong|supple|enduring|observant|arithmetic|spiritual|cc|cm|co|cp|cs|css|ss)$/i")
	 */
	public function aiarmorCommand($message, $channel, $sender, $sendto, $args) {
		$armortype = '';
		$ql = 300;
		// get ql and armor type from command arguments
		for ($i = 1; $i < count($args); $i++) {
			$value = $args[$i];
			if (is_numeric($value)) {
				if ($value >= 1 && $value <= 300) {
					$ql = intval($value);
				}
			} else {
				$armortype = strtolower($value);
			}
		}
		if (preg_match("/^(cc|cm|co|cp|cs|css|ss)$/i", $armortype)) {

			$trg_ql = $ql;
			$src_ql = floor($trg_ql * 0.8);

			switch ($armortype) {
				case 'cc':
					//Result
					$icon_armor_result = 256308;
					$name_armor_result = "Combined Commando's";
					$lowid_armor_result = 246659;
					$highid_armor_result = 246660;
					//Source
					$icon_armor_src = 256362;
					$name_armor_src = "Strong";
					$lowid_armor_src = 246615;
					$highid_armor_src = 246616;

					//Target
					$icon_armor_trg = 256296;
					$name_armor_trg = "Supple";
					$lowid_armor_trg = 246621;
					$highid_armor_trg = 246622;
					break;

				case 'cm':
					//Result
					$icon_armor_result = 256356;
					$name_armor_result = "Combined Mercenary's";
					$lowid_armor_result = 246637;
					$highid_armor_result = 246638;

					//Source
					$icon_armor_src = 256362;
					$name_armor_src = "Strong";
					$lowid_armor_src = 246615;
					$highid_armor_src = 246616;

					//Target
					$icon_armor_trg = 256344;
					$name_armor_trg = "Enduring";
					$lowid_armor_trg = 246579;
					$highid_armor_trg = 246580;
					break;

				case 'co':
					//Result
					$icon_armor_result = 256320;
					$name_armor_result = "Combined Officer's";
					$lowid_armor_result = 246671;
					$highid_armor_result = 246672;

					//Source
					$icon_armor_src = 256332;
					$name_armor_src = "Spiritual";
					$lowid_armor_src = 246599;
					$highid_armor_src = 246600;

					//Target
					$icon_armor_trg = 256314;
					$name_armor_trg = "Arithmetic";
					$lowid_armor_trg = 246559;
					$highid_armor_trg = 246560;
					break;

				case 'cp':
					//Result
					$icon_armor_result = 256350;
					$name_armor_result = "Combined Paramedic's";
					$lowid_armor_result = 246647;
					$highid_armor_result = 246648;

					//Source
					$icon_armor_src = 256332;
					$name_armor_src = "Spiritual";
					$lowid_armor_src = 246599;
					$highid_armor_src = 246600;

					//Target
					$icon_armor_trg = 256344;
					$name_armor_trg = "Enduring";
					$lowid_armor_trg = 246579;
					$highid_armor_trg = 246580;
					break;

				case 'cs':
					//Result
					$icon_armor_result = 256326;
					$name_armor_result = "Combined Scout's";
					$lowid_armor_result = 246683;
					$highid_armor_result = 246684;

					//Source
					$icon_armor_src = 256338;
					$name_armor_src = "Observant";
					$lowid_armor_src = 246591;
					$highid_armor_src = 246592;

					//Target
					$icon_armor_trg = 256314;
					$name_armor_trg = "Arithmetic";
					$lowid_armor_trg = 246559;
					$highid_armor_trg = 246560;
					break;

				case 'css':
				case 'ss':
					//Result
					$icon_armor_result = 256302;
					$name_armor_result = "Combined Sharpshooter's";
					$lowid_armor_result = 246695;
					$highid_armor_result = 246696;

					//Source
					$icon_armor_src = 256338;
					$name_armor_src = "Observant";
					$lowid_armor_src = 246591;
					$highid_armor_src = 246592;

					//Target
					$icon_armor_trg = 256296;
					$name_armor_trg = "Supple";
					$lowid_armor_trg = 246621;
					$highid_armor_trg = 246622;
					break;
			}

			$list = "<u>Result</u> \n";
			$list .= "<img src=rdb://$icon_armor_result>\n";
			$list .= "<a href='itemref://$lowid_armor_result/$highid_armor_result/$ql'>QL$ql $name_armor_result</a>\n\n";

			$list .= "<u>Source Armor</u>\n";
			$list .= "<img src=rdb://$icon_armor_src>\n";
			$list .= "<a href='itemref://$lowid_armor_src/$highid_armor_src/$src_ql'>QL$src_ql $name_armor_src</a> (";
			$list .= $this->text->make_chatcmd("Tradeskill process for this item", "/tell <myname> aiarmor $name_armor_src $src_ql").")\n\n";

			$list .= "<u>Target Armor</u>\n";
			$list .= "<img src=rdb://$icon_armor_trg>\n";
			$list .= "<a href='itemref://$lowid_armor_trg/$highid_armor_trg/$trg_ql'>QL$trg_ql $name_armor_trg</a> (";
			$list .= $this->text->make_chatcmd("Tradeskill process for this item", "/tell <myname> aiarmor $name_armor_trg $trg_ql").")";
			$msg = $this->text->make_blob("Building process for $ql $name_armor_result", $list);
			$sendto->reply($msg);
		} else if (preg_match("/^(strong|supple|enduring|observant|arithmetic|spiritual)$/i", $armortype)) {
			$armortype = ucfirst($armortype);
			$misc_ql = floor($ql * 0.8);

			$list = " Note: <highlight>All tradeskill processes are based on the lowest QL items usable.<end>\n\n";
			$list .= "<highlight>You need the following items to build $armortype Armor:\n<end>";
			$list .= "- Kyr'Ozch Viralbots\n";
			$list .= "- Kyr'Ozch Atomic Re-Structulazing Tool\n";
			$list .= "- Solid Clump of Kyr'Ozch Biomaterial\n";
			$list .= "- Arithmetic/Strong/Enduring/Spiritual/Observant/Supple Viralbots\n\n";

			$list .= "<highlight><u>Step 1</u><end>\n";
			$list .= "<tab><img src=rdb://100330>\n<a href='itemref://247113/247114/$misc_ql'>Kyr'Ozch Viralbots</a> (<highlight>Drops of Alien City Generals<end>)\n";
			$list .= "<tab><tab>+\n";
			$list .= "<tab><img src=rdb://247098>\n<a href='itemref://247099/247099/100'>Kyr'Ozch Atomic Re-Structuralizing Tool</a> (<highlight>Drops of every Alien<end>)\n";
			$list .= "<tab><tab>=\n";
			$list .= "<tab><img src=rdb://100331>\n<a href='itemref://247118/247119/$misc_ql'>Memory-Wiped Kyr'Ozch Viralbots</a>\n";
			$list .= "<highlight>Required Skills:<end>\n";
			$list .= "- ".ceil($misc_ql * 4.5)." Computer Literacy\n";
			$list .= "- ".ceil($misc_ql * 4.5)." Nano Programming\n\n";

			$list .= "<highlight><u>Step 2</u><end>\n";
			$list .= "<tab><img src=rdb://99279>\n<a href='itemref://161699/161699/1'>Nano Programming Interface</a> (<highlight>Can be bought in General Shops<end>)\n";
			$list .= "<tab><tab>+\n";
			$list .= "<tab><img src=rdb://100331>\n<a href='itemref://247118/247119/$misc_ql'>Memory-Wiped Kyr'Ozch Viralbots</a>\n";
			$list .= "<tab><tab>=\n";
			$list .= "<tab><img src=rdb://100334>\n<a href='itemref://247120/247121/$misc_ql'>Formatted Kyr'Ozch Viralbots</a>\n";
			$list .= "<highlight>Required Skills:<end>\n";
			$list .= "- ".ceil($misc_ql * 4.5)." Computer Literacy\n";
			$list .= "- ".ceil($misc_ql * 6)." Nano Programming\n\n";

			$list .= "<highlight><u>Step 3</u><end>\n";
			$list .= "<tab><img src=rdb://247097>\n<a href='itemref://247100/247100/100'>Kyr'Ozch Structural Analyzer</a>\n";
			$list .= "<tab><tab>+\n";
			$list .= "<tab><img src=rdb://247101>\n<a href='itemref://247102/247103/$ql'>QL$ql Solid Clump of Kyr'Ozch Biomaterial</a> (<highlight>Drops of every Alien<end>)\n";
			$list .= "<tab><tab>=\n";
			$list .= "<tab><img src=rdb://255705>\n<a href='itemref://247108/247109/$ql'>QL$ql Mutated Kyr'Ozch Biomaterial</a> or <a href='itemref://247106/247107/$ql'>QL$ql Pristine Kyr'Ozch Biomaterial</a>\n";
			$list .= "<highlight>Required Skills:<end>\n";
			$list .= "- ".ceil($ql * 4.5)." Chemistry (Both require the same amount)\n\n";

			$list .= "<highlight><u>Step 4</u><end>\n";
			$list .= "<tab><img src=rdb://255705>\n<a href='itemref://247108/247109/$ql'>QL$ql Mutated Kyr'Ozch Biomaterial</a> or <a href='itemref://247106/247107/$ql'>QL$ql Pristine Kyr'Ozch Biomaterial</a>\n";
			$list .= "<tab><tab>+\n";
			$list .= "<tab><img src=rdb://100333>\n<a href='itemref://247110/247110/100'>Uncle Bazzit's Generic Nano Solvent</a> (<highlight>Can be bought in Bazzit Shop in MMD<end>)\n";
			$list .= "<tab><tab>=\n";
			$list .= "<tab><img src=rdb://247115>\n<a href='itemref://247111/247112/$ql'>Generic Kyr'Ozch DNA Soup</a>\n";
			$list .= "<highlight>Required Skills:<end>\n";
			$list .= "- ".ceil($ql * 4.5)." Chemistry(for Pristine)\n";
			$list .= "- ".ceil($ql * 7)." Chemistry(for Mutated)\n\n";

			$list .= "<highlight><u>Step 5</u><end>\n";
			$list .= "<tab><img src=rdb://247115>\n<a href='itemref://247111/247112/$ql'>Generic Kyr'Ozch DNA Soup</a>\n";
			$list .= "<tab><tab>+\n";
			$list .= "<tab><img src=rdb://247122>\n<a href='itemref://247123/247123/100'>Essential Human DNA</a> (<highlight>Can be bought in Bazzit Shop in MMD<end>)\n";
			$list .= "<tab><tab>=\n";
			$list .= "<tab><img src=rdb://247116>\n<a href='itemref://247124/247125/$ql'>DNA Cocktail</a>\n";
			$list .= "<highlight>Required Skills:<end>\n";
			$list .= "- ".ceil($ql * 6)." Pharma Tech\n\n";

			$list .= "<highlight><u>Step 6</u><end>\n";
			$list .= "<tab><img src=rdb://100334>\n<a href='itemref://247120/247121/$misc_ql'>Formatted Kyr'Ozch Viralbots</a>\n";
			$list .= "<tab><tab>+\n";
			$list .= "<tab><img src=rdb://247116>\n<a href='itemref://247124/247125/$ql'>DNA Cocktail</a>\n";
			$list .= "<tab><tab>=\n";
			$list .= "<tab><img src=rdb://247117>\n<a href='itemref://247126/247127/$ql'>Kyr'Ozch Formatted Viralbot Solution</a>\n";
			$list .= "<highlight>Required Skills:<end>\n";
			$list .= "- ".ceil($ql * 6)." Pharma Tech\n\n";

			$list .= "<highlight><u>Step 7</u><end>\n";
			$list .= "<tab><img src=rdb://247117>\n<a href='itemref://247126/247127/$ql'>Kyr'Ozch Formatted Viralbot Solution</a>\n";
			$list .= "<tab><tab>+\n";
			$list .= "<tab><img src=rdb://245924>\n<a href='itemref://247163/247163/1'>Basic Vest</a> (<highlight>Can be obtained by the Basic Armor Quest<end>)\n";
			$list .= "<tab><tab>=\n";
			$list .= "<tab><img src=rdb://245924>\n<a href='itemref://247172/247173/$ql'>Formatted Viralbot Vest</a>\n\n";

			$list .= "<highlight><u>Step 8</u><end>\n";
			$list .= "<tab><img src=rdb://100337>\n";

			$vb_ql = floor($ql * 0.8);
			switch ($armortype) {
				case "Arithmetic":
					$list .= "<a href='itemref://247144/247145/$vb_ql'>QL$vb_ql Arithmetic Lead Viralbots</a> (<highlight>Rare Drop off Alien City Generals<end>)\n";
					break;
				case "Supple":
					$list .= "<a href='itemref://247140/247141/$vb_ql'>QL$vb_ql Supple Lead Viralbots</a> (<highlight>Rare Drop off Alien City Generals<end>)\n";
					break;
				case "Enduring":
					$list .= "<a href='itemref://247136/247137/$vb_ql'>QL$vb_ql Enduring Lead Viralbots</a> (<highlight>Rare Drop off Alien City Generals<end>)\n";
					break;
				case "Observant":
					$list .= "<a href='itemref://247142/247143/$vb_ql'>QL$vb_ql Observant Lead Viralbots</a> (<highlight>Rare Drop off Alien City Generals<end>)\n";
					break;
				case "Strong":
					$list .= "<a href='itemref://247138/247139/$vb_ql'>QL$vb_ql Strong Lead Viralbots</a> (<highlight>Rare Drop off Alien City Generals<end>)\n";
					break;
				case "Spiritual":
					$list .= "<a href='itemref://247146/247147/$vb_ql'>QL$vb_ql Spiritual Lead Viralbots</a> (<highlight>Rare Drop off Alien City Generals<end>)\n";
					break;
			}
			$list .= "<tab><tab>+\n";
			$list .= "<tab><img src=rdb://245924>\n<a href='itemref://247172/247173/$ql'>Formatted Viralbot Vest</a></a>\n";
			$list .= "<tab><tab>=\n";
			switch ($armortype) {
				case "Arithmetic":
					$list .= "<tab><img src=rdb://256314>\n<a href='itemref://246559/246560/$ql'>QL$ql Arithmetic Body Armor</a>\n";
					break;
				case "Supple":
					$list .= "<tab><img src=rdb://256296>\n<a href='itemref://246621/246622/$ql'>QL$ql Supple Body Armor</a>\n";
					break;
				case "Enduring":
					$list .= "<tab><img src=rdb://256344>\n<a href='itemref://246579/246580/$ql'>QL$ql Enduring Body Armor</a>\n";
					break;
				case "Observant":
					$list .= "<tab><img src=rdb://256338>\n<a href='itemref://246591/246592/$ql'>QL$ql Observant Body Armor</a></a>\n";
					break;
				case "Strong":
					$list .= "<tab><img src=rdb://256362>\n<a href='itemref://246615/246616/$ql'>QL$ql Strong Body Armor</a>\n";
					break;
				case "Spiritual":
					$list .= "<tab><img src=rdb://256332>\n<a href='itemref://246600/246601/$ql'>QL$ql Spiritual Body Armor</a>\n";
					break;
			}
			$list .= "<highlight>Required Skills:<end>\n";
			$list .= "- ".floor($ql * 6)." Psychology\n\n";

			$msg = $this->text->make_blob("Building process for $ql $armortype", $list);
			$sendto->reply($msg);
		} else {
			return false;
		}
	}

	/**
	 * This command handler shows info about a particular bio type.
	 * @HandlesCommand("bioinfo")
	 * @Matches("/^bioinfo (.+) (\d+)$/i")
	 * @Matches("/^bioinfo (.+)$/i")
	 */
	public function bioinfoCommand($message, $channel, $sender, $sendto, $args) {
		$bio = strtolower($args[1]);
		$ql = 300;
		if ($args[2]) {
			$ql = $args[2];
		}
		if ($ql < 1) {
			$ql = 1;
		} else if ($ql > 300) {
			$ql = 300;
		}
		switch ($bio) {
			// Ofab armor types
			case '64':
			case '295':
			case '468':
			case '935':
				$msg = $this->ofabArmorBio($ql, $bio);
				break;

			// Ofab weapon types
			case '18':
			case '34':
			case '687':
			case '812':
				$msg = $this->ofabWeaponBio($ql, $bio);
				break;

			// AI weapon types
			case '1':
			case '2':
			case '3':
			case '4':
			case '5':
			case '12':
			case '13':
			case '48':
			case '76':
			case '112':
			case '240':
			case '880':
			case '992':
				$msg = $this->alienWeaponBio($ql, $bio);
				break;

			// AI armor types
			case 'pristine':
			case 'mutated':
				$msg = $this->alienArmorBio($ql, $bio);
				break;

			case 'serum':
				$msg = $this->serumBio($ql, $bio);
				break;

			default:
				$msg = "Unknown Bio-Material";
				break;
		}

		$sendto->reply($msg);
	}
	
	/**
	 * Returns item reference to item with given $ql and $name.
	 */
	private function findItem($ql, $name) {
		$row = $this->db->queryRow("SELECT * FROM aodb WHERE name = ? AND lowql <= ? AND highql >= ?", $name, $ql, $ql);

		return $this->text->make_item($row->lowid, $row->highid, $ql, $row->name);
	}

	/**
	 * Returns information of how much weapon of given $ql requires skills
	 * to upgrade it.
	 */
	private function getWeaponInfo($ql) {
		$ts_wep = floor($ql * 6);
		$text .= "\n\n<highlight>QL $ql<end> is the highest weapon this type will combine into.";
		if ($ql != 300) {
			$text .= "\nNote: <highlight>The weapon can bump several QL's.<end>";
		}
		$text .= "\n\nIt will take <highlight>$ts_wep<end> ME & WS (<highlight>6 * QL<end>) to combine with a <highlight>QL $ql<end> Kyr'ozch Weapon.";

		return $text;
	}

	/**
	 * Returns list of professions (in a blob) whose ofab armor given $type
	 * will upgrade.
	 */
	private function ofabArmorBio($ql, $type) {
		$name = "Kyr'Ozch Bio-Material - Type $type";
		$item = $this->findItem($ql, $name);

		$data = $this->db->query("SELECT * FROM ofabarmortype WHERE type = ?", $type);

		$blob = $item . "\n\n";
		$blob .= "<highlight>Upgrades Ofab armor for:<end>\n";
		forEach ($data as $row) {
			$blob .= $this->text->make_chatcmd($row->profession, "/tell <myname> ofabarmor {$row->profession}") . "\n";
		}

		return $this->text->make_blob("$name (QL $ql)", $blob);
	}

	/**
	 * Returns list of professions (in a blob) whose ofab weapon given $type
	 * will upgrade.
	 */
	private function ofabWeaponBio($ql, $type) {
		$name = "Kyr'Ozch Bio-Material - Type $type";
		$item = $this->findItem($ql, $name);

		$data = $this->db->query("SELECT * FROM ofabweapons WHERE type = ?", $type);

		$blob = $item . "\n\n";
		$blob .= "<highlight>Upgrades Ofab weapons:<end>\n";
		forEach ($data as $row) {
			$blob .= $this->text->make_chatcmd("Ofab {$row->name} Mk 1", "/tell <myname> ofabweapons {$row->name}") . "\n";
		}

		return $this->text->make_blob("$name (QL $ql)", $blob);
	}

	/**
	 * Returns what special attacks given bio type adds to each ofab weapon and
	 * tells how much skills analyzing the clump requires and how much skills
	 * is needed to upgrade the weapon.
	 */
	private function alienWeaponBio($ql, $type) {
		$name = "Kyr'Ozch Bio-Material - Type $type";
		$item = $this->findItem($ql, $name);

		// Ensures that the maximum AI weapon that combines into doesn't go over QL 300 when the user presents a QL 271+ bio-material
		$maxaitype = floor($ql / 0.9);
		if ($maxaitype > 300 || $maxaitype < 1) {
			$maxaitype = 300;
		}

		$ts_bio = floor($ql * 4.5);

		$row = $this->db->queryRow("SELECT specials FROM alienweaponspecials WHERE type = ?", $type);
		$specials = $row->specials;

		$data = $this->db->query("SELECT * FROM alienweapons WHERE type = ?", $type);

		$blob = $item . "\n\n";
		$blob .= "It will take <highlight>$ts_bio<end> EE & CL (<highlight>4.5 * QL<end>) to analyze the Bio-Material.\n\n";
		$blob .= "<highlight>Adds {$specials} to:<end>\n";
		forEach ($data as $row) {
			$blob .= $this->findItem($maxaitype, $row->name) . "\n";
		}
		$blob .= $this->getWeaponInfo($maxaitype);
		$blob .= "\n\n<yellow>Tradeskilling info added by Mdkdoc420 (RK2)<end>";

		return $this->text->make_blob("$name (QL $ql)", $blob);
	}

	/**
	 * Returns what skills and how much is required for analyzing the bio
	 * material and building alien armor of it.
	 */
	private function alienArmorBio($ql, $type) {
		// All the min/max QL and tradeskill calcs for the mutated/pristine process
		$min_ql = floor($ql * 0.8);
		if ($min_ql < 1) {
			$min_ql = 1;
		}
		if ($ql >= 1 && $ql <= 240) {
			$max_ql = floor($ql / 0.8);
		} else {
			$max_ql = 300;
		}

		$cl = floor($min_ql * 4.5);
		$pharma = floor($ql * 6);
		$np = floor($min_ql * 6);
		$psyco = floor($ql * 6);
		$max_psyco = floor($max_ql * 6);
		$ts_bio = floor($ql * 4.5);
		if (strtolower($type) == "mutated") {
			$name = "Mutated Kyr'Ozch Bio-Material";
			$chem = floor($ql * 7);
			$chem_msg = "7 * QL";
		} else if (strtolower($type) == "pristine") {
			$name = "Pristine Kyr'Ozch Bio-Material";
			$chem = floor($ql * 4.5);
			$chem_msg = "4.5 * QL";
			$extraInfo = "(<highlight>less tradeskill requirements then mutated.<end>)";
		} else {
			$name = "UNKNOWN";
		}
		//End of tradeskill processes

		$item = $this->findItem($ql, $name);

		$blob = $item . "\n\n";
		$blob .= "It will take <highlight>$ts_bio<end> EE & CL (<highlight>4.5 * QL<end>) to analyze the Bio-Material.\n\n";

		$blob .= "Used to build Alien Armor $extraInfo\n\n" .
			"<highlight>The following tradeskill amounts are required to make<end> QL $ql<highlight>\n" .
			"strong/arithmetic/enduring/spiritual/supple/observant armor:<end>\n\n" .
			"Computer Literacy - <highlight>$cl<end> (<highlight>4.5 * QL<end>)\n" .
			"Chemistry - <highlight>$chem<end> (<highlight>$chem_msg<end>)\n" .
			"Nano Programming - <highlight>$np<end> (<highlight>6 * QL<end>)\n" .
			"Pharma Tech - <highlight>$pharma<end> (<highlight>6 * QL<end>)\n" .
			"Psychology - <highlight>$psyco<end> (<highlight>6 * QL<end>)\n\n" .
			"Note:<highlight> Tradeskill requirements are based off the lowest QL items needed throughout the entire process.<end>";

		$blob .= "\n\nFor Supple, Arithmetic, or Enduring:\n\n" .
			"<highlight>When completed, the armor piece can have as low as<end> QL $min_ql <highlight>combined into it, depending on available tradeskill options.\n\n" .
			"Does not change QL's, therefore takes<end> $psyco <highlight>Psychology for available combinations.<end>\n\n" .
			"For Spiritual, Strong, or Observant:\n\n" .
			"<highlight>When completed, the armor piece can combine upto<end> QL $max_ql<highlight>, depending on available tradeskill options.\n\n" .
			"Changes QL depending on targets QL. The max combination is: (<end>QL $max_ql<highlight>) (<end>$max_psyco Psychology required for this combination<highlight>)<end>";

		$blob .= "\n\n<yellow>Tradeskilling info added by Mdkdoc420 (RK2)<end>";

		return $this->text->make_blob("$name (QL $ql)", $blob);
	}

	/**
	 * Tells how much skills is required to analyze serum bio material and how
	 * much skills are needed to to build buildings from it.
	 */
	private function serumBio($ql, $type) {
		$name = "Kyr'Ozch Viral Serum";
		$item = $this->findItem($ql, $name);

		$pharma_ts = floor($ql * 3.5);
		$chem_me_ts = floor($ql * 4);
		$ee_ts = floor($ql * 4.5);
		$cl_ts = floor($ql * 5);
		$ts_bio = floor($ql * 4.5);

		$blob = $item . "\n\n";
		$blob .= "It will take <highlight>$ts_bio<end> EE & CL (<highlight>4.5 * QL<end>) to analyze the Bio-Material.\n\n";

		$blob .= "<highlight>Used to build city buildings<end>\n\n" .
			"<highlight>The following are the required skills throughout the process of making a building:<end>\n\n" .
			"Quantum FT - <highlight>400<end> (<highlight>Static<end>)\nPharma Tech - ";

		//Used to change dialog between minimum and actual requirements, for requirements that go under 400
		if ($pharma_ts < 400) {
			$blob .= "<highlight>400<end>";
		} else {
			$blob .= "<highlight>$pharma_ts<end>";
		}

		$blob .= " (<highlight>3.5 * QL<end>) 400 is minimum requirement\nChemistry - ";

		if ($chem_me_ts < 400) {
			$blob .= "<highlight>400<end>";
		} else {
			$blob .= "<highlight>$chem_me_ts<end>";
		}

		$blob .= " (<highlight>4 * QL<end>) 400 is minimum requirement\n" .
			"Mechanical Engineering - <highlight>$chem_me_ts<end> (<highlight>4 * QL<end>)\n" .
			"Electrical Engineering - <highlight>$ee_ts<end> (<highlight>4.5 * QL<end>)\n" .
			"Comp Liter - <highlight>$cl_ts<end> (<highlight>5 * QL<end>)";

		$blob .= "\n\n<yellow>Tradeskilling info added by Mdkdoc420 (RK2)<end>";

		return $this->text->make_blob("$name (QL $ql)", $blob);
	}
	
	/**
	 * Returns item reference to alien weapon of given $name and $ql.
	 */
	public function makeAlienWeapon($ql, $name) {
		$row = $this->db->queryRow("SELECT * FROM aodb WHERE name = ? AND lowql <= ? AND highql >= ?", $name, $ql, $ql);
		return $this->text->make_item($row->lowid, $row->highid, $ql, $row->name);
	}
}