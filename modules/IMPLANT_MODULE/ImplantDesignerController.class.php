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
	private $grades = array('shiny', 'bright', 'faded');
	
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
		$blob = $this->getImplantDesignerBuild($sender);
		$msg = $this->text->make_blob("Implant Designer", $blob);
		$sendto->reply($msg);
	}
	
	private function getImplantDesignerBuild($sender) {
		$design = $this->getDesign($sender, '@');
	
		$blob = $this->text->make_chatcmd("Results", "/tell <myname> implantdesigner results");
		$blob .= "<tab>";
		$blob .= $this->text->make_chatcmd("Clear All", "/tell <myname> implantdesigner clear");
		$blob .= "\n-----------------\n\n";

		forEach ($this->slots as $slot) {
			$blob .= $this->text->make_chatcmd($slot, "/tell <myname> implantdesigner $slot");
			if (!empty($design->$slot)) {
				$blob .= $this->getImplantSummary($design->$slot);
			} else {
				$blob .= "\n";
			}
			$blob .= "\n";
		}

		return $blob;
	}
	
	private function getImplantSummary($slotObj) {
		if ($slotObj->symb !== null) {
			$msg = " " . $slotObj->symb->name . "\n";
		} else {
			$ql = empty($slotObj->ql) ? 300 : $slotObj->ql;
			$implant = $this->getImplantInfo($ql, $slotObj->shiny, $slotObj->bright, $slotObj->faded);
			$msg = " QL" . $ql;
			if ($implant !== null) {
				$msg .= " - Treatment: {$implant->Treatment} {$implant->AbilityName}: {$implant->Ability}";
			}
			$msg .= "\n";
			
			forEach ($this->grades as $grade) {
				if (empty($slotObj->$grade)) {
					$msg .= "<tab><highlight>-Empty-<end>\n";
				} else {
					$newSlotObj = clone $slotObj;
					unset($newSlotObj->$grade);
					$newImplant = $this->getImplantInfo($ql, $newSlotObj->shiny, $newSlotObj->bright, $newSlotObj->faded);
					if ($newImplant !== null && $newImplant->AbilityName != $implant->AbilityName) {
						$changeAbility = " (remove for $newImplant->AbilityName)";
					}

					$effectTypeIdName = ucfirst(strtolower($grade)) . 'EffectTypeID';
					$effectId = $implant->$effectTypeIdName;
					$msg .= "<tab><highlight>{$slotObj->$grade}<end> (" . $this->getClusterModAmount($ql, $grade, $effectId) . ")$changeAbility\n";
				}
			}
		}
		return $msg;
	}
	
	private function getClusterModAmount($ql, $grade, $effectId) {
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

		$row = $this->db->queryRow($sql, $effectId);
		
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

		return $modAmount;
	}
	
	/**
	 * @HandlesCommand("implantdesigner")
	 * @Matches("/^implantdesigner clear$/i")
	 */
	public function implantdesignerClearCommand($message, $channel, $sender, $sendto, $args) {
		$this->saveDesign($sender, '@', new stdClass);
		$msg = "Implant Designer has been cleared.";
		$sendto->reply($msg);
		
		// send results
		$blob = $this->getImplantDesignerBuild($sender);
		$msg = $this->text->make_blob("Implant Designer", $blob);
		$sendto->reply($msg);
	}

	/**
	 * @HandlesCommand("implantdesigner")
	 * @Matches("/^implantdesigner (head|eye|ear|rarm|chest|larm|rwrist|waist|lwrist|rhand|legs|lhand|feet)$/i")
	 */
	public function implantdesignerSlotCommand($message, $channel, $sender, $sendto, $args) {
		$slot = strtolower($args[1]);

		$blob .= $this->text->make_chatcmd("See Build", "/tell <myname> implantdesigner");
		$blob .= "<tab>";
		$blob .= $this->text->make_chatcmd("Clear this slot", "/tell <myname> implantdesigner $slot clear");
		$blob .= "\n-------------------------\n";
		$blob .= "<header2>Implants<end>  ";
		forEach (array(25, 50, 75, 100, 125, 150, 175, 200, 225, 250, 275, 300) as $ql) {
			$blob .= $this->text->make_chatcmd($ql, "/tell <myname> implantdesigner $slot $ql") . " ";
		}
		$blob .= "\n\n" . $this->getSymbiantsLinks($slot);
		$blob .= "\n-------------------------\n\n";
		
		$design = $this->getDesign($sender, '@');
		$slotObj = &$design->$slot;
		
		if ($slotObj->symb !== null) {
			$symb = &$slotObj->symb;
			$blob .= $symb->name ."\n\n";
			$blob .= "<header2>Requirements<end>\n";
			$blob .= "Treatment: {$symb->Treatment}\n";
			$blob .= "Level: {$symb->Level}\n";
			forEach ($symb->reqs as $req){
				$blob .= "{$req->Name}: {$req->Amount}\n";
			}
			$blob .= "\n<header2>Modifications<end>\n";
			forEach ($symb->mods as $mod){
				$blob .= "{$mod->Name}: {$mod->Amount}\n";
			}
			$blob .= "\n\n";
		} else {
			$ql = empty($design->$slot->ql) ? 300 : $design->$slot->ql;
			$blob .= "<header2>QL<end> $ql";
			$implant = $this->getImplantInfo($ql, $design->$slot->shiny, $design->$slot->bright, $design->$slot->faded);
			if ($implant !== null) {
				$blob .= " - Treatment: {$implant->Treatment} {$implant->AbilityName}: {$implant->Ability}";
			}
			$blob .= "\n\n";
			
			$blob .= "<header2>Shiny<end>";
			$blob .= $this->showClusterChoices($design, $slot, 'shiny');
			
			$blob .= "<header2>Bright<end>";
			$blob .= $this->showClusterChoices($design, $slot, 'bright');
			
			$blob .= "<header2>Faded<end>";
			$blob .= $this->showClusterChoices($design, $slot, 'faded');
		}
		
		$msg = $this->text->make_blob("Implant Designer ($slot)", $blob);

		$sendto->reply($msg);
	}
	
	private function getSymbiantsLinks($slot) {
		$artilleryLink = $this->text->make_chatcmd("Artillery", "/tell <myname> symb $slot artillery");
		$controlLink = $this->text->make_chatcmd("Control", "/tell <myname> symb $slot control");
		$exterminationLink = $this->text->make_chatcmd("Extermination", "/tell <myname> symb $slot extermination");
		$infantryLink = $this->text->make_chatcmd("Infantry", "/tell <myname> symb $slot infantry");
		$supportLink = $this->text->make_chatcmd("Support", "/tell <myname> symb $slot support");
		return "<header2>Symbiants<end>  $artilleryLink  $controlLink  $exterminationLink  $infantryLink  $supportLink";
	}
	
	private function showClusterChoices($design, $slot, $grade) {
		$msg = '';
		if (!empty($design->$slot->$grade)) {
			$msg .= " - {$design->$slot->$grade}";
		}
		$msg .= "\n";
		$msg .= $this->text->make_chatcmd("-Empty-", "/tell <myname> implantdesigner $slot $grade clear") . "\n";
		$data = $this->getClustersForSlot($slot, $grade);
		forEach ($data as $row) {
			$msg .= $this->text->make_chatcmd($row->skill, "/tell <myname> implantdesigner $slot $grade $row->skill") . "\n";
		}
		$msg .= "\n\n";
		return $msg;
	}
	
	/**
	 * @HandlesCommand("implantdesigner")
	 * @Matches("/^implantdesigner (head|eye|ear|rarm|chest|larm|rwrist|waist|lwrist|rhand|legs|lhand|feet) (shiny|bright|faded|symb) (.+)$/i")
	 */
	public function implantdesignerSlotAddClusterCommand($message, $channel, $sender, $sendto, $args) {
		$slot = strtolower($args[1]);
		$type = strtolower($args[2]);
		$item = $args[3];
		
		$design = $this->getDesign($sender, '@');
		$slotObj = &$design->$slot;
		
		if ($type == 'symb') {
			$sql = 
				"SELECT
					s.ID,
					s.Name,
					s.TreatmentReq,
					s.LevelReq
				FROM
					Symbiant s
					JOIN ImplantType i
						ON s.SlotID = i.ImplantTypeID
				WHERE
					i.ShortName = ?
					AND s.Name = ?";
			
			$symbRow = $this->db->queryRow($sql, $slot, $item);
			
			if ($symbRow === null) {
				$msg = "Could not find symbiant <highlight>$item<end>.";
			} else {
				// convert slot to symb
				unset($slotObj->shiny);
				unset($slotObj->bright);
				unset($slotObj->faded);
				unset($slotObj->ql);
				
				$symb = new stdClass;
				$symb->name = $symbRow->Name;
				$symb->Treatment = $symbRow->TreatmentReq;
				$symb->Level = $symbRow->LevelReq;
				
				// add requirements
				$sql = "SELECT a.Name, s.Amount FROM SymbiantAbilityMatrix s JOIN Ability a ON s.AbilityID = a.AbilityID WHERE SymbiantID = ?";
				$symb->reqs = $this->db->query($sql, $symbRow->ID);
				
				// add mods
				$sql = "SELECT c.LongName AS Name, s.Amount FROM SymbiantClusterMatrix s JOIN Cluster c ON s.ClusterID = c.ClusterID WHERE SymbiantID = ?";
				$symb->mods = $this->db->query($sql, $symbRow->ID);
				
				$slotObj->symb = $symb;
				$msg = "<highlight>$slot(symb)<end> has been set to <highlight>$symb->name<end>.";
			}
		} else {
			if (strtolower($item) == 'clear') {
				if ($slotObj->$type === null) {
					$msg = "There is no cluster in <highlight>$slot($type)<end>.";
				} else {
					unset($slotObj->$type);
					$msg = "<highlight>$slot($type)<end> has been cleared.";
				}
			} else {
				unset($slotObj->$type);
				$slotObj->$type = $item;
				$msg = "<highlight>$slot($type)<end> has been set to <highlight>$item<end>.";
			}
		}
		
		$this->saveDesign($sender, '@', $design);
		
		$sendto->reply($msg);
	
		// send results
		$blob = $this->getImplantDesignerBuild($sender);
		$msg = $this->text->make_blob("Implant Designer", $blob);
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
		$slotObj = &$design->$slot;
		unset($slotObj->symb);
		$slotObj->ql = $ql;
		$this->saveDesign($sender, '@', $design);
		
		$msg = "<highlight>$slot<end> has been set to QL <highlight>$ql<end>.";

		$sendto->reply($msg);
		
		// send results
		$blob = $this->getImplantDesignerBuild($sender);
		$msg = $this->text->make_blob("Implant Designer", $blob);
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
		
		// send results
		$blob = $this->getImplantDesignerBuild($sender);
		$msg = $this->text->make_blob("Implant Designer", $blob);
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("implantdesigner")
	 * @Matches("/^implantdesigner (result|results)$/i")
	 */
	public function implantdesignerResultCommand($message, $channel, $sender, $sendto, $args) {
		$blob = $this->getImplantDesignerResults($sender);
		
		$msg = $this->text->make_blob("Implant Designer Results", $blob);

		$sendto->reply($msg);
	}
	
	public function getImplantDesignerResults($name) {
		$design = $this->getDesign($name, '@');
		
		$mods = array();
		$reqs = array('Treatment' => 0, 'Level' => 1);  // force treatment and level to be shown first
		$implants = array();
		$clusters = array();
		
		forEach ($this->slots as $slot) {
			$slotObj = &$design->$slot;
			
			// skip empty slots
			if (empty($slotObj)) {
				continue;
			}
			
			if (!empty($slotObj->symb)) {
				$symb = &$slotObj->symb;
				
				// add reqs
				if ($symb->Treatment > $reqs['Treatment']) {
					$reqs['Treatment'] = $symb->Treatment;
				}
				if ($symb->Level > $reqs['Level']) {
					$reqs['Level'] = $symb->Level;
				}
				forEach ($symb->reqs as $req) {
					if ($req->Amount > $reqs[$req->Name]) {
						$reqs[$req->Name] = $req->Amount;
					}
				}
				
				// add mods
				forEach ($symb->mods as $mod) {
					$mods[$mod->Name] += $mod->Amount;
				}
				
			} else {
				$ql = 300;
				if (!empty($slotObj->ql)) {
					$ql = $slotObj->ql;
				}
				
				// add reqs
				$implant = $this->getImplantInfo($ql, $slotObj->shiny, $slotObj->bright, $slotObj->faded);
				if ($implant->Treatment > $reqs['Treatment']) {
					$reqs['Treatment'] = $implant->Treatment;
				}
				if ($implant->Ability > $reqs[$implant->AbilityName]) {
					$reqs[$implant->AbilityName] = $implant->Ability;
				}
				
				// add implant
				$obj = new stdClass;
				$obj->ql = $ql;
				$obj->slot = $slot;
				$implants []= $obj;
				
				// add mods
				forEach ($this->grades as $grade) {
					if (!empty($slotObj->$grade)) {
						$effectTypeIdName = ucfirst(strtolower($grade)) . 'EffectTypeID';
						$effectId = $implant->$effectTypeIdName;
						$mods[$slotObj->$grade] += $this->getClusterModAmount($ql, $grade, $effectId);
						
						// add cluster
						$obj = new stdClass;
						$obj->ql = $this->implantController->getClusterMinQl($ql, $grade);
						$obj->slot = $slot;
						$obj->grade = $grade;
						$obj->name = $slotObj->$grade;
						$clusters []= $obj;
					}
				}
			}
		}
		
		// sort mods by name alphabetically
		ksort($mods);
		
		// sort clusters by name alphabetically, and then by grade, shiny first
		$grades = $this->grades;
		usort($clusters, function($cluster1, $cluster2) use($grades) {
			$val = strcmp($cluster1->name, $cluster2->name);
			if ($val == 0) {
				$val1 = array_search($cluster1->grade, $grades);
				$val2 = array_search($cluster2->grade, $grades);
				return $val1 > $val2;
			} else {
				return $val > 0;
			}
		});
		
		$blob .= $this->text->make_chatcmd("See Build", "/tell <myname> implantdesigner");
		$blob .= "\n---------\n\n";
		
		$blob .= "<header2>Requirements to Equip<end>\n";
		forEach ($reqs as $requirement => $amount) {
			$blob .= "$requirement: <highlight>$amount<end>\n";
		}
		$blob .= "\n";
		
		$blob .= "<header2>Skills Gained<end>\n";
		forEach ($mods as $skill => $amount) {
			$blob .= "$skill: <highlight>$amount<end>\n";
		}
		$blob .= "\n";
		
		$blob .= "<header2>Basic Implants Needed<end>\n";
		forEach ($implants as $implant) {
			$blob .= "<highlight>$implant->slot<end> ($implant->ql)\n";
		}
		$blob .= "\n";
		
		$blob .= "<header2>Clusters Needed<end>\n";
		forEach ($clusters as $cluster) {
			$blob .= "<highlight>{$cluster->name}<end>, {$cluster->grade} ({$cluster->ql}+)\n";
		}
		
		return $blob;
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
