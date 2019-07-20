<?php
declare(strict_types=1);

namespace BreathTakinglyBinary\Skylands\ui\forms\skyblock;


use BreathTakinglyBinary\Skylands\ui\forms\SimpleForm;
use BreathTakinglyBinary\DynamicCore\util\Utils;
use pocketmine\Player;
use BreathTakinglyBinary\Skylands\Skylands;

class SelectMemberToManageMenu extends SimpleForm implements MemeberManagementActions{

    /** @var int */
    private $action;

    public function __construct(Player $islandOfficer, int $action){
        parent::__construct();
        $this->action = $action;
        $session = Skylands::getInstance()->getSessionManager()->getSession($islandOfficer);
        $actionWord = Utils::getMemberManagementActionWord($action);

        $this->setTitle($actionWord . " Island Member");

        foreach($session->getIsle()->getMembers() as $member){
            $this->addButton($member->getUsername(), $member->getUsername());
        }
        foreach($session->getIsle()->getCooperators() as $cooperator){
            $this->addButton($cooperator->getUsername(), $cooperator->getUsername());
        }

    }

    protected function onSubmit(Player $player, $data){
        $player->sendForm(new ConfirmMemberManagementMenu($this->action, $data));
    }

}