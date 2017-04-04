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

// don't change this! unless you know what you do
define('SMARTIRC_CRLF',      "\r\n");
define('SMARTIRC_UNUSED',    '*');
define('SMARTIRC_STDOUT',        0);
define('SMARTIRC_FILE',          1);
define('SMARTIRC_SYSLOG',        2);
define('SMARTIRC_BROWSEROUT',    3);
define('SMARTIRC_NONE',          4);
define('SMARTIRC_LOW',         0);
define('SMARTIRC_MEDIUM',      1);
define('SMARTIRC_HIGH',        2);
define('SMARTIRC_CRITICAL',    3);
define('SMARTIRC_STATE_DISCONNECTED',    0);
define('SMARTIRC_STATE_CONNECTING',      1);
define('SMARTIRC_STATE_CONNECTED',       2);
define('SMARTIRC_DEBUG_NONE',             0);
define('SMARTIRC_DEBUG_NOTICE',           1);
define('SMARTIRC_DEBUG_CONNECTION',       2);
define('SMARTIRC_DEBUG_SOCKET',           4);
define('SMARTIRC_DEBUG_IRCMESSAGES',      8);
define('SMARTIRC_DEBUG_MESSAGETYPES',     16);
define('SMARTIRC_DEBUG_ACTIONHANDLER',    32);
define('SMARTIRC_DEBUG_TIMEHANDLER',      64);
define('SMARTIRC_DEBUG_MESSAGEHANDLER',   128);
define('SMARTIRC_DEBUG_CHANNELSYNCING',   256);
define('SMARTIRC_DEBUG_MODULES',          512);
define('SMARTIRC_DEBUG_USERSYNCING',      1024);
define('SMARTIRC_DEBUG_MESSAGEPARSER',    2048);
define('SMARTIRC_DEBUG_DCC',              4096);
define('SMARTIRC_DEBUG_ALL',              8191);
define('SMARTIRC_TYPE_UNKNOWN',        1);
define('SMARTIRC_TYPE_CHANNEL',        2);
define('SMARTIRC_TYPE_QUERY',          4);
define('SMARTIRC_TYPE_CTCP',           8);
define('SMARTIRC_TYPE_NOTICE',         16);
define('SMARTIRC_TYPE_WHO',            32);
define('SMARTIRC_TYPE_JOIN',           64);
define('SMARTIRC_TYPE_INVITE',         128);
define('SMARTIRC_TYPE_ACTION',         256);
define('SMARTIRC_TYPE_TOPICCHANGE',    512);
define('SMARTIRC_TYPE_NICKCHANGE',     1024);
define('SMARTIRC_TYPE_KICK',           2048);
define('SMARTIRC_TYPE_QUIT',           4096);
define('SMARTIRC_TYPE_LOGIN',          8192);
define('SMARTIRC_TYPE_INFO',           16384);
define('SMARTIRC_TYPE_LIST',           32768);
define('SMARTIRC_TYPE_NAME',           65536);
define('SMARTIRC_TYPE_MOTD',           131072);
define('SMARTIRC_TYPE_MODECHANGE',     262144);
define('SMARTIRC_TYPE_PART',           524288);
define('SMARTIRC_TYPE_ERROR',          1048576);
define('SMARTIRC_TYPE_BANLIST',        2097152);
define('SMARTIRC_TYPE_TOPIC',          4194304);
define('SMARTIRC_TYPE_NONRELEVANT',    8388608);
define('SMARTIRC_TYPE_WHOIS',          16777216);
define('SMARTIRC_TYPE_WHOWAS',         33554432);
define('SMARTIRC_TYPE_USERMODE',       67108864);
define('SMARTIRC_TYPE_CHANNELMODE',    134217728);
define('SMARTIRC_TYPE_CTCP_REQUEST',   268435456);
define('SMARTIRC_TYPE_CTCP_REPLY',     536870912);
//define('SMARTIRC_TYPE_DCC',            536870912);
define('SMARTIRC_TYPE_ALL',            1073741823);

