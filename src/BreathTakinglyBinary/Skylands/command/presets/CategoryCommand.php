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

class CategoryCommand extends IsleCommand {
    
    /**
     * CategoryCommand constructor.
     */
    public function __construct() {
        parent::__construct(["category", "c"], "CATEGORY_USAGE", "CATEGORY_DESCRIPTION");
    }
    
    /**
     * @param Session $session
     * @param array $args
     */
    public function onCommand(Session $session, array $args): void {
        if($this->checkIsle($session)) {
            return;
        }
        $session->sendTranslatedMessage("ISLE_CATEGORY", [
            "category" => $session->getIsle()->getCategory()
        ]);
    }
    
}