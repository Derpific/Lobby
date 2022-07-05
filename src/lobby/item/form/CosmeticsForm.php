<?php

declare(strict_types=1);

namespace lobby\item\form;

use lobby\Lobby;

use lobby\player\LobbyPlayer;

use dktapps\pmforms\{
	CustomForm,
	MenuOption
};

use pocketmine\utils\TextFormat;

use pocketmine\player\Player;

//TODO: Armor, Emotes, ETC
class CosmeticsForm extends CustomForm {
	public function getTitle() : string {
		return TextFormat::GOLD . "Cosmetics";
	}

	public function getText() {
		return TextFormat::LIGHT_PURPLE . "Select a Cosmetic!";
	}

	public function getOptions() : array {
		$b1 = new MenuOption(TextFormat::GRAY . "Trails");
		$b2 = new MenuOption(TextFormat::GRAY . "Morphs");
		$options = [$b1];
		return $options;
	}

	public function onSubmit() {
		return function(Player $submitter, int $selected) : void {
			if($submitter instanceof LobbyPlayer) {
				switch($selected) {
					case 0:
						if($submitter->hasPermission("trails.use")) {
							new TrailsForm($submitter);
						}
					break;
					case 1:
						if($submitter->hasPermission("morphs.use")) {
							new MorphsForm($submitter);
						}
					break;
				}
			}
		};
	}

	public function onClose() {
		return function(Player $submitter) : void {
			$submitter->sendMessage(Lobby::PREFIX . "Closed Cosmetics menu");
		};
	}

	public function __construct(LobbyPlayer $player) {
		parent::__construct($this->getTitle(), $this->getText(), $this->getOptions(), $this->onSubmit(), $this->onClose());

		$player->sendForm($this);
		$player->sendMessage(Lobby::PREFIX . "Opened Cosmetics menu");
	}
}