// see https://www.alien.net.au/irc/irc2numerics.html
$SMARTIRC_nreplycodes = array(
'001' =>              'RPL_WELCOME',
'002' =>             'RPL_YOURHOST',
'003' =>              'RPL_CREATED',
'004' =>               'RPL_MYINFO',
'005' =>               'RPL_BOUNCE',
'008' =>              'RPL_SNOMASK',
'009' =>           'RPL_STATMEMTOT',
'014' =>           'RPL_YOURCOOKIE',
'042' =>               'RPL_YOURID',
'043' =>             'RPL_SAVENICK',
'050' =>       'RPL_ATTEMPTINGJUNC',
'051' =>    'RPL_ATTEMPTINGREROUTE',
'200' =>            'RPL_TRACELINK',
'201' =>      'RPL_TRACECONNECTING',
'202' =>       'RPL_TRACEHANDSHAKE',
'203' =>         'RPL_TRACEUNKNOWN',
'204' =>        'RPL_TRACEOPERATOR',
'205' =>            'RPL_TRACEUSER',
'206' =>          'RPL_TRACESERVER',
'207' =>         'RPL_TRACESERVICE',
'208' =>         'RPL_TRACENEWTYPE',
'209' =>           'RPL_TRACECLASS',
'210' =>       'RPL_TRACERECONNECT',
'211' =>        'RPL_STATSLINKINFO',
'212' =>        'RPL_STATSCOMMANDS',
'213' =>           'RPL_STATSCLINE',
'215' =>           'RPL_STATSILINE',
'216' =>           'RPL_STATSKLINE',
'218' =>           'RPL_STATSYLINE',
'219' =>           'RPL_ENDOFSTATS',
'221' =>              'RPL_UMODEIS',
'231' =>          'RPL_SERVICEINFO',
'232' =>        'RPL_ENDOFSERVICES',
'233' =>              'RPL_SERVICE',
'234' =>             'RPL_SERVLIST',
'235' =>          'RPL_SERVLISTEND',
'236' =>         'RPL_STATSVERBOSE',
'237' =>          'RPL_STATSENGINE',
'239' =>           'RPL_STATSIAUTH',
'240' =>           'RPL_STATSVLINE',
'241' =>           'RPL_STATSLLINE',
'242' =>          'RPL_STATSUPTIME',
'243' =>           'RPL_STATSOLINE',
'244' =>           'RPL_STATSHLINE',
'245' =>           'RPL_STATSSLINE',
'246' =>            'RPL_STATSPING',
'247' =>           'RPL_STATSBLINE',
'250' =>           'RPL_STATSDLINE',
'251' =>          'RPL_LUSERCLIENT',
'252' =>              'RPL_LUSEROP',
'253' =>         'RPL_LUSERUNKNOWN',
'254' =>        'RPL_LUSERCHANNELS',
'255' =>              'RPL_LUSERME',
'256' =>              'RPL_ADMINME',
'257' =>            'RPL_ADMINLOC1',
'258' =>            'RPL_ADMINLOC2',
'259' =>           'RPL_ADMINEMAIL',
'261' =>             'RPL_TRACELOG',
'262' =>             'RPL_TRACEEND',
'263' =>             'RPL_TRYAGAIN',
'265' =>           'RPL_LOCALUSERS',
'266' =>          'RPL_GLOBALUSERS',
'267' =>        'RPL_START_NETSTAT',
'268' =>              'RPL_NETSTAT',
'269' =>          'RPL_END_NETSTAT',
'270' =>                'RPL_PRIVS',
'271' =>             'RPL_SILELIST',
'272' =>        'RPL_ENDOFSILELIST',
'273' =>               'RPL_NOTIFY',
'277' =>            'RPL_VCHANLIST',
'278' =>            'RPL_VCHANHELP',
'280' =>                'RPL_GLIST',
'296' =>       'RPL_CHANINFO_KICKS',
'299' =>         'RPL_END_CHANINFO',
'300' =>                 'RPL_NONE',
'301' =>                 'RPL_AWAY',
'302' =>             'RPL_USERHOST',
'303' =>                 'RPL_ISON',
'304' =>                 'RPL_TEXT',
'305' =>               'RPL_UNAWAY',
'306' =>              'RPL_NOWAWAY',
'311' =>            'RPL_WHOISUSER',
'312' =>          'RPL_WHOISSERVER',
'313' =>        'RPL_WHOISOPERATOR',
'314' =>           'RPL_WHOWASUSER',
'315' =>             'RPL_ENDOFWHO',
'316' =>          'RPL_WHOISCHANOP',
'317' =>            'RPL_WHOISIDLE',
'318' =>           'RPL_ENDOFWHOIS',
'319' =>        'RPL_WHOISCHANNELS',
'321' =>            'RPL_LISTSTART',
'322' =>                 'RPL_LIST',
'323' =>              'RPL_LISTEND',
'324' =>        'RPL_CHANNELMODEIS',
'325' =>             'RPL_UNIQOPIS',
'326' =>           'RPL_NOCHANPASS',
'327' =>        'RPL_CHPASSUNKNOWN',
'328' =>          'RPL_CHANNEL_URL',
'329' =>         'RPL_CREATIONTIME',
'331' =>              'RPL_NOTOPIC',
'332' =>                'RPL_TOPIC',
'333' =>         'RPL_TOPICWHOTIME',
'339' =>          'RPL_BADCHANPASS',
'340' =>               'RPL_USERIP',
'341' =>             'RPL_INVITING',
'342' =>            'RPL_SUMMONING',
'345' =>              'RPL_INVITED',
'346' =>           'RPL_INVITELIST',
'347' =>      'RPL_ENDOFINVITELIST',
'348' =>           'RPL_EXCEPTLIST',
'349' =>      'RPL_ENDOFEXCEPTLIST',
'351' =>              'RPL_VERSION',
'352' =>             'RPL_WHOREPLY',
'353' =>             'RPL_NAMREPLY',
'354' =>            'RPL_WHOSPCRPL',
'355' =>            'RPL_NAMREPLY_',
'361' =>             'RPL_KILLDONE',
'362' =>              'RPL_CLOSING',
'363' =>             'RPL_CLOSEEND',
'364' =>                'RPL_LINKS',
'365' =>           'RPL_ENDOFLINKS',
'366' =>           'RPL_ENDOFNAMES',
'367' =>              'RPL_BANLIST',
'368' =>         'RPL_ENDOFBANLIST',
'369' =>          'RPL_ENDOFWHOWAS',
'371' =>                 'RPL_INFO',
'372' =>                 'RPL_MOTD',
'373' =>            'RPL_INFOSTART',
'374' =>            'RPL_ENDOFINFO',
'375' =>            'RPL_MOTDSTART',
'376' =>            'RPL_ENDOFMOTD',
'381' =>            'RPL_YOUREOPER',
'382' =>            'RPL_REHASHING',
'383' =>         'RPL_YOURESERVICE',
'384' =>             'RPL_MYPORTIS',
'385' =>       'RPL_NOTOPERANYMORE',
'388' =>                'RPL_ALIST',
'389' =>           'RPL_ENDOFALIST',
'391' =>                 'RPL_TIME',
'392' =>           'RPL_USERSSTART',
'393' =>                'RPL_USERS',
'394' =>           'RPL_ENDOFUSERS',
'395' =>              'RPL_NOUSERS',
'396' =>           'RPL_HOSTHIDDEN',
'400' =>         'ERR_UNKNOWNERROR',
'401' =>           'ERR_NOSUCHNICK',
'402' =>         'ERR_NOSUCHSERVER',
'403' =>        'ERR_NOSUCHCHANNEL',
'404' =>     'ERR_CANNOTSENDTOCHAN',
'405' =>      'ERR_TOOMANYCHANNELS',
'406' =>        'ERR_WASNOSUCHNICK',
'407' =>       'ERR_TOOMANYTARGETS',
'408' =>        'ERR_NOSUCHSERVICE',
'409' =>             'ERR_NOORIGIN',
'411' =>          'ERR_NORECIPIENT',
'412' =>         'ERR_NOTEXTTOSEND',
'413' =>           'ERR_NOTOPLEVEL',
'414' =>         'ERR_WILDTOPLEVEL',
'415' =>              'ERR_BADMASK',
'419' =>      'ERR_LENGTHTRUNCATED',
'421' =>       'ERR_UNKNOWNCOMMAND',
'422' =>               'ERR_NOMOTD',
'423' =>          'ERR_NOADMININFO',
'424' =>            'ERR_FILEERROR',
'425' =>           'ERR_NOOPERMOTD',
'429' =>          'ERR_TOOMANYAWAY',
'430' =>      'ERR_EVENTNICKCHANGE',
'431' =>      'ERR_NONICKNAMEGIVEN',
'432' =>     'ERR_ERRONEUSNICKNAME',
'433' =>        'ERR_NICKNAMEINUSE',
'436' =>        'ERR_NICKCOLLISION',
'437' =>      'ERR_UNAVAILRESOURCE',
'439' =>        'ERR_TARGETTOOFAST',
'400' =>         'ERR_SERVICESDOWN',
'441' =>     'ERR_USERNOTINCHANNEL',
'442' =>         'ERR_NOTONCHANNEL',
'443' =>        'ERR_USERONCHANNEL',
'444' =>              'ERR_NOLOGIN',
'445' =>       'ERR_SUMMONDISABLED',
'446' =>        'ERR_USERSDISABLED',
'447' =>         'ERR_NONICKCHANGE',
'449' =>       'ERR_NOTIMPLEMENTED',
'451' =>        'ERR_NOTREGISTERED',
'452' =>          'ERR_IDCOLLISION',
'453' =>             'ERR_NICKLOST',
'455' =>          'ERR_HOSTILENAME',
'456' =>           'ERR_ACCEPTFULL',
'457' =>          'ERR_ACCEPTEXIST',
'458' =>            'ERR_ACCEPTNOT',
'459' =>             'ERR_NOHIDING',
'460' =>        'ERR_NOTFORHALFOPS',
'461' =>       'ERR_NEEDMOREPARAMS',
'462' =>     'ERR_ALREADYREGISTRED',
'463' =>        'ERR_NOPERMFORHOST',
'464' =>       'ERR_PASSWDMISMATCH',
'465' =>     'ERR_YOUREBANNEDCREEP',
'466' =>      'ERR_YOUWILLBEBANNED',
'467' =>               'ERR_KEYSET',
'469' =>              'ERR_LINKSET',
'471' =>        'ERR_CHANNELISFULL',
'472' =>          'ERR_UNKNOWNMODE',
'473' =>       'ERR_INVITEONLYCHAN',
'474' =>       'ERR_BANNEDFROMCHAN',
'475' =>        'ERR_BADCHANNELKEY',
'476' =>          'ERR_BADCHANMASK',
'477' =>          'ERR_NOCHANMODES',
'478' =>          'ERR_BANLISTFULL',
'481' =>         'ERR_NOPRIVILEGES',
'482' =>     'ERR_CHANOPRIVSNEEDED',
'483' =>       'ERR_CANTKILLSERVER',
'484' =>           'ERR_RESTRICTED',
'485' =>    'ERR_UNIQOPPRIVSNEEDED',
'491' =>           'ERR_NOOPERHOST',
'492' =>        'ERR_NOSERVICEHOST',
'493' =>            'ERR_NOFEATURE',
'494' =>           'ERR_BADFEATURE',
'495' =>           'ERR_BADLOGTYPE',
'496' =>            'ERR_BADLOGSYS',
'497' =>          'ERR_BADLOGVALUE',
'498' =>          'ERR_ISOPERLCHAN',
'499' =>    'ERR_CHANOWNPRIVNEEDED',
'501' =>     'ERR_UMODEUNKNOWNFLAG',
'502' =>       'ERR_USERSDONTMATCH',
'504' =>        'ERR_USERNOTONSERV',
'511' =>         'ERR_SILELISTFULL',
'512' =>         'ERR_TOOMANYWATCH',
'513' =>              'ERR_BADPING',
'515' =>            'ERR_BADEXPIRE',
'516' =>            'ERR_DONTCHEAT',
'517' =>             'ERR_DISABLED',
'522' =>            'ERR_WHOSYNTAX',
'523' =>         'ERR_WHOLIMEXCEED',
'550' =>          'ERR_BADHOSTMASK',
'551' =>          'ERR_HOSTUNAVAIL',
'552' =>           'ERR_USINGSLINE',
'600' =>                'RPL_LOGON',
'601' =>               'RPL_LOGOFF',
'602' =>             'RPL_WATCHOFF',
'603' =>            'RPL_WATCHSTAT',
'604' =>                'RPL_NOWON',
'605' =>               'RPL_NOWOFF',
'606' =>            'RPL_WATCHLIST',
'607' =>       'RPL_ENDOFWATCHLIST',
'608' =>           'RPL_WATCHCLEAR',
'611' =>              'RPL_ISLOCOP',
'612' =>            'RPL_ISNOTOPER',
'613' =>          'RPL_ENDOFISOPER',
'618' =>              'RPL_DCCLIST',
'626' =>               'RPL_ENDOFO',
'630' =>             'RPL_SETTINGS',
'631' =>        'RPL_ENDOFSETTINGS',
'660' =>       'RPL_TRACEROUTE_HOP',
'661' =>     'RPL_TRACEROUTE_START',
'662' =>       'RPL_MODECHANGEWARN',
'663' =>            'RPL_CHANREDIR',
'664' =>           'RPL_SERVMODEIS',
'665' =>         'RPL_OTHERUMODEIS',
'666' =>        'RPL_ENDOF_GENERIC',
'670' =>        'RPL_WHOWASDETAILS',
'671' =>          'RPL_WHOISSECURE',
'672' =>         'RPL_UNKNOWNMODES',
'673' =>       'RPL_CANNOTSETMODES',
'678' =>           'RPL_LUSERSTAFF',
'679' =>       'RPL_TIMEONSERVERIS',
'682' =>             'RPL_NETWORKS',
'687' =>       'RPL_YOURLANGUAGEIS',
'688' =>             'RPL_LANGUAGE',
'689' =>           'RPL_WHOISSTAFF',
'690' =>        'RPL_WHOISLANGUAGE',
'702' =>              'RPL_MODLIST',
'703' =>         'RPL_ENDOFMODLIST',
'704' =>            'RPL_HELPSTART',
'705' =>              'RPL_HELPTXT',
'706' =>            'RPL_ENDOFHELP',
'708' =>           'RPL_ETRACEFULL',
'709' =>               'RPL_ETRACE',
'710' =>                'RPL_KNOCK',
'711' =>            'RPL_KNOCKDLVR',
'712' =>         'ERR_TOOMANYKNOCK',
'713' =>             'ERR_CHANOPEN',
'714' =>          'ERR_KNOCKONCHAN',
'715' =>        'ERR_KNOCKDISABLED',
'716' =>           'RPL_TARGUMODEG',
'717' =>           'RPL_TARGNOTIFY',
'718' =>            'RPL_UMODEGMSG',
'720' =>           'RPL_OMOTDSTART',
'721' =>                'RPL_OMOTD',
'722' =>           'RPL_ENDOFOMOTD',
'723' =>              'ERR_NOPRIVS',
'724' =>             'RPL_TESTMARK',
'725' =>             'RPL_TESTLINE',
'726' =>           'RPL_NOTESTLINE',
'771' =>                'RPL_XINFO',
'773' =>           'RPL_XINFOSTART',
'774' =>             'RPL_XINFOEND',
'972' =>      'ERR_CANNOTDOCOMMAND',
'973' =>    'ERR_CANNOTCHANGEUMODE',
'974' => 'ERR_CANNOTCHANGECHANMODE',
'975' => 'ERR_CANNOTCHANGESERVERMODE',
'976' =>     'ERR_CANNOTSENDTONICK',
'977' =>    'ERR_UNKNOWNSERVERMODE',
'979' =>       'ERR_SERVERMODELOCK',
'980' =>      'ERR_BADCHARENCODING',
'981' =>     'ERR_TOOMANYLANGUAGES',
'982' =>           'ERR_NOLANGUAGE',
'983' =>         'ERR_TEXTTOOSHORT',
'999' =>          'ERR_NUMERIC_ERR',
);

foreach ($SMARTIRC_nreplycodes as $key => $value) {
    define('SMARTIRC_'.$value, $key);
}
