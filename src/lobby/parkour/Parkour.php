<?php

declare(strict_types = 1);

namespace lobby\parkour;
//TODO
use core\utils\Manager;

class Parkour extends Manager {
	public static $instance = null;

    public function init() {
		self::$instance = $this;
	}

	public static function getInstance() : self {
    	return self::$instance;
	}
}