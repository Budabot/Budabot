<?php

  /*
   * $Id: aochat.php,v 1.1 2006/12/08 15:17:54 genesiscl Exp $
   *
   * Modified to handle the recent problem with the integer overflow
   *
   * Copyright (C) 2002-2005  Oskari Saarenmaa <auno@auno.org>.
   *
   * AOChat, a PHP class for talking with the Anarchy Online chat servers.
   * It requires the sockets extension (to connect to the chat server..)
   * from PHP 4.2.0+ and either the GMP or BCMath extension (for generating
   * and calculating the login keys) to work.
   *
   * A disassembly of the official java chat client[1] for Anarchy Online
   * and Slicer's AO::Chat perl module[2] were used as a reference for this
   * class.
   *
   * [1]: <http://www.anarchy-online.com/content/community/forumsandchat/>
   * [2]: <http://www.hackersquest.org/ao/>
   *
   * Updates to this class can be found from the following web site:
   *   http://auno.org/dev/aochat.html
   *
   **************************************************************************
   *
   * This program is free software; you can redistribute it and/or modify
   * it under the terms of the GNU General Public License as published by
   * the Free Software Foundation; either version 2 of the License, or
   * (at your option) any later version.
   *
   * This program is distributed in the hope that it will be useful, but
   * WITHOUT ANY WARRANTY; without even the implied warranty of
   * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
   * General Public License for more details.
   *
   * You should have received a copy of the GNU General Public License
   * along with this program; if not, write to the Free Software
   * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307
   * USA
   *
   */


  if((float)phpversion() < 5.0)
  {
    die("AOChat class needs PHP version >= 5.0.0 to work.\n");
  }

  if(!extension_loaded("sockets"))
  {
    die("AOChat class needs the Sockets extension to work.\n");
  }

  if(!extension_loaded("gmp") &&
     !extension_loaded("bcmath") &&
     !extension_loaded("aokex"))
  {
    die("AOChat class needs either AOkex, GMP or BCMath extension to work.\n");
  }

  set_time_limit(0);
  ini_set("html_errors", 0);

  /* Packet type definitions - so we won't have to use the number IDs
   * .. I did not distinct between server and client message types, as
   * they are mostly the same for same type packets, but maybe it should
   * have been done anyway..  // auno - 2004/mar/26
   */
  define('AOCP_LOGIN_SEED',		0);
  define('AOCP_LOGIN_REQUEST',		2);
  define('AOCP_LOGIN_SELECT',		3);
  define('AOCP_LOGIN_OK',		5);
  define('AOCP_LOGIN_ERROR',		6);
  define('AOCP_LOGIN_CHARLIST',		7);
  define('AOCP_CLIENT_UNKNOWN',		10);
  define('AOCP_CLIENT_NAME',		20);
  define('AOCP_CLIENT_LOOKUP',		21);
  define('AOCP_MSG_PRIVATE',		30);
  define('AOCP_MSG_VICINITY',		34);
  define('AOCP_MSG_VICINITYA',		35);
  define('AOCP_MSG_SYSTEM',		36);
  define('AOCP_CHAT_NOTICE',		37);
  define('AOCP_BUDDY_ADD',		40);
  define('AOCP_BUDDY_REMOVE',		41);
  define('AOCP_ONLINE_SET',		42);
  define('AOCP_PRIVGRP_INVITE',		50);
  define('AOCP_PRIVGRP_KICK',		51);
  define('AOCP_PRIVGRP_JOIN',		52);
  define('AOCP_PRIVGRP_PART',		53);
  define('AOCP_PRIVGRP_KICKALL',	54);
  define('AOCP_PRIVGRP_CLIJOIN',	55);
  define('AOCP_PRIVGRP_CLIPART',	56);
  define('AOCP_PRIVGRP_MESSAGE',	57);
  define('AOCP_PRIVGRP_REFUSE',		58);
  define('AOCP_GROUP_ANNOUNCE',		60);
  define('AOCP_GROUP_PART',		61);
  define('AOCP_GROUP_DATA_SET',		64);
  define('AOCP_GROUP_MESSAGE',		65);
  define('AOCP_GROUP_CM_SET',		66);
  define('AOCP_CLIENTMODE_GET',		70);
  define('AOCP_CLIENTMODE_SET',		71);
  define('AOCP_PING',			100);
  define('AOCP_FORWARD',		110);
  define('AOCP_CC',			120);
  define('AOCP_ADM_MUX_INFO',		1100);

  define('AOCP_GROUP_JOIN',		AOCP_GROUP_ANNOUNCE); /* compat */

  define('AOC_GROUP_NOWRITE',		0x00000002);
  define('AOC_GROUP_NOASIAN',		0x00000020);
  define('AOC_GROUP_MUTE',		0x01010000);
  define('AOC_GROUP_LOG',		0x02020000);

  define('AOC_BUDDY_KNOWN',		0x01);
  define('AOC_BUDDY_ONLINE',		0x02);

  define('AOC_FLOOD_LIMIT',		7);
  define('AOC_FLOOD_INC',		2);

  define('AOC_PRIORITY_HIGH',		1000);
  define('AOC_PRIORITY_MED',		 500);
  define('AOC_PRIORITY_LOW',		 100);

  define('AOEM_UNKNOWN',                0xFF);
  define('AOEM_ORG_JOIN',               0x10);
  define('AOEM_ORG_KICK',               0x11);
  define('AOEM_ORG_LEAVE',              0x12);
  define('AOEM_ORG_DISBAND',            0x13);
  define('AOEM_ORG_FORM',               0x14);
  define('AOEM_ORG_VOTE',               0x15);
  define('AOEM_NW_ATTACK',              0x20);
  define('AOEM_NW_ABANDON',             0x21);
  define('AOEM_AI_CLOAK',               0x30);
  define('AOEM_AI_RADAR',               0x31);
  define('AOEM_AI_ATTACK',              0x32);
  define('AOEM_AI_REMOVE_INIT',         0x33);
  define('AOEM_AI_REMOVE',              0x34);
  define('AOEM_AI_HQ_REMOVE_INIT',      0x35);
  define('AOEM_AI_HQ_REMOVE',           0x36);

  class AOChat
  {
    var $state, $debug, $id, $gid, $chars, $char, $grp, $buddies;
    var $socket, $last_packet, $last_ping, $callback, $cbargs;
    var $serverseed, $tellqueue;

    /* Initialization */
    function AOChat($cb, $args = NULL)
    {
      $this->callback = $cb;
      $this->cbargs   = $args;
      $this->disconnect();
    }

    function disconnect()
    {
      if(is_resource($this->socket))
        socket_close($this->socket);
      $this->socket      = NULL;
      $this->serverseed  = NULL;
      $this->chars       = NULL;
      $this->char        = NULL;
      $this->last_packet = 0;
      $this->last_ping   = 0;
      $this->state       = "connect";
      $this->id          = array();
      $this->gid         = array();
      $this->grp         = array();
      $this->chars       = array();
      $this->buddies     = array();
      $this->tellqueue   = NULL;
      $this->groupqueue  = NULL;
    }

    /* Network stuff */
    function connect($server = "chat2.d1.funcom.com", $port = 7102)
    {
      if($this->state !== "connect")
        die("AOChat: not expecting connect.\n");

      $s = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
      if(!is_resource($s)) /* this is fatal */
        die("Could not create socket.\n");

      $this->socket = $s;
      $this->state = "auth";

      if(@socket_connect($s, $server, $port) === false)
      {
        trigger_error("Could not connect to the AO Chat server ($server:$port): ".
          socket_strerror(socket_last_error($s)), E_USER_WARNING);
        $this->disconnect();
        return false;
      }

      $packet = $this->get_packet();
      if(!is_object($packet) || $packet->type != AOCP_LOGIN_SEED)
      {
        trigger_error("Received invalid greeting packet from AO Chat server.", E_USER_WARNING);
        $this->disconnect();
        return false;
      }

      $this->tellqueue  = new AOChatQueue(array($this, 'dispatch_tell'), AOC_FLOOD_LIMIT, AOC_FLOOD_INC);
      $this->groupqueue = new AOChatQueue(array($this, 'dispatch_groupmsg'), AOC_FLOOD_LIMIT, AOC_FLOOD_INC);

      return $s;
    }

    function iteration()
    {
      $now = time();

      if($this->tellqueue !== NULL)
        $this->tellqueue->run();
      if($this->groupqueue !== NULL)
        $this->groupqueue->run();

      if(($now-$this->last_packet) > 60)
        if(($now-$this->last_ping) > 60)
          $this->send_ping();
    }

    function wait_for_packet($time = 1)
    {
      $this->iteration();

      $sec = (int)$time;
      if(is_float($time))
        $usec = (int)($time * 1000000 % 1000000);
      else
        $usec = 0;

      if(!socket_select($a = array($this->socket), $b = null, $c = null, $sec, $usec))
        return NULL;
      else
        return $this->get_packet();
    }

    function read_data($len)
    {
      $data = "";
      $rlen = $len;
      while($rlen > 0)
      {
        if(($tmp = socket_read($this->socket, $rlen)) === false)
        {
          $last_error = socket_strerror(socket_last_error($this->socket));
          die("Read error: $last_error\n");
          $this->disconnect();
          return "";
        }
        if($tmp == "")
        {
          die("Read error: EOF\n");
          $this->disconnect();
          return "";
        }
        $data .= $tmp;
        $rlen -= strlen($tmp);
      }
      return $data;
    }
    
    function get_packet()
    {
      $head = $this->read_data(4);
      if(strlen($head) != 4)
        return false;

      list(, $type, $len) = unpack("n2", $head);

      $data = $this->read_data($len);

      if(is_resource($this->debug))
      {
        fwrite($this->debug, "<<<<<\n");
        fwrite($this->debug, $head);
        fwrite($this->debug, $data);
        fwrite($this->debug, "\n=====\n");
      }

      $packet = new AOChatPacket("in", $type, $data);

      switch($type)
      {
        case AOCP_LOGIN_SEED :
          $this->serverseed = $packet->args[0];
          break;

        case AOCP_CLIENT_NAME :
        case AOCP_CLIENT_LOOKUP :
          list($id, $name) = $packet->args;
          $id   = "" . $id;
          $name = ucfirst(strtolower($name));
          $this->id[$id]   = $name;
          $this->id[$name] = $id;
          break;

        case AOCP_GROUP_ANNOUNCE :
          list($gid, $name, $status) = $packet->args;
          $this->grp[$gid] = $status;
          $this->gid[$gid] = $name;
          $this->gid[strtolower($name)] = $gid;
          break;

        case AOCP_GROUP_MESSAGE :
          /* Hack to support extended messages */
          if($packet->args[1] === 0 && substr($packet->args[2], 0, 2) == "~&")
          {
            $em = new AOExtMsg($packet->args[2]);
            if($em->type != AOEM_UNKNOWN)
            {
              $packet->args[2] = $em->text;
              $packet->args[] = $em;
            }
          }
          break;

        case AOCP_BUDDY_ADD :
          list($bid, $bonline, $btype) = $packet->args;
          $this->buddies[$bid] = ($bonline    ? AOC_BUDDY_ONLINE : 0)|
                                 (ord($btype) ? AOC_BUDDY_KNOWN  : 0);
          break;

        case AOCP_BUDDY_REMOVE :
          unset($this->buddies[$packet->args[0]]);
          break;
      }

      $this->last_packet = time();

      if(is_callable($this->callback))
      {
		call_user_func($this->callback, $packet->type, $packet->args, $this->cbargs);
      }

      return $packet;
    }

    function send_packet($packet)
    {
      $data = pack("n2", $packet->type, strlen($packet->data)) . $packet->data;
      if(is_resource($this->debug))
      {
        fwrite($this->debug, ">>>>>\n");
        fwrite($this->debug, $data);
        fwrite($this->debug, "\n=====\n");
      }
      socket_write($this->socket, $data, strlen($data));
      return true;
    }

    /* Login functions */
    function authenticate($username, $password)
    {
      if($this->state != "auth")
        die("AOChat: not expecting authentication.\n");

      if(extension_loaded("aokex"))
        $key = aokex_login_key($this->serverseed, $username, $password);
      else
        $key = $this->generate_login_key($this->serverseed, $username, $password);
      $pak = new AOChatPacket("out", AOCP_LOGIN_REQUEST, array(0, $username, $key));
      $this->send_packet($pak);
      $packet = $this->get_packet();
      if($packet->type != AOCP_LOGIN_CHARLIST)
      {
        return false;
      }

      for($i=0;$i<sizeof($packet->args[0]);$i++)
      {
        $this->chars[] = array(
          "id"     => $packet->args[0][$i],
          "name"   => ucfirst(strtolower($packet->args[1][$i])),
          "level"  => $packet->args[2][$i],
          "online" => $packet->args[3][$i]);
      }

      $this->username = $username;
      $this->state    = "login";

      return $this->chars;
    }

    function login($char)
    {
      if($this->state != "login")
        die("AOChat: not expecting login.\n");

      if(is_int($char))
      {
        $field = "id";
      }
      else if(is_string($char))
      {
        $field = "name";
        $char  = ucfirst(strtolower($char));
      }

      if(!is_array($char))
      {
        if(empty($field))
        {
          return false;
        }
        else
        {
          foreach($this->chars as $e)
          {
            if($e[$field] == $char)
            {
              $char = $e;
              break;
            }
          }
        }
      }

      if(!is_array($char))
      {
        echo "AOChat: no valid character to login.\n";
        return false;
      }

      $pq = new AOChatPacket("out", AOCP_LOGIN_SELECT, $char["id"]);
      $this->send_packet($pq);
      $pr = $this->get_packet();
      if($pr->type != AOCP_LOGIN_OK)
      {
        return false;
      }

      $this->char  = $char;
      $this->state = "ok";

      return true;
    }

    /* User and group lookup functions */
    function lookup_user($u)
    {
      $u = ucfirst(strtolower($u));

      if(isset($this->id[$u]))
        return $this->id[$u];

      $this->send_packet(new AOChatPacket("out", AOCP_CLIENT_LOOKUP, $u));
      for($i=0; $i<100 && !isset($this->id[$u]); $i++)
        $this->get_packet();

      return isset($this->id[$u]) ? $this->id[$u] : false;
    }

    function get_uid($user)
    {
      if($this->is_really_numeric($user)) 
        return $this->fixunsigned($user); 

      $uid = $this->lookup_user($user); 

      if(($uid == 0) || ($uid == 0xffffffff) || (!$this->is_really_numeric($uid)) || ($uid == -1)) 
        return false; 

      return $uid;
    }

    function fixunsigned($num) 
    { 
	    if ($this->is_really_numeric($num) && bcdiv("" . $num, "2147483648", 0)) 
	    { 
	        $num2 = -1 * bcsub("4294967296", "" . $num); 
	        return (int)$num2; 
	    } 

	    return (int)$num; 
    } 

    function is_really_numeric($num) 
    { 
	    if(preg_match("/^([0-9\-]+)$/", "" . $num))
			return true; 

	    return false; 
    }

    function get_uname($user)
    {
      if(!($uid = (int)$user))
        return $user;
      else
        return $this->lookup_user($uid);
    }

    function lookup_group($arg, $type=0)
    {
      if($type && ($is_gid = (strlen($arg) === 5 && (ord($arg[0])&~0x80) < 0x10)))
        return $arg;
      if(!$is_gid)
        $arg = strtolower($arg);
      return isset($this->gid[$arg]) ? $this->gid[$arg] : false;
    }

    function get_gid($g)
    {
      return $this->lookup_group($g, 1);
    }

    function get_gname($g)
    {
      if(($gid = $this->lookup_group($g, 1)) === false)
        return false;
      return $this->gid[$gid];
    }

    /* Sending various packets */
    function send_ping()
    {
      $this->last_ping = time();
      return $this->send_packet(new AOChatPacket("out", AOCP_PING, "AOChat.php"));
    }

    function dispatch_tell($tgt, $msg)
    {
      if(($uid = $this->get_uid($tgt)) === false)
        return false;

      return $this->send_packet(new AOChatPacket("out", AOCP_MSG_PRIVATE,
        array($uid, $msg, "\0")));
    }

    function send_tell($user, $msg, $blob = "\0")
    {
      $this->tellqueue->push(AOC_PRIORITY_MED, $user, $msg);
      return true;
    }

    /* General chat groups */
    function dispatch_groupmsg($group, $msg)
    {
      if(($gid = $this->get_gid($group)) === false)
        return false;

      return $this->send_packet(new AOChatPacket("out", AOCP_GROUP_MESSAGE,
        array($gid, $msg, "\0")));
    }

    function send_group($group, $msg, $blob = "\0")
    {
      $this->groupqueue->push(AOC_PRIORITY_MED, $group, $msg);
      return true;
    }

    function group_join($group)
    {
      if(($gid = $this->get_gid($group)) === false)
        return false;

      return $this->send_packet(new AOChatPacket("out", AOCP_GROUP_DATA_SET,
        array($gid, $this->grp[$gid] & ~AOC_GROUP_MUTE, "\0")));
    }

    function group_leave($group)
    {
      if(($gid = $this->get_gid($group)) === false)
        return false;

      return $this->send_packet(new AOChatPacket("out", AOCP_GROUP_DATA_SET,
        array($gid, $this->grp[$gid] | AOC_GROUP_MUTE, "\0")));
    }

    function group_status($group)
    {
      if(($gid = $this->get_gid($group)) === false)
        return false;

      return $this->grp[$gid];
    }

    /* Private chat groups */
    function send_privgroup($group, $msg, $blob = "\0")
    {
      if(($gid = $this->get_uid($group)) === false)
        return false;

      return $this->send_packet(new AOChatPacket("out", AOCP_PRIVGRP_MESSAGE,
        array($gid, $msg, $blob)));
    }

    function privategroup_join($group)
    {
      if(($gid = $this->get_uid($group)) === false)
        return false;

      return $this->send_packet(new AOChatPacket("out", AOCP_PRIVGRP_JOIN, $gid));
    }
    function join_privgroup($group) /* Deprecated - 2004/Mar/26 - auno@auno.org */
    {
      return $this->privategroup_join($group);
    }

    function privategroup_invite($user)
    {
      if(($uid = $this->get_uid($user)) === false)
        return false;

      return $this->send_packet(new AOChatPacket("out", AOCP_PRIVGRP_INVITE, $uid));
    }

    function privategroup_kick($user)
    {
      if(($uid = $this->get_uid($user)) === false)
        return false;

      return $this->send_packet(new AOChatPacket("out", AOCP_PRIVGRP_KICK, $uid));
    }

    function privategroup_kick_all()
    {
      return $this->send_packet(new AOChatPacket("out", AOCP_PRIVGRP_KICKALL, ""));
    }

    /* Buddies */
    function buddy_add($user, $type="\1")
    {
      if(($uid = $this->get_uid($user)) === false)
        return false;

      if($uid === $this->char['id'])
        return false;

      return $this->send_packet(new AOChatPacket("out", AOCP_BUDDY_ADD,
        array($uid, $type)));
    }

    function buddy_remove($user)
    {
      if(($uid = $this->get_uid($user)) === false)
        return false;

      return $this->send_packet(new AOChatPacket("out", AOCP_BUDDY_REMOVE, $uid));
    }

    function buddy_remove_unknown()
    {
      return $this->send_packet(new AOChatPacket("out", AOCP_CC,
        array(array("rembuddy", "?"))));
    }

    function buddy_exists($who)
    {
      if(($uid = $this->get_uid($who)) === false)
        return false;
      return (int)$this->buddies[$uid];
    }

    function buddy_online($who)
    {
      return ($this->buddy_exists($who) & AOC_BUDDY_ONLINE) ? true : false;
    }

    /* Login key generation and encryption */
    function get_random_hex_key($bits)
    {
      $str = "";
      do
        $str .= sprintf('%02x', mt_rand(0, 0xff));
      while(($bits -= 8) > 0);
      return $str;
    }

    function bighexdec($x)
    {
      if(substr($x, 0, 2) != "0x")
        return $x;
      $r = "0";
      for($p = $q = strlen($x) - 1; $p >= 2; $p--)
      {
        $r = bcadd($r, bcmul(hexdec($x[$p]), bcpow(16, $q - $p)));
      }
      return $r;
    }

    function bigdechex($x)
    {
      $r = "";
      while($x != "0")
      {
        $r = dechex(bcmod($x, 16)) . $r;
        $x = bcdiv($x, 16);
      }
      return $r;
    }

    function bcmath_powm($base, $exp, $mod)
    {
      $base = $this->bighexdec($base);
      $exp  = $this->bighexdec($exp);
      $mod  = $this->bighexdec($mod);

      if(function_exists("bcpowmod")) /* PHP5 finally has this */
      {
        $r = bcpowmod($base, $exp, $mod);
        return $this->bigdechex($r);
      }

      $r = 1;
      $p = $base;

      while(true)
      {
        if(bcmod($exp, 2))
        {
          $r = bcmod(bcmul($p, $r), $mod);
          $exp = bcsub($exp, "1");
          if(bccomp($exp, "0") == 0)
          {
            return $this->bigdechex($r);
          }
        }
        $exp = bcdiv($exp, 2);
        $p = bcmod(bcmul($p, $p), $mod);
      }
    }

    /* This is 'half' Diffie-Hellman key exchange.
     * 'Half' as in we already have the server's key ($dhY)
     * $dhN is a prime and $dhG is generator for it.
     *
     * http://en.wikipedia.org/wiki/Diffie-Hellman_key_exchange
     */
    function generate_login_key($servkey, $username, $password)
    {
      $dhY = "0x9c32cc23d559ca90fc31be72df817d0e124769e809f936bc14360ff4bed758f260a0d596584eacbbc2b88bdd410416163e11dbf62173393fbc0c6fefb2d855f1a03dec8e9f105bbad91b3437d8eb73fe2f44159597aa4053cf788d2f9d7012fb8d7c4ce3876f7d6cd5d0c31754f4cd96166708641958de54a6def5657b9f2e92";
      $dhN = "0xeca2e8c85d863dcdc26a429a71a9815ad052f6139669dd659f98ae159d313d13c6bf2838e10a69b6478b64a24bd054ba8248e8fa778703b418408249440b2c1edd28853e240d8a7e49540b76d120d3b1ad2878b1b99490eb4a2a5e84caa8a91cecbdb1aa7c816e8be343246f80c637abc653b893fd91686cf8d32d6cfe5f2a6f";
      $dhG = "0x5";
      $dhx = "0x".$this->get_random_hex_key(256);

      if(extension_loaded("gmp"))
      {
        $dhN = gmp_init($dhN);
        $dhX = gmp_strval(gmp_powm($dhG, $dhx, $dhN), 16);
        $dhK = gmp_strval(gmp_powm($dhY, $dhx, $dhN), 16);
      }
      else if(extension_loaded("bcmath"))
      {
        $dhX = $this->bcmath_powm($dhG, $dhx, $dhN);
        $dhK = $this->bcmath_powm($dhY, $dhx, $dhN);
      }
      else
      {
        die("generate_login_key(): no idea how to powm...\n");
      }

      $str = sprintf("%s|%s|%s", $username, $servkey, $password);

      if(strlen($dhK) < 32)
        $dhK = str_repeat("0", 32-strlen($dhK)) . $dhK;
      else
        $dhK = substr($dhK, 0, 32);

      $prefix = pack("H16", $this->get_random_hex_key(64));
      $length = 8 + 4 + strlen($str); /* prefix, int, ... */
      $pad    = str_repeat(" ", (8 - $length % 8) % 8);
      $strlen = pack("N", strlen($str));

      $plain   = $prefix . $strlen . $str . $pad;
      $crypted = $this->aochat_crypt($dhK, $plain);

      return $dhX . "-" . $crypted;
    }

    function aochat_crypt($key, $str)
    {
      if(strlen($key) != 32 || strlen($str) % 8 != 0)
      {
        return false;
      }

      $cycle  = array(0, 0);
      $result = array(0, 0);
      $ret    = "";

      $keyarr  = unpack("V*", pack("H*", $key));
      $dataarr = unpack("V*", $str);

      for($i=1; $i<=sizeof($dataarr); $i+=2)
      {
        $cycle[0] = $dataarr[$i]   ^ $result[0];
        $cycle[1] = $dataarr[$i+1] ^ $result[1];
        $result   = $this->aochat_tea_encrypt($cycle, $keyarr);
        $ret     .= array_pop(unpack("H*", pack("V*", $result[0], $result[1])));
      }

      return $ret;
    }

    /* TEA encryption
     * http://en.wikipedia.org/wiki/Tiny_Encryption_Algorithm
     */
    function aochat_tea_encrypt($cycle, $key)
    {
      $a = $cycle[0];
      $b = $cycle[1];
      $sum = 0;
      $delta = (int)0x9e3779b9;
      $i = 32;

      while($i--)
      {
        $sum = (int)($sum + $delta);
        $a += (($b << 4 & 0xfffffff0) + $key[1]) ^ ($b + $sum) ^ (($b >> 5 & 0x7ffffff) + $key[2]);
        $b += (($a << 4 & 0xfffffff0) + $key[3]) ^ ($a + $sum) ^ (($a >> 5 & 0x7ffffff) + $key[4]);
      }

      return array($a, $b);
    }
  }


  /* The AOChatPacket class - turning packets into binary blobs and
   * binary blobs into packets.
   *
   * Data types:
   * I - 32 bit integer: uint32_t
   * S - 8 bit string array: uint16_t length, char str[length]
   * G - 40 bit binary data: unsigned char data[5]
   * i - integer array: uint16_t count, uint32_t[count]
   * s - string array: uint16_t count, aochat_str_t[count]
   *
   * D - 'data', we have relabeled all 'D' type fields to 'S'
   * M - mapping [see t.class in ao_nosign.jar] - unsupported
   *
   */


  class AOChatPacket
  {
    private static $packet_map = array(
      "in" => array(
        AOCP_LOGIN_SEED		=> array("name"=>"Login Seed",			"args"=>"S"),
        AOCP_LOGIN_OK		=> array("name"=>"Login Result OK",		"args"=>""),
        AOCP_LOGIN_ERROR	=> array("name"=>"Login Result Error",		"args"=>"S"),
        AOCP_LOGIN_CHARLIST	=> array("name"=>"Login CharacterList",		"args"=>"isii"),
        AOCP_CLIENT_UNKNOWN	=> array("name"=>"Client Unknown",		"args"=>"I"),
        AOCP_CLIENT_NAME	=> array("name"=>"Client Name",			"args"=>"IS"),
        AOCP_CLIENT_LOOKUP	=> array("name"=>"Lookup Result",		"args"=>"IS"),
        AOCP_MSG_PRIVATE	=> array("name"=>"Message Private",		"args"=>"ISS"),
        AOCP_MSG_VICINITY	=> array("name"=>"Message Vicinity",		"args"=>"ISS"),
        AOCP_MSG_VICINITYA	=> array("name"=>"Message Anon Vicinity",	"args"=>"SSS"),
        AOCP_MSG_SYSTEM		=> array("name"=>"Message System",		"args"=>"S"),
        AOCP_CHAT_NOTICE	=> array("name"=>"Chat Notice",			"args"=>"IIIS"),
        AOCP_BUDDY_ADD		=> array("name"=>"Buddy Added",			"args"=>"IIS"),
        AOCP_BUDDY_REMOVE	=> array("name"=>"Buddy Removed",		"args"=>"I"),
        AOCP_PRIVGRP_INVITE	=> array("name"=>"Privategroup Invited",	"args"=>"I"),
        AOCP_PRIVGRP_KICK	=> array("name"=>"Privategroup Kicked",		"args"=>"I"),
        AOCP_PRIVGRP_PART	=> array("name"=>"Privategroup Part",		"args"=>"I"),
        AOCP_PRIVGRP_CLIJOIN	=> array("name"=>"Privategroup Client Join",	"args"=>"II"),
        AOCP_PRIVGRP_CLIPART	=> array("name"=>"Privategroup Client Part",	"args"=>"II"),
        AOCP_PRIVGRP_MESSAGE	=> array("name"=>"Privategroup Message",	"args"=>"IISS"),
        AOCP_PRIVGRP_REFUSE	=> array("name"=>"Privategroup Refuse Invite",	"args"=>"II"),
        AOCP_GROUP_ANNOUNCE	=> array("name"=>"Group Announce",		"args"=>"GSIS"),
        AOCP_GROUP_PART		=> array("name"=>"Group Part",			"args"=>"G"),
        AOCP_GROUP_MESSAGE	=> array("name"=>"Group Message",		"args"=>"GISS"),
        AOCP_PING		=> array("name"=>"Pong",			"args"=>"S"),
        AOCP_FORWARD		=> array("name"=>"Forward",			"args"=>"IM"),
        AOCP_ADM_MUX_INFO	=> array("name"=>"Adm Mux Info",		"args"=>"iii"),
        ),
      "out" => array(
        AOCP_LOGIN_REQUEST	=> array("name"=>"Login Response GetCharLst",	"args"=>"ISS"),
        AOCP_LOGIN_SELECT	=> array("name"=>"Login Select Character",	"args"=>"I"),
        AOCP_CLIENT_LOOKUP	=> array("name"=>"Name Lookup",			"args"=>"S"),
        AOCP_MSG_PRIVATE	=> array("name"=>"Message Private",		"args"=>"ISS"),
        AOCP_BUDDY_ADD		=> array("name"=>"Buddy Add",			"args"=>"IS"),
        AOCP_BUDDY_REMOVE	=> array("name"=>"Buddy Remove",		"args"=>"I"),
        AOCP_ONLINE_SET		=> array("name"=>"Onlinestatus Set",		"args"=>"I"),
        AOCP_PRIVGRP_INVITE	=> array("name"=>"Privategroup Invite",		"args"=>"I"),
        AOCP_PRIVGRP_KICK	=> array("name"=>"Privategroup Kick",		"args"=>"I"),
        AOCP_PRIVGRP_JOIN	=> array("name"=>"Privategroup Join",		"args"=>"I"),
        AOCP_PRIVGRP_PART	=> array("name"=>"Privategroup Part",		"args"=>"I"),
        AOCP_PRIVGRP_KICKALL	=> array("name"=>"Privategroup Kickall",	"args"=>""),
        AOCP_PRIVGRP_MESSAGE	=> array("name"=>"Privategroup Message",	"args"=>"ISS"),
        AOCP_GROUP_DATA_SET	=> array("name"=>"Group Data Set",		"args"=>"GIS"),
        AOCP_GROUP_MESSAGE	=> array("name"=>"Group Message",		"args"=>"GSS"),
        AOCP_GROUP_CM_SET	=> array("name"=>"Group Clientmode Set",	"args"=>"GIIII"),
        AOCP_CLIENTMODE_GET	=> array("name"=>"Clientmode Get",		"args"=>"IG"),
        AOCP_CLIENTMODE_SET	=> array("name"=>"Clientmode Set",		"args"=>"IIII"),
        AOCP_PING		=> array("name"=>"Ping",			"args"=>"S"),
        AOCP_CC			=> array("name"=>"CC",				"args"=>"s"),
        ),
      );

    function AOChatPacket($dir, $type, $data)
    {
      $this->args = array();
      $this->type = $type;
      $this->dir  = $dir;
      $pmap = self::$packet_map[$dir][$type];

      if(!$pmap)
      {
        echo "Unsupported packet type (". $dir . ", " . $type . ")\n";
        return false;
      }

      if($dir == "in")
      {
        if(!is_string($data))
        {
          echo "Incorrect argument for incoming packet, expecting a string.\n";
          return false;
        }

        for($i=0; $i<strlen($pmap["args"]); $i++)
        {
          $sa = $pmap["args"][$i];
          switch($sa)
          {
            case "I" :
              $res  = array_pop(unpack("N", $data));
              $data = substr($data, 4);
              break;

            case "S" :
              $len  = array_pop(unpack("n", $data));
              $res  = substr($data, 2, $len);
              $data = substr($data, 2 + $len);
              break;

            case "G" :
              $res  = substr($data, 0, 5);
              $data = substr($data, 5);
              break;

            case "i" :
              $len  = array_pop(unpack("n", $data));
              $res  = array_values(unpack("N" . $len, substr($data, 2)));
              $data = substr($data, 2 + 4 * $len);
              break;

            case "s" :
              $len  = array_pop(unpack("n", $data));
              $data = substr($data, 2);
              $res  = array();
              while($len--)
              {
                $slen  = array_pop(unpack("n", $data));
                $res[] = substr($data, 2, $slen);
                $data  = substr($data, 2+$slen);
              }
              break;

            default :
              echo "Unknown argument type! (" . $sa . ")\n";
              continue(2);
          }
          $this->args[] = $res;
        }
      }
      else
      {
        if(!is_array($data))
        {
          $args = array($data);
        }
        else
        {
          $args = $data;
        }
        $data = "";

        for($i=0; $i<strlen($pmap["args"]); $i++)
        {
          $sa = $pmap["args"][$i];
          $it = array_shift($args);

          if(is_null($it))
          {
            echo "Missing argument for packet.\n";
            break;
          }

          switch($sa)
          {
            case "I" :
              $data .= pack("N", $it);
              break;

            case "S" :
              $data .= pack("n", strlen($it)) . $it;
              break;

            case "G" :
              $data .= $it;
              break;

            case "s" :
              $data .= pack("n", sizeof($it));
              foreach($it as $it_elem)
                $data .= pack("n", strlen($it_elem)) . $it_elem;
              break;

            default :
              echo "Unknown argument type! (" . $sa . ")\n";
              continue(2);
          }
        }

        $this->data = $data;
      }
      return true;
    }
  }
  
  /* New "extended" messages, parser and abstraction.
   * These were introduced in 16.1.  The messages use postscript
   * base85 encoding (not ipv6 / rfc 1924 base85).  They also use
   * some custom encoding and references to further confuse things.
   *
   * Messages start with the magic marker ~& and end with ~
   * Messages begin with two base85 encoded numbers that define
   * the category and instance of the message.  After that there
   * are an category/instance defined amount of variables which
   * are prefixed by the variable type.  A base85 encoded number
   * takes 5 bytes.  Variable types:
   *
   * s: string, first byte is the length of the string
   * i: signed integer (b85)
   * u: unsigned integer (b85)
   * f: float (b85)
   * R: reference, b85 category and instance
   * F: recursive encoding
   * ~: end of message
   *
   * Message categories:
   *  501 : More org messages
   *        0xad0ae9b : Organization leave because of alignment change
   *                    s(Char)
   *  506 : NW messages
   *        0x0c299d4 : Tower attack
   *                    R(Faction), s(Org), s(Char),
   *                    R(Faction), s(Org),
   *                    s(Zone), i(Zone-X), i(Zone-Y)
   *        0x8cac524 : Area abandoned
   *                    R(Faction), s(Org), s(Zone)
   *  508 : Org messages
   *        0x04e87e7 : Character joined the organization
   *                    s(Char)
   *        0x2360067 : Character was kicked
   *                    s(Kicker), s(Kicked)
   *        0x2bd9377 : Character has left
   *                    s(Char)
   *        0x8487156 : Change of governing form
   *                    s(Char), s(Form)
   *        0x88cc2e7 : Organization disbanded
   *                    s(Char)
   *        0xc477095 : Vote begins
   *                    s(Vote text), u(Minutes), s(Choices)
   * 1001 : AI messages
   *        0x01 : Cloak
   *               s(Char), s(Cloak status)
   *        0x02 : Radar alert
   *        0x03 : Alien attack
   *               s(Zone)
   *        0x04 : Org HQ removed
   *               s(Char), s(Zone)
   *        0x05 : Building removal initiated
   *               s(Char), R(House type), s(Zone)
   *        0x06 : Building removed
   *               s(Char), R(House type), s(Zone)
   *        0x07 : Org HQ remove initiated
   *               s(Char), s(Zone)
   *
   * Reference categories:
   *  509 : House types (?)
   *        0x00 : Normal House
   * 2005 : Faction
   *        0x00 : Neutral
   *        0x01 : Clan
   *        0x02 : Omni
   *
   */
   
  class AOExtMsg
  {
    private static $msg_cat = array(
      501 => array(0xad0ae9b => array(AOEM_ORG_LEAVE,
                                      "{NAME} has left the organization because of alignment change.",
                                      "s{NAME}"),
                  ),
      506 => array(0x0c299d4 => array(AOEM_NW_ATTACK,
                                      "{ATT_NAME} ({ATT_ORG}, {ATT_SIDE}) attacked {DEF_ORG} ({DEF_SIDE}) in {ZONE} at {X}, {Y}.",
                                      "R{ATT_SIDE}/s{ATT_ORG}/s{ATT_NAME}/R{DEF_SIDE}/s{DEF_ORG}/s{ZONE}/i{X}/i{Y}"),
                   0x8cac524 => array(AOEM_NW_ABANDON,
                                      "{ORG} ({SIDE}) abandoned their base in {ZONE}.",
                                      "R{SIDE}/s{ORG}/s{ZONE}"),
                  ),
      508 => array(0x04e87e7 => array(AOEM_ORG_JOIN,
                                      "{NAME} has joined the organization.",
                                      "s{NAME}"),
                   0x2360067 => array(AOEM_ORG_KICK,
                                      "{KICKER} kicked {NAME} from the organization.",
                                      "s{KICKER}/s{NAME}"),
                   0x2bd9377 => array(AOEM_ORG_LEAVE,
                                      "{NAME} has left the organization.",
                                      "s{NAME}"),
                   0x8487156 => array(AOEM_ORG_FORM,
                                      "{NAME} changed the organization governing form to {FORM}.",
                                      "s{NAME}/s{FORM}"),
                   0x88cc2e7 => array(AOEM_ORG_DISBAND,
                                      "{NAME} has disbanded the organization.",
                                      "s{NAME}"),
                   0xc477095 => array(AOEM_ORG_VOTE,
                                      "Voting notice: {SUBJECT}\nCandidates: {CHOICES}\nDuration: {DURATION} minutes",
                                      "s{SUBJECT}/u{MINUTES}/s{CHOICES}"),
                  ),
     1001 => array(0x01 => array(AOEM_AI_CLOAK,
                                 "{NAME} turned the cloaking device in your city {STATUS}.",
                                 "s{NAME}/s{STATUS}"),
                   0x02 => array(AOEM_AI_RADAR,
                                  "Your radar station is picking up alien activity in the area surrounding your city.",
                                  ""),
                   0x03 => array(AOEM_AI_ATTACK,
                                 "Your city in {ZONE} has been targeted by hostile forces.",
                                 "s{ZONE}"),
                   0x04 => array(AOEM_AI_HQ_REMOVE,
                                 "{NAME} removed the organization headquarters in {ZONE}.",
                                 "s{NAME}/s{ZONE}"),
                   0x05 => array(AOEM_AI_REMOVE_INIT,
                                 "{NAME} initiated removal of a {TYPE} in {ZONE}.",
                                 "s{NAME}/R{TYPE}/s{ZONE}"),
                   0x06 => array(AOEM_AI_REMOVE,
                                 "{NAME} removed a {TYPE} in {ZONE}.",
                                 "s{NAME}/R{TYPE}/s{ZONE}"),
                   0x07 => array(AOEM_AI_HQ_REMOVE_INIT,
                                 "{NAME} initiated removal of the organization headquarters in {ZONE}.",
                                 "s{NAME}/s{ZONE}"),
                  ),
    );
    private static $ref_cat = array(
      509 => array(0x00 => "Normal House"),
     2005 => array(0x00 => "Neutral",
                   0x01 => "Clan",
                   0x02 => "Omni"),
    );
    public $type, $text, $args;

    function AOExtMsg($str=NULL)
    {
      $this->type = AOEM_UNKNOWN;
      if(!empty($str))
        $this->read($str);
    }
    
    function arg($n)
    {
      $key = "{".strtoupper($n)."}";
      if(isset($this->args[$key]))
        return $this->args[$key];
      return NULL;
    }

    function read($msg)
    {
      if(substr($msg, 0, 2) !== "~&")
        return false;
      $msg = substr($msg, 2);
      $category = $this->b85g($msg);
      $instance = $this->b85g($msg);
      
      if(!isset(self::$msg_cat[$category]) || !isset(self::$msg_cat[$category][$instance]))
        return false;
      
      $typ = self::$msg_cat[$category][$instance][0];
      $fmt = self::$msg_cat[$category][$instance][1];
      $enc = self::$msg_cat[$category][$instance][2];
      
      $args = array();
      
      foreach(split("/", $enc) as $eone)
      {
        $ename = substr($eone, 1);
        $msg = substr($msg, 1); // skip the data type id
        switch($eone[0])
        {
          case "s":
            $len = ord($msg[0])-1;
            $str = substr($msg, 1, $len);
            $msg = substr($msg, $len +1);
            $args[$ename] = $str;
            break;

          case "i":
          case "u":
            $num = $this->b85g($msg);
            $args[$ename] = $num;
            break;
          
          case "R":
            $cat = $this->b85g($msg);
            $ins = $this->b85g($msg);
            if(!isset(self::$ref_cat[$cat]) || !isset(self::$ref_cat[$cat][$ins]))
              $str = "Unknown ($cat, $ins)";
            else
              $str = self::$ref_cat[$cat][$ins];
            $args[$ename] = $str;
            break;
        }
      }
      
      $str = strtr($fmt, $args);
      
      $this->type = $typ;
      $this->text = $str;
      $this->args = $args;
    }

    function b85g(&$str)
    {
      $n = 0;
      for($i=0; $i<5; $i++)
        $n = $n*85 + ord($str[$i])-33;
      $str = substr($str, 5);
      return $n;
    }
  }

  /* Prioritized chat message queue. */

  class AOChatQueue
  {
    var $dfunc, $queue, $qsize;
    var $point, $limit, $inc;

    function AOChatQueue($cb, $limit, $inc)
    {
      $this->dfunc = $cb;
      $this->limit = $limit;
      $this->inc = $inc;
      $this->point = 0;
      $this->queue = array();
      $this->qsize = 0;
    }

    function push($priority)
    {
      $args = array_slice(func_get_args(), 1);
      $now = time();
      if($this->point <= ($now+$this->limit))
      {
        call_user_func_array($this->dfunc, $args);
        $this->point = (($this->point<$now) ? $now : $this->point)+$this->inc;
        return 1;
      }
      if(isset($this->queue[$priority]))
      {
        $this->queue[$priority][] = $args;
      }
      else
      {
        $this->queue[$priority] = array($args);
        krsort($this->queue);
      }
      $this->qsize ++;
      return 2;
    }

    function run()
    {
      if($this->qsize === 0)
        return 0;
      $now = time();
      if($this->point < $now)
        $this->point = $now;
      else if($this->point > ($now + $this->limit))
        return 0;
      $processed = 0;
      foreach(array_keys($this->queue) as $priority)
      {
        for(;;)
        {
          $item = array_shift($this->queue[$priority]);
          if($item === NULL)
          {
            unset($this->queue[$priority]);
            break;
          }
          call_user_func_array($this->dfunc, $item);
          $this->point += $this->inc;
          $processed ++;
          if($this->point > ($now + $this->limit))
          {
            break(2);
          }
        }
      }
      $this->qsize -= $processed;
      return $processed;
    }
  }

?>
