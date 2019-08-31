<?php

declare(strict_types = 1);

namespace lobby\trails\command;

use core\Core;

use lobby\Lobby;
use lobby\LobbyPlayer;

use pocketmine\command\{
	PluginCommand,
	CommandSender
};

class Trail extends PluginCommand {
	private $lobby;

	public function __construct(Lobby $lobby) {
		parent::__construct("trail", $lobby);

		$this->lobby = $lobby;

		$this->setPermission("lobby.trail.command");
		$this->setUsage("<trail : off : list> [player]");
		$this->setDescription("Apply a Trail to yourself or a Player");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args) : bool {
		if(!$sender->hasPermission($this->getPermission())) {
			$sender->sendMessage(Core::getInstance()->getErrorPrefix() . "You do not have Permission to use this Command");
			return false;
		}
		if(count($args) < 1) {
			$sender->sendMessage(Core::getInstance()->getErrorPrefix() . "Usage: /trail " . $this->getUsage());
			return false;
		}
		if((!$this->lobby->getTrails()->get($args[0]) instanceof \lobby\trails\Trail) && strtolower($args[0]) !== "off" && strtolower($args[0]) !== "list") {
			$sender->sendMessage(Core::getInstance()->getErrorPrefix() . $args[0] . " is not a valid Trail");
			return false;
		}
		if(strtolower($args[0]) === "list") {
			$types = [];

			foreach($this->lobby->getTrails()->getAll() as $trail) {
				if($trail instanceof \lobby\trails\Trail) {
					$types[] = $trail->getName();
				}
			}
			$sender->sendMessage($this->lobby->getPrefix() . "Types of Trails: " . implode(", ", $types));
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
					if(!$player->getTrail() instanceof \lobby\trails\Trail) {
						$sender->sendMessage(Core::getInstance()->getErrorPrefix() . $player->getName() . " does not have a Trail Applied");
						return false;
					} else {
						$player->removeTrail();
						$sender->sendMessage($this->lobby->getPrefix() . "Removed " . $player->getName() . "'s Trail");
						$player->sendMessage($this->lobby->getPrefix() . $sender->getName() . " Removed your Trail");
						return true;
					}
				}
				if(!$player->getTrail() instanceof Trail) {
					$player->removeTrail();
					$sender->sendMessage($this->lobby->getPrefix() . "Removed " . $player->getName() . "'s old Trail");
					$player->sendMessage(Lobby::getInstance()->getPrefix() . $sender->getName() . " Removed your old Trail");
				}
				$player->spawnTrail($trail);
				$player->updateTrail();
				$sender->sendMessage($this->lobby->getPrefix() . "Applied the Trail " . $trail->getName() . " to " . $player->getName());
				$player->sendMessage($this->lobby->getPrefix() . $sender->getName() . " Applied the Trail " . $trail->getName() . " to you");
				return true;
			}
		}
		if(!$sender instanceof LobbyPlayer && strtolower($args[0]) !== "list") {
			$sender->sendMessage(Core::getInstance()->getErrorPrefix() . "You must be a Player to use this Command");
			return false;
		} else {
			if(strtolower($args[0]) === "off") {
				if(!$sender->getTrail() instanceof \lobby\trails\Trail) {
					$sender->sendMessage(Core::getInstance()->getErrorPrefix() . "You do not have a Trail Applied");
					return false;
				} else {
					$sender->removeTrail();
					$sender->sendMessage($this->lobby->getPrefix() . "Removed your Trail");
					return true;
				}
			}
			if(!$sender->getTrail() instanceof Trail) {
				$sender->removeTrail();
				$sender->sendMessage(Lobby::getInstance()->getPrefix() . "Removed your Old Trail");
			}
			$sender->spawnTrail($trail);
			$sender->updateTrail();
			$sender->sendMessage($this->lobby->getPrefix() . "Applied the Trail " . $trail->getName() . " to you");
			return true;
		}
	}
}