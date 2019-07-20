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

class ChatCommand extends IsleCommand {
    
    /**
     * ChatCommand constructor.
     */
    public function __construct() {
        parent::__construct(["chat"], "CHAT_USAGE", "CHAT_DESCRIPTION");
    }
    
    /**
     * @param Session $session
     * @param array $args
     */
    public function onCommand(Session $session, array $args): void {
        if($this->checkIsle($session)) {
            return;
        }
        $session->setInChat(!$session->isInChat());
        $session->sendTranslatedMessage($session->isInChat() ? "JOINED_ISLE_CHAT" : "JOINED_GLOBAL_CHAT");
    }
    
}