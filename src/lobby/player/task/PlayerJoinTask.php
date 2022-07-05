<?php

declare(strict_types = 1);

namespace lobby\player\task;

use core\Core;

use core\player\CorePlayer;

use lobby\Lobby;

use pocketmine\scheduler\Task;

use pocketmine\utils\TextFormat;

use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\network\mcpe\protocol\types\LevelEvent;

class PlayerJoinTask extends Task {
    private $player;
    
    public function __construct(CorePlayer $player) {
        $this->player = $player;
    }
    
    public function onRun() : void {
		if(!$this->player->isOnline()) {
			return;
		}
		$this->player->getWorld()->broadcastPacketToViewers($this->player->getPosition(), LevelEventPacket::create(LevelEvent::GUARDIAN_CURSE, 0, $this->player->getPosition()));
		$this->player->sendTitle(Lobby::PREFIX, TextFormat::GRAY . "Lobby");
		$this->player->sendMessage(Lobby::PREFIX . "Welcome to the Athena Lobby!");
    }
}