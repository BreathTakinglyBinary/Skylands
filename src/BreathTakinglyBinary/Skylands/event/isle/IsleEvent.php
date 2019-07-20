<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace BreathTakinglyBinary\Skylands\event\isle;


use BreathTakinglyBinary\Skylands\event\SkyBlockEvent;
use BreathTakinglyBinary\Skylands\isle\Isle;

abstract class IsleEvent extends SkyBlockEvent {
    
    /** @var Isle */
    private $isle;
    
    /**
     * IsleEvent constructor.
     * @param Isle $isle
     */
    public function __construct(Isle $isle) {
        $this->isle = $isle;
    }
    
    /**
     * @return Isle
     */
    public function getIsle(): Isle {
        return $this->isle;
    }
    
}