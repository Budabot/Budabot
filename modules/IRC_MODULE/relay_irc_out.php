<?php
/*
** Author: Legendadv (RK2)
** IRC RELAY MODULE
**
** Developed for: Budabot(http://aodevs.com/index.php/topic,512.0.html)
**
*/

global $ircSocket;
if (IRC::isConnectionActive($ircSocket)) {
	if ($args[2][0] != $setting->get("symbol")) {

		$pattern = '/<a href="itemref:\/\/(\d+)\/(\d+)\/(\d+)">([^<]+)<\/a>/';
		$replace = chr(3) . chr(3) . '\4' . chr(3) . ' ' . chr(3) . '(http://auno.org/ao/db.php?id=\1&id2=\2&ql=\3)' . chr(3) . chr(3);

		$msg = htmlspecialchars_decode(preg_replace($pattern, $replace, $message));
		//$msg = htmlspecialchars_decode(preg_replace($patterns, $replaces, $message), ENT_QUOTES);

		if (Util::isValidSender($sender)) {
			$msg = "$sender: $msg";
		}
		IRC::send($ircSocket, $setting->get('irc_channel'), encodeGuildMessage(getGuildAbbreviation(), $msg));
		LegacyLogger::log_chat("Out. IRC Msg.", $sender, $msg);
	}
}

?>
