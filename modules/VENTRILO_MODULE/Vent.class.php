<?php

namespace Budabot\User\Modules;

/*
 * Amonette RK2
 *
 * Vent: class to create all the data needed to encode and decode VentPackets.
 */
class Vent {
	var $clock;			// u_short of the epoch time for the last request.
	var $timeout;			// timeout for socket read in *microseconds* ( 1,000,000 microsec = 1 sec )
	var $packets = array();	// hold all the decoded response packets, in correct order
	var $response;			// all the decoded data

	function getClock() {
		return $this->clock;
	}

	function getTimeout() {
		return $this->timeout;
	}

	function setTimeout( $microsecs ) {
		$this->timeout = $microsecs;
	}

	function &getPackets() { 
		return $this->packets;
	}

	function getResponse() {
		return $this->response;
	}
	
	function __construct() {
		$this->timeout = 5000000;		// default to 5 second timeout
	}

	/**
	 * makeRequest: send off a request to the vent server, return true/false. I'm not checking
	 * for valid IP or hostname - someone else can add this stuff.
	 * Note: The password field is no longer required for 2.3 or higher servers. Even if a server
	 * is password protected, it will return status info.
	 */
	function makeRequest( $cmd, $ip, $port, $pass = "" ) {
		$this->clock = smallCast( time(), 16 );		// reset the clock for each request
		$this->packets = array();					// start fresh
		$this->response = '';

		$data = pack( "a16", $pass );				// the only data for a request is a password, 16 bytes.

		$request = new VentRequestPacket( $cmd, $this->clock, $pass );

		$sfh = fsockopen("udp://$ip", $port, $errno, $errstr );

		if (!$sfh) {
			echo "Socket Error: $errno - $errstr\n";
			return false;
		}

		// put the encoded request packet on the stream
		fwrite($sfh, $request->packet);
		stream_set_timeout($sfh, 0, $this->timeout);

		// read response packets off the stream. with UDP, packets can (and often)
		// come out of order, so we'll put then back together after closing the socket.
		while(false !== $pck = fread($sfh, VENT_MAXPACKETSIZE)) {
			if (count($this->packets) >= VENT_MAXPACKETNO) {
				echo "ERROR: Received more packets than the maximum allowed in a response.\n";
				fclose($sfh);
				return false;
			}

			// decode this packet. If we get null back, there was an error in the decode.
			$rpobj = VentResponsePacket::create($pck);
			if (null == $rpobj) {
				fclose($sfh);
				return false;
			}

			// check the id / clock. They should match the request, if not - skip it.
			// also skip if there's a duplicate packet. Could throw an error here.
			if ($rpobj->id != $this->clock || isset($this->packets[$rpobj->pck])) {
				continue;
			}
			$this->packets[$rpobj->pck] = $rpobj;
			
			if ($this->checkForEnd($this->packets)) {
				break;
			}
		}

		fclose( $sfh );

		// check if we've got the right number of packets
		if ($this->packets[0]->totpck != count($this->packets)) {
			echo "ERROR: Received less packets than expected in the response.\n";
			return false;
		}

		// the order may not be correct so sort on the key
		if (!ksort( $this->packets, SORT_NUMERIC)) {
			echo "ERROR: Failed to sort the response packets in order.\n";
			return false;
		}

		// All the response packets arrived, were decoded, and are in the proper order. We
		// can pull the decoded data together, and check that the total length matches
		// the value in the header, and the crc matches.
		forEach ($this->packets as $packet) {
			$this->response .= $packet->rawdata;
		}

		$rlen = strlen($this->response);
		if ($rlen != $this->packets[0]->totlen) {
			echo "ERROR: Response data is $rlen bytes. Expected {$this->packets[0]->totlen} bytes.\n";
			return false;
		}

		$crc = self::getCRC($this->response);

		if ($crc != $this->packets[0]->crc) {
			echo "ERROR: response crc is $crc. Expected: {$this->packets[0]->crc}.\n";
			return false;
		}

		// everything worked fine
		return true;
	}


	/**
	 * getCRC: find the CRC for a data argument.
	 */
	static function getCRC($data) {
		$crc = 0;
		$dtoks = unpack("c*", $data);		// Note: unpack starts output array index at 1, NOT 0.
		$table = getCRCref();			// my CRC table reference

		for ($i = 1; $i <= count($dtoks); $i++) {
			$crc = $table[ $crc >> 8 ] ^ $dtoks[$i] ^ (smallCast($crc << 8, 16));
		}

		return $crc;
	}
	
	function checkForEnd($arr) {
		if (count($arr) == 0) {
			return false;
		}
		
		$totalLength = $arr[0]->totlen;
		forEach ($arr as $packet) {
			$totalLength -= $packet->len;
		}

		return $totalLength === 0;
	}
}