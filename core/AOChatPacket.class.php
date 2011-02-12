<?php

/**
 * The AOChatPacket class - turning packets into binary blobs and
 * binary blobs into packets.
 *
 * Data types:
 * I - 32 bit integer: uint32_t
 * S - 8 bit string array: uint16_t length, char str[length]
 * G - 40 bit binary data: unsigned char data[5]
 * i - integer array: uint16_t count, uint32_t[count]
 * s - string array: uint16_t count, aochat_str_t[count]
 *
 * D - 'data', we have relabeled all 'D' type fields to 'S'
 * M - mapping [see t.class in ao_nosign.jar] - unsupported
 *
 */

class AOChatPacket {
	private static $packet_map = array(
		"in" => array(
			AOCP_LOGIN_SEED       => array("name"=>"Login Seed",                  "args"=>"S"),
			AOCP_LOGIN_OK         => array("name"=>"Login Result OK",             "args"=>""),
			AOCP_LOGIN_ERROR      => array("name"=>"Login Result Error",          "args"=>"S"),
			AOCP_LOGIN_CHARLIST   => array("name"=>"Login CharacterList",         "args"=>"isii"),
			AOCP_CLIENT_UNKNOWN   => array("name"=>"Client Unknown",              "args"=>"I"),
			AOCP_CLIENT_NAME      => array("name"=>"Client Name",                 "args"=>"IS"),
			AOCP_CLIENT_LOOKUP    => array("name"=>"Lookup Result",               "args"=>"IS"),
			AOCP_MSG_PRIVATE      => array("name"=>"Message Private",             "args"=>"ISS"),
			AOCP_MSG_VICINITY     => array("name"=>"Message Vicinity",            "args"=>"ISS"),
			AOCP_MSG_VICINITYA    => array("name"=>"Message Anon Vicinity",       "args"=>"SSS"),
			AOCP_MSG_SYSTEM       => array("name"=>"Message System",              "args"=>"S"),
			AOCP_CHAT_NOTICE      => array("name"=>"Chat Notice",                 "args"=>"IIIS"),
			AOCP_BUDDY_ADD        => array("name"=>"Buddy Added",                 "args"=>"IIS"),
			AOCP_BUDDY_REMOVE     => array("name"=>"Buddy Removed",               "args"=>"I"),
			AOCP_PRIVGRP_INVITE   => array("name"=>"Privategroup Invited",        "args"=>"I"),
			AOCP_PRIVGRP_KICK     => array("name"=>"Privategroup Kicked",         "args"=>"I"),
			AOCP_PRIVGRP_PART     => array("name"=>"Privategroup Part",           "args"=>"I"),
			AOCP_PRIVGRP_CLIJOIN  => array("name"=>"Privategroup Client Join",    "args"=>"II"),
			AOCP_PRIVGRP_CLIPART  => array("name"=>"Privategroup Client Part",    "args"=>"II"),
			AOCP_PRIVGRP_MESSAGE  => array("name"=>"Privategroup Message",        "args"=>"IISS"),
			AOCP_PRIVGRP_REFUSE   => array("name"=>"Privategroup Refuse Invite",  "args"=>"II"),
			AOCP_GROUP_ANNOUNCE   => array("name"=>"Group Announce",              "args"=>"GSIS"),
			AOCP_GROUP_PART       => array("name"=>"Group Part",                  "args"=>"G"),
			AOCP_GROUP_MESSAGE    => array("name"=>"Group Message",               "args"=>"GISS"),
			AOCP_PING             => array("name"=>"Pong",                        "args"=>"S"),
			AOCP_FORWARD          => array("name"=>"Forward",                     "args"=>"IM"),
			AOCP_ADM_MUX_INFO     => array("name"=>"Adm Mux Info",                "args"=>"iii"),
		),
		"out" => array(
			AOCP_LOGIN_REQUEST    => array("name"=>"Login Response GetCharLst",   "args"=>"ISS"),
			AOCP_LOGIN_SELECT     => array("name"=>"Login Select Character",      "args"=>"I"),
			AOCP_CLIENT_LOOKUP    => array("name"=>"Name Lookup",                 "args"=>"S"),
			AOCP_MSG_PRIVATE      => array("name"=>"Message Private",             "args"=>"ISS"),
			AOCP_BUDDY_ADD        => array("name"=>"Buddy Add",                   "args"=>"IS"),
			AOCP_BUDDY_REMOVE     => array("name"=>"Buddy Remove",                "args"=>"I"),
			AOCP_ONLINE_SET       => array("name"=>"Onlinestatus Set",            "args"=>"I"),
			AOCP_PRIVGRP_INVITE   => array("name"=>"Privategroup Invite",         "args"=>"I"),
			AOCP_PRIVGRP_KICK     => array("name"=>"Privategroup Kick",           "args"=>"I"),
			AOCP_PRIVGRP_JOIN     => array("name"=>"Privategroup Join",           "args"=>"I"),
			AOCP_PRIVGRP_PART     => array("name"=>"Privategroup Part",           "args"=>"I"),
			AOCP_PRIVGRP_KICKALL  => array("name"=>"Privategroup Kickall",        "args"=>""),
			AOCP_PRIVGRP_MESSAGE  => array("name"=>"Privategroup Message",        "args"=>"ISS"),
			AOCP_GROUP_DATA_SET   => array("name"=>"Group Data Set",              "args"=>"GIS"),
			AOCP_GROUP_MESSAGE    => array("name"=>"Group Message",               "args"=>"GSS"),
			AOCP_GROUP_CM_SET     => array("name"=>"Group Clientmode Set",        "args"=>"GIIII"),
			AOCP_CLIENTMODE_GET   => array("name"=>"Clientmode Get",              "args"=>"IG"),
			AOCP_CLIENTMODE_SET   => array("name"=>"Clientmode Set",              "args"=>"IIII"),
			AOCP_PING             => array("name"=>"Ping",                        "args"=>"S"),
			AOCP_CC               => array("name"=>"CC",                          "args"=>"s"),
		)
	);

