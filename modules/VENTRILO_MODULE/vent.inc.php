<?php
/*
 * Amonette RK2
 *
 */

define("VENT_HEADSIZE", 20);
define("VENT_MAXPACKETSIZE", 512);
define("VENT_MAXPACKETNO", 32);

function &getHeadEncodeRef() {
	static $ventrilo_udp_encdata_head = array(
	0x80, 0xe5, 0x0e, 0x38, 0xba, 0x63, 0x4c, 0x99, 0x88, 0x63, 0x4c, 0xd6, 0x54, 0xb8, 0x65, 0x7e,
	0xbf, 0x8a, 0xf0, 0x17, 0x8a, 0xaa, 0x4d, 0x0f, 0xb7, 0x23, 0x27, 0xf6, 0xeb, 0x12, 0xf8, 0xea,
	0x17, 0xb7, 0xcf, 0x52, 0x57, 0xcb, 0x51, 0xcf, 0x1b, 0x14, 0xfd, 0x6f, 0x84, 0x38, 0xb5, 0x24,
	0x11, 0xcf, 0x7a, 0x75, 0x7a, 0xbb, 0x78, 0x74, 0xdc, 0xbc, 0x42, 0xf0, 0x17, 0x3f, 0x5e, 0xeb,
	0x74, 0x77, 0x04, 0x4e, 0x8c, 0xaf, 0x23, 0xdc, 0x65, 0xdf, 0xa5, 0x65, 0xdd, 0x7d, 0xf4, 0x3c,
	0x4c, 0x95, 0xbd, 0xeb, 0x65, 0x1c, 0xf4, 0x24, 0x5d, 0x82, 0x18, 0xfb, 0x50, 0x86, 0xb8, 0x53,
	0xe0, 0x4e, 0x36, 0x96, 0x1f, 0xb7, 0xcb, 0xaa, 0xaf, 0xea, 0xcb, 0x20, 0x27, 0x30, 0x2a, 0xae,
	0xb9, 0x07, 0x40, 0xdf, 0x12, 0x75, 0xc9, 0x09, 0x82, 0x9c, 0x30, 0x80, 0x5d, 0x8f, 0x0d, 0x09,
	0xa1, 0x64, 0xec, 0x91, 0xd8, 0x8a, 0x50, 0x1f, 0x40, 0x5d, 0xf7, 0x08, 0x2a, 0xf8, 0x60, 0x62,
	0xa0, 0x4a, 0x8b, 0xba, 0x4a, 0x6d, 0x00, 0x0a, 0x93, 0x32, 0x12, 0xe5, 0x07, 0x01, 0x65, 0xf5,
	0xff, 0xe0, 0xae, 0xa7, 0x81, 0xd1, 0xba, 0x25, 0x62, 0x61, 0xb2, 0x85, 0xad, 0x7e, 0x9d, 0x3f,
	0x49, 0x89, 0x26, 0xe5, 0xd5, 0xac, 0x9f, 0x0e, 0xd7, 0x6e, 0x47, 0x94, 0x16, 0x84, 0xc8, 0xff,
	0x44, 0xea, 0x04, 0x40, 0xe0, 0x33, 0x11, 0xa3, 0x5b, 0x1e, 0x82, 0xff, 0x7a, 0x69, 0xe9, 0x2f,
	0xfb, 0xea, 0x9a, 0xc6, 0x7b, 0xdb, 0xb1, 0xff, 0x97, 0x76, 0x56, 0xf3, 0x52, 0xc2, 0x3f, 0x0f,
	0xb6, 0xac, 0x77, 0xc4, 0xbf, 0x59, 0x5e, 0x80, 0x74, 0xbb, 0xf2, 0xde, 0x57, 0x62, 0x4c, 0x1a,
	0xff, 0x95, 0x6d, 0xc7, 0x04, 0xa2, 0x3b, 0xc4, 0x1b, 0x72, 0xc7, 0x6c, 0x82, 0x60, 0xd1, 0x0d );

	return $ventrilo_udp_encdata_head;
}

