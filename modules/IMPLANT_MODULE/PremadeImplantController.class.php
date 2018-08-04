<?php

namespace Budabot\User\Modules;

use Exception;

/**
 * Authors: 
 *  - Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this class contains:
 *	@DefineCommand(
 *		command     = 'premade',
 *		accessLevel = 'all',
 *		description = 'Searches for implants out of the premade implants booths',
 *		help        = 'premade.txt'
 *	)
 */
class PremadeImplantController {

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
	
	private $slots = array('head', 'eye', 'ear', 'rarm', 'chest', 'larm', 'rwrist', 'waist', 'lwrist', 'rhand', 'legs', 'lhand', 'feet');
	
	/**
	 * @Setup
	 */
	public function setup() {
		$this->db->loadSQLFile($this->moduleName, "premade_implant");
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
		forEach ($implants as $implant) {
			$blob .= $this->getFormattedLine($implant);
		}

		return $blob;
	}

	public function getFormattedLine($implant) {
		return "<header2>$implant->profession<end> $implant->slot <highlight>$implant->ability<end> $implant->shiny, $implant->bright, $implant->faded\n";
	}
}
