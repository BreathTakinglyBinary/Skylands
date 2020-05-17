<?php
declare(strict_types=1);

namespace BreathTakinglyBinary\Skylands\ui\forms;


use BreathTakinglyBinary\libDynamicForms\Form;
use BreathTakinglyBinary\libDynamicForms\SimpleForm;
use BreathTakinglyBinary\Skylands\isle\Isle;
use BreathTakinglyBinary\Skylands\isle\IsleManager;
use BreathTakinglyBinary\Skylands\locale\TranslationManager;
use BreathTakinglyBinary\Skylands\session\SessionManager;
use pocketmine\Player;
use pocketmine\Server;

class InviteToIslandMenu extends SimpleForm{

    public function __construct(Player $player, ?Form $previousForm = null){
        parent::__construct(TranslationManager::getTranslatedMessage("FORM_TITLE_INVITE_TO_ISLE"), $previousForm);

        foreach(Server::getInstance()->getOnlinePlayers() as $onlinePlayer){
            if($onlinePlayer !== $player){
                $this->addButton($onlinePlayer->getDisplayName(), $onlinePlayer->getName());
            }
        }
        $this->addButton(TranslationManager::getTranslatedMessage("FORM_BUTTON_TEXT_BACK"));
    }

    public function onResponse(Player $player, $data) : void{
        $session = SessionManager::getInstance()->getSession($player);
        $isle = $session->getIsle();
        if(!$isle instanceof Isle){
            //something went totally wrong! :D
            $player->sendForm(new SkylandsMainMenu($player, "An error occured.  Try Again."));
            return;
        }
        $helper = Server::getInstance()->getPlayer($data);
        if(!$helper instanceof Player){
            $player->sendForm(new SkylandsMainMenu($player, TranslationManager::getTranslatedMessage("MENU_MESSAGE_MUST_BE_ONLINE_TO_INVITE", ["name" => $data])));
            return;
        }
        $player->sendForm(new ConfirmSendInvite($helper->getName(), $this));
    }
}