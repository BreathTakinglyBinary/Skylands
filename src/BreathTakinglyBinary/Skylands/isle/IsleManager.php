<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace BreathTakinglyBinary\Skylands\isle;


use BreathTakinglyBinary\DynamicCore\util\FileUtils;
use BreathTakinglyBinary\DynamicCore\util\Utils;
use BreathTakinglyBinary\Skylands\locale\TranslationManager;
use BreathTakinglyBinary\Skylands\Skylands;
use http\Exception\RuntimeException;
use pocketmine\level\format\Chunk;
use pocketmine\level\format\io\region\McRegion;
use pocketmine\level\generator\GeneratorManager;
use pocketmine\level\Level;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\tile\Chest;
use pocketmine\tile\Tile;
use pocketmine\utils\Config;

class IsleManager{

    /** @var IsleManager */
    private static $instance;

    /** @var InviteManger */
    private $inviteManager;

    /** @var Isle[] */
    private $isles = [];

    /** @var IsleType[] */
    private $types = [];

    /** @var IsleType */
    private $defaultIsleType;

    public function __construct(){
        if(self::$instance instanceof IsleManager){
            throw new \RuntimeException("Only one instance of " . self::class . " allowed!");
        }
        $this->loadTypes();
        $this->inviteManager = new InviteManger();
        self::$instance = $this;
    }

    /**
     * @return InviteManger
     */
    public function getInviteManager() : InviteManger{
        return $this->inviteManager;
    }

    /**
     * @return IsleManager
     */
    public static function getInstance() : IsleManager{
        return self::$instance;
    }

    private function loadTypes() : void{
        Skylands::logger()->debug("Starting to load isle types.");
        $typeDir = Skylands::getInstance()->getDataFolder() . DIRECTORY_SEPARATOR . "types" . DIRECTORY_SEPARATOR;
        if(!is_dir($typeDir)){
            throw new \RuntimeException("Missing required \"types\" folder for Skylands!.");
        }
        $itr = new \DirectoryIterator($typeDir);
        foreach($itr as $fileInfo){
            if($fileInfo->isDot()){
                continue;
            }
            Skylands::logger()->debug("Testing path " . $fileInfo->getRealPath());
            if($fileInfo->isDir() and is_file(($settingFile = $fileInfo->getRealPath() . DIRECTORY_SEPARATOR . "isleType.yml"))){
                $name = $fileInfo->getBasename();
                Skylands::logger()->info("Attempting to load type $name");

                if(!Server::getInstance()->isLevelGenerated($name)){
                    Skylands::getInstance()->getLogger()->info("Adding world for isle type $name");
                    FileUtils::recursiveDirectoryCopy($fileInfo->getRealPath(), $this->getWorldsFolder() . $name);
                }
                if(!Server::getInstance()->loadLevel($name)){
                    throw new \RuntimeException("Level for template $name was not able to be loaded!");
                }
                $level = Server::getInstance()->getLevelByName($name);

                $chunks[Level::chunkHash(0,0)] = $level->getChunk(0,0)->fastSerialize();
                $chunks[Level::chunkHash(0,-1)] = $level->getChunk(0,-1)->fastSerialize();
                $chunks[Level::chunkHash(-1,0)] = $level->getChunk(-1,0)->fastSerialize();
                $chunks[Level::chunkHash(-1,-1)] = $level->getChunk(-1,-1)->fastSerialize();

                $this->types[$name] = $this->createTypeFromConfig($name, $settingFile, $chunks);
                $isleConfig = new Config($settingFile);
                if($isleConfig->get("default", false) or !isset($this->defaultIsleType)){
                    $this->defaultIsleType = $this->types[$name];
                }

            }
        }
    }

    private function createTypeFromConfig(string $name, string $configFilePath, array $chunks) : IsleType{
        $typeCfg = new Config($configFilePath);

        $chestCfg = $typeCfg->get("chest", null);
        if($chestCfg === null){
            throw new \RuntimeException("Missing chest location entry in $configFilePath !");
        }
        try{
            $chestLoc = Utils::vector3FromString((string) $chestCfg);
        }catch(\InvalidArgumentException $exception){
            Skylands::logger()->debug("ERROR: " . $exception->getMessage());
            throw new \RuntimeException("Invalid chest location entry found in $configFilePath", 0, $exception);
        }

        $spawnCfg = $typeCfg->get("spawn", null);
        if($spawnCfg === null){
            throw new \RuntimeException("Missing spawn location entry in $configFilePath !");
        }
        try{
            $spawnLoc = Utils::vector3FromString((string) $spawnCfg);
        }catch(\InvalidArgumentException $exception){
            throw new \RuntimeException("Invalid spawn location entry found in $configFilePath", 0, $exception);
        }

        return new IsleType($name, $spawnLoc, $chestLoc, $chunks);
    }

