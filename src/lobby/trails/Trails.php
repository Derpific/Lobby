<?php

declare(strict_types = 1);

namespace lobby\trails;

use lobby\Lobby;
use lobby\LobbyPlayer;

use pocketmine\item\Item;

use pocketmine\block\Block;

use pocketmine\level\Position;
use pocketmine\level\particle\{
    Particle,
    ExplodeParticle,
    HugeExplodeParticle,
    BubbleParticle,
    SplashParticle,
    WaterParticle,
    CriticalParticle,
    EnchantParticle,
    InstantEnchantParticle,
    SmokeParticle,
    WaterDripParticle,
    LavaDripParticle,
    SporeParticle,
    PortalParticle,
    EntityFlameParticle,
    FlameParticle,
    LavaParticle,
    RedstoneParticle,
    ItemBreakParticle,
    HeartParticle,
    InkParticle,
    EnchantmentTableParticle,
    HappyVillagerParticle,
    AngryVillagerParticle,
    RainSplashParticle,
    TerrainParticle,
    DestroyBlockParticle,
	DustParticle,
};

class Trails implements Data {
    private $lobby;
	
	public $trails = [];

    public function __construct(Lobby $lobby) {
        $this->lobby = $lobby;

        foreach(self::TRAILS as $trail) {
            $this->trails[$trail] = new Trail($trail, self::ICONS[$trail]);
        }
    }

    public function tick() {
        foreach($this->lobby->getServer()->getOnlinePlayers() as $onlinePlayer) {
            if($onlinePlayer instanceof LobbyPlayer) {
            	if(!is_null($onlinePlayer->getTrail())) {
            		$onlinePlayer->updateTrail($onlinePlayer->getTrail());
				}
            }
        }
    }

	public function getAll() : array {
        return $this->trails;
    }
	
	public function getTrail(string $trail) : ?Trail {
        $lowerKeys = array_change_key_case($this->trails, CASE_LOWER);

        if(isset($lowerKeys[strtolower($trail)])) {
            return $lowerKeys[strtolower($trail)];
        }
        return null;
    }
}