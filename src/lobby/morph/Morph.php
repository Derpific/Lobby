<?php

declare(strict_types = 1);

namespace lobby\morph;

use lobby\Lobby;

class Morph {
	private $lobby;

	public $morphs = [];

	public function __construct(Lobby $lobby) {
		$this->lobby = $lobby;

		$lobby->getServer()->getCommandMap()->register(\lobby\morph\command\Morph::class, new \lobby\morph\command\Morph($this->lobby));
		$lobby->getServer()->getPluginManager()->registerEvents(new MorphListener($lobby), $lobby);
	}

	public function nameToId(string $name) {
		//TODO
	}
}