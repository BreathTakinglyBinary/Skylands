<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace BreathTakinglyBinary\Skylands\event\isle;


class IsleDisbandEvent extends IsleEvent {

    /**
     * If this is true, then the world files and
     * configuration data will be removed for this
     * island.
     *
     * @var bool
     */
    public $removeData = true;
    
}