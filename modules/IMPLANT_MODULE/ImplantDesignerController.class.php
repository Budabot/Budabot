<?php

namespace Budabot\User\Modules;

use Budabot\Core\AutoInject;
use \stdClass;

/**
 * Authors: 
 *  - Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this class contains:
 *	@DefineCommand(
 *		command     = 'implantdesigner',
 *		accessLevel = 'all',
 *		description = 'Implant Designer',
 *		help        = 'implantdesigner.txt',
 *		alias		= 'impdesign'
 *	)
 */
class ImplantDesignerController extends AutoInject {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;
	
	private $slots = array('head', 'eye', 'ear', 'rarm', 'chest', 'larm', 'rwrist', 'waist', 'lwrist', 'rhand', 'legs', 'lhand', 'feet');
	
	private $design;
	
	/**
	 * @Setup
	 */
	public function setup() {
		$this->design = new stdClass;
	}
	
	/**
	 * @HandlesCommand("implantdesigner")
	 * @Matches("/^implantdesigner$/i")
	 */
	public function implantdesignerCommand($message, $channel, $sender, $sendto, $args) {
		$blob = '';
		forEach ($this->slots as $slot) {
			$blob .= $this->text->make_chatcmd($slot, "/tell <myname> implantdesigner $slot");
			if (!empty($this->design->$slot)) {
				$ql = empty($this->design->$slot->ql) ? 300 : $this->design->$slot->ql;
				$implant = $this->implantController->getRequirements($ql);
				$blob .= " QL" . $ql . " - Treatment: {$implant->treatment} Ability: {$implant->ability}\n";
				$blob .= "<tab>" . $this->getClusterInfo($slot, 'shiny', $implant) . "\n";
				$blob .= "<tab>" . $this->getClusterInfo($slot, 'bright', $implant) . "\n";
				$blob .= "<tab>" . $this->getClusterInfo($slot, 'faded', $implant) . "\n";
			} else {
				$blob .= "\n";
			}
			$blob .= "\n";
		}
		
		$blob .= "\n" . $this->text->make_chatcmd("Clear All", "/tell <myname> implantdesigner clear");

		$msg = $this->text->make_blob("Implant Designer", $blob);

		$sendto->reply($msg);
	}
	
	private function getClusterInfo($slot, $type, $implant) {
		$skillModName = 'skill' . ucfirst(strtolower($type));
		if (empty($this->design->$slot->$type)) {
			return '--';
		} else {
			return $this->design->$slot->$type . " ({$implant->$skillModName})";
		}
	}
	
	/**
	 * @HandlesCommand("implantdesigner")
	 * @Matches("/^implantdesigner clear$/i")
	 */
	public function implantdesignerClearCommand($message, $channel, $sender, $sendto, $args) {
		$this->design = new stdClass;

		$msg = "Implant Designer has been cleared.";

		$sendto->reply($msg);
	}

