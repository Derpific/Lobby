<?php

declare(strict_types = 1);

namespace lobby\registry\npc;

use core\essence\npc\NPC;
use core\network\NetworkManager;

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

class Lobby extends NPC {
    public function __construct() {
        parent::__construct("Lobby");
    }

    public function getPosition() : Position {
		$level = Server::getInstance()->getWorldManager()->getWorldByName("world");

        return new Position(132.5, 80, 128.5, $level);
    }

	public function getSize() : EntitySizeInfo {
		return new EntitySizeInfo(5, 5);
	}

	public function getScale() : float {
		return 1.0;
	}

    public function getNameTag() : string {
        return TextFormat::BOLD . TextFormat::DARK_GRAY . "[NPC]" . TextFormat::RESET . TextFormat::GREEN . " Lobby\n" . TextFormat::GRAY . "Online: {ONLINE}\n" . TextFormat::GRAY . "{ONLINE_PLAYERS}{MAX_SLOTS}";
    }

	public function getSkin() : Skin {
		$geometryData = file_get_contents(Path::join(\lobby\Lobby::getResourcesFolder(), "npc", "Lobby.json"));
		$skin = new Skin($this->getName(), EntityUtils::skinFromImage(Path::join(\lobby\Lobby::getResourcesFolder(), "npc", "Lobby.png")), "", "geometry.humanoid.custom", $geometryData);

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
        return true;
    }

    public function getMovement() : array {
        return [];
    }

	public function getMoveTime() : int {
		return 1;
	}

    public function getCommands() : array {
    	$ip = NetworkManager::getInstance()->getServer("Lobby")->getIp();
		$port = NetworkManager::getInstance()->getServer("Lobby")->getPort();

        return [
            "transfer " . $ip . " " . $port . " {PLAYER}"
        ];
    }

    public function getMessages() : array {
        return [
            TextFormat::BOLD . TextFormat::DARK_GRAY . "[NPC]" . TextFormat::RESET . TextFormat::GREEN . " Lobby" . TextFormat::DARK_GREEN . "> " . TextFormat::GRAY . "Hi {PLAYER}, reconnecting you to the Lobby!"
        ];
    }
}