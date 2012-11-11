<?php

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
	public $chatBot;

	public function get_by_name($name, $dimension = 0, $forceUpdate = false) {
		if ($dimension == 0) {
			$dimension = $this->chatBot->vars['dimension'];
		}

		$name = ucfirst(strtolower($name));

		$charid = '';
		if ($dimension == $this->chatBot->vars['dimension']) {
			$charid = $this->chatBot->get_uid($name);
			if ($charid == null) {
				return null;
			}
		}

		$player = $this->findInDb($name, $dimension);

		if ($player === null || $forceUpdate) {
			$player = $this->lookup($name, $dimension);
			if ($player !== null) {
				$player->charid = $charid;
				$this->update($player);
			}
		} else if ($player->last_update < (time() - 86400)) {
			$player2 = $this->lookup($name, $dimension);
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

	public function findInDb($name, $dimension) {
		$sql = "SELECT * FROM players WHERE name LIKE ? AND dimension = ?";
		return $this->db->queryRow($sql, $name, $dimension);
	}

	public function lookup($name, $dimension) {
		$xml = $this->lookup_url("http://people.anarchy-online.com/character/bio/d/$dimension/name/$name/bio.xml");
		if ($xml->name == $name) {
			$xml->source = 'people.anarchy-online.com';
			$xml->dimension = $dimension;

			return $xml;
		}

		// if people.anarchy-online.com was too slow to respond or returned invalid data then try to update from auno.org
		$xml = $this->lookup_url("http://auno.org/ao/char.php?output=xml&dimension=$dimension&name=$name");
		if ($xml->name == $name) {
			$xml->source = 'auno.org';
			$xml->dimension = $dimension;

			return $xml;
		}

		return null;
	}

	private function lookup_url($url) {
		$playerbio = xml::getUrl($url);

		$xml = new stdClass;

		// parsing of the player data
		$xml->firstname      = xml::spliceData($playerbio, '<firstname>', '</firstname>');
		$xml->name           = xml::spliceData($playerbio, '<nick>', '</nick>');
		$xml->lastname       = xml::spliceData($playerbio, '<lastname>', '</lastname>');
		$xml->level          = xml::spliceData($playerbio, '<level>', '</level>');
		$xml->breed          = xml::spliceData($playerbio, '<breed>', '</breed>');
		$xml->gender         = xml::spliceData($playerbio, '<gender>', '</gender>');
		$xml->faction        = xml::spliceData($playerbio, '<faction>', '</faction>');
		$xml->profession     = xml::spliceData($playerbio, '<profession>', '</profession>');
		$xml->prof_title     = xml::spliceData($playerbio, '<profession_title>', '</profession_title>');
		$xml->ai_rank        = xml::spliceData($playerbio, '<defender_rank>', '</defender_rank>');
		$xml->ai_level       = xml::spliceData($playerbio, '<defender_rank_id>', '</defender_rank_id>');
		$xml->guild_id       = xml::spliceData($playerbio, '<organization_id>', '</organization_id>');
		$xml->guild          = xml::spliceData($playerbio, '<organization_name>', '</organization_name>');
		$xml->guild_rank     = xml::spliceData($playerbio, '<rank>', '</rank>');
		$xml->guild_rank_id  = xml::spliceData($playerbio, '<rank_id>', '</rank_id>');

		return $xml;
	}

	public function update(&$char) {
		$sql = "DELETE FROM players WHERE `name` = ? AND `dimension` = ?";
		$this->db->exec($sql, $char->name, $char->dimension);
		
		if (empty($char->guild_id)) {
			$char->guild_id = 0;
		}
		
		if ($char->guild_rank_id == '') {
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
				?
			)";

		$this->db->exec($sql, $char->charid, $char->firstname, $char->name, $char->lastname, $char->level, $char->breed, $char->gender, $char->faction,
			$char->profession, $char->prof_title, $char->ai_rank, $char->ai_level, $char->guild_id, $char->guild, $char->guild_rank, $char->guild_rank_id,
			$char->dimension, $char->source, time());
	}

	public function get_info(&$whois) {
		$msg = '';

		if ($whois->firstname) {
            $msg = $whois->firstname." ";
		}

        $msg .= "<highlight>\"{$whois->name}\"<end> ";

        if ($whois->lastname) {
            $msg .= $whois->lastname." ";
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
}

?>
