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
			$blob .= $this->text->make_chatcmd($slot, "/tell <myname> implantdesigner $slot") . "\n";
			if (!empty($this->design->$slot)) {
				$blob .= "<tab>" . (empty($this->design->$slot->shiny) ? 'empty' : $this->design->$slot->shiny) . "\n";
				$blob .= "<tab>" . (empty($this->design->$slot->bright) ? 'empty' : $this->design->$slot->bright) . "\n";
				$blob .= "<tab>" . (empty($this->design->$slot->faded) ? 'empty' : $this->design->$slot->faded) . "\n";
			}
			$blob .= "\n";
		}

		$msg = $this->text->make_blob("Implant Designer", $blob);

		$sendto->reply($msg);
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
		
		$blob .= "<header2>Shiny<end>\n";
		$sql = "SELECT * FROM cluster WHERE shiny = ?";
		$data = $this->db->query($sql, $slot);
		forEach ($data as $row) {
			$blob .= $this->text->make_chatcmd($row->skill, "/tell <myname> implantdesigner $slot shiny $row->skill") . "\n";
		}
		$blob .= "\n";
		
		$blob .= "<header2>Bright<end>\n";
		$sql = "SELECT * FROM cluster WHERE bright = ?";
		$data = $this->db->query($sql, $slot);
		forEach ($data as $row) {
			$blob .= $this->text->make_chatcmd($row->skill, "/tell <myname> implantdesigner $slot bright $row->skill") . "\n";
		}
		$blob .= "\n";
		
		$blob .= "<header2>Faded<end>\n";
		$sql = "SELECT * FROM cluster WHERE faded = ?";
		$data = $this->db->query($sql, $slot);
		forEach ($data as $row) {
			$blob .= $this->text->make_chatcmd($row->skill, "/tell <myname> implantdesigner $slot faded $row->skill") . "\n";
		}
		$blob .= "\n";
		
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
		
		$this->design->$slot->$type = $skill;
		
		$msg = "<highlight>$skill<end> has been assigned to <highlight>$slot($type)<end>.";

		$sendto->reply($msg);
	}
}

?>
