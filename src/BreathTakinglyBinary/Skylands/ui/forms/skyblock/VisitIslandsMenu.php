<?php
declare(strict_types=1);

namespace BreathTakinglyBinary\Skylands\ui\forms\skyblock;


use BreathTakinglyBinary\Skylands\isle\Isle;
use BreathTakinglyBinary\Skylands\ui\forms\SimpleForm;
use pocketmine\Player;
use BreathTakinglyBinary\Skylands\Skylands;

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

    protected function onSubmit(Player $player, $data){
        if(isset($this->isles[$data])){
            $player->teleport($this->isles[$data]->getSpawnLocation());
            return;
        }
        $msg = $data === "Back" ? null : "Island $data, is not available.";
        $player->sendForm(new SkyBlockMainMenu($msg));
    }

}