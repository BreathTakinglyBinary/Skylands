<?php
declare(strict_types=1);

namespace BreathTakinglyBinary\Skylands\ui\forms\skyblock;


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

        $this->addButton($lockAction . " Island");
        $this->addButton("Invite a Player");
        $this->addButton("Promote a Member");
        $this->addButton("Demote a Member");
        $this->addButton("Remove a Member");
        $this->addButton("Disband Island");
    }

    public function onResponse(Player $player, $data) : void{
        $island = Skylands::getInstance()->getSessionManager()->getSession($player)->getIsle();
        if(!$island instanceof Isle){
            return;
        }
        switch($data){
            case 0:
                $island->setLocked(!$island->isLocked());
                $lockAction = $island->isLocked() ? "Locked" : "Unlocked";
                $player->sendMessage("Your island has been $lockAction!");
                break;
            case 1:
                $player->sendForm(new InviteToIslandMenu());
                break;
            case 2:
                $player->sendForm(new SelectMemberToManageMenu($player, SelectMemberToManageMenu::ACTION_PROMOTE));
                break;
            case 3:
                $player->sendForm(new SelectMemberToManageMenu($player, SelectMemberToManageMenu::ACTION_DEMOTE));
                break;
            case 4:
                $player->sendForm(new SelectMemberToManageMenu($player, SelectMemberToManageMenu::ACTION_FIRE));
                break;
            case 5:
                $player->sendForm(new ConfirmDisbandIslandMenu());
                break;
        }
    }
}
