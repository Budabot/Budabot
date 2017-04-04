<?php
/**
 * $Id$
 * $Revision$
 * $Author$
 * $Date$
 *
 * Copyright (c) 2002-2004 Mirco Bauer <meebey@meebey.net> <http://www.meebey.net>
 *
 * Full LGPL License: <http://www.gnu.org/licenses/lgpl.txt>
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

abstract class Net_SmartIRC_irccommands
{
    /**
     * sends a new message
     *
     * Sends a message to a channel or user.
     *
     * @see DOCUMENTATION
     * @param integer $type specifies the type, like QUERY/ACTION or CTCP see 'Message Types'
     * @param string $destination can be a user or channel
     * @param mixed $messagearray the message
     * @param integer $priority the priority level of the message
     * @return boolean|Net_SmartIRC
     * @api
     */
    public function message($type, $destination, $messagearray,
        $priority = SMARTIRC_MEDIUM
    ) {
        if (!is_array($messagearray)) {
            $messagearray = array($messagearray);
        }

        switch ($type) {
            case SMARTIRC_TYPE_CHANNEL:
            case SMARTIRC_TYPE_QUERY:
                foreach ($messagearray as $message) {
                    $this->send('PRIVMSG '.$destination.' :'.$message, $priority);
                }
                break;

            case SMARTIRC_TYPE_ACTION:
                foreach ($messagearray as $message) {
                    $this->send('PRIVMSG '.$destination.' :'.chr(1).'ACTION '
                        .$message.chr(1), $priority
                    );
                }
                break;

            case SMARTIRC_TYPE_NOTICE:
                foreach ($messagearray as $message) {
                    $this->send('NOTICE '.$destination.' :'.$message, $priority);
                }
                break;

            case SMARTIRC_TYPE_CTCP: // backwards compatibilty
            case SMARTIRC_TYPE_CTCP_REPLY:
                foreach ($messagearray as $message) {
                    $this->send('NOTICE '.$destination.' :'.chr(1).$message
                        .chr(1), $priority
                    );
                }
                break;

            case SMARTIRC_TYPE_CTCP_REQUEST:
                foreach ($messagearray as $message) {
                    $this->send('PRIVMSG '.$destination.' :'.chr(1).$message
                        .chr(1), $priority
                    );
                }
                break;

            default:
                return false;
        }

        return $this;
    }

    // <IRC methods>
    /**
     * Joins one or more IRC channels with an optional key.
     *
     * @param mixed $channelarray
     * @param string $key
     * @param integer $priority message priority, default is SMARTIRC_MEDIUM
     * @return Net_SmartIRC
     * @api
     */
    public function join($channelarray, $key = null, $priority = SMARTIRC_MEDIUM)
    {
        if (!is_array($channelarray)) {
            $channelarray = array($channelarray);
        }

        $channellist = implode(',', $channelarray);

        if ($key !== null) {
            foreach ($channelarray as $idx => $value) {
                $this->send('JOIN '.$value.' '.$key, $priority);
            }
        } else {
            foreach ($channelarray as $idx => $value) {
                $this->send('JOIN '.$value, $priority);
            }
        }

        return $this;
    }

    /**
     * parts from one or more IRC channels with an optional reason
     *
     * @param mixed $channelarray
     * @param string $reason
     * @param integer $priority message priority, default is SMARTIRC_MEDIUM
     * @return Net_SmartIRC
     * @api
     */
    public function part($channelarray, $reason = null,
        $priority = SMARTIRC_MEDIUM
    ) {
        if (!is_array($channelarray)) {
            $channelarray = array($channelarray);
        }

        $channellist = implode(',', $channelarray);

        if ($reason !== null) {
            $this->send('PART '.$channellist.' :'.$reason, $priority);
        } else {
            $this->send('PART '.$channellist, $priority);
        }
        return $this;
    }

    /**
     * Kicks one or more user from an IRC channel with an optional reason.
     *
     * @param string $channel
     * @param mixed $nicknamearray
     * @param string $reason
     * @param integer $priority message priority, default is SMARTIRC_MEDIUM
     * @return Net_SmartIRC
     * @api
     */
    public function kick($channel, $nicknamearray, $reason = null,
        $priority = SMARTIRC_MEDIUM
    ) {
        if (!is_array($nicknamearray)) {
            $nicknamearray = array($nicknamearray);
        }

        $nicknamelist = implode(',', $nicknamearray);

        if ($reason !== null) {
            $this->send('KICK '.$channel.' '.$nicknamelist.' :'.$reason, $priority);
        } else {
            $this->send('KICK '.$channel.' '.$nicknamelist, $priority);
        }
        return $this;
    }

    /**
     * gets a list of one ore more channels
     *
     * Requests a full channellist if $channelarray is not given.
     * (use it with care, usualy its a looooong list)
     *
     * @param mixed $channelarray
     * @param integer $priority message priority, default is SMARTIRC_MEDIUM
     * @return Net_SmartIRC
     * @api
     */
    public function getList($channelarray = null, $priority = SMARTIRC_MEDIUM)
    {
        if ($channelarray !== null) {
            if (!is_array($channelarray)) {
                $channelarray = array($channelarray);
            }

            $channellist = implode(',', $channelarray);
            $this->send('LIST '.$channellist, $priority);
        } else {
            $this->send('LIST', $priority);
        }
        return $this;
    }

    /**
     * requests all nicknames of one or more channels
     *
     * The requested nickname list also includes op and voice state
     *
     * @param mixed $channelarray
     * @param integer $priority message priority, default is SMARTIRC_MEDIUM
     * @return Net_SmartIRC
     * @api
     */
    public function names($channelarray = null, $priority = SMARTIRC_MEDIUM)
    {
        if ($channelarray !== null) {
            if (!is_array($channelarray)) {
                $channelarray = array($channelarray);
            }

            $channellist = implode(',', $channelarray);
            $this->send('NAMES '.$channellist, $priority);
        } else {
            $this->send('NAMES', $priority);
        }
        return $this;
    }

    /**
     * sets a new topic of a channel
     *
     * @param string $channel
     * @param string $newtopic
     * @param integer $priority message priority, default is SMARTIRC_MEDIUM
     * @return Net_SmartIRC
     * @api
     */
    public function setTopic($channel, $newtopic, $priority = SMARTIRC_MEDIUM)
    {
        $this->send('TOPIC '.$channel.' :'.$newtopic, $priority);
        return $this;
    }

    /**
     * gets the topic of a channel
     *
     * @param string $channel
     * @param integer $priority message priority, default is SMARTIRC_MEDIUM
     * @return Net_SmartIRC
     * @api
     */
    public function getTopic($channel, $priority = SMARTIRC_MEDIUM)
    {
        $this->send('TOPIC '.$channel, $priority);
        return $this;
    }

    /**
     * sets or gets the mode of an user or channel
     *
     * Changes/requests the mode of the given target.
     *
     * @param string $target the target, can be an user (only yourself) or a channel
     * @param string $newmode the new mode like +mt
     * @param integer $priority message priority, default is SMARTIRC_MEDIUM
     * @return Net_SmartIRC
     * @api
     */
    public function mode($target, $newmode = null, $priority = SMARTIRC_MEDIUM)
    {
        if ($newmode !== null) {
            $this->send('MODE '.$target.' '.$newmode, $priority);
        } else {
            $this->send('MODE '.$target, $priority);
        }
        return $this;
    }

    /**
     * founders an user in the given channel
     *
     * @param string $channel
     * @param string $nickname
     * @param integer $priority message priority, default is SMARTIRC_MEDIUM
     * @return Net_SmartIRC
     * @api
     */
    public function founder($channel, $nickname, $priority = SMARTIRC_MEDIUM)
    {
        return $this->mode($channel, '+q '.$nickname, $priority);
    }

    /**
     * defounders an user in the given channel
     *
     * @param string $channel
     * @param string $nickname
     * @param integer $priority message priority, default is SMARTIRC_MEDIUM
     * @return Net_SmartIRC
     * @api
     */
    public function defounder($channel, $nickname, $priority = SMARTIRC_MEDIUM)
    {
        return $this->mode($channel, '-q '.$nickname, $priority);
    }

    /**
     * admins an user in the given channel
     *
     * @param string $channel
     * @param string $nickname
     * @param integer $priority message priority, default is SMARTIRC_MEDIUM
     * @return Net_SmartIRC
     * @api
     */
    public function admin($channel, $nickname, $priority = SMARTIRC_MEDIUM)
    {
        return $this->mode($channel, '+a '.$nickname, $priority);
    }

    /**
     * deadmins an user in the given channel
     *
     * @param string $channel
     * @param string $nickname
     * @param integer $priority message priority, default is SMARTIRC_MEDIUM
     * @return Net_SmartIRC
     * @api
     */
    public function deadmin($channel, $nickname, $priority = SMARTIRC_MEDIUM)
    {
        return $this->mode($channel, '-a '.$nickname, $priority);
    }

    /**
     * ops an user in the given channel
     *
     * @param string $channel
     * @param string $nickname
     * @param integer $priority message priority, default is SMARTIRC_MEDIUM
     * @return Net_SmartIRC
     * @api
     */
    public function op($channel, $nickname, $priority = SMARTIRC_MEDIUM)
    {
        return $this->mode($channel, '+o '.$nickname, $priority);
    }

    /**
     * deops an user in the given channel
     *
     * @param string $channel
     * @param string $nickname
     * @param integer $priority message priority, default is SMARTIRC_MEDIUM
     * @return Net_SmartIRC
     * @api
     */
    public function deop($channel, $nickname, $priority = SMARTIRC_MEDIUM)
    {
        return $this->mode($channel, '-o '.$nickname, $priority);
    }

    /**
     * hops an user in the given channel
     *
     * @param string $channel
     * @param string $nickname
     * @param integer $priority message priority, default is SMARTIRC_MEDIUM
     * @return Net_SmartIRC
     * @api
     */
    public function hop($channel, $nickname, $priority = SMARTIRC_MEDIUM)
    {
        return $this->mode($channel, '+h '.$nickname, $priority);
    }

    /**
     * dehops an user in the given channel
     *
     * @param string $channel
     * @param string $nickname
     * @param integer $priority message priority, default is SMARTIRC_MEDIUM
     * @return Net_SmartIRC
     * @api
     */
    public function dehop($channel, $nickname, $priority = SMARTIRC_MEDIUM)
    {
        return $this->mode($channel, '-h '.$nickname, $priority);
    }

    /**
     * voice a user in the given channel
     *
     * @param string $channel
     * @param string $nickname
     * @param integer $priority message priority, default is SMARTIRC_MEDIUM
     * @return Net_SmartIRC
     * @api
     */
    public function voice($channel, $nickname, $priority = SMARTIRC_MEDIUM)
    {
        return $this->mode($channel, '+v '.$nickname, $priority);
    }

    /**
     * devoice a user in the given channel
     *
     * @param string $channel
     * @param string $nickname
     * @param integer $priority message priority, default is SMARTIRC_MEDIUM
     * @return Net_SmartIRC
     * @api
     */
    public function devoice($channel, $nickname, $priority = SMARTIRC_MEDIUM)
    {
        return $this->mode($channel, '-v '.$nickname, $priority);
    }

    /**
     * bans a hostmask for the given channel or requests the current banlist
     *
     * The banlist will be requested if no hostmask is specified
     *
     * @param string $channel
     * @param string $hostmask
     * @param integer $priority message priority, default is SMARTIRC_MEDIUM
     * @return Net_SmartIRC
     * @api
     */
    public function ban($channel, $hostmask = null, $priority = SMARTIRC_MEDIUM)
    {
        if ($hostmask !== null) {
            $this->mode($channel, '+b '.$hostmask, $priority);
        } else {
            $this->mode($channel, 'b', $priority);
        }
        return $this;
    }

    /**
     * unbans a hostmask on the given channel
     *
     * @param string $channel
     * @param string $hostmask
     * @param integer $priority message priority, default is SMARTIRC_MEDIUM
     * @return Net_SmartIRC
     * @api
     */
    public function unban($channel, $hostmask, $priority = SMARTIRC_MEDIUM)
    {
        return $this->mode($channel, '-b '.$hostmask, $priority);
    }

    /**
     * invites a user to the specified channel
     *
     * @param string $nickname
     * @param string $channel
     * @param integer $priority message priority, default is SMARTIRC_MEDIUM
     * @return Net_SmartIRC
     * @api
     */
    public function invite($nickname, $channel, $priority = SMARTIRC_MEDIUM)
    {
        return $this->send('INVITE '.$nickname.' '.$channel, $priority);
    }

    /**
     * changes the own nickname
     *
     * Trys to set a new nickname, nickcollisions are handled.
     *
     * @param string $newnick
     * @param integer $priority message priority, default is SMARTIRC_MEDIUM
     * @return Net_SmartIRC
     * @api
     */
    public function changeNick($newnick, $priority = SMARTIRC_MEDIUM)
    {
        $this->_nick = $newnick;
        return $this->send('NICK '.$newnick, $priority);
    }

    /**
     * requests a 'WHO' from the specified target
     *
     * @param string $target
     * @param integer $priority message priority, default is SMARTIRC_MEDIUM
     * @return Net_SmartIRC
     * @api
     */
    public function who($target, $priority = SMARTIRC_MEDIUM)
    {
        return $this->send('WHO '.$target, $priority);
    }

    /**
     * requests a 'WHOIS' from the specified target
     *
     * @param string $target
     * @param integer $priority message priority, default is SMARTIRC_MEDIUM
     * @return Net_SmartIRC
     * @api
     */
    public function whois($target, $priority = SMARTIRC_MEDIUM)
    {
        return $this->send('WHOIS '.$target, $priority);
    }

    /**
     * requests a 'WHOWAS' from the specified target
     * (if he left the IRC network)
     *
     * @param string $target
     * @param integer $priority message priority, default is SMARTIRC_MEDIUM
     * @return Net_SmartIRC
     * @api
     */
    public function whowas($target, $priority = SMARTIRC_MEDIUM)
    {
        return $this->send('WHOWAS '.$target, $priority);
    }

    /**
     * sends QUIT to IRC server and disconnects
     *
     * @param string $quitmessage optional quitmessage
     * @param integer $priority message priority, default is SMARTIRC_CRITICAL
     * @return Net_SmartIRC
     * @api
     */
    public function quit($quitmessage = null, $priority = SMARTIRC_CRITICAL)
    {
        if ($quitmessage !== null) {
            $this->send('QUIT :'.$quitmessage, $priority);
        } else {
            $this->send('QUIT', $priority);
        }

        return $this->disconnect(true);
    }
}
