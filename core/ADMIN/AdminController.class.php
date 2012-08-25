<?php

/**
 * @Instance
 */
class AdminController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;

	/** @Inject */
	public $commandManager;

	/** @Inject */
	public $eventManager;

	/** @Inject */
	public $help;

	/** @Inject */
	public $admin;

	/**
	 * @Setup
	 * This handler is called on bot startup.
	 */
	public function setup() {
		$this->commandManager->activate("msg", "Admin.addCommand", "addadmin", "admin");
		$this->commandManager->activate("priv", "Admin.addCommand", "addadmin", "admin");
		$this->commandManager->activate("guild", "Admin.addCommand", "addadmin", "admin");

		$this->commandManager->activate("msg", "Admin.removeCommand", "remadmin", "superadmin");
		$this->commandManager->activate("priv", "Admin.removeCommand", "remadmin", "superadmin");
		$this->commandManager->activate("guild", "Admin.removeCommand", "remadmin", "superadmin");

		$this->commandManager->activate("msg", "Admin.addCommand", "addmod", "admin");
		$this->commandManager->activate("priv", "Admin.addCommand", "addmod", "admin");
		$this->commandManager->activate("guild", "Admin.addCommand", "addmod", "admin");

		$this->commandManager->activate("msg", "Admin.removeCommand", "remmod", "admin");
		$this->commandManager->activate("priv", "Admin.removeCommand", "remmod", "admin");
		$this->commandManager->activate("guild", "Admin.removeCommand", "remmod", "admin");

		$this->commandManager->activate("msg", "Admin.adminlistCommand", "adminlist", 'all');
		$this->commandManager->activate("priv", "Admin.adminlistCommand", "adminlist", 'all');
		$this->commandManager->activate("guild", "Admin.adminlistCommand", "adminlist", 'all');

		$this->eventManager->activate("connect", "Admin.checkAdmins");
		
		$this->admin->uploadAdmins();

		$this->help->register($this->moduleName, "admin", "admin.txt", "mod", "Mod/admin help file");
	}
}
