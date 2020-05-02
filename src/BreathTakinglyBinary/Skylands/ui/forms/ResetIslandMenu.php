<?php
declare(strict_types=1);

namespace BreathTakinglyBinary\Skylands\ui\forms;


use BreathTakinglyBinary\libDynamicForms\CustomForm;
use BreathTakinglyBinary\libDynamicForms\Form;
use pocketmine\Player;

class ResetIslandMenu extends CustomForm{

    public function __construct(Player $player, ?Form $previousForm = null){
        parent::__construct("Reset Island Menu", $previousForm);
    }

    /**
     * @param Player $player
     * @param        $data
     */
    public function onResponse(Player $player, $data) : void{
        // TODO: Implement onResponse() method.
    }
}