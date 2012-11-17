<?php
/**
 * Bossloot Module Ver 1.1
 * Written By Jaqueme
 * For Budabot
 * Database Adapted From One Originally Compiled by Malosar For BeBot
 * Boss Drop Table Database Module
 * Written 5/11/07
 * Last Modified 5/14/07
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'boss',
 *		accessLevel = 'all',
 *		description = 'Shows bosses and their loot',
 *		help        = 'boss.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'bossloot',
 *		accessLevel = 'all',
 *		description = 'Finds which boss drops certain loot',
 *		help        = 'boss.txt'
 *	)
 */
class BosslootController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;

	/** @Inject */
	public $db;

	/** @Inject */
	public $text;

	/** @Setup */
	public function setup() {
		$this->db->loadSQLFile($this->moduleName, "boss_namedb");
		$this->db->loadSQLFile($this->moduleName, "boss_lootdb");
	}

	/**
	 * This command handler shows bosses and their loot.
	 *
	 * @HandlesCommand("boss")
	 * @Matches("/^boss (.+)$/i")
	 */
	public function bossCommand($message, $channel, $sender, $sendto, $args) {
		$search = strtolower($args[1]);

		// Find boss by name or key
		$bosses = $this->db->query("SELECT * FROM boss_namedb b LEFT JOIN
			whereis w ON b.bossname = w.name WHERE bossname LIKE ? OR keyname
			LIKE ?", "%{$search}%", "%{$search}%");
		$count = count($bosses);

		if ($count > 1) {
			$blob = "Results of Search for '$search'\n\n";
			//If multiple matches found output list of bosses
			forEach ($bosses as $row) {
				$blob .= $this->getBossLootOutput($row);
			}
			$output = $this->text->make_blob("Boss Search Results ($count)", $blob);
		} else if ($count == 1) {
			//If single match found, output full loot table
			$row = $bosses[0];

			$blob .= "Location: <highlight>{$row->answer}<end>\n\n";
			$blob .= "Loot:\n\n";

			$data = $this->db->query("SELECT * FROM boss_lootdb b LEFT JOIN
				aodb a ON (b.itemid = a.lowid OR b.itemid = a.highid)
				WHERE b.bossid = ?", $row->bossid);
			forEach ($data as $row2) {
				$blob .= $this->text->make_image($row2->icon) . "\n";
				$blob .= $this->text->make_item($row2->lowid, $row2->highid, $row2->highql, $row2->itemname) . "\n\n";
			}
			$output = $this->text->make_blob($row->bossname, $blob);
		} else {
			$output = "There were no matches for your search.";
		}
		$sendto->reply($output);
	}

	/**
	 * This command handler finds which boss drops certain loot.
	 *
	 * @HandlesCommand("bossloot")
	 * @Matches("/^bossloot (.+)$/i")
	 */
	public function bosslootCommand($message, $channel, $sender, $sendto, $args) {
		$search = strtolower($args[1]);

		$blob = "Bosses that drop items matching '$search':\n\n";

		$loot = $this->db->query("SELECT DISTINCT b2.bossid, b2.bossname, w.answer
			FROM boss_lootdb b1 JOIN boss_namedb b2 ON b2.bossid = b1.bossid
			LEFT JOIN whereis w ON w.name = b2.bossname WHERE b1.itemname LIKE ?", "%{$search}%");
		$count = count($loot);

		if ($count != 0) {
			forEach ($loot as $row) {
				$blob .= $this->getBossLootOutput($row);
			}
			$output = $this->text->make_blob("Bossloot Search Results ($count)", $blob);
		} else {
			$output .= "There were no matches for your search.";
		}
		$sendto->reply($output);
	}

	public function getBossLootOutput($row) {
		$data = $this->db->query("SELECT * FROM boss_lootdb b LEFT JOIN
			aodb a ON (b.itemid = a.lowid OR b.itemid = a.highid) WHERE
			b.bossid = ?", $row->bossid);
			
		$blob = '<pagebreak>' . $this->text->make_chatcmd($row->bossname, "/tell <myname> boss $row->bossname") . "\n";
		$blob .= "Location: <highlight>{$row->answer}<end>\n";
		$blob .= "Loot: ";
		forEach ($data as $row2) {
			$blob .= $this->text->make_item($row2->lowid, $row2->highid, $row2->highql, $row2->itemname) . ', ';
		}
		$blob .= "\n\n";
		return $blob;
	}
}

