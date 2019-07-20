<?php
declare(strict_types=1);

namespace BreathTakinglyBinary\Skylands\ui\forms\skyblock;


use BreathTakinglyBinary\Skylands\ui\forms\SimpleForm;
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

    protected function onSubmit(Player $player, $data){
        Server::getInstance()->dispatchCommand($player, "isle invite $data");
    }

}