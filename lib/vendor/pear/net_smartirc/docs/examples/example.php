<?php
/**
 * $Id$
 * $Revision$
 * $Author$
 * $Date$
 *
 * Copyright (c) 2002-2003 Mirco "MEEBEY" Bauer <mail@meebey.net> <http://www.meebey.net>
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
// this code shows how a mini php bot which could be written
include_once('Net/SmartIRC.php');

class Net_SmartIRC_module_MyBot
{
    public $name        = 'MyBot';
    public $description = 'My custom bot with two action handlers';
    public $author      = 'My Name';
    public $license     = 'LGPL';

    private $irc;
    private $handlerids;

    public function __construct($irc)
    {
        $this->irc = $irc;
        $this->handlerids = array(
            $irc->registerActionHandler(SMARTIRC_TYPE_QUERY|SMARTIRC_TYPE_NOTICE, '^test', array($this, 'query_test')),
            $irc->registerActionHandler(SMARTIRC_TYPE_CHANNEL, '^test', function($irc, $data)
                {
                    $irc->message(SMARTIRC_TYPE_CHANNEL, $data->channel, $data->nick.': I dont like tests!');
                }
            ),
        );
    }

    public function __destruct()
    {
        $this->irc->unregisterActionId($this->handlerids);
    }

    public function query_test($irc, $data)
    {
        // result is sent to #smartirc-test
        $irc->message(SMARTIRC_TYPE_CHANNEL, '#smartirc-test', $data->nick.' said "'.$data->message.'" to me!');
        $irc->message(SMARTIRC_TYPE_QUERY, $data->nick, 'I told everyone on #smartirc-test what you said!');
    }
}

$irc = new Net_SmartIRC(array(
    'DebugLevel' => SMARTIRC_DEBUG_ALL,
));
$irc->loadModule('MyBot')
    ->connect('ssl://chat.freenode.net', 6697)
    ->login('Net_SmartIRC', 'example.php', 0, 'Net_SmartIRC')
    ->join(array('#smartirc-test'))
    ->listen()
    ->disconnect();
