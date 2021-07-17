<?php

namespace Rang\Listeners;

use Rang\Main\Rang;

use pocketmine\event\player\PlayerQuitEvent;

use pocketmine\Player;
use pocketmine\event\Listener;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

class PlayerQuitListener implements Listener {

    private $plugin;
	
	public function __construct(Rang $plugin) {
		$this->plugin = $plugin;
	}
	
	public function onQuit(PlayerQuitEvent $event) {
		$player = $event->getPlayer();
		$name = $event->getPlayer()->getName();
		$this->plugin->group->unregisterPlayer($name);
	}
}