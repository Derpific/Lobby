<?php

declare(strict_types = 1);

namespace lobby\registry\hologram;

use lobby\Lobby;

use core\player\CorePlayer;

use core\essence\hologram\Hologram;

use core\network\NetworkManager;

use pocketmine\Server;

use pocketmine\world\Position;
use pocketmine\world\particle\FloatingTextParticle;

class Parkour extends Hologram {
    public function __construct() {
        parent::__construct("ParkourManager");
    }

    public function getPosition() : Position {
		$level = Server::getInstance()->getLevelByName("Lobby");
		
        return new Position(113, 12, 91, $level);
    }

    public function getText() : string {
        return Lobby::PREFIX . "ParkourManager coming Soon!";
    }

	public function spawnTo(?CorePlayer $player = null) : void {
		$text = str_replace([
			"{TOTAL_ONLINE_PLAYERS}",
			"{TOTAL_MAX_SLOTS}"
		], [
			count(NetworkManager::getInstance()->getTotalOnlinePlayers()),
			NetworkManager::getInstance()->getTotalMaxSlots()
		], $this->getText());
		$this->particle = new FloatingTextParticle("", $text);

		$this->getPosition()->getWorld()->addParticle($this->getPosition()->add(0.5, 0, 0.5), $this->getParticle());
	}

	public function getUpdateTime() : ?int {
		return null;
	}
}