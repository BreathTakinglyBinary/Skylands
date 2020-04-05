<?php
declare(strict_types=1);

namespace BreathTakinglyBinary\Skylands\ui\forms\skyblock;


use BreathTakinglyBinary\libDynamicForms\SimpleForm;
use pocketmine\Player;
use pocketmine\Server;
use BreathTakinglyBinary\Skylands\Skylands;

class InviteToIslandMenu extends SimpleForm{

    public function __construct(){
        parent::__construct();
        $this->setTitle("Invite Player to Island");
        $sessionManager = Skylands::getInstance()->getSessionManager();

        foreach(Server::getInstance()->getOnlinePlayers() as $player){
            if(!$sessionManager->getSession($player)->hasIsle()){
                $this->addButton($player->getName(), $player->getName());
            }
        }
    }

    public function onResponse(Player $player, $data) : void{
        Server::getInstance()->dispatchCommand($player, "isle invite $data");
    }
}