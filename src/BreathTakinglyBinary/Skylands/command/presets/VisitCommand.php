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
use BreathTakinglyBinary\Skylands\session\Session;
use BreathTakinglyBinary\Skylands\Skylands;

class VisitCommand extends IsleCommand {
    
    /**
     * VisitCommand constructor.
     * @param IsleCommandMap $map
     */
    public function __construct(IsleCommandMap $map) {
        parent::__construct(["visit", "teleport", "tp"], "VISIT_USAGE", "VISIT_DESCRIPTION");
    }

    /**
     * @param Session $session
     * @param array $args
     * @throws \ReflectionException
     */
    public function onCommand(Session $session, array $args): void {
        if(!isset($args[0])) {
            $session->sendTranslatedMessage("VISIT_USAGE");
            return;
        }
        $offline = Skylands::getInstance()->getSessionManager()->getOfflineSession($args[0]);
        $isleId = $offline->getIsleId();
        if($isleId === null) {
            $session->sendTranslatedMessage("HE_DO_NOT_HAVE_AN_ISLE", [
                "name" => $args[0]
            ]);
            return;
        }
        Skylands::getInstance()->getProvider()->loadIsle($isleId);
        $isle = Skylands::getInstance()->getIsleManager()->getIsle($isleId);
        if($isle->isLocked() and !($session->getPlayer()->isOp())) {
            $session->sendTranslatedMessage("HIS_ISLE_IS_LOCKED", [
                "name" => $args[0]
            ]);
            $isle->tryToClose();
            return;
        }
        $session->getPlayer()->teleport($isle->getLevel()->getSpawnLocation());
        $session->sendTranslatedMessage("VISITING_ISLE", [
            "name" => $args[0]
        ]);
    }
    
}