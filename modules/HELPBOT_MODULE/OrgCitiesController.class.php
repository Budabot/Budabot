<?php

/**
 * Authors: 
 *	- Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'orgcities', 
 *		accessLevel = 'all', 
 *		description = 'Show coords for org cities', 
 *		help        = 'orgcities.txt'
 *	)
 */
class OrgCitiesController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;
	
	/** @Inject */
	public $db;

	/** @Inject */
	public $text;
	
	/** @Inject */
	public $playfieldController;
	
	/**
	 * This handler is called on bot startup.
	 * @Setup
	 */
	public function setup() {
		$this->db->loadSQLFile($this->moduleName, 'orgcities');
	}

	/**
	 * @HandlesCommand("orgcities")
	 * @Matches("/^orgcities$/i")
	 */
	public function orgCitiesListCommand($message, $channel, $sender, $sendto, $args) {
		$data = $this->db->query("SELECT DISTINCT playfield_id, long_name, short_name FROM orgcities c JOIN playfields p ON c.playfield_id = p.id ORDER BY long_name ASC");

		$blob = '';
		forEach ($data as $row) {
			$cityLink = $this->text->make_chatcmd($row->long_name, "/tell <myname> orgcities $row->short_name");
			$blob .= $cityLink . "\n";
		}

		$msg = $this->text->make_blob("Playfields with Org Cities", $blob);
		$sendto->reply($msg);
	}

	/**
	 * @HandlesCommand("orgcities")
	 * @Matches("/^orgcities (.+)$/i")
	 */
	public function orgCitiesShowCommand($message, $channel, $sender, $sendto, $args) {
		$playfieldName = $args[1];
	
		$playfield = $this->playfieldController->get_playfield_by_name($playfieldName);
		if ($playfield === null) {
			$sendto->reply("Could not find playfield '$playfieldName'");
			return;
		}

		$data = $this->db->query("SELECT * FROM orgcities WHERE playfield_id = ? ORDER BY cluster ASC, plot ASC", $playfield->id);

		$blob = '';
		$current_cluster = '';
		forEach ($data as $row) {
			if ($current_cluster != $row->cluster) {
				$blob .= "\n<header2>Cluster {$row->cluster}<end>\n";
				$current_cluster = $row->cluster;
			}
			$coords = $this->text->make_chatcmd("{$row->xcoord}x{$row->ycoord}", "/waypoint {$row->xcoord} {$row->ycoord} {$row->playfield_id}");
			$blob .= $row->cluster . $row->plot . " {$coords}\n";
		}

		$msg = $this->text->make_blob("Org cities in {$playfield->long_name}", $blob);
		$sendto->reply($msg);
	}
}
