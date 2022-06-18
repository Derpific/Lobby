<?php

declare(strict_types = 1);

namespace lobby\item;

use core\player\form\ProfileForm;
use core\utils\CustomItem;
use lobby\Lobby;
use lobby\player\LobbyPlayer;

use pocketmine\block\Block;

use pocketmine\item\{
	ItemIdentifier,
	ItemIds,
	ItemUseResult
};

use pocketmine\math\Vector3;

use pocketmine\player\Player;

use pocketmine\utils\TextFormat;

class Profile extends CustomItem {
    public function __construct() {
        parent::__construct(new ItemIdentifier(ItemIds::MOB_HEAD, 0), "Profile", TextFormat::AQUA . "Profile", [TextFormat::GOLD . "Tap somewhere to open your Profile menu"], 1);
    }

	public function onClickAir(Player $player, Vector3 $directionVector) : ItemUseResult {
		if($player instanceof LobbyPlayer) {
			new ProfileForm($player);
			$player->sendMessage(Lobby::PREFIX . "Opened Profile menu");
			return ItemUseResult::SUCCESS();
		}
		return ItemUseResult::FAIL();
	}

	public function onInteractBlock(Player $player, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector) : ItemUseResult {
		if($player instanceof LobbyPlayer) {
			new ProfileForm($player);
			$player->sendMessage(Lobby::PREFIX . "Opened Profile menu");
			return ItemUseResult::SUCCESS();
		}
		return ItemUseResult::FAIL();
	}
}