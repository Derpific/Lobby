<?php

declare(strict_types = 1);

namespace lobby;

use core\Core;
use core\CorePlayer;

use lobby\item\{
    ServerSelector,
    Profile,
    Cosmetics,
    Gadgets
};

use lobby\trails\Trail;

use form\{
	MenuForm,
	CustomForm,
	CustomFormResponse
};
use form\element\{
	Button,
	Image,
	Label
};

use pocketmine\Player;

use pocketmine\network\mcpe\protocol\{
	AddActorPacket,
	MoveActorAbsolutePacket,
	RemoveActorPacket
};

use pocketmine\entity\{
	EffectInstance,
	Effect,
	Entity
};

use pocketmine\utils\TextFormat;

use pocketmine\level\Position;

class LobbyPlayer extends CorePlayer {
    /**
     * @var Lobby
     */
    private $lobby;
	/**
	 * @var Trail|null
	 */
    public $trail = null;

    public $doubleJump = [];

    public function setLobby(Lobby $lobby) {
        $this->lobby = $lobby;
    }

    public function joinLobby() {
        $this->getLevel()->setTime(5000);
        $this->getLevel()->stopTime();
        $this->addEffect(new EffectInstance(Effect::getEffect(Effect::SPEED), 9999999, 1, false));
        $this->getInventory()->clearAll();
        $this->getInventory()->setItem(1, new ServerSelector());
        $this->getInventory()->setItem(3, new Profile());
        $this->getInventory()->setItem(5, new Cosmetics());
        $this->getInventory()->setItem(7, new Gadgets());
    }

    public function leaveLobby() {
    	$this->despawnTrail();

		if($this->isMorphed()) {
			$this->removeMorph();
		}
	}

	public function sendCosmeticsForm() {
		//ToDo: Cosmetics (Armor, Changing Armor, Dance)
		$b1 = new Button(TextFormat::GRAY . "Trails");

		$b1->setId(1);

		$options = [
			$b1
		];

		$this->sendForm(new MenuForm(TextFormat::GOLD . "Cosmetics", TextFormat::LIGHT_PURPLE . "Select a Cosmetic!", $options,
			function(Player $player, Button $selectedOption) : void {
				if($player instanceof LobbyPlayer) {
					switch($selectedOption->getId()) {
						case 1:
							if($player->hasPermission("lobby.trails.use")) {
								$player->sendTrailsForm();
							}
						break;
					}
				}
			},
			function(Player $player) : void {
				$player->sendMessage(Lobby::getInstance()->getPrefix() . "Closed Cosmetics menu");
			}
		));
	}

	public function sendGadgetsForm() {
		//TODO: Gadgets (Hide Players, Fly, Pets)
		$e1 = new Label(TextFormat::GRAY . "Coming Soon..");

		$e1->setValue(1);

		$elements = [
			$e1
		];

		$this->sendForm(new CustomForm(TextFormat::GOLD . "Gadgets", $elements,
			function(Player $player, CustomFormResponse $response) : void {

			},
			function(Player $player) : void {
				$player->sendMessage(Lobby::getInstance()->getPrefix() . "Closed Gadgets menu");
			}
		));
	}

	public function sendTrailsForm() {
		$options = [];

		foreach($this->lobby->getTrails()->getAll() as $trail) {
			if($trail instanceof Trail) {
				if(empty($trail->getIcon())) {
					$b1 = new Button(TextFormat::GRAY . $trail->getName());

					$b1->setId($trail->getName());

					$options[] = $b1;
				}
				$b2 = new Button(TextFormat::GRAY . $trail->getName(), new Image($trail->getIcon(), Image::TYPE_URL));

				$b2->setId($trail->getName());

				$options[] = $b2;
			}
		}
		$this->sendForm(new MenuForm(TextFormat::GOLD . "Trails", TextFormat::LIGHT_PURPLE . "Select a Trail!", $options,
			function(Player $player, Button $button) : void {
				if($player instanceof LobbyPlayer) {
					$trail = Lobby::getInstance()->getTrails()->getTrail($button->getId());

					if($trail instanceof Trail) {
						if(!$player->hasPermission("lobby.trails." . $trail->getName())) {
							$player->sendMessage(Core::getInstance()->getErrorPrefix() . "You do not have Permission to use this Trail");
						}
						if($player->getTrail()->getName() === $trail->getName()) {
							$player->sendMessage(Core::getInstance()->getErrorPrefix() . "You already have the Trail " . $trail->getName() . " Applied");
						} else {
							if(!is_null($player->getTrail())) {
								$player->sendMessage(Lobby::getInstance()->getPrefix() . "Removed your old Trail");
							}
							$player->spawnTrail($trail);
							$player->sendMessage(Lobby::getInstance()->getPrefix() . "Applied the Trail: " . $trail->getName());
						}
					}
				}
			},
			function(Player $player) : void {
				$player->sendMessage(Lobby::getInstance()->getPrefix() . "Closed Trails menu");
			}
		));
	}

	public function isMorphed() {
    	return isset($this->lobby->getMorph()->morphs[$this->getName()]);
	}

	public function getMorph() : string {
    	return $this->lobby->getMorph()->morphs[$this->getName()];
	}

	public function morph(int $id) {
		$pk = new AddActorPacket();
		$pk->entityRuntimeId = Entity::$entityCount++;
		$pk->type = $id;
		$pk->position = $this->getPosition();

		$this->lobby->getMorph()->morphs[$this->getName()][$id] = $pk->entityRuntimeId;
		$this->setInvisible(true);
		$this->sendDataPacket($pk);
		$this->getServer()->broadcastPacket($this->getServer()->getOnlinePlayers(), $pk);
	}

	public function moveMorph() {
		$pk = new MoveActorAbsolutePacket();
		$pk->entityRuntimeId = array_key_last($this->lobby->getMorph()->morphs[$this->getName()]);
		$pk->position = $this->asVector3()->subtract(0, 0.4, 0);
		$pk->xRot = $this->pitch;
		$pk->yRot = $this->yaw;
		$pk->zRot = $this->yaw;

		$this->sendDataPacket($pk);
		$this->getServer()->broadcastPacket($this->getServer()->getOnlinePlayers(), $pk);
	}

	public function removeMorph() {
		$pk = new RemoveActorPacket();
		$pk->entityRuntimeId = array_key_last($this->lobby->getMorph()->morphs[$this->getName()]);

		unset($this->lobby->getMorph()->morphs[$this->getName()]);
		$this->setInvisible(false);
		$this->sendDataPacket($pk);
		$this->getServer()->broadcastPacket($this->getServer()->getOnlinePlayers(), $pk);
	}

	public function getTrail() : ?Trail {
    	return $this->trail;
	}

	public function spawnTrail(?Trail $trail) {
		$this->trail = $trail;
	}

	public function updateTrail() {
		$y = $this->y;
		$y2 = $y + 0.5;
		$y3 = $y2 + 1.4;

		$this->getLevel()->addParticle($this->lobby->getTrails()->convertTrail($this->getTrail()->getName(), new Position($this->x, mt_rand($y, rand($y2, $y3)), $this->z)));
	}

	public function despawnTrail() {
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
				$y = $this->y;
				$y2 = $y + 0.5;
				$y3 = $y2 + 1.4;

				$this->getLevel()->addParticle($this->lobby->getTrails()->convertTrail($trail, new Position($this->x, mt_rand($y, rand($y2, $y3)), $this->z)));
				$this->trail = $this->lobby->getTrails()->getTrail($trail);
			}
		}
	}
}