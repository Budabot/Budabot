<?php
/**
 * $Id: example7.php 175138 2004-12-20 15:44:35Z meebey $
 * $Revision: 175138 $
 * $Author: meebey $
 * $Date: 2004-12-21 02:14:35 +1030 (Tue, 21 Dec 2004) $
 *
 * Copyright (C) 2002-2003 Mirco "MEEBEY" Bauer <mail@meebey.net> <http://www.meebey.net>
 * 
 * Full LGPL License: <http://www.meebey.net/lgpl.txt>
 * 
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

// ---EXAMPLE OF HOW TO USE Net_SmartIRC---
// this code shows how a mini php bot could be written
include_once('Net/SmartIRC.php');

class mybot
{
    function saytime_once(&$irc)
    {
        global $saytime_once_id;
        $irc->message(SMARTIRC_TYPE_CHANNEL, '#smartirc-test', '(once) the time is: '.date('H:i:s'));
        $irc->unregisterTimeid($saytime_once_id);
    }
    
    function saytime(&$irc)
    {
        $irc->message(SMARTIRC_TYPE_CHANNEL, '#smartirc-test', 'the time is: '.date('H:i:s'));
    }
    
    function quit(&$irc, &$ircdata)
    {
        $irc->quit("time to say goodbye...");
    }
}

$bot = &new mybot();
$irc = &new Net_SmartIRC();
$irc->setDebug(SMARTIRC_DEBUG_ALL);
$irc->setUseSockets(TRUE);

// register saytime() to be called every 30 sec. (30,000 milliseconds)
$irc->registerTimehandler(30000, $bot, 'saytime');

// register saytime_once() to be called in 10 sec. (10,000 milliseconds) and save the assigned id
// which is needed for unregistering the timehandler.
$saytime_once_id = $irc->registerTimehandler(10000, $bot, 'saytime_once');

$irc->registerActionhandler(SMARTIRC_TYPE_CHANNEL, '^!quit', $bot, 'quit');
$irc->connect('irc.freenet.de', 6667);
$irc->login('Net_SmartIRC', 'Net_SmartIRC Client '.SMARTIRC_VERSION.' (example7.php)', 8, 'Net_SmartIRC');
$irc->join(array('#smartirc-test','#test'));
$irc->listen();
$irc->disconnect();
?>