<?php

declare(strict_types = 1);

namespace lobby\registry\hologram;

use lobby\Lobby;

use core\vote\{
	VoteManager,
	VoteData,
};

use core\essence\hologram\Hologram;

use core\player\CorePlayer;

use pocketmine\Server;

use pocketmine\world\Position;
use pocketmine\world\particle\FloatingTextParticle;

use pocketmine\utils\TextFormat;

class TopVoter extends Hologram {
	public function __construct() {
		parent::__construct("TopVoter");
	}

	public function getPosition() : Position {
		$level = Server::getInstance()->getLevelByName("Lobby");
		
		return new Position(126, 16, 104, $level);
	}

	public function getText() : string {
		if(!empty(VoteData::API_KEY)) {
			return "";
		}
		$voters = VoteManager::getInstance()->getTopVoters();
		$i = 1;

		$text = Lobby::PREFIX . "Top Voters this Month:";

		foreach($voters as $vote) {
			$text .= TextFormat::GRAY . "#" . $i . ". " . $vote["nickname"] . ": " . $vote["votes"];
			$i++;
		}
		return $text;
	}

	public function spawnTo(?CorePlayer $player = null) : void {
		$this->particle = new FloatingTextParticle("", $this->getText());

		$this->getPosition()->getWorld()->addParticle($this->getPosition()->add(0.5, 0, 0.5), $this->getParticle());
	}

	public function getUpdateTime() : ?int {
		return VoteData::VOTE_UPDATE;
	}
}