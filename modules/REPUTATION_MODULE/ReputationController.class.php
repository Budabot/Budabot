<?php

namespace Budabot\User\Modules;

/**
 * Authors: 
 *	- Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'reputation', 
 *		accessLevel = 'guild', 
 *		description = 'Allows people to see and and reputation of other players', 
 *		help        = 'reputation.txt'
 *	)
 */
class ReputationController {

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
	public $text;
	
	/** @Inject */
	public $util;
	
	/** @Inject */
	public $settingManager;
	
	/**
	 * @Setup
	 */
	public function setup() {
		$this->settingManager->add($this->moduleName, "reputation_min_time", "How much time is required for leaving reputation for the same character", "edit", "time", "6h", "1h;6h;24h", '', "mod");
		
		$this->db->loadSQLFile($this->moduleName, 'reputation');
	}

	/**
	 * @HandlesCommand("reputation")
	 * @Matches("/^reputation$/i")
	 */
	public function reputationListCommand($message, $channel, $sender, $sendto, $args) {
		$sql = "
			SELECT
				name,
				SUM(CASE WHEN reputation = '+1' THEN 1 ELSE 0 END) pos_rep,
				SUM(CASE WHEN reputation = '-1' THEN 1 ELSE 0 END) neg_rep
			FROM
				reputation
			GROUP BY
				name";

		$data = $this->db->query($sql);
		$count = count($data);

		if ($count == 0) {
			$msg = "There are no characters on the reputation list.";
			$sendto->reply($msg);
			return;
		}

		$blob = '';
		forEach ($data as $row) {
			$details_link = $this->text->make_chatcmd('Details', "/tell <myname> reputation $row->name");
			$blob .= "$row->name  <green>+{$row->pos_rep}<end> <orange>-{$row->neg_rep}<end>   {$details_link}\n";
		}
		$msg = $this->text->makeBlob("Reputation List ($count)", $blob);
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("reputation")
	 * @Matches("/^reputation ([a-z0-9-]+) (\+1|\-1) (.+)$/i")
	 */
	public function reputationAddCommand($message, $channel, $sender, $sendto, $args) {
		$name = ucfirst(strtolower($args[1]));
		$charid = $this->chatBot->get_uid($name);
		$rep = $args[2];
		$comment = $args[3];

		if ($charid == false) {
			$sendto->reply("Character <highlight>$name<end> does not exist.");
			return;
		}

		if ($sender == $name) {
			$sendto->reply("You cannot give yourself reputation.");
			return;
		}

		$minTime = $this->settingManager->get('reputation_min_time');
		$time = time() - $minTime;

		$sql = "SELECT dt FROM reputation WHERE `by` = ? AND `name` = ? AND `dt` > ? ORDER BY dt DESC LIMIT 1";
		$row = $this->db->queryRow($sql, $sender, $name, $time);
		if ($row !== null) {
			$timeString = $this->util->unixtimeToReadable($row->dt - $time);
			$sendto->reply("You must wait $timeString before submitting more reputation for $name.");
			return;
		}

		$sql = "
			INSERT INTO reputation (
				`name`,
				`reputation`,
				`comment`,
				`by`,
				`dt`
			) VALUES (
				?,
				?,
				?,
				?,
				?
			)";

		$this->db->exec($sql, $name, $rep, $comment, $sender, time());
		$sendto->reply("Reputation for $name added successfully.");
	}
	
	/**
	 * @HandlesCommand("reputation")
	 * @Matches("/^reputation ([a-z0-9-]+) (all)$/i")
	 * @Matches("/^reputation ([a-z0-9-]+)$/i")
	 */
	public function reputationViewCommand($message, $channel, $sender, $sendto, $args) {
		$name = ucfirst(strtolower($args[1]));
		
		$limit = 10;
		if (count($args) == 3) {
			$limit = 1000;
		}

		$sql = "
			SELECT
				sum(CASE WHEN reputation = '+1' THEN 1 ELSE 0 END) AS positive_rep,
				sum(CASE WHEN reputation = '-1' THEN 1 ELSE 0 END) AS negative_rep
			FROM
				reputation
			WHERE
				name = ?";

		$row = $this->db->queryRow($sql, $name);
		if ($row === null) {
			$msg = "<highlight>$name<end> has no reputation.";
		} else {
			$num_positive = $row->positive_rep;
			$num_negative = $row->negative_rep;

			$blob = "Positive reputation: <green>{$num_positive}<end>\n";
			$blob .= "Negative reputation: <orange>{$num_negative}<end>\n\n";
			if ($limit != 1000) {
				$blob .= "Last $limit comments about this user:\n\n";
			} else {
				$blob .= "All comments about this user:\n\n";
			}

			$sql = "SELECT * FROM reputation WHERE name = ? ORDER BY `dt` DESC LIMIT " . $limit;
			$data = $this->db->query($sql, $name);
			forEach ($data as $row) {
				if ($row->reputation == '-1') {
					$blob .= "<orange>";
				} else {
					$blob .= "<green>";
				}

				$time = $this->util->unixtimeToReadable(time() - $row->dt);
				$blob .= "({$row->reputation}) $row->comment <end> $row->by <white>{$time} ago<end>\n\n";
			}
			
			if ($limit != 1000) {
				$blob .= $this->text->make_chatcmd("Show all comments", "/tell <myname> reputation $name all");
			}

			$msg = $this->text->makeBlob("Reputation for {$name} (+$num_positive -$num_negative)", $blob);
		}

		$sendto->reply($msg);
	}
}
