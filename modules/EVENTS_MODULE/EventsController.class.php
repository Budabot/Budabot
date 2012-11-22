<?php
/**
 * Authors: 
 *	- Legendadv (RK2)
 *	- Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'events',
 *		accessLevel = 'all',
 *		description = 'View/Join/Leave events',
 *		help        = 'events.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'events add .+',
 *		accessLevel = 'mod',
 *		description = 'Add an event',
 *		help        = 'events.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'events rem .+',
 *		accessLevel = 'mod',
 *		description = 'Remove an event',
 *		help        = 'events.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'events setdesc .+',
 *		accessLevel = 'mod',
 *		description = 'Change or set the description for an event',
 *		help        = 'events.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'events setdate .+',
 *		accessLevel = 'mod',
 *		description = 'Change or set the date for an event',
 *		help        = 'events.txt'
 *	)
 */
class EventsController {

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
	public $alts;
	
	private $apisocket = null;

	/** @Setup */
	public function setup() {
		$this->db->loadSQLFile($this->moduleName, "events");
	}
	
	/**
	 * @HandlesCommand("events")
	 * @Matches("/^events$/i")
	 */
	public function eventsCommand($message, $channel, $sender, $sendto, $args) {
		$msg = $this->getEvents();
		if ($msg == '') {
			$msg = "No events entered yet.";
		}
		$sendto->reply($msg);
	}

