<?php
declare(strict_types=1);

namespace BreathTakinglyBinary\Skylands\ui\forms;


use BreathTakinglyBinary\libDynamicForms\SimpleForm;
use BreathTakinglyBinary\Skylands\isle\Isle;
use BreathTakinglyBinary\Skylands\Skylands;
use pocketmine\Player;

class IslandOwnerSettingsMenu extends SimpleForm{

    public function __construct(Player $player){
        parent::__construct();
        $island = Skylands::getInstance()->getSessionManager()->getSession($player)->getIsle();
        if(!$island instanceof Isle){
            throw new \RuntimeException("IslandOwnerSettingsMenu being created for player with no island!");
        }
        $lockAction = $island->isLocked() ? "Unlock" : "Lock";

        $this->setTitle("Island Settings");

        $this->addButton($lockAction . " Island", "lock");
        $this->addButton("Invite a Player", "invite");
        $this->addButton("Remove a Player", "remove");
        $this->addButton("Reset Island", "reset");
    }

    public function onResponse(Player $player, $data) : void{
        $island = Skylands::getInstance()->getSessionManager()->getSession($player)->getIsle();
        if(!$island instanceof Isle){
            return;
        }
        switch($data){
            case 0:
                $lockAction = $island->isLocked() ? "Unlocked" : "Locked";
                $island->setLocked(!$island->isLocked());
                $player->sendMessage("Your island has been $lockAction!");
                break;
            case "invite":
                $player->sendForm(new InviteToIslandMenu());
                break;
            case "remove":
                // TODO:
                break;
            case "reset":
                $player->sendForm();
                break;
        }
    }
}