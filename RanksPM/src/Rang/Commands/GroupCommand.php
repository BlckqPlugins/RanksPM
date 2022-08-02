<?php

namespace Rang\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use Rang\Main\Rang;

class GroupCommand extends Command {

    private $plugin;

    public function __construct(Rang $plugin)
    {
        $this->plugin = $plugin;
        parent::__construct("rank", "Rank Command", "/rank set <Player> <Rank>", ["group"]);
        $this->setPermission("rank.admin");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$sender instanceof Player){
            $sender->sendMessage("§4You must be an player.");
            return;
        }

        if (!$this->testPermission($sender)){
            $sender->sendMessage("§4You don't have enough permissions to use this command.");
            return;
        }

        $groupfile = new Config(Rang::$pfad . "Groups.yml", Config::YAML);

        if (!empty($args[0])) {
            if (strtolower($args[0]) == "set") {
                if (!empty($args[1]) && !empty($args[2])) {
                    $targetfile = new Config(Rang::$pfad . "Players/" . strtolower($args[1]) . ".yml", Config::YAML);
                    if ($groupfile->exists($args[2])) {
                        $targetfile->set("Group", $args[2]);
                        $targetfile->save();
                        $sender->sendMessage($this->plugin->prefix . TextFormat::GREEN . "You have given " . TextFormat::GOLD . strtolower($args[1]) . TextFormat::GREEN . " the Rank " . TextFormat::GOLD . $args[2] . TextFormat::GRAY . ".");
                        $target = Server::getInstance()->getPlayerExact($args[1]);
                        if ($target != null) {
                            $target->kick(TextFormat::GREEN . "Rank update: " . TextFormat::GOLD . $args[2]);
                            $target->setDisplayName($this->plugin->group->getNameTag($args[1]));
                        }
                    } else {
                        $sender->sendMessage($this->plugin->prefix . TextFormat::RED . "The rank " . TextFormat::GOLD . $args[2] . TextFormat::RED . " don't exists.");
                    }
                } else {
                    $sender->sendMessage(TextFormat::RED . "Usage: /rank set <name> <rank>");
                }
            } elseif (strtolower($args[0]) == "add"){
                if (!empty($args[1])){
                    if ($this->plugin->group->addNewGroup($args[1])){
                        $sender->sendMessage($this->plugin->prefix . "§aSuccesfully added the group §e{$args[1]}§8.");
                    } else {
                        $sender->sendMessage($this->plugin->prefix . "§cThis group already exists.");
                    }
                }
            } elseif (strtolower($args[0]) == "list"){
                $message = "§cList of all ranks:\n";
                foreach ($this->plugin->group->getAllGroups() as $groups){
                    if (!str_starts_with("DefaultGroup", $groups)) {
                        $message .= "§7- §e{$groups}§r\n";
                    }
                }
                $sender->sendMessage($message);
            }
            if (strtolower($args[0]) == "help") {
                $sender->sendMessage($this->plugin->prefix . "-> /rank set <name> <rank>");
                $sender->sendMessage($this->plugin->prefix . "-> /rank add <groupname>");
                $sender->sendMessage($this->plugin->prefix . "-> /rank list");
            }
        } else {
            $sender->sendMessage($this->plugin->prefix . "-> /rank set <name> <rank>");
            $sender->sendMessage($this->plugin->prefix . "-> /rank add <groupname>");
            $sender->sendMessage($this->plugin->prefix . "-> /rank list");
        }
    }

}