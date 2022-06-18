<?php

declare(strict_types=1);

namespace core\player\form\subForm;

use core\player\CorePlayer;

use dktapps\pmforms\CustomForm;
use dktapps\pmforms\element\Label;

use pocketmine\utils\TextFormat;

class MorphsForm extends CustomForm {
	/**
	$b = new Button(TextFormat::RED . "Remove MorphManager");

	$b->setId("off");

	$options[] = $b;

	foreach(Core::getInstance()->getMCPE()->getRegisteredEntities() as $entity) {
	$b2 = new Button(TextFormat::GRAY . $entity->getName(), new Image("", Image::TYPE_URL));

	$b2->setId($entity->getName());

	$options[] = $b2;
	}
	$this->sendForm(new MenuForm(TextFormat::GOLD . "Morphs", TextFormat::LIGHT_PURPLE . "Select a MorphManager!", $options,
	function(Player $player, Button $button) : void {
	if($player instanceof LobbyPlayer) {
	$morph = $button->getId();

	if(!$player->hasPermission("lobby.morphs." . $morph)) {
	$player->sendMessage(Core::ERROR_PREFIX . "You do not have Permission to use this Trail");
	}
	if(!is_null($player->getMorph()) && $player->getMorph() === $morph) {
	$player->sendMessage(Core::ERROR_PREFIX . "You are already Morphed into a " . $morph);
	} else {
	if($button->getId() === "off") {
	if(is_null($player->getMorph())) {
	$player->sendMessage(Core::ERROR_PREFIX . "You do not have a MorphManager applied");
	}
	$player->removeMorph();
	$player->sendMessage(Lobby::PREFIX . "Removed your MorphManager");
	}
	if(!is_null($player->getMorph())) {
	$player->removeMorph();
	$player->sendMessage(Lobby::PREFIX . "Removed your Old MorphManager");
	}
	$player->morph(Entity::nameToId($morph));
	$player->sendMessage(Lobby::PREFIX . "Morphed into a(n) " . $morph->getName());
	}
	}
	},
	function(Player $player) : void {
	$player->sendMessage(Lobby::PREFIX . "Closed MorphManager menu");
	}
	));*/
	public function getTitle() : string {
		return TextFormat::GOLD . "Morphs";
	}

	public function getText() {
		return TextFormat::LIGHT_PURPLE . "Select a Morph!";
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
			new CosmeticsForm($submitter); //idk.. back button is better
		};
	}

	public function __construct(CorePlayer $player) {
		parent::__construct($this->getTitle(), $this->getOptions(), $this->onSubmit(), $this->onClose());

		$player->sendForm($this);
	}
}