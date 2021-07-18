<?php

namespace Rang\Main;

use pocketmine\Server;
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

        @mkdir("/home/data/");
        @mkdir("/home/data/Rank/");
        @mkdir("/home/data/Rank/Players/");

        @mkdir($this->getDataFolder());
        @mkdir($this->getDataFolder() . "data/");
        @mkdir($this->getDataFolder() . "data/Rank/");
        @mkdir($this->getDataFolder() . "data/Rank/Players/");

        self::$pfad = null;
        $config = $this->getConfig();
        if ($config->get("global") === true){
            self::$pfad = "/home/data/Rank/";
        } elseif ($config->get("global") === false) {
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
		
		$this->getServer()->getLogger()->info(TextFormat::GREEN . "Enabled.");
		
	}
	
	public function onCommand(CommandSender $sender, Command $command, string $label, array $args):bool
    {
        $name = $sender->getName();
        $playerfile = new Config(Rang::$pfad . "Players/" . strtolower($name) . ".yml", Config::YAML);
        $groupfile = new Config(Rang::$pfad . "Groups.yml", Config::YAML);
        $player = $sender;
        if ($player instanceof Player) {
            if ($command->getName() == "nick" && $sender->hasPermission("nick.set")) {
                if ($playerfile->get("Nick") != "UNNICKED") {
                    $playerfile->set("Nick", "UNNICKED");
                    $playerfile->save();
                    $sender->sendMessage($this->prefix . TextFormat::RED . "You are no longer nicked.");
                    $player->setNameTag($this->group->getNameTag($name));
                } else {
                    $nicks = Utils::getNicks();
                    $m = mt_rand(0, count($nicks) - 1);
                    $nick = $nicks[$m];
                    $playerfile->set("Nick", $nick);
                    $playerfile->save();
                    $player->setDisplayName($this->group->getNameTag($name));
                    $sender->sendMessage($this->prefix . TextFormat::GREEN . "You are now nicked as " . TextFormat::GOLD . $nick . TextFormat::GREEN . ".");
                }
            }

            if (strtolower($command->getName()) == "rank") {
                if (Server::getInstance()->isOp($player->getName())) {
                    if (!empty($args[0])) {
                        if (strtolower($args[0]) == "set") {
                            if (!empty($args[1]) && !empty($args[2])) {
                                $targetfile = new Config(Rang::$pfad . "Players/" . strtolower($args[1]) . ".yml", Config::YAML);
                                if ($groupfile->exists($args[2])) {
                                    $targetfile->set("Group", $args[2]);
                                    $targetfile->save();
                                    $sender->sendMessage($this->prefix . TextFormat::GREEN . "You have given " . TextFormat::GOLD . strtolower($args[1]) . TextFormat::GREEN . " the Rank " . TextFormat::GOLD . $args[2] . TextFormat::GRAY . ".");
                                    $target = $this->getServer()->getPlayerExact($args[1]);
                                    if ($target != null) {
                                        $target->kick(TextFormat::GREEN . "Rank update: " . TextFormat::GOLD . $args[2]);
                                        $target->setDisplayName($this->group->getNameTag($args[1]));
                                    }
                                } else {
                                    $sender->sendMessage($this->prefix . TextFormat::RED . "The rank " . TextFormat::GOLD . $args[2] . TextFormat::RED . " don't exists.");
                                }
                            } else {
                                $sender->sendMessage(TextFormat::RED . "Usage: /rank set <name> <rank>");
                            }
                        }
                        if (strtolower($args[0]) == "help") {
                            $sender->sendMessage($this->prefix . "-> /rank set <name> <rank>");
                        }
                    } else {
                        $sender->sendMessage($this->prefix . "-> /rank set <name> <rank>");
                    }
                } else {
                    $sender->sendMessage(TextFormat::RED . "You must be an Operator to execute this command§8.");
                }

            }
        }
        return true;
    }
	
	public function getGroup($name) {
		return $this->group->getGroup($name);
	}
}