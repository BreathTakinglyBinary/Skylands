<?php
declare(strict_types=1);

namespace BreathTakinglyBinary\Skylands\ui\forms;


use BreathTakinglyBinary\libDynamicForms\Form;
use BreathTakinglyBinary\libDynamicForms\ModalForm;
use BreathTakinglyBinary\Skylands\isle\IsleHelperInvite;
use BreathTakinglyBinary\Skylands\locale\TranslationManager;
use pocketmine\Player;

class HandleReceivedInvitationForm extends ModalForm{

    /** @var IsleHelperInvite */
    private $invite;

    public function __construct(IsleHelperInvite $invite, ?Form $previousForm = null){
        $this->invite = $invite;
        parent::__construct(TranslationManager::getTranslatedMessage("FORM_TITLE_HANDLE_RECEIVED_INVITE"), $previousForm);
        $this->setContent(TranslationManager::getTranslatedMessage("FORM_CONTENT_HANDLE_RECEIVED_INVITE", ["owner" => $invite->getIsleOwnerName()]));
        $this->setButton1(TranslationManager::getTranslatedMessage("FORM_BUTTON_TEXT_ACCEPT"));
        $this->setButton2(TranslationManager::getTranslatedMessage("FORM_BUTTON_TEXT_DENY"));
    }

    public function onResponse(Player $player, $data) : void{
        $data ? $this->invite->accept() : $this->invite->deny();
    }

}