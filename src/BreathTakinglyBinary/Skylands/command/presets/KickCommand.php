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

class KickCommand extends IsleCommand {

    public function __construct() {
        parent::__construct(["kick"], "KICK_USAGE", "KICK_DESCRIPTION");
    }
    
    /**
     * @param Session $session
     * @param array $args
     */
    public function onCommand(Session $session, array $args): void {
        if($this->checkOfficer($session)) {
            return;
        } elseif(!isset($args[0])) {
            $session->sendTranslatedMessage("KICK_USAGE");
            return;
        }
        $server = Skylands::getInstance()->getServer();
        $player = $server->getPlayer($args[0]);
        if($player === null) {
            $session->sendTranslatedMessage("NOT_ONLINE_PLAYER", [
                "name" => $args[0]
            ]);
            return;
        }
        $playerSession = Skylands::getInstance()->getSessionManager()->getSession($player);
        if($this->checkClone($session, $playerSession)) {
            return;
        } elseif($playerSession->getIsle() === $session->getIsle()) {
            $session->sendTranslatedMessage("CANNOT_KICK_A_MEMBER");
        } elseif(in_array($player, $session->getIsle()->getPlayersOnline())) {
            $player->teleport($server->getDefaultLevel()->getSpawnLocation());
            $playerSession->sendTranslatedMessage("KICKED_FROM_THE_ISLE");
            $session->sendTranslatedMessage("YOU_KICKED_A_PLAYER", [
                "name" => $playerSession->getUsername()
            ]);
        } else {
            $session->sendTranslatedMessage("NOT_A_VISITOR", [
                "name" => $playerSession->getUsername()
            ]);
        }
    }
    
}