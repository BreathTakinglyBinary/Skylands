<?php
declare(strict_types=1);

namespace BreathTakinglyBinary\Skylands\ui\forms;


use BreathTakinglyBinary\libDynamicForms\Form;
use BreathTakinglyBinary\libDynamicForms\ModalForm;
use BreathTakinglyBinary\Skylands\isle\IsleHelperInvite;
use BreathTakinglyBinary\Skylands\isle\IsleManager;
use BreathTakinglyBinary\Skylands\locale\TranslationManager;
use pocketmine\Player;

class HandleSentInvitationForm extends ModalForm{

    /** @var IsleHelperInvite */
    private $invite;

    public function __construct(IsleHelperInvite $invite, ?Form $previousForm = null){
        $this->invite = $invite;
        parent::__construct(TranslationManager::getTranslatedMessage("FORM_TITLE_HANDLE_SENT_INVITE"), $previousForm);
        $this->setContent(TranslationManager::getTranslatedMessage("FORM_CONTENT_HANDLE_SENT_INVITE", ["invitee" => $invite->getInvitee()->getName()]));
        $this->setButton1(TranslationManager::getTranslatedMessage("FORM_BUTTON_TEXT_YES"));
        $this->setButton2(TranslationManager::getTranslatedMessage("FORM_BUTTON_TEXT_NO"));
    }

    public function onResponse(Player $player, $data) : void{
        if($data){
            IsleManager::getInstance()->getInviteManager()->removeInvitation($this->invite->getInvitee()->getName(), $player->getName());
            $player->sendMessage(TranslationManager::getTranslatedMessage("FORM_MESSAGE_INVITE_DELETED", ["invitee" => $this->invite->getInvitee()->getName()]));
        }
    }
}