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
use BreathTakinglyBinary\Skylands\session\BaseSession;
use BreathTakinglyBinary\Skylands\session\Session;

class AcceptCommand extends IsleCommand {
    
    /**
     * AcceptCommand constructor.
     */
    public function __construct() {
        parent::__construct(["accept", "acc"], "ACCEPT_USAGE", "ACCEPT_DESCRIPTION");
    }
    
    /**
     * @param Session $session
     * @param array $args
     */
    public function onCommand(Session $session, array $args): void {
        if($session->hasIsle()) {
            $session->sendTranslatedMessage("NEED_TO_BE_FREE");
            return;
        } elseif(!isset($args[0]) and !$session->hasLastInvitation()) {
            $session->sendTranslatedMessage("ACCEPT_USAGE");
            return;
        }
        $isle = $session->getInvitation($invitation = $args[0] ?? $session->getLastInvitation());
        if($isle === null) {
            return;
        }
        $session->setLastInvitation(null);
        $session->removeInvitation($invitation);
        $session->setRank(BaseSession::RANK_DEFAULT);
        $session->setIsle($isle);
        $isle->broadcastTranslatedMessage("PLAYER_JOINED_THE_ISLE", [
            "name" => $session->getUsername()
        ]);
    }
    
}