<?php
declare(strict_types=1);

namespace BreathTakinglyBinary\Skylands\ui\forms;


use BreathTakinglyBinary\libDynamicForms\SimpleForm;
use BreathTakinglyBinary\Skylands\isle\Isle;
use BreathTakinglyBinary\Skylands\isle\IsleManager;
use BreathTakinglyBinary\Skylands\locale\TranslationManager;
use BreathTakinglyBinary\Skylands\session\SessionManager;
use BreathTakinglyBinary\Skylands\SkylandsSettings;
use pocketmine\Player;
use pocketmine\Server;
use BreathTakinglyBinary\Skylands\Skylands;

class InviteToIslandMenu extends SimpleForm{

    public function __construct(){
        parent::__construct();
        $this->setTitle("Invite Player to Island");

        foreach(Server::getInstance()->getOnlinePlayers() as $player){
                $this->addButton($player->getName(), $player->getName());
        }
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
        IsleManager::getInstance()->getInviteManager()->addInvitation($player, $helper);
        $player->sendMessage(TranslationManager::getTranslatedMessage("INVITATION_SENT"));
    }
}