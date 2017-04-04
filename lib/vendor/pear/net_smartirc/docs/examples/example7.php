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
    private $actionid;
    private $timeids;

    public function __construct($irc)
    {
        $this->irc = $irc;
        $this->timeids = array(
            // register this to be called every 30 sec. (30,000 milliseconds)
            $irc->registerTimeHandler(30000, function($irc)
                {
                    $irc->message(SMARTIRC_TYPE_CHANNEL, '#smartirc-test', 'the time is: '.date('H:i:s'));
                }
            ),

            // register saytime_once() to be called in 10 sec. (10,000 milliseconds) and save the assigned id
            // which is needed for unregistering the timehandler.
            $irc->registerTimeHandler(10000, array($this, 'saytime_once')),
        );

        $this->actionid = $irc->registerActionHandler(SMARTIRC_TYPE_CHANNEL, '^!quit', function($irc)
            {
                $irc->quit("time to say goodbye...");
            }
        );
    }

    public function __destruct()
    {
        $this->irc->unregisterActionId($this->actionid);
        $this->irc->unregisterTimeId($this->timeids);
    }

    public function saytime_once($irc)
    {
        $irc->message(SMARTIRC_TYPE_CHANNEL, '#smartirc-test', '(once) the time is: '.date('H:i:s'));
        $irc->unregisterTimeId($this->timeids[1]);
        unset($this->timeids[1]);
    }
}

$irc = new Net_SmartIRC(array(
    'DebugLevel' => SMARTIRC_DEBUG_ALL,
));
$bot = new MyBot($irc);
$irc->connect('chat.freenode.net', 6667);
$irc->login('Net_SmartIRC', 'Net_SmartIRC Client '.SMARTIRC_VERSION.' (example7.php)', 8, 'Net_SmartIRC');
$irc->join(array('#smartirc-test','#test'));
$irc->listen();
$irc->disconnect();
