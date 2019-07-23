<?php

declare(strict_types = 1);

namespace lobby;

use core\Core;

use core\utils\CustomItem;

use core\mcpe\network\InventoryTransactionPacket;

use core\stats\rank\Rank;

use pocketmine\event\Listener;
use pocketmine\event\player\{
    PlayerCreationEvent,
    PlayerExhaustEvent,
    PlayerInteractEvent,
    PlayerJoinEvent,
    PlayerToggleFlightEvent,
	PlayerMoveEvent,
	PlayerQuitEvent
};
use pocketmine\event\entity\{
    EntityDamageEvent,
    EntityDamageByEntityEvent
};
use pocketmine\event\server\DataPacketReceiveEvent;

use pocketmine\nbt\tag\CompoundTag;

class LobbyListener implements Listener {
    private $lobby;

    public function __construct(Lobby $lobby) {
        $this->lobby = $lobby;
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

    public function onPlayerInteract(PlayerInteractEvent $event) {
        $player = $event->getPlayer();
        $item = $event->getItem();

		if($player instanceof LobbyPlayer) {
			if($item->getNamedTagEntry("Cosmetics")) {
				$player->sendCosmeticsForm();
				$player->sendMessage($this->lobby->getPrefix() . "Opened Cosmetics menu");
			}
			if($item->getNamedTagEntry("Gadgets")) {
				$player->sendGadgetsForm();
				$player->sendMessage($this->lobby->getPrefix() . "Opened Gadgets menu");
			}
			if($item->getNamedTagEntry("Profile")) {
				$player->sendProfileForm();
				$player->sendMessage($this->lobby->getPrefix() . "Opened Profile menu");
			}
			if($item->getNamedTagEntry("Server Selector")) {
				$player->sendServerSelectorForm();
				$player->sendMessage($this->lobby->getPrefix() . "Opened Servers menu");
			}
		}
    }

    public function onPlayerJoin(PlayerJoinEvent $event) {
        $player = $event->getPlayer();

        if($player instanceof LobbyPlayer) {
            $player->setLobby($this->lobby);
            $player->joinLobby();
        }
    }

    public function onPlayerToggleFlightEvents(PlayerToggleFlightEvent $event) {
        $player = $event->getPlayer();

        if($player->isSurvival()) {
            $event->setCancelled();

            if($player instanceof LobbyPlayer) {
				if(!$player->flying()) {
					if(($player->doubleJump - time()) <= 2) {
						$player->doubleJump = null;

						$player->setAllowFlight(false);

						$directionVector = $player->getDirectionVector()->multiply(2);
						$directionVector->y = 1.1;

						$player->setMotion($directionVector);
						$player->setGamemode(0);
					}
                }
            }
        }
    }

	public function onPlayerMoveEvents(PlayerMoveEvent $event) {
		$player = $event->getPlayer();

		if($player instanceof LobbyPlayer) {
			if(!is_null($player->getMorph())) {
				$player->moveMorph();
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
                    if(!$damager->hasPermission("lobby.essential.staffpuncher")) {
                        $damager->sendMessage(Core::getInstance()->getErrorPrefix() . "You don't have permission to Punch a Staff!");
					}
					if(!$victim->hasPermission("lobby.essential.staffpuncher.exempt")) {
                        $damager->sendMessage(Core::getInstance()->getErrorPrefix() . "This Staff is special. Can't punch him today!");
                    } else {
                        $victim->knockBack($victim, 0, 6, 0, 1);
                        $victim->sendMessage(Core::getInstance()->getPrefix() . $damager->getName() . " Punched you! Staff disadvantages..");
                        $damager->sendMessage(Core::getInstance()->getPrefix() . "You punched " . $victim->getName() . "! Take that Staff!");
                    }
                }
            }
        }
    }

    public function onDataPacketReceiveEvents(DataPacketReceiveEvent $event) {
    	$pk = $event->getPacket();
    	$player = $event->getPlayer();

    	if($player instanceof LobbyPlayer) {
			if($pk instanceof InventoryTransactionPacket) {
				if($pk->transactionType === InventoryTransactionPacket::TYPE_USE_ITEM_ON_ENTITY && $pk->trData->actionType === InventoryTransactionPacket::USE_ITEM_ON_ENTITY_ACTION_INTERACT) {
					$entity = $pk->trData;

					foreach($this->lobby->getServer()->getOnlinePlayers() as $onlinePlayer) {
						if($onlinePlayer instanceof LobbyPlayer) {
							if(!is_null($onlinePlayer->getMorph())) {
								if($onlinePlayer->getMorph()[2] === $entity->entityRuntimeId) {
									return;
								}
							}
						}
					}
				}
			}
		}
	}
}