<?php

namespace Rang\Main;

use pocketmine\Server;
use Rang\Commands\GroupCommand;
use Rang\Commands\NickCommand;
use Rang\Group\Group;

use Rang\Listeners\PlayerChatListener;
use Rang\Listeners\PlayerJoinListener;
use Rang\Listeners\PlayerQuitListener;

use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

use pocketmine\command\CommandSender;
use pocketmine\command\Command;

class Rang extends PluginBase implements Listener {
	
	public $prefix = TextFormat::GRAY . "[" . TextFormat::RED . "RankSystem" . TextFormat::GOLD . "" . TextFormat::GRAY . "] " . TextFormat::RESET;
	public static $pfad;
	public $config;

	/** @var Group */
	public $group;

	/** @var array */
	public $attachments = [];
	
	public function onEnable(): void{
		$this->group = new Group($this);
        $this->saveDefaultConfig();

        self::$pfad = null;
        $config = $this->getConfig();
        $global = $config->get("global") ?? false;

        if ($global){
            @mkdir("/home/data/");
            @mkdir("/home/data/Rank/");
            @mkdir("/home/data/Rank/Players/");
            self::$pfad = "/home/data/Rank/";
        } else {
            @mkdir($this->getDataFolder());
            @mkdir($this->getDataFolder() . "data/");
            @mkdir($this->getDataFolder() . "data/Rank/");
            @mkdir($this->getDataFolder() . "data/Rank/Players/");
            self::$pfad = ($this->getDataFolder() . "data/Rank/");
        }

        if (!is_file(Rang::$pfad . "nicks.yml")) {
            $nicks = new Config(Rang::$pfad . "nicks.yml", Config::YAML);
            $nicks->set("Nicks", [
                "AntiAcYT",
                "MarvinPlayerHD",
                "KaiDerCoole",
                "SchwitzerGang28",
                "AntiSchwitzerHD",
                "RandomJohn4812"
            ]);
            $nicks->save();
        }
		
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getServer()->getPluginManager()->registerEvents(new Group($this), $this);
		
		// Listeners
		$this->getServer()->getPluginManager()->registerEvents(new PlayerChatListener($this), $this);
		$this->getServer()->getPluginManager()->registerEvents(new PlayerJoinListener($this), $this);
		$this->getServer()->getPluginManager()->registerEvents(new PlayerQuitListener($this), $this);

		// Commands
        $map = Server::getInstance()->getCommandMap();
        $map->register("group", new GroupCommand($this));
        $map->register("nick", new NickCommand($this));
		
		$this->getServer()->getLogger()->info(TextFormat::GREEN . "Enabled.");
		
	}
	
	public function getGroup($name) {
		return $this->group->getGroup($name);
	}
}