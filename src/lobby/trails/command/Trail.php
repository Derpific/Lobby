<?php

declare(strict_types = 1);

namespace lobby\trails\command;

use core\Core;
use lobby\Lobby;

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
		$this->setDescription("Set a Hud Type on or Off");
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
		return true;
	}
}