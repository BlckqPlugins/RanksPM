<?php

namespace Rang\Main;

use pocketmine\utils\Config;

class Utils {

    public static function getNicks(): array{
        $nicks = new Config(Rang::$pfad . "nicks.yml", Config::YAML);
        return (array)$nicks->get("Nicks");
    }

}
