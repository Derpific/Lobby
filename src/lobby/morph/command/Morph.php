<?php

declare(strict_types = 1);

namespace lobby\morph\command;

use core\Core;

use lobby\Lobby;
use lobby\LobbyPlayer;

use core\utils\Entity;

use pocketmine\command\{
	PluginCommand,
	CommandSender
};

class Morph extends PluginCommand {
	private $lobby;

	public function __construct(Lobby $lobby) {
		parent::__construct("morph", $lobby);

		$this->lobby = $lobby;

		$this->setPermission("lobby.morph.command");
		$this->setUsage("<entity : off : list> [player]");
		$this->setDescription("Morph yourself or a Player into an Entity");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args) : bool {
		if(!$sender->hasPermission($this->getPermission())) {
			$sender->sendMessage(Core::getInstance()->getErrorPrefix() . "You do not have Permission to use this Command");
			return false;
		}
		if(count($args) < 1) {
			$sender->sendMessage(Core::getInstance()->getErrorPrefix() . "Usage: /morph " . $this->getUsage());
			return false;
		}
		if(is_null(Entity::nameToId($args[0])) && strtolower($args[0]) !== "off" && strtolower($args[0]) !== "list") {
			$sender->sendMessage(Core::getInstance()->getErrorPrefix() . $args[0] . " is not a valid Morph");
			return false;
		}
		if(strtolower($args[0]) === "list") {
			$types = [];

			foreach(Core::getInstance()->getMCPE()->getRegisteredEntities() as $entity) {
				$types[] = $entity->getNameTag();
			}
			$sender->sendMessage($this->lobby->getPrefix() . "Types of Morphs: " . implode(", ", $types));
		}
		if(isset($args[1])) {
			if(!$sender->hasPermission($this->getPermission() . ".other")) {
				$sender->sendMessage(Core::getInstance()->getErrorPrefix() . "You do not have Permission to use this Command");
				return false;
			}
			$player = $this->lobby->getServer()->getPlayer($args[1]);

			if(!$player instanceof LobbyPlayer) {
				$sender->sendMessage(Core::getInstance()->getErrorPrefix() . $args[1] . " is not Online");
				return false;
			} else {
				if(strtolower($args[0]) === "off") {
					if(is_null($player->getMorph())) {
						$sender->sendMessage(Core::getInstance()->getErrorPrefix() . $player->getName() . " is not Morphed");
						return false;
					} else {
						$player->removeMorph();
						$sender->sendMessage($this->lobby->getPrefix() . "Removed " . $player->getName() . "'s Morph");
						$player->sendMessage($this->lobby->getPrefix() . $sender->getName() . " Removed your Morph");
						return true;
					}
				}
				$player->morph(Entity::nameToId($args[0]));
				$sender->sendMessage($this->lobby->getPrefix() . "Morphed " . $player->getName() . " to " . strtoupper($args[1]));
				$player->sendMessage($this->lobby->getPrefix() . $sender->getName() . " Morphed you to " . strtoupper($args[1]));
				return true;
			}
		}
		if(!$sender instanceof LobbyPlayer && strtolower($args[0]) !== "list") {
			$sender->sendMessage(Core::getInstance()->getErrorPrefix() . "You must be a Player to use this Command");
			return false;
		} else if($sender instanceof LobbyPlayer && strtolower($args[0]) !== "list") {
			if(strtolower($args[0]) === "off") {
				if(is_null($sender->getMorph())) {
					$sender->sendMessage(Core::getInstance()->getErrorPrefix() . "You are not Morphed");
					return false;
				} else {
					$sender->removeMorph();
					$sender->sendMessage($this->lobby->getPrefix() . "Removed your Morph");
					return true;
				}
			}
			$sender->morph(Entity::nameToId($args[0]));
			$sender->sendMessage($this->lobby->getPrefix() . "Morphed to " . strtoupper($args[1]));
			return true;
		}
	}
}