	/**
	 * @HandlesCommand("implantdesigner")
	 * @Matches("/^implantdesigner (head|eye|ear|rarm|chest|larm|rwrist|waist|lwrist|rhand|legs|lhand|feet)$/i")
	 */
	public function implantdesignerSlotCommand($message, $channel, $sender, $sendto, $args) {
		$slot = strtolower($args[1]);
		$blob = '';
		
		$spacing = "   ";
		
		$ql = empty($this->design->$slot->ql) ? 300 : $this->design->$slot->ql;
		$implant = $this->implantController->getRequirements($ql);
		$blob .= "<header2>QL<end> $ql - Treatment: {$implant->treatment} Ability: {$implant->ability}\n";
		forEach (array(25, 50, 75, 100, 125, 150, 175, 200, 225, 250, 275, 300) as $ql) {
			$blob .= $this->text->make_chatcmd($ql, "/tell <myname> implantdesigner $slot $ql") . " ";
		}
		$blob .= "\n\n";
		
		$blob .= "<header2>Shiny<end>";
		if (!empty($this->design->$slot->shiny)) {
			$blob .= " - {$this->design->$slot->shiny}";
		}
		$blob .= "\n";
		$blob .= $this->text->make_chatcmd("Empty", "/tell <myname> implantdesigner $slot shiny clear") . $spacing;
		$sql = "SELECT * FROM cluster WHERE shiny = ? ORDER BY skill ASC";
		$data = $this->db->query($sql, $slot);
		forEach ($data as $row) {
			$blob .= $this->text->make_chatcmd($row->skill, "/tell <myname> implantdesigner $slot shiny $row->skill") . $spacing;
		}
		$blob .= "\n\n";
		
		$blob .= "<header2>Bright<end>";
		if (!empty($this->design->$slot->bright)) {
			$blob .= " - {$this->design->$slot->bright}";
		}
		$blob .= "\n";
		$blob .= $this->text->make_chatcmd("Empty", "/tell <myname> implantdesigner $slot bright clear") . $spacing;
		$sql = "SELECT * FROM cluster WHERE bright = ? ORDER BY skill ASC";
		$data = $this->db->query($sql, $slot);
		forEach ($data as $row) {
			$blob .= $this->text->make_chatcmd($row->skill, "/tell <myname> implantdesigner $slot bright $row->skill") . $spacing;
		}
		$blob .= "\n\n";
		
		$blob .= "<header2>Faded<end>";
		if (!empty($this->design->$slot->faded)) {
			$blob .= " - {$this->design->$slot->faded}";
		}
		$blob .= "\n";
		$blob .= $this->text->make_chatcmd("Empty", "/tell <myname> implantdesigner $slot faded clear") . $spacing;
		$sql = "SELECT * FROM cluster WHERE faded = ? ORDER BY skill ASC";
		$data = $this->db->query($sql, $slot);
		forEach ($data as $row) {
			$blob .= $this->text->make_chatcmd($row->skill, "/tell <myname> implantdesigner $slot faded $row->skill") . $spacing;
		}
		$blob .= "\n\n\n";
		
		$blob .= $this->text->make_chatcmd("Clear this slot", "/tell <myname> implantdesigner $slot clear");
		
		$msg = $this->text->make_blob("Implant Designer ($slot)", $blob);

		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("implantdesigner")
	 * @Matches("/^implantdesigner (head|eye|ear|rarm|chest|larm|rwrist|waist|lwrist|rhand|legs|lhand|feet) (shiny|bright|faded) (.+)$/i")
	 */
	public function implantdesignerSlotAddClusterCommand($message, $channel, $sender, $sendto, $args) {
		$slot = strtolower($args[1]);
		$type = strtolower($args[2]);
		$skill = $args[3];
		
		if (strtolower($skill) == 'clear') {
			unset($this->design->$slot->$type);
			$msg = "<highlight>$slot($type)<end> has been cleared.";
		} else {
			$this->design->$slot->$type = $skill;
			$msg = "<highlight>$slot($type)<end> has been set to <highlight>$skill<end>.";
		}

		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("implantdesigner")
	 * @Matches("/^implantdesigner (head|eye|ear|rarm|chest|larm|rwrist|waist|lwrist|rhand|legs|lhand|feet) (\d+)$/i")
	 */
	public function implantdesignerSlotQLCommand($message, $channel, $sender, $sendto, $args) {
		$slot = strtolower($args[1]);
		$ql = $args[2];
		
		$this->design->$slot->ql = $ql;
		
		$msg = "<highlight>$slot<end> has been set to QL <highlight>$ql<end>.";

		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("implantdesigner")
	 * @Matches("/^implantdesigner (head|eye|ear|rarm|chest|larm|rwrist|waist|lwrist|rhand|legs|lhand|feet) clear$/i")
	 */
	public function implantdesignerSlotClearCommand($message, $channel, $sender, $sendto, $args) {
		$slot = strtolower($args[1]);
		
		unset($this->design->$slot);
		
		$msg = "<highlight>$slot<end> has been cleared.";

		$sendto->reply($msg);
	}
}

?>
