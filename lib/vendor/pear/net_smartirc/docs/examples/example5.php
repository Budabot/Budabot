<?php
/**
 * $Id$
 * $Revision$
 * $Author$
 * $Date$
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

class MyBot
{
    private $irc;
    private $handlerid;

    public function __construct($irc)
    {
        $this->irc = $irc;
        $this->handlerid = $irc->registerActionHandler(SMARTIRC_TYPE_CHANNEL, '^!kick', array($this, 'kick'));
    }

    public function __destruct()
    {
        $this->irc->unregisterActionId($this->handlerid);
    }

    public function kick($irc, $data)
    {
        // we need the nickname parameter
        if(isset($data->messageex[1])) {
            $nickname = $data->messageex[1];
            $channel = $data->channel;
            $irc->kick($channel, $nickname);
        } else {
            $irc->message($data->type, $data->nick, 'wrong parameter count');
            $irc->message($data->type, $data->nick, 'usage: !kick $nickname');
        }
    }
}

$irc = new Net_SmartIRC(array(
    'DebugLevel' => SMARTIRC_DEBUG_ALL,
));
$bot = new MyBot($irc);
$irc->connect('chat.freenode.net', 6667);
$irc->login('Net_SmartIRC', 'Net_SmartIRC Client '.SMARTIRC_VERSION.' (example5.php)', 8, 'Net_SmartIRC');
$irc->join('#test');
$irc->listen();
$irc->disconnect();
