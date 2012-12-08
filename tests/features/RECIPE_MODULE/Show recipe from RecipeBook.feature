Feature: Show a recipe from RecipeBook
	In order to build weird stuff
	As a tradeskiller
	I need to be able to see a recipe from RecipeBook service

	Scenario: Show a recipe
		Given "RECIPE_MODULE" module is enabled
		And RecipeBook service is online
		When I give command "!rbshow 20"
		Then the response should contain phrase "Monster Parts"
		And the response should contain phrase "Bio-Comminutor"
		And the response should contain phrase "Blood Plasma"

