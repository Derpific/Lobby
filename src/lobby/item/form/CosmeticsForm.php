<?php

declare(strict_types=1);

namespace core\player\form;

use core\Core;

use core\network\NetworkManager;

use core\player\CorePlayer;
use core\player\form\subForm\GlobalProfileForm;
use dktapps\pmforms\{
	CustomForm,
	MenuForm,
	MenuOption,
	FormIcon};

use pocketmine\utils\TextFormat;

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
		return function(CorePlayer $submitter, int $selected) : void {
			if($submitter instanceof CorePlayer) {
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
		return function(CorePlayer $submitter, int $selected) : void {
			$submitter->sendMessage(Core::PREFIX . "Closed Cosmetics menu");
		};
	}

	public function __construct(CorePlayer $player) {
		parent::__construct($this->getTitle(), $this->getText(), $this->getOptions(), $this->onSubmit(), $this->onClose());

		$player->sendForm($this);
		$player->sendMessage($this->core::PREFIX . "Opened Cosmetics menu");
	}
}