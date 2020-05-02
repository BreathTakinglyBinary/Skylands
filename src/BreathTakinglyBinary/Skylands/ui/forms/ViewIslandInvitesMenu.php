<?php
declare(strict_types=1);

namespace BreathTakinglyBinary\Skylands\ui\forms;


use BreathTakinglyBinary\libDynamicForms\Form;
use BreathTakinglyBinary\libDynamicForms\ModalForm;
use BreathTakinglyBinary\libDynamicForms\SimpleForm;
use BreathTakinglyBinary\Skylands\isle\Isle;
use BreathTakinglyBinary\Skylands\session\SessionManager;
use BreathTakinglyBinary\Skylands\session\SkylandsSession;
use pocketmine\Player;

class ViewIslandInvitesMenu extends SimpleForm{

    /** @var Isle[]|null */
    private $invites;

    public function __construct(SkylandsSession $session, Form $previousForm){
        parent::__construct("Island Invitations", $previousForm);
        $this->invites = $session->getInvitations();
        foreach($this->invites as $sender => $invitation){
            $this->addButton($sender, $sender);
        }
        $this->addButton("Back", "back");
    }

    public function onResponse(Player $player, $data) : void{
        if($data === "back"){
            $player->sendForm($this->getPreviousForm());
            return;
        }
        $session = SessionManager::getInstance()->getSession($player);
        if(isset($this->invites[$data])){
            $isle = $this->invites[$data];
            $session->setLastInvitation(null);
            $session->removeInvitation($data);
            $session->setIsleId($isle->getIdentifier());
        }
        $form = $this->getPreviousForm();
        if($form instanceof SimpleForm or $form instanceof ModalForm){
            $form->setContent("Invite from $data has expired.");
        }
        $player->sendForm($form);
    }
}