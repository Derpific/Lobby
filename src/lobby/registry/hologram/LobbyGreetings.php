<?php

declare(strict_types = 1);

namespace lobby\registry\hologram;

use lobby\Lobby;

use core\essence\hologram\Hologram;

use core\player\CorePlayer;

use core\network\NetworkManager;

use pocketmine\Server;

use pocketmine\world\Position;
use pocketmine\world\particle\FloatingTextParticle;

use pocketmine\utils\TextFormat;

class LobbyGreetings extends Hologram {
    public function __construct() {
        parent::__construct("LobbyGreetings");
    }

    public function getPosition() : Position {
		$level = Server::getInstance()->getWorldManager()->getWorldByName("Lobby");
		
        return new Position(126, 15, 98, $level);
    }

	public function getText() : string {
        return Lobby::PREFIX . "Welcome to the Athena Lobby!\n" . TextFormat::GRAY . "There are currently {TOTAL_ONLINE_PLAYERS}/{TOTAL_MAX_SLOTS} Online!\n" . TextFormat::GRAY . "Pick a server, or just hang around in the Lobby";
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