function &getDataEncodeRef() {
	static $ventrilo_udp_encdata_data = array(
	0x82, 0x8b, 0x7f, 0x68, 0x90, 0xe0, 0x44, 0x09, 0x19, 0x3b, 0x8e, 0x5f, 0xc2, 0x82, 0x38, 0x23,
	0x6d, 0xdb, 0x62, 0x49, 0x52, 0x6e, 0x21, 0xdf, 0x51, 0x6c, 0x76, 0x37, 0x86, 0x50, 0x7d, 0x48,
	0x1f, 0x65, 0xe7, 0x52, 0x6a, 0x88, 0xaa, 0xc1, 0x32, 0x2f, 0xf7, 0x54, 0x4c, 0xaa, 0x6d, 0x7e,
	0x6d, 0xa9, 0x8c, 0x0d, 0x3f, 0xff, 0x6c, 0x09, 0xb3, 0xa5, 0xaf, 0xdf, 0x98, 0x02, 0xb4, 0xbe,
	0x6d, 0x69, 0x0d, 0x42, 0x73, 0xe4, 0x34, 0x50, 0x07, 0x30, 0x79, 0x41, 0x2f, 0x08, 0x3f, 0x42,
	0x73, 0xa7, 0x68, 0xfa, 0xee, 0x88, 0x0e, 0x6e, 0xa4, 0x70, 0x74, 0x22, 0x16, 0xae, 0x3c, 0x81,
	0x14, 0xa1, 0xda, 0x7f, 0xd3, 0x7c, 0x48, 0x7d, 0x3f, 0x46, 0xfb, 0x6d, 0x92, 0x25, 0x17, 0x36,
	0x26, 0xdb, 0xdf, 0x5a, 0x87, 0x91, 0x6f, 0xd6, 0xcd, 0xd4, 0xad, 0x4a, 0x29, 0xdd, 0x7d, 0x59,
	0xbd, 0x15, 0x34, 0x53, 0xb1, 0xd8, 0x50, 0x11, 0x83, 0x79, 0x66, 0x21, 0x9e, 0x87, 0x5b, 0x24,
	0x2f, 0x4f, 0xd7, 0x73, 0x34, 0xa2, 0xf7, 0x09, 0xd5, 0xd9, 0x42, 0x9d, 0xf8, 0x15, 0xdf, 0x0e,
	0x10, 0xcc, 0x05, 0x04, 0x35, 0x81, 0xb2, 0xd5, 0x7a, 0xd2, 0xa0, 0xa5, 0x7b, 0xb8, 0x75, 0xd2,
	0x35, 0x0b, 0x39, 0x8f, 0x1b, 0x44, 0x0e, 0xce, 0x66, 0x87, 0x1b, 0x64, 0xac, 0xe1, 0xca, 0x67,
	0xb4, 0xce, 0x33, 0xdb, 0x89, 0xfe, 0xd8, 0x8e, 0xcd, 0x58, 0x92, 0x41, 0x50, 0x40, 0xcb, 0x08,
	0xe1, 0x15, 0xee, 0xf4, 0x64, 0xfe, 0x1c, 0xee, 0x25, 0xe7, 0x21, 0xe6, 0x6c, 0xc6, 0xa6, 0x2e,
	0x52, 0x23, 0xa7, 0x20, 0xd2, 0xd7, 0x28, 0x07, 0x23, 0x14, 0x24, 0x3d, 0x45, 0xa5, 0xc7, 0x90,
	0xdb, 0x77, 0xdd, 0xea, 0x38, 0x59, 0x89, 0x32, 0xbc, 0x00, 0x3a, 0x6d, 0x61, 0x4e, 0xdb, 0x29 );

	return $ventrilo_udp_encdata_data;
}

