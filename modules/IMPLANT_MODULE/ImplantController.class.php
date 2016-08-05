<?php

namespace Budabot\User\Modules;

/**
 * Authors: 
 *  - Tyrence (RK2)
 *	- Imoutochan (RK1)
 *
 * @Instance
 *
 * Commands this class contains:
 *	@DefineCommand(
 *		command     = 'implant',
 *		accessLevel = 'all',
 *		description = 'Shows info about implants given a QL or stats',
 *		help        = 'implant.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'premade',
 *		accessLevel = 'all',
 *		description = 'Searches for implants out of the premade implants booths',
 *		help        = 'premade.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'cluster',
 *		accessLevel = 'all',
 *		description = 'Find which clusters buff a specified skill',
 *		help        = 'cluster.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'ladder',
 *		accessLevel = 'all',
 *		description = 'Show sequence of laddering implants for maximum ability or treatment',
 *		help        = 'ladder.txt'
 *	)
 */
class ImplantController {

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
	
	private $slots = array('head', 'eye', 'ear', 'rarm', 'chest', 'larm', 'rwrist', 'waist', 'lwrist', 'rhand', 'legs', 'lhand', 'feet');
	
	/**
	 * @Setup
	 */
	public function setup() {
		$this->db->loadSQLFile($this->moduleName, "implant_requirements");
		$this->db->loadSQLFile($this->moduleName, "premade_implant");
	}
	
	/**
	 * @HandlesCommand("implant")
	 * @Matches("/^implant ([0-9]+)$/i")
	 */
	public function implantQlCommand($message, $channel, $sender, $sendto, $args) {
		$ql = $args[1];

		// make sure the $ql is an integer between 1 and 300
		if (($ql < 1) || ($ql > 300)) {
			$msg = "You must enter a value between 1 and 300.";
		} else {
			$obj = $this->getRequirements($ql);
			$clusterInfo = $this->formatClusterBonuses($obj);
			$link = $this->text->makeBlob("QL$obj->ql", $clusterInfo, "Implant Info (QL $obj->ql)");
			$msg = "QL $ql implants--Ability: {$obj->ability}, Treatment: {$obj->treatment} $link";

			$msg = "$link: <highlight>$obj->ability<end> Ability, <highlight>$obj->treatment<end> Treatment";
		}

		$sendto->reply($msg);
	}

