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
use lobby\morph\MorphManager;
use lobby\parkour\ParkourManager;
use lobby\stacker\StackerManager;
use lobby\trails\TrailsManager;

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
    	$this->morph = new MorphManager();
		$this->parkour = new ParkourManager();
		$this->stacker = new StackerManager();
		$this->trails = new TrailsManager();

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

    public function getMorph() : MorphManager {
    	return $this->morph;
	}

	public function getParkour() : ParkourManager {
		return $this->parkour;
	}

	public function getStacker() : StackerManager {
		return $this->stacker;
	}

    public function getTrails() : TrailsManager {
        return $this->trails;
    }

    public function onDisable() {
        $this->getServer()->getLogger()->notice(self::PREFIX . "Lobby Disabled");
    }
}