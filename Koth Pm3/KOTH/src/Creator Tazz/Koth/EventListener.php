<?php

namespace Tazz\KOTH;

use Tazz\SimpleFaction\API\FactionsAPI;
use pocketmine\{command\ConsoleCommandSender, event\Listener, event\player\PlayerMoveEvent, event\player\PlayerQuitEvent,};
use Tazz\KOTH\{tasks\CaptureTask};

class EventListener implements Listener{

    /** @var Main $plugin */
    public $plugin;
    /** @var string[] $session */
    public $session = [];

    public function __construct(Main $plugin){
        $this->plugin = $plugin;
        $this->faction = $plugin->getServer()->getPluginManager()->getPlugin("SimpleFaction");
        if($this->faction === null){
            $plugin->getLogger()->critical("SimpleFaction plugin was not found");
        }
    }

    /**
     * @param PlayerMoveEvent $ev
     * @priority HIGHEST
     */
    public function onPlayerMove(PlayerMoveEvent $ev): void{
        $player = $ev->getPlayer();
        if(Settings::isKothEnabled()){
            if($this->plugin->isPlayerOnKoth($player)){
                if(FactionsAPI::isInFaction($player->getName())){
                    $factionName = FactionsAPI::getFaction($player->getName());
                    if(!isset($this->session[$factionName])){
                        $task = new CaptureTask($this, $factionName);
                        $this->plugin->getScheduler()->scheduleRepeatingTask($task, 20);
                        $this->session[$factionName]["task"] = $task->getTaskId();
                    }
                    if(!isset($this->session[$factionName]["players"][$player->getName()])){
                        $this->session[$factionName]["players"][$player->getName()] = true;
                    }
                }
            }else{
                foreach($this->session as $facName => $val){
                    if(isset($this->session[$facName]["players"][$player->getName()])){
                        unset($this->session[$facName]["players"][$player->getName()]);
                        $player->sendTip(Settings::getOutsideKothMessage());
                    }
                    if(empty($this->session[$facName]["players"])){
                        $this->plugin->getScheduler()->cancelTask($val["task"]);

                        unset($this->session[$facName]);
                    }
                }
            }
        }
    }

    /**
     * @param PlayerQuitEvent $ev
     * @priority LOWEST
     */
    public function onPlayerQuit(PlayerQuitEvent $ev): void{
        $player = $ev->getPlayer();
        foreach($this->session as $facName => $val){
            if(isset($this->session[$facName]["players"][$player->getName()])){
                unset($this->session[$facName]["players"][$player->getName()]);
            }
            if(empty($this->session[$facName]["players"])){
                $this->plugin->getScheduler()->cancelTask($val["task"]);

                unset($this->session[$facName]);
            }
        }
    }

    /**
     * @param string $factionName
     */
    public function factionCaptured(string $factionName): void{
        $msg = Settings::getBroadcastCaptureMessage();
        $msg = str_replace("{factionName}", $factionName, $msg);
        foreach($this->plugin->getServer()->getOnlinePlayers() as $player){
            $player->sendMessage($msg);
        }

        $arr = $this->plugin->getConfig()->get("koth");
        $arr["enabled"] = false;
        $this->plugin->getConfig()->set("koth", $arr);

        $this->executeRewardCommands($factionName);
    }

    /**
     * @param string $factionName
     */
    private function executeRewardCommands(string $factionName): void{
        foreach(Settings::getExecuteWhenCaptured() as $execute){
            foreach($this->session[$factionName]["players"] as $player => $useless){
                $execute = str_replace("{player}", $player, $execute);
                $this->plugin->getServer()->getCommandMap()->dispatch(new ConsoleCommandSender(), $execute);
            }
        }
    }
}