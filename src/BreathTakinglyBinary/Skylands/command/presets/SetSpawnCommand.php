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

class SetSpawnCommand extends IsleCommand {
    
    /**
     * SetSpawnCommand constructor.
     */
    public function __construct() {
        parent::__construct(["setspawn"], "SET_SPAWN_USAGE", "SET_SPAWN_DESCRIPTION");
    }
    
    /**
     * @param Session $session
     * @param array $args
     */
    public function onCommand(Session $session, array $args): void {
        if($this->checkOfficer($session)) {
            return;
        } elseif($session->getPlayer()->getLevel() !== $session->getIsle()->getLevel()) {
            $session->sendTranslatedMessage("MUST_BE_IN_YOUR_ISLE");
        } else {
            $session->getIsle()->setSpawnLocation($session->getPlayer());
            $session->sendTranslatedMessage("SUCCESSFULLY_SET_SPAWN");
        }
    }
    
}