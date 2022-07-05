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

class HCF extends NPC {
    public function __construct() {
        parent::__construct("HCF");
    }

    public function getPosition() : Position {
		$level = Server::getInstance()->getWorldManager()->getWorldByName("Lobby");

        return new Position(138.5, 80, 127.5, $level);
    }

	public function getSize() : EntitySizeInfo {
		return new EntitySizeInfo(5, 5);
	}

	public function getScale() : float {
		return 1.0;
	}

	public function getSkin() : Skin {
		$geometryData = file_get_contents(Path::join(\lobby\Lobby::getResourcesFolder(), "npc", "HCF.json"));
		$skin = new Skin($this->getName(), EntityUtils::skinFromImage(Path::join(\lobby\Lobby::getResourcesFolder(), "npc", "HCF.png")), "", "geometry.humanoid.custom", $geometryData);

		return $skin;
	}

    public function getNameTag() : string {
        return TextFormat::BOLD . TextFormat::DARK_GRAY . "[NPC]" . TextFormat::RESET . TextFormat::RED . " HCF\n" . TextFormat::GRAY . "Online: {ONLINE}\n" . TextFormat::GRAY . "{ONLINE_PLAYERS}{MAX_SLOTS}";
    }

    public function getHeldItem() : Item {
        return ItemFactory::getInstance()->get(ItemIds::DIAMOND_SWORD);
    }

    public function getArmor() : array {
        return [
            "helmet" => ItemFactory::getInstance()->get(ItemIds::DIAMOND_HELMET),
            "chestplate" => ItemFactory::getInstance()->get(ItemIds::DIAMOND_CHESTPLATE),
            "leggings" => ItemFactory::getInstance()->get(ItemIds::DIAMOND_LEGGINGS),
            "boots" => ItemFactory::getInstance()->get(ItemIds::DIAMOND_BOOTS)
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
		$ip = NetworkManager::getInstance()->getServer("HCF")->getIp();
		$port = NetworkManager::getInstance()->getServer("HCF")->getPort();

		return [
			"transfer " . $ip . " " . $port . " {PLAYER}"
		];
    }

    public function getMessages() : array {
        return [
            TextFormat::BOLD . TextFormat::DARK_GRAY . "[NPC]" . TextFormat::RESET . TextFormat::RED . " HCF" . TextFormat::DARK_GREEN . "> " . TextFormat::GRAY . "Hi {PLAYER}, HCF server is coming soon!",
            TextFormat::GRAY . "If you want to help test, contact us on Twitter! Send (@GratonePix) or our Discord a message!\n"
        ];
    }
}