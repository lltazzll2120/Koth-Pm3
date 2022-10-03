<?php

namespace Tazz\KOTH;

final class Settings{

    /**
     * @return int
     */
    public static function getCaptureTime(): int{
        return Main::getInstance()->getConfig()->get("koth")["timeToCapture"];
    }

    /**
     * @return bool
     */
    public static function isKothEnabled(): bool{
        return Main::getInstance()->getConfig()->get("koth")["enabled"];
    }

    /**
     * @return array
     */
    public static function getExecuteWhenCaptured(): array{
        return Main::getInstance()->getConfig()->get("koth")["executeWhenCaptured"];
    }

    /**
     * @return string
     */
    public static function getCaptureBar(): string{
        return Main::getInstance()->getConfig()->get("captureBar");
    }

    /**
     * @return string
     */
    public static function getCaptureBarMessage(): string{
        return Main::getInstance()->getConfig()->get("captureBarMessage");
    }

    /**
     * @return string
     */
    public static function getBroadcastCaptureMessage(): string{
        return Main::getInstance()->getConfig()->get("broadcastCaptureMessage");
    }

    /**
     * @return string
     */
    public static function getOutsideKothMessage(): string{
        return Main::getInstance()->getConfig()->get("outsideKothMessage");
    }

    /**
     * @return string
     */
    public static function getBroadcastMessage(): string{
        return Main::getInstance()->getConfig()->get("broadcastMessage");
    }
}