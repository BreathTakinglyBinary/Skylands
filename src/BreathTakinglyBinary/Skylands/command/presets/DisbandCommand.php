<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace BreathTakinglyBinary\Skylands\command\presets;


use BreathTakinglyBinary\Skylands\command\IsleCommand;
use BreathTakinglyBinary\Skylands\command\IsleCommandMap;
use BreathTakinglyBinary\Skylands\isle\IsleManager;
use BreathTakinglyBinary\Skylands\session\Session;

class DisbandCommand extends IsleCommand {
    
    /** @var IsleManager */
    private $isleManager;
    
    /**
     * DisbandCommand constructor.
     * @param IsleCommandMap $map
     */
    public function __construct(IsleCommandMap $map) {
        $this->isleManager = $map->getPlugin()->getIsleManager();
        parent::__construct(["disband"], "DISBAND_USAGE", "DISBAND_DESCRIPTION");
    }

    /**
     * @param Session $session
     * @param array $args
     * @throws \ReflectionException
     */
    public function onCommand(Session $session, array $args): void {
        if($this->checkFounder($session)) {
            return;
        }
        $this->isleManager->disbandIsle($session->getIsle());
    }
    
}
