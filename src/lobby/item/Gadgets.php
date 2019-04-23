<?php

declare(strict_types = 1);

namespace lobby\item;

use core\utils\CustomItem;

use pocketmine\utils\TextFormat;

class Gadgets extends CustomItem {
    public function __construct() {
        parent::__construct(self::REDSTONE_TORCH, TextFormat::AQUA . "Gadgets", [TextFormat::GOLD . "Tap somewhere to open the Gadgets menu"], 1);
    }
}