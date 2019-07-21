<?php

declare(strict_types = 1);

namespace lobby\trails;

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
}