<?php

/**
 * Authors:
 *  - Tyrence (RK2)
 *
 * @Instance
 */
class GuildManager {
	/** @Inject */
	public $chatBot;
	
	/** @Inject */
	public $db;
	
	/** @Inject */
	public $cacheManager;
	
	/** @Inject */
	public $playerManager;

	public function get_by_id($guild_id, $rk_num = 0, $force_update = false) {
		// if no server number is specified use the one on which the bot is logged in
		if ($rk_num == 0) {
			$rk_num = $this->chatBot->vars["dimension"];
		}
		
		$name = ucfirst(strtolower($name));
		$url = "http://people.anarchy-online.com/org/stats/d/$rk_num/name/$guild_id/basicstats.xml";
		$groupName = "guild_roster";
		$filename = "$guild_id.$rk_num.xml";
		if ($this->chatBot->vars["my_guild_id"] == $guild_id) {
			$maxCacheAge = 21600;
		} else {
			$maxCacheAge = 86400;
		}
		$cb = create_function('$data',
			'if (xml::spliceData($data, "<id>", "</id>") != "") {
				return true;
			} else {
				return false;
			}');

		$cacheResult = $this->cacheManager->lookup($url, $groupName, $filename, $cb, $maxCacheAge);

		// if there is still no valid data available give an error back
		if ($cacheResult->success !== true) {
			return null;
		}
		
		$guild = new stdClass;
		$guild->guild_id = $guild_id;

		// parsing of the memberdata
		$members = xml::splicemultidata($cacheResult->data, "<member>", "</member>");
		$guild->orgname	= xml::spliceData($cacheResult->data, "<name>", "</name>");
		$guild->orgside	= xml::spliceData($cacheResult->data, "<side>", "</side");

		// pre fetch the charids...this speeds things up immensely
		forEach ($members as $xmlmember) {
			$name = xml::splicedata($xmlmember, "<nickname>", "</nickname>");
			if (!isset($this->chatBot->id[$name])) {
				$this->chatBot->send_packet(new AOChatPacket("out", AOCP_CLIENT_LOOKUP, $name));
			}
		}

		forEach ($members as $member) {
			$name = xml::splicedata($member, "<nickname>", "</nickname>");
			$charid = $this->chatBot->get_uid($name);
			if ($charid == null) {
				$charid = 0;
			}

			$guild->members[$name]                 = new stdClass;
			$guild->members[$name]->charid         = $charid;
			$guild->members[$name]->firstname      = xml::spliceData($member, '<firstname>', '</firstname>');
			$guild->members[$name]->name           = xml::spliceData($member, '<nickname>', '</nickname>');
			$guild->members[$name]->lastname       = xml::spliceData($member, '<lastname>', '</lastname>');
			$guild->members[$name]->level          = xml::spliceData($member, '<level>', '</level>');
			$guild->members[$name]->breed          = xml::spliceData($member, '<breed>', '</breed>');
			$guild->members[$name]->gender         = xml::spliceData($member, '<gender>', '</gender>');
			$guild->members[$name]->faction        = $guild->orgside;
			$guild->members[$name]->profession     = xml::spliceData($member, '<profession>', '</profession>');
			$guild->members[$name]->prof_title     = xml::spliceData($member, '<profession_title>', '</profession_title>');
			$guild->members[$name]->ai_rank        = xml::spliceData($member, '<defender_rank>', '</defender_rank>');
			$guild->members[$name]->ai_level       = xml::spliceData($member, '<defender_rank_id>', '</defender_rank_id>');
			$guild->members[$name]->guild_id       = $guild->guild_id;
			$guild->members[$name]->guild          = $guild->orgname;
			$guild->members[$name]->guild_rank     = xml::spliceData($member, '<rank_name>', '</rank_name>');
			$guild->members[$name]->guild_rank_id  = xml::spliceData($member, '<rank>', '</rank>');
			$guild->members[$name]->dimension      = $rk_num;
			$guild->members[$name]->source         = 'org_roster';
		}

		// this is done separately from the loop above to prevent nested transaction errors from occuring
		// when looking up charids for characters
		if ($cacheResult->usedCache === false) {
			$this->db->begin_transaction();

			$sql = "UPDATE players SET guild_id = 0, guild = '' WHERE guild_id = ? AND dimension = ?";
			$this->db->exec($sql, $guild->guild_id, $rk_num);

			forEach ($guild->members as $member) {
				$this->playerManager->update($member);
			}

			$this->db->commit();
		}

		return $guild;
	}
}

?>
