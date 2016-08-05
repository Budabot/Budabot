<?php

namespace Budabot\User\Modules;

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