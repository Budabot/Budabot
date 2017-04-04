<?php
/**
 * $Id: example4.php 175138 2004-12-20 15:44:35Z meebey $
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
    function realname_check(&$irc, &$data)
    {
        // lets loop through all user that are on the #test channel
        // result is send to #smartirc-test (we don't want to spam #test)
        foreach ($irc->channel['#test']->users as $value) {
            $nickname = $value->nick;
            $realname = $value->realname;
            
            // lets match against this realname regex, which wants (capital-letter) *(small-letter) (space) (capital-letter) *(small-letter)
            if(preg_match('/^[A-Z][a-z]+ ([A-Z][a-z]+(\-[A-Z][a-z]+)?)+/', $realname) == 0)
                $irc->message(SMARTIRC_TYPE_CHANNEL, '#smartirc-test', $nickname.' has not valid realname! ('.$realname.')');
            else 
                $irc->message(SMARTIRC_TYPE_CHANNEL, '#smartirc-test', $nickname.' approved ('.$realname.')');
        }
        
        $irc->message(SMARTIRC_TYPE_CHANNEL, '#smartirc-test', '<end of realnamecheck>');
    }
}

$bot = &new mybot();
$irc = &new Net_SmartIRC();
$irc->setDebug(SMARTIRC_DEBUG_ALL);
$irc->setUseSockets(TRUE);
// activating the channel synching is important, or we won't have $irc->channel[] available
$irc->setChannelSynching(TRUE);
$irc->registerActionhandler(SMARTIRC_TYPE_CHANNEL, '^!realnamecheck', $bot, 'realname_check');
$irc->connect('irc.freenet.de', 6667);
$irc->login('Net_SmartIRC', 'Net_SmartIRC Client '.SMARTIRC_VERSION.' (example4.php)', 8, 'Net_SmartIRC');
$irc->join(array('#smartirc-test','#test'));
$irc->listen();
$irc->disconnect();
?>