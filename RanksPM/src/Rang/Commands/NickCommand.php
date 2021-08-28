<?php

namespace Rang\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use Rang\Main\Rang;
use Rang\Main\Utils;

class NickCommand extends Command {

    private $plugin;

    public function __construct(Rang $plugin)
    {
        $this->plugin = $plugin;
        parent::__construct("nick", "Nick Command", "/nick", []);
        $this->setPermission("nick.set");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage("§4You must be an player.");
            return;
        }

        if (!$this->testPermission($sender)) {
            $sender->sendMessage("§4You don't have enough permissions to use this command.");
            return;
        }

        $name = $sender->getName();
        $playerfile = new Config(Rang::$pfad . "Players/" . strtolower($name) . ".yml", Config::YAML);
        $player = $sender;

        if ($playerfile->get("Nick") != "UNNICKED") {
            $playerfile->set("Nick", "UNNICKED");
            $playerfile->save();
            $sender->sendMessage($this->plugin->prefix . TextFormat::RED . "You are no longer nicked.");
            $player->setNameTag($this->plugin->group->getNameTag($name));
        } else {
            $nicks = Utils::getNicks();
            $m = mt_rand(0, count($nicks) - 1);
            $nick = $nicks[$m];
            $playerfile->set("Nick", $nick);
            $playerfile->save();
            $player->setDisplayName($this->plugin->group->getNameTag($name));
            $sender->sendMessage($this->plugin->prefix . TextFormat::GREEN . "You are now nicked as " . TextFormat::GOLD . $nick . TextFormat::GREEN . ".");
        }
    }
}