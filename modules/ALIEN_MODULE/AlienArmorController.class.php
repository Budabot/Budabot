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
		$list .= "<tab>" . $this->text->make_image(100330) . "\n";
		$list .= $this->text->make_item(247113, 247114, $misc_ql, "Kyr'Ozch Viralbots");
		$list .= " (<highlight>Drops of Alien City Generals<end>)\n";
		$list .= "<tab><tab>+\n";
		$list .= "<tab>" . $this->text->make_image(247098) . "\n";
		$list .= $this->text->make_item(247099, 247099, 100, "Kyr'Ozch Atomic Re-Structuralizing Tool");
		$list .= " (<highlight>Drops of every Alien<end>)\n";
		$list .= "<tab><tab>=\n";
		$list .= "<tab>" . $this->text->make_image(100331) . "\n";
		$list .= $this->text->make_item(247118, 247119, $misc_ql, "Memory-Wiped Kyr'Ozch Viralbots") . "\n";
		$list .= "<highlight>Required Skills:<end>\n";
		$list .= "- ".ceil($misc_ql * 4.5)." Computer Literacy\n";
		$list .= "- ".ceil($misc_ql * 4.5)." Nano Programming\n\n";

		$list .= "<header2>Step 2<end>\n";
		$list .= "<tab>" . $this->text->make_image(99279) . "\n";
		$list .= $this->text->make_item(161699, 161699, 1, "Nano Programming Interface");
		$list .= " (<highlight>Can be bought in General Shops<end>)\n";
		$list .= "<tab><tab>+\n";
		$list .= "<tab>" . $this->text->make_image(100331) . "\n";
		$list .= $this->text->make_item(247118, 247119, $misc_ql, "Memory-Wiped Kyr'Ozch Viralbots") . "\n";
		$list .= "<tab><tab>=\n";
		$list .= "<tab>" . $this->text->make_image(100334) . "\n";
		$list .= $this->text->make_item(247120, 247121, $misc_ql, "Formatted Kyr'Ozch Viralbots") . "\n";
		$list .= "<highlight>Required Skills:<end>\n";
		$list .= "- ".ceil($misc_ql * 4.5)." Computer Literacy\n";
		$list .= "- ".ceil($misc_ql * 6)." Nano Programming\n\n";

		$list .= "<header2>Step 3<end>\n";
		$list .= "<tab>" . $this->text->make_image(247097) . "\n";
		$list .= $this->text->make_item(247100, 247100, 100, "Kyr'Ozch Structural Analyzer") . "\n";
		$list .= "<tab><tab>+\n";
		$list .= "<tab>" . $this->text->make_image(247101) . "\n";
		$list .= $this->text->make_item(247102, 247103, $ql, "QL$ql Solid Clump of Kyr'Ozch Biomaterial");
		$list .= " (<highlight>Drops of every Alien<end>)\n";
		$list .= "<tab><tab>=\n";
		$list .= "<tab>" . $this->text->make_image(255705) . "\n";
		$list .= $this->text->make_item(247108, 247109, $ql, "QL$ql Mutated Kyr'Ozch Biomaterial");
		$list .= " or " . $this->text->make_item(247106, 247107, $ql, "QL$ql Pristine Kyr'Ozch Biomaterial") . "\n";
		$list .= "<highlight>Required Skills:<end>\n";
		$list .= "- ".ceil($ql * 4.5)." Chemistry (Both require the same amount)\n\n";

		$list .= "<header2>Step 4<end>\n";
		$list .= "<tab>" . $this->text->make_image(255705) . "\n";
		$list .= $this->text->make_item(247108, 247109, $ql, "QL$ql Mutated Kyr'Ozch Biomaterial");
		$list .= " or " . $this->text->make_item(247106, 247107, $ql, "QL$ql Pristine Kyr'Ozch Biomaterial") . "\n";
		$list .= "<tab><tab>+\n";
		$list .= "<tab>" . $this->text->make_image(100333) . "\n";
		$list .= $this->text->make_item(247110, 247110, 100, "Uncle Bazzit's Generic Nano Solvent");
		$list .= " (<highlight>Can be bought in Bazzit Shop in MMD<end>)\n";
		$list .= "<tab><tab>=\n";
		$list .= "<tab>" . $this->text->make_image(247115) . "\n";
		$list .= $this->text->make_item(247111, 247112, $ql, "Generic Kyr'Ozch DNA Soup") . "\n";
		$list .= "<highlight>Required Skills:<end>\n";
		$list .= "- ".ceil($ql * 4.5)." Chemistry(for Pristine)\n";
		$list .= "- ".ceil($ql * 7)." Chemistry(for Mutated)\n\n";

		$list .= "<header2>Step 5<end>\n";
		$list .= "<tab>" . $this->text->make_image(247115) . "\n";
		$list .= $this->text->make_item(247111, 247112, $ql, "Generic Kyr'Ozch DNA Soup") . "\n";
		$list .= "<tab><tab>+\n";
		$list .= "<tab>" . $this->text->make_image(247122) . "\n";
		$list .= $this->text->make_item(247123, 247123, 100, "Essential Human DNA");
		$list .= " (<highlight>Can be bought in Bazzit Shop in MMD<end>)\n";
		$list .= "<tab><tab>=\n";
		$list .= "<tab>" . $this->text->make_image(247116) . "\n";
		$list .= $this->text->make_item(247124, 247125, $ql, "DNA Cocktail") . "\n";
		$list .= "<highlight>Required Skills:<end>\n";
		$list .= "- ".ceil($ql * 6)." Pharma Tech\n\n";

		$list .= "<header2>Step 6<end>\n";
		$list .= "<tab>" . $this->text->make_image(100334) . "\n";
		$list .= $this->text->make_item(247120, 247121, $misc_ql, "Formatted Kyr'Ozch Viralbots") . "\n";
		$list .= "<tab><tab>+\n";
		$list .= "<tab>" . $this->text->make_image(247116) . "\n";
		$list .= $this->text->make_item(247124, 247125, $ql, "DNA Cocktail") . "\n";
		$list .= "<tab><tab>=\n";
		$list .= "<tab>" . $this->text->make_image(247117) . "\n";
		$list .= $this->text->make_item(247126, 247127, $ql, "Kyr'Ozch Formatted Viralbot Solution") . "\n";
		$list .= "<highlight>Required Skills:<end>\n";
		$list .= "- ".ceil($ql * 6)." Pharma Tech\n\n";

		$list .= "<header2>Step 7<end>\n";
		$list .= "<tab>" . $this->text->make_image(247117) . "\n";
		$list .= $this->text->make_item(247126, 247127, $ql, "Kyr'Ozch Formatted Viralbot Solution") . "\n";
		$list .= "<tab><tab>+\n";
		$list .= "<tab>" . $this->text->make_image(245924) . "\n";
		$list .= $this->text->make_item(247163, 247163, 1, "Basic Vest") . " (<highlight>Can be obtained by the Basic Armor Quest<end>)\n";
		$list .= "<tab><tab>=\n";
		$list .= "<tab>" . $this->text->make_image(245924) . "\n";
		$list .= $this->text->make_item(247172, 247173, $ql, "Formatted Viralbot Vest") . "\n\n";

		$list .= "<header2>Step 8<end>\n";
		$list .= "<tab>" . $this->text->make_image(100337) . "\n";

		$vb_ql = floor($ql * 0.8);
		switch ($armortype) {
			case "Arithmetic":
				$list .= $this->text->make_item(247144, 247145, $vb_ql, "QL$vb_ql Arithmetic Lead Viralbots");
				$list .= " (<highlight>Rare Drop off Alien City Generals<end>)\n";
				break;
			case "Supple":
				$list .= $this->text->make_item(247140, 247141, $vb_ql, "QL$vb_ql Supple Lead Viralbots");
				$list .= " (<highlight>Rare Drop off Alien City Generals<end>)\n";
				break;
			case "Enduring":
				$list .= $this->text->make_item(247136, 247137, $vb_ql, "QL$vb_ql Enduring Lead Viralbots");
				$list .= " (<highlight>Rare Drop off Alien City Generals<end>)\n";
				break;
			case "Observant":
				$list .= $this->text->make_item(247142, 247143, $vb_ql, "QL$vb_ql Observant Lead Viralbots");
				$list .= " (<highlight>Rare Drop off Alien City Generals<end>)\n";
				break;
			case "Strong":
				$list .= $this->text->make_item(247138, 247139, $vb_ql, "QL$vb_ql Strong Lead Viralbots");
				$list .= " (<highlight>Rare Drop off Alien City Generals<end>)\n";
				break;
			case "Spiritual":
				$list .= $this->text->make_item(247146, 247147, $vb_ql, "QL$vb_ql Spiritual Lead Viralbots");
				$list .= " (<highlight>Rare Drop off Alien City Generals<end>)\n";
				break;
		}
		$list .= "<tab><tab>+\n";
		$list .= "<tab>" . $this->text->make_image(245924) . "\n";
		$list .= $this->text->make_item(247172, 247173, $ql, "Formatted Viralbot Vest") . "\n";
		$list .= "<tab><tab>=\n";
		switch ($armortype) {
			case "Arithmetic":
				$list .= "<tab>" . $this->text->make_image(256314) . "\n";
				$list .= $this->text->make_item(246559, 246560, $ql, "QL$ql Arithmetic Body Armor") . "\n";
				break;
			case "Supple":
				$list .= "<tab>" . $this->text->make_image(256296) . "\n";
				$list .= $this->text->make_item(246621, 246622, $ql, "QL$ql Supple Body Armor") . "\n";
				break;
			case "Enduring":
				$list .= "<tab>" . $this->text->make_image(256344) . "\n";
				$list .= $this->text->make_item(246579, 246580, $ql, "QL$ql Enduring Body Armor") . "\n";
				break;
			case "Observant":
				$list .= "<tab>" . $this->text->make_image(256338) . "\n";
				$list .= $this->text->make_item(246591, 246592, $ql, "QL$ql Observant Body Armor") . "\n";
				break;
			case "Strong":
				$list .= "<tab>" . $this->text->make_image(256362) . "\n";
				$list .= $this->text->make_item(246615, 246616, $ql, "QL$ql Strong Body Armor") . "\n";
				break;
			case "Spiritual":
				$list .= "<tab>" . $this->text->make_image(256332) . "\n";
				$list .= $this->text->make_item(246600, 246601, $ql, "QL$ql Spiritual Body Armor") . "\n";
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
				//Result
				$icon_armor_result = 256308;
				$name_armor_result = "Combined Commando's";
				$lowid_armor_result = 246659;
				$highid_armor_result = 246660;
				//Source
				$icon_armor_src = 256362;
				$name_armor_src = "Strong";
				$lowid_armor_src = 246615;
				$highid_armor_src = 246616;

				//Target
				$icon_armor_trg = 256296;
				$name_armor_trg = "Supple";
				$lowid_armor_trg = 246621;
				$highid_armor_trg = 246622;
				break;

			case 'cm':
				//Result
				$icon_armor_result = 256356;
				$name_armor_result = "Combined Mercenary's";
				$lowid_armor_result = 246637;
				$highid_armor_result = 246638;

				//Source
				$icon_armor_src = 256362;
				$name_armor_src = "Strong";
				$lowid_armor_src = 246615;
				$highid_armor_src = 246616;

				//Target
				$icon_armor_trg = 256344;
				$name_armor_trg = "Enduring";
				$lowid_armor_trg = 246579;
				$highid_armor_trg = 246580;
				break;

			case 'co':
				//Result
				$icon_armor_result = 256320;
				$name_armor_result = "Combined Officer's";
				$lowid_armor_result = 246671;
				$highid_armor_result = 246672;

				//Source
				$icon_armor_src = 256332;
				$name_armor_src = "Spiritual";
				$lowid_armor_src = 246599;
				$highid_armor_src = 246600;

				//Target
				$icon_armor_trg = 256314;
				$name_armor_trg = "Arithmetic";
				$lowid_armor_trg = 246559;
				$highid_armor_trg = 246560;
				break;

			case 'cp':
				//Result
				$icon_armor_result = 256350;
				$name_armor_result = "Combined Paramedic's";
				$lowid_armor_result = 246647;
				$highid_armor_result = 246648;

				//Source
				$icon_armor_src = 256332;
				$name_armor_src = "Spiritual";
				$lowid_armor_src = 246599;
				$highid_armor_src = 246600;

				//Target
				$icon_armor_trg = 256344;
				$name_armor_trg = "Enduring";
				$lowid_armor_trg = 246579;
				$highid_armor_trg = 246580;
				break;

			case 'cs':
				//Result
				$icon_armor_result = 256326;
				$name_armor_result = "Combined Scout's";
				$lowid_armor_result = 246683;
				$highid_armor_result = 246684;

				//Source
				$icon_armor_src = 256338;
				$name_armor_src = "Observant";
				$lowid_armor_src = 246591;
				$highid_armor_src = 246592;

				//Target
				$icon_armor_trg = 256314;
				$name_armor_trg = "Arithmetic";
				$lowid_armor_trg = 246559;
				$highid_armor_trg = 246560;
				break;

			case 'css':
			case 'ss':
				//Result
				$icon_armor_result = 256302;
				$name_armor_result = "Combined Sharpshooter's";
				$lowid_armor_result = 246695;
				$highid_armor_result = 246696;

				//Source
				$icon_armor_src = 256338;
				$name_armor_src = "Observant";
				$lowid_armor_src = 246591;
				$highid_armor_src = 246592;

				//Target
				$icon_armor_trg = 256296;
				$name_armor_trg = "Supple";
				$lowid_armor_trg = 246621;
				$highid_armor_trg = 246622;
				break;
		}

		$list = "<header2>Result<end>\n";
		$list .= $this->text->make_image($icon_armor_result) . "\n";
		$list .= $this->text->make_item($lowid_armor_result, $highid_armor_result, $ql, "QL$ql $name_armor_result") . "\n\n";

		$list .= "<header2>Source Armor<end>\n";
		$list .= $this->text->make_image($icon_armor_src) . "\n";
		$list .= $this->text->make_item($lowid_armor_src, $highid_armor_src, $src_ql, "QL$src_ql $name_armor_src");
		$list .= " (" . $this->text->make_chatcmd("Tradeskill process for this item", "/tell <myname> aiarmor $name_armor_src $src_ql") . ")\n\n";

		$list .= "<header2>Target Armor<end>\n";
		$list .= $this->text->make_image($icon_armor_trg) . "\n";
		$list .= $this->text->make_item($lowid_armor_trg, $highid_armor_trg, $trg_ql, "QL$trg_ql $name_armor_trg");
		$list .= " (" . $this->text->make_chatcmd("Tradeskill process for this item", "/tell <myname> aiarmor $name_armor_trg $trg_ql") . ")";
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