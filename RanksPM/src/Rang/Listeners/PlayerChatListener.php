<?php

namespace Rang\Listeners;

use Rang\Main\Rang;
use Rang\Group\Group;

use pocketmine\event\player\PlayerChatEvent;

use pocketmine\Player;
use pocketmine\event\Listener;
use pocketmine\utils\Config;

class PlayerChatListener implements Listener {

    public $group;

	public function __construct(Rang $plugin) {
		
		$this->plugin = $plugin;
		$this->group = new Group($plugin);
		
	}
	
	public function onChat(PlayerChatEvent $event){
		$player = $event->getPlayer();
		$name = $event->getPlayer()->getName();
		$playerfile = new Config(Rang::$pfad . "Players/" . strtolower($name) . ".yml", Config::YAML);
		
		$pw = $event->getMessage();
		
		$msg = $pw;	
		$event->setFormat($this->group->getChat($name, $msg));
		
	}
	
}