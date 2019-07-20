<?php
declare(strict_types=1);

namespace BreathTakinglyBinary\Skylands\ui\forms\skyblock;


use BreathTakinglyBinary\Skylands\ui\forms\SimpleForm;
use pocketmine\Player;
use BreathTakinglyBinary\Skylands\Skylands;

class CreateIslandMenu extends SimpleForm{

    public function __construct(){
        parent::__construct();
        $this->setTitle("SkyBlock Menu");
        $skyBlock = Skylands::getInstance();
        foreach($skyBlock->getGeneratorManager()->getGenerators() as $generatorName => $generator){
            $this->addButton($generatorName, $generatorName);
        }
    }

    protected function onSubmit(Player $player, $data){
        $skyBlock = Skylands::getInstance();
        $session = $skyBlock->getSessionManager()->getSession($player);
        if(!$session->canCreateIsland()){
            $session->sendTranslatedMessage("YOU_HAVE_TO_WAIT", [
                "minutes" => ceil($session->timeToNextIslandCreation()),
            ]);
            return;
        }

        $skyBlock->getIsleManager()->createIsleFor($session, $data);
        $session->sendTranslatedMessage("SUCCESSFULLY_CREATED_A_ISLE");
    }

}