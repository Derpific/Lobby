<?php

declare(strict_types = 1);

namespace lobby\trail;

use lobby\Lobby;
use lobby\LobbyPlayer;

use pocketmine\level\Position;

class Trail {
    private $name;

    private $icon;

    public $spawnedTo = [];

    public function __construct(string $name, string $icon) {
        $this->name = $name;
        $this->icon = $icon;
    }

    public function getName() : string {
        return $this->name;
    }

    public function getIcon() : string {
        return $this->icon;
    }

    public function isRandom() : bool {
        return $this->getName() === "Random";
    }

    public function spawnRandom(LobbyPlayer $player) {
        $random = round(rand(0, 114));
        $int = rand(1, 4);
        $trail = null;

        switch($int) {
            case 1:
                $trail = "item_" . $random;
            break;
            case 2:
                $trail = "block_" . $random;
            break;
            case 3:
                $trail = "destroyblock_" . $random;
            break;
            case 4:
                $trail = $random;
            break;
        }
        $this->spawnedTo[$player->getName()] = true;
        $y = $player->y;
        $y2 = $y + 0.5;
        $y3 = $y2 + 1.4;

        $player->getLevel()->addParticle(Lobby::getInstance()->getTrails()->convertTrail($trail, new Position($player->x, mt_rand($y, rand($y2, $y3)), $player->z)));
    }

    public function isSpawnedTo(LobbyPlayer $player) : bool {
        return isset($this->spawnedTo[$player->getName()]);
    }

    public function spawnTo(LobbyPlayer $player) {
        $this->spawnedTo[$player->getName()] = true;
        $y = $player->y;
        $y2 = $y + 0.5;
        $y3 = $y2 + 1.4;

        $player->getLevel()->addParticle(Lobby::getInstance()->getTrails()->convertTrail($this->name, new Position($player->x, mt_rand($y, rand($y2, $y3)), $player->z)));
    }

    public function despawnFrom(LobbyPlayer $player) {
        unset($this->spawnedTo[$player->getName()]);
    }
}