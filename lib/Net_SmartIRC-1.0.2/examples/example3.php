<?php
/**
 * $Id: example3.php 241083 2007-08-11 16:26:08Z amir $
 * $Revision: 241083 $
 * $Author: amir $
 * $Date: 2007-08-12 01:56:08 +0930 (Sun, 12 Aug 2007) $
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
    function op_list(&$irc, &$data)
    {
        $irc->message(SMARTIRC_TYPE_CHANNEL, '#smartirc-test', 'ops on this channel are:');
        
        $oplist = '';
        // Here we're going to get the Channel Operators, the voices and users
        // Method is available too, e.g. $irc->channel['#test']->users will
        // Return the channel's users.
        foreach ($irc->channel['#test']->ops as $key => $value) {
            $oplist .= ' '.$key;
        }
        
        // result is send to #smartirc-test (we don't want to spam #test)
        $irc->message(SMARTIRC_TYPE_CHANNEL, '#smartirc-test', $oplist);
    }
}

$bot = &new mybot();
$irc = &new Net_SmartIRC();
$irc->setDebug(SMARTIRC_DEBUG_ALL);
$irc->setUseSockets(TRUE);
// Using Channel Syncing we will track all users on all channels we are joined
// (Note. Use setChannelSyncing instead of setChannelSynching)
$irc->setChannelSyncing(TRUE);
$irc->registerActionhandler(SMARTIRC_TYPE_CHANNEL, '^!ops', $bot, 'op_list');
$irc->connect('irc.freenet.de', 6667);
$irc->login('Net_SmartIRC', 'Net_SmartIRC Client '.SMARTIRC_VERSION.' (example3.php)', 8, 'Net_SmartIRC');
$irc->join(array('#smartirc-test','#test'));
$irc->listen();
$irc->disconnect();
?>
