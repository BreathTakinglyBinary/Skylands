<?php
declare(strict_types=1);

namespace BreathTakinglyBinary\Skylands\ui\forms;


use BreathTakinglyBinary\libDynamicForms\Form;
use BreathTakinglyBinary\libDynamicForms\SimpleForm;
use BreathTakinglyBinary\Skylands\locale\TranslationManager;
use pocketmine\Player;

class ManageInvitationsForm extends SimpleForm{

    public function __construct(Player $player, string $message = "", ?Form $previousForm = null){
        parent::__construct(TranslationManager::getTranslatedMessage("MENU_TITLE_MANAGE_INVITES"), $previousForm);
    }

    /**
     * @param Player $player
     * @param        $data
     */
    public function onResponse(Player $player, $data) : void{
        // TODO: Implement onResponse() method.
    }
}