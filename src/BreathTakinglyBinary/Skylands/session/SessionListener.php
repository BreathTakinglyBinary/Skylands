<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace BreathTakinglyBinary\Skylands\session;


use pocketmine\event\Listener;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;

class SessionListener implements Listener {
    
    /** @var SessionManager */
    private $manager;
    
    /**
     * SessionListener constructor.
     * @param SessionManager $manager
     */
    public function __construct(SessionManager $manager) {
        $this->manager = $manager;
    }

    /**
     * @param PlayerLoginEvent $event
     * @throws \ReflectionException
     */
    public function onLogin(PlayerLoginEvent $event): void {
        $this->manager->openSession($event->getPlayer());
    }

    /**
     * @param PlayerQuitEvent $event
     * @throws \ReflectionException
     */
    public function onQuit(PlayerQuitEvent $event): void {
        $this->manager->closeSession($event->getPlayer());
    }
    
}