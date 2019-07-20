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
    
    /** @var Skylands */
    private $plugin;
    
    /** @var Session[] */
    private $sessions = [];
    
    /**
     * SessionManager constructor.
     *
     * @param Skylands $plugin
     */
    public function __construct(Skylands $plugin) {
        $this->plugin = $plugin;
        $plugin->getServer()->getPluginManager()->registerEvents(new SessionListener($this), $plugin);
    }
    
    /**
     * @return Skylands
     */
    public function getPlugin() : Skylands {
        return $this->plugin;
    }
    
    /**
     * @return Session[]
     */
    public function getSessions(): array {
        return $this->sessions;
    }
    
    /**
     * @param string $username
     * @return null|OfflineSession
     */
    public function getOfflineSession(string $username): ?OfflineSession {
        return new OfflineSession($this, $username);
    }
    
    /**
     * @param Player $player
     * @return null|Session
     */
    public function getSession(Player $player): ?Session {
        return $this->sessions[$player->getName()] ?? null;
    }

    /**
     * @param Player $player
     * @throws \ReflectionException
     */
    public function openSession(Player $player): void {
        $this->sessions[$username = $player->getName()] = new Session($this, $player);
        (new SessionOpenEvent($this->sessions[$username]))->call();
    }

    /**
     * @param Player $player
     * @throws \ReflectionException
     */
    public function closeSession(Player $player): void {
        if(isset($this->sessions[$username = $player->getName()])) {
            $session = $this->sessions[$username];
            $session->save();
            (new SessionCloseEvent($session))->call();
            unset($this->sessions[$username]);
            if($session->hasIsle()) {
                $session->getIsle()->tryToClose();
            }
        }
    }
    
}