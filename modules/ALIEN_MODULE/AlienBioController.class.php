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
 *		command     = 'bio',
 *		accessLevel = 'all', 
 *		description = "Identifies Solid Clump of Kyr'Ozch Bio-Material", 
 *		help        = 'bio.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'bioinfo',
 *      alias       = 'biotype',
 *		accessLevel = 'all', 
 *		description = 'Shows info about a particular bio type', 
 *		help        = 'bioinfo.txt'
 *	)
 */
class AlienBioController {

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
	public $itemsController;

	/** @Logger */
	public $logger;
	
	private $leArmorTypes = array('64', '295', '468', '935');
	private $leWeaponTypes = array('18', '34', '687', '812');
	private $aiArmorTypes = array('mutated', 'pristine');
	private $aiWeaponTypes = array('1', '2', '3', '4', '5', '12', '13', '48', '76', '112', '240', '880', '992');

	/**
	 * This handler is called on bot startup.
	 * @Setup
	 */
	public function setup() {
		// load database tables from .sql-files
		$this->db->loadSQLFile($this->moduleName, 'alienweapons');
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
			// if there is only one bio, show detailed info by calling !bioinfo command handler directly
			$this->bioinfoCommand("", $channel, $sender, $sendto, array("bioinfo $bioinfo $ql", $bioinfo, $ql));
		} else {
			$msg = $this->text->make_blob("Identified Bio-Materials", $blob);
			$sendto->reply($msg);
		}
	}
	
	/**
	 * @HandlesCommand("bioinfo")
	 * @Matches("/^bioinfo$/i")
	 */
	public function bioinfoListCommand($message, $channel, $sender, $sendto, $args) {
		$blob .= "<header2>OFAB Armor Types<end>\n";
		$blob .= $this->getTypeBlob($this->leArmorTypes);
		
		$blob .= "\n<header2>OFAB Weapon Types<end>\n";
		$blob .= $this->getTypeBlob($this->leWeaponTypes);
		
		$blob .= "\n<header2>AI Armor Types<end>\n";
		$blob .= $this->getTypeBlob($this->aiArmorTypes);
		
		$blob .= "\n<header2>AI Weapon Types<end>\n";
		$blob .= $this->getTypeBlob($this->aiWeaponTypes);
		
		$msg = $this->text->make_blob("Bio-Material Types", $blob);
		$sendto->reply($msg);
	}
	
	public function getTypeBlob($types) {
		$blob = '';
		forEach ($types as $type) {
			$blob .= $this->text->make_chatcmd($type, "/tell <myname> bioinfo $type") . "\n";
		}
		return $blob;
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

		if (in_array($bio, $this->leArmorTypes)) {
			$msg = $this->ofabArmorBio($ql, $bio);
		} else if (in_array($bio, $this->leWeaponTypes)) {
			$msg = $this->ofabWeaponBio($ql, $bio);
		} else if (in_array($bio, $this->aiArmorTypes)) {
			$msg = $this->alienArmorBio($ql, $bio);
		} else if (in_array($bio, $this->aiWeaponTypes)) {
			$msg = $this->alienWeaponBio($ql, $bio);
		} else if ($bio == 'serum') {
			$msg = $this->serumBio($ql, $bio);
		} else {
			$msg = "Unknown Bio-Material";
		}

		$sendto->reply($msg);
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
		$item = $this->itemsController->getItem($name, $ql);

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
		$item = $this->itemsController->getItem($name, $ql);

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
		$item = $this->itemsController->getItem($name, $ql);

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
			$blob .= $this->itemsController->getItem($maxaitype, $row->name) . "\n";
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

		$item = $this->itemsController->getItem($name, $ql);

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
		$item = $this->itemsController->getItem($name, $ql);

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
}
