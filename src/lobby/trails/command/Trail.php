<?php

declare(strict_types = 1);

namespace lobby\trails\command;

use core\Core;

use lobby\Lobby;
use lobby\LobbyPlayer;

use lobby\trails\Trails;

use pocketmine\command\{
	PluginCommand,
	CommandSender
};
use pocketmine\Server;

class Trail extends PluginCommand {
	private $manager;

	public function __construct(Trails $manager) {
		parent::__construct("trail", Lobby::getInstance());

		$this->manager = $manager;

		$this->setPermission("lobby.trail.command");
		$this->setUsage("<trail : off : list> [player]");
		$this->setDescription("Apply a Trail to yourself or a Player");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args) : bool {
		if(!$sender->hasPermission($this->getPermission())) {
			$sender->sendMessage(Core::ERROR_PREFIX . "You do not have Permission to use this Command");
			return false;
		}
		if(count($args) < 1) {
			$sender->sendMessage(Core::ERROR_PREFIX . "Usage: /trail " . $this->getUsage());
			return false;
		}
		if((!$this->manager->get($args[0]) instanceof \lobby\trails\Trail) && strtolower($args[0]) !== "off" && strtolower($args[0]) !== "list") {
			$sender->sendMessage(Core::ERROR_PREFIX . $args[0] . " is not a valid Trail");
			return false;
		}
		if(strtolower($args[0]) === "list") {
			$types = [];

			foreach($this->manager->getAll() as $trail) {
				if($trail instanceof \lobby\trails\Trail) {
					$types[] = $trail->getName();
				}
			}
			$sender->sendMessage(Lobby::PREFIX . "Types of Trails: " . implode(", ", $types));
		}
		if(isset($args[1])) {
			if(!$sender->hasPermission($this->getPermission() . ".other")) {
				$sender->sendMessage(Core::ERROR_PREFIX . "You do not have Permission to use this Command");
				return false;
			}
			$player = Server::getInstance()->getPlayer($args[1]);

			if(!$player instanceof LobbyPlayer) {
				$sender->sendMessage(Core::ERROR_PREFIX . $args[1] . " is not Online");
				return false;
			} else {
				if(strtolower($args[0]) === "off") {
					if(!$player->getTrail() instanceof \lobby\trails\Trail) {
						$sender->sendMessage(Core::ERROR_PREFIX . $player->getName() . " does not have a Trail Applied");
						return false;
					} else {
						$player->removeTrail();
						$sender->sendMessage(Lobby::PREFIX . "Removed " . $player->getName() . "'s Trail");
						$player->sendMessage(Lobby::PREFIX . $sender->getName() . " Removed your Trail");
						return true;
					}
				}
				if(!$player->getTrail() instanceof Trail) {
					$player->removeTrail();
					$sender->sendMessage(Lobby::PREFIX . "Removed " . $player->getName() . "'s old Trail");
					$player->sendMessage(Lobby::PREFIX . $sender->getName() . " Removed your old Trail");
				}
				$player->spawnTrail($trail);
				$player->updateTrail();
				$sender->sendMessage(Lobby::PREFIX . "Applied the Trail " . $trail->getName() . " to " . $player->getName());
				$player->sendMessage(Lobby::PREFIX . $sender->getName() . " Applied the Trail " . $trail->getName() . " to you");
				return true;
			}
		}
		if(!$sender instanceof LobbyPlayer && strtolower($args[0]) !== "list") {
			$sender->sendMessage(Core::ERROR_PREFIX . "You must be a Player to use this Command");
			return false;
		} else {
			if(strtolower($args[0]) === "off") {
				if(!$sender->getTrail() instanceof \lobby\trails\Trail) {
					$sender->sendMessage(Core::ERROR_PREFIX . "You do not have a Trail Applied");
					return false;
				} else {
					$sender->removeTrail();
					$sender->sendMessage(Lobby::PREFIX . "Removed your Trail");
					return true;
				}
			}
			if(!$sender->getTrail() instanceof Trail) {
				$sender->removeTrail();
				$sender->sendMessage(Lobby::PREFIX . "Removed your Old Trail");
			}
			$sender->spawnTrail($trail);
			$sender->updateTrail();
			$sender->sendMessage(Lobby::PREFIX . "Applied the Trail " . $trail->getName() . " to you");
			return true;
		}
	}
}