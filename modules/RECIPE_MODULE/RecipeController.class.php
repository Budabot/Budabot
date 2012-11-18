<?php
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
 *  @DefineCommand(
 *		command     = 'rshow',
 *		accessLevel = 'all',
 *		description = 'Show a recipe',
 *		help        = 'recipe.txt'
 *	)
 *  @DefineCommand(
 *		command     = 'rb',
 *		accessLevel = 'all',
 *		description = 'Show a recipe',
 *		help        = 'recipe.txt'
 *	)
 *  @DefineCommand(
 *		command     = 'rbshow',
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
	
	/** @Inject */
	public $itemsController;
	
	private $baseUrl = "http://aodevnet.com/recipes/api";

	/** @Setup */
	public function setup() {
		$this->db->loadSQLFile($this->moduleName, "recipes");
		$this->db->loadSQLFile($this->moduleName, "recipe_type");
	}
	
	/**
	 * @HandlesCommand("rb")
	 * @Matches("/^rb (.+)$/i")
	 */
	public function rbSearchCommand($message, $channel, $sender, $sendto, $args) {
		if (preg_match('/<a href="itemref:\/\/(\d+)\/(\d+)\/(\d+)">([^<]+)<\/a>/', $args[1], $matches)) {
			$lowId = $matches[1];
			$search = $matches[4];
			
			$url = "/byitem/id/$lowId/mode/default/format/json/bot/budabot";
		} else {
			$search = $args[1];
			
			$url = "/search/kw/" . rawurlencode($search) . "/mode/default/format/json/bot/budabot";
		}
		
		$curl = new MyCurl($this->baseUrl . $url);
		$curl->createCurl();
		$contents = $curl->__toString();

		$obj = json_decode($contents);
		if (!empty($obj->error)) {
			$msg = "Error searching for recipe: " . $obj->error;
		} else {
			$blob = '';
			$count = count($obj);
			forEach ($obj as $recipe) {
				$blob .= $this->text->make_chatcmd($recipe->recipe_name, "/tell <myname> rbshow $recipe->recipe_id") . "\n";
			}

			$blob .= $this->getAORecipebookFooter();

			$msg = $this->text->make_blob("Recipes matching '$search' ($count)", $blob);
		}
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("rbshow")
	 * @Matches("/^rbshow (\d+)$/i")
	 */
	public function rbShowCommand($message, $channel, $sender, $sendto, $args) {
		$id = $args[1];
		
		$url = "/show/id/" . $id . "/format/json/bot/budabot";
		$curl = new MyCurl($this->baseUrl . $url);
		$curl->createCurl();
		$contents = $curl->__toString();

		$obj = json_decode($contents);
		if (!empty($obj->error)) {
			$msg = "Error showing recipe: " . $obj->error;
		} else {
			$recipe_name = $obj->recipe_name;
			
			$recipeText = $obj->recipe_text;
			$recipeText = str_replace("\\r\\n", "\n", $recipeText);
			$recipeText = $this->formatRecipeText($recipeText);
			
			$recipeText .= $this->getAORecipebookFooter();
			
			$msg = $this->text->make_blob("Recipe for $recipe_name", $recipeText);
		}
		$sendto->reply($msg);
	}
	
	private function getAORecipebookFooter() {
		return "\n\n<header>Powered by " . $this->text->make_chatcmd("AORecipebook.com", "/start http://aorecipebook.com") . "<end>\n" .
			"For more information, " . $this->text->make_chatcmd("/tell recipebook about", "/tell recipebook about");
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
			$msg = "A recipe with id <highlight>$id<end> could not be found.";
		} else {
			$recipe_name = $row->recipe_name;
			$author = $row->author;
			
			$recipeText = "";
			if (!empty($author)) {
				$recipeText .= "Author: <highlight>$author<end>\n\n";
			}
			
			$recipeText .= $this->formatRecipeText($row->recipe_text);

			$msg = $this->text->make_blob("Recipe for $recipe_name", $recipeText);
		}
		$sendto->reply($msg);
	}
	
	private function formatRecipeText($input) {
		$input = str_replace("\\n", "\n", $input);
		$input = preg_replace_callback('/#L "([^"]+)" "([0-9]+)"/', array($this, 'replaceItem'), $input);
		$input = preg_replace('/#L "([^"]+)" "([^"]+)"/', "<a href='chatcmd://\\2'>\\1</a>", $input);

		$input = str_replace("#C09","</font><font color=#FFFFFF>",
			str_replace("#C12","</font><font color=#FF0000>",
			str_replace("#C13","</font><font color=#FFFFFF>",
			str_replace("#C14","</font><font color=#FFFFFF>",
			str_replace("#C15","</font><font color=#FFFFFF>",
			str_replace("#C16","</font><font color=#FFFF00>",
			str_replace("#C18","</font><font color=#AAFF00>",
			str_replace("#C20","</font><font color=#009B00>",$input))))))));
			
		return $input;
	}

	/**
	 * Search for a recipe
	 *
	 * @HandlesCommand("recipe")
	 * @Matches("/^recipe (.+)$/i")
	 */
	public function recipeSearchCommand($message, $channel, $sender, $sendto, $args) {
		if (preg_match('/<a href="itemref:\/\/(\d+)\/(\d+)\/(\d+)">([^<]+)<\/a>/', $args[1], $matches)) {
			$search = $matches[4];
		} else {
			$search = strtolower($args[1]);
		}
			
		$sql = "
			SELECT
				recipe_id,
				recipe_name
			FROM
				recipes
			WHERE
				recipe_text LIKE ? AND recipe_type != '8'
			ORDER BY
				recipe_name ASC";

		$results = $this->db->query($sql, "%" . str_replace(" ", "%", $search) . "%");
		$count = count($results);

		if ($count > 0) {
			$blob = $this->makeRecipeSearchBlob($results);
			$output = $this->text->make_blob("Recipes matching '$search' ($count)", $blob);
		} else {
			$output = "There were no matches for your search.";
		}
		$sendto->reply($output);
	}
	
	private function replaceItem($arr) {
		$id = $arr[2];
		$row = $this->itemsController->findById($id);
		if ($row !== null) {
			$output = $this->text->make_item($row->lowid, $row->highid, $row->highql, $row->name);
		} else {
			$obj = $this->itemsController->doXyphosLookup($id);
			if (null == $obj) {
				$output = "#L \"{$arr[1]}\" \"/tell <myname> itemid {$arr[2]}\"";
			} else if ($obj->icon == 0) {  // for perks and items that aren't displayable in game
				$output = $this->text->make_chatcmd($obj->name, "/start http://www.xyphos.com/ao/aodb.php?id={$obj->lowid}");
			} else {
				$output = $this->text->make_item($obj->lowid, $obj->highid, $obj->highql, $obj->name);
			}
		}
		return $output;
	}
	
	private function makeRecipeSearchBlob($results) {
		$blob = '';
		forEach ($results as $row) {
			$blob .= $this->text->make_chatcmd($row->recipe_name, "/tell <myname> rshow $row->recipe_id") . "\n";
		}
		return $blob;
	}
}

