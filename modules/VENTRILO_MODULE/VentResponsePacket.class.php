<?php

namespace Budabot\User\Modules;

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