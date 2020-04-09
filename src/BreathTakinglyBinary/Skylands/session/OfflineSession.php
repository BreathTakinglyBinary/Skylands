<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace BreathTakinglyBinary\Skylands\session;


use BreathTakinglyBinary\Skylands\Skylands;

class OfflineSession extends BaseSession {
    
    /**
     * @return null|Session
     */
    public function getSession(): ?Session {
        $player = Skylands::getInstance()->getServer()->getPlayerExact($this->username);
        if($player !== null) {
            return $this->manager->getSession($player);
        }
        return null;
    }
    
}