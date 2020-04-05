<?php
declare(strict_types=1);

namespace BreathTakinglyBinary\Skylands\ui\forms\skyblock;


use BreathTakinglyBinary\libDynamicForms\SimpleForm;
use BreathTakinglyBinary\Skylands\utils\SkylandsUtils;
use pocketmine\Player;
use BreathTakinglyBinary\Skylands\Skylands;

class SelectMemberToManageMenu extends SimpleForm implements MemeberManagementActions{

    /** @var int */
    private $action;

    public function __construct(Player $islandOfficer, int $action){
        parent::__construct();
        $this->action = $action;
        $session = Skylands::getInstance()->getSessionManager()->getSession($islandOfficer);
        $actionWord = SkylandsUtils::getMemberManagementActionWord($action);

        $this->setTitle($actionWord . " Island Member");

        foreach($session->getIsle()->getMembers() as $member){
            $this->addButton($member->getUsername(), $member->getUsername());
        }
        foreach($session->getIsle()->getCooperators() as $cooperator){
            $this->addButton($cooperator->getUsername(), $cooperator->getUsername());
        }

    }

    public function onResponse(Player $player, $data) : void{
        $player->sendForm(new ConfirmMemberManagementMenu($this->action, $data));
    }
}