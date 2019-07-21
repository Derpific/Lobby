<?php

declare(strict_types = 1);

namespace lobby\morph\command;

use core\Core;

use lobby\Lobby;

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
		$this->setUsage("<entity> [value] [player]");
		$this->setDescription("Set a Hud Type on or Off");
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
		return true;
	}
}