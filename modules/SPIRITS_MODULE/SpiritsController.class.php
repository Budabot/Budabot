<?php

namespace Budabot\User\Modules;

/**
 * Authors: 
 *	- Tyrence (RK2)
 *
 * Originally Written for Budabot By Jaqueme
 * Database Adapted From One Originally Compiled by Wolfbiter For BeBot
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'spirits', 
 *		accessLevel = 'all', 
 *		description = 'Search for spirits', 
 *		help        = 'spirits.txt'
 *	)
 */
class SpiritsController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;
	
	/** @Inject */
	public $db;
	
	/** @Inject */
	public $text;
	
	/**
	 * @Setup
	 */
	public function setup() {
		$this->db->loadSQLFile($this->moduleName, 'spirits');
	}

	/**
	 * @HandlesCommand("spirits")
	 * @Matches("/^spirits (.+)$/i")
	 */
	public function spiritsCommand($message, $channel, $sender, $sendto, $args) {
		if (preg_match("/^spirits ([^0-9,]+)$/i", $message, $arr)) {
			$name = $arr[1];
			$name = ucwords(strtolower($name));
			$title = "Spirits Database for $name";
			$data = $this->db->query("SELECT * FROM spiritsdb WHERE name LIKE ? OR spot LIKE ? ORDER BY level", '%'.$name.'%', '%'.$name.'%');
			if (count($data) == 0) {
				$spirits .= "There were no matches found for $name.\nTry putting a comma between search values.\n\n";
				$spirits .= $this->getValidSlotTypes();
			} else {
				$spirits .= $this->formatSpiritOutput($data);
			}
		}
			//If searched by name and slot
		else if (preg_match("/^spirits ([^0-9]+),([^0-9]+)$/i", $message, $arr)) {
			if (preg_match("/(chest|ear|eye|feet|head|larm|legs|lhand|lwrist|rarm|rhand|rwrist|waist)/i", $arr[1])) {
				$slot = $arr[1];
				$name = $arr[2];
				$title = "Spirits Database for $name $slot";
			} else if (preg_match("/(chest|ear|eye|feet|head|larm|legs|lhand|lwrist|rarm|rhand|rwrist|waist)/i", $arr[2])) {
				$name = $arr[1];
				$slot = $arr[2];
				$title = "Spirits Database for $name $slot";
			} else {
				$spirits .= "No matches were found for $name $slot\n\n";
				$spirits .= $this->getValidSlotTypes();
			}
			$name = ucwords(strtolower($name));
			$name = trim($name);
			$slot = ucwords(strtolower($slot));
			$slot = trim($slot);
			$data = $this->db->query("SELECT * FROM spiritsdb WHERE name LIKE ? AND spot = ? ORDER BY level", '%'.$name.'%', $slot);
			$spirits .= $this->formatSpiritOutput($data);
		}
			// If searched by ql
		else if (preg_match("/^spirits ([0-9]+)$/i", $message, $arr)) {
			$ql = $arr[1];
			if ($ql <= 1 OR $ql >= 300) {
				$msg = "Invalid Ql specified.";
				$sendto->reply($msg);
				return;
			}
			$title = "Spirits QL $ql";
			$data = $this->db->query("SELECT * FROM spiritsdb where ql = ? ORDER BY ql", $ql);
			$spirits .= $this->formatSpiritOutput($data);
		}
			// If searched by ql range
		else if (preg_match("/^spirits ([0-9]+)-([0-9]+)$/i", $message, $arr)) {
			$qllorange = $arr[1];
			$qlhirange = $arr[2];
			if ($qllorange < 1 OR $qlhirange > 219 OR $qllorange >= $qlhirange) {
				$msg = "Invalid Ql range specified.";
				$sendto->reply($msg);
				return;
			}
			$title = "Spirits QL $qllorange to $qlhirange";
			$data = $this->db->query("SELECT * FROM spiritsdb where ql >= ? AND ql <= ? ORDER BY ql", $qllorange, $qlhirange);
			$spirits .= $this->formatSpiritOutput($data);
		}
			// If searched by ql and slot
		else if (preg_match("/^spirits ([0-9]+) (.+)$/i", $message, $arr)) {
			$ql = $arr[1];
			$slot = ucwords(strtolower($arr[2]));
			$title = "$slot Spirits QL $ql";
			if ($ql < 1 OR $ql > 300) {
				$msg = "Invalid Ql specified.";
				$sendto->reply($msg);
				return;
			} else if (preg_match("/[^chest|ear|eye|feet|head|larm|legs|lhand|lwrist|rarm|rhand|rwrist|waist]/i", $slot)) {
				$spirits .= "Invalid Input\n\n";
				$spirits .= $this->getValidSlotTypes();
			} else {
				$data = $this->db->query("SELECT * FROM spiritsdb where spot = ? AND ql = ? ORDER BY ql", $slot, $ql);
				$spirits .= $this->formatSpiritOutput($data);
			}
		}
			// If searched by ql range and slot
		else if (preg_match("/^spirits ([0-9]+)-([0-9]+) (.+)$/i", $message, $arr)) {
			$qllorange = $arr[1];
			$qlhirange = $arr[2];
			$slot = ucwords(strtolower($arr[3]));
			$title = "$slot Spirits QL $qllorange to $qlhirange";
			if ($qllorange < 1 OR $qlhirange > 300 OR $qllorange >= $qlhirange) {
				$msg = "Invalid Ql range specified.";
				$sendto->reply($msg);
				return;
			} else if (preg_match("/[^chest|ear|eye|feet|head|larm|legs|lhand|lwrist|rarm|rhand|rwrist|waist]/i", $slot)) {
				$spirits .= "Invalid Input\n\n";
				$spirits .= $this->getValidSlotTypes();
			} else {
				$data = $this->db->query("SELECT * FROM spiritsdb where spot = ? AND ql >= ? AND ql <= ? ORDER BY ql", $slot, $qllorange, $qlhirange);
				$spirits .= $this->formatSpiritOutput($data);
			}
		}
		if ($spirits) {
			$spirits = $this->text->makeBlob("Spirits", $spirits, $title);
			$sendto->reply($spirits);
		} else {
			return false;
		}
	}

	public function formatSpiritOutput($data) {
		if (count($data) == 0) {
			return "No matches found.";
		}

		$msg = '';
		forEach ($data as $row) {
			$slot = $row->spot;
			$lvl = $row->level;
			$lowid = $row->id;
			$agi = $row->agility;

			$data2 = $this->db->query("SELECT * FROM aodb WHERE lowid = ?", $lowid);
			forEach ($data2 as $row); {
				$highid = $row->highid;
				$icon = $row->icon;
				$name = $row->name;
				$ql = $row->highql;
			}
			$msg .= $this->text->makeImage($icon) . ' ';
			$msg .= $this->text->makeItem($lowid, $highid, $ql, $name) . "\n";
			$msg .= "Minimum Level=$lvl   Slot=$slot   Agility/Sense Needed=$agi\n\n";
		}
		return $msg;
	}

	public function getValidSlotTypes() {
		$output = "Valid slot types are:\n";
		$output .= "Head\n";
		$output .= "Eye\n";
		$output .= "Ear\n";
		$output .= "Chest\n";
		$output .= "Larm\n";
		$output .= "Rarm\n";
		$output .= "Waist\n";
		$output .= "Lwrist\n";
		$output .= "Rwrist\n";
		$output .= "Legs\n";
		$output .= "Lhand\n";
		$output .= "Rhand\n";
		$output .= "Feet\n";

		return $output;
	}
}
