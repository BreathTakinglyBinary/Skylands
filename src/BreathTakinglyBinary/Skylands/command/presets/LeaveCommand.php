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

class LeaveCommand extends IsleCommand {
    
    public function __construct() {
        parent::__construct(["leave"], "LEAVE_USAGE", "LEAVE_DESCRIPTION");
    }
    
    /**
     * @param Session $session
     * @param array $args
     */
    public function onCommand(Session $session, array $args): void {
        if($this->checkIsle($session)) {
            return;
        } elseif($session->getRank() === BaseSession::RANK_FOUNDER) {
            $session->sendTranslatedMessage("FOUNDER_CANNOT_LEAVE");
            return;
        }
        $session->setRank(BaseSession::RANK_DEFAULT);
        $session->setIsle(null);
        $session->setInChat(false);
        $session->sendTranslatedMessage("LEFT_ISLE");
    }
    
}