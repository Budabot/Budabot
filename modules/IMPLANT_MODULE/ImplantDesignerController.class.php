<?php

namespace Budabot\User\Modules;

use Budabot\Core\AutoInject;
use Budabot\Core\DB;
use \stdClass;
use PDO;

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
		
		$this->db->loadSQLFile($this->moduleName, "implant_design");
		
		$this->db->loadSQLFile($this->moduleName, "Ability");
		$this->db->loadSQLFile($this->moduleName, "Cluster");
		$this->db->loadSQLFile($this->moduleName, "ClusterImplantMap");
		$this->db->loadSQLFile($this->moduleName, "ClusterType");
		$this->db->loadSQLFile($this->moduleName, "EffectTypeMatrix");
		$this->db->loadSQLFile($this->moduleName, "EffectValue");
		$this->db->loadSQLFile($this->moduleName, "ImplantMatrix");
		$this->db->loadSQLFile($this->moduleName, "ImplantType");
		$this->db->loadSQLFile($this->moduleName, "Profession");
		$this->db->loadSQLFile($this->moduleName, "Symbiant");
		$this->db->loadSQLFile($this->moduleName, "SymbiantAbilityMatrix");
		$this->db->loadSQLFile($this->moduleName, "SymbiantClusterMatrix");
		$this->db->loadSQLFile($this->moduleName, "SymbiantProfessionMatrix");
	}
	
	/**
	 * @HandlesCommand("implantdesigner")
	 * @Matches("/^implantdesigner$/i")
	 */
	public function implantdesignerCommand($message, $channel, $sender, $sendto, $args) {
		$design = $this->getDesign($sender, '@');
	
		$blob = '';
		forEach ($this->slots as $slot) {
			$blob .= $this->text->make_chatcmd($slot, "/tell <myname> implantdesigner $slot");
			if (!empty($design->$slot)) {
				if (!empty($design->$slot->symb)) {
					$blob .= $this->getSymbSummary($design->$slot->symb);
				} else {
					$blob .= $this->getImplantSummary($design->$slot);
				}
			} else {
				$blob .= "\n";
			}
			$blob .= "\n";
		}
		
		$blob .= "\n" . $this->text->make_chatcmd("Clear All", "/tell <myname> implantdesigner clear");

		$msg = $this->text->make_blob("Implant Designer", $blob);

		$sendto->reply($msg);
	}
	
	private function getSymbSummary($symb) {
		$msg = "\n<tab>" . $symb . "\n";
		return $msg;
	}
	
	private function getImplantSummary($slotObj) {
		$ql = empty($slotObj->ql) ? 300 : $slotObj->ql;
		$implant = $this->getImplantInfo($ql, $slotObj->shiny, $slotObj->bright, $slotObj->faded);
		$msg = " QL" . $ql;
		if ($implant !== null) {
			$msg .= " - Treatment: {$implant->Treatment} {$implant->AbilityName}: {$implant->Ability}";
		}
		$msg .= "\n";
		$msg .= "<tab>" . $this->getClusterInfo($ql, $slotObj->shiny, 'shiny', $implant) . "\n";
		$msg .= "<tab>" . $this->getClusterInfo($ql, $slotObj->bright, 'bright', $implant) . "\n";
		$msg .= "<tab>" . $this->getClusterInfo($ql, $slotObj->faded, 'faded', $implant) . "\n";
		return $msg;
	}
	
	private function getClusterInfo($ql, $cluster, $grade, $implant) {
		$effectTypeIdName = ucfirst(strtolower($grade)) . 'EffectTypeID';
		if (empty($cluster)) {
			return '--';
		} else {
			$sql =
				"SELECT
					ID,
					Name,
					MinValLow,
					MaxValLow,
					MinValHigh,
					MaxValHigh
				FROM
					EffectTypeMatrix
				WHERE
					ID = ?";

			$row = $this->db->queryRow($sql, $implant->$effectTypeIdName);
			
			if ($ql < 201) {
				$minVal = $row->MinValLow;
				$maxVal = $row->MaxValLow;
				$minQl = 1;
				$maxQl = 200;
			} else {
				$minVal = $row->MinValHigh;
				$maxVal = $row->MaxValHigh;
				$minQl = 201;
				$maxQl = 300;
			}
			
			$modAmount = $this->util->interpolate($minQl, $maxQl, $minVal, $maxVal, $ql);
			if ($grade == 'bright') {
				$modAmount = round($modAmount * 0.6, 0);
			} else if ($grade == 'faded') {
				$modAmount = round($modAmount * 0.4, 0);
			}

			return $cluster . " ($modAmount)";
		}
	}
	
	/**
	 * @HandlesCommand("implantdesigner")
	 * @Matches("/^implantdesigner clear$/i")
	 */
	public function implantdesignerClearCommand($message, $channel, $sender, $sendto, $args) {
		$this->saveDesign($sender, '@', new stdClass);

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
		
		$design = $this->getDesign($sender, '@');
		
		$spacing = "\n";
		
		$ql = empty($design->$slot->ql) ? 300 : $design->$slot->ql;
		$blob .= "<header2>QL<end> $ql";
		$implant = $this->getImplantInfo($ql, $design->$slot->shiny, $design->$slot->bright, $design->$slot->faded);
		if ($implant !== null) {
			$blob .= " - Treatment: {$implant->Treatment} {$implant->AbilityName}: {$implant->Ability}";
		}
		$blob .= "\n";
		forEach (array(25, 50, 75, 100, 125, 150, 175, 200, 225, 250, 275, 300) as $ql) {
			$blob .= $this->text->make_chatcmd($ql, "/tell <myname> implantdesigner $slot $ql") . " ";
		}
		$blob .= "\n\n";
		
		$blob .= "<header2>Shiny<end>";
		if (!empty($design->$slot->shiny)) {
			$blob .= " - {$design->$slot->shiny}";
		}
		$blob .= "\n";
		$blob .= $this->text->make_chatcmd("-Empty-", "/tell <myname> implantdesigner $slot shiny clear") . $spacing;
		$data = $this->getClustersForSlot($slot, 'shiny');
		forEach ($data as $row) {
			$blob .= $this->text->make_chatcmd($row->skill, "/tell <myname> implantdesigner $slot shiny $row->skill") . $spacing;
		}
		$blob .= "\n\n";
		
		$blob .= "<header2>Bright<end>";
		if (!empty($design->$slot->bright)) {
			$blob .= " - {$design->$slot->bright}";
		}
		$blob .= "\n";
		$blob .= $this->text->make_chatcmd("-Empty-", "/tell <myname> implantdesigner $slot bright clear") . $spacing;
		$data = $this->getClustersForSlot($slot, 'bright');
		forEach ($data as $row) {
			$blob .= $this->text->make_chatcmd($row->skill, "/tell <myname> implantdesigner $slot bright $row->skill") . $spacing;
		}
		$blob .= "\n\n";
		
		$blob .= "<header2>Faded<end>";
		if (!empty($design->$slot->faded)) {
			$blob .= " - {$design->$slot->faded}";
		}
		$blob .= "\n";
		$blob .= $this->text->make_chatcmd("-Empty-", "/tell <myname> implantdesigner $slot faded clear") . $spacing;
		$data = $this->getClustersForSlot($slot, 'faded');
		forEach ($data as $row) {
			$blob .= $this->text->make_chatcmd($row->skill, "/tell <myname> implantdesigner $slot faded $row->skill") . $spacing;
		}
		$blob .= "\n\n";
		
		$blob .= "<pagebreak><header2>Symb<end>";
		if (!empty($design->$slot->symb)) {
			$blob .= " - {$design->$slot->symb}";
		}
		$blob .= "\n";
		$data = $this->getSymbsForSlot($slot, 'fixer', 100, 7);
		forEach ($data as $row) {
			$blob .= $this->text->make_chatcmd($row->Name, "/tell <myname> implantdesigner $slot symb $row->Name") . "\n";
		}
		$blob .= "\n\n";
		
		$blob .= $this->text->make_chatcmd("Show Build", "/tell <myname> implantdesigner");
		$blob .= "<tab><tab>";
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
		
		$design = $this->getDesign($sender, '@');
		
		if (strtolower($skill) == 'clear') {
			unset($design->$slot->$type);
			$msg = "<highlight>$slot($type)<end> has been cleared.";
		} else {
			$design->$slot->$type = $skill;
			unset($design->$slot->symb);
			$msg = "<highlight>$slot($type)<end> has been set to <highlight>$skill<end>.";
		}
		
		$this->saveDesign($sender, '@', $design);

		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("implantdesigner")
	 * @Matches("/^implantdesigner (head|eye|ear|rarm|chest|larm|rwrist|waist|lwrist|rhand|legs|lhand|feet) symb (.+)$/i")
	 */
	public function implantdesignerSlotAddSymbCommand($message, $channel, $sender, $sendto, $args) {
		$slot = strtolower($args[1]);
		$symb = $args[2];
		
		$design = $this->getDesign($sender, '@');
		
		$design->$slot->symb = $symb;
		unset($design->$slot->shiny);
		unset($design->$slot->bright);
		unset($design->$slot->faded);
		unset($design->$slot->ql);
		$msg = "<highlight>$slot<end> has been set to <highlight>$symb<end>.";
		
		$this->saveDesign($sender, '@', $design);

		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("implantdesigner")
	 * @Matches("/^implantdesigner (head|eye|ear|rarm|chest|larm|rwrist|waist|lwrist|rhand|legs|lhand|feet) (\d+)$/i")
	 */
	public function implantdesignerSlotQLCommand($message, $channel, $sender, $sendto, $args) {
		$slot = strtolower($args[1]);
		$ql = $args[2];
		
		$design = $this->getDesign($sender, '@');
		$design->$slot->ql = $ql;
		unset($design->$slot->symb);
		$this->saveDesign($sender, '@', $design);
		
		$msg = "<highlight>$slot<end> has been set to QL <highlight>$ql<end>.";

		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("implantdesigner")
	 * @Matches("/^implantdesigner (head|eye|ear|rarm|chest|larm|rwrist|waist|lwrist|rhand|legs|lhand|feet) clear$/i")
	 */
	public function implantdesignerSlotClearCommand($message, $channel, $sender, $sendto, $args) {
		$slot = strtolower($args[1]);
		
		$design = $this->getDesign($sender, '@');
		unset($design->$slot);
		$this->saveDesign($sender, '@', $design);
		
		$msg = "<highlight>$slot<end> has been cleared.";

		$sendto->reply($msg);
	}
	
	public function getImplantInfo($ql, $shiny, $bright, $faded) {
		$shiny = empty($shiny) ? '' : $shiny;
		$bright = empty($bright) ? '' : $bright;
		$faded = empty($faded) ? '' : $faded;

		$sql = 
			"SELECT
				i.AbilityQL1,
				i.AbilityQL200,
				i.AbilityQL201,
				i.AbilityQL300,
				i.TreatQL1,
				i.TreatQL200,
				i.TreatQL201,
				i.TreatQL300,
				c1.EffectTypeID as ShinyEffectTypeID,
				c2.EffectTypeID as BrightEffectTypeID,
				c3.EffectTypeID as FadedEffectTypeID,
				a.Name AS AbilityName
			FROM
				ImplantMatrix i
				JOIN Cluster c1
					ON i.ShiningID = c1.ClusterID
				JOIN Cluster c2
					ON i.BrightID = c2.ClusterID
				JOIN Cluster c3
					ON i.FadedID = c3.ClusterID
				JOIN Ability a
					ON i.AbilityID = a.AbilityID
			WHERE
				c1.LongName = ?
				AND c2.LongName = ?
				AND c3.LongName = ?";

		$row = $this->db->queryRow($sql, $shiny, $bright, $faded);
		if ($row === null) {
			return null;
		} else {
			return $this->addImplantInfo($row, $ql);
		}
	}
	
	private function addImplantInfo($implantInfo, $ql) {
		if ($ql < 201) {
			$minAbility = $implantInfo->AbilityQL1;
			$maxAbility = $implantInfo->AbilityQL200;
			$minTreatment = $implantInfo->TreatQL1;
			$maxTreatment = $implantInfo->TreatQL200;
			$minQl = 1;
			$maxQl = 200;
		} else {
			$minAbility = $implantInfo->AbilityQL201;
			$maxAbility = $implantInfo->AbilityQL300;
			$minTreatment = $implantInfo->TreatQL201;
			$maxTreatment = $implantInfo->TreatQL300;
			$minQl = 201;
			$maxQl = 300;
		}
		
		$implantInfo->Ability = $this->util->interpolate($minQl, $maxQl, $minAbility, $maxAbility, $ql);
		$implantInfo->Treatment = $this->util->interpolate($minQl, $maxQl, $minTreatment, $maxTreatment, $ql);
		
		return $implantInfo;
	}
	
	public function getClustersForSlot($implantType, $clusterType) {
		$sql = 
			"SELECT
				LongName AS skill
			FROM
				Cluster c1
				JOIN ClusterImplantMap c2
					ON c1.ClusterID = c2.ClusterID
				JOIN ClusterType c3
					ON c2.ClusterTypeID = c3.ClusterTypeID
				JOIN ImplantType i
					ON c2.ImplantTypeID = i.ImplantTypeID
			WHERE
				i.ShortName = ?
				AND c3.Name = ?";
				
		return $this->db->query($sql, strtolower($implantType), strtolower($clusterType));
	}
	
	public function getSymbsForSlot($implantType, $profession, $maxLevel, $num) {
		$sql = 
			"SELECT
				s.Name
			FROM
				Symbiant s
				JOIN ImplantType i
					ON s.SlotID = i.ImplantTypeID
				JOIN SymbiantProfessionMatrix s2
					ON s.ID = s2.SymbiantID
				JOIN Profession p
					ON s2.ProfessionID = p.ID
			WHERE
				i.ShortName = ?
				AND p.Name = ?
				AND s.LevelReq <= ?
			ORDER BY
				s.LevelReq DESC
			LIMIT " . $num;
				
		return $this->db->query($sql, strtolower($implantType), ucfirst(strtolower($profession)), $maxLevel);
	}
	
	public function getDesign($sender, $name) {
		$sql = "SELECT * FROM implant_design WHERE owner = ? AND name = ?";
		$row = $this->db->queryRow($sql, $sender, $name);
		if ($row === null) {
			return new stdClass;
		} else {
			return json_decode($row->design);
		}
	}
	
	public function saveDesign($sender, $name, $design) {
		$json = json_encode($design);
		$sql = "UPDATE implant_design SET design = ?, dt = ? WHERE owner = ? AND name = ?";
		$numRows = $this->db->exec($sql, $json, time(), $sender, $name);
		if ($numRows == 0) {
			$sql = "INSERT INTO implant_design (name, owner, dt, design) VALUES (?, ?, ?, ?)";
			$this->db->exec($sql, $name, $sender, time(), $json);
		}
	}
}

?>
