<?php

declare(strict_types = 1);

namespace lobby;

use lobby\item\{
    Cosmetics,
    Gadgets,
    Profile,
    ServerSelector
};

use lobby\parkour\Parkour;

use lobby\trail\Trails;

use pocketmine\plugin\PluginBase;

use pocketmine\utils\TextFormat;

use pocketmine\item\ItemBlock;

class Lobby extends PluginBase {
    public static $instance = null;

    private $trails;
    private $parkour;

    const PREFIX = TextFormat::GREEN . "Lobby> " . TextFormat::GRAY;

    public function onLoad() {
        self::$instance = $this;
    }

    public function onEnable() {
		$this->parkour = new Parkour($this);
        $this->trails = new Trails($this);

        ItemBlock::addCreativeItem(new Cosmetics());
        ItemBlock::addCreativeItem(new Gadgets());
        ItemBlock::addCreativeItem(new Profile());
        ItemBlock::addCreativeItem(new ServerSelector());
		$this->getServer()->getLogger()->notice($this->getPrefix() . "Lobby Enabled");
		$this->getServer()->getPluginManager()->registerEvents(new LobbyListener($this), $this);
    }

    public static function getInstance() : Lobby {
        return self::$instance;
    }

	public function getParkour() : Parkour {
		return $this->parkour;
	}

    public function getTrails() : Trails {
        return $this->trails;
    }

    public function getPrefix() : string {
        return self::PREFIX;
    }

    public function onDisable() {
        $this->getServer()->getLogger()->notice($this->getPrefix() . "Lobby Disabled");
    }
}