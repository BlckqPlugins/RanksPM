<?php

namespace Rang\Group;

use pocketmine\permission\PermissionManager;
use Rang\Main\Rang;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

class Group implements Listener {

    private $plugin;
	
	public function __construct(Rang $plugin) {
		
		$this->plugin = $plugin;
		
		$groupfile = new Config(Rang::$pfad . "Groups.yml", Config::YAML);
		
		if(!$groupfile->exists("DefaultGroup")) {
			$groupfile->set("DefaultGroup", "Guest");
			$groupfile->save();
			
		}
		
		if(!$groupfile->exists($groupfile->get("DefaultGroup"))) {
			$groupfile->setNested($groupfile->get("DefaultGroup") . ".nametag", "&7[&8Guest&7] &8%name%");
			$groupfile->setNested($groupfile->get("DefaultGroup") . ".chatformat", "&7[&8Guest&7] &8%name% &7> &f%msg%");
			$groupfile->setNested($groupfile->get("DefaultGroup") . ".perms", array("pocketmine.command.transfer"));
			$groupfile->save();
			
		}
		
	}
	
	public function unregisterPlayer($name)
    {
		$name = strtolower($name);
		if (isset($this->plugin->attachments[$name])) {
			$player = $this->plugin->getServer()->getPlayerExact($name);
			if($player != null) {
				$player->removeAttachment($this->plugin->attachments[$name]);
			}
            unset($this->plugin->attachments[$name]);
        }
    }
	
	public function registerPlayer($name)
    {
		$name = strtolower($name);
		if (!isset($this->plugin->attachments[$name])) {
			$player = $this->plugin->getServer()->getPlayerExact($name);

            $attachment = null;
			if($player != null) {
			   $attachment = $player->addAttachment($this->plugin);
			}
            $this->plugin->attachments[$name] = $attachment;
        }
        $this->updatePermissions($name);
    }
	
	public function updatePermissions($name)
    {
		
		$groupfile = new Config(Rang::$pfad . "Groups.yml", Config::YAML);
		
        $permissions = [];
		$group = $this->getGroup($name);
		
        foreach($groupfile->getNested($group . ".perms") as $permission) {
            if ($permission == "*") {
                foreach(PermissionManager::getInstance()->getPermissions() as $perm) {
                    $permissions[$perm->getName()] = true;
                }
            } else {
                $permissions[$permission] = true;
            }
        }

        $attachment = $this->getAttachment($name);
        $attachment->clearPermissions();
        $attachment->setPermissions($permissions);
    }

    public function getAttachment($name)
    {
        return $this->plugin->attachments[$name];
    }
	
	public function getChat($name, $msg) {
		$playerfile = new Config(Rang::$pfad . "Players/" . strtolower($name) . ".yml", Config::YAML);
		$groupfile = new Config(Rang::$pfad . "Groups.yml", Config::YAML);
		
		if (!empty($playerfile->get("Group"))) {
			$group = $playerfile->get("Group");
			
			if ($groupfile->getNested($group . ".chatformat") != null) {
				
				if($playerfile->get("Nick") != "UNNICKED") {
					
					$filechatformat = $groupfile->getNested("Guest.chatformat");
					$chatformat = str_replace("&", TextFormat::ESCAPE, str_replace("%group%", $group, str_replace("%name%", $playerfile->get("Nick"), str_replace("%msg%", $msg, $filechatformat))));
					
				} else {
					
					$filechatformat = $groupfile->getNested($group . ".chatformat");
					$chatformat = str_replace("&", TextFormat::ESCAPE, str_replace("%group%", $group, str_replace("%name%", $name, str_replace("%msg%", $msg, $filechatformat))));
					
				}
				
				return $chatformat;
			}
			
		}
		
	}
	
	public function getNameTag($name) {
		$playerfile = new Config(Rang::$pfad . "Players/" . strtolower($name) . ".yml", Config::YAML);
		$groupfile = new Config(Rang::$pfad . "Groups.yml", Config::YAML);
		
		if (!empty($playerfile->get("Group"))) {
			$group = $playerfile->get("Group");
			if ($groupfile->getNested($group . ".nametag") != null) {
				
				if($playerfile->get("Nick") != "UNNICKED") {
					
					$filenametag = $groupfile->getNested("Guest.nametag");
					$nametag = str_replace("&", TextFormat::ESCAPE, str_replace("%group%", $group, str_replace("%name%", $playerfile->get("Nick"), $filenametag)));
					
				} else {
					
					$filenametag = $groupfile->getNested($group . ".nametag");
					$nametag = str_replace("&", TextFormat::ESCAPE, str_replace("%group%", $group, str_replace("%name%", $name, $filenametag)));
					
				}
				
				return $nametag;
			}
			
		}
		
	}
	
	public function setGroup($name, $group) {
		$groupfile = new Config(Rang::$pfad . "Groups.yml", Config::YAML);
		$playerfile = new Config(Rang::$pfad . "Players/" . strtolower($name) . ".yml", Config::YAML);
		
		$group = $groupfile->getNested($group . ".group");
		$playerfile->set("Group", $group);
		$playerfile->save();
		return true;
	}
	
	public function addGroup($group) {
		$groupfile = new Config(Rang::$pfad . "Groups.yml", Config::YAML);
		$groupfile->setNested($group . ".nametag", "&7[&8Guest&7] &8%name%");
		$groupfile->setNested($group . ".chatformat", "&7[&8Guest&7] &8%name% &7> &f%msg%");
		$groupfile->setNested($group . ".perms", array("pocketmine.command.transfer"));
		$groupfile->save();
		return true;
	}
	
	public function getGroup($name) {
		$groupfile = new Config(Rang::$pfad . "Groups.yml", Config::YAML);
		$playerfile = new Config(Rang::$pfad . "Players/" . strtolower($name) . ".yml", Config::YAML);
		return $playerfile->get("Group");
	}
	
}