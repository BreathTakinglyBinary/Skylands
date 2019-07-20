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
use BreathTakinglyBinary\Skylands\session\Session;

class JoinCommand extends IsleCommand {
    
    /**
     * JoinCommand constructor.
     */
    public function __construct() {
        parent::__construct(["join", "go", "spawn"], "JOIN_USAGE", "JOIN_DESCRIPTION");
    }
    
    /**
     * @param Session $session
     * @param array $args
     */
    public function onCommand(Session $session, array $args): void {
        if($this->checkIsle($session)) {
            return;
        }
        $session->getPlayer()->teleport($session->getIsle()->getLevel()->getSpawnLocation());
        $session->sendTranslatedMessage("TELEPORTED_TO_ISLE");
    }
    
}