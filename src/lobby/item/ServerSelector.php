<?php

declare(strict_types = 1);

namespace lobby\item;

use core\utils\CustomItem;

use pocketmine\utils\TextFormat;

class ServerSelector extends CustomItem {
    public function __construct() {
        parent::__construct(self::END_PORTAL_FRAME, TextFormat::AQUA . "Server Selector", [TextFormat::GOLD . "Tap somewhere to open the Server Selection menu"], 1);
    }
}