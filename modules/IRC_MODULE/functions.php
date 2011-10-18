<?php

function encodeGuildMessage($guild, $msg) {
	return chr(2) . chr(2) . chr(2) . "[{$guild}]" .  chr(2) . ' ' . $msg;
}

function decodeMessage() {

}

?>