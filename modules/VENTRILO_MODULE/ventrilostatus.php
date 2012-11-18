<?php

/*
	This file should not be modified. This is so that future versions can be
	dropped into place as servers are updated.

	Version 2.3.0: Supports phantoms.
	Version 2.2.1: Supports channel comments.
*/


function StrKey($src, $key, &$res) {
	$key .= " ";
	if (strncasecmp($src, $key, strlen($key))) {
		return false;
	}

	$res = substr($src, strlen($key));
	return true;
}

function StrSplit($src, $sep, &$d1, &$d2) {
	$pos = strpos($src, $sep);
	if ($pos === false) {
		$d1 = $src;
		return;
	}

	$d1 = substr($src, 0, $pos);
	$d2 = substr($src, $pos + 1);
}

function StrDecode(&$src) {
	$res = "";

	for ($i = 0; $i < strlen($src);) {
		if ($src[$i] == '%') {
			$res .= sprintf("%c", intval(substr($src, $i + 1, 2), 16));
			$i += 3;
			continue;
		}

		$res .= $src[$i];
		$i += 1;
	}

	return $res;
}


class CVentriloClient {
	var	$m_uid;			// User ID.
	var	$m_admin;		// Admin flag.
	var $m_phan;		// Phantom flag.
	var $m_cid;			// Channel ID.
	var $m_ping;		// Ping.
	var	$m_sec;			// Connect time in seconds.
	var	$m_name;		// Login name.
	var	$m_comm;		// Comment.

	function Parse($str) {
		$ary = explode(",", $str);

		for ($i = 0; $i < count($ary); $i++) {
			StrSplit($ary[$i], "=", $field, $val);

			if (strcasecmp($field, "UID") == 0) {
				$this->m_uid = $val;
				continue;
			}

			if (strcasecmp($field, "ADMIN") == 0) {
				$this->m_admin = $val;
				continue;
			}

			if (strcasecmp($field, "CID") == 0) {
				$this->m_cid = $val;
				continue;
			}

			if (strcasecmp($field, "PHAN") == 0) {
				$this->m_phan = $val;
				continue;
			}

			if (strcasecmp($field, "PING") == 0) {
				$this->m_ping = $val;
				continue;
			}

			if (strcasecmp($field, "SEC") == 0) {
				$this->m_sec = $val;
				continue;
			}

			if (strcasecmp($field, "NAME") == 0) {
				$this->m_name = StrDecode($val);
				continue;
			}

			if (strcasecmp($field, "COMM") == 0) {
				$this->m_comm = StrDecode($val);
				continue;
			}
		}
	}
}

class CVentriloChannel {
	var	$m_cid;		// Channel ID.
	var	$m_pid;		// Parent channel ID.
	var	$m_prot;	// Password protected flag.
	var	$m_name;	// Channel name.
	var	$m_comm;	// Channel comment.

	function Parse($str) {
		$ary = explode(",", $str);

		for ($i = 0; $i < count( $ary ); $i++) {
			StrSplit($ary[$i], "=", $field, $val);

			if (strcasecmp($field, "CID") == 0) {
				$this->m_cid = $val;
				continue;
			}

			if (strcasecmp($field, "PID") == 0) {
				$this->m_pid = $val;
				continue;
			}

			if (strcasecmp($field, "PROT") == 0) {
				$this->m_prot = $val;
				continue;
			}

			if (strcasecmp($field, "NAME") == 0) {
				$this->m_name = StrDecode($val);
				continue;
			}

			if (strcasecmp($field, "COMM") == 0) {
				$this->m_comm = StrDecode($val);
				continue;
			}
		}
	}
}


class CVentriloStatus {
	// These need to be filled in before issueing the request.

	var	$m_cmdprog;		// Path and filename of external process to execute. ex: /var/www/html/ventrilo_status
	var	$m_cmdcode;		// Specific status request code. 1=General, 2=Detail.
	var	$m_cmdhost;		// Hostname or IP address to perform status of.
	var	$m_cmdport;		// Port number of server to status.

	// These are the result variables that are filled in when the request is performed.

	var	$m_error;		// If the ERROR: keyword is found then this is the reason following it.

	var	$m_name;				// Server name.
	var	$m_phonetic;			// Phonetic spelling of server name.
	var	$m_comment;				// Server comment.
	var	$m_maxclients;			// Maximum number of clients.
	var	$m_voicecodec_code;		// Voice codec code.
	var $m_voicecodec_desc;		// Voice codec description.
	var	$m_voiceformat_code;	// Voice format code.
	var	$m_voiceformat_desc;	// Voice format description.
	var	$m_uptime;				// Server uptime in seconds.
	var	$m_platform;			// Platform description.
	var	$m_version;				// Version string.

