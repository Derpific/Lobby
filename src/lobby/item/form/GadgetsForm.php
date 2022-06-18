<?php

declare(strict_types=1);

namespace core\player\form;

use core\player\CorePlayer;

use dktapps\pmforms\CustomForm;
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
		return function(CorePlayer $submitter, int $selected) : void {
		};
	}

	public function onClose() {
		return function(CorePlayer $submitter, int $selected) : void {
			$submitter->sendMessage(Core::PREFIX . "Closed Gadgets menu");
		};
	}

	public function __construct(CorePlayer $player) {
		parent::__construct($this->getTitle(), $this->getOptions(), $this->onSubmit(), $this->onClose());

		$player->sendForm($this);
	}
}