<?php

declare(strict_types = 1);

namespace lobby\morph;

use lobby\LobbyPlayer;

use pocketmine\event\Listener;

use pocketmine\event\player\PlayerMoveEvent;

use pocketmine\event\server\DataPacketReceiveEvent;

use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;

use pocketmine\Server;

class MorphListener implements Listener {
	private $manager;

	public function __construct(Morph $manager) {
		$this->manager = $manager;
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

					foreach(Server::getInstance()->getOnlinePlayers() as $onlinePlayer) {
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