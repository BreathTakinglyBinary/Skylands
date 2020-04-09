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

class FireCommand extends IsleCommand {
    
    /**
     * FireCommand constructor.
     * @param IsleCommandMap $map
     */
    public function __construct(IsleCommandMap $map) {
        parent::__construct(["fire"], "FIRE_USAGE", "FIRE_DESCRIPTION");
    }
    
    /**
     * @param Session $session
     * @param array $args
     */
    public function onCommand(Session $session, array $args): void {
        if($this->checkLeader($session)) {
            return;
        } elseif(!isset($args[0])) {
            $session->sendTranslatedMessage("FIRE_USAGE");
            return;
        }
        $offlineSession = Skylands::getInstance()->getSessionManager()->getOfflineSession($args[0]);
        if($this->checkClone($session, $offlineSession->getSession())) {
            return;
        } elseif($offlineSession->getIsleId() !== $session->getIsleId()) {
            $session->sendTranslatedMessage("MUST_BE_PART_OF_YOUR_ISLE", [
                "name" => $args[0]
            ]);
        } elseif($offlineSession->getRank() === Session::RANK_FOUNDER) {
            $session->sendTranslatedMessage("CANNOT_FIRE_FOUNDER");
        } else {
            $onlineSession = $offlineSession->getSession();
            if($onlineSession !== null) {
                if($onlineSession->getIsle()->getLevel() === $onlineSession->getPlayer()->getLevel()) {
                    $onlineSession->getPlayer()->teleport(Skylands::getInstance()->getServer()->getDefaultLevel()->getSpawnLocation());
                }
                $onlineSession->setRank(Session::RANK_DEFAULT);
                $onlineSession->setIsle(null);
                $onlineSession->sendTranslatedMessage("YOU_HAVE_BEEN_FIRED");
                $onlineSession->save();
            } else {
                $offlineSession->setIsleId(null);
                $offlineSession->setRank(Session::RANK_DEFAULT);
                $offlineSession->save();
            }
            $session->sendTranslatedMessage("SUCCESSFULLY_FIRED", [
                "name" => $args[0]
            ]);
        }
    }
    
}