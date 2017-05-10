<?php

namespace Budabot\User\Modules;

/*
 * Amonette RK2
 *
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

/*
 * Amonette RK2
 *
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

/*
 * Amonette RK2
 *
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