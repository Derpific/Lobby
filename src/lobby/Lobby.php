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
use lobby\registry\RegistryManager;
use lobby\stacker\StackerManager;
use lobby\trails\TrailsManager;
use pocketmine\inventory\CreativeInventory;
use Webmozart\PathUtil\Path;

use pocketmine\plugin\PluginBase;

use pocketmine\utils\TextFormat;

class Lobby extends PluginBase {
    public static $instance = null;

    private static $resourceFolder;

    const PREFIX = TextFormat::GREEN . "Lobby> " . TextFormat::GRAY;

    public function onLoad() : void {
        self::$instance = $this;

		$this->getServer()->getNetwork()->setName(TextFormat::BOLD . Core::PREFIX . TextFormat::GREEN . "Lobby");
    }

    public function onEnable() : void {
		self::$resourceFolder = str_replace("\\", DIRECTORY_SEPARATOR, str_replace("/", DIRECTORY_SEPARATOR, Path::join($this->getFile(), "resources")));

    	if(!$this->getServer()->getPluginManager()->isPluginEnabled(Core::getInstance())) {
			$this->getServer()->getLogger()->error(Core::ERROR_PREFIX . "Core was not Enabled.");
			$this->getServer()->shutdown();
		}
    	new MorphManager();
		new ParkourManager();
		new StackerManager();
		new RegistryManager();
		//$this->trails = new TrailsManager();

		CreativeInventory::getInstance()->add(new Cosmetics());
		CreativeInventory::getInstance()->add(new Gadgets());
		CreativeInventory::getInstance()->add(new Profile());
		CreativeInventory::getInstance()->add(new ServerSelector());
		$this->getServer()->getLogger()->notice(self::PREFIX . "Lobby Enabled");
		$this->getServer()->getPluginManager()->registerEvents(new PlayerListener($this), $this);
    }

    public static function getInstance() : Lobby {
        return self::$instance;
    }

    public static function getResourcesFolder() {
    	return self::$resourceFolder;
	}

    public function onDisable() : void {
        $this->getServer()->getLogger()->notice(self::PREFIX . "Lobby Disabled");
    }
}