	var	$m_channelcount;		// Number of channels as specified by the server.
	var	$m_channelfields;		// Channel field names.
	var	$m_channellist;			// Array of CVentriloChannel's.

	var	$m_clientcount;			// Number of clients as specified by the server.
	var	$m_clientfields;		// Client field names.
	var $m_clientlist;			// Array of CVentriloClient's.

	function Parse($str) {
		// Remove trailing newline.
		$pos = strpos($str, "\n");
		if ($pos !== false) {
			$str = substr($str, 0, $pos);
		}

		// Begin parsing for keywords.

		if (StrKey($str, "ERROR:", $val)) {
			$this->m_error = $val;
			return -1;
		}

		if (StrKey($str, "NAME:", $val)) {
			$this->m_name = StrDecode($val);
			return 0;
		}

		if (StrKey($str, "PHONETIC:", $val)) {
			$this->m_phonetic = StrDecode($val);
			return 0;
		}

		if (StrKey($str, "COMMENT:", $val)) {
			$this->m_comment = StrDecode($val);
			return 0;
		}

		if (StrKey($str, "AUTH:", $this->m_auth)) {
			return 0;
		}

		if (StrKey($str, "MAXCLIENTS:", $this->m_maxclients)) {
			return 0;
		}

		if (StrKey($str, "VOICECODEC:", $val)) {
			StrSplit($val, ",", $this->m_voicecodec_code, $desc);
			$this->m_voicecodec_desc = StrDecode($desc);
			return 0;
		}

		if (StrKey($str, "VOICEFORMAT:", $val)) {
			StrSplit($val, ",", $this->m_voiceformat_code, $desc);
			$this->m_voiceformat_desc = StrDecode($desc);
			return 0;
		}

		if (StrKey($str, "UPTIME:", $val)) {
			$this->m_uptime = $val;
			return 0;
		}

		if (StrKey($str, "PLATFORM:", $val)) {
			$this->m_platform = StrDecode($val);
			return 0;
		}

		if (StrKey($str, "VERSION:", $val)) {
			$this->m_version = StrDecode($val);
			return 0;
		}

		if (StrKey($str, "CHANNELCOUNT:", $this->m_channelcount)) {
			return 0;
		}

		if (StrKey($str, "CHANNELFIELDS:", $this->m_channelfields)) {
			return 0;
		}

		if (StrKey($str, "CHANNEL:", $val)) {
			$chan = new CVentriloChannel;
			$chan->Parse($val);

			$this->m_channellist[count($this->m_channellist)] = $chan;
			return 0;
		}

		if (StrKey($str, "CLIENTCOUNT:", $this->m_clientcount)) {
			return 0;
		}

		if (StrKey($str, "CLIENTFIELDS:", $this->m_clientfields)) {
			return 0;
		}

		if (StrKey($str, "CLIENT:", $val)) {
			$client = new CVentriloClient;
			$client->Parse($val);

			$this->m_clientlist[count($this->m_clientlist)] = $client;
			return 0;
		}

		// Unknown key word. Could be a new keyword from a newer server.

		return 1;
	}

	function ChannelFind($cid) {
		for ($i = 0; $i < count($this->m_channellist); $i++) {
			if ($this->m_channellist[$i]->m_cid == $cid) {
				return $this->m_channellist[$i];
			}
		}

		return null;
	}

	function ChannelPathName($idx) {
		$chan = $this->m_channellist[$idx];
		$pathname = $chan->m_name;

		for(;;) {
			$chan = $this->ChannelFind($chan->m_pid);
			if ($chan == null) {
				break;
			}

			$pathname = $chan->m_name . "/" . $pathname;
		}

		return $pathname;
	}

	function Request() {
		$vent = new Vent;
		$vent->setTimeout( 5000000 );  // 5 seconds

		if (!$vent->makeRequest(2, $this->m_cmdhost, $this->m_cmdport)) {
			$this->m_error = "Could not connect to server.";
			return -2;
		} else {
			$rawresponse = $vent->getResponse();
			if (empty($rawresponse)) {
				$this->m_error = "The server returned no data.";
				return -3;
			}

			$nohtmltags = strip_tags($rawresponse);
			$formattedResponse = preg_split("/[\r\n]+/", $nohtmltags, 0, REG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

			forEach ($formattedResponse as $line) {
				$val = $this->Parse( $line );
				if ($val < 0) {
					return $val;
				}
			}
			return 0;
		}
	}
};

?>