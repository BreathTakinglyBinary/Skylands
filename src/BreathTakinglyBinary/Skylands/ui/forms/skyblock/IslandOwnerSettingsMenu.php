<?php
declare(strict_types=1);

namespace BreathTakinglyBinary\Skylands\ui\forms\skyblock;


use BreathTakinglyBinary\Skylands\ui\forms\SimpleForm;
use pocketmine\Player;
use BreathTakinglyBinary\Skylands\Skylands;

class IslandOwnerSettingsMenu extends SimpleForm{

    public function __construct(){
        parent::__construct();
        $this->setTitle("Island Settings");

        $this->addButton("Lock Island");
        $this->addButton("Invite a Player");
        $this->addButton("Promote a Member");
        $this->addButton("Demote a Member");
        $this->addButton("Remove a Member");
        $this->addButton("Disband Island");
    }

    protected function onSubmit(Player $player, $data){
        $skyBlock = Skylands::getInstance();
        switch($data){
            case 0:
                $skyBlock->getSessionManager()->getSession($player)->getIsle()->setLocked();
                $player->sendMessage("Your island has been locked!");
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