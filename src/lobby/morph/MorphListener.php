<?php

declare(strict_types = 1);

namespace lobby\morph;

use lobby\Lobby;
use lobby\LobbyPlayer;

use pocketmine\event\Listener;

use pocketmine\event\player\PlayerMoveEvent;

use pocketmine\event\server\DataPacketReceiveEvent;

use core\mcpe\network\InventoryTransactionPacket;

class MorphListener implements Listener {
	private $lobby;

	public function __construct(Lobby $lobby) {
		$this->lobby = $lobby;
	}

	public function onPlayerMoveEvents(PlayerMoveEvent $event) {
		$player = $event->getPlayer();

		if($player instanceof LobbyPlayer) {
			if(!is_null($player->getMorph())) {
				$player->moveMorph();
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