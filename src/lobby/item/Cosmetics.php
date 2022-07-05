<?php

declare(strict_types = 1);

namespace lobby\item;


use core\utils\CustomItem;

use lobby\Lobby;
use lobby\player\LobbyPlayer;

use lobby\item\form\CosmeticsForm;

use pocketmine\block\Block;

use pocketmine\item\{
	ItemIdentifier,
	ItemIds,
	ItemUseResult
};

use pocketmine\math\Vector3;

use pocketmine\player\Player;

use pocketmine\utils\TextFormat;

class Cosmetics extends CustomItem {
    public function __construct() {
        parent::__construct(new ItemIdentifier(ItemIds::ENDER_CHEST, 0), "Cosmetics", TextFormat::AQUA . "Cosmetics", [TextFormat::GOLD . "Tap somewhere to open the Cosmetics menu"], 1);
    }

    public function onClickAir(Player $player, Vector3 $directionVector) : ItemUseResult {
		if($player instanceof LobbyPlayer) {
			new CosmeticsForm($player);
			$player->sendMessage(Lobby::PREFIX . "Opened Cosmetics menu");
			return ItemUseResult::SUCCESS();
		}
		return ItemUseResult::FAIL();
	}

	public function onInteractBlock(Player $player, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector) : ItemUseResult {
		if($player instanceof LobbyPlayer) {
			new CosmeticsForm($player);
			$player->sendMessage(Lobby::PREFIX . "Opened Cosmetics menu");
			return ItemUseResult::SUCCESS();
		}
		return ItemUseResult::FAIL();
	}
}