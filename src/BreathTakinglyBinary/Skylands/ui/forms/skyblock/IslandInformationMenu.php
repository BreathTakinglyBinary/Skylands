<?php
declare(strict_types=1);

namespace BreathTakinglyBinary\Skylands\ui\forms\skyblock;


use BreathTakinglyBinary\libDynamicForms\SimpleForm;
use pocketmine\Player;
use BreathTakinglyBinary\Skylands\isle\Isle;

class IslandInformationMenu extends SimpleForm{

    public function __construct(Isle $island){
        parent::__construct();
        $this->setTitle("Island Information");

        $isMembers = count($island->getMembers());
        $content = $island->isLocked() ? "State: Locked\n\n" : "State: Unlocked\n\n";
        $content .= "Members: " . $isMembers . "/" . $island->getSlots() . "\n\n";
        $content .= "Online Members: " . count($island->getMembersOnline()) . "/" . $isMembers . "\n\n";

        $this->setContent($content);

        $this->addButton("Exit");
    }

    public function onResponse(Player $player, $data) : void{
    }
}