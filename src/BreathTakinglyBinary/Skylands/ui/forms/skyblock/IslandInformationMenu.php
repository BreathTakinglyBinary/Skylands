<?php
declare(strict_types=1);

namespace BreathTakinglyBinary\Skylands\ui\forms\skyblock;


use BreathTakinglyBinary\Skylands\ui\forms\SimpleForm;
use pocketmine\Player;
use BreathTakinglyBinary\Skylands\isle\Isle;

class IslandInformationMenu extends SimpleForm{

    public function __construct(Isle $island){
        parent::__construct();
        $this->setTitle("Island Information");

        $isMembers = count($island->getMembers());
        $content = "Blocks: " . $island->getBlocksBuilt() . "\n\n";
        $content .= $island->isLocked() ? "State: Locked\n\n" : "State: Unlocked\n\n";
        $content .= "Members: " . $isMembers . "/" . $island->getSlots() . "\n\n";
        $content .= "Online Members: " . count($island->getMembersOnline()) . "/" . $isMembers . "\n\n";
        $content .= "Category: " . $island->getCategory() . "\n\n";
        $content .= "Next Category: " . $island->getNextCategory() . "\n";

        $this->setContent($content);

        $this->addButton("Exit");
    }

    protected function onSubmit(Player $player, $data){
    }

}