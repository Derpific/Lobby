<?php

declare(strict_types = 1);

namespace lobby\item;

use core\utils\CustomItem;

use pocketmine\utils\TextFormat;

class Profile extends CustomItem {
    public function __construct() {
        parent::__construct(self::MOB_HEAD, TextFormat::AQUA . "Profile", [TextFormat::GOLD . "Tap somewhere to open your Profile menu"], 1);
    }
}