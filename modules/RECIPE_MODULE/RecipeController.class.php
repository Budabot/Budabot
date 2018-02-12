<?php

namespace Budabot\User\Modules;

use Exception;

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

	private $path;

	/** @Setup */
	public function setup() {
		$this->db->loadSQLFile($this->moduleName, "recipes", true);

		$this->path = getcwd() . "/modules/" . $this->moduleName . "/recipes/";
		if ($handle = opendir($this->path)) {
			while (false !== ($fileName = readdir($handle))) {
				// if file has the correct extension, load recipe into database
				if (preg_match("/(\d+)\.txt/", $fileName, $args)) {
					$id = $args[1];
					$lines = file($this->path . $fileName);
					$name = substr(trim(array_shift($lines)), 6);
					$author = substr(trim(array_shift($lines)), 8);
					$data = implode("", $lines);
					$this->db->exec("INSERT INTO recipes (id, name, author, recipe) VALUES (?, ?, ?, ?)", $id, $name, $author, $data);
				} else if (preg_match("/(\d+)\.json/", $fileName, $args)) {
					$recipe = json_decode(file_get_contents($this->path . $fileName));
					if ($recipe === null) {
						throw new Exception("Could not read '$fileName', invalid JSON");
					}
					$id = $args[1];
					$name = $recipe->name;
					$author = $recipe->author;
					$items = array();
					forEach($recipe->items as $item) {
						$dbItem = $this->itemsController->findById($item->item_id);
						if ($dbItem === null) {
							throw Exception("Could not find item '{$item->item_id}'");
						}
						$items[$item->alias] = $dbItem;
						$items[$item->alias]->ql = $item->ql;
					}
					$data = "#C16------------------------------\n";
					$data .= "#C12Ingredients #C20\n";
					$data .= "#C16------------------------------\n\n";
					$ingredients = $items;
					forEach($recipe->steps as $step) {
						unset($ingredients[$step->result]);
					}
					forEach($ingredients as $ingredient) {
						$data .= $this->text->makeImage($ingredient->icon) . "\n";
						$data .= $this->text->makeItem($ingredient->lowid, $ingredient->highid, $ingredient->ql, $ingredient->name) . "\n\n\n";
					}

					$data .= "#C16------------------------------\n";
					$data .= "#C12Recipe #C16\n";
					$data .= "#C16------------------------------#C20\n\n";
					forEach($recipe->steps as $step) {
						$source = $items[$step->source];
						$target = $items[$step->target];
						$result = $items[$step->result];
						$data .= $source->name . "\n";
						$data .= "#C15+#C20" . "\n";
						$data .= $target->name . "\n";
						$data .= "#C15=#C20" . "\n";
						$data .= $this->text->makeImage($result->icon) . "\n";
						$data .= $this->text->makeItem($result->lowid, $result->highid, $result->ql, $result->name) . "\n";
						if ($step->skills) {
							$data .= "#C16Skills: | {$step->skills} |#C20\n";
						}
						$data .= "\n\n";
					}

					$this->db->exec("INSERT INTO recipes (id, name, author, recipe) VALUES (?, ?, ?, ?)", $id, $name, $author, $data);
				}
			}
			closedir($handle);
		} else {
			throw new Exception("Could not open '$this->path' for loading recipes");
		}
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
				$blob .= $this->text->makeChatcmd($row->name, "/tell <myname> recipe $row->id") . "\n";
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

		$recipeText = "Recipe Id: <highlight>$row->id<end>\n"; 
		$recipeText .= "Author: <highlight>$author<end>\n\n";
		$recipeText .= $this->formatRecipeText($row->recipe);

		return $this->text->makeBlob("Recipe for $recipe_name", $recipeText);
	}

	private function replaceItem($arr) {
		$id = $arr[2];
		$row = $this->itemsController->findById($id);
		if ($row !== null) {
			$output = $this->text->makeItem($row->lowid, $row->highid, $row->highql, $row->name);
		} else {
			$output = "#L \"{$arr[1]}\" \"/tell <myname> itemid {$arr[2]}\"";
		}
		return $output;
	}
}

