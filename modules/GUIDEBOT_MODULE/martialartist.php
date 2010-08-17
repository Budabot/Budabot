<?php
$blob = "<header>::::: Guide to Martial Artists :::::<end>\n\n
Please see the following website for an excellent, yet somewhat outdated, Martial Artist Guide

'http://users.adelphia.net/~chronita/index.htm'

This guide is quite excellent in giving you information all about the MA class and how to play them
 ";

$msg = bot::makeLink("Guide to Martialartists", $blob); 
bot::send($msg, $sendto); 
?>