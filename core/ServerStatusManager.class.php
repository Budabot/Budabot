<?php

/**
 * @Instance
 */
class ServerStatusManager {

	public function lookup($rk_num) {
		$serverstat = xml::getUrl("probes.funcom.com/ao.xml", 30);
		
		if ($serverstat == NULL) {
			return null;
		}

		$data = xml::spliceData($serverstat, "<dimension name=\"d$rk_num", "</dimension>");
		if (!$data) {
			return null;
		}
		
		$obj = new ServerStatus();

		preg_match("/locked=\"(0|1)\"/i", $data, $tmp);
		$obj->locked = $tmp[1];

		preg_match("/<omni percent=\"([0-9.]+)\"\/>/i", $data, $tmp);
		$obj->omni = $tmp[1];
		preg_match("/<neutral percent=\"([0-9.]+)\"\/>/i", $data, $tmp);
		$obj->neutral = $tmp[1];
		preg_match("/<clan percent=\"([0-9.]+)\"\/>/i", $data, $tmp);
		$obj->clan = $tmp[1];

		preg_match("/<servermanager status=\"([0-9]+)\"\/>/i", $data, $tmp);
		$obj->servermanager = $tmp[1];
		preg_match("/<clientmanager status=\"([0-9]+)\"\/>/i", $data, $tmp);
		$obj->clientmanager = $tmp[1];
		preg_match("/<chatserver status=\"([0-9]+)\"\/>/i", $data, $tmp);
		$obj->chatserver = $tmp[1];

		preg_match("/display-name=\"(.+)\" loadmax/i", $data, $tmp);
		$obj->name = $tmp[1];

		$data = xml::spliceMultiData($data, "<playfield", "/>");
		forEach ($data as $hdata) {
			if (preg_match("/id=\"(.+)\" name=\"(.+)\" status=\"(.+)\" load=\"(.+)\" players=\"(.+)%\"/i", $hdata, $arr)) {
				$playfield = new stdClass;
				$playfield->id = $arr[1];
				$playfield->long_name = $arr[2];
				$playfield->status = $arr[3];
				$playfield->load = $arr[4];
				$playfield->percent = $arr[5];
				$obj->data[$arr[1]] = $playfield;
			}
		}

		return $obj;
	}
}

class ServerStatus {
	public $data;
	public $servermanager;
	public $clientmanager;
	public $chatserver;
	public $locked;
	public $omni;
	public $neutral;
	public $clan;
	public $name;
}