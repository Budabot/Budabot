<?php

/*
`module` VARCHAR(50) NOT NULL
`type` VARCHAR(18)
`file` VARCHAR(255)
`is_core` TINYINT NOT NULL
`description` VARCHAR(50) NOT NULL DEFAULT ''
`verify` INT DEFAULT 0
`status` INT DEFAULT 1
*/

class Event {

	/**
	 * @name: register
	 * @description: Registers an event on the bot so it can be configured
	 */
	public static function register($module, $type, $filename, $dependson = 'none', $description = 'none') {
		$db = DB::get_instance();
		global $chatBot;
		
		// disable depends on
		$description = str_replace("'", "''", $description);
		
		$actual_filename = $module . '/' . $filename;
		
		Logger::log('debug', 'Core', "Adding Event to list:($type) File:($filename)");

		if ($chatBot->existing_events[$type][$actual_filename] == true) {
		  	$db->exec("UPDATE cmdcfg_<myname> SET `verify` = 1, `description` = '$description' WHERE `type` = '$type' AND `cmdevent` = 'event' AND `file` = '$actual_filename' AND `module` = '$module'");
		} else {
			if ($chatBot->settings["default_module_status"] == 1) {
				$status = 1;
			} else {
				$status = 0;
			}
			$db->exec("INSERT INTO cmdcfg_<myname> (`module`, `cmdevent`, `type`, `file`, `verify`, `description`, `status`) VALUES ('$module', 'event', '$type', '$actual_filename', '1', '$description', '$status')");
		}
	}

	/**
	 * @name: activate
	 * @description: Activates an event
	 */
	public static function activate($type, $filename) {
		global $chatBot;
		$db = DB::get_instance();
		
		Logger::log('debug', 'Core', "Activating Event:($type) File:($filename)");

		//Check if the file exists
		$actual_filename = $chatBot->verifyFilename($filename);
		if ($actual_filename == '') {
			Logger::log('error', 'Core', "Error in registering File '{$filename}' for Event type {$type}. The file doesn't exist!");
			return;
		}

		switch ($type){
			case "towers":
				if (!in_array($actual_filename, $chatBot->towers))
					$chatBot->towers[] = $actual_filename;
				break;
			case "orgmsg":
				if (!in_array($actual_filename, $chatBot->orgmsg))
					$chatBot->orgmsg[] = $actual_filename;
				break;
			case "msg":
				if (!in_array($actual_filename, $chatBot->privMsgs))
					$chatBot->privMsgs[] = $actual_filename;
				break;
			case "priv":
				if (!in_array($actual_filename, $chatBot->privChat))
					$chatBot->privChat[] = $actual_filename;
				break;
			case "extPriv":
				if (!in_array($actual_filename, $chatBot->extPrivChat))
					$chatBot->extPrivChat[] = $actual_filename;
				break;
			case "guild":
				if (!in_array($actual_filename, $chatBot->guildChat))
					$chatBot->guildChat[] = $actual_filename;
				break;
			case "joinPriv":
				if (!in_array($actual_filename, $chatBot->joinPriv))
					$chatBot->joinPriv[] = $actual_filename;
				break;
			case "extJoinPriv":
				if (!in_array($actual_filename, $chatBot->extJoinPriv))
					$chatBot->extJoinPriv[] = $actual_filename;
				break;
			case "leavePriv":
				if (!in_array($actual_filename, leavePriv))
					$chatBot->leavePriv[] = $actual_filename;
				break;
			case "extLeavePriv":
				if (!in_array($actual_filename, extLeavePriv))
					$chatBot->extLeavePriv[] = $actual_filename;
				break;
			case "extJoinPrivRequest":
				if (!in_array($actual_filename, $chatBot->extJoinPrivRequest))
					$chatBot->extJoinPrivRequest[] = $actual_filename;
				break;
			case "extKickPriv":
				if (!in_array($actual_filename, $chatBot->extKickPriv))
					$chatBot->extKickPriv[] = $actual_filename;
				break;
			case "logOn":
				if (!in_array($actual_filename, $chatBot->logOn))
					$chatBot->logOn[] = $actual_filename;
				break;
			case "logOff":
				if (!in_array($actual_filename, $chatBot->logOff))
					$chatBot->logOff[] = $actual_filename;
				break;
			case "2sec":
				if (!in_array($actual_filename, $chatBot->_2sec))
					$chatBot->_2sec[] = $actual_filename;
				break;
			case "1min":
				if (!in_array($actual_filename, $chatBot->_1min))
					$chatBot->_1min[] = $actual_filename;
				break;
			case "10mins":
				if (!in_array($actual_filename, $chatBot->_10mins))
					$chatBot->_10mins[] = $actual_filename;
				break;
			case "15mins":
				if (!in_array($actual_filename, $chatBot->_15mins))
					$chatBot->_15mins[] = $actual_filename;
				break;
			case "30mins":
				if (!in_array($actual_filename, $chatBot->_30mins))
					$chatBot->_30mins[] = $actual_filename;
				break;
			case "1hour":
				if (!in_array($actual_filename, $chatBot->_1hour))
					$chatBot->_1hour[] = $actual_filename;
				break;
			case "24hrs":
				if (!in_array($actual_filename, $chatBot->_24hrs))
					$chatBot->_24hrs[] = $actual_filename;
				break;
			case "connect":
				if (!in_array($actual_filename, $chatBot->_connect))
					$chatBot->_connect[] = $actual_filename;
				break;
			case "shopping":
				if (!in_array($actual_filename, $chatBot->shopping))
					$chatBot->shopping[] = $actual_filename;
				break;
			case "sendGuild":
				if (!in_array($actual_filename, $chatBot->sendGuild))
					$chatBot->sendGuild[] = $actual_filename;
				break;
			case "sendPriv":
				if (!in_array($actual_filename, $chatBot->sendPriv))
					$chatBot->sendPriv[] = $actual_filename;
				break;
			case "setup":
				include $actual_filename;
				break;
		}
	}

