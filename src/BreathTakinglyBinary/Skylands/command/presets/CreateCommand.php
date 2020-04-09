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

class CreateCommand extends IsleCommand {
    
    /**
     * CreateCommand constructor.
     * @param IsleCommandMap $map
     */
    public function __construct(IsleCommandMap $map) {
        parent::__construct(["create"], "CREATE_USAGE", "CREATE_DESCRIPTION");
    }

    /**
     * @param Session $session
     * @param array $args
     * @throws \ReflectionException
     */
    public function onCommand(Session $session, array $args): void {
        if($session->hasIsle()) {
            $session->sendTranslatedMessage("NEED_TO_BE_FREE");
            return;
        }
        if(!$session->canCreateIsland()){
            $session->sendTranslatedMessage("YOU_HAVE_TO_WAIT", [
                "minutes" => ceil($session->timeToNextIslandCreation()),
            ]);
            return;
        }
        $generator = $args[0] ?? "Shelly";
        if(Skylands::getInstance()->getGeneratorManager()->isGenerator($generator)) {
            Skylands::getInstance()->getIsleManager()->createIsleFor($session, $generator);
            $session->sendTranslatedMessage("SUCCESSFULLY_CREATED_A_ISLE");
        } else {
            $session->sendTranslatedMessage("NOT_VALID_GENERATOR", [
                "name" => $generator
            ]);
        }
    }
    
}