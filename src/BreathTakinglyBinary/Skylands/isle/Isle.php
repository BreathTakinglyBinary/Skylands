<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace BreathTakinglyBinary\Skylands\isle;


use BreathTakinglyBinary\Skylands\session\Session;
use BreathTakinglyBinary\Skylands\Skylands;
use pocketmine\level\Level;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\UUID;

class Isle{

    /** @var UUID */
    private $ownerUUID;

    /** @var string */
    private $identifier;

    /** @var IsleType */
    private $type;

    /** @var string */
    private $name;

    /** @var bool */
    private $locked = false;

    /** @var UUID[] */
    private $helpers = [];

    /** @var int */
    private $points;

    /**
     * Isle constructor.
     *
     * @param UUID     $ownerUUID
     * @param string   $identifier
     * @param IsleType $type
     * @param string   $name
     * @param bool     $locked
     * @param array    $helpers
     * @param int      $points
     */
    public function __construct(UUID $ownerUUID, string $identifier, IsleType $type, string $name, bool $locked, array $helpers, int $points){
        $this->ownerUUID = $ownerUUID;
        $this->identifier = $identifier;
        $this->type = $type;
        $this->name = $name;
        $this->locked = $locked;
        $this->setHelpers($helpers);
        $this->points = $points;
    }

    /**
     * @return UUID
     */
    public function getOwnerUUID() : UUID{
        return $this->ownerUUID;
    }

    public function isOwner(Player $player) : bool{
        return $player->getUniqueId()->toString() === $this->ownerUUID->toString();
    }

    /**
     * @return string
     */
    public function getIdentifier() : string{
        return $this->identifier;
    }

    /**
     * @return IsleType
     */
    public function getType() : IsleType{
        return $this->type;
    }

    /**
     * @return string
     */
    public function getName() : string{
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isLocked() : bool{
        return $this->locked;
    }

    /**
     * @param bool $locked
     */
    public function setLocked(bool $locked = true) : void{
        $this->locked = $locked;
    }

    /**
     * @return UUID[]
     */
    public function getHelpers() : array{
        return $this->helpers;
    }

    /**
     * @param Session $session
     *
     * @return bool
     */
    public function isHelper(Player $player) : bool{
        return isset($this->helpers[$player->getUniqueId()->toString()]);
    }

    public function addHelper(Player $player) : void{
        $this->helpers[$player->getUniqueId()->toString()] = $player->getUniqueId();
    }

    /**
     * UUID of players who are allowed to work on the island.
     *
     * @param UUID[] $helpers
     */
    public function setHelpers(array $helpers) : void{
        foreach($helpers as $helper){
            $this->helpers[$helper->toString()] = $helper;
        }
    }

    public function removeHelper(Player $player) : void{
        $this->removeHelperByUUID($player->getUniqueId());
    }

    public function removeHelperByUUID(UUID $UUID) : void{
        unset($this->helpers[$UUID->toString()]);
    }

    /**
     * @param Player $player
     *
     * @return bool
     */
    public function canInteract(Player $player) : bool{
        return $this->isHelper($player) or $player->getUniqueId()->toString() === $this->ownerUUID->toString();
    }

    /**
     * @return int
     */
    public function getPoints() : int{
        return $this->points;
    }

    public function addPoints(int $points = 1) : void{
        $this->points += $points;
    }

    /**
     * @return Level
     */
    public function getLevel() : Level{
        return Server::getInstance()->getLevelByName($this->identifier);
    }

    public function save() : void{
        Skylands::getInstance()->getProvider()->saveIsle($this);
    }

    /**
     * @throws \ReflectionException
     */
    public function tryToClose() : void{
        //TODO: Determine if this is needed.
    }

}