function &getCRCref() {
	static $ventrilo_crc_table = array(
	0x0000, 0x1021, 0x2042, 0x3063, 0x4084, 0x50a5, 0x60c6, 0x70e7,
	0x8108, 0x9129, 0xa14a, 0xb16b, 0xc18c, 0xd1ad, 0xe1ce, 0xf1ef,
	0x1231, 0x0210, 0x3273, 0x2252, 0x52b5, 0x4294, 0x72f7, 0x62d6,
	0x9339, 0x8318, 0xb37b, 0xa35a, 0xd3bd, 0xc39c, 0xf3ff, 0xe3de,
	0x2462, 0x3443, 0x0420, 0x1401, 0x64e6, 0x74c7, 0x44a4, 0x5485,
	0xa56a, 0xb54b, 0x8528, 0x9509, 0xe5ee, 0xf5cf, 0xc5ac, 0xd58d,
	0x3653, 0x2672, 0x1611, 0x0630, 0x76d7, 0x66f6, 0x5695, 0x46b4,
	0xb75b, 0xa77a, 0x9719, 0x8738, 0xf7df, 0xe7fe, 0xd79d, 0xc7bc,
	0x48c4, 0x58e5, 0x6886, 0x78a7, 0x0840, 0x1861, 0x2802, 0x3823,
	0xc9cc, 0xd9ed, 0xe98e, 0xf9af, 0x8948, 0x9969, 0xa90a, 0xb92b,
	0x5af5, 0x4ad4, 0x7ab7, 0x6a96, 0x1a71, 0x0a50, 0x3a33, 0x2a12,
	0xdbfd, 0xcbdc, 0xfbbf, 0xeb9e, 0x9b79, 0x8b58, 0xbb3b, 0xab1a,
	0x6ca6, 0x7c87, 0x4ce4, 0x5cc5, 0x2c22, 0x3c03, 0x0c60, 0x1c41,
	0xedae, 0xfd8f, 0xcdec, 0xddcd, 0xad2a, 0xbd0b, 0x8d68, 0x9d49,
	0x7e97, 0x6eb6, 0x5ed5, 0x4ef4, 0x3e13, 0x2e32, 0x1e51, 0x0e70,
	0xff9f, 0xefbe, 0xdfdd, 0xcffc, 0xbf1b, 0xaf3a, 0x9f59, 0x8f78,
	0x9188, 0x81a9, 0xb1ca, 0xa1eb, 0xd10c, 0xc12d, 0xf14e, 0xe16f,
	0x1080, 0x00a1, 0x30c2, 0x20e3, 0x5004, 0x4025, 0x7046, 0x6067,
	0x83b9, 0x9398, 0xa3fb, 0xb3da, 0xc33d, 0xd31c, 0xe37f, 0xf35e,
	0x02b1, 0x1290, 0x22f3, 0x32d2, 0x4235, 0x5214, 0x6277, 0x7256,
	0xb5ea, 0xa5cb, 0x95a8, 0x8589, 0xf56e, 0xe54f, 0xd52c, 0xc50d,
	0x34e2, 0x24c3, 0x14a0, 0x0481, 0x7466, 0x6447, 0x5424, 0x4405,
	0xa7db, 0xb7fa, 0x8799, 0x97b8, 0xe75f, 0xf77e, 0xc71d, 0xd73c,
	0x26d3, 0x36f2, 0x0691, 0x16b0, 0x6657, 0x7676, 0x4615, 0x5634,
	0xd94c, 0xc96d, 0xf90e, 0xe92f, 0x99c8, 0x89e9, 0xb98a, 0xa9ab,
	0x5844, 0x4865, 0x7806, 0x6827, 0x18c0, 0x08e1, 0x3882, 0x28a3,
	0xcb7d, 0xdb5c, 0xeb3f, 0xfb1e, 0x8bf9, 0x9bd8, 0xabbb, 0xbb9a,
	0x4a75, 0x5a54, 0x6a37, 0x7a16, 0x0af1, 0x1ad0, 0x2ab3, 0x3a92,
	0xfd2e, 0xed0f, 0xdd6c, 0xcd4d, 0xbdaa, 0xad8b, 0x9de8, 0x8dc9,
	0x7c26, 0x6c07, 0x5c64, 0x4c45, 0x3ca2, 0x2c83, 0x1ce0, 0x0cc1,
	0xef1f, 0xff3e, 0xcf5d, 0xdf7c, 0xaf9b, 0xbfba, 0x8fd9, 0x9ff8,
	0x6e17, 0x7e36, 0x4e55, 0x5e74, 0x2e93, 0x3eb2, 0x0ed1, 0x1ef0 );

	return $ventrilo_crc_table;
}

