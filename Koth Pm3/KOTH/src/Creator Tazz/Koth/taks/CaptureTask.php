<?php

namespace Tazz\KOTH\tasks;

use pocketmine\{scheduler\Task, utils\TextFormat};
use Tazz\KOTH\{EventListener, Settings};

class CaptureTask extends Task{

    /** @var EventListener $plugin */
    private $plugin;
    /** @var string $factionName */
    private $factionName;
    /** @var int $time */
    private $time = 0;

    public function __construct(EventListener $plugin, string $factionName){
        $this->plugin = $plugin;
        $this->factionName = $factionName;
    }

    public function onRun(int $currentTick){
        foreach($this->plugin->session as $facName => $val){
            if($facName === $this->factionName){
                foreach($val["players"] as $playerName => $useless){
                    $player = $this->plugin->plugin->getServer()->getPlayerExact($playerName);
                    if($player === null) continue;

                    $greenBars = TextFormat::GREEN . str_repeat(":", ($this->time / Settings::getCaptureTime()) * 80);
                    $grayBars = (strlen($greenBars) < 80 ? TextFormat::GRAY . str_repeat(":", 80 - strlen($greenBars)) : "");
                    $bar = Settings::getCaptureBar();
                    $bar = str_replace("{bar}", $greenBars . $grayBars, $bar);
                    $bar = str_replace("{time}", Settings::getCaptureTime() - $this->time, $bar);
                    $bar = Settings::getCaptureBarMessage() . "\n" . $bar;
                    $player->sendTip($bar);
                }
            }
        }
        if($this->time >= Settings::getCaptureTime()){
            $this->plugin->factionCaptured($this->factionName);
            foreach($this->plugin->session as $facName => $val){
                $this->plugin->plugin->getScheduler()->cancelTask($val["task"]);
                unset($this->plugin->session[$facName]);
            }
        }
        $this->time++;
    }
}