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
 *		command     = 'aigen',
 *		accessLevel = 'all', 
 *		description = 'Shows info about Alien City Generals', 
 *		help        = 'aigen.txt'
 *	)
 */
class AlienMiscController {

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
	
	/** @Inject */
	public $itemsController;

	/** @Logger */
	public $logger;

	/**
	 * @Setup
	 */
	public function setup() {
		// load database tables from .sql-files
		$this->db->loadSQLFile($this->moduleName, 'leprocs');
		$this->db->loadSQLFile($this->moduleName, 'ofabarmor');
		$this->db->loadSQLFile($this->moduleName, 'ofabweapons');
	}

	/**
	 * This command handler shows menu of each profession's LE procs.
	 *
	 * @HandlesCommand("leprocs")
	 * @Matches("/^leprocs$/i")
	 */
	public function leprocsCommand($message, $channel, $sender, $sendto, $args) {
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
		$profession = $this->util->get_profession_name($args[1]);
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
	 * @Matches("/^ofabarmor (\S+)$/i")
	 * @Matches("/^ofabarmor (\S+) (\d+)$/i")
	 */
	public function ofabarmorInfoCommand($message, $channel, $sender, $sendto, $args) {
		$ql = isset($args[2])? intval($args[2]): 300;

		$profession = $this->util->get_profession_name($args[1]);

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
	 * @Matches("/^ofabweapons (\S+)$/i")
	 * @Matches("/^ofabweapons (\S+) (\d+)$/i")
	 */
	public function ofabweaponsInfoCommand($message, $channel, $sender, $sendto, $args) {
		$weapon = ucfirst($args[1]);
		$ql = isset($args[2])? intval($args[2]): 300;

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
			$blob .=  $this->itemsController->getItem("Ofab {$weapon} Mk {$i}", $ql);
			if ($i == 1) {
				$blob .= "  (<highlight>{$row->vp}<end> VP)";
			}
			$blob .= "\n";
		}

		$msg = $this->text->make_blob("Ofab $weapon (QL $ql)", $blob);
		$sendto->reply($msg);
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
				$blob .= "Low Evade/Dodge, Low AR, Casts Viral/Virral nukes\n\n";
				$blob .= $this->itemsController->getItemAndIcon("Arithmetic Lead Viralbots") . "\n";
				$blob .= "(Nanoskill / Tradeskill)\n\n";
				$blob .= $this->itemsController->getItemAndIcon("Kyr'Ozch Bio-Material - Type 1") . "\n\n";
				$blob .= $this->itemsController->getItemAndIcon("Kyr'Ozch Bio-Material - Type 2") . "\n\n";
				$blob .= $this->itemsController->getItemAndIcon("Kyr'Ozch Bio-Material - Type 48");
				break;
			case "Ilari":
				$blob .= "Low Evade/Dodge\n\n";
				$blob .= $this->itemsController->getItemAndIcon("Spiritual Lead Viralbots") . "\n";
				$blob .= "(Nanocost / Nanopool / Max Nano)\n\n";
				$blob .= $this->itemsController->getItemAndIcon("Kyr'Ozch Bio-Material - Type 992") . "\n\n";
				$blob .= $this->itemsController->getItemAndIcon("Kyr'Ozch Bio-Material - Type 880");
				break;
			case "Rimah":
				$blob .= "Low Evade/Dodge\n\n";
				$blob .= $this->itemsController->getItemAndIcon("Observant Lead Viralbots") . "\n";
				$blob .= "(Init / Evades)\n\n";
				$blob .= $this->itemsController->getItemAndIcon("Kyr'Ozch Bio-Material - Type 112") . "\n\n";
				$blob .= $this->itemsController->getItemAndIcon("Kyr'Ozch Bio-Material - Type 240");
				break;
			case "Jaax":
				$blob .= "High Evade, Low Dodge\n\n";
				$blob .= $this->itemsController->getItemAndIcon("Strong Lead Viralbots") . "\n";
				$blob .= "(Melee / Spec Melee / Add All Def / Add Damage)\n\n";
				$blob .= $this->itemsController->getItemAndIcon("Kyr'Ozch Bio-Material - Type 3") . "\n\n";
				$blob .= $this->itemsController->getItemAndIcon("Kyr'Ozch Bio-Material - Type 4");
				break;
			case "Xoch":
				$blob .= "High Evade/Dodge, Casts Ilari Biorejuvenation heals\n\n";
				$blob .= $this->itemsController->getItemAndIcon("Enduring Lead Viralbots") . "\n";
				$blob .= "(Max Health / Body Dev)\n\n";
				$blob .= $this->itemsController->getItemAndIcon("Kyr'Ozch Bio-Material - Type 5") . "\n\n";
				$blob .= $this->itemsController->getItemAndIcon("Kyr'Ozch Bio-Material - Type 12");
				break;
			case "Cha":
				$blob .= "High Evade/NR, Low Dodge\n\n";
				$blob .= $this->itemsController->getItemAndIcon("Supple Lead Viralbots") . "\n";
				$blob .= "(Ranged / Spec Ranged / Add All Off)\n\n";
				$blob .= $this->itemsController->getItemAndIcon("Kyr'Ozch Bio-Material - Type 13") . "\n\n";
				$blob .= $this->itemsController->getItemAndIcon("Kyr'Ozch Bio-Material - Type 76");
				break;
		}

		$msg = $this->text->make_blob("General $gen", $blob);
		$sendto->reply($msg);
	}
}