function strstrbef($haystack, $needle) {
	$needle=strrev($needle);
	$haystack=strrev($haystack);
	$result=strstr($haystack,$needle);
	$result=substr($haystack,strlen($needle));
	$result=strrev($result);
	return $result;
}


/**
 * decbin2: PHP's decbin() doesn't pad the binary string to a full 32 bits, unless
 * it's a negative number. Since we need to mimic casting to smaller int types,
 * I'll use this version over the built-in decbin().
 */
function decbin2($val) {
	return str_pad(decbin($val), 32, "0", STR_PAD_LEFT);
}


/**
 * bindec2: PHP's decbin() stores negative numbers as two's complement... but PHP's
 * bindec() doesn't check for two's! I'll use this to decode rather than the built-in function.
 *
 * bindec( decbin( -1000 )) != -1000		// not correct!
 * bindec2( decbin2( -1000 )) == -1000		// correct!
 */
function bindec2($binstr) {
	$val = bindec($binstr);

	// it's not two's. Return the built-in bindec() value.
	if (strlen($binstr) != 32 || substr($binstr, 0, 1) == "0") {
		return $val;
	}

	// it's two's. I needed the 0 shift to trick PHP into treating the var as an int.
	return (( ~ ( $val << 0 )) + 1 ) * - 1;
}


/**
 * smallCast: mimic a cast from larger int to smaller int. $bits is the destination size.
 * Internally, PHP integers seem to always be 32 bit. (my test systems are both 64-bit
 * - Athlon x64 and PowerPC G5 - and PHP still uses 32 bit ints.)
 */
function smallCast($val, $bits) {
	$binstr = decbin2($val);
	return bindec2(substr($binstr, 32 - $bits));
}


/**
 * Vent: class to create all the data needed to encode and decode VentPackets.
 */
class Vent {
	var $clock;			// u_short of the epoch time for the last request.
	var $timeout;			// timeout for socket read in *microseconds* ( 1,000,000 microsec = 1 sec )
	var $packets = array();	// hold all the decoded response packets, in correct order
	var $response;			// all the decoded data

	function getClock()			{ return $this->clock; }
	function getTimeout()			{ return $this->timeout; }
	function setTimeout( $microsecs )	{ $this->timeout = $microsecs; }
	function &getPackets()		{ return $this->packets; } // by ref
	function getResponse()		{ return $this->response; }
	
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