	function __construct($dir, $type, $data) {
		$this->args = array();
		$this->type = $type;
		$this->dir  = $dir;
		$pmap = self::$packet_map[$dir][$type];

		if (!$pmap) {
			echo "Unsupported packet type (". $dir . ", " . $type . ")\n";
			return false;
		}

		if ($dir == "in") {
			if (!is_string($data)) {
				echo "Incorrect argument for incoming packet, expecting a string.\n";
				return false;
			}

			for ($i = 0; $i < strlen($pmap["args"]); $i++) {
				$sa = $pmap["args"][$i];
				switch ($sa) {
					case "I" :
						$res  = array_pop(unpack("N", $data));
						$data = substr($data, 4);
						break;

					case "S" :
						$len  = array_pop(unpack("n", $data));
						$res  = substr($data, 2, $len);
						$data = substr($data, 2 + $len);
						break;

					case "G" :
						$res  = substr($data, 0, 5);
						$data = substr($data, 5);
						break;

					case "i" :
						$len  = array_pop(unpack("n", $data));
						$res  = array_values(unpack("N" . $len, substr($data, 2)));
						$data = substr($data, 2 + 4 * $len);
						break;

					case "s" :
						$len  = array_pop(unpack("n", $data));
						$data = substr($data, 2);
						$res  = array();
						while ($len--) {
							$slen  = array_pop(unpack("n", $data));
							$res[] = substr($data, 2, $slen);
							$data  = substr($data, 2+$slen);
						}
						break;

						default :
						echo "Unknown argument type! (" . $sa . ")\n";
						continue(2);
				}
				$this->args[] = $res;
			}
		} else {
			if (!is_array($data)) {
				$args = array($data);
			} else {
				$args = $data;
			}
			$data = "";

			for ($i = 0; $i < strlen($pmap["args"]); $i++) {
				$sa = $pmap["args"][$i];
				$it = array_shift($args);

				if (is_null($it)) {
					echo "Missing argument for packet.\n";
					break;
				}

				switch ($sa) {
					case "I" :
						$data .= pack("N", $it);
						break;

					case "S" :
						$data .= pack("n", strlen($it)) . $it;
						break;

					case "G" :
						$data .= $it;
						break;

					case "s" :
						$data .= pack("n", sizeof($it));
						forEach ($it as $it_elem) {
							$data .= pack("n", strlen($it_elem)) . $it_elem;
						}
						break;

						default :
						echo "Unknown argument type! (" . $sa . ")\n";
						continue(2);
				}
			}

			$this->data = $data;
		}
		return true;
	}
}
  
?>