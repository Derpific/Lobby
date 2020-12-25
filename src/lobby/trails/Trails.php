<?php

declare(strict_types = 1);

namespace lobby\trails;

use lobby\LobbyPlayer;

use core\utils\Manager;

use pocketmine\Server;

class Trails extends Manager implements Data {
	public static $instance = null;

	public $trails = [];

    public function init() {
    	self::$instance = $this;

        foreach(self::TRAILS as $trail) {
            $this->trails[$trail] = new Trail($trail, self::ICONS[$trail]);
        }
        $this->registerCommand(\lobby\trails\command\Trail::class, new \lobby\trails\command\Trail($this));
    }

    public static function getInstance() : self {
		return self::$instance;
	}

	public function tick() {
        foreach(Server::getInstance()->getOnlinePlayers() as $onlinePlayer) {
            if($onlinePlayer instanceof LobbyPlayer) {
            	if(!is_null($onlinePlayer->getTrail())) {
            		$onlinePlayer->updateTrail();
				}
            }
        }
    }

	public function getAll() : array {
        return $this->trails;
    }
	
	public function get(string $trail) : ?Trail {
        $lowerKeys = array_change_key_case($this->trails, CASE_LOWER);

        if(isset($lowerKeys[strtolower($trail)])) {
            return $lowerKeys[strtolower($trail)];
        }
        return null;
    }
}