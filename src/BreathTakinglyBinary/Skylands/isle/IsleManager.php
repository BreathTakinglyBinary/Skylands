<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace BreathTakinglyBinary\Skylands\isle;


use BreathTakinglyBinary\Skylands\event\isle\IsleCloseEvent;
use BreathTakinglyBinary\Skylands\event\isle\IsleCreateEvent;
use BreathTakinglyBinary\Skylands\event\isle\IsleDisbandEvent;
use BreathTakinglyBinary\Skylands\event\isle\IsleOpenEvent;
use BreathTakinglyBinary\Skylands\generator\IsleGenerator;
use BreathTakinglyBinary\Skylands\session\BaseSession;
use BreathTakinglyBinary\Skylands\session\Session;
use BreathTakinglyBinary\Skylands\Skylands;
use pocketmine\level\Level;

class IsleManager {

    /** @var Isle[] */
    private $isles = [];

    /**
     * @return Isle[]
     */
    public function getIsles(): array {
        return $this->isles;
    }

    /**
     * @param string $identifier
     * @return null|Isle
     */
    public function getIsle(string $identifier): ?Isle {
        return $this->isles[$identifier] ?? null;
    }

    /**
     * @param Session $session
     * @param string $type
     * @throws \ReflectionException
     */
    public function createIsleFor(Session $session, string $type): void {
        $identifier = Skylands::generateUniqueId();

        $generatorManager = Skylands::getInstance()->getGeneratorManager();
        if($generatorManager->isGenerator($type)) {
            $generator = $generatorManager->getGenerator($type);
        } else {
            $generator = $generatorManager->getGenerator("Basic");
        }

        $server = Skylands::getInstance()->getServer();
        $server->generateLevel($identifier, null, $generator);
        $server->loadLevel($identifier);
        $level = $server->getLevelByName($identifier);
        /** @var IsleGenerator $generator */
        $level->setSpawnLocation($generator::getWorldSpawn());

        $this->openIsle($identifier, [$session->getOffline()], true, $type, $level);
        $session->setIsle($isle = $this->isles[$identifier]);
        $session->setRank(BaseSession::RANK_FOUNDER);
        $session->save();
        $isle->save();
        $session->setLastIslandCreationTime(microtime(true));
        (new IsleCreateEvent($isle))->call();
    }

    /**
     * @param Isle $isle
     * @throws \ReflectionException
     */
    public function disbandIsle(Isle $isle): void {
        $event = new IsleDisbandEvent($isle);
        $event->call();
        foreach($isle->getLevel()->getPlayers() as $player) {
            $player->teleport($player->getServer()->getDefaultLevel()->getSpawnLocation());
        }
        foreach($isle->getMembers() as $offlineMember) {
            $onlineSession = $offlineMember->getSession();
            if($onlineSession !== null) {
                $onlineSession->setIsle(null);
                $onlineSession->setRank(Session::RANK_DEFAULT);
                $onlineSession->save();
                $onlineSession->sendTranslatedMessage("ISLE_DISBANDED");
            } else {
                $offlineMember->setIsleId(null);
                $offlineMember->setRank(Session::RANK_DEFAULT);
                $offlineMember->save();
            }
        }
        $isle->setMembers([]);
        $isle->save();
        $this->closeIsle($isle);
        if($event->removeData){
            Skylands::getInstance()->getProvider()->deleteIsleData($isle->getIdentifier());
            Skylands::recursiveRemoveDirectory(Skylands::getInstance()->getServer()->getDataPath() . "worlds/" . $isle->getLevel()->getFolderName());
        }
    }

    /**
     * @param string $identifier
     * @param array $members
     * @param bool $locked
     * @param string $type
     * @param Level $level
     * @throws \ReflectionException
     */
    public function openIsle(string $identifier, array $members, bool $locked, string $type, Level $level): void {
        $this->isles[$identifier] = new Isle($this, $identifier, $members, $locked, $type, $level);
        (new IsleOpenEvent($this->isles[$identifier]))->call();
    }

    /**
     * @param Isle $isle
     * @throws \ReflectionException
     */
    public function closeIsle(Isle $isle): void {
        $isle->save();
        $server = Skylands::getInstance()->getServer();
        (new IsleCloseEvent($isle))->call();
        $server->unloadLevel($isle->getLevel());
        unset($this->isles[$isle->getIdentifier()]);
    }

}