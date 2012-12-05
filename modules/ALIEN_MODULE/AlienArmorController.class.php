<?php
/**
 * Authors: 
 *  - Blackruby (RK2),
 *  - Mdkdoc420 (RK2), 
 *  - Wolfbiter (RK1), 
 *  - Gatester (RK2), 
 *  - Marebone (RK2)
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'aiarmor',
 *		accessLevel = 'all', 
 *		description = 'Shows tradeskill process for Alien Armor', 
 *		help        = 'aiarmor.txt'
 *	)
 */
class AlienArmorController {

	/** @Inject */
	public $text;
	
	/** @Inject */
	public $itemsController;

	/**
	 * This command handler shows tradeskill process for normal Alien Armor.
	 *
	 * @HandlesCommand("aiarmor")
	 * @Matches("/^aiarmor (strong|supple|enduring|observant|arithmetic|spiritual)$/i")
	 * @Matches("/^aiarmor (strong|supple|enduring|observant|arithmetic|spiritual) (\d+)$/i")
	 * @Matches("/^aiarmor (\d+) (strong|supple|enduring|observant|arithmetic|spiritual)$/i")
	 */
	public function aiarmorNormalCommand($message, $channel, $sender, $sendto, $args) {
		list($armortype, $ql) = $this->extractArgs($args);
		$armortype = ucfirst($armortype);
		$misc_ql = floor($ql * 0.8);

		$list = "Note: All tradeskill processes are based on the lowest QL items usable.\n\n";
		$list .= "<header2>You need the following items to build $armortype Armor:<end>\n";
		$list .= "- Kyr'Ozch Viralbots\n";
		$list .= "- Kyr'Ozch Atomic Re-Structulazing Tool\n";
		$list .= "- Solid Clump of Kyr'Ozch Biomaterial\n";
		$list .= "- Arithmetic/Strong/Enduring/Spiritual/Observant/Supple Viralbots\n\n";

		$list .= "<header2>Step 1<end>\n";
		$list .= "<tab>" . $this->itemsController->getItemAndIcon("Kyr'Ozch Viralbots", $misc_ql);
		$list .= " (<highlight>Drops from Alien City Generals<end>)\n";
		$list .= "<tab><tab>+\n";
		$list .= "<tab>" . $this->itemsController->getItemAndIcon("Kyr'Ozch Atomic Re-Structuralizing Tool", 100);
		$list .= " (<highlight>Drops from every Alien<end>)\n";
		$list .= "<tab><tab>=\n";
		$list .= "<tab>" . $this->itemsController->getItemAndIcon("Memory-Wiped Kyr'Ozch Viralbots", $misc_ql) . "\n";
		$list .= "<highlight>Required Skills:<end>\n";
		$list .= "- ".ceil($misc_ql * 4.5)." Computer Literacy\n";
		$list .= "- ".ceil($misc_ql * 4.5)." Nano Programming\n\n";

		$list .= "<header2>Step 2<end>\n";
		$list .= "<tab>" . $this->itemsController->getItemAndIcon("Nano Programming Interface", 1);
		$list .= " (<highlight>Can be bought in General Shops<end>)\n";
		$list .= "<tab><tab>+\n";
		$list .= "<tab>" . $this->itemsController->getItemAndIcon("Memory-Wiped Kyr'Ozch Viralbots", $misc_ql) . "\n";
		$list .= "<tab><tab>=\n";
		$list .= "<tab>" . $this->itemsController->getItemAndIcon("Formatted Kyr'Ozch Viralbots", $misc_ql) . "\n";
		$list .= "<highlight>Required Skills:<end>\n";
		$list .= "- ".ceil($misc_ql * 4.5)." Computer Literacy\n";
		$list .= "- ".ceil($misc_ql * 6)." Nano Programming\n\n";

		$list .= "<header2>Step 3<end>\n";
		$list .= "<tab>" . $this->itemsController->getItemAndIcon("Kyr'Ozch Structural Analyzer", 100) . "\n";
		$list .= "<tab><tab>+\n";
		$list .= "<tab>" . $this->itemsController->getItemAndIcon("Solid Clump of Kyr'Ozch Bio-Material", $ql) . " QL$ql";
		$list .= " (<highlight>Drops from every Alien<end>)\n";
		$list .= "<tab><tab>=\n";
		$list .= "<tab>" . $this->itemsController->getItemAndIcon("Mutated Kyr'Ozch Bio-Material", $ql) . " QL$ql";
		$list .= "\n\nor\n\n<tab>" . $this->itemsController->getItemAndIcon("Pristine Kyr'Ozch Bio-Material", $ql) . " QL$ql\n";
		$list .= "<highlight>Required Skills:<end>\n";
		$list .= "- ".ceil($ql * 4.5)." Chemistry (Both require the same amount)\n\n";

		$list .= "<header2>Step 4<end>\n";
		$list .= "<tab>" . $this->itemsController->getItemAndIcon("Mutated Kyr'Ozch Bio-Material", $ql) . " QL$ql";
		$list .= "\n\nor\n\n<tab>" . $this->itemsController->getItemAndIcon("Pristine Kyr'Ozch Bio-Material", $ql) . " QL$ql\n";
		$list .= "<tab><tab>+\n";
		$list .= "<tab>" . $this->itemsController->getItemAndIcon("Uncle Bazzit's Generic Nano-Solvent", 100);
		$list .= " (<highlight>Can be bought in Bazzit Shop in MMD<end>)\n";
		$list .= "<tab><tab>=\n";
		$list .= "<tab>" . $this->itemsController->getItemAndIcon("Generic Kyr'Ozch DNA-Soup", $ql) . "\n";
		$list .= "<highlight>Required Skills:<end>\n";
		$list .= "- ".ceil($ql * 4.5)." Chemistry(for Pristine)\n";
		$list .= "- ".ceil($ql * 7)." Chemistry(for Mutated)\n\n";

		$list .= "<header2>Step 5<end>\n";
		$list .= "<tab>" . $this->itemsController->getItemAndIcon("Generic Kyr'Ozch DNA-Soup", $ql) . "\n";
		$list .= "<tab><tab>+\n";
		$list .= "<tab>" . $this->itemsController->getItemAndIcon("Essential Human DNA", 100);
		$list .= " (<highlight>Can be bought in Bazzit Shop in MMD<end>)\n";
		$list .= "<tab><tab>=\n";
		$list .= "<tab>" . $this->itemsController->getItemAndIcon("DNA Cocktail", $ql) . "\n";
		$list .= "<highlight>Required Skills:<end>\n";
		$list .= "- ".ceil($ql * 6)." Pharma Tech\n\n";

		$list .= "<header2>Step 6<end>\n";
		$list .= "<tab>" . $this->itemsController->getItemAndIcon("Formatted Kyr'Ozch Viralbots", $misc_ql) . "\n";
		$list .= "<tab><tab>+\n";
		$list .= "<tab>" . $this->itemsController->getItemAndIcon("DNA Cocktail", $ql) . "\n";
		$list .= "<tab><tab>=\n";
		$list .= "<tab>" . $this->itemsController->getItemAndIcon("Kyr'Ozch Formatted Viralbot Solution", $ql) . "\n";
		$list .= "<highlight>Required Skills:<end>\n";
		$list .= "- ".ceil($ql * 6)." Pharma Tech\n\n";

		$list .= "<header2>Step 7<end>\n";
		$list .= "<tab>" . $this->itemsController->getItemAndIcon("Kyr'Ozch Formatted Viralbot Solution", $ql) . "\n";
		$list .= "<tab><tab>+\n";
		$list .= "<tab>" . $this->itemsController->getItemAndIcon("Basic Fashion Vest", 1) . " (<highlight>Can be obtained by the Basic Armor Quest<end>)\n";
		$list .= "<tab><tab>=\n";
		$list .= "<tab>" . $this->itemsController->getItemAndIcon("Formatted Viralbot Vest", $ql) . "\n\n";

		$list .= "<header2>Step 8<end>\n";

		$vb_ql = floor($ql * 0.8);
		switch ($armortype) {
			case "Arithmetic":
				$list .= "<tab>" . $this->itemsController->getItemAndIcon("Arithmetic Lead Viralbots", $vb_ql) . " QL$vb_ql";
				$list .= " (<highlight>Rare Drop off Alien City Generals<end>)\n";
				break;
			case "Supple":
				$list .= "<tab>" . $this->itemsController->getItemAndIcon("Supple Lead Viralbots", $vb_ql) . " QL$vb_ql";
				$list .= " (<highlight>Rare Drop off Alien City Generals<end>)\n";
				break;
			case "Enduring":
				$list .= "<tab>" . $this->itemsController->getItemAndIcon("Enduring Lead Viralbots", $vb_ql) . " QL$vb_ql";
				$list .= " (<highlight>Rare Drop off Alien City Generals<end>)\n";
				break;
			case "Observant":
				$list .= "<tab>" . $this->itemsController->getItemAndIcon("Observant Lead Viralbots", $vb_ql) . " QL$vb_ql";
				$list .= " (<highlight>Rare Drop off Alien City Generals<end>)\n";
				break;
			case "Strong":
				$list .= "<tab>" . $this->itemsController->getItemAndIcon("Strong Lead Viralbots", $vb_ql) . " QL$vb_ql";
				$list .= " (<highlight>Rare Drop off Alien City Generals<end>)\n";
				break;
			case "Spiritual":
				$list .= "<tab>" . $this->itemsController->getItemAndIcon("Spiritual Lead Viralbots", $vb_ql) . " QL$vb_ql";
				$list .= " (<highlight>Rare Drop off Alien City Generals<end>)\n";
				break;
		}
		$list .= "<tab><tab>+\n";
		$list .= "<tab>" . $this->itemsController->getItemAndIcon("Formatted Viralbot Vest", $ql) . "\n";
		$list .= "<tab><tab>=\n";
		switch ($armortype) {
			case "Arithmetic":
				$list .= "<tab>" . $this->itemsController->getItemAndIcon("Arithmetic Body Armor", $ql) . " QL$ql\n";
				break;
			case "Supple":
				$list .= "<tab>" . $this->itemsController->getItemAndIcon("Supple Body Armor", $ql) . " QL$ql\n";
				break;
			case "Enduring":
				$list .= "<tab>" . $this->itemsController->getItemAndIcon("Enduring Body Armor", $ql) . " QL$ql\n";
				break;
			case "Observant":
				$list .= "<tab>" . $this->itemsController->getItemAndIcon("Observant Body Armor", $ql) . " QL$ql\n";
				break;
			case "Strong":
				$list .= "<tab>" . $this->itemsController->getItemAndIcon("Strong Body Armor", $ql) . " QL$ql\n";
				break;
			case "Spiritual":
				$list .= "<tab>" . $this->itemsController->getItemAndIcon("Spiritual Body Armor", $ql) . " QL$ql\n";
				break;
		}
		$list .= "<highlight>Required Skills:<end>\n";
		$list .= "- ".floor($ql * 6)." Psychology\n\n";

		$msg = $this->text->make_blob("Building process for $ql $armortype", $list);
		$sendto->reply($msg);
	}

