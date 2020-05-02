<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace BreathTakinglyBinary\Skylands\provider;


use BreathTakinglyBinary\Skylands\isle\Isle;
use BreathTakinglyBinary\Skylands\session\SkylandsSession;

abstract class Provider {

    public function __construct() {
        $this->initialize();
    }
    
    abstract public function initialize(): void;
    
    /**
     * @param SkylandsSession $session
     */
    abstract public function loadSession(SkylandsSession $session): void;
    
    /**
     * @param SkylandsSession $session
     */
    abstract public function saveSession(SkylandsSession $session): void;

    /**
     * @param string $identifier
     *
     * @return bool
     */
    abstract public function loadIsle(string $identifier): bool;
    
    /**
     * @param Isle $isle
     */
    abstract public function saveIsle(Isle $isle): void;

}
