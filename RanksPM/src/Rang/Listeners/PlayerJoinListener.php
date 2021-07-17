<?php

namespace Rang\Listeners;

use Rang\Main\Rang;
use Rang\Group\Group;

use pocketmine\event\player\PlayerJoinEvent;

use pocketmine\Player;
use pocketmine\event\Listener;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

class PlayerJoinListener implements Listener {

    public $group;

	public function __construct(Rang $plugin) {
		
		$this->plugin = $plugin;
		
		$this->group = new Group($plugin);
		
	}
	
	public function onJoin(PlayerJoinEvent $event) {
		$player = $event->getPlayer();
		$name = $event->getPlayer()->getName();

		if (!is_file(Rang::$pfad . "Players/" . strtolower($name) . ".yml")){
            $playerfile = new Config(Rang::$pfad . "Players/" . strtolower($name) . ".yml", Config::YAML);
            $playerfile->set("Group", "Guest");
            $playerfile->set("Nick", "UNNICKED");
            $playerfile->save();
        }

        $playerfile = new Config(Rang::$pfad . "Players/" . strtolower($name) . ".yml", Config::YAML);
		if ($playerfile->get("Group") == null) {
			$playerfile->set("Group", "Guest");
			$playerfile->save();
		}
		
		if ($playerfile->get("Nick") == null) {
			$playerfile->set("Nick", "UNNICKED");
			$playerfile->save();
		}

		$playerfile->save();
		$player->setDisplayName($this->group->getNameTag($name));
		$this->group->registerPlayer($name);
		
	}
	
}

?>