	/**
	 * @HandlesCommand("events")
	 * @Matches("/^events join ([0-9]+)$/i")
	 */
	public function eventsJoinCommand($message, $channel, $sender, $sendto, $args) {
		$id = $args[1];
		$row = $this->db->queryRow("SELECT * FROM events WHERE `id` = ?", $id);
		if (time() < ($row->event_date + (3600 * 3))) {
			// cannot join an event after 3 hours past its starttime
			if (strpos($row->event_attendees, $sender) !== false) {
				$msg = "You are already on the event list.";
			} else {
				if ($row->event_attendees == "") {
					$row->event_attendees = "$sender";
				} else {
					$row->event_attendees .= ",$sender";
				}
				$this->db->exec("UPDATE events SET `event_attendees` = ? WHERE `id` = ?", $row->event_attendees, $id);
				$msg = "You have been added to the event.";
			}
		} else {
			$msg = "You cannot join an event once it has already passed!";
		}
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("events")
	 * @Matches("/^events leave ([0-9]+)$/i")
	 */
	public function eventsLeaveCommand($message, $channel, $sender, $sendto, $args) {
		$id = $args[1];
		$row = $this->db->queryRow("SELECT * FROM events WHERE `id` = ?", $id);
		if (time() < ($row->event_date + (3600 * 3))) {
			// cannot leave an event after 3 hours past its starttime
			if (strpos($row->event_attendees,$sender) !== false) {
				$event = explode(",", $row->event_attendees);
				forEach ($event as $i => $value) {
					if ($value == $sender) {
						unset($event[$i]);
						$event = array_values($event);
					}
				}
				$event = implode(",", $event);
				$this->db->exec("UPDATE events SET `event_attendees` = ? WHERE `id` = ?", $event, $id);
				$msg = "You have been removed from the event.";
			} else {
				$msg = "You are not on the event list.";
			}
		} else {
			$msg = "You cannot leave an event once it has already passed!";
		}
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("events")
	 * @Matches("/^events list ([0-9]+)$/i")
	 */
	public function eventsListCommand($message, $channel, $sender, $sendto, $args) {
		$id = $args[1];
		$row = $this->db->queryRow("SELECT event_attendees FROM events WHERE `id` = ?", $id);
		if ($row !== null) {
			$link = $this->text->make_chatcmd("Join this event", "/tell <myname> events join $id")."\n";
			$link .= $this->text->make_chatcmd("Leave this event", "/tell <myname> events leave $id")."\n\n";

			if (empty($row->event_attendees)) {
				$link .= "No one has signed up to attend this event!";
			} else {
				$eventlist = explode(",", $row->event_attendees);
				sort($eventlist);
				forEach ($eventlist as $key => $name) {
					$row = $this->db->queryRow("SELECT * FROM players WHERE name = ? AND dimension = '<dim>'", $name);
					$info = '';
					if ($row !== null) {
						$info = " <white>Lvl $row->level $row->profession<end>\n";
					}

					$altInfo = $this->alts->get_alt_info($name);
					$alt = '';
					if (count($altInfo->alts) > 0) {
						if ($altInfo->main == $name) {
							$alt = "<highlight>::<end> " . $this->text->make_chatcmd("Alts", "/tell <myname> alts $name");
						} else {
							$alt = "<highlight>::<end> " . $this->text->make_chatcmd("Alts of {$altInfo->main}", "/tell <myname> alts $name");
						}
					}

					$link .= $name . "$info $alt\n";
				}
			}
			$msg = $this->text->make_blob("Players Attending Event $id", $link);
		} else {
			$msg = "Could not find event with id $id.";
		}

		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("events add .+")
	 * @Matches("/^events add (.+)$/i")
	 */
	public function eventsAddCommand($message, $channel, $sender, $sendto, $args) {
		$eventName = $args[1];
		$this->db->exec("INSERT INTO events (`time_submitted`, `submitter_name`, `event_name`) VALUES (?, ?, ?)", time(), $sender, $eventName);
		$event_id = $this->db->lastInsertId();
		$msg = "Event: '$eventName' was added [Event ID $event_id].";
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("events rem .+")
	 * @Matches("/^events rem ([0-9]+)$/i")
	 */
	public function eventsRemoveCommand($message, $channel, $sender, $sendto, $args) {
		$id = $args[1];
		$row = $this->db->queryRow("SELECT * FROM events WHERE id = ?", $id);
		if ($row === null) {
			$msg = "Could not find an event with id $id.";
		} else {
			$this->db->exec("DELETE FROM events WHERE `id` = ?", $id);
			$msg = "Event with id $id has been deleted.";
		}
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("events setdesc .+")
	 * @Matches("/^events setdesc ([0-9]+) (.+)$/i")
	 */
	public function eventsSetDescCommand($message, $channel, $sender, $sendto, $args) {
		$id = $args[1];
		$desc = $args[2];
		$row = $this->db->queryRow("SELECT * FROM events WHERE id = ?", $id);
		if ($row === null) {
			$msg = "Could not find an event with id $id.";
		} else {
			$this->db->exec("UPDATE events SET `event_desc` = ? WHERE `id` = ?", $desc, $id);
			$msg = "Description for event with id $id has been updated.";
		}
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("events setdate .+")
	 * @Matches("/^events setdate ([0-9]+) ([0-9]{4})-(0?[1-9]|1[012])-(0?[1-9]|[12][0-9]|3[01]) ([0-1]?[0-9]|[2][0-3]):([0-5][0-9]):([0-5][0-9])$/i")
	 */
	public function eventsSetDateCommand($message, $channel, $sender, $sendto, $args) {
		$id = $args[1];
		$row = $this->db->queryRow("SELECT * FROM events WHERE id = ?", $id);
		if ($row === null) {
			$msg = "Could not find an event with id $id.";
		} else {
			// yyyy-dd-mm hh:mm:ss
			$eventDate = mktime($args[5], $args[6], 0, $args[3], $args[4], $args[2]);
			$this->db->exec("UPDATE events SET `event_date` = ? WHERE `id` = ?", $eventDate, $id);
			$msg = "Date/Time for event with id $id has been updated.";
		}
		$sendto->reply($msg);
	}
	
	public function getEvents() {
		$data = $this->db->query("SELECT * FROM events ORDER BY `event_date` DESC LIMIT 0,5");
		if (count($data) > 0) {
			$upcoming_title = "<header2>Upcoming Events<end>\n\n";
			$past_title = "<header2>Past Events<end>\n\n";
			$updated = 0;
			forEach ($data as $row) {
				if ($row->event_attendees == '') {
					$attendance = 0;
				} else {
					$attendance = count(explode(",", $row->event_attendees));
				}
				if ($updated < $row->time_submitted) {
					$updated = $row->time_submitted;
				}

				if ($row->event_date > time()) {
					$upcoming = "Event Date: <highlight>" . $this->util->date($row->event_date) . "<end>\n";
					$upcoming .= "Event Name: <highlight>$row->event_name<end>     [Event ID $row->id]\n";
					$upcoming .= "Author: <highlight>$row->submitter_name<end>\n";
					$upcoming .= "Attendance: <highlight>" . $this->text->make_chatcmd("$attendance signed up", "/tell <myname> events list $row->id") . "<end>" .
						" [" . $this->text->make_chatcmd("Join", "/tell <myname> events join $row->id") . "/" . 
						$this->text->make_chatcmd("Leave", "/tell <myname> events leave $row->id") . "]\n";
					$upcoming .= "Description: <highlight>" . $row->event_desc . "<end>\n";
					$upcoming .= "Date Submitted: <highlight>" . $this->util->date($row->time_submitted) . "<end>\n\n";
					$upcoming_events = $upcoming.$upcoming_events;
				} else {
					$past = "Event Date: <highlight>" . $this->util->date($row->event_date) . "<end>\n";
					$past .= "Event Name: <highlight>$row->event_name<end>     [Event ID $row->id]\n";
					$past .= "Author: <highlight>$row->submitter_name<end>\n";
					$past .= "Attendance: <highlight>" . $this->text->make_chatcmd("$attendance signed up", "/tell <myname> events list $row->id") . "<end>\n";
					$past .= "Description: <highlight>" . $row->event_desc . "<end>\n";
					$past .= "Date Submitted: <highlight>" . $this->util->date($row->time_submitted) . "<end>\n\n";
					$past_events .= $past;
				}
			}
			if (!$upcoming_events) {
				$upcoming_events = "<i>More to come.  Check back soon!</i>\n\n";
			}
			if (!$past_events) {
				$link = $upcoming_title.$upcoming_events;
			} else {
				$link = $upcoming_title.$upcoming_events.$past_title.$past_events;
			}

			return $this->text->make_legacy_blob("Latest Events", $link) . " [Last updated " . $this->util->date($updated)."]";
		} else {
			return "";
		}
	}
	
	/**
	 * @Event("logOn")
	 * @Description("Show events to org members logging on")
	 */
	public function logonEvent($eventObj) {
		$sender = $eventObj->sender;
		if ($this->chatBot->is_ready() && isset($this->chatBot->guildmembers[$sender])) {
			$msg = $this->getEvents();
			if ($msg != '') {
				$this->chatBot->sendTell($msg, $sender);
			}
		}
	}
	
	/**
	 * @Event("joinPriv")
	 * @Description("Show events to characters joining the private channel")
	 */
	public function joinPrivEvent($eventObj) {
		$sender = $eventObj->sender;
		$msg = $this->getEvents();
		if ($msg != '') {
			$this->chatBot->sendTell($msg, $sender);
		}
	}
}

