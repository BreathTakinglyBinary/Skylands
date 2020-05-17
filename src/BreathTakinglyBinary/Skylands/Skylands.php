<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace BreathTakinglyBinary\Skylands;

use BreathTakinglyBinary\DynamicCore\generators\VoidGenerator;
use BreathTakinglyBinary\Skylands\command\BaseIsleCommand;
use BreathTakinglyBinary\Skylands\isle\IsleManager;
use BreathTakinglyBinary\Skylands\provider\Provider;
use BreathTakinglyBinary\Skylands\provider\sqlite\SQLiteProvider;
use BreathTakinglyBinary\Skylands\session\SessionManager;
use pocketmine\item\Item;
use pocketmine\level\generator\GeneratorManager;
use pocketmine\plugin\PluginBase;
use pocketmine\plugin\PluginLogger;

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

    public function onEnable(): void {
        self::$object = $this;
        $this->loadResources();
        GeneratorManager::addGenerator(VoidGenerator::class, "void");
        $this->settings = new SkylandsSettings();
        $this->provider = new SQLiteProvider();
        $this->sessionManager = new SessionManager();
        $this->isleManager = new IsleManager();
        $this->getServer()->getPluginManager()->registerEvents(new SkylandsListener(), $this);
        $this->getServer()->getCommandMap()->register("skylands", new BaseIsleCommand());
        if($this->getServer()->getSpawnRadius() > -1) {
            $this->getLogger()->warning("Please, disable the spawn protection on your server.properties, otherwise Skylands won't work correctly");
        }
    }

    public function onDisable(): void {
        foreach($this->isleManager->getIsles() as $isle) {
            $isle->save();
        }
    }

    /**
     * @return Skylands
     */
    public static function getInstance(): Skylands {
        return self::$object;
    }

    /**
     * @return PluginLogger
     */
    public static function logger() : PluginLogger{
        return self::$object->getLogger();
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

    private function loadResources() : void{
        foreach($this->getResources() as $splFileInfo){
            $filePath = str_replace($this->getFile() . "resources" . DIRECTORY_SEPARATOR, "", $splFileInfo->getPathname());
            $this->saveResource($filePath);
        }
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

}