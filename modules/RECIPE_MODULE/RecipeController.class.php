<?php
/**
 * Author: Tyrence
 * 12-Sep-2012
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
 *  @DefineCommand(
 *		command     = 'rshow',
 *		accessLevel = 'all',
 *		description = 'Show a recipe',
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
	public $text;

	/** @Setup */
	public function setup() {
		$this->db->loadSQLFile($this->moduleName, "recipes");
		$this->db->loadSQLFile($this->moduleName, "recipe_items");
		$this->db->loadSQLFile($this->moduleName, "recipe_type");
	}
	
	/**
	 * Show a recipe
	 *
	 * @HandlesCommand("rshow")
	 * @Matches("/^rshow ([0-9]+)$/i")
	 */
	public function recipeShowCommand($message, $channel, $sender, $sendto, $args) {
		$id = $args[1];
		
		$row = $this->db->queryRow("SELECT * FROM recipes WHERE recipe_id = ?", $id);
		
		if ($row === null) {
			$output = "A recipe with id <highlight>$id<end> could not be found.";
		} else {
			$recipe_name = $row->recipe_name;
			
			$recipe_text = $row->recipe_text;
			$recipe_text = str_replace("\\r\\n", "\n", $recipe_text);
			$recipe_text = ereg_replace("#C([0-9]+)","[16,\\1]",$recipe_text);
			$recipe_text = ereg_replace('#L "([^"]+)" "([0-9]+)"','#L "\\1" "/tell <myname> ishow \\2"',$recipe_text);

			$recipe_text = str_replace("[16,1]", "<font color=#FFFFFF>",
				str_replace("[16,2]", "</font><font color=#FFFFFF>",
				str_replace("[16,3]", "</font><font color=#FFFFFF>",
				str_replace("[16,4]", "</font><font color=#FFFFFF>",
				str_replace("[16,5]", "</font><font color=#FFFFFF>",
				str_replace("[16,6]", "</font><font color=#FFFFFF>",
				str_replace("[16,7]", "</font><font color=#FFFFFF>",
				str_replace("[16,8]", "</font><font color=#FFFFFF>",
				str_replace("[16,9]", "</font><font color=#FFFFFF>",
				str_replace("[16,10]","</font><font color=#FFFFFF>",
				str_replace("[16,11]","</font><font color=#FFFFFF>",
				str_replace("[16,12]","</font><font color=#FF0000>",
				str_replace("[16,13]","</font><font color=#FFFFFF>",
				str_replace("[16,14]","</font><font color=#FFFFFF>",
				str_replace("[16,15]","</font><font color=#FFFFFF>",
				str_replace("[16,16]","</font><font color=#FFFF00>",
				str_replace("[16,17]","</font><font color=#FFFFFF>",
				str_replace("[16,18]","</font><font color=#AAFF00>",
				str_replace("[16,19]","</font><font color=#FFFFFF>",
				str_replace("[16,20]","</font><font color=#009B00>",
				str_replace("[16,21]","</font><font color=#FFFFFF>",
				str_replace("[16,22]","</font><font color=#FFFFFF>",
				str_replace("[16,23]","</font><font color=#FFFFFF>",
				str_replace("[16,24]","</font><font color=#FFFFFF>",
				str_replace("[16,25]","</font><font color=#FFFFFF>",
				str_replace("[16,26]","</font><font color=#FFFFFF>",
				str_replace("[16,27]","</font><font color=#FFFFFF>",
				str_replace("[16,28]","</font><font color=#FFFFFF>",
				str_replace("[16,29]","</font><font color=#FFFFFF>",
				str_replace("[16,30]","</font><font color=#FFFFFF>",
				str_replace("[16,31]","</font><font color=#FFFFFF>",
				str_replace("[17]",chr(17),
				str_replace("[18]",chr(18),$recipe_text)))))))))))))))))))))))))))))))));
	
			$recipe_text = ereg_replace('#L "([^"]+)" "([^"]+)"',"<a href='chatcmd://\\2'>\\1</a>",$recipe_text);

			$output = $this->text->make_blob("Recipe for $recipe_name", $recipe_text);
		}
		$sendto->reply($output);
	}

	/**
	 * Search for a recipe
	 *
	 * @HandlesCommand("recipe")
	 * @Matches("/^recipe (.+)$/i")
	 */
	public function recipeSearchCommand($message, $channel, $sender, $sendto, $args) {
		if (preg_match('/<a href="itemref:\/\/(\d+)\/(\d+)\/(\d+)">([^<]+)<\/a>/', $args[1], $itemValues)) {
			$lowId = $itemValues[1];
			$itemName = $itemValues[4];
			
			$results = $this->db->query("SELECT r1.recipe_id, r1.recipe_name FROM recipes r1 JOIN recipe_items r2 ON r1.recipe_id = r2.recipe_id WHERE r2.item_id = ?", $lowId);
			$count = count($results);

			if (count($results) == 0) {
				$output = "This item is not used in any known recipe.";
			} else {
				$blob = $this->makeRecipeSearchBlob($results);
				$output = $this->text->make_blob("Recipes matching '{$itemName}' ($count)", $blob);
			}
		} else {
			$search = strtolower($args[1]);
			
			$results = $this->db->query("SELECT * FROM recipes WHERE recipe_text like ? AND recipe_type != '8'", "%{$search}%");
			$count = count($results);

			if ($count > 0) {
				$blob = $this->makeRecipeSearchBlob($results);
				$output = $this->text->make_blob("Recipes matching '$search' ($count)", $blob);
			} else {
				$output = "There were no matches for your search.";
			}
		}
		$sendto->reply($output);
	}
	
	private function makeRecipeSearchBlob($results) {
		$blob = '';
		forEach ($results as $row) {
			$blob .= $this->text->make_chatcmd($row->recipe_name, "/tell <myname> rshow $row->recipe_id") . "\n";
		}
		return $blob;
	}
}

