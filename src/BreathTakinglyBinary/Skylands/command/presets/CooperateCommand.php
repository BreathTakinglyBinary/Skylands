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

class CooperateCommand extends IsleCommand {
    
    /** @var Skylands */
    private $plugin;
    
    /**
     * CooperateCommand constructor.
     * @param IsleCommandMap $map
     */
    public function __construct(IsleCommandMap $map) {
        $this->plugin = $map->getPlugin();
        parent::__construct(["cooperate"], "COOPERATE_USAGE", "COOPERATE_DESCRIPTION");
    }
    
    /**
     * @param Session $session
     * @param array $args
     */
    public function onCommand(Session $session, array $args): void {
        if($this->checkLeader($session)) {
            return;
        } elseif(!isset($args[0])) {
            $session->sendTranslatedMessage("COOPERATE_USAGE");
            return;
        }
        $player = $this->plugin->getServer()->getPlayer($args[0]);
        if($player === null) {
            $session->sendTranslatedMessage("NOT_ONLINE_PLAYER", [
                "name" => $args[0]
            ]);
            return;
        }
        $playerSession = $this->plugin->getSessionManager()->getSession($player);
        $playerName = $playerSession->getPlayer()->getName();
        $sessionName = $session->getPlayer()->getName();
        $isle = $session->getIsle();
        if($this->checkClone($session, $playerSession)) {
            return;
        } elseif($playerSession->getIsle() === $session->getIsle()) {
            $session->sendTranslatedMessage("ALREADY_ON_YOUR_ISLE", [
                "name" => $playerName
            ]);
        } elseif($isle->isCooperator($playerSession)) {
            $isle->removeCooperator($playerSession);
            $session->sendTranslatedMessage("REMOVED_A_COOPERATOR", [
                "name" => $playerName
            ]);
            $playerSession->sendTranslatedMessage("NOW_YOU_CANNOT_COOPERATE", [
                "name" => $sessionName
            ]);
        } else {
            $isle->addCooperator($playerSession);
            $session->sendTranslatedMessage("ADDED_A_COOPERATOR", [
                "name" => $playerName
            ]);
            $playerSession->sendTranslatedMessage("NOW_YOU_CAN_COOPERATE", [
                "name" => $sessionName
            ]);
        }
    }
    
}