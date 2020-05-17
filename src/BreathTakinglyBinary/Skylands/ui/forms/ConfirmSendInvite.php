<?php
declare(strict_types=1);

namespace BreathTakinglyBinary\Skylands\ui\forms;


use BreathTakinglyBinary\libDynamicForms\Form;
use BreathTakinglyBinary\libDynamicForms\ModalForm;
use BreathTakinglyBinary\Skylands\isle\IsleManager;
use BreathTakinglyBinary\Skylands\locale\TranslationManager;
use pocketmine\Player;
use pocketmine\Server;

class ConfirmSendInvite extends ModalForm{

    /** @var string */
    private $inviteeName;

    public function __construct(string $inviteeName, ?Form $previousForm = null){
        $this->inviteeName = $inviteeName;
        $title = TranslationManager::getTranslatedMessage("FORM_TITLE_CONFIRM_SEND_INVITE", ["invitee" => $inviteeName]);
        parent::__construct($title, $previousForm);
        $this->setContent(TranslationManager::getTranslatedMessage("FORM_CONTENT_CONFIRM_SEND_INVITE", ["invitee" => $inviteeName]));
        $this->setButton1(TranslationManager::getTranslatedMessage("FORM_BUTTON_TEXT_YES"));
        $this->setButton2(TranslationManager::getTranslatedMessage("FORM_BUTTON_TEXT_NO"));
    }

    /**
     * @param Player $player
     * @param        $data
     */
    public function onResponse(Player $player, $data) : void{
        if($data){
            $invitee = Server::getInstance()->getPlayer($this->inviteeName);
            if(!$invitee instanceof Player){
                $player->sendMessage(TranslationManager::getTranslatedMessage("FORM_MESSAGE_INVITEE_NOT_ONLINE", ["invitee" => $this->inviteeName]));
                return;
            }
            IsleManager::getInstance()->getInviteManager()->addInvitation($player, $invitee);
            $player->sendMessage(TranslationManager::getTranslatedMessage("PLAYER_MESSAGE_INVITATION_SENT", ["invitee" => $invitee->getName()]));
            $invitee->sendMessage(TranslationManager::getTranslatedMessage("PLAYER_MESSAGE_NEW_INVITE_RECEIVED", ["owner" => $player->getName()]));
        }
    }
}