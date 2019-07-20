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

class HelpCommand extends IsleCommand {
    
    /** @var IsleCommandMap */
    private $map;
    
    /**
     * HelpCommand constructor.
     * @param IsleCommandMap $map
     */
    public function __construct(IsleCommandMap $map) {
        $this->map = $map;
        parent::__construct(["help", "?"], "HELP_USAGE", "HELP_DESCRIPTION");
    }
    
    /**
     * @param Session $session
     * @param array $args
     */
    public function onCommand(Session $session, array $args): void {
        $session->sendTranslatedMessage("HELP_HEADER", ["amount" => count($this->map->getCommands())]);
        foreach($this->map->getCommands() as $command) {
            $session->sendTranslatedMessage("HELP_COMMAND_TEMPLATE", [
                "name" => $command->getName(),
                "description" => $session->translate($command->getDescriptionMessageId()),
                "usage" => $session->translate($command->getUsageMessageId())
            ]);
        }
    }
    
}