		$crc = $this->getCRC($this->response);

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
	function getCRC(&$data) {
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
/* end of Vent class */


/**
 * VentPacket: class to mimic the ventrilo_udp_head struct in the source,
 * but with more logic moved inside.
 */
class VentPacket {
	var $rawdata;		// hold raw, unencoded data portion of packet
	var $data;		// hold encoded data

	var $head_items;	// an array, with references to each item in the header, in proper order.
	var $header;		// encoded header string

	var $packet;		// hold full encoded packet (header + data)

	/**
	 * 10 vars for the packet header. all 2 byte unsigned shorts (20 byte header)
	 * The order is important for packing / unpacking.
	 */
	var $headkey;		// key used to encode the header part
	var $zero;		// always 0!
	var $cmd;		// 1, 2, or 7 are valid command requests
	var $id;		// epoch time cast into an unsigned short
	var $totlen;		// total data length, across all packets in this request / result
	var $len;		// data length in this packet
	var $totpck;		// total packets in this request / result
	var $pck;		// number of this packet
	var $datakey;		// key used to encode the data part
	var $crc;		// checksum


	/**
	 * mapHeader: Easy way to keep the correct order. We can use the array for loops when byte
	 * order is important, and still access each element by name. Using a straight hash would
	 * have lost the ordering.
	 */
	function mapHeader() {
		$this->head_items = array(
			&$this->headkey,
			&$this->zero,
			&$this->cmd,
			&$this->id,
			&$this->totlen,
			&$this->len,
			&$this->totpck,
			&$this->pck,
			&$this->datakey,
			&$this->crc);
	}

}
/* end of VentPacket class */


/**
 * VentRequestPacket: For outgoing requests.
 */
class VentRequestPacket extends VentPacket {

	public function __construct($cmd, $id, $pass) {
		// set up the references
		$this->mapHeader();
		
		// the only thing in the data part
		$this->rawdata = pack("a16", $pass);

		$this->zero = 0;
		$this->cmd = ($cmd == 1 || $cmd == 2 || $cmd == 7) ? $cmd : 1;
		$this->id = $id;
		$this->totlen = strlen($this->rawdata);
		$this->len = $this->totlen;
		$this->totpck = 1;
		$this->pck = 0;
		$this->crc = Vent::getCRC($this->rawdata);
		
		// $this->data & datakey set here
		$this->encodeData();

		// $this->header & headkey set here
		$this->encodeHeader();

		$this->packet = $this->header . $this->data;
	}

	/**
	 * createKeys: keys are used to encode header and data parts. a1 and a2 are the two cyphers
	 * derived from the full key.
	 */
	function createKeys(&$key, &$a1, &$a2, $is_head = false) {
		$rndupk = unpack("vx", pack("S", mt_rand(1, 65536)));	// need this in little endian
		$rnd = $rndupk['x'];

		$rnd &= 0x7fff;

		$a1 = smallCast($rnd, 8);
		$a2 = $rnd >> 8;

		if ($a2 == 0) {
			$a2 = $is_head ? 69 : 1;
			$rnd |= ($a2 << 8);
		}

		$key = $rnd;
	}

	/**
	 * encodeHeader: Encoded after the data portion. Do some sanity checks here,
	 * make sure all the header info is here, and we've got encoded data of
	 * the correct length...
	 */
	function encodeHeader() {
		$this->createKeys($key, $a1, $a2, true);
		$table = getHeadEncodeRef();

		// the head key doesn't get encoded, just packed
		$enchead = pack("n", $key);

		// start the loop at 1 to skip headkey, pack them as unsigned shorts
		// in network byte order. Append each one to our $to_encode string.
		$to_encode = '';

		for ($i = 1; $i < count($this->head_items); $i++) {
			$to_encode .= pack("n", $this->head_items[$i]);
		}

		// Need to encode as unsigned chars, not shorts. That's the reason for the pack & unpack.
		// Index starts at 1 for unpack return array, not 0.
		$chars = unpack("C*", $to_encode);

		for ($i = 1; $i <= count($chars); $i++) {
			$chars[$i] = smallCast($chars[$i] + $table[$a2] + (($i - 1) % 5), 8);
			$enchead .= pack("C", $chars[$i]);
			$a2 = smallCast($a2 + $a1, 8);
		}

		$this->headkey = $key;
		$this->header = $enchead;
	}

	/**
	 * encodeData: The data has to be encoded first because the datakey is part of the
	 * header, and it needs to encoded along with the rest of the header.
	 */
	function encodeData() {
		$this->createKeys($key, $a1, $a2);

		// 1 indexed array
		$chars = unpack("c*", $this->rawdata);
		
		// Data table reference
		$table = getDataEncodeRef();
		$encdata = '';

		for ($i = 1; $i <= count($chars); $i++) {
			$chars[$i] = smallCast($chars[$i] + $table[$a2] + (($i - 1) % 72), 8);
			$encdata .= pack("C", $chars[$i]);
			$a2 = smallCast($a2 + $a1, 8);
		}

		$this->datakey = $key;
		$this->data = $encdata;
	}
}
/* end of VentRequestPacket class */


/**
 * VentResponsePacket: For incoming data.
 */
class VentResponsePacket extends VentPacket {

	public static function create($packet) {
		$plen = strlen($packet);

		if ($plen > VENT_MAXPACKETSIZE || $plen < VENT_HEADSIZE) {
			echo "ERROR: Response packet was $plen bytes. It should be between ";
			echo VENT_HEADSIZE ." and ". VENT_MAXPACKETSIZE ." bytes.\n";
			return null;
		}

		$rp = new VentResponsePacket();

		$rp->mapHeader();							// set up the references
		$rp->packet = $packet;
		$rp->header = substr($packet, 0, VENT_HEADSIZE);
		$rp->data = substr($packet, VENT_HEADSIZE);

		if (!$rp->decodeHeader()) {
			return null;
		}
		if (!$rp->decodeData()) {
			return null;
		}
		
		return $rp;
	}

	public function __construct($packet) {
		
	}

	/**
	 * decodeHeader: run through the header portion of the packet, get the key, decode,
	 * and perform some sanity checks.
	 */
	function decodeHeader() {
		$table = getHeadEncodeRef();

		// unpack the key as a short
		$key_array = unpack("n1", $this->header);

		// unpack the rest of the header as chars
		$chars = unpack("C*", substr( $this->header, 2));
		$key = $key_array[1];

		$a1 = smallCast($key, 8);
		$a2 = $key >> 8;

		if ($a1 == 0) {
			echo("ERROR: Invalid packet. Header key is invalid.\n");
			return false;
		}

		// First step is to decode each unsigned char using the cypher key.
		// Once we finish 2 bytes treat them as a short, get the endian right,
		// and stick them in the proper header item slot.
		$item_no = 1;		// for $this->head_items array. we skip the unencoded headkey, at index 0.

		for ($i = 1; $i <= count($chars); $i++) {
			$chars[$i] -= smallCast($table[$a2] + (($i - 1) % 5), 8);
			$a2 = smallCast($a2 + $a1, 8);

			// Once we've completed two bytes, we can treat them as a short, and fix the endian.
			if (($i % 2) == 0) {
				$short_array = unpack("n1", pack( "C2", $chars[$i - 1], $chars[$i]));
				$this->head_items[$item_no] = $short_array[1];
				$item_no++;
			}
		}

		// simple sanity checks
		if ($this->zero != 0 || $this->cmd != 3) {
			echo "ERROR: Invalid packet. Expected 0 & 3, found {$this->zero} & {$this->cmd}.\n";
			return false;
		}

		if ($this->len != strlen( $this->data )) {
			echo "ERROR: Invalid packet. Data is ". strlen( $this->data ) ." bytes, expected {$this->len}.\n";
			return false;
		}

		$this->headkey = $key;
		return true;
	}


	/**
	 * use the datakey to find the cyphers and decode the data portion of the packet
	 */
	function decodeData() {
		$table = getDataEncodeRef();

		$a1 = smallCast($this->datakey, 8);
		$a2 = $this->datakey >> 8;

		if ($a1 == 0) {
			echo "ERROR: Invalid packet. Data key is invalid.\n";
			return false;
		}

		$chars = unpack( "C*", $this->data );		// unpack the data as unsigned chars

		for ($i = 1; $i <= count($chars); $i++) {
			$chars[$i] -= smallCast($table[$a2] + (($i - 1) % 72), 8);
			$a2 = smallCast($a2 + $a1, 8);
			$this->rawdata .= chr($chars[$i]);
		}

		return true;
	}
}
/* end of VentResponsePacket class */
?>
