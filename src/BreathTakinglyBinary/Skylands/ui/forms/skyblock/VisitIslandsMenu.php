<?php
declare(strict_types=1);

namespace BreathTakinglyBinary\Skylands\ui\forms\skyblock;


use BreathTakinglyBinary\libDynamicForms\SimpleForm;
use BreathTakinglyBinary\Skylands\isle\Isle;
use BreathTakinglyBinary\Skylands\Skylands;
use pocketmine\Player;

class VisitIslandsMenu extends SimpleForm{

    /** @var Isle[] */
    private $isles = [];

    public function __construct(?Player $player = null){
        parent::__construct();
        $skyBlock = Skylands::getInstance();
        $playerIsleId = null;
        if($player instanceof Player){
            $playerIsleId = $skyBlock->getSessionManager()->getSession($player)->getIsle()->getIdentifier();
        }
        foreach($skyBlock->getIsleManager()->getIsles() as $isle){
            if(!$isle->isLocked() and $isle->getIdentifier() !== $playerIsleId){
                $this->addButton($isle->getIdentifier(), $isle->getIdentifier());
                $this->isles[$isle->getIdentifier()] = $isle;
            }
        }
        $this->addButton("Back");
    }

    public function onResponse(Player $player, $data) : void{
        if(isset($this->isles[$data])){
            $player->teleport($this->isles[$data]->getSpawnLocation());
            return;
        }
        $msg = $data === "Back" ? null : "Island $data, is not available.";
        $player->sendForm(new SkyBlockMainMenu($msg));
    }
}