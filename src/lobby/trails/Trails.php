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
            		$onlinePlayer->spawnTrail($onlinePlayer->getTrail());
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

    public function convertTrail(string $trail, Position $position, $data = null) : ?Particle {
        switch(strtolower($trail)) {
            case "explode":
                return new ExplodeParticle($position);
            case "huge explode":
                return new HugeExplodeParticle($position);
            case "bubble":
                return new BubbleParticle($position);
            case "splash":
                return new SplashParticle($position);
            case "water":
                return new WaterParticle($position);
            case "critical":
                return new CriticalParticle($position);
            case "spell":
                return new EnchantParticle($position);
            case "instant spell":
                return new InstantEnchantParticle($position);
            case "smoke":
                return new SmokeParticle($position, ($data === null ? 0 : $data));
            case "drip water":
                return new WaterDripParticle($position);
            case "drip lava":
                return new LavaDripParticle($position);
            case "spore":
                return new SporeParticle($position);
            case "portal":
                return new PortalParticle($position);
            case "entity flame":
                return new EntityFlameParticle($position);
            case "flame":
                return new FlameParticle($position);
            case "lava":
                return new LavaParticle($position);
            case "redstone":
                return new RedstoneParticle($position, ($data === null ? 1 : $data));
            case "snowball":
                return new ItemBreakParticle($position, Item::get(Item::SNOWBALL));
            case "slime":
                return new ItemBreakParticle($position, Item::get(Item::SLIMEBALL));
            case "heart":
                return new HeartParticle($position, ($data === null ? 0 : $data));
            case "ink":
                return new InkParticle($position, ($data === null ? 0 : $data));
            case "enchantment table":
                return new EnchantmentTableParticle($position);
            case "happy villager":
                return new HappyVillagerParticle($position);
            case "angry villager":
                return new AngryVillagerParticle($position);
            case "rain":
                return new RainSplashParticle($position);
            case "colourful":
            	return new DustParticle ($position, rand(0, 255), rand(0, 255), rand(0, 255));
        }
        if(substr($trail, 0, 5) === "item_") {
            $array = explode("_", $trail);
            return new ItemBreakParticle($position, new Item($array[1]));
        }
        if(substr($trail, 0, 6) === "block_") {
            $array = explode("_", $trail);
            return new TerrainParticle($position, Block::get($array[1]));
        }
        if(substr($trail, 0, 9) === "destroyblock_") {
            $array = explode("_", $trail);
            return new DestroyBlockParticle($position, Block::get($array[1]));
        }
		if(substr($trail, 0, 5 ) === "dust_") {
			$arr = explode("_", $trail);

			if(strpos($arr[1], ",") !== false) {
				$rgb = explode(",", $arr[1]);

				if(is_numeric($rgb[0]) && is_numeric($rgb[1]) && is_numeric($rgb[2])) {
					if($rgb[0] > -1 && $rgb[0] < 256 && $rgb[1] > -1 && $rgb[1] < 256 && $rgb[2] > -1 && $rgb[2] < 256) {
						return new DustParticle($position, $rgb[0], $rgb[1], $rgb[2]);
					}
				}
			}
			switch($arr[1]) {
				case "red":
				case "4":
				case "c":
					return new DustParticle($position, 252, 8, 8);
				case "orange" :
				case "6" :
					return new DustParticle($position, 252, 195, 8);
				case "yellow" :
				case "e" :
					return new DustParticle($position, 252, 252, 8);
				case "green":
				case "a" :
				case "2" :
					return new DustParticle($position, 8, 252, 8);
				case "aqua" :
				case "b" :
					return new DustParticle($position, 8, 252, 228);
				case "blue" :
				case "1" :
					return new DustParticle($position, 8, 8, 252);
				case "purple" :
				case "d" :
				case "5" :
					return new DustParticle($position, 252, 8, 252);
				case "pink" :
					return new DustParticle($position, 252, 8, 150);
				case "white" :
				case "f" :
					return new DustParticle($position, 255, 255, 255);
				case "black" :
				case "0" :
					return new DustParticle($position, 0, 0, 0);
				case "grey" :
				case "gray" :
					return new DustParticle($position, 138, 138, 138);
				default :
					return new DustParticle($position, 255, 255, 255);
			}
		}
		return new TerrainParticle($position, Block::get(0));
    }
}