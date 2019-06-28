<?php

declare(strict_types = 1);

namespace lobby;

use core\Core;
use core\CorePlayer;

use lobby\item\{
    ServerSelector,
    Profile,
    Cosmetics,
    Gadgets
};

use lobby\trail\Trail;

use core\mcpe\form\{
	MenuForm,
	CustomForm,
	CustomFormResponse
};
use core\mcpe\form\element\{
	Button,
	Image,
	Label
};

use pocketmine\Player;

use pocketmine\entity\{
    EffectInstance,
    Effect
};

use pocketmine\utils\TextFormat;

class LobbyPlayer extends CorePlayer {
    /**
     * @var Lobby
     */
    private $lobby;

    public $doubleJump = [];

    public function setLobby(Lobby $lobby) {
        $this->lobby = $lobby;
    }

    public function joinLobby() {
        $this->getLevel()->setTime(5000);
        $this->getLevel()->stopTime();
        $this->addEffect(new EffectInstance(Effect::getEffect(Effect::SPEED), 9999999, 1, false));
        $this->getInventory()->clearAll();
        $this->getInventory()->setItem(1, new ServerSelector());
        $this->getInventory()->setItem(3, new Profile());
        $this->getInventory()->setItem(5, new Cosmetics());
        $this->getInventory()->setItem(7, new Gadgets());
    }

    public function spawnTrail(?Trail $trail) {
        $trail->spawnTo($this);
    }

    public function despawnTrail(Trail $trail) {
        $trail->despawnFrom($this);
    }

    public function randomTrail() {
        foreach($this->lobby->getTrails()->getAll() as $trail) {
            if($trail instanceof Trail) {
                $this->despawnTrail($trail);
                $trail->spawnRandom($this);
            }
        }
    }

    public function sendCosmeticsForm() {
        $this->sendMessage($this->lobby->getPrefix() . "Opened Cosmetics menu");
        //ToDo: Cosmetics (Armor, Changing Armor, Dance)
		$b1 = new Button(TextFormat::GRAY . "Trails");

		$b1->setId(1);

		$options = [
			$b1
		];

        $this->sendForm(new class(TextFormat::GOLD . "Cosmetics", TextFormat::LIGHT_PURPLE . "Select a Cosmetic!", $options) extends MenuForm {
           	public function __construct(string $title, string $text, array $buttons = [], ?\Closure $onSubmit = null, ?\Closure $onClose = null) {
				parent::__construct($title, $text, $buttons, $onSubmit, $onClose);
			}

            public function onSubmit(Player $player, Button $selectedOption) : void {
                if($player instanceof LobbyPlayer) {
                    switch($selectedOption->getId()) {
						case 1:
							if($player->hasPermission("lobby.trail.use")) {
								$player->sendTrailsForm();
							}
						break;
                    }
                }
            }

            public function onClose(Player $player) : void {
                $player->sendMessage(Lobby::getInstance()->getPrefix() . "Closed Servers menu");
            }
        });
    }

    public function sendGadgetsForm() {
        $this->sendMessage($this->lobby->getPrefix() . "Opened Gadgets menu");
        //TODO: Gadgets (Hide Players, Fly, Pets)

        $e1 = new Label(TextFormat::GRAY . "Coming Soon..");

        $e1->setValue(1);

        $elements = [
        	$e1
		];

        $this->sendForm(new class(TextFormat::GOLD . "Gadgets", $elements) extends CustomForm {
            public function __construct(string $title, array $elements, \Closure $onSubmit, ?\Closure $onClose = null) {
				parent::__construct($title, $elements, $onSubmit, $onClose);
			}

			public function onSubmit(Player $player, CustomFormResponse $data) : void {

            }

            public function onClose(Player $player) : void {
                $player->sendMessage(Lobby::getInstance()->getPrefix() . "Closed Gadgets menu");
            }
        });
    }

    public function sendTrailsForm() {
        $this->sendMessage(Lobby::getInstance()->getPrefix() . "Opened Trails menu");

        $options = [];
        $trail = null;

        foreach($this->lobby->getTrails()->getAll() as $trail) {
            if($trail instanceof Trail) {
                if(empty($trail->getIcon())) {
                	$b1 = new Button(TextFormat::GRAY . $trail->getName());

                	$b1->setId($trail->getName());

                	$options[] = $b1;
                }
                $b2 = new Button(TextFormat::GRAY . $trail->getName(), new Image($trail->getIcon(), Image::TYPE_URL));

                $b2->setId($trail->getName());

                $options[] = $b2;
            }
        }
        $this->sendForm(new class(TextFormat::GOLD . "Trails", TextFormat::LIGHT_PURPLE . "Select a Trail!", $options, $trail) extends MenuForm {
            private $trail;

            public function __construct(string $title, string $text, array $buttons = [], ?\Closure $onSubmit = null, ?\Closure $onClose = null, Trail $trail) {
				parent::__construct($title, $text, $buttons, $onSubmit, $onClose);

				$this->trail = $trail;
			}

			public function onSubmit(Player $player, Button $selectedOption) : void {;
                if($player instanceof LobbyPlayer) {
                    $trail = Lobby::getInstance()->getTrails()->getTrailFromString($selectedOption->getId());

                    if($trail instanceof Trail) {
                        if(!$player->hasPermission("lobby.trail." . $trail->getName())) {
                            $player->sendMessage(Core::getInstance()->getErrorPrefix() . "You do not have Permission to use this Trail");
                        } else {
                            $player->spawnTrail($this->trail);
                        }
                    }
                }
            }

            public function onClose(Player $player) : void {
                $player->sendMessage(Lobby::getInstance()->getPrefix() . "Closed Trails menu");
            }
        });
    }
}