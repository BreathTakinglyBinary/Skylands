<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace BreathTakinglyBinary\Skylands\provider\json;


use pocketmine\level\LevelException;
use pocketmine\utils\Config;
use BreathTakinglyBinary\Skylands\isle\Isle;
use BreathTakinglyBinary\Skylands\provider\Provider;
use BreathTakinglyBinary\Skylands\session\BaseSession;
use BreathTakinglyBinary\Skylands\session\Session;

class JSONProvider extends Provider {
    
    public function initialize(): void {
        $dataFolder = $this->plugin->getDataFolder();
        if(!is_dir($dataFolder . "isles")) {
            mkdir($dataFolder . "isles");
        }
        if(!is_dir($dataFolder . "users")) {
            mkdir($dataFolder . "users");
        }
    }
    
    /**
     * @param string $username
     * @return Config
     */
    private function getUserConfig(string $username): Config {
        return new Config($this->plugin->getDataFolder() . "users/$username.json", Config::JSON, [
                "isle" => null,
                "rank" => Session::RANK_DEFAULT
            ]);
    }

    /**
     * @param string $isleId
     *
     * @return string
     */
    private function getConfigPath(string $isleId): string{
        return $this->plugin->getDataFolder() . "isles/$isleId.json";
    }
    
    /**
     * @param string $isleId
     * @return null|Config
     */
    private function getIsleConfig(string $isleId, bool $force = false): ?Config {
        $config = null;
        $fileName = $this->getConfigPath($isleId);
        if(is_file($fileName) or $force){
            $config = new Config($fileName, Config::JSON);
        }
        return $config;
    }
    
    /**
     * @param BaseSession $session
     */
    public function loadSession(BaseSession $session): void {
        $config = $this->getUserConfig($session->getUsername());
        $session->setIsleId($config->get("isle", null));
        $session->setRank($config->get("rank", 1));
        $session->setLastIslandCreationTime($config->get("lastIsle", null));
    }
    
    /**
     * @param BaseSession $session
     */
    public function saveSession(BaseSession $session): void {
        $config = $this->getUserConfig($session->getUsername());
        $config->set("isle", $session->getIsleId());
        $config->set("rank", $session->getRank());
        $config->set("lastIsle", $session->getLastIslandCreationTime());
        $config->save();
    }

    /**
     * @param string $identifier
     *
     * @return bool
     */
    public function loadIsle(string $identifier): bool {
        if($this->plugin->getIsleManager()->getIsle($identifier) !== null) {
            return true;
        }
        $config = $this->getIsleConfig($identifier);
        if($config === null){
            return false;
        }
        $server = $this->plugin->getServer();
        if(!$server->isLevelLoaded($identifier)) {
            try{
                $server->loadLevel($identifier);
            } catch(LevelException $exception){
                $this->plugin->getLogger()->error("Failed to load level $identifier, which appears to have a valid configuration file.");
                $this->plugin->getLogger()->error($exception->getMessage());
                return false;
            }
        }
        
        $members = [];
        foreach($config->get("members", []) as $username) {
            $members[] = $this->plugin->getSessionManager()->getOfflineSession($username);
        }
        
        $this->plugin->getIsleManager()->openIsle(
            $identifier,
            $members,
            $config->get("locked"),
            $config->get("type"),
            $server->getLevelByName($identifier)
        );
        return true;
    }
    
    /**
     * @param Isle $isle
     */
    public function saveIsle(Isle $isle): void {
        $config = $this->getIsleConfig($isle->getIdentifier(), true);
        $config->set("identifier", $isle->getIdentifier());
        $config->set("locked", $isle->isLocked());
        $config->set("type", $isle->getType());
        
        $members = [];
        foreach($isle->getMembers() as $member) {
            $members[] = $member->getUsername();
        }
        $config->set("members", $members);
        
        $config->save();
    }

    /**
     * @param string $isleIdId
     */
    public function deleteIsleData(string $isleIdId) : void{
        if(is_file($this->getConfigPath($isleIdId))){
            unlink($this->getConfigPath($isleIdId));
        }
    }

}