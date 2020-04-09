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

class DemoteCommand extends IsleCommand {

    public function __construct() {
        parent::__construct(["demote"], "DEMOTE_USAGE", "DEMOTE_DESCRIPTION");
    }
    
    /**
     * @param Session $session
     * @param array $args
     */
    public function onCommand(Session $session, array $args): void {
        if($this->checkLeader($session)) {
            return;
        } elseif(!isset($args[0])) {
            $session->sendTranslatedMessage("DEMOTE_USAGE");
            return;
        }
        
        $offlineSession = Skylands::getInstance()->getSessionManager()->getOfflineSession($args[0]);
        if($this->checkClone($session, $offlineSession->getSession())) {
            return;
        } elseif($offlineSession->getIsleId() !== $session->getIsleId()) {
            $session->sendTranslatedMessage("MUST_BE_PART_OF_YOUR_ISLE", [
                "name" => $args[0]
            ]);
        } else {
            $rank = null;
            $rankName = "";
            switch($offlineSession->getRank()) {
                case Session::RANK_OFFICER:
                    $rank = Session::RANK_DEFAULT;
                    $rankName = "MEMBER";
                    break;
                case Session::RANK_LEADER:
                    $rank = Session::RANK_OFFICER;
                    $rankName = "OFFICER";
                    break;
                case Session::RANK_FOUNDER:
                    $rank = false;
            }
            if($rank === null) {
                $session->sendTranslatedMessage("CANNOT_DEMOTE_MEMBER", [
                    "name" => $args[0]
                ]);
                return;
            } elseif($rank === false) {
                $session->sendTranslatedMessage("CANNOT_DEMOTE_FOUNDER");
                return;
            }
            $onlineSession = $offlineSession->getSession();
            if($onlineSession !== null) {
                $onlineSession->setRank($rank);
                $onlineSession->sendTranslatedMessage("YOU_HAVE_BEEN_DEMOTED");
                $onlineSession->save();
            } else {
                $offlineSession->setRank($rank);
                $offlineSession->save();
            }
            $session->sendTranslatedMessage("SUCCESSFULLY_DEMOTED_PLAYER", [
                "name" => $args[0],
                "to" => $session->translate($rankName)
            ]);
        }
        
    }
    
}