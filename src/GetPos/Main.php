<?php

namespace GetPos;

use pocketmine\Player;
use pocketmine\Server;

use pocketmine\plugin\PluginBase;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;

use pocketmine\utils\Config;

use pocketmine\event\Listener;

use libs\FormAPI\SimpleForm;
use libs\FormAPI\CustomForm;

class Main extends PluginBase implements Listener {
    
    public function onEnable(){
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args):bool
    {
        switch($cmd->getName()){
        case "getpos":
        if($sender instanceof Player){
            $this->openCoordinate($sender);
          } else {
              $sender->sendMessage("§cYou can only use this command in-game");
          }
        }
        return true;
    }
    
    public function openCoordinate(Player $sender){
        $form = new SimpleForm(function (Player $sender, $data){
            $result = $data;
            if ($result == null) {
            }
            switch ($result) {
                    case 0:
                        if($this->getConfig()->get("exit.msg.enable") == true) {
                        $sender->sendMessage($this->getConfig()->get("exit.msg"));
                        }
                        break;
                        
                    case 1:
                        if ($sender->hasPermission("getpos.self")) {
                        $playerX = $sender->getX();
                        $playerY = $sender->getY();
                        $playerZ = $sender->getZ();
                        $myX = round($playerX, 1);
                        $myY = round($playerY, 1);
                        $myZ = round($playerZ, 1);
                        $playerLevel = $sender->getLevel()->getName();
                        $sender->sendMessage(str_replace(["{myX}", "{myY}", "{myZ}", "{myWorld}"], [round($playerX, 1), round($playerY, 1), round($playerZ, 1), $sender->getLevel()->getName()], $this->getConfig()->get("self.coord.msg")));
                        } else {
                            $sender->sendMessage($this->getConfig()->get("noperm.msg"));
                        }
                    return true;
                        break;
                        
                    case 2:
                        if ($sender->hasPermission("getpos.other")) {
                        $this->otherCoordinate($sender);
                        } else {
                            $sender->sendMessage($this->getConfig()->get("noperm.msg"));
                        }
                        break;
                        
                    case 3:
                        if ($sender->hasPermission("getpos.share")) {
                        $this->shareMyCoordinate($sender);
                        } else {
                            $sender->sendMessage($this->getConfig()->get("noperm.msg"));
                        }
                        break;
            }
        });
        $form->setTitle("§lGetPosUI");
        $form->setContent("Get your coordinate, other player's coordinate, or share your coodinate to other player!");
        $form->addButton("§cEXIT");
        $form->addButton("§lMy Coordinate");
        $form->addButton("§lOther's Coordinate");
        $form->addButton("§lShare My Coordinate");
        $form->sendToPlayer($sender);
        return $form;
    }
    
    public function otherCoordinate(Player $sender){
        $form = new CustomForm(function (Player $sender, $data){
            
            $target = $this->getServer()->getPlayer($data[1]);
                if($target === null || !$target->isOnline()) {
                    $sender->sendMessage($this->getConfig()->get("player.notfound.msg"));
                    return true;
						
				} else {
				    $targetName = $target->getName();
                    $targetX = $target->getX();
                    $targetY = $target->getY();
                    $targetZ = $target->getZ();
                    $toutX = round($targetX, 1);
                    $toutY = round($targetY, 1);
                    $toutZ = round($targetZ, 1);
                    $targetLevel = $target->getLevel()->getName();
                    $sender->sendMessage(str_replace(["{targetX}", "{targetY}", "{targetZ}", "{targetWorld}", "{target}"], [round($targetX, 1), round($targetY, 1), round($targetZ, 1), $target->getLevel()->getName(), $targetName], $this->getConfig()->get("other.coord.msg")));
                    return false;
				}
            
        });
        $form->setTitle("§lOther Coordinate");
        $form->addLabel("Get other player's coordinate!");
        $form->addInput("Type the player's name", "Player Name");
        $form->sendToPlayer($sender);
        return $form;
    }
    
    public function shareMyCoordinate(Player $sender){
        $form = new CustomForm(function (Player $sender, $data){
            
            $target = $this->getServer()->getPlayer($data[1]);
                if($target === null || !$target->isOnline()) {
                    $sender->sendMessage($this->getConfig()->get("player.notfound.msg"));
                    return true;
						
				} else {
				    $myName = $sender->getName();
				    $targetName = $target->getName();
                    $playerX = $sender->getX();
                    $playerY = $sender->getY();
                    $playerZ = $sender->getZ();
                    $myX = round($playerX, 1);
                    $myY = round($playerY, 1);
                    $myZ = round($playerZ, 1);
                    $playerLevel = $sender->getLevel()->getName();
                    $target->sendMessage(str_replace(["{myX}", "{myY}", "{myZ}", "{myWorld}", "{myName}"], [round($playerX, 1), round($playerY, 1), round($playerZ, 1), $sender->getLevel()->getName(), $myName], $this->getConfig()->get("sharemy.coord.msg")));
                    $sender->sendMessage(str_replace(["{player}"], [$targetName], $this->getConfig()->get("success.shared.msg")));
                    return false;
				}
            
        });
        $form->setTitle("§lShare Coordinate");
        $form->addLabel("Share your coordinate to other player!");
        $form->addInput("Send to:", "Player Name");
        $form->sendToPlayer($sender);
        return $form;
    }
}
