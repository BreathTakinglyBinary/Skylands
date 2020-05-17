<?php
declare(strict_types=1);

namespace BreathTakinglyBinary\Skylands\ui\forms;


use BreathTakinglyBinary\libDynamicForms\Form;
use BreathTakinglyBinary\libDynamicForms\SimpleForm;
use BreathTakinglyBinary\Skylands\isle\IsleHelperInvite;
use BreathTakinglyBinary\Skylands\isle\IsleManager;
use BreathTakinglyBinary\Skylands\locale\TranslationManager;
use pocketmine\Player;

class ManageReceivedInvitationsForm extends SimpleForm{

    /** @var IsleHelperInvite[] */
    protected $invites = [];

    public function __construct(Player $isleOwner, ?Form $previousForm = null){
        parent::__construct(TranslationManager::getTranslatedMessage("FORM_TITLE_MANAGE_RECEIVED_INVITES"), $previousForm);
        $this->invites = IsleManager::getInstance()->getInviteManager()->getInvitationsByInvitee($isleOwner);
        if(count($this->invites) < 1){
            $this->setContent(TranslationManager::getTranslatedMessage("FORM_MESSAGE_NO_INVITES_RECEIVED"));
        }
        foreach($this->invites as $isleOwnerName => $invite){
            if(!$invite->isExpired()){
                $this->addButton($isleOwnerName, $isleOwnerName);
            }
        }
        $this->addButton(TranslationManager::getTranslatedMessage("FORM_BUTTON_TEXT_BACK"), "back");
    }

    /**
     * @param Player $player
     * @param        $data
     */
    public function onResponse(Player $player, $data) : void{
        if(!isset($this->invites[$data])){
            $form = $this->getPreviousForm();
            if($form === null){
                return;
            }
            if(($form instanceof SimpleForm) and $data !== "back"){
                $form->setContent(TranslationManager::getTranslatedMessage("FORM_MESSAGE_INVALID_INVITE"));
            }
            $player->sendForm($form);
            return;
        }
        $player->sendForm(new HandleReceivedInvitationForm($this->invites[$data]));
    }
}