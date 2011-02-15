<?php

class Guild {
    public $members = array();
    public $errorCode = 0;
    public $errorInfo;

    //the organisation lookup function
	public static function get_by_id($guild_id, $rk_num = 0, $force_update = false) {
		global $chatBot;

		$data_found = false;
		$data_save = false;
	
		//if no server number is specified use the one on which the bot is logged in
		if ($rk_num == 0) {
			$rk_num = $chatBot->vars["dimension"];
		}

		$cache = $chatBot->vars["cachefolder"];

		//Making sure that the cache folder exists
        if (!dir($cache)) {
	        @mkdir($cache, 0777, true);
		}
		
		//Check if a xml file of the person exists and if it is uptodate
		if (!force_update && file_exists("$cache/$guild_id.$rk_num.xml")) {
	        $mins = (time() - filemtime("$cache/$guild_id.$rk_num.xml")) / 60;
            $hours = floor($mins/60);
            //if the file is not older then 24hrs and it is not the roster of the bot guild then use the cache one, when it the xml file from the org bot guild and not older then 6hrs use it
            if (($hours < 24 && $chatBot->vars["my guild id"] != $guild_id) || ($hours < 6 && $chatBot->vars["my guild id"] == $guild_id)) {
             	$orgxml = file_get_contents("$cache/$guild_id.$rk_num.xml");
				if (xml::spliceData($orgxml, '<id>', '</id>') == $guild_id) {
					$data_found = true;
				} else {
					$data_found = false;
					unset($orgxml);
					@unlink("$cache/$guild_id.$rk_num.xml");
				}
			}
        }

        //If no file was found or it is outdated try to update it from anarchyonline.com
        if (!$data_found) {
			$orgxml = xml::getUrl("http://people.anarchy-online.com/org/stats/d/$rk_num/name/$guild_id/basicstats.xml", 30);
			if (xml::spliceData($orgxml, '<id>', '</id>') == $guild_id) {
				$data_found = true;
				$data_save = true;
			} else {
				$data_found = false;
				unset($orgxml);
			}
		}
		
		//If the site was not responding or the data was invalid and a xml file exists get that one
		if (!$data_found && file_exists("$cache/$guild_id.$rk_num.xml")) {
			$orgxml = file_get_contents("$cache/$guild_id.$rk_num.xml");
			if (xml::spliceData($orgxml, '<id>', '</id>') == $name) {
				$data_found = true;
			} else {
				$data_found = false;
				unset($orgxml);
				@unlink("$cache/$guild_id.$rk_num.xml");
			}
		}
		
		$guild = new Guild();
		$guild->guild_id = $guild_id;
		
		//if there is still no valid data available give an error back
		if (!$data_found) {
           	return null;
		}

		//parsing of the memberdata
		$members = xml::splicemultidata($orgxml, "<member>", "</member>");
        $guild->orgname	= xml::spliceData($orgxml, "<name>", "</name>");
        $guild->orgside	= xml::spliceData($orgxml, "<side>", "</side");
		
		// pre fetch the charids...this speeds things up immensely
		forEach ($members as $xmlmember) {
			$name = xml::splicedata($xmlmember, "<nickname>", "</nickname>");
			if (!isset($chatBot->id[$name])) {
				$chatBot->send_packet(new AOChatPacket("out", AOCP_CLIENT_LOOKUP, $name));
			}
		}

        forEach ($members as $member) {
			$name                                  = xml::splicedata($member, "<nickname>", "</nickname>");
			$charid = $chatBot->get_uid($name);
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
		if ($data_save) {
			$db = DB::get_instance();
			$db->beginTransaction();
			
			forEach ($guild->members as $member) {
				Player::update($member);
			}

			$db->Commit();
		}

		// if a new xml file was downloaded, save it
		if ($data_save) {
	        $fp = fopen("$cache/$guild_id.$rk_num.xml", "w");
	        fwrite($fp, $orgxml);
	        fclose($fp);
	    }
		
		return $guild;
	}
}

?>