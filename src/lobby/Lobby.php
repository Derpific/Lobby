<?php

declare(strict_types = 1);

namespace lobby;

use core\Core;

use lobby\item\{
    Cosmetics,
    Gadgets,
    Profile,
    ServerSelector
};
use lobby\morph\Morph;
use lobby\parkour\Parkour;
use lobby\stacker\Stacker;
use lobby\trails\Trails;

use pocketmine\plugin\PluginBase;

use pocketmine\utils\TextFormat;

use pocketmine\item\ItemBlock;

class Lobby extends PluginBase {
    public static $instance = null;

    private $morph;
    private $trails;
    private $parkour;
    private $stacker;

    const PREFIX = TextFormat::GREEN . "Lobby> " . TextFormat::GRAY;

    public function onLoad() {
        self::$instance = $this;
		
		$this->getServer()->getNetwork()->setName(TextFormat::BOLD . Core::PREFIX . TextFormat::GREEN . "Lobby");
    }

    public function onEnable() {
    	if(!$this->getServer()->getPluginManager()->isPluginEnabled(Core::getInstance())) {
			$this->getServer()->getLogger()->error(Core::ERROR_PREFIX . "Core was not Enabled.");
			$this->getServer()->shutdown();
		}
    	$this->morph = new Morph();
		$this->parkour = new Parkour();
		$this->stacker = new Stacker();
		$this->trails = new Trails();

		ItemBlock::addCreativeItem(new Cosmetics());
		ItemBlock::addCreativeItem(new Gadgets());
		ItemBlock::addCreativeItem(new Profile());
		ItemBlock::addCreativeItem(new ServerSelector());
		$this->getServer()->getLogger()->notice(self::PREFIX . "Lobby Enabled");
		$this->getServer()->getPluginManager()->registerEvents(new LobbyListener($this), $this);
    }

    public static function getInstance() : Lobby {
        return self::$instance;
    }

    public function getMorph() : Morph {
    	return $this->morph;
	}

	public function getParkour() : Parkour {
		return $this->parkour;
	}

	public function getStacker() : Stacker {
		return $this->stacker;
	}

    public function getTrails() : Trails {
        return $this->trails;
    }

    public function onDisable() {
        $this->getServer()->getLogger()->notice(self::PREFIX . "Lobby Disabled");
    }
}