<?php

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

use pocketmine\entity\{
    EffectInstance,
    Effect
};

use core\mcpe\form\{
    MenuOption,
    MenuForm,
    CustomForm,
    CustomFormResponse,
    FormIcon
};

use core\mcpe\form\element\Label;

use pocketmine\utils\TextFormat;

use pocketmine\Player;

class LobbyPlayer extends CorePlayer {
    /**
     * @var \lobby\Lobby
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

    public function getNameTagFormat() : string {
        $format = $this->getCoreUser()->getRank()->getNameTagFormat();
        $format = str_replace("{DISPLAY_NAME}", $this->getDisplayName(), $format);
        $format = str_replace("{NAME}", $this->getName(), $format);
        return $format;
    }

    public function getChatFormat(string $message) : string {
        $format = $this->getCoreUser()->getRank()->getChatFormat();
        $format = str_replace("{DISPLAY_NAME}", $this->getDisplayName(), $format);
        $format = str_replace("{NAME}", $this->getName(), $format);
        $format = str_replace("{MESSAGE}", $message, $format);
        return $format;
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
        $options = [];
        $options[] = new MenuOption("Trails");

        $this->sendForm(new class(TextFormat::GOLD . "Cosmetics", TextFormat::LIGHT_PURPLE . "Select a Cosmetic!", $options) extends MenuForm {
            public function __construct(string $title, string $text, array $options) {
                parent::__construct($title, $text, $options);
            }

            public function onSubmit(Player $player, int $selectedOption) : void {
                $selectedOptionText = $this->getOption($selectedOption)->getText();

                if($player instanceof LobbyPlayer) {
                    if($selectedOptionText === "Trails") {
                        if($player->hasPermission("lobby.trail.use")) {
                            $player->sendTrailsForm();
                        }
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
        $elements = [];
        $elements[] = new Label(TextFormat::GRAY . "Coming Soon..", "");

        $this->sendForm(new class(TextFormat::GOLD . "Gadgets", $elements) extends CustomForm {
            public function __construct($title, $elements) {
                parent::__construct($title, $elements);
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
                    $options[] = new MenuOption($trail->getName());
                }
                $options[] = new MenuOption($trail->getName(), new FormIcon(FormIcon::IMAGE_TYPE_URL, $trail->getIcon()));
            }
        }
        $this->sendForm(new class(TextFormat::GOLD . "Trails", TextFormat::LIGHT_PURPLE . "Select a Trail!", $options, $trail) extends MenuForm {
            private $trail;

            public function __construct(string $title, string $text, array $options, Trail $trail) {
                parent::__construct($title, $text, $options);

                $this->trail = $trail;
            }

            public function onSubmit(Player $player, int $selectedOption) : void {
                $selectedOptionText = $this->getOption($selectedOption)->getText();

                if($player instanceof LobbyPlayer) {
                    $trail = Lobby::getInstance()->getTrails()->getTrailFromString($selectedOptionText);

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