<?php

namespace Budabot\Core\Modules;

use Budabot\Core\DB;

/**
 * Authors:
 *  - Tyrence (RK2)
 *
 * @Instance
 */
class PlayerLookupController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;

	/** @Inject */
	public $db;

	/**
	 * @Setup
	 * This handler is called on bot startup.
	 */
	public function setup() {
		if ($this->db->getType() == DB::MYSQL) {
			$this->db->loadSQLFile($this->moduleName, 'players_mysql');
		} else if ($this->db->getType() == DB::SQLITE) {
			$this->db->loadSQLFile($this->moduleName, 'players_sqlite');
		}
	}
}
