<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace BreathTakinglyBinary\Skylands\session;


use pocketmine\Player;
use BreathTakinglyBinary\Skylands\event\session\SessionCloseEvent;
use BreathTakinglyBinary\Skylands\event\session\SessionOpenEvent;
use BreathTakinglyBinary\Skylands\Skylands;

class SessionManager {

    /** @var SessionManager */
    private static $instance;
    
    /** @var SkylandsSession[] */
    private $sessions = [];

    public function __construct() {
        if(self::$instance instanceof SessionManager){
            throw new \RuntimeException("Only one instance of " . self::class . " allowed at one time!");
        }
        self::$instance = $this;
    }

    /**
     * @return SessionManager
     */
    public static function getInstance() : SessionManager{
        return self::$instance;
    }
    
    /**
     * @return SkylandsSession[]
     */
    public function getSessions(): array {
        return $this->sessions;
    }
    
    /**
     * @param Player $player
     * @return SkylandsSession
     */
    public function getSession(Player $player): SkylandsSession {
        if(!isset($this->sessions[$player->getName()])){
            $this->openSession($player);
        }

        return $this->sessions[$player->getName()] ?? null;
    }

    /**
     * @param Player $player
     */
    public function openSession(Player $player): void {
        $session = new SkylandsSession($player);
        $this->sessions[$player->getName()] = $session;
        (new SessionOpenEvent($session))->call();
        Skylands::getInstance()->getProvider()->loadSession($session);
    }

    /**
     * @param Player $player
     */
    public function closeSession(Player $player): void {
        if(isset($this->sessions[$username = $player->getName()])) {
            $session = $this->sessions[$username];
            $session->save();
            (new SessionCloseEvent($session))->call();
            unset($this->sessions[$username]);
        }
    }
    
}