<?php

namespace Budabot\User\Modules;

use Budabot\Core\StopExecutionException;

/**
 * Authors: 
 *  - Tyrence (RK2)
 *  - Mindrila (RK1)
 *	- Naturarum (Paradise, RK2)
 *
 * @Instance
 *
 * Commands this class contains:
 *	@DefineCommand(
 *		command     = 'online',
 *		accessLevel = 'member',
 *		description = 'Shows who is online',
 *		help        = 'online.txt',
 *		alias		= 'sm'
 *	)
 */
class OnlineController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;
	
	/** @Inject */
	public $db;
	
	/** @Inject */
	public $chatBot;
	
	/** @Inject */
	public $settingManager;
	
	/** @Inject */
	public $accessManager;
	
	/** @Inject */
	public $buddylistManager;
	
	/** @Inject */
	public $altsController;

	/** @Inject */
	public $text;
	
	/** @Inject */
	public $util;
	
	/** @Logger */
	public $logger;
	
	/**
	 * @Setup
	 */
	public function setup() {
		$this->db->loadSQLFile($this->moduleName, "online");
		
		$this->settingManager->add($this->moduleName, "online_expire", "How long to wait before clearing online list", "edit", "time", "15m", "2m;5m;10m;15m;20m", '', "mod");
		$this->settingManager->add($this->moduleName, "fancy_online", "Show fancy delimiters and profession icons in the online display", "edit", "options", "1", "true;false", "1;0");
		$this->settingManager->add($this->moduleName, "online_show_org_guild", "Show org/rank for players in guild channel", "edit", "options", "1", "Show org and rank;Show rank only;Show org only;Show no org info", "2;1;3;0");
		$this->settingManager->add($this->moduleName, "online_show_org_priv", "Show org/rank for players in private channel", "edit", "options", "2", "Show org and rank;Show rank only;Show org only;Show no org info", "2;1;3;0");
		$this->settingManager->add($this->moduleName, "online_admin", "Show admin levels in online list", "edit", "options", "0", "true;false", "1;0");
	}
	
	/**
	 * @HandlesCommand("online")
	 * @Matches("/^online$/i")
	 */
	public function onlineCommand($message, $channel, $sender, $sendto, $args) {
		$msg = $this->getOnlineList();
		$sendto->reply($msg);
	}

	/**
	 * @HandlesCommand("online")
	 * @Matches("/^online (.+)$/i")
	 */
	public function onlineProfCommand($message, $channel, $sender, $sendto, $args) {
		$profession = $this->util->getProfessionName($args[1]);
		if (empty($profession)) {
			return false;
		}

		$sql = "
			SELECT DISTINCT p.*, o.afk, COALESCE(a.main, o.name) AS pmain, (CASE WHEN o2.name IS NULL THEN 0 ELSE 1 END) AS online
			FROM online o
			LEFT JOIN alts a ON o.name = a.alt
			LEFT JOIN alts a2 ON a2.main = COALESCE(a.main, o.name)
			LEFT JOIN players p ON a2.alt = p.name OR COALESCE(a.main, o.name) = p.name
			LEFT JOIN online o2 ON p.name = o2.name
			WHERE p.profession = ?
			ORDER BY COALESCE(a.main, o.name) ASC";
		$data = $this->db->query($sql, $profession);
		$count = count($data);
		$mainCount = 0;
		$currentMain = "";

		if ($count > 0) {
			forEach ($data as $row) {
				if ($currentMain != $row->pmain) {
					$mainCount++;
					$blob .= "\n<highlight>$row->pmain<end> has\n";
					$currentMain = $row->pmain;
				}

				if ($row->profession === null) {
					$blob .= "| ($row->name)\n";
				} else {
					$prof = $this->util->getProfessionAbbreviation($row->profession);
					$blob.= "| $row->name - $row->level/<green>$row->ai_level<end> $prof";
				}
				if ($row->online == 1) {
					$blob .= " <green>Online<end>";
				}
				$blob .= "\n";
			}
			$blob .= "\nWritten by Naturarum (RK2)";
			$msg = $this->text->makeBlob("$profession Search Results ($mainCount)", $blob);
		} else {
			$msg = "$profession Search Results (0)";
		}

		$sendto->reply($msg);
	}
	
	/**
	 * @Event("logOn")
	 * @Description("Records an org member login in db")
	 */
	public function recordLogonEvent($eventObj) {
		$sender = $eventObj->sender;
		if (isset($this->chatBot->guildmembers[$sender])) {
			$this->addPlayerToOnlineList($sender, $this->chatBot->vars['guild'], 'guild');
		}
	}
	
	/**
	 * @Event("logOff")
	 * @Description("Records an org member logoff in db")
	 */
	public function recordLogoffEvent($eventObj) {
		$sender = $eventObj->sender;
		if (isset($this->chatBot->guildmembers[$sender])) {
			$this->removePlayerFromOnlineList($sender, 'guild');
		}
	}
	
	/**
	 * @Event("logOn")
	 * @Description("Sends a tell to players on logon showing who is online in org")
	 */
	public function showOnlineOnLogonEvent($eventObj) {
		$sender = $eventObj->sender;
		if (isset($this->chatBot->guildmembers[$sender]) && $this->chatBot->isReady()) {
			$msg = $this->getOnlineList();
			$this->chatBot->sendTell($msg, $sender);
		}
	}
	
	/**
	 * @Event("timer(10mins)")
	 * @Description("Online check")
	 */
	public function onlineCheckEvent($eventObj) {
		if ($this->chatBot->isReady()) {
			//$this->db->beginTransaction();
			$data = $this->db->query("SELECT name, channel_type FROM `online`");

			$guildArray = array();
			$privArray = array();
			$ircArray = array();

			forEach ($data as $row) {
				switch ($row->channel_type) {
					case 'guild':
						$guildArray []= $row->name;
						break;
					case 'priv':
						$privArray []= $row->name;
						break;
					case 'irc':
						$ircArray []= $row->name;
						break;
					default:
						$this->logger->log("WARN", "ONLINE_MODULE", "Unknown channel type: '$row->channel_type'. Expected: 'guild', 'priv' or 'irc'");
				}
			}

			$time = time();

			forEach ($this->chatBot->guildmembers as $name => $rank) {
				if ($this->buddylistManager->isOnline($name)) {
					if (in_array($name, $guildArray)) {
						$sql = "UPDATE `online` SET `dt` = ? WHERE `name` = ? AND added_by = '<myname>' AND channel_type = 'guild'";
						$this->db->exec($sql, $time, $name);
					} else {
						$sql = "INSERT INTO `online` (`name`, `channel`,  `channel_type`, `added_by`, `dt`) VALUES (?, '<myguild>', 'guild', '<myname>', ?)";
						$this->db->exec($sql, $name, $time);
					}
				}
			}

			forEach ($this->chatBot->chatlist as $name => $value) {
				if (in_array($name, $privArray)) {
					$sql = "UPDATE `online` SET `dt` = ? WHERE `name` = ? AND added_by = '<myname>' AND channel_type = 'priv'";
					$this->db->exec($sql, $time, $name);
				} else {
					$sql = "INSERT INTO `online` (`name`, `channel`,  `channel_type`, `added_by`, `dt`) VALUES (?, '<myguild> Guest', 'priv', '<myname>', ?)";
					$this->db->exec($sql, $name, $time);
				}
			}

			$sql = "DELETE FROM `online` WHERE (`dt` < ? AND added_by = '<myname>') OR (`dt` < ?)";
			$this->db->exec($sql, $time, ($time - $this->settingManager->get('online_expire')));
			//$this->db->commit();
		}
	}
	
	/**
	 * @Event("priv")
	 * @Description("Afk check")
	 * @Help("afk")
	 */
	public function afkCheckPrivateChannelEvent($eventObj) {
		$this->afkCheck($eventObj->sender, $eventObj->message, $eventObj->type);
	}
	
	/**
	 * @Event("guild")
	 * @Description("Afk check")
	 * @Help("afk")
	 */
	public function afkCheckGuildChannelEvent($eventObj) {
		$this->afkCheck($eventObj->sender, $eventObj->message, $eventObj->type);
	}
	
	/**
	 * @Event("priv")
	 * @Description("Sets a member afk")
	 * @Help("afk")
	 */
	public function afkPrivateChannelEvent($eventObj) {
		$this->afk($eventObj->sender, $eventObj->message, $eventObj->type);
	}
	
	/**
	 * @Event("guild")
	 * @Description("Sets a member afk")
	 * @Help("afk")
	 */
	public function afkGuildChannelEvent($eventObj) {
		$this->afk($eventObj->sender, $eventObj->message, $eventObj->type);
	}
	
	public function afkCheck($sender, $message, $type) {
		// to stop raising and lowering the cloak messages from triggering afk check
		if (!$this->util->isValidSender($sender)) {
			return;
		}

		if (!preg_match("/^.?afk(.*)$/i", $message)) {
			$row = $this->db->queryRow("SELECT afk FROM online WHERE `name` = ? AND added_by = '<myname>' AND channel_type = ?", $sender, $type);

			if ($row !== null && $row->afk != '') {
				list($time, $reason) = explode('|', $row->afk);
				$timeString = $this->util->unixtimeToReadable(time() - $time);
				// $sender is back
				$this->db->exec("UPDATE online SET `afk` = '' WHERE `name` = ? AND added_by = '<myname>' AND channel_type = ?", $sender, $type);
				$msg = "<highlight>{$sender}<end> is back after $timeString.";
				
				if ('priv' == $type) {
					$this->chatBot->sendPrivate($msg);
				} else if ('guild' == $type) {
					$this->chatBot->sendGuild($msg);
				}
			}
		}
	}
	
	public function afk($sender, $message, $type) {
		if (preg_match("/^.?afk$/i", $message)) {
			$reason = time();
			$this->db->exec("UPDATE online SET `afk` = ? WHERE `name` = ? AND added_by = '<myname>' AND channel_type = ?", $reason, $sender, $type);
			$msg = "<highlight>$sender<end> is now AFK.";
		} else if (preg_match("/^.?brb(.*)$/i", $message, $arr)) {
			$msg = trim($arr[1]);
			$reason = time() . '|brb ' . $msg;
			$this->db->exec("UPDATE online SET `afk` = ? WHERE `name` = ? AND added_by = '<myname>' AND channel_type = ?", $reason, $sender, $type);
			$msg = "<highlight>$sender<end> is now AFK.";
		} else if (preg_match("/^.?afk (.*)$/i", $message, $arr)) {
			$reason = time() . '|' . $arr[1];
			$this->db->exec("UPDATE online SET `afk` = ? WHERE `name` = ? AND added_by = '<myname>' AND channel_type = ?", $reason, $sender, $type);
			$msg = "<highlight>$sender<end> is now AFK.";
		}

		if ('' != $msg) {
			if ('priv' == $type) {
				$this->chatBot->sendPrivate($msg);
			} else if ('guild' == $type) {
				$this->chatBot->sendGuild($msg);
			}
			
			// if 'afk' was used as a command, throw StopExecutionException to prevent
			// normal command handling from occurring
			if ($message[0] == $this->settingManager->get('symbol')) {
				throw new StopExecutionException();
			}
		}
	}
	
	public function addPlayerToOnlineList($sender, $channel, $channelType) {
		$sql = "SELECT name FROM `online` WHERE `name` = ? AND `channel_type` = ? AND added_by = '<myname>'";
		$data = $this->db->query($sql, $sender, $channelType);
		if (count($data) == 0) {
			$sql = "INSERT INTO `online` (`name`, `channel`,  `channel_type`, `added_by`, `dt`) VALUES (?, ?, ?, '<myname>', ?)";
			$this->db->exec($sql, $sender, $channel, $channelType, time());
		}
	}
	
	public function removePlayerFromOnlineList($sender, $channelType) {
		$sql = "DELETE FROM `online` WHERE `name` = ? AND `channel_type` = ? AND added_by = '<myname>'";
		$this->db->exec($sql, $sender, $channelType);
	}
	
	public function getOnlineList() {
		$orgData = $this->getPlayers('guild');
		list($orgCount, $orgMain, $orgBlob) = $this->formatData($orgData, $this->settingManager->get("online_show_org_guild"));

		$privData = $this->getPlayers('priv');
		list($privCount, $privMain, $privBlob) = $this->formatData($privData, $this->settingManager->get("online_show_org_priv"));

		$totalCount = $orgCount + $privCount;
		$totalMain = $orgMain + $privMain;

		$blob = "\n";
		if ($orgCount > 0) {
			$blob .= "<header2>Org Channel ($orgMain)<end>\n";
			$blob .= $orgBlob;
			$blob .= "\n\n";
		}
		if ($privCount > 0) {
			$blob .= "<header2>Private Channel ($privMain)<end>\n";
			$blob .= $privBlob;
			$blob .= "\n\n";
		}

		if ($totalCount > 0) {
			$blob .= "Written by Naturarum (RK2)";
			$msg = $this->text->makeBlob("Players Online ($totalMain)", $blob);
		} else {
			$msg = "Players Online (0)";
		}
		return $msg;
	}

	public function getOrgInfo($show_org_info, $fancyColon, $guild, $guild_rank) {
		switch ($show_org_info) {
			case  3: return $guild != "" ? " $fancyColon {$guild}":" $fancyColon Not in an org";
			case  2: return $guild != "" ? " $fancyColon {$guild} ({$guild_rank})":" $fancyColon Not in an org";
			case  1: return $guild != "" ? " $fancyColon {$guild_rank}":"";
			default: return "";
		}
	}

	public function getAdminInfo($name, $fancyColon) {
		if ($this->settingManager->get("online_admin") != 1) {
			return "";
		}

		switch ($this->accessManager->getAccessLevelForCharacter($name)) {
			case 'superadmin': return " $fancyColon <red>SuperAdmin<end>";
			case 'admin'     : return " $fancyColon <red>Admin<end>";
			case 'mod'       : return " $fancyColon <green>Mod<end>";
			case 'rl'        : return " $fancyColon <orange>RL<end>";
		}
	}

	public function getAfkInfo($afk, $fancyColon) {
		list($time, $reason) = explode("|", $afk);
		if (empty($afk)) {
			return '';
		} else if (empty($reason)) {
			$timeString = $this->util->unixtimeToReadable(time() - $time, false);
			return " $fancyColon <highlight>AFK for $timeString<end>";
		} else {
			$timeString = $this->util->unixtimeToReadable(time() - $time, false);
			return " $fancyColon <highlight>AFK for $timeString: {$reason}<end>";
		}
	}

	public function formatData($data, $showOrgInfo) {
		$count = count($data);
		$mainCount = 0;
		$currentMain = "";
		$blob = "";
		$separator = "-";

		if ($count > 0) {
			forEach ($data as $row) {
				if ($currentMain != $row->pmain) {
					$mainCount++;
					$blob .= "\n<pagebreak><highlight>$row->pmain<end> on\n";
					$currentMain = $row->pmain;
				}

				$admin = $this->getAdminInfo($row->name, $separator);
				$afk = $this->getAfkInfo($row->afk, $separator);

				if ($row->profession === null) {
					$blob .= "| $row->name$admin$afk\n";
				} else {
					$prof = $this->util->getProfessionAbbreviation($row->profession);
					$orgRank = $this->getOrgInfo($showOrgInfo, $separator, $row->guild, $row->guild_rank);
					$blob.= "| $row->name - $row->level/<green>$row->ai_level<end> $prof$orgRank$admin$afk\n";
				}
			}
		}
		
		return [$count, $mainCount, $blob];
	}

	public function getPlayers($channelType) {
		$sql = "
			SELECT p.*, o.name, o.afk, COALESCE(a.main, o.name) AS pmain
			FROM online o
			LEFT JOIN alts a ON o.name = a.alt
			LEFT JOIN players p ON o.name = p.name
			WHERE o.channel_type = ?
			ORDER BY COALESCE(a.main, o.name) ASC";
		return $this->db->query($sql, $channelType);
	}
}
