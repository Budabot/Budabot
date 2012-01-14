<?php

class SignupController {

	/** @Inject */
	public $db;
	
	/** @Inject */
	public $chatBot;
	
	/** @Inject */
	public $text;

	/**
	 * @Command("signup")
	 * @AccessLevel("all")
	 * @Description("Show and manage signup lists")
	 */
	public function listCommand($message, $channel, $sender, $sendto) {
		if (preg_match("/^signup$/i", $message)) {
			$msg = $this->showSignupLists();
		} else if (preg_match("/^signup add (.+)$/i", $message, $arr)) {
			$msg = $this->addList($arr[1], $sender);
		} else if (preg_match("/^signup rem (\\d+)$/i", $message, $arr)) {
			$msg = $this->removeList($arr[1], $sender);
		} else if (preg_match("/^signup assign (\\d+)$/i", $message, $arr)) {
			$msg = $this->assignToList($arr[1], $sender);
		} else if (preg_match("/^signup assign (\\d+) (.+)$/i", $message, $arr)) {
			$msg = $this->assignToList($arr[1], $arr[2]);
		} else if (preg_match("/^signup unassign (\\d+)$/i", $message, $arr)) {
			$msg = $this->unassignFromList($arr[1], $sender);
		} else if (preg_match("/^signup unassign (\\d+) (.+)$/i", $message, $arr)) {
			$msg = $this->unassignFromList($arr[1], $arr[2]);
		} else if (preg_match("/^signup (\\d+)$/i", $message, $arr)) {
			$msg = $this->viewList($arr[1]);
		} else {
			return false;
		}
		
		$this->chatBot->send($msg, $sendto);
	}
	
	public function showSignupLists() {
		$data = $this->db->query("SELECT * FROM signup_lists ORDER BY name ASC");
		if (count($data) == 0) {
			return "No signup lists have been created yet.";
		}
		
		$blob = "<header> :::::: Signup Lists :::::: <end>\n\n";
		forEach ($data as $row) {
			$viewListLink = $this->text->make_chatcmd($row->name, "/tell <myname> signup $row->id");
			$signupLink = $this->text->make_chatcmd("Signup", "/tell <myname> signup assign $row->id");
			$blob .= "$viewListLink [$signupLink]\n";
		}
		return $this->text->make_blob("Signup Lists", $blob);
	}
	
	public function addList($name, $owner) {
		$row = $this->db->queryRow("SELECT id FROM signup_lists WHERE name LIKE ?", $name);
		if ($row !== null) {
			return "A list already exists with that name.";
		}
		
		$this->db->exec("INSERT INTO signup_lists (name, owner, dt) VALUES (?, ?, ?)", $name, $owner, time());
		return "A list with name <highlight>$name<end> has been created successfully.";
	}
	
	public function removeList($id, $owner) {
		$row = $this->db->queryRow("SELECT id FROM signup_lists WHERE id = ?", $id);
		if ($row === null) {
			return "There is no list with id <highlight>$id<end>.";
		}
		
		$this->db->exec("DELETE FROM signup_lists WHERE id = ?", $id);
		$this->db->exec("DELETE FROM signup_characters WHERE list_id = ?", $id);
		return "The list <highlight>$row->name<end> has been deleted successfully.";
	}
	
	public function assignToList($id, $sender) {
		$list = $this->db->queryRow("SELECT * FROM signup_lists WHERE id = ?", $id);
		if ($list === null) {
			return "There is no list that exists with that id.";
		}
	
		$row = $this->db->queryRow("SELECT name FROM signup_characters WHERE list_id = ? AND name LIKE ?", $id, $sender);
		if ($row !== null) {
			return "<highlight>$sender<end> has already been assigned to the list <highlight>$list->name<end>.";
		}
		
		$this->db->exec("INSERT INTO signup_characters (list_id, name) VALUES (?,?)", $id, $sender);
		return "<highlight>$sender<end> has been assigned to the list <highlight>$list->name<end> successfully.";
	}
	
	public function unassignFromList($id, $sender) {
		$list = $this->db->queryRow("SELECT * FROM signup_lists WHERE id = ?", $id);
		if ($list === null) {
			return "There is no list that exists with that id.";
		}
	
		$row = $this->db->queryRow("SELECT name FROM signup_characters WHERE list_id = ? AND name LIKE ?", $id, $sender);
		if ($row === null) {
			return "<highlight>$sender<end> is not assigned to the list <highlight>$list->name<end>.";
		}
		
		$this->db->exec("DELETE FROM signup_characters WHERE list_id = ? AND name = ?", $id, $sender);
		return "<highlight>$sender<end> has been unassigned from the list <highlight>$list->name<end> successfully.";
	}
	
	public function viewList($id) {
		$list = $this->db->queryRow("SELECT * FROM signup_lists WHERE id LIKE ?", $id);
		if ($list === null) {
			return "There is no list that exists with that id.";
		}
		
		$blob = "<header> :::::: $list->name List :::::: <end>\n\n";
		$blob .= "Owner: <highlight>$list->owner<end>\n";
		$blob .= "Added: <highlight>" . date(Util::DATETIME, $list->dt) . "<end>\n\n";

		$blob .= "<header> ::: Players Signed up ::: <end>\n\n";
		$data = $this->db->query("SELECT * FROM signup_characters s LEFT JOIN players p ON s.name = p.name AND p.dimension = '<dim>' WHERE list_id = ?", $id);
		forEach ($data as $row) {
			if ($row->profession == null) {
				$blob .= "$row->name - Unknown\n";
			} else {
				$blob .= "$row->name - $row->level<green>/$row->ai_level<end> $row->profession, $row->guild \n";
			}
		}
		return $this->text->make_blob("$list->name List", $blob);
	}
}

?>