	/**
	 * @name: deactivate
	 * @description: Deactivates an event
	 */
	public static function deactivate($type, $filename) {
		global $chatBot;

		$type = strtolower($type);

		Logger::log('debug', 'Core', "Deactivating Event:($type) File:($filename)");

		//Check if the file exists
		$actual_filename = $chatBot->verifyFilename($filename);
		if ($actual_filename == '') {
			Logger::log('ERROR', 'Core', "Error in deactivating the File $filename for Event $type. The file doesn't exist!");
		}

		switch ($type){
			case "towers":
				if(in_array($actual_filename, $chatBot->towers)) {
					$temp = array_flip($chatBot->towers);
					unset($chatBot->towers[$temp[$actual_filename]]);
				}
				break;
			case "orgmsg":
				if(in_array($actual_filename, $chatBot->orgmsg)) {
					$temp = array_flip($chatBot->orgmsg);
					unset($chatBot->orgmsg[$temp[$actual_filename]]);
				}
				break;
			case "msg":
				if(in_array($actual_filename, $chatBot->privMsgs)) {
					$temp = array_flip($chatBot->privMsgs);
					unset($chatBot->privMsgs[$temp[$actual_filename]]);
				}
				break;
			case "priv":
				if(in_array($actual_filename, $chatBot->privChat)) {
					$temp = array_flip($chatBot->privChat);
					unset($chatBot->privChat[$temp[$actual_filename]]);
				}
				break;
			case "extPriv":
				if(in_array($actual_filename, $chatBot->extPrivChat)) {
					$temp = array_flip($chatBot->extPrivChat);
					unset($chatBot->extPrivChat[$temp[$actual_filename]]);
				}
				break;
			case "guild":
				if(in_array($actual_filename, $chatBot->guildChat)) {
					$temp = array_flip($chatBot->guildChat);
					unset($chatBot->guildChat[$temp[$actual_filename]]);
				}
				break;
			case "joinPriv":
				if(in_array($actual_filename, $chatBot->joinPriv)) {
					$temp = array_flip($chatBot->joinPriv);
					unset($chatBot->joinPriv[$temp[$actual_filename]]);
				}
				break;
			case "extJoinPriv":
				if(in_array($actual_filename, $chatBot->extJoinPriv)) {
					$temp = array_flip($chatBot->extJoinPriv);
					unset($chatBot->extJoinPriv[$temp[$actual_filename]]);
				}
				break;
			case "leavePriv":
				if(in_array($actual_filename, $chatBot->leavePriv)) {
					$temp = array_flip($chatBot->leavePriv);
					unset($chatBot->leavePriv[$temp[$actual_filename]]);
				}
				break;
			case "extLeavePriv":
				if(in_array($actual_filename, $chatBot->extLeavePriv)) {
					$temp = array_flip($chatBot->extLeavePriv);
					unset($chatBot->extLeavePriv[$temp[$actual_filename]]);
				}
				break;
			case "extJoinPrivRequest":
				if(in_array($actual_filename, $chatBot->extJoinPrivRequest)) {
					$temp = array_flip($chatBot->extJoinPrivRequest);
					unset($chatBot->extJoinPrivRequest[$temp[$actual_filename]]);
				}
				break;
			case "extKickPriv":
				if(in_array($actual_filename, $chatBot->extKickPriv)) {
					$temp = array_flip($chatBot->extKickPriv);
					unset($chatBot->extKickPriv[$temp[$actual_filename]]);
				}
				break;
			case "logOn":
				if(in_array($actual_filename, $chatBot->logOn)) {
					$temp = array_flip($chatBot->logOn);
					unset($chatBot->logOn[$temp[$actual_filename]]);
				}
				break;
			case "logOff":
				if(in_array($actual_filename, $chatBot->logOff)) {
					$temp = array_flip($chatBot->logOff);
					unset($chatBot->logOff[$temp[$actual_filename]]);
				}
				break;
			case "2sec":
				if(in_array($actual_filename, $chatBot->_2sec)) {
					$temp = array_flip($chatBot->_2sec);
					unset($chatBot->_2sec[$temp[$actual_filename]]);
				}
				break;
			case "1min":
				if(in_array($actual_filename, $chatBot->_1min)) {
					$temp = array_flip($chatBot->_1min);
					unset($chatBot->_1min[$temp[$actual_filename]]);
				}
				break;
			case "10mins":
				if(in_array($actual_filename, $chatBot->_10mins)) {
					$temp = array_flip($chatBot->_10mins);
					unset($chatBot->_10mins[$temp[$actual_filename]]);
				}
				break;
			case "15mins":
				if(in_array($actual_filename, $chatBot->_15mins)) {
					$temp = array_flip($chatBot->_15mins);
					unset($chatBot->_15mins[$temp[$actual_filename]]);
				}
				break;
			case "30mins":
				if(in_array($actual_filename, $chatBot->_30mins)) {
					$temp = array_flip($chatBot->_30mins);
					unset($chatBot->_30mins[$temp[$actual_filename]]);
				}
				break;
			case "1hour":
				if(in_array($actual_filename, $chatBot->_1hour)) {
					$temp = array_flip($chatBot->_1hour);
					unset($chatBot->_1hour[$temp[$actual_filename]]);
				}
				break;
			case "24hrs":
				if(in_array($actual_filename, $chatBot->_24hrs)) {
					$temp = array_flip($chatBot->_24hrs);
					unset($chatBot->_24hrs[$temp[$actual_filename]]);
				}
				break;
			case "connect":
				if(in_array($actual_filename, $chatBot->_connect)) {
					$temp = array_flip($chatBot->_connect);
					unset($chatBot->_connect[$temp[$actual_filename]]);
				}
				break;
			case "shopping":
				if(in_array($actual_filename, $chatBot->shopping)) {
					$temp = array_flip($chatBot->shopping);
					unset($chatBot->shopping[$temp[$actual_filename]]);
				}
				break;
			case "sendGuild":
				if(in_array($actual_filename, $chatBot->sendGuild)) {
					$temp = array_flip($chatBot->sendGuild);
					unset($chatBot->sendGuild[$temp[$actual_filename]]);
				}
				break;
			case "sendPriv":
				if(in_array($actual_filename, $chatBot->sendPriv)) {
					$temp = array_flip($chatBot->sendPriv);
					unset($chatBot->sendPriv[$temp[$actual_filename]]);
				}
				break;
		}
	}
}

?>