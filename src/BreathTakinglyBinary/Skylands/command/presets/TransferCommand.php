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
use BreathTakinglyBinary\Skylands\session\BaseSession;
use BreathTakinglyBinary\Skylands\session\Session;
use BreathTakinglyBinary\Skylands\Skylands;

class TransferCommand extends IsleCommand {

    public function __construct() {
        parent::__construct(["transfer", "makeleader"], "TRANSFER_USAGE", "TRANSFER_DESCRIPTION");
    }
    
    /**
     * @param Session $session
     * @param array $args
     */
    public function onCommand(Session $session, array $args): void {
        if($this->checkFounder($session)) {
            return;
        } elseif(!isset($args[0])) {
            $session->sendTranslatedMessage("TRANSFER_USAGE");
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
        } elseif($playerSession->getIsle() !== $session->getIsle()) {
            $session->sendTranslatedMessage("MUST_BE_PART_OF_YOUR_ISLE", [
                "name" => $playerSession->getUsername()
            ]);
            return;
        }
        $session->setRank(BaseSession::RANK_DEFAULT);
        $playerSession->setRank(BaseSession::RANK_FOUNDER);
        $session->sendTranslatedMessage("RANK_TRANSFERRED", [
            "name" => $playerSession->getUsername()
        ]);
        $playerSession->sendTranslatedMessage("GOT_RANK_TRANSFERRED", [
            "name" => $session->getUsername()
        ]);
    }
    
}