<?php

declare(strict_types = 1);

namespace lobby\item;

use core\network\form\ServerSelectorForm;
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

class ServerSelector extends CustomItem {
    public function __construct() {
        parent::__construct(new ItemIdentifier(ItemIds::END_PORTAL_FRAME, 0), "Server Selector", TextFormat::AQUA . "Server Selector", [TextFormat::GOLD . "Tap somewhere to open the Server Selection menu"], 1);
    }

	public function onClickAir(Player $player, Vector3 $directionVector) : ItemUseResult {
		if($player instanceof LobbyPlayer) {
			new ServerSelectorForm($player);
			$player->sendMessage(Lobby::PREFIX . "Opened Server Selector menu");
			return ItemUseResult::SUCCESS();
		}
		return ItemUseResult::FAIL();
	}

	public function onInteractBlock(Player $player, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector) : ItemUseResult {
		if($player instanceof LobbyPlayer) {
			new ServerSelectorForm($player);
			$player->sendMessage(Lobby::PREFIX . "Opened Server Selector menu");
			return ItemUseResult::SUCCESS();
		}
		return ItemUseResult::FAIL();
	}
}