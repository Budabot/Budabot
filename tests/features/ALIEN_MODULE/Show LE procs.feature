Feature: Show LE procs
	In order to spank alien butt!
	As an alium hunter
	I need to see list of LE procs availalable to different professions

	Background:
		Given "ALIEN_MODULE" module is enabled

	Scenario: Command leprocs lists all professions
		When I give command "!leprocs"
		Then the response should contain phrases:
			| profession      |
			| Adventurer      |
			| Agent           |
			| Bureaucrat      |
			| Doctor          |
			| Enforcer        |
			| Engineer        |
			| Fixer           |
			| Keeper          |
			| Martial Artist  |
			| Meta-Physicist  |
			| Nano-Technician |
			| Shade           |
			| Soldier         |
			| Trader          |

	Scenario Outline: Command leprocs lists procs for a profession
		When I give command "!leprocs <profession>"
		Then the response should contain phrase "<proc>"

		Examples:
			| profession | proc                | 
			| adv        | Soothing Herbs      |
			| agent      | Grim Reaper         |
			| crat       | Lost Paperwork      |
			| doc        | Anatomic Blight     |
			| enf        | Inspire Rage        |
			| eng        | Personal Protection |
			| fix        | Escape The System   |
			| keep       | Virtuous Reaper     |
			| ma         | Absolute Fist       |
			| mp         | Super-Ego Strike    |
			| nt         | Circular Logic      |
			| sol        | Furious Ammunition  |
			| shade      | Blackened Legacy    |
			| trader     | Unopened Letter     |
