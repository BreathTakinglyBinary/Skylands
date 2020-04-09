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

class InviteCommand extends IsleCommand {

    public function __construct() {
        parent::__construct(["invite", "inv"], "INVITE_USAGE", "INVITE_DESCRIPTION");
    }
    
    /**
     * @param Session $session
     * @param array $args
     */
    public function onCommand(Session $session, array $args): void {
        if($this->checkOfficer($session)) {
            return;
        } elseif(!isset($args[0])) {
            $session->sendTranslatedMessage("INVITE_USAGE");
            return;
        } elseif(count($session->getIsle()->getMembers()) >= $session->getIsle()->getSlots()) {
            $session->sendTranslatedMessage("ISLE_IS_FULL");
            return;
        }
        $player = Skylands::getInstance()->getServer()->getPlayer($args[0]);
        if($player === null) {
            $session->sendTranslatedMessage("NOT_ONLINE_PLAYER", [
                "name" => $args[0]
            ]);
            return;
        }
        $playerSession = Skylands::getInstance()->getSessionManager()->getSession($player);
        if($this->checkClone($session, $playerSession)) {
            return;
        } elseif($playerSession->hasIsle()) {
            $session->sendTranslatedMessage("CANNOT_INVITE_BECAUSE_HAS_ISLE", [
                "name" => $player->getName()
            ]);
            return;
        }
        $playerSession->addInvitation($session->getUsername(), $session->getIsle());
        $playerSession->sendTranslatedMessage("YOU_WERE_INVITED_TO_AN_ISLE", [
            "name" => $session->getUsername()
        ]);
        $session->sendTranslatedMessage("SUCCESSFULLY_INVITED", [
            "name" => $player->getName()
        ]);
    }
    
}