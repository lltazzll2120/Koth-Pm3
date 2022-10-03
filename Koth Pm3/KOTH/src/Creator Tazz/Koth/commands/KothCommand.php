<?php

namespace Tazz\KOTH\commands;

use pocketmine\{command\CommandSender, command\PluginCommand, plugin\Plugin, Server, Player, utils\TextFormat};

use Tazz\KOTH\{Main, Settings};

class KothCommand extends PluginCommand{

    /** @var string[] $session */
    private $session = [];

    public function __construct(Plugin $owner){
        parent::__construct("koth", $owner);
        $this->setPermission("koth.command");
    }

    public function execute(CommandSender $player, string $commandLabel, array $args): bool{
        if($player instanceof Player){
            if(!$player->hasPermission("koth.command")){
                $player->sendMessage(TextFormat::RED . "No permission.");
                return true;
            }
            /** @var Main $plugin */
            $plugin = $this->getPlugin();
            if(isset($args[0])){
                if($args[0] == "start"){
                    if(!Settings::isKothEnabled()){
                        $arr = $plugin->getConfig()->get("koth");
                        $arr["enabled"] = true;
                        $plugin->getConfig()->set("koth", $arr);

                        $player->sendMessage(TextFormat::GREEN . "Koth has started");

                        foreach($plugin->getServer()->getOnlinePlayers() as $players){
                            $players->sendMessage(Settings::getBroadcastMessage());
                        }
                    }else{
                        $player->sendMessage(TextFormat::RED . "Koth is already enabled");
                    }
                }elseif($args[0] == "stop"){
                    if(Settings::isKothEnabled()){
                        $arr = $plugin->getConfig()->get("koth");
                        $arr["enabled"] = false;
                        $plugin->getConfig()->set("koth", $arr);

                        $player->sendMessage(TextFormat::GREEN . "Koth has been stopped");
                    }else{
                        $player->sendMessage(TextFormat::RED . "Koth is already disabled");
                    }
                }elseif($args[0] == "set1"){
                    $this->session[$player->getName()]["coords1"] = $player->getFloorX() . ":" . "0" . ":" . $player->getFloorZ() . ":" . $player->getLevel()->getName();

                    $player->sendMessage(TextFormat::GREEN . "Position 1 set");
                }elseif($args[0] == "set2"){
                    $this->session[$player->getName()]["coords2"] = $player->getFloorX() . ":" . "0" . ":" . $player->getFloorZ() . ":" . $player->getLevel()->getName();

                    $player->sendMessage(TextFormat::GREEN . "Position 2 set");
                }else{
                    $player->sendMessage(TextFormat::RED . "Usage:\n/koth start\n/koth set1\n/koth set2");
                }
                if(isset($this->session[$player->getName()]["coords1"]) && isset($this->session[$player->getName()]["coords2"])){
                    $arr = $plugin->getConfig()->get("koth");
                    $arr["coords1"] = $this->session[$player->getName()]["coords1"];
                    $arr["coords2"] = $this->session[$player->getName()]["coords2"];
                    $plugin->getConfig()->set("koth", $arr);

                    unset($this->session[$player->getName()]);

                    $player->sendMessage(TextFormat::GREEN . "You have set the koth position!");
                }
            }else{
                $player->sendMessage(TextFormat::RED . "Usage:\n/koth start\n/koth set1\n/koth set2");
            }
        }
        return true;
    }
}