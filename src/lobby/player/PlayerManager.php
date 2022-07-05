<?php

declare(strict_types = 1);

namespace lobby\player;

use core\utils\Manager;

use lobby\Lobby;

use pocketmine\Server;

class PlayerManager extends Manager {
	public static $instance = null;

    public function init() {
		self::$instance = $this;

		$this->registerListener(new PlayerListener($this), Lobby::getInstance());
	}

	public static function getInstance() : self {
    	return self::$instance;
	}
}