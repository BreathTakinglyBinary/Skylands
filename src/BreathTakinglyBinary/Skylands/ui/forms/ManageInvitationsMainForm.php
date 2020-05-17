<?php
declare(strict_types=1);

namespace BreathTakinglyBinary\Skylands\ui\forms;


use BreathTakinglyBinary\libDynamicForms\Form;
use BreathTakinglyBinary\libDynamicForms\SimpleForm;
use BreathTakinglyBinary\Skylands\locale\TranslationManager;
use pocketmine\Player;

class ManageInvitationsMainForm extends SimpleForm{

    public function __construct(?Form $previousForm = null){
        parent::__construct(TranslationManager::getTranslatedMessage("FORM_TITLE_MANAGE_INVITES"), $previousForm);
        $this->addButton(TranslationManager::getTranslatedMessage("FORM_BUTTON_TEXT_INVITES_RECEIVED"), "received");
        $this->addButton(TranslationManager::getTranslatedMessage("FORM_BUTTON_TEXT_INVITES_SENT"),"sent");
        $this->addButton(TranslationManager::getTranslatedMessage("FORM_BUTTON_TEXT_BACK"), "back");
    }

    /**
     * @param Player $player
     * @param        $data
     */
    public function onResponse(Player $player, $data) : void{
        switch($data){
            case "received":
                $player->sendForm(new ManageReceivedInvitationsForm($player, $this));
                break;
            case "sent":
                $player->sendForm(new ManageSentInvitationsForm($player, $this));
                break;
            case "back":
                if(($previousForm = $this->getPreviousForm()) !== null){
                    $player->sendForm($previousForm);
                }
                break;
        }
    }
}