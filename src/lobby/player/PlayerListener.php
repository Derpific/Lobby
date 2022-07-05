<?php

declare(strict_types = 1);

namespace lobby\player;

use core\Core;

use core\player\rank\Rank;

use lobby\Lobby;

use pocketmine\event\Listener;

use pocketmine\event\player\{
    PlayerCreationEvent,
    PlayerExhaustEvent,
    PlayerInteractEvent,
    PlayerJoinEvent,
    PlayerToggleFlightEvent,
	PlayerQuitEvent,
	PlayerMoveEvent
};

use pocketmine\event\entity\{
    EntityDamageEvent,
    EntityDamageByEntityEvent
};

use pocketmine\player\GameMode;

class PlayerListener implements Listener {
    private $manager;

    public function __construct(PlayerManager $manager) {
		$this->manager = $manager;
    }    

	public function onPlayerCreation(PlayerCreationEvent $event) {
		$event->setPlayerClass(LobbyPlayer::class);
    }

    public function onPlayerExhaustEvents(PlayerExhaustEvent $event) {
        $player = $event->getPlayer();

        if($player instanceof LobbyPlayer) {
			if(!$player->flying()) {
				if($event->getCause() === PlayerExhaustEvent::CAUSE_JUMPING or $event->getCause() === PlayerExhaustEvent::CAUSE_SPRINT_JUMPING) {
					$player->doubleJump = time();
					$player->setAllowFlight(true);
				}
			}
        }
    }

    /**
    public function onPlayerInteract(PlayerInteractEvent $event) {
        $player = $event->getPlayer();
        $item = $event->getItem();

		if($player instanceof LobbyPlayer) {
			$nbt = $item->getNamedTag();
			if($nbt->getString("Cosmetics", "") !== "") {
				$player->sendCosmeticsForm();
				$player->sendMessage($this->lobby::PREFIX . "Opened Cosmetics menu");
			}
			if($nbt->getString("Gadgets", "") !== "") {
				$player->sendGadgetsForm();
				$player->sendMessage($this->lobby::PREFIX . "Opened Gadgets menu");
			}
			if($nbt->getString("Profile", "") !== "") {
				$player->sendProfileForm();
				$player->sendMessage($this->lobby::PREFIX . "Opened Profile menu");
			}
			if($nbt->getString("Server Selector", "") !== "") {
				$player->sendServerSelectorForm();
				$player->sendMessage($this->lobby::PREFIX . "Opened Servers menu");
			}
		}
    }*/

    public function onPlayerJoin(PlayerJoinEvent $event) {
        $player = $event->getPlayer();

        if($player instanceof LobbyPlayer) {
            $player->setLobby(Lobby::getInstance());
            $player->joinLobby();
        }
    }

    public function onPlayerToggleFlightEvents(PlayerToggleFlightEvent $event) {
        $player = $event->getPlayer();
        
        if($player->getGameMode() == GameMode::SURVIVAL()) {
            if($player instanceof LobbyPlayer) {
				if(!$player->flying()) {
					$event->cancel();
					 
					if(($player->doubleJump - time()) <= 2) {
						$player->doubleJump = null;

						$player->setAllowFlight(false);

						$directionVector = $player->getDirectionVector()->multiply(2);
						$directionVector->y = 1.1;

						$player->setMotion($directionVector);
						$player->setGamemode(GameMode::SURVIVAL());
					}
                }
            }
        }
    }

	public function onPlayerQuitEvents(PlayerQuitEvent $event) {
		$player = $event->getPlayer();

		if($player instanceof LobbyPlayer) {
			$player->leaveLobby();
		}
	}

    public function onEntityDamageEvents(EntityDamageEvent $event) {
        if($event instanceof EntityDamageByEntityEvent) {
            $victim = $event->getEntity();
            $damager = $event->getDamager();

            if($victim instanceof LobbyPlayer and $damager instanceof LobbyPlayer) {
                if($victim->getCoreUser()->getRank()->getValue() === Rank::STAFF) {
                    if(!$damager->hasPermission("lobby.essentials.staffpuncher")) {
                        $damager->sendMessage(Core::ERROR_PREFIX . "You don't have permission to Punch a Staff!");
					}
					if(!$victim->hasPermission("lobby.essentials.staffpuncher.exempt")) {
                        $damager->sendMessage(Core::ERROR_PREFIX . "This Staff is special. Can't punch him today!");
                    } else {
                        $victim->knockBack(0, 6, 0, 1.0);
                        $victim->sendMessage(Core::PREFIX . $damager->getName() . " Punched you! Staff disadvantages..");
                        $damager->sendMessage(Core::PREFIX . "You punched " . $victim->getName() . "! Take that Staff!");
                    }
                }
            }
        }
    }

	public function onPlayerMove(PlayerMoveEvent $event) {
		$player = $event->getPlayer();

		if($player instanceof LobbyPlayer) {
			if(!$event->getFrom()->equals($event->getTo()->asPosition())) {
				if($player->updateArea()) {
					//$player->setMotion($event->getFrom()->subtract($player->getLocation()->normalize()->multiply(4)));
				}
			}
			if(!is_null($area = $player->getArea())) {
				if($area instanceof Lobby && $event->getTo()->getFloorY() < 0) {
					$player->teleport($player->getWorld()->getSafeSpawn());
				}
			}
		}
	}
}