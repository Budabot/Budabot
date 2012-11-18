<?php

/**
 * Authors: 
 *	- Marinerecon (RK2)
 *  - Derroylo (RK2)
 *  - Tyrence (RK2)
 *  - Morgo (RK2)
 *	- Chachy (RK2)
 *  - Dare2005 (RK2), based on code for dbloot module by Chachy (RK2)
 *
 * @Instance
 *
 * Commands this class contains:
 *	@DefineCommand(
 *		command     = 'alb',
 *		accessLevel = 'all',
 *		description = 'Shows Possible Albtraum loots',
 *		help        = 'albloot.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'db1',
 *		accessLevel = 'all',
 *		description = 'Shows Possible DB1 Armor/NCUs/Programs',
 *		help        = 'dbloot.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'db2',
 *		accessLevel = 'all',
 *		description = 'Shows Possible DB2 Armor',
 *		help        = 'dbloot.txt'
 *	)
 *	@DefineCommand(
 *		command     = '13',
 *		accessLevel = 'rl',
 *		description = 'Adds APF 13 loot to the loot list',
 *		help        = 'apf.txt'
 *	)
 *	@DefineCommand(
 *		command     = '28',
 *		accessLevel = 'rl',
 *		description = 'Adds APF 28 loot to the loot list',
 *		help        = 'apf.txt'
 *	)
 *	@DefineCommand(
 *		command     = '35',
 *		accessLevel = 'rl',
 *		description = 'Adds APF 35 loot to the loot list',
 *		help        = 'apf.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'apf',
 *		accessLevel = 'all',
 *		description = 'Shows what drops off APF Bosses',
 *		help        = 'apf.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'beastarmor',
 *		accessLevel = 'all',
 *		description = 'Shows Beast Armor loot',
 *		help        = 'pande.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'beastweaps',
 *		accessLevel = 'all',
 *		description = 'Shows Beast Weapons loot',
 *		help        = 'pande.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'beaststars',
 *		accessLevel = 'all',
 *		description = 'Shows Beast Stars loot',
 *		help        = 'pande.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'tnh',
 *		accessLevel = 'all',
 *		description = 'Shows The Night Heart loot',
 *		help        = 'pande.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'sb',
 *		accessLevel = 'all',
 *		description = 'Shows Shadowbreeds loot',
 *		help        = 'pande.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'aries',
 *		accessLevel = 'all',
 *		description = 'Shows Aries Zodiac loot',
 *		help        = 'pande.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'leo',
 *		accessLevel = 'all',
 *		description = 'Shows Leo Zodiac loot',
 *		help        = 'pande.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'virgo',
 *		accessLevel = 'all',
 *		description = 'Shows Virgo Zodiac loot',
 *		help        = 'pande.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'aquarius',
 *		accessLevel = 'all',
 *		description = 'Shows Aquarius Zodiac loot',
 *		help        = 'pande.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'cancer',
 *		accessLevel = 'all',
 *		description = 'Shows Cancer Zodiac loot',
 *		help        = 'pande.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'gemini',
 *		accessLevel = 'all',
 *		description = 'Shows Gemini Zodiac loot',
 *		help        = 'pande.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'libra',
 *		accessLevel = 'all',
 *		description = 'Shows Libra Zodiac loot',
 *		help        = 'pande.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'pisces',
 *		accessLevel = 'all',
 *		description = 'Shows Pisces Zodiac loot',
 *		help        = 'pande.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'taurus',
 *		accessLevel = 'all',
 *		description = 'Shows Taurus Zodiac loot',
 *		help        = 'pande.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'capricorn',
 *		accessLevel = 'all',
 *		description = 'Shows Capricorn Zodiac loot',
 *		help        = 'pande.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'sagittarius',
 *		accessLevel = 'all',
 *		description = 'Shows Sagittarius Zodiac loot',
 *		help        = 'pande.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'scorpio',
 *		accessLevel = 'all',
 *		description = 'Shows Scorpio Zodiac loot',
 *		help        = 'pande.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'bastion',
 *		accessLevel = 'all',
 *		description = 'Shows Bastion loot',
 *		help        = 'pande.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'pande',
 *		accessLevel = 'all',
 *		description = 'Shows Pandemonium bosses and loot categories',
 *		help        = 'pande.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'xan',
 *		accessLevel = 'all',
 *		description = 'Shows Legacy of the Xan loot categories',
 *		help        = 'xan.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'vortexx',
 *		accessLevel = 'all',
 *		description = 'Shows possible Vortexx Loot',
 *		help        = 'xan.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'mitaar',
 *		accessLevel = 'all',
 *		description = 'Shows possible Mitaar Hero Loot',
 *		help        = 'xan.txt'
 *	)
 *	@DefineCommand(
 *		command     = '12m',
 *		accessLevel = 'all',
 *		description = 'Shows possible 12 man Loot',
 *		help        = 'xan.txt'
 *	)
 */
