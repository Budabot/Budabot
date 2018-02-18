<?php

namespace Budabot\Core;

use stdClass;

/**
 * Authors:
 *  - Tyrence (RK2)
 *
 * @Instance
 */
class PlayerManager {
	/** @Inject */
	public $db;
	
	/** @Inject */
	public $util;
	
	/** @Inject */
	public $chatBot;
	
	/** @Inject */
	public $http;

	public function getByName($name, $rk_num = 0, $forceUpdate = false) {
		if ($rk_num == 0) {
			$rk_num = $this->chatBot->vars['dimension'];
		}

		$name = ucfirst(strtolower($name));

		$charid = '';
		if ($rk_num == $this->chatBot->vars['dimension']) {
			$charid = $this->chatBot->get_uid($name);
			if ($charid == null) {
				return null;
			}
		}

		$player = $this->findInDb($name, $rk_num);

		if ($player === null || $forceUpdate) {
			$player = $this->lookup($name, $rk_num);
			if ($player !== null) {
				$player->charid = $charid;
				$this->update($player);
			}
		} else if ($player->last_update < (time() - 86400)) {
			$player2 = $this->lookup($name, $rk_num);
			if ($player2 !== null) {
				$player = $player2;
				$player->charid = $charid;
				$this->update($player);
			} else {
				$player->source .= ' (old-cache)';
			}
		} else {
			$player->source .= ' (current-cache)';
		}

		return $player;
	}

	public function findInDb($name, $rk_num) {
		$sql = "SELECT * FROM players WHERE name LIKE ? AND dimension = ? LIMIT 1";
		return $this->db->queryRow($sql, $name, $rk_num);
	}

	public function lookup($name, $rk_num) {
		$obj = $this->lookupUrl("http://people.anarchy-online.com/character/bio/d/$rk_num/name/$name/bio.xml?data_type=json");
		if ($obj->name == $name) {
			$obj->source = 'people.anarchy-online.com';
			$obj->dimension = $rk_num;
			return $obj;
		}

		return null;
	}

	private function lookupUrl($url) {
		$response = $this->http->get($url)->waitAndReturnResponse();
		list($char, $org, $lastUpdated) = json_decode($response->body);

		$obj = new stdClass;

		// parsing of the player data
		$obj->firstname      = trim($char->FIRSTNAME);
		$obj->name           = $char->NAME;
		$obj->lastname       = trim($char->LASTNAME);
		$obj->level          = $char->LEVELX;
		$obj->breed          = $char->BREED;
		$obj->gender         = $char->SEX;
		$obj->faction        = $char->SIDE;
		$obj->profession     = $char->PROF;
		$obj->prof_title     = $char->PROFNAME;
		$obj->ai_rank        = $char->RANK_name;
		$obj->ai_level       = $char->ALIENLEVEL;
		$obj->guild_id       = $org->ORG_INSTANCE;
		$obj->guild          = $org->NAME;
		$obj->guild_rank     = $org->RANK_TITLE;
		$obj->guild_rank_id  = $org->RANK;

		$obj->head_id        = $char->HEADID;
		$obj->pvp_rating     = $char->PVPRATING;
		$obj->pvp_title      = $char->PVPTITLE;

		//$obj->charid        = $char->CHAR_INSTANCE;
		$obj->dimension      = $char->CHAR_DIMENSION;

		forEach ($obj as $key => $value) {
			if (is_null($value)) {
				$obj->$key = "";
			}
		}

		return $obj;
	}

	public function update($char) {
		$sql = "DELETE FROM players WHERE `name` = ? AND `dimension` = ?";
		$this->db->exec($sql, $char->name, $char->dimension);
		
		if (empty($char->guild_id)) {
			$char->guild_id = 0;
		}
		
		if ($char->guild_rank_id === '') {
			$char->guild_rank_id = -1;
		}

		$sql = "
			INSERT INTO players (
				`charid`,
				`firstname`,
				`name`,
				`lastname`,
				`level`,
				`breed`,
				`gender`,
				`faction`,
				`profession`,
				`prof_title`,
				`ai_rank`,
				`ai_level`,
				`guild_id`,
				`guild`,
				`guild_rank`,
				`guild_rank_id`,
				`dimension`,
				`head_id`,
				`pvp_rating`,
				`pvp_title`,
				`source`,
				`last_update`
			) VALUES (
				?,
				?,
				?,
				?,
				?,
				?,
				?,
				?,
				?,
				?,
				?,
				?,
				?,
				?,
				?,
				?,
				?,
				?,
				?,
				?,
				?,
				?
			)";

		$this->db->exec($sql, $char->charid, $char->firstname, $char->name, $char->lastname, $char->level, $char->breed, $char->gender, $char->faction,
			$char->profession, $char->prof_title, $char->ai_rank, $char->ai_level, $char->guild_id, $char->guild, $char->guild_rank, $char->guild_rank_id,
			$char->dimension, $char->head_id, $char->pvp_rating, $char->pvp_title, $char->source, time());
	}

	public function getInfo($whois, $showFirstAndLastName = true) {
		$msg = '';

		if ($showFirstAndLastName && $whois->firstname) {
			$msg = $whois->firstname . " ";
		}

		$msg .= "<highlight>\"{$whois->name}\"<end> ";

		if ($showFirstAndLastName && $whois->lastname) {
			$msg .= $whois->lastname . " ";
		}

		$msg .= "(<highlight>{$whois->level}<end>/<green>{$whois->ai_level}<end>";
		$msg .= ", {$whois->gender} {$whois->breed} <highlight>{$whois->profession}<end>";
		$msg .= ", <" . strtolower($whois->faction) . ">$whois->faction<end>";

		if ($whois->guild) {
			$msg .= ", {$whois->guild_rank} of <highlight>{$whois->guild}<end>)";
		} else {
			$msg .= ", Not in a guild)";
		}

		return $msg;
	}
	
	public function searchForPlayers($search, $rkNum = null) {
		$searchTerms = explode(' ', $search);
		list($query, $params) = $this->util->generateQueryFromParams($searchTerms, 'name');

		if ($rkNum == null) {
			$sql = "SELECT * FROM players WHERE $query ORDER BY name ASC LIMIT 100";
			return $this->db->query($sql, $params);
		} else {
			$sql = "SELECT * FROM players WHERE $query AND dimension = ? ORDER BY name ASC LIMIT 100";
			$params []= $rkNum;

			return $this->db->query($sql, $params);
		}
	}
}
