<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace BreathTakinglyBinary\Skylands;

use pocketmine\item\Item;
use pocketmine\level\Position;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use BreathTakinglyBinary\Skylands\command\IsleCommandMap;
use BreathTakinglyBinary\Skylands\generator\IsleGeneratorManager;
use BreathTakinglyBinary\Skylands\isle\IsleManager;
use BreathTakinglyBinary\Skylands\provider\json\JSONProvider;
use BreathTakinglyBinary\Skylands\provider\Provider;
use BreathTakinglyBinary\Skylands\session\SessionManager;

class Skylands extends PluginBase {

    /** @var Skylands */
    private static $object = null;

    /** @var SkylandsSettings */
    private $settings;

    /** @var Provider */
    private $provider;

    /** @var SessionManager */
    private $sessionManager;

    /** @var IsleManager */
    private $isleManager;

    /** @var IsleCommandMap */
    private $commandMap;

    /** @var IsleGeneratorManager */
    private $generatorManager;

    /** @var SkylandsListener */
    private $eventListener;

    public function onLoad(): void {
        self::$object = $this;
        if(!is_dir($this->getDataFolder())) {
            mkdir($this->getDataFolder());
        }
        $this->saveResource("messages.json");
        $this->saveResource("settings.json");
    }

    public function onEnable(): void {
        $this->settings = new SkylandsSettings($this);
        $this->provider = new JSONProvider($this);
        $this->sessionManager = new SessionManager($this);
        $this->isleManager = new IsleManager($this);
        $this->generatorManager = new IsleGeneratorManager($this);
        $this->commandMap = new IsleCommandMap($this);
        $this->eventListener = new SkylandsListener($this);
        if($this->getServer()->getSpawnRadius() > 0) {
            $this->getLogger()->warning("Please, disable the spawn protection on your server.properties, otherwise SkyBlock won't work correctly");
        }
        $this->getLogger()->info("SkyBlock was enabled");
    }

    public function onDisable(): void {
        foreach($this->isleManager->getIsles() as $isle) {
            $isle->save();
        }
        $this->getLogger()->info("SkyBlock was disabled");
    }

    /**
     * @return Skylands
     */
    public static function getInstance(): Skylands {
        return self::$object;
    }

    /**
     * @return SkylandsSettings
     */
    public function getSettings(): SkylandsSettings {
        return $this->settings;
    }

    /**
     * @return Provider
     */
    public function getProvider(): Provider {
        return $this->provider;
    }

    /**
     * @return SessionManager
     */
    public function getSessionManager(): SessionManager {
        return $this->sessionManager;
    }

    /**
     * @return IsleManager
     */
    public function getIsleManager(): IsleManager {
        return $this->isleManager;
    }

    /**
     * @return IsleGeneratorManager
     */
    public function getGeneratorManager(): IsleGeneratorManager {
        return $this->generatorManager;
    }

    /**
     * @param Position $position
     * @return string
     */
    public static function writePosition(Position $position): string {
        return "{$position->getLevel()->getName()},{$position->getX()},{$position->getY()},{$position->getZ()}";
    }

    /**
     * @param string $position
     * @return null|Position
     */
    public static function parsePosition(string $position): ?Position {
        $array = explode(",", $position);
        if(isset($array[3])) {
            $level = Server::getInstance()->getLevelByName($array[0]);
            if($level !== null) {
                return new Position((float) $array[1],(float) $array[2],(float) $array[3], $level);
            }
        }
        return null;
    }

    /**
     * Parse an Item
     *
     * @param string $item
     * @return null|Item
     */
    public static function parseItem(string $item): ?Item {
        $parts = explode(",", $item);
        foreach($parts as $key => $value) {
            $parts[$key] = (int) $value;
        }
        if(isset($parts[0])) {
            return Item::get($parts[0], $parts[1] ?? 0, $parts[2] ?? 1);
        }
        return null;
    }

    /**
     * @param array $items
     * @return array
     */
    public static function parseItems(array $items): array {
        $result = [];
        foreach($items as $item) {
            $item = self::parseItem($item);
            if($item !== null) {
                $result[] = $item;
            }
        }
        return $result;
    }

    /**
     * @param string $message
     * @return string
     */
    public static function translateColors(string $message): string {
        $message = str_replace("&", TextFormat::ESCAPE, $message);
        $message = str_replace("{BLACK}", TextFormat::BLACK, $message);
        $message = str_replace("{DARK_BLUE}", TextFormat::DARK_BLUE, $message);
        $message = str_replace("{DARK_GREEN}", TextFormat::DARK_GREEN, $message);
        $message = str_replace("{DARK_AQUA}", TextFormat::DARK_AQUA, $message);
        $message = str_replace("{DARK_RED}", TextFormat::DARK_RED, $message);
        $message = str_replace("{DARK_PURPLE}", TextFormat::DARK_PURPLE, $message);
        $message = str_replace("{ORANGE}", TextFormat::GOLD, $message);
        $message = str_replace("{GRAY}", TextFormat::GRAY, $message);
        $message = str_replace("{DARK_GRAY}", TextFormat::DARK_GRAY, $message);
        $message = str_replace("{BLUE}", TextFormat::BLUE, $message);
        $message = str_replace("{GREEN}", TextFormat::GREEN, $message);
        $message = str_replace("{AQUA}", TextFormat::AQUA, $message);
        $message = str_replace("{RED}", TextFormat::RED, $message);
        $message = str_replace("{LIGHT_PURPLE}", TextFormat::LIGHT_PURPLE, $message);
        $message = str_replace("{YELLOW}", TextFormat::YELLOW, $message);
        $message = str_replace("{WHITE}", TextFormat::WHITE, $message);
        $message = str_replace("{OBFUSCATED}", TextFormat::OBFUSCATED, $message);
        $message = str_replace("{BOLD}", TextFormat::BOLD, $message);
        $message = str_replace("{STRIKETHROUGH}", TextFormat::STRIKETHROUGH, $message);
        $message = str_replace("{UNDERLINE}", TextFormat::UNDERLINE, $message);
        $message = str_replace("{ITALIC}", TextFormat::ITALIC, $message);
        $message = str_replace("{RESET}", TextFormat::RESET, $message);
        return $message;
    }

    /**
     * @return string
     */
    public static function generateUniqueId(): string {
        return uniqid("is-");
    }

    /**
     * Thanks to the community at StackOverflow for this:
     * https://stackoverflow.com/questions/11267086/php-unlink-all-files-within-a-directory-and-then-deleting-that-directory
     *
     * @param $directory
     */
    public static function recursiveRemoveDirectory($directory){
        foreach(glob("{$directory}/*") as $file){
            if(is_dir($file)){
                self::recursiveRemoveDirectory($file);
            }else{
                unlink($file);
            }
        }
        rmdir($directory);
    }

}