<?php

/**
 * Authors: 
 *	- Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'feedback', 
 *		accessLevel = 'all', 
 *		description = 'Allows people to add and see feedback', 
 *		help        = 'feedback.txt'
 *	)
 */
class FeedbackController {

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
	
	/**
	 * This handler is called on bot startup.
	 * @Setup
	 */
	public function setup() {
		$this->db->loadSQLFile($this->moduleName, 'feedback');
	}

	/**
	 * @HandlesCommand("feedback")
	 * @Matches("/^feedback$/i")
	 */
	public function feedbackListCommand($message, $channel, $sender, $sendto, $args) {
		$sql = "
			SELECT
				name,
				SUM(CASE WHEN reputation = '+1' THEN 1 ELSE 0 END) pos_rep,
				SUM(CASE WHEN reputation = '-1' THEN 1 ELSE 0 END) neg_rep
			FROM
				feedback
			GROUP BY
				name";

		$data = $this->db->query($sql);
		$count = count($data);

		if ($count == 0) {
			$msg = "There are no characters on the feedback list.";
			$sendto->reply($msg);
			return;
		}

		$blob = '';
		forEach ($data as $row) {
			$details_link = $this->text->make_chatcmd('Details', "/tell <myname> feedback $row->name");
			$blob .= "$row->name  <green>+{$row->pos_rep}<end> <orange>-{$row->neg_rep}<end>   {$details_link}\n";
		}
		$msg = $this->text->make_blob("Feedback List ($count)", $blob);
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("feedback")
	 * @Matches("/^feedback ([a-z0-9-]+) (\+1|\-1) (.+)$/i")
	 */
	public function feedbackAddCommand($message, $channel, $sender, $sendto, $args) {
		$name = ucfirst(strtolower($args[1]));
		$charid = $this->chatBot->get_uid($name);
		$rep = $args[2];
		$comment = $args[3];
		$by_charid = $this->chatBot->get_uid($sender);

		if ($charid == false) {
			$sendto->reply("Could not find character '$name'.");
			return;
		}

		if ($charid == $by_charid) {
			$sendto->reply("You cannot give yourself feedback.");
			return;
		}

		$time = time() - 86400;

		$sql = "SELECT name FROM feedback WHERE `by_charid` = ? AND `charid` = ? AND `dt` > ?";
		$data = $this->db->query($sql, $by_charid, $charid, $time);
		if (count($data) > 0) {
			$sendto->reply("You may only submit feedback for a player once every 24 hours. Please try again later.");
			return;
		}

		$sql = "SELECT name FROM feedback WHERE `by_charid` = ?";
		$data = $this->db->query($sql, $by_charid);
		if (count($data) > 3) {
			$sendto->reply("You may submit feedback a maximum of 3 times in a 24 hour period. Please try again later.");
			return;
		}

		$sql = "
			INSERT INTO feedback (
				`name`,
				`charid`,
				`reputation`,
				`comment`,
				`by`,
				`by_charid`,
				`dt`
			) VALUES (
				?,
				?,
				?,
				?,
				?,
				?,
				?
			)";

		$this->db->exec($sql, $name, $charid, $rep, $comment, $sender, $by_charid, time());
		$sendto->reply("Feedback for $name added successfully.");
	}
	
	/**
	 * @HandlesCommand("feedback")
	 * @Matches("/^feedback ([a-z0-9-]+)$/i")
	 */
	public function feedbackViewCommand($message, $channel, $sender, $sendto, $args) {
		$name = ucfirst(strtolower($args[1]));
		$charid = $this->chatBot->get_uid($name);

		if ($charid == false) {
			$where_sql = "WHERE `name` = '$name'";
		} else {
			$where_sql = "WHERE `charid` = '$charid'";
		}

		$data = $this->db->query("SELECT reputation, COUNT(*) count FROM feedback {$where_sql} GROUP BY `reputation`");
		if (count($data) == 0) {
			$msg = "<highlight>$name<end> has no feedback.";
		} else {
			$num_positive = 0;
			$num_negative = 0;
			forEach ($data as $row) {
				if ($row->reputation == '+1') {
					$num_positive = $row->count;
				} else if ($row->reputation == '-1') {
					$num_negative = $row->count;
				}
			}

			$blob = "Positive feedback: <green>{$num_positive}<end>\n";
			$blob .= "Negative feedback: <orange>{$num_negative}<end>\n\n";
			$blob .= "Last 10 comments about this user:\n\n";

			$sql = "SELECT * FROM feedback {$where_sql} ORDER BY `dt` DESC LIMIT 10";
			$data = $this->db->query($sql);
			forEach ($data as $row) {
				if ($row->reputation == '-1') {
					$blob .= "<orange>";
				} else {
					$blob .= "<green>";
				}

				$time = $this->util->unixtime_to_readable(time() - $row->dt);
				$blob .= "({$row->reputation}) $row->comment <end> $row->by <white>{$time} ago<end>\n\n";
			}

			$msg = $this->text->make_blob("Feedback for {$name}", $blob);
		}

		$sendto->reply($msg);
	}
}
