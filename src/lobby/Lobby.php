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
use lobby\player\PlayerManager;
use lobby\stacker\StackerManager;
use lobby\trails\TrailsManager;
use pocketmine\inventory\CreativeInventory;
use Webmozart\PathUtil\Path;

use pocketmine\plugin\PluginBase;

use pocketmine\utils\TextFormat;

use pocketmine\item\ItemFactory;
use pocketmine\item\Item;

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
		new PlayerManager();
		//$this->trails = new TrailsManager();
		
		ItemFactory::getInstance()->register(new Cosmetics(), true);
		ItemFactory::getInstance()->register(new Gadgets(), true);
		ItemFactory::getInstance()->register(new Profile(), true);
		ItemFactory::getInstance()->register(new ServerSelector(), true);
		CreativeInventory::getInstance()->add(new Cosmetics());
		CreativeInventory::getInstance()->add(new Gadgets());
		CreativeInventory::getInstance()->add(new Profile());
		CreativeInventory::getInstance()->add(new ServerSelector());
		$this->getServer()->getLogger()->notice(self::PREFIX . "Lobby Enabled");
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