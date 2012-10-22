<?php
/**
 * $Id: example6.php 175138 2004-12-20 15:44:35Z meebey $
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
    function onjoin_greeting(&$irc, &$data)
    {
        // if _we_ join, don't greet ourself, just jump out via return
        if ($data->nick == $irc->_nick)
            return;
        
        // now check if this is the right channel
        if ($data->channel == '#test')
            // it is, lets greet the joint user
            $irc->message(SMARTIRC_TYPE_CHANNEL, '#test', 'hi '.$data->nick);
    }
}

$bot = &new mybot();
$irc = &new Net_SmartIRC();
$irc->setDebug(SMARTIRC_DEBUG_ALL);
$irc->setUseSockets(TRUE);
$irc->registerActionhandler(SMARTIRC_TYPE_JOIN, '.*', $bot, 'onjoin_greeting');
$irc->connect('irc.freenet.de', 6667);
$irc->login('Net_SmartIRC', 'Net_SmartIRC Client '.SMARTIRC_VERSION.' (example6.php)', 8, 'Net_SmartIRC');
$irc->join(array('#smartirc-test','#test'));
$irc->listen();
$irc->disconnect();
?>