	/**
	 * This command handler shows tradeskill process for combined Alien Armor.
	 *
	 * @HandlesCommand("aiarmor")
	 * @Matches("/^aiarmor (cc|cm|co|cp|cs|css|ss)$/i")
	 * @Matches("/^aiarmor (cc|cm|co|cp|cs|css|ss) (\d+)$/i")
	 * @Matches("/^aiarmor (\d+) (cc|cm|co|cp|cs|css|ss)$/i")
	 */
	public function aiarmorCombinedCommand($message, $channel, $sender, $sendto, $args) {
		list($armortype, $ql) = $this->extractArgs($args);
		$trg_ql = $ql;
		$src_ql = floor($trg_ql * 0.8);

		switch ($armortype) {
			case 'cc':
				$name_armor_result = "Combined Commando's Jacket";

				$name_armor_src = "Strong Body Armor";
				$nameSrc = "strong";

				$name_armor_trg = "Supple Body Armor";
				$nameTarget = "supple";
				break;

			case 'cm':
				$name_armor_result = "Combined Mercenary's Jacket";

				$name_armor_src = "Strong Body Armor";
				$nameSrc = "strong";

				$name_armor_trg = "Enduring Body Armor";
				$nameTarget = "enduring";
				break;

			case 'co':
				$name_armor_result = "Combined Officer's Jacket";

				$name_armor_src = "Spiritual Body Armor";
				$nameSrc = "spiritual";

				$name_armor_trg = "Arithmetic Body Armor";
				$nameTarget = "arithmetic";
				break;

			case 'cp':
				$name_armor_result = "Combined Paramedic's Jacket";

				$name_armor_src = "Spiritual Body Armor";
				$nameSrc = "spiritual";

				$name_armor_trg = "Enduring Body Armor";
				$nameTarget = "enduring";
				break;

			case 'cs':
				$name_armor_result = "Combined Scout's Jacket";

				$name_armor_src = "Observant Body Armor";
				$nameSrc = "observant";

				$name_armor_trg = "Arithmetic Body Armor";
				$nameTarget = "arithmetic";
				break;

			case 'css':
			case 'ss':
				$name_armor_result = "Combined Sharpshooter's Jacket";

				$name_armor_src = "Observant Body Armor";
				$nameSrc = "observant";

				$name_armor_trg = "Supple Body Armor";
				$nameTarget = "supple";
				break;
		}

		$list = "<header2>Result<end>\n";
		$list .= $this->itemsController->getItemAndIcon($name_armor_result, $ql) . " QL$ql\n\n";

		$list .= "<header2>Source Armor<end>\n";
		$list .= $this->itemsController->getItemAndIcon($name_armor_src, $src_ql) . " QL$src_ql";
		$list .= " (" . $this->text->make_chatcmd("Tradeskill process for this item", "/tell <myname> aiarmor $nameSrc $src_ql") . ")\n\n";

		$list .= "<header2>Target Armor<end>\n";
		$list .= $this->itemsController->getItemAndIcon($name_armor_trg, $trg_ql) . " QL$trg_ql";
		$list .= " (" . $this->text->make_chatcmd("Tradeskill process for this item", "/tell <myname> aiarmor $nameTarget $trg_ql") . ")";
		$msg = $this->text->make_blob("Building process for $ql $name_armor_result", $list);
		$sendto->reply($msg);
	}
	
	/**
	 * Extracts armor type and quality from given $args regexp matches.
	 */
	private function extractArgs($args) {
		$armortype = '';
		$ql = 300;
		// get ql and armor type from command arguments
		for ($i = 1; $i < count($args); $i++) {
			$value = $args[$i];
			if (is_numeric($value)) {
				if ($value >= 1 && $value <= 300) {
					$ql = intval($value);
				}
			} else {
				$armortype = strtolower($value);
			}
		}
		return array ($armortype, $ql);
	}
}
