<?php

declare(strict_types = 1);

namespace lobby\item;

use core\utils\CustomItem;

use pocketmine\utils\TextFormat;

class Cosmetics extends CustomItem {
    public function __construct() {
        parent::__construct(self::ENDER_CHEST, TextFormat::AQUA . "Cosmetics", [TextFormat::GOLD . "Tap somewhere to open the Cosmetics menu"], 1);
    }
}