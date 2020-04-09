<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace BreathTakinglyBinary\Skylands\isle;


use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use BreathTakinglyBinary\Skylands\session\OfflineSession;
use BreathTakinglyBinary\Skylands\session\Session;

class Isle {
    
    /** @var IsleManager */
    private $manager;
    
    /** @var string */
    private $identifier;
    
    /** @var OfflineSession[] */
    private $members = [];
    
    /** @var bool */
    private $locked = false;
    
    /** @var string */
    private $type = self::TYPE_BASIC;
    
    const TYPE_BASIC = "basic.isle";
    const TYPE_OP = "op.isle";
    
    /** @var Level */
    private $level;
    
    /** @var Session[] */
    private $cooperators = [];
    
    /**
     * Isle constructor.
     * @param IsleManager $manager
     * @param string $identifier
     * @param array $members
     * @param bool $locked
     * @param string $type
     * @param Level $level
     */
    public function __construct(IsleManager $manager, string $identifier, array $members, bool $locked, string $type, Level $level) {
        $this->manager = $manager;
        $this->identifier = $identifier;
        $this->locked = $locked;
        $this->type = $type;
        $this->level = $level;
    
        foreach($members as $member) {
            if($member instanceof OfflineSession) {
                $this->addMember($member);
            }
        }
    }
    
    /**
     * @return string
     */
    public function getIdentifier(): string {
        return $this->identifier;
    }
    
    /**
     * @return OfflineSession[]
     */
    public function getMembers(): array {
        return $this->members;
    }
    
    /**
     * @return Session[]
     */
    public function getPlayersOnline(): array {
        return $this->level->getPlayers();
    }
    
    /**
     * @return Session[]
     */
    public function getMembersOnline(): array {
        $sessions = [];
        foreach($this->members as $member) {
            $session = $member->getSession();
            if($session !== null) {
                $sessions[] = $session;
            }
        }
        return $sessions;
    }
    
    /**
     * @return bool
     */
    public function isLocked(): bool {
        return $this->locked;
    }
    
    /**
     * @return string
     */
    public function getType(): string {
        return $this->type;
    }
    
    /**
     * @return Level
     */
    public function getLevel(): Level {
        return $this->level;
    }
    
    /**
     * @return Position
     */
    public function getSpawnLocation(): Position {
        return $this->level->getSpawnLocation();
    }
    
    /**
     * @return int
     */
    public function getSlots(): int {
        // TODO: Make this number variable
        return 5;
    }
    
    /**
     * @return Session[]
     */
    public function getCooperators(): array {
        return $this->cooperators;
    }
    
    /**
     * @param Session $session
     * @return bool
     */
    public function isCooperator(Session $session): bool {
        return isset($this->cooperators[$session->getUsername()]);
    }
    
    /**
     * @param Session $session
     * @return bool
     */
    public function canInteract(Session $session): bool {
        return $session->getIsle() === $this or $this->isCooperator($session) or $session->getPlayer()->isOp();
    }
    
    /**
     * @param bool $locked
     */
    public function setLocked(bool $locked = true): void {
        $this->locked = $locked;
    }
    
    /**
     * @param OfflineSession[] $members
     */
    public function setMembers(array $members): void {
        $this->members = $members;
    }
    
    /**
     * @param Vector3 $position
     */
    public function setSpawnLocation(Vector3 $position): void {
        $this->level->setSpawnLocation($position);
    }
    
    /**
     * @param OfflineSession $session
     */
    public function addMember(OfflineSession $session): void {
        $this->members[strtolower($session->getUsername())] = $session;
    }
    
    /**
     * @param Session[] $cooperators
     */
    public function setCooperators(array $cooperators): void {
        $this->cooperators = $cooperators;
    }
    
    /**
     * @param Session $session
     */
    public function addCooperator(Session $session): void {
        $this->cooperators[$session->getUsername()] = $session;
    }
    
    /**
     * @param Session $session
     */
    public function removeCooperator(Session $session): void {
        if(isset($this->cooperators[$username = $session->getUsername()])) {
            unset($this->cooperators[$username]);
        }
    }
    
    /**
     * @param string $message
     */
    public function broadcastMessage(string $message): void {
        foreach($this->getMembersOnline() as $session) {
            $session->getPlayer()->sendMessage($message);
        }
    }
    
    /**
     * @param string $identifier
     * @param array $args
     */
    public function broadcastTranslatedMessage(string $identifier, array $args = []): void {
        foreach($this->getMembersOnline() as $session) {
            $session->sendTranslatedMessage($identifier, $args);
        }
    }
    
    /**
     * @param string $message
     */
    public function broadcastPopup(string $message): void {
        foreach($this->getMembersOnline() as $session) {
            $session->getPlayer()->sendPopup($message);
        }
    }
    
    /**
     * @param string $identifier
     * @param array $args
     */
    public function broadcastTranslatedPopup(string $identifier, array $args = []): void {
        foreach($this->getMembersOnline() as $session) {
            $session->sendTranslatedPopup($identifier, $args);
        }
    }
    
    /**
     * @param string $message
     */
    public function broadcastTip(string $message): void {
        foreach($this->getMembersOnline() as $session) {
            $session->getPlayer()->sendTip($message);
        }
    }
    
    /**
     * @param string $identifier
     * @param array $args
     */
    public function broadcastTranslatedTip(string $identifier, array $args = []): void {
        foreach($this->getMembersOnline() as $session) {
            $session->sendTranslatedTip($identifier, $args);
        }
    }
    
    public function save(): void {
        $this->manager->getPlugin()->getProvider()->saveIsle($this);
    }
    
    public function updateMembers(): void {
        foreach($this->getMembersOnline() as $member) {
            if($member->getIsle() !== $this) {
                unset($this->members[$member->getUsername()]);
            }
        }
    }

    /**
     * @throws \ReflectionException
     */
    public function tryToClose(): void {
        $this->updateMembers();
        if(empty($this->getPlayersOnline()) and empty($this->getMembersOnline())) {
            $this->manager->closeIsle($this);
        }
    }
    
}