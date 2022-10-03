<?php

namespace Tazz\KOTH;

use Tazz\KOTH\commands\KothCommand;
use pocketmine\{plugin\PluginBase, Player, utils\Config};
use pocketmine\utils\Utils;

class Main extends PluginBase {

    /** @var Config $config */
    private $config;

    /** @var self $object */
    protected static $object;

    public function onLoad() {
        self::$object = $this;
    }

    public static function getInstance(): self {
        return self::$object;
    }

    public function onEnable() {

        $this->saveDefaultConfig();
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        $this->getServer()->getCommandMap()->register("KOTHSystem", new KothCommand($this));
    }

    /**
     * @param Player $player
     * @return bool
     */
    public function isPlayerOnKoth(Player $player): bool{
        $pos1 = explode(":", $this->getConfig()->get("koth")["coords1"]);
        $pos2 = explode(":", $this->getConfig()->get("koth")["coords2"]);

        $minX = min($pos1[0], $pos2[0]);
        $maxX = max($pos1[0], $pos2[0]);
        $minZ = min($pos1[2], $pos2[2]);
        $maxZ = max($pos1[2], $pos2[2]);

        if(
            $player->getX() >= $minX && $player->getX() <= $maxX &&
            $player->getZ() >= $minZ && $player->getZ() <= $maxZ &&
            $player->getLevel()->getName() == $pos1[3]
        ){
            return true;
        }
        return false;
    }
}