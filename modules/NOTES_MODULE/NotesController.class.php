<?php

/**
 * Authors: 
 *  - Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this class contains:
 *	@DefineCommand(
 *		command     = 'notes',
 *		accessLevel = 'guild',
 *		description = 'Displays, adds, or removes a note from your list',
 *		help        = 'notes.txt'
 *	)
 */
class NotesController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;
	
	/** @Inject */
	public $db;

	/** @Inject */
	public $text;
	
	/**
	 * @Setup
	 */
	public function setup() {
		$this->db->loadSQLFile($this->moduleName, "notes");
	}
	
	/**
	 * @HandlesCommand("notes")
	 * @Matches("/^notes$/i")
	 */
	public function notesListCommand($message, $channel, $sender, $sendto, $args) {
		$blob = '';

		$sql = "SELECT * FROM notes WHERE name LIKE ?";
		$data = $this->db->query($sql, $sender);

		if (count($data) == 0) {
			$msg = "No notes for $sender.";
		} else {
			forEach ($data as $row) {
				$remove = $this->text->make_chatcmd('Remove', "/tell <myname> notes rem $row->id");
				$blob .= "$remove $row->note\n\n";
			}
			$msg = $this->text->make_blob("Notes for $sender", $blob);
		}

		$sendto->reply($msg);
	}

	/**
	 * @HandlesCommand("notes")
	 * @Matches("/^notes add (.*)$/i")
	 */
	public function notesAddCommand($message, $channel, $sender, $sendto, $args) {
		$note = $args[1];

		$this->db->exec("INSERT INTO notes (name, note) VALUES(?, ?)", $sender, $note);
		$msg = "Note added successfully.";

		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("notes")
	 * @Matches("/^notes rem ([0-9]+)$/i")
	 */
	public function notesRemoveCommand($message, $channel, $sender, $sendto, $args) {
		$id = $args[1];

		$numRows = $this->db->exec("DELETE FROM notes WHERE id = ? AND name LIKE ?", $id, $sender);
		if ($numRows == 0) {
			$msg = "Note could not be found or note does not belong to you.";
		} else {
			$msg = "Note deleted successfully.";
		}

		$sendto->reply($msg);
	}
}

?>
