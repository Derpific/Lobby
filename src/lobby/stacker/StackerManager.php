<?php

declare(strict_types = 1);

namespace lobby\stacker;
//TODO
use core\utils\Manager;

class StackerManager extends Manager {
	public static $instance = null;

	public function init() {
		self::$instance = $this;
	}

	public static function getInstance() : self {
		return self::$instance;
	}
}