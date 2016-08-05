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