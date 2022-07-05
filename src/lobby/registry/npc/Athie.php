<?php

declare(strict_types = 1);

namespace lobby\registry\npc;

use core\essence\npc\NPC;

use core\utils\EntityUtils;

use Webmozart\PathUtil\Path;

use pocketmine\entity\EntitySizeInfo;

use pocketmine\item\{
	ItemFactory,
	ItemIds
};

use pocketmine\Server;

use pocketmine\world\Position;

use pocketmine\entity\Skin;

use pocketmine\utils\TextFormat;

use pocketmine\item\Item;

class Athie extends NPC {
    public function __construct() {
        parent::__construct("LobbyGreetings");
    }

    public function getPosition() : Position {
    	$level = Server::getInstance()->getWorldManager()->getWorldByName("Lobby");

        return new Position(126, 80, 115, $level);
    }

    public function getSize() : EntitySizeInfo {
        return new EntitySizeInfo(5, 5);
    }

    public function getScale() : float {
		return 1.0;
	}

	public function getNameTag() : string {
        return TextFormat::BOLD . TextFormat::DARK_GRAY . "[NPC]" . TextFormat::RESET . TextFormat::BLUE . " Athie";
    }

    public function getSkin() : Skin {
		$geometryData = file_get_contents(Path::join(\lobby\Lobby::getResourcesFolder(), "npc", "Athie.json"));
		$skin = new Skin($this->getName(), EntityUtils::skinFromImage(Path::join(\lobby\Lobby::getResourcesFolder(), "npc", "Athie.png")), "", "geometry.humanoid.custom", $geometryData);

		return $skin;
    }

    public function getHeldItem() : Item {
        return ItemFactory::getInstance()->get(ItemIds::AIR);
    }

    public function getArmor() : array {
        return [
            "helmet" => "",
            "chestplate" => "",
            "leggings" => "",
            "boots" => ""
        ];
    }

    public function rotate() : bool {
        return false;
    }

    public function getMovement() : array {
        return [
            1 => "126, 13, 114, Lobby",
            2 => "126, 13, 113, Lobby",
            3 => "126, 13, 112, Lobby",
			4 => "126, 13, 111, Lobby",
			5 => "126, 13, 110, Lobby",
			6 => "126, 13, 109, Lobby",
        ];
    }
	
	public function getMoveTime() : int {
		return 10;
	}

    public function getCommands() : array {
		return [];
    }

    public function getMessages() : array {
        return [
            TextFormat::BOLD . TextFormat::DARK_GRAY . "[NPC]" . TextFormat::RESET . TextFormat::BLUE . " Athie" . TextFormat::DARK_GREEN . "> " . TextFormat::GRAY . "Hi {PLAYER}, I'm better than Derpific!"
        ];
    }
}