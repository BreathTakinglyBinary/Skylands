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
use BreathTakinglyBinary\Skylands\session\BaseSession;
use BreathTakinglyBinary\Skylands\Skylands;

abstract class Provider {
    
    /** @var Skylands */
    protected $plugin;
    
    /**
     * Provider constructor.
     *
     * @param Skylands $plugin
     */
    public function __construct(Skylands $plugin) {
        $this->plugin = $plugin;
        $this->initialize();
    }
    
    public abstract function initialize(): void;
    
    /**
     * @param BaseSession $session
     */
    public abstract function loadSession(BaseSession $session): void;
    
    /**
     * @param BaseSession $session
     */
    public abstract function saveSession(BaseSession $session): void;

    /**
     * @param string $identifier
     *
     * @return bool
     */
    public abstract function loadIsle(string $identifier): bool;
    
    /**
     * @param Isle $isle
     */
    public abstract function saveIsle(Isle $isle): void;

    /**
     * @param string $isleId
     */
    public abstract function deleteIsleData(string $isleId): void;
    
}