	/**
	 * @HandlesCommand("implant")
	 * @Matches("/^implant ([0-9]+) ([0-9]+)$/i")
	 */
	public function implantRequirementsCommand($message, $channel, $sender, $sendto, $args) {
		$ability = $args[1];
		$treatment = $args[2];

		if ($treatment < 11 || $ability < 6) {
			$msg = "You do not have enough treatment or ability to wear an implant.";
		} else {
			$obj = $this->findMaxImplantQlByReqs($ability, $treatment);
			$clusterInfo = $this->formatClusterBonuses($obj);
			$link = $this->text->makeBlob("QL$obj->ql", $clusterInfo, "Implant Info (QL $obj->ql)");

			$msg = "$link: <highlight>$obj->ability<end> Ability, <highlight>$obj->treatment<end> Treatment";
		}
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("premade")
	 * @Matches("/^premade (.*)$/i")
	 */
	public function premadeCommand($message, $channel, $sender, $sendto, $args) {
		$searchTerms = strtolower($args[1]);
		$results = null;

		$profession = $this->util->getProfessionName($searchTerms);
		if ($profession != '') {
			$searchTerms = $profession;
			$results = $this->searchByProfession($profession);
		} else if (in_array($searchTerms, $this->slots)) {
			$results = $this->searchBySlot($searchTerms);
		} else {
			$results = $this->searchByModifier($searchTerms);
		}

		if ($results != null) {
			$blob = $this->formatResults($results);
			$blob .= "\n\nWritten by Tyrence (RK2)";
			$msg = $this->text->makeBlob("Implant Search Results for '$searchTerms'", $blob);
		} else {
			$msg = "No results found.";
		}

		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("cluster")
	 * @Matches("/^cluster (.+)$/i")
	 */
	public function clusterCommand($message, $channel, $sender, $sendto, $args) {
		$search = trim($args[1]);
		
		list($query, $params) = $this->util->generateQueryFromParams(explode(' ', $search), 'LongName');

		$sql = "SELECT ClusterID, LongName FROM Cluster WHERE $query";
		$data = $this->db->query($sql, $params);
		$count = count($data);

		if ($count == 0) {
			$msg = "No skills found that match <highlight>$search<end>.";
		} else {
			$implantDesignerLink = $this->text->make_chatcmd("implant designer", "/tell <myname> implantdesigner");
			$blob = "Click 'Add' to add cluster to $implantDesignerLink.\n\n";
			forEach ($data as $cluster) {
				$sql = "SELECT i.ShortName as Slot, c2.Name AS ClusterType FROM ClusterImplantMap c1 JOIN ClusterType c2 ON c1.ClusterTypeID = c2.ClusterTypeID JOIN ImplantType i ON c1.ImplantTypeID = i.ImplantTypeID WHERE c1.ClusterID = ? ORDER BY c2.ClusterTypeID DESC";
				$results = $this->db->query($sql, $cluster->ClusterID);
				
				$blob .= "<pagebreak><highlight>$cluster->LongName<end>:\n<tab>";

				forEach ($results as $row) {
					$impDesignerLink = $this->text->make_chatcmd("Add", "/tell <myname> implantdesigner $row->Slot $row->ClusterType $cluster->LongName");
					$clusterType = ucfirst($row->ClusterType);
					$blob .= "<font color=#ffcc33>$clusterType</font>: $row->Slot ($impDesignerLink)<tab>";
				}
				$blob .= "\n\n";
			}
			$msg = $this->text->makeBlob("Cluster search results ($count)", $blob);
		}
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("ladder")
	 * @Matches("/^ladder (.+) (\d+)$/i")
	 */
	public function ladderCommand($message, $channel, $sender, $sendto, $args) {
		$type = strtolower($args[1]);
		$startingValue = $args[2];
		
		if ($type == 'treat') {
			$type = 'treatment';
		}
		
		// allow treatment, ability, or any of the 6 abilities
		if ($type != 'treatment' && $type != 'ability') {
			$type = $this->util->getAbility($type, true);
			if ($type === null) {
				return false;
			}
			$type = strtolower($type);
		}

		$value = $startingValue;
		$prefix = $type == 'treatment' ? 'skill' : 'ability';
		
		$blob = "Starting $type: $value\n\n-------------------\n\n";
		
		$that = $this;
		if ($type == 'treatment') {
			if ($value < 11) {
				$sendto->reply("Base treatment must be at least <highlight>11<end>.");
				return;
			}
		
			$getMax = function($value) use ($that) {
				return $that->findMaxImplantQlByReqs(10000, $value);
			};
		} else {
			if ($value < 6) {
				$sendto->reply("Base ability must be at least <highlight>6<end>.");
				return;
			}
		
			$getMax = function($value) use ($that) {
				return $that->findMaxImplantQlByReqs($value, 10000);
			};
		}

		$shiny = null;
		$bright = null;
		$faded = null;
		$added = true;

		// will continue to loop as long as at least one implant is added each loop
		while ($added) {
			$added = false;
			
			// add shiny
			$tempValue = $shiny === null ? $value : $value - $shiny->{$prefix . 'Shiny'};
			$newShiny = $getMax($tempValue);
			if ($shiny === null || $newShiny->{$prefix . 'Shiny'} > $shiny->{$prefix . 'Shiny'}) {
				$added = true;
				if ($shiny !== null) {
					$value -= $shiny->{$prefix . 'Shiny'};
					$blob .= "Remove shiny QL $shiny->ql\n\n";
				}
				$shiny = $newShiny;
				$value += $shiny->{$prefix . 'Shiny'};
				$lowest = $shiny->{'lowest' . ucfirst($prefix) . 'Shiny'};
				$blob .= "<highlight>Add shiny QL $shiny->ql<end> ($lowest) - Treatment: {$shiny->treatment}, Ability: {$shiny->ability}\n\n";
			}
			
			// add bright
			$tempValue = $bright === null ? $value : $value - $bright->{$prefix . 'Bright'};
			$newBright = $getMax($tempValue);
			if ($bright === null || $newBright->{$prefix . 'Bright'} > $bright->{$prefix . 'Bright'}) {
				$added = true;
				if ($bright !== null) {
					$value -= $bright->{$prefix . 'Bright'};
					$blob .= "Remove bright QL $bright->ql\n\n";
				}
				$bright = $newBright;
				$value += $bright->{$prefix . 'Bright'};
				$lowest = $bright->{'lowest' . ucfirst($prefix) . 'Bright'};
				$blob .= "<highlight>Add bright QL $bright->ql<end> ($lowest) - Treatment: {$bright->treatment}, Ability: {$bright->ability}\n\n";
			}
			
			// add faded
			$tempValue = $faded === null ? $value : $value - $faded->{$prefix . 'Faded'};
			$newFaded = $getMax($tempValue);
			if ($faded === null || $newFaded->{$prefix . 'Faded'} > $faded->{$prefix . 'Faded'}) {
				$added = true;
				if ($faded !== null) {
					$value -= $faded->{$prefix . 'Faded'};
					$blob .= "Remove faded QL $faded->ql\n\n";
				}
				$faded = $newFaded;
				$value += $faded->{$prefix . 'Faded'};
				$lowest = $faded->{'lowest' . ucfirst($prefix) . 'Faded'};
				$blob .= "<highlight>Add faded QL $faded->ql<end> ($lowest) - Treatment: {$faded->treatment}, Ability: {$faded->ability}\n\n";
			}
		}
		
		$blob .= "-------------------\n\nEnding $type: $value";
		$blob .= "\n\n<highlight>Inspired by a command written by Lucier of the same name<end>";
		$msg = $this->text->makeBlob("Laddering from $startingValue to $value " . ucfirst(strtolower($type)), $blob);
		
		$sendto->reply($msg);
	}
	
	// premade implant functions
	public function searchByProfession($profession) {
		$sql = "SELECT
				i.Name AS slot,
				p2.Name AS profession,
				a.Name AS ability,
				CASE WHEN c1.ClusterID = 0 THEN 'N/A' ELSE c1.LongName END AS shiny,
				CASE WHEN c2.ClusterID = 0 THEN 'N/A' ELSE c2.LongName END AS bright,
				CASE WHEN c3.ClusterID = 0 THEN 'N/A' ELSE c3.LongName END AS faded
			FROM premade_implant p
			JOIN ImplantType i ON p.ImplantTypeID = i.ImplantTypeID
			JOIN Profession p2 ON p.ProfessionID = p2.ID
			JOIN Ability a ON p.AbilityID = a.AbilityID
			JOIN Cluster c1 ON p.ShinyClusterID = c1.ClusterID
			JOIN Cluster c2 ON p.BrightClusterID = c2.ClusterID
			JOIN Cluster c3 ON p.FadedClusterID = c3.ClusterID
			WHERE p2.Name = ?
			ORDER BY slot";
		return $this->db->query($sql, $profession);
	}

	public function searchBySlot($slot) {
		$sql = "SELECT
				i.Name AS slot,
				p2.Name AS profession,
				a.Name AS ability,
				CASE WHEN c1.ClusterID = 0 THEN 'N/A' ELSE c1.LongName END AS shiny,
				CASE WHEN c2.ClusterID = 0 THEN 'N/A' ELSE c2.LongName END AS bright,
				CASE WHEN c3.ClusterID = 0 THEN 'N/A' ELSE c3.LongName END AS faded
			FROM premade_implant p
			JOIN ImplantType i ON p.ImplantTypeID = i.ImplantTypeID
			JOIN Profession p2 ON p.ProfessionID = p2.ID
			JOIN Ability a ON p.AbilityID = a.AbilityID
			JOIN Cluster c1 ON p.ShinyClusterID = c1.ClusterID
			JOIN Cluster c2 ON p.BrightClusterID = c2.ClusterID
			JOIN Cluster c3 ON p.FadedClusterID = c3.ClusterID
			WHERE i.ShortName = ?
			ORDER BY shiny, bright, faded";
		return $this->db->query($sql, $slot);
	}

	public function searchByModifier($modifier) {
		list($shinyQuery, $shinyParams) = $this->util->generateQueryFromParams(explode(' ', $modifier), 'c1.LongName');
		list($brightQuery, $brightParams) = $this->util->generateQueryFromParams(explode(' ', $modifier), 'c2.LongName');
		list($fadedQuery, $fadedParams) = $this->util->generateQueryFromParams(explode(' ', $modifier), 'c3.LongName');
		
		$params = array_merge($shinyParams, $brightParams, $fadedParams);
		
		$sql = "SELECT
				i.Name AS slot,
				p2.Name AS profession,
				a.Name AS ability,
				CASE WHEN c1.ClusterID = 0 THEN 'N/A' ELSE c1.LongName END AS shiny,
				CASE WHEN c2.ClusterID = 0 THEN 'N/A' ELSE c2.LongName END AS bright,
				CASE WHEN c3.ClusterID = 0 THEN 'N/A' ELSE c3.LongName END AS faded
			FROM premade_implant p
			JOIN ImplantType i ON p.ImplantTypeID = i.ImplantTypeID
			JOIN Profession p2 ON p.ProfessionID = p2.ID
			JOIN Ability a ON p.AbilityID = a.AbilityID
			JOIN Cluster c1 ON p.ShinyClusterID = c1.ClusterID
			JOIN Cluster c2 ON p.BrightClusterID = c2.ClusterID
			JOIN Cluster c3 ON p.FadedClusterID = c3.ClusterID
			WHERE ($shinyQuery) OR ($brightQuery) OR ($fadedQuery)";

		return $this->db->query($sql, $params);
	}

	public function formatResults($implants) {
		$count = 0;
		forEach ($implants as $implant) {
			$blob .= $this->getFormattedLine($implant);
			$count++;
		}

		return $blob;
	}

	public function getFormattedLine($implant) {
		return "<header2>$implant->profession<end> $implant->slot <highlight>$implant->ability<end> $implant->shiny, $implant->bright, $implant->faded\n";
	}

	// implant functions
	public function getRequirements($ql) {
		$sql = "SELECT * FROM implant_requirements WHERE ql = ?";

		$row = $this->db->queryRow($sql, $ql);

		$this->add_info($row);

		return $row;
	}

	public function findMaxImplantQlByReqs($ability, $treatment) {
		$sql = "SELECT * FROM implant_requirements WHERE ability <= ? AND treatment <= ? ORDER BY ql DESC LIMIT 1";

		$row = $this->db->queryRow($sql, $ability, $treatment);

		$this->add_info($row);

		return $row;
	}

	public function formatClusterBonuses(&$obj) {
		$msg .= "You will gain for most skills:\n" .
			"<tab>Shiny    <highlight>$obj->skillShiny<end> ($obj->lowestSkillShiny - $obj->highestSkillShiny)\n" .
			"<tab>Bright    <highlight>$obj->skillBright<end> ($obj->lowestSkillBright - $obj->highestSkillBright)\n" .
			"<tab>Faded   <highlight>$obj->skillFaded<end> ($obj->lowestSkillFaded - $obj->highestSkillFaded)\n" .
			"-----------------------\n" .
			"<tab>Total   $obj->skillTotal\n";

		$msg .= "\n\n";

		$msg .= "You will gain for abilities:\n" .
			"<tab>Shiny    <highlight>$obj->abilityShiny<end> ($obj->lowestAbilityShiny - $obj->highestAbilityShiny)\n" .
			"<tab>Bright    <highlight>$obj->abilityBright<end> ($obj->lowestAbilityBright - $obj->highestAbilityBright)\n" .
			"<tab>Faded   <highlight>$obj->abilityFaded<end> ($obj->lowestAbilityFaded - $obj->highestAbilityFaded)\n" .
			"-----------------------\n" .
			"<tab>Total   $obj->abilityTotal\n";


		if ($obj->ql > 250) {

			$msg .= "\n\nRequires Title Level 6";

		} else if ($obj->ql > 200) {

			$msg .= "\n\nRequires Title Level 5";
		}

		$msg .= "\n\nMinimum QL for clusters:\n" .
			"<tab>Shiny: $obj->minShinyClusterQl\n" .
			"<tab>Bright: $obj->minBrightClusterQl\n" .
			"<tab>Faded: $obj->minFadedClusterQl\n";

		$msg .= "\n\nWritten by Tyrence (RK2)";

		return $msg;
	}

	public function add_info(&$obj) {
		if ($obj === null) {
			return;
		}

		$this->_setHighestAndLowestQls($obj, 'abilityShiny');
		$this->_setHighestAndLowestQls($obj, 'abilityBright');
		$this->_setHighestAndLowestQls($obj, 'abilityFaded');
		$this->_setHighestAndLowestQls($obj, 'skillShiny');
		$this->_setHighestAndLowestQls($obj, 'skillBright');
		$this->_setHighestAndLowestQls($obj, 'skillFaded');

		$obj->abilityTotal = $obj->abilityShiny + $obj->abilityBright + $obj->abilityFaded;
		$obj->skillTotal = $obj->skillShiny + $obj->skillBright + $obj->skillFaded;

		$obj->minShinyClusterQl = $this->getClusterMinQl($obj->ql, 'shiny');
		$obj->minBrightClusterQl = $this->getClusterMinQl($obj->ql, 'bright');
		$obj->minFadedClusterQl = $this->getClusterMinQl($obj->ql, 'faded');

		// if implant QL is 201+, then clusters must be refined and must be QL 201+ also
		if ($obj->ql >= 201) {
			if ($obj->minShinyClusterQl < 201) {
				$obj->minShinyClusterQl = 201;
			}
			if ($obj->minBrightClusterQl < 201) {
				$obj->minBrightClusterQl = 201;
			}
			if ($obj->minFadedClusterQl < 201) {
				$obj->minFadedClusterQl = 201;
			}
		}
	}
	
	public function getClusterMinQl($ql, $grade) {
		if ($grade == 'shiny') {
			return round($ql * 0.86);
		} else if ($grade == 'bright') {
			return round($ql * 0.84);
		} else if ($grade == 'faded') {
			return round($ql * 0.82);
		} else {
			throw new \Exception("Invalid grade: '$grade'.  Must be one of: 'shiny', 'bright', 'faded'");
		}
	}

	public function _setHighestAndLowestQls(&$obj, $var) {
		$varValue = $obj->$var;

		$sql = "SELECT MAX(ql) as max, MIN(ql) as min FROM implant_requirements WHERE $var = ?";
		$row = $this->db->queryRow($sql, $varValue);

		// camel case var name
		$tempNameVar = ucfirst($var);
		$tempHighestName = "highest$tempNameVar";
		$tempLowestName = "lowest$tempNameVar";

		$obj->$tempLowestName = $row->min;
		$obj->$tempHighestName = $row->max;
	}
}

?>
