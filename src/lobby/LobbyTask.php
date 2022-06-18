<?php

declare(strict_types = 1);

namespace lobby;

use pocketmine\scheduler\Task;

class LobbyTask extends Task {
    private $core;

    private $runs = 0;

    public function __construct(Lobby $core) {
        $this->core = $core;
    }

    public function onRun() : void {
        $this->runs++;

        if($this->runs % 1 === 0) {
            $this->core->getTrails()->tick();
        }
    }
}