    /**
     * @return Isle[]
     */
    public function getIsles() : array{
        return $this->isles;
    }

    public function getIsle(string $identifier) : ?Isle{
        return $this->isles[$identifier] ?? null;
    }

    public function createIsle(Player $owner, IsleType $type, string $name, bool $locked) : Isle{
        do{
            $identifier = str_replace(" ", "", uniqid("", true));
        }while(Server::getInstance()->isLevelGenerated($identifier));
        $this->createNewIsleWorld($identifier, $type);
        $isle = new Isle($owner->getUniqueId(), $identifier, $type, $name, $locked, [], 0);
        $this->openIsle($isle);
        return $isle;
    }

    private function createNewIsleWorld(string $identifier, IsleType $type) : void{
        $worldDir = Server::getInstance()->getDataPath() . "worlds" . DIRECTORY_SEPARATOR . $identifier . DIRECTORY_SEPARATOR;
        $generator = GeneratorManager::getGenerator("void");
        McRegion::generate($worldDir, $identifier, 0, $generator);
        $this->resetIsle($identifier, $type);

    }

    private function resetIsle(string $identifier, IsleType $type) {
        $server = Server::getInstance();
        $server->loadLevel($identifier);
        $level = $server->getLevelByName($identifier);
        if($level === null){
            throw new \RuntimeException("Isle creation failed trying to create world \"$identifier\"");
        }
        $level->setSpawnLocation($type->getSpawn());

        for($x = -1; $x < 1; $x++){
            for($z = -1; $z < 1; $z++){
                $level->loadChunk($x, $z);
                $level->saveChunks();
                $level->setChunk($x, $z, $type->getChunk($x, $z));
            }
        }
        $chestLoc = $type->getChestLocation();
        $chest = $level->getBlock($chestLoc);
        if(!$chest instanceof \pocketmine\block\Chest){
            throw new RuntimeException("Invalid chest location for island type " . $type->getName() . " Chest block not found!");
        }

        $chestTile = Chest::createTile(Tile::CHEST, $level, Chest::createNBT($chestLoc));
        if(!$chestTile instanceof Chest){
            throw new \RuntimeException("Invalid chest location for island type " . $type->getName() . ". ChestTile not found.");
        }
        $chestTile->getInventory()->clearAll();
        foreach(Skylands::getInstance()->getSettings()->getChestPerIsleType($type) as $item){
            $chestTile->getInventory()->addItem($item);
        }
        $level->addTile($chestTile);

    }

    public function openIsle(Isle $isle){
        if(isset($this->isles[$isle->getIdentifier()])){
            throw new \RuntimeException("Tried to open an Isle that was already open!");
        }
        if(!Server::getInstance()->loadLevel($isle->getIdentifier())){
            throw new \RuntimeException("Unable to open island " . $isle->getIdentifier() . " because of a missing world!");
        }
        Server::getInstance()->getLevelByName($isle->getIdentifier());
        $this->isles[$isle->getIdentifier()] = $isle;
    }

    public function closeIsle(Isle $isle){
        $isle->save();
        foreach($isle->getLevel()->getPlayers() as $nonMember){
            $nonMember->teleport(Skylands::getInstance()->getServer()->getDefaultLevel()->getSafeSpawn());
            $nonMember->sendMessage(TranslationManager::getTranslatedMessage("ISLE_CLOSING"));
        }
        Server::getInstance()->unloadLevel($isle->getLevel());
        unset($this->isles[$isle->getIdentifier()]);
    }

    public function closeAllIsles() : void{
        foreach($this->isles as $isle){
            $this->closeIsle($isle);
        }
    }

    /**
     * @return IsleType[]
     */
    public function getIsleTypes() : array{
        return $this->types;
    }

    /**
     * @param string $name
     *
     * @return IsleType|null
     */
    public function getIsleTypeByName(string $name) : ?IsleType{
        return $this->types[$name] ?? null;
    }

    private function getWorldsFolder() : string{
        return Server::getInstance()->getDataPath() . "worlds" . DIRECTORY_SEPARATOR;
    }


}