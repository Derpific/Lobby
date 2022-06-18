<?php

declare(strict_types = 1);

namespace lobby\player;

use core\player\CorePlayer;

use core\utils\EntityUtils;

use lobby\item\{
    ServerSelector,
    Profile,
    Cosmetics,
    Gadgets
};
use lobby\Lobby;
use lobby\trails\Trail;

use pocketmine\entity\effect\VanillaEffects;
use pocketmine\entity\Entity;

use pocketmine\network\mcpe\protocol\{
	AddActorPacket,
	MoveActorAbsolutePacket,
	RemoveActorPacket
};
use pocketmine\Server;

use pocketmine\world\Position;

use pocketmine\entity\effect\EffectInstance;

class LobbyPlayer extends CorePlayer {
    /**
     * @var Lobby
     */
    private $lobby;
	/**
	 * @var Trail|null
	 */
    public $trail = null;

    public $doubleJump = [], $morph = [];

    public function setLobby(Lobby $lobby) {
        $this->lobby = $lobby;
    }

    public function joinLobby() {
		if($this->isOnline()) {
			$this->getWorld()->setTime(5000);
			$this->getWorld()->stopTime();
			$this->getEffects()->add(new EffectInstance(VanillaEffects::SPEED(), 9999999, 1, false));
			$this->getInventory()->clearAll();
			$this->getInventory()->setItem(1, new ServerSelector());
			$this->getInventory()->setItem(3, new Profile());
			$this->getInventory()->setItem(5, new Cosmetics());
			$this->getInventory()->setItem(7, new Gadgets());
			Lobby::getInstance()->getScheduler()->scheduleDelayedTask(new PlayerJoinTask($this->lobby, $this), 20);
		}
    }

    public function leaveLobby() {
    	if($this->getTrail() instanceof Trail) {
			$this->removeTrail();
		}
		if(!is_null($this->getMorph())) {
			$this->removeMorph();
		}
	}

	public function getMorph() : ?string {
    	return $this->morph[1] ?? null;
	}
	
	public function morph(int $id) {
		$pk = AddActorPacket::create(Entity::nextRuntimeId(), Entity::nextRuntimeId(), $id, $this->getPosition());
		$this->morph[$id] = $pk->entityRuntimeId;
		$this->setInvisible(true);
		$this->getNetworkSession()->sendDataPacket($pk);

		foreach(Server::getInstance()->getOnlinePlayers() as $player) {
			$player->getNetworkSession()->broadcastPacket($pk);
		}
	}

	public function moveMorph() {
		$array = end($this->morph);
		$key = key($array);

    	$pk = MoveActorAbsolutePacket::create($key, $this->asVector3()->subtract(0, 0.4, 0), $this->pitch, $this->yaw, $this->yaw, 0);

		$this->getNetworkSession()->sendDataPacket($pk);

		foreach(Server::getInstance()->getOnlinePlayers() as $player) {
			$player->getNetworkSession()->broadcastPacket($pk);
		}
	}

	public function removeMorph() {
		$array = end($this->morph);
		$key = key($array);
		$pk = RemoveActorPacket::create($key);

		unset($array);
		$this->setInvisible(false);
		$this->getNetworkSession()->sendDataPacket($pk);

		foreach(Server::getInstance()->getOnlinePlayers() as $player) {
			$player->getNetworkSession()->broadcastPacket($pk);
		}
	}

	public function getTrail() : ?Trail {
    	return $this->trail;
	}

	public function spawnTrail(?Trail $trail) {
		$this->trail = $trail;
	}

	public function updateTrail() {
		$y = $this->getPosition()->getY();
		$y2 = $y + 0.5;
		$y3 = $y2 + 1.4;

		$this->getWorld()->addParticle(EntityUtils::getParticle($this->getTrail()->getName(), new Position($this->x, mt_rand($y, rand($y2, $y3)), $this->z)));
	}

	public function removeTrail() {
    	$this->trail = null;
	}

	public function randomTrail() {
		foreach($this->lobby->getTrails()->getAll() as $trail) {
			if($trail instanceof Trail) {
				$random = round(rand(0, 114));
				$int = rand(1, 4);
				$trail = null;

				switch($int) {
					case 1:
						$trail = "item_" . $random;
						break;
					case 2:
						$trail = "block_" . $random;
						break;
					case 3:
						$trail = "destroyblock_" . $random;
						break;
					case 4:
						$trail = $random;
						break;
				}
				$y = $this->getPosition()->getY();
				$y2 = $y + 0.5;
				$y3 = $y2 + 1.4;

				$this->getWorld()->addParticle(EntityUtils::getParticle($trail, new Position($this->x, mt_rand($y, rand($y2, $y3)), $this->z)));
				$this->trail = $this->lobby->getTrails()->get($trail);
			}
		}
	}
}