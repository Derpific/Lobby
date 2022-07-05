<?php

declare(strict_types=1);

namespace lobby\item\form;

use lobby\Lobby;

use pocketmine\player\Player;

use dktapps\pmforms\CustomForm;
use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Label;

use pocketmine\utils\TextFormat;

class GadgetsForm extends CustomForm {
	public function getTitle() : string {
		return TextFormat::GOLD . "Gadgets";
	}

	public function getText() {
		return TextFormat::LIGHT_PURPLE . "Select a Gadget!";
	}

	public function getOptions() : array {
		$b1 = new Label("Default", TextFormat::GRAY . "Coming Soon..");
		$options = [$b1];
		return $options;
	}

	public function onSubmit() {
		return function(Player $submitter, CustomFormResponse $response) : void {
		};
	}

	public function onClose() {
		return function(Player $submitter) : void {
			$submitter->sendMessage(Lobby::PREFIX . "Closed Gadgets menu");
		};
	}

	public function __construct(LobbyPlayer $player) {
		parent::__construct($this->getTitle(), $this->getOptions(), $this->onSubmit(), $this->onClose());

		$player->sendForm($this);
	}
}