<?php

class Player {
	public static function get_by_name($name, $forceUpdate = false) {
		$db = DB::get_instance();
		global $vars;
		
		$name = ucfirst(strtolower($name));
		
		if ($dimension === null) {
			$dimension = $vars['dimension'];
		}
	
		$sql = "SELECT * FROM players WHERE name LIKE '$name'";
		$db->query($sql);
		$player = $db->fObject();

		if ($player === null || $forceUpdate) {
			$player = Player::lookup($name, $dimension);
			if ($player !== null) {
				Player::update($player);
			}
		} else if ($player->last_update < (time() - 86400)) {
			$player2 = Player::lookup($name, $dimension);
			if ($player2 !== null) {
				$player = $player2;
				Player::update($player);
			} else {
				$player->source .= ' (old-cache)';
			}
		} else {
			$player->source .= ' (current-cache)';
		}
		
		return $player;
	}
	
	public static function add_info($name) {
		global $vars;
	
		$char = Player::get_by_name($name);
		if ($char === null) {
			$charid = $chatBot->get_uid($name);
			if ($charid == null) {
				return null;
			}
			
			$char = new stdClass;
			$char->name = $name;
			$char->charid = $charid;
			$char->dimension = $vars['dimension'];
			$char->source = 'placeholder';
			Player::update($char);
		}			
		return $char;
	}
	
	public static function lookup($name, $dimension) {
		global $chatBot;

		$charid = $chatBot->get_uid($name);
		if ($charid == null) {
			return null;
		}

		$xml = Player::lookup_url("http://people.anarchy-online.com/character/bio/d/$dimension/name/$name/bio.xml");
		if ($xml->name == $name) {
			$xml->source = 'people.anarchy-online.com';
			$xml->dimension = $dimension;
			$xml->charid = $charid;

			return $xml;
		}
		
		// if people.anarchy-online.com was too slow to respond or returned invalid data then try to update from auno.org
		$xml = Player::lookup_url("http://auno.org/ao/char.php?output=xml&dimension=$dimension&name=$name");
		if ($xml->name == $name) {
			$xml->source = 'auno.org';
			$xml->dimension = $dimension;
			$xml->charid = $charid;

			return $xml;
		}
		
		return null;
	}
	
	private static function lookup_url($url) {
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
	
	public static function update(&$char) {
		$db = DB::get_instance();
		
		$sql = "DELETE FROM players WHERE `name` = '{$char->name}'";
		$db->exec($sql);

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
				'{$char->charid}',
				'{$char->firstname}',
				'{$char->name}',
				'{$char->lastname}',
				'{$char->level}',
				'{$char->breed}',
				'{$char->gender}',
				'{$char->faction}',
				'{$char->profession}',
				'{$char->prof_title}',
				'{$char->ai_rank}',
				'{$char->ai_level}',
				'{$char->guild_id}',
				'" . str_replace("'", "''", $char->guild) . "',
				'{$char->guild_rank}',
				'{$char->guild_rank_id}',
				'{$char->dimension}',
				'{$char->source}',
				'" . time() . "'
			)";
		
		$db->exec($sql);
	}
	
	public static function get_info(&$whois) {
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