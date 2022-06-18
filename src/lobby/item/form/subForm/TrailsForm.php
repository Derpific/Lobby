<?php

declare(strict_types=1);

namespace core\player\form\subForm;

use core\Core;

use core\network\NetworkManager;

use core\player\CorePlayer;
use core\player\form\subForm\GlobalProfileForm;
use dktapps\pmforms\{
	CustomForm,
	element\Label,
	MenuForm,
	MenuOption,
	FormIcon};

use pocketmine\utils\TextFormat;

class TrailsForm extends CustomForm {
	/**
	 * @return string$b = new Button(TextFormat::RED . "Remove Trail");

	$b->setId("off");

	$options[] = $b;

	foreach($this->lobby->getTrails()->getAll() as $trail) {
	if($trail instanceof Trail) {
	$img = new Image($trail->getIcon());

	if(!empty($img)) {
	$b2 = new Button(TextFormat::GRAY . $trail->getName(), new Image($trail->getIcon(), Image::TYPE_URL));

	$b2->setId($trail->getName());
	} else {
	$b2 = new Button(TextFormat::GRAY . $trail->getName());

	$b2->setId($trail->getName());
	}
	$options[] = $b2;
	}
	}
	$this->sendForm(new MenuForm(TextFormat::GOLD . "TrailsManager", TextFormat::LIGHT_PURPLE . "Select a Trail!", $options,
	function(Player $player, Button $button) : void {
	if($player instanceof LobbyPlayer) {
	$trail = Lobby::getInstance()->getTrails()->get($button->getId());

	if($trail instanceof Trail) {
	if(!$player->hasPermission("lobby.trails." . $trail->getName())) {
	$player->sendMessage(Core::ERROR_PREFIX . "You do not have Permission to use this Trail");
	}
	if(!is_null($player->getTrail()) && $player->getTrail()->getName() === $trail->getName()) {
	$player->sendMessage(Core::ERROR_PREFIX . "You already have the Trail " . $trail->getName() . " Applied");
	} else {
	if($button->getId() === "off") {
	if(!$player->getTrail() instanceof Trail) {
	$player->sendMessage(Core::ERROR_PREFIX . "You do not have a Trail applied");
	}
	$player->removeTrail();
	$player->sendMessage(Lobby::PREFIX . "Removed your Trail");
	}
	if(!$player->getTrail() instanceof Trail) {
	$player->removeTrail();
	$player->sendMessage(Lobby::PREFIX . "Removed your Old Trail");
	}
	$player->spawnTrail($trail);
	$player->updateTrail();
	$player->sendMessage(Lobby::PREFIX . "Applied the Trail: " . $trail->getName());
	}
	}
	}
	},
	function(Player $player) : void {
	$player->sendMessage(Lobby::PREFIX . "Closed TrailsManager menu");
	}
	));*/
	
	public function getTitle() : string {
		return TextFormat::GOLD . "Trails";
	}

	public function getText() {
		return TextFormat::LIGHT_PURPLE . "Select a Trail!";
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