class LootListsController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;
	
	/** @Inject */
	public $chatBot;
	
	/** @Inject */
	public $db;

	/** @Inject */
	public $text;

	/** @Inject */
	public $util;
	
	/** @Inject */
	public $raidController;
	
	/**
	 * @Setup
	 */
	public function setup() {
		$this->db->loadSQLFile($this->moduleName, 'raid_loot');
	}
	
	/**
	 * Author: Dare2005 (RK2), based on code for dbloot module by Chachy (RK2)
	 *
	 * @HandlesCommand("alb")
	 * @Matches("/^alb$/i")
	 */
	public function albCommand($message, $channel, $sender, $sendto, $args) {
		$sendto->reply($this->getAlbatraumLoot('Albtraum', 'Crystals & Crystalised Memories'));
		$sendto->reply($this->getAlbatraumLoot('Albtraum', 'Ancients'));
		$sendto->reply($this->getAlbatraumLoot('Albtraum', 'Samples'));
		$sendto->reply($this->getAlbatraumLoot('Albtraum', 'Rings and Preservation Units'));
		$sendto->reply($this->getAlbatraumLoot('Albtraum', 'Pocket Boss Crystals'));
	}

	public function getAlbatraumLoot($raid, $category) {
		$blob = $this->find_raid_loot($raid, $category);
		$blob .= "\n\nAlbtraum Loot By Dare2005 (RK2)";
		return $this->text->make_blob("$raid $category Loot", $blob);
	}
	
	/**
	 * Author: Chachy (RK2), based on code for Pande Loot Bot by Marinerecon (RK2)
	 *
	 * @HandlesCommand("db1")
	 * @Matches("/^db1$/i")
	 */
	public function db1Command($message, $channel, $sender, $sendto, $args) {
		$sendto->reply($this->getDustBrigadeLoot('DustBrigade', 'Armor'));
		$sendto->reply($this->getDustBrigadeLoot('DustBrigade', '1'));
	}
	
	/**
	 * Author: Chachy (RK2), based on code for Pande Loot Bot by Marinerecon (RK2)
	 *
	 * @HandlesCommand("db2")
	 * @Matches("/^db2$/i")
	 */
	public function db2Command($message, $channel, $sender, $sendto, $args) {
		$sendto->reply($this->getDustBrigadeLoot('DustBrigade', 'Armor'));
		$sendto->reply($this->getDustBrigadeLoot('DustBrigade', '2'));
	}
	
	public function getDustBrigadeLoot($raid, $category) {
		$blob = $this->find_raid_loot($raid, $category);
		$blob .= "\n\nDust Brigrade Loot By Chachy (RK2)";
		return $this->text->make_blob("$raid $category Loot", $blob);
	}
	
	/**
	 * @HandlesCommand("13")
	 * @Matches("/^13$/i")
	 */
	public function apf13Command($message, $channel, $sender, $sendto, $args) {
		$this->addAPFLootToList(13);
	}
	
	/**
	 * @HandlesCommand("28")
	 * @Matches("/^28$/i")
	 */
	public function apf28Command($message, $channel, $sender, $sendto, $args) {
		$this->addAPFLootToList(28);
	}
	
	/**
	 * @HandlesCommand("35")
	 * @Matches("/^35$/i")
	 */
	public function apf35Command($message, $channel, $sender, $sendto, $args) {
		$this->addAPFLootToList(35);
	}
	
	public function addAPFLootToList($sector) {
		// adding apf stuff
		$this->raidController->add_raid_to_loot_list('APF', "Sector $sector");
		$msg = "Sector $sector loot table was added to the loot list.";
		$this->chatBot->sendPrivate($msg);

		$msg = $this->raidController->get_current_loot_list();
		$this->chatBot->sendPrivate($msg);
	}
	
	/**
	 * @HandlesCommand("apf")
	 * @Matches("/^apf (13|28|35)$/i")
	 */
	public function apfCommand($message, $channel, $sender, $sendto, $args) {
		$sector = $args[1];

		$itemlink["ICE"] = $this->text->make_item(257968, 257968, 1, "Hacker ICE-Breaker Source");
		$itemlink["BOARD"] = $this->text->make_item(257706, 257706, 1, "Kyr'Ozch Helmet");
		$itemlink["APE"] = $this->text->make_item(257960, 257960, 250, "Action Probability Estimator");
		$itemlink["DGRV"] = $this->text->make_item(257962, 257962, 250, "Dynamic Gas Redistribution Valves");
		$itemlink["KBAP"] = $this->text->make_item(257529, 257529, 1, "Kyr'Ozch Battlesuit Audio Processor");
		$itemlink["KVPU"] = $this->text->make_item(257533, 257533, 1, "Kyr'Ozch Video Processing Unit");
		$itemlink["KRI"] = $this->text->make_item(257531, 257531, 1, "Kyr'Ozch Rank Identification");
		$itemlink["ICEU"] = $this->text->make_item(257110, 257110, 1, "Intrusion Countermeasure Electronics Upgrade");
		$itemlink["OTAE"] = $this->text->make_item(257112, 257112, 1, "Omni-Tek Award - Exemplar");
		$itemlink["CMP"] = $this->text->make_item(257113, 257113, 1, "Clan Merits - Paragon");
		$itemlink["EMCH"] = $this->text->make_item(257379, 257379, 200, "Extruder's Molybdenum Crash Helmet");
		$itemlink["CKCNH"] = $this->text->make_item(257115, 257115, 200, "Conscientious Knight Commander Nizno's Helmet");
		$itemlink["SKCGH"] = $this->text->make_item(257114, 257114, 200, "Sworn Knight Commander Genevra's Helmet");
		$itemlink["BCOH"] = $this->text->make_item(257383, 257383, 300, "Blackmane's Combined Officer's Headwear");
		$itemlink["GCCH"] = $this->text->make_item(257381, 257381, 300, "Gannondorf's Combined Commando's Headwear");
		$itemlink["HCSH"] = $this->text->make_item(257384, 257384, 300, "Haitte's Combined Sharpshooter's Headwear");
		$itemlink["OCPH"] = $this->text->make_item(257377, 257377, 300, "Odum's Combined Paramedic's Headwear");
		$itemlink["SCMH"] = $this->text->make_item(257380, 257380, 300, "Sillum's Combined Mercenary's Headwear");
		$itemlink["YCSH"] = $this->text->make_item(257382, 257382, 300, "Yakomo's Combined Scout's Headwear");
		$itemlink["HLOA"] = $this->text->make_item(257128, 257128, 300, "High Lord of Angst");
		$itemlink["SKR2"] = $this->text->make_item(257967, 257967, 300, "Silenced Kyr'Ozch Rifle - Type 2");
		$itemlink["SKR3"] = $this->text->make_item(257131, 257131, 300, "Silenced Kyr'Ozch Rifle - Type 3");
		$itemlink["ASC"] = $this->text->make_item(257126, 257126, 300, "Amplified Sleek Cannon");
		$itemlink["IAPU"] = $this->text->make_item(257959, 257959, 1, "Inertial Adjustment Processing Unit");
		$itemlink["HVBCP"] = $this->text->make_item(257119, 257119, 300, "Hadrulf's Viral Belt Component Platform");
		$itemlink["NAC"] = $this->text->make_item(257963, 257963, 250, "Notum Amplification Coil");
		$itemlink["TAHSC"] = $this->text->make_item(257124, 257124, 300, "Twice Augmented Hellspinner Shock Cannon");
		$itemlink["ONC"] = $this->text->make_item(257118, 257118, 250, "ObiTom's Nano Calculator");
		$itemlink["AKC12"] = $this->text->make_item(257143, 257143, 300, "Amplified Kyr'Ozch Carbine - Type 12");
		$itemlink["AKC13"] = $this->text->make_item(257142, 257142, 300, "Amplified Kyr'Ozch Carbine - Type 13");
		$itemlink["AKC5"] = $this->text->make_item(257144, 257144, 300, "Amplified Kyr'Ozch Carbine - Type 5");
		$itemlink["ERU"] = $this->text->make_item(257961, 257961, 250, "Energy Redistribution Unit");
		$itemlink["BOB"] = $this->text->make_item(257147, 257147, 300, "Blades of Boltar");
		$itemlink["DVLPR"] = $this->text->make_item(257116, 257116, 1, "De'Valos Lava Protection Ring");
		$itemlink["VLRD"] = $this->text->make_item(257964, 257964, 250, "Visible Light Remodulation Device");
		$itemlink["DVRPR"] = $this->text->make_item(257117, 257117, 1, "De'Valos Radiation Protection Ring");
		$itemlink["SSSS"] = $this->text->make_item(257141, 257141, 300, "Scoped Salabim Shotgun Supremo");
		$itemlink["EPP"] = $this->text->make_item(258345, 258345, 300, "Explosif's Polychromatic Pillows");
		$itemlink["VNGW"] = $this->text->make_item(257123, 257123, 300, "Vektor ND Grand Wyrm");
		$list = '';

		switch($sector) {
			case "13":
				//CRU
				$list .= $this->text->make_image(257196) . "\n";
				$list .= "Name: {$itemlink["ICE"]}\n";
				$list .= "Purpose: {$itemlink["ICEU"]}\n";
				$list .= "Note: <highlight>Drops on all Alien Playfield 5 times from the Boss.<end>\n\n";

				//Token Credit Items
				$list .= $this->text->make_image(218775) . "\n";
				$list .= "Name: {$itemlink["KBAP"]}\n";
				$list .= $this->text->make_image(218758) . "\n";
				$list .= "Name: {$itemlink["KVPU"]}\n";
				$list .= $this->text->make_image(218768) . "\n";
				$list .= "Name: {$itemlink["KRI"]}\n";
				$list .= "Purpose: <highlight>Kyr'Ozch Rank Identification, Kyr'Ozch Video Processing Unit and Kyr'Ozch Battlesuit Audio Processor can be traded at your faction vendor at the Alien Playfield Bar for Tokens or Credits.<end>\n";
				$list .= "Note: <highlight>Drops on all Alien Playfield from the Boss (one from each type).<end>\n\n";

				//Token Board
				$list .= $this->text->make_image(230855) . "\n";
				$list .= "Name: {$itemlink["BOARD"]}\n";
				$list .= "Purpose: - {$itemlink["OTAE"]}\n";
				$list .= "<tab><tab>     - {$itemlink["CMP"]}\n";
				$list .= "Note: <highlight>Drops on all Alien Playfield from the Boss.<end>\n\n";

				//Action Probability Estimator
				$list .= $this->text->make_image(203502) . "\n";
				$list .= "Name: {$itemlink["APE"]}\n";
				$list .= "Purpose: - {$itemlink["EMCH"]}\n";
				$list .= "<tab><tab>     - {$itemlink["CKCNH"]}\n";
				$list .= "<tab><tab>     - {$itemlink["SKCGH"]}\n";
				$list .= "<tab><tab>     - {$itemlink["BCOH"]}\n";
				$list .= "<tab><tab>     - {$itemlink["GCCH"]}\n";
				$list .= "<tab><tab>     - {$itemlink["HCSH"]}\n";
				$list .= "<tab><tab>     - {$itemlink["OCPH"]}\n";
				$list .= "<tab><tab>     - {$itemlink["SCMH"]}\n";
				$list .= "<tab><tab>     - {$itemlink["YCSH"]}\n\n";

				//Dynamic Gas Redistribution Valves
				$list .= $this->text->make_image(205508) . "\n";
				$list .= "Name: {$itemlink["DGRV"]}\n";
				$list .= "Purpose: - {$itemlink["HLOA"]}\n";
				$list .= "<tab><tab>     - {$itemlink["SKR2"]}\n";
				$list .= "<tab><tab>     - {$itemlink["SKR3"]}\n";
				$list .= "<tab><tab>     - {$itemlink["ASC"]}\n\n";
				break;
			case "28":
				//CRU
				$list .= $this->text->make_image(257196) . "\n";
				$list .= "Name: {$itemlink["ICE"]}\n";
				$list .= "Purpose: {$itemlink["ICEU"]}\n";
				$list .= "Note: <highlight>Drops on all Alien Playfield 5 times from the Boss.<end>\n\n";

				//Token Credit Items
				$list .= $this->text->make_image(218775) . "\n";
				$list .= "Name: {$itemlink["KBAP"]}\n";
				$list .= $this->text->make_image(218758) . "\n";
				$list .= "Name: {$itemlink["KVPU"]}\n";
				$list .= $this->text->make_image(218768) . "\n";
				$list .= "Name: {$itemlink["KRI"]}\n";
				$list .= "Purpose: <highlight>Kyr'Ozch Rank Identification, Kyr'Ozch Video Processing Unit and Kyr'Ozch Battlesuit Audio Processor can be traded at your faction vendor at the Alien Playfield Bar for Tokens or Credits.<end>\n";
				$list .= "Note: <highlight>Drops on all Alien Playfields from the Boss (one from each type).<end>\n\n";

				//Token Board
				$list .= $this->text->make_image(230855) . "\n";
				$list .= "Name: {$itemlink["BOARD"]}\n";
				$list .= "Purpose: - {$itemlink["OTAE"]}\n";
				$list .= "<tab><tab>     - {$itemlink["CMP"]}\n";
				$list .= "Note: <highlight>Drops on all Alien Playfield from the Boss.<end>\n\n";

				//APF Belt
				$list .= $this->text->make_image(11618) . "\n";
				$list .= "Name: {$itemlink["IAPU"]}\n";
				$list .= "Purpose: - {$itemlink["HVBCP"]}\n\n";

				//Notum coil
				$list .= $this->text->make_image(257195) . "\n";
				$list .= "Name: {$itemlink["NAC"]}\n";
				$list .= "Purpose: - {$itemlink["TAHSC"]}\n";
				$list .= "<tab><tab>     - {$itemlink["ONC"]}\n";
				$list .= "<tab><tab>     - {$itemlink["AKC12"]}\n";
				$list .= "<tab><tab>     - {$itemlink["AKC13"]}\n";
				$list .= "<tab><tab>     - {$itemlink["AKC5"]}\n\n";
				break;
			case "35":
				//CRU
				$list .= $this->text->make_image(257196) . "\n";
				$list .= "Name: {$itemlink["ICE"]}\n";
				$list .= "Purpose: {$itemlink["ICEU"]}\n";
				$list .= "Note: <highlight>Drops on all Alien Playfield 5 times from the Boss.<end>\n\n";

				//Token Credit Items
				$list .= $this->text->make_image(218775) . "\n";
				$list .= "Name: {$itemlink["KBAP"]}\n";
				$list .= $this->text->make_image(218758) . "\n";
				$list .= "Name: {$itemlink["KVPU"]}\n";
				$list .= $this->text->make_image(218768) . "\n";
				$list .= "Name: {$itemlink["KRI"]}\n";
				$list .= "Purpose: <highlight>Kyr'Ozch Rank Identification, Kyr'Ozch Video Processing Unit and Kyr'Ozch Battlesuit Audio Processor can be traded at your faction vendor at the Alien Playfield Bar for Tokens or Credits.<end>\n";
				$list .= "Note: <highlight>Drops on all Alien Playfield from the Boss (one from each type).<end>\n\n";

				//Token Board
				$list .= $this->text->make_image(230855) . "\n";
				$list .= "Name:{$itemlink["BOARD"]}\n";
				$list .= "Purpose: - {$itemlink["OTAE"]}\n";
				$list .= "<tab><tab>     - {$itemlink["CMP"]}\n";
				$list .= "Note: <highlight>Drops on all Alien Playfield from the Boss.<end>\n\n";

				//Energy Redistribution Unit
				$list .= $this->text->make_image(257197) . "\n";
				$list .= "Name: {$itemlink["ERU"]}\n";
				$list .= "Purpose: - {$itemlink["BOB"]}\n";
				$list .= "<tab><tab>     - {$itemlink["DVLPR"]}\n";
				$list .= "<tab><tab>     - {$itemlink["VNGW"]}\n\n";

				//Visible Light Remodulation Device
				$list .= $this->text->make_image(235270) . "\n";
				$list .= "Name: {$itemlink["VLRD"]}\n";
				$list .= "Purpose: - {$itemlink["DVRPR"]}\n";
				$list .= "<tab><tab>     - {$itemlink["SSSS"]}\n";
				$list .= "<tab><tab>     - {$itemlink["EPP"]}\n\n";
				break;
		}

		$msg = $this->text->make_blob("Loot table for sector $sector", $list);

		$sendto->reply($msg);
	}
	
	/**
	 * Author: Marinerecon (RK2)
	 *
	 * @HandlesCommand("beastarmor")
	 * @Matches("/^beastarmor$/i")
	 */
	public function beastarmorCommand($message, $channel, $sender, $sendto, $args) {
		$sendto->reply($this->getPandemoniumLoot('Pande', 'Beast Armor'));
	}
	
	/**
	 * Author: Marinerecon (RK2)
	 *
	 * @HandlesCommand("beastweaps")
	 * @Matches("/^beastweaps$/i")
	 */
	public function beastweapsCommand($message, $channel, $sender, $sendto, $args) {
		$sendto->reply($this->getPandemoniumLoot('Pande', 'Beast Weapons'));
	}
	
	/**
	 * Author: Marinerecon (RK2)
	 *
	 * @HandlesCommand("beaststars")
	 * @Matches("/^beaststars$/i")
	 */
	public function beaststarsCommand($message, $channel, $sender, $sendto, $args) {
		$sendto->reply($this->getPandemoniumLoot('Pande', 'Stars'));
	}
	
	/**
	 * Author: Marinerecon (RK2)
	 *
	 * @HandlesCommand("sb")
	 * @Matches("/^sb$/i")
	 */
	public function sbCommand($message, $channel, $sender, $sendto, $args) {
		$sendto->reply($this->getPandemoniumLoot('Pande', 'Shadowbreeds'));
	}
	
	/**
	 * Author: Marinerecon (RK2)
	 *
	 * @HandlesCommand("tnh")
	 * @Matches("/^tnh$/i")
	 */
	public function tnhCommand($message, $channel, $sender, $sendto, $args) {
		$sendto->reply($this->getPandemoniumLoot('Pande', 'The Night Heart'));
	}
	
	/**
	 * Author: Marinerecon (RK2)
	 *
	 * @HandlesCommand("aries")
	 * @Matches("/^aries$/i")
	 */
	public function ariesCommand($message, $channel, $sender, $sendto, $args) {
		$sendto->reply($this->getPandemoniumLoot('Pande', 'Aries'));
	}
	
	/**
	 * Author: Marinerecon (RK2)
	 *
	 * @HandlesCommand("leo")
	 * @Matches("/^leo$/i")
	 */
	public function leoCommand($message, $channel, $sender, $sendto, $args) {
		$sendto->reply($this->getPandemoniumLoot('Pande', 'Leo'));
	}
	
	/**
	 * Author: Marinerecon (RK2)
	 *
	 * @HandlesCommand("virgo")
	 * @Matches("/^virgo$/i")
	 */
	public function virgoCommand($message, $channel, $sender, $sendto, $args) {
		$sendto->reply($this->getPandemoniumLoot('Pande', 'Virgo'));
	}
	
	/**
	 * Author: Marinerecon (RK2)
	 *
	 * @HandlesCommand("aquarius")
	 * @Matches("/^aquarius$/i")
	 */
	public function aquariusCommand($message, $channel, $sender, $sendto, $args) {
		$sendto->reply($this->getPandemoniumLoot('Pande', 'Aquarius'));
	}
	
	/**
	 * Author: Marinerecon (RK2)
	 *
	 * @HandlesCommand("cancer")
	 * @Matches("/^cancer$/i")
	 */
	public function cancerCommand($message, $channel, $sender, $sendto, $args) {
		$sendto->reply($this->getPandemoniumLoot('Pande', 'Cancer'));
	}
	
	/**
	 * Author: Marinerecon (RK2)
	 *
	 * @HandlesCommand("gemini")
	 * @Matches("/^gemini$/i")
	 */
	public function geminiCommand($message, $channel, $sender, $sendto, $args) {
		$sendto->reply($this->getPandemoniumLoot('Pande', 'Gemini'));
	}
	
	/**
	 * Author: Marinerecon (RK2)
	 *
	 * @HandlesCommand("libra")
	 * @Matches("/^libra$/i")
	 */
	public function libraCommand($message, $channel, $sender, $sendto, $args) {
		$sendto->reply($this->getPandemoniumLoot('Pande', 'Libra'));
	}
	
	/**
	 * Author: Marinerecon (RK2)
	 *
	 * @HandlesCommand("pisces")
	 * @Matches("/^pisces$/i")
	 */
	public function piscesCommand($message, $channel, $sender, $sendto, $args) {
		$sendto->reply($this->getPandemoniumLoot('Pande', 'Pisces'));
	}
	
	/**
	 * Author: Marinerecon (RK2)
	 *
	 * @HandlesCommand("taurus")
	 * @Matches("/^taurus$/i")
	 */
	public function taurusCommand($message, $channel, $sender, $sendto, $args) {
		$sendto->reply($this->getPandemoniumLoot('Pande', 'Taurus'));
	}
	
	/**
	 * Author: Marinerecon (RK2)
	 *
	 * @HandlesCommand("capricorn")
	 * @Matches("/^capricorn$/i")
	 */
	public function capricornCommand($message, $channel, $sender, $sendto, $args) {
		$sendto->reply($this->getPandemoniumLoot('Pande', 'Capricorn'));
	}
	
	/**
	 * Author: Marinerecon (RK2)
	 *
	 * @HandlesCommand("sagittarius")
	 * @Matches("/^sagittarius$/i")
	 */
	public function sagittariusCommand($message, $channel, $sender, $sendto, $args) {
		$sendto->reply($this->getPandemoniumLoot('Pande', 'Sagittarius'));
	}
	
	/**
	 * Author: Marinerecon (RK2)
	 *
	 * @HandlesCommand("scorpio")
	 * @Matches("/^scorpio$/i")
	 */
	public function scorpioCommand($message, $channel, $sender, $sendto, $args) {
		$sendto->reply($this->getPandemoniumLoot('Pande', 'Scorpio'));
	}
	
	/**
	 * Author: Marinerecon (RK2)
	 *
	 * @HandlesCommand("bastion")
	 * @Matches("/^bastion$/i")
	 */
	public function bastionCommand($message, $channel, $sender, $sendto, $args) {
		$sendto->reply($this->getPandemoniumLoot('Pande', 'Bastion'));
	}
	
	public function getPandemoniumLoot($raid, $category) {
		$blob = $this->find_raid_loot($raid, $category);
		$blob .= "\n\nPande Loot By Marinerecon (RK2)";
		return $this->text->make_blob("$raid $category Loot", $blob);
	}
	
	/**
	 * Author: Marinerecon (RK2)
	 *
	 * @HandlesCommand("pande")
	 * @Matches("/^pande$/i")
	 */
	public function pandeCommand($message, $channel, $sender, $sendto, $args) {
		$list .= "<header2>The Beast<end>\n";
		$list .= "<tab>".$this->text->make_chatcmd("Beast Armor\n", "/tell <myname> beastarmor");
		$list .= "<tab>".$this->text->make_chatcmd("Beast Weapons\n", "/tell <myname> beastweaps");
		$list .= "<tab>".$this->text->make_chatcmd("Beast Stars\n", "/tell <myname> beaststars");
		$list .= "\n<header2>The Night Heart<end>\n";
		$list .= "<tab>".$this->text->make_chatcmd("TNH\n", "/tell <myname> tnh");
		$list .= "\n<header2>West Zodiacs<end>\n";
		$list .= "<tab>".$this->text->make_chatcmd("Aries\n", "/tell <myname> aries");
		$list .= "<tab>".$this->text->make_chatcmd("Leo\n", "/tell <myname> leo");
		$list .= "<tab>".$this->text->make_chatcmd("Virgo\n", "/tell <myname> virgo");
		$list .= "\n<header2>East Zodiacs<end>\n";
		$list .= "<tab>".$this->text->make_chatcmd("Aquarius\n", "/tell <myname> aquarius");
		$list .= "<tab>".$this->text->make_chatcmd("Cancer\n", "/tell <myname> cancer");
		$list .= "<tab>".$this->text->make_chatcmd("Gemini\n", "/tell <myname> gemini");
		$list .= "\n<header2>Middle Zodiacs<end>\n";
		$list .= "<tab>".$this->text->make_chatcmd("Libra\n", "/tell <myname> libra");
		$list .= "<tab>".$this->text->make_chatcmd("Pisces\n", "/tell <myname> pisces");
		$list .= "<tab>".$this->text->make_chatcmd("Taurus\n", "/tell <myname> taurus");
		$list .= "\n<header2>North Zodiacs<end>\n";
		$list .= "<tab>".$this->text->make_chatcmd("Capricorn\n", "/tell <myname> capricorn");
		$list .= "<tab>".$this->text->make_chatcmd("Sagittarius\n", "/tell <myname> sagittarius");
		$list .= "<tab>".$this->text->make_chatcmd("Scorpio\n", "/tell <myname> scorpio");
		$list .= "\n<header2>Other<end>\n";
		$list .= "<tab>".$this->text->make_chatcmd("Shadowbreeds\n", "/tell <myname> sb");
		$list .= "<tab>".$this->text->make_chatcmd("Bastion\n", "/tell <myname> bastion");

		$list .= "\n\nPandemonium Loot By Marinerecon (RK2)";

		$msg = $this->text->make_blob("Pandemonium Loot", $list);
		$sendto->reply($msg);
	}
	
	/**
	 * Author: Morgo (RK2)
	 *
	 * @HandlesCommand("vortexx")
	 * @Matches("/^vortexx$/i")
	 */
	public function xanVortexxCommand($message, $channel, $sender, $sendto, $args) {
		$sendto->reply($this->getXanLoot('Vortexx', 'General'));
		$sendto->reply($this->getXanLoot('Vortexx', 'Symbiants'));
		$sendto->reply($this->getXanLoot('Vortexx', 'Spirits'));
	}
	
	/**
	 * Author: Morgo (RK2)
	 *
	 * @HandlesCommand("mitaar")
	 * @Matches("/^mitaar$/i")
	 */
	public function xanMitaarCommand($message, $channel, $sender, $sendto, $args) {
		$sendto->reply($this->getXanLoot('Mitaar', 'General'));
		$sendto->reply($this->getXanLoot('Mitaar', 'Symbiants'));
		$sendto->reply($this->getXanLoot('Mitaar', 'Spirits'));
	}
	
	/**
	 * Author: Morgo (RK2)
	 *
	 * @HandlesCommand("12m")
	 * @Matches("/^12m$/i")
	 */
	public function xan12mCommand($message, $channel, $sender, $sendto, $args) {
		$sendto->reply($this->getXanLoot('12Man', 'General'));
		$sendto->reply($this->getXanLoot('12Man', 'Symbiants'));
		$sendto->reply($this->getXanLoot('12Man', 'Spirits'));
		$sendto->reply($this->getXanLoot('12Man', 'Profession Gems'));
	}
	
	public function getXanLoot($raid, $category) {
		$blob = $this->find_raid_loot($raid, $category);
		$blob .= "\n\nXan Loot By Morgo (RK2)";
		return $this->text->make_blob("$raid $category Loot", $blob);
	}
	
	/**
	 * Author: Morgo (RK2)
	 *
	 * @HandlesCommand("xan")
	 * @Matches("/^xan$/i")
	 */
	public function xanCommand($message, $channel, $sender, $sendto, $args) {
		$list = $this->text->make_chatcmd("Vortexx", "/tell <myname> vortexx") . "\n";
		$list .= "<tab>General\n";
		$list .= "<tab>Symbiants (Beta)\n";
		$list .= "<tab>Spirits (Beta)\n\n";

		$list .= $this->text->make_chatcmd("Mitaar Hero", "/tell <myname> mitaar") . "\n";
		$list .= "<tab>General\n";
		$list .= "<tab>Symbiants (Beta)\n";
		$list .= "<tab>Spirits (Beta)\n\n";

		$list .= $this->text->make_chatcmd("12 Man", "/tell <myname> 12m") . "\n";
		$list .= "<tab>General\n";
		$list .= "<tab>Symbiants (Beta)\n";
		$list .= "<tab>Spirits (Beta)\n";
		$list .= "<tab>Profession Gems\n";

		$list .= "\n\nXan Loot By Morgo (RK2)";

		$msg = $this->text->make_blob("Legacy of the Xan Loot", $list);
		$sendto->reply($msg);
	}
	
	public function find_raid_loot($raid, $category) {
		$sql = "SELECT * FROM raid_loot WHERE raid = ? AND category = ?";
		$data = $this->db->query($sql, $raid, $category);

		if (count($data) == 0) {
			return null;
		}

		$blob = "\n";
		forEach ($data as $row) {
			$blob .= "<pagebreak>";
			$blob .= $this->text->make_item($row->lowid, $row->highid, $row->ql, "<img src=rdb://{$row->imageid}>");
			$blob .= "\nItem: <highlight>{$row->name}<end>\n";
			$blob .= $this->text->make_chatcmd("Add to Loot List", "/tell <myname> loot $row->id");
			$blob .= "\n\n";
		}

		return $blob;
	}
}

?>
