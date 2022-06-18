<?php

declare(strict_types = 1);

namespace lobby\registry;

use lobby\registry\hologram\{
	LobbyGreetings,
	Parkour,
	TopVoter
};

use lobby\registry\npc\{
	Athie,
	HCF,
	Lobby
};

use core\essence\EssenceManager;

use core\network\NetworkManager;

use core\world\WorldManager;
use core\world\area\Area;

use core\utils\Manager;

use pocketmine\world\Position;

use pocketmine\Server;

class RegistryManager extends Manager {
	public static $instance = null;

    public function init() {
		self::$instance = $this;

		EssenceManager::getInstance()->initHologram(new LobbyGreetings());
		EssenceManager::getInstance()->initHologram(new Parkour());
		EssenceManager::getInstance()->initHologram(new TopVoter());

		EssenceManager::getInstance()->initNPC(new Athie());
		EssenceManager::getInstance()->initNPC(new HCF());
		EssenceManager::getInstance()->initNPC(new Lobby());

		NetworkManager::getInstance()->initServer(new Lobby());

		WorldManager::getInstance()->initArea(new Area("Lobby", new Position(0, 0, 0, Server::getInstance()->getWorldManager()->getWorldByName("nether")), new Position(2000, 256, 2000, Server::getInstance()->getWorldManager()->getWorldByName("lobby")), true, false));
	}

	public static function getInstance() : self {
    	return self::$instance;
	}
}