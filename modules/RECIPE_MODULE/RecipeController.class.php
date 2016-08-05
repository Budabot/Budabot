<?php

namespace Budabot\User\Modules;

/**
 * Authors:
 *  - Tyrence
 *
 * Based on a module written by Captainzero (RK1) of the same name for an earlier version of Budabot
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'recipe',
 *		accessLevel = 'all',
 *		description = 'Search for a recipe',
 *		help        = 'recipe.txt'
 *	)
 */
class RecipeController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;

	/** @Inject */
	public $db;

	/** @Inject */
	public $util;

	/** @Inject */
	public $text;
	
	/** @Inject */
	public $itemsController;

	/** @Setup */
	public function setup() {
		$this->db->loadSQLFile($this->moduleName, "recipes");
	}
	
	/**
	 * @HandlesCommand("recipe")
	 * @Matches("/^recipe ([0-9]+)$/i")
	 */
	public function recipeShowCommand($message, $channel, $sender, $sendto, $args) {
		$id = $args[1];
		
		$row = $this->db->queryRow("SELECT * FROM recipes WHERE id = ?", $id);

		if ($row === null) {
			$msg = "Could not find recipe with id <highlight>$id<end>.";
		} else {
			$msg = $this->createRecipeBlob($row);
		}
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("recipe")
	 * @Matches("/^recipe (.+)$/i")
	 */
	public function recipeSearchCommand($message, $channel, $sender, $sendto, $args) {
		if (preg_match('|<a href="itemref://(\d+)/(\d+)/(\d+)">([^<]+)</a>|', $args[1], $matches)) {
			$lowId = $matches[1];
			$search = $matches[4];
			
			$data = $this->db->query("SELECT * FROM recipes WHERE recipe LIKE ? OR recipe LIKE ? ORDER BY name ASC", $search, "%" . $lowId . "%");
		} else {
			$search = $args[1];
			
			list($query, $queryParams) = $this->util->generateQueryFromParams(explode(" ", $search), "recipe");
			$data = $this->db->query("SELECT * FROM recipes WHERE $query ORDER BY name ASC", $queryParams);
		}
		
		$count = count($data);

		if ($count == 0) {
			$msg = "Could not find any recipes matching your search criteria.";
		} else if ($count == 1) {
			$msg = $this->createRecipeBlob($data[0]);
		} else {
			$blob = '';
			forEach ($data as $row) {
				$blob .= $this->text->make_chatcmd($row->name, "/tell <myname> recipe $row->id") . "\n";
			}

			$msg = $this->text->makeBlob("Recipes matching '$search' ($count)", $blob);
		}

		$sendto->reply($msg);
	}
	
	public function formatRecipeText($input) {
		$input = str_replace("\\n", "\n", $input);
		$input = preg_replace_callback('/#L "([^"]+)" "([0-9]+)"/', array($this, 'replaceItem'), $input);
		$input = preg_replace('/#L "([^"]+)" "([^"]+)"/', "<a href='chatcmd://\\2'>\\1</a>", $input);
		
		// we can't use <myname> in the sql since that will get converted on load,
		// and we need to wait to convert until display time due to the possibility
		// of several bots sharing the same db
		$input = str_replace("{myname}", "<myname>", $input);

		$input = str_replace("#C09","</font><font color=#FFFFFF>", $input);
		$input = str_replace("#C12","</font><font color=#FF0000>", $input);
		$input = str_replace("#C13","</font><font color=#FFFFFF>", $input);
		$input = str_replace("#C14","</font><font color=#FFFFFF>", $input);
		$input = str_replace("#C15","</font><font color=#FFFFFF>", $input);
		$input = str_replace("#C16","</font><font color=#FFFF00>", $input);
		$input = str_replace("#C18","</font><font color=#AAFF00>", $input);
		$input = str_replace("#C20","</font><font color=#009B00>", $input);
			
		return $input;
	}
	
	public function createRecipeBlob($row) {
		$recipe_name = $row->name;
		$author = empty($row->author) ? "Unknown" : $row->author;

		$recipeText = "Author: <highlight>$author<end>\n\n";
		$recipeText .= $this->formatRecipeText($row->recipe);

		return $this->text->makeBlob("Recipe for $recipe_name", $recipeText);
	}

	private function replaceItem($arr) {
		$id = $arr[2];
		$row = $this->itemsController->findById($id);
		if ($row !== null) {
			$output = $this->text->make_item($row->lowid, $row->highid, $row->highql, $row->name);
		} else {
			$obj = $this->itemsController->getDetailedItemInfo($id);
			if (null == $obj) {
				$output = "#L \"{$arr[1]}\" \"/tell <myname> itemid {$arr[2]}\"";
			} else if ($obj->icon == 0) {  // for perks and items that aren't displayable in game
				$output = $this->text->make_chatcmd($obj->name, "/start https://aoitems.com/item/{$obj->lowid}");
			} else {
				$output = $this->text->make_item($obj->lowid, $obj->highid, $obj->highql, $obj->name);
			}
		}
		return $output;
	}
}

