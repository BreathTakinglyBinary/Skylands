<?php

declare(strict_types=1);

namespace BreathTakinglyBinary\Skylands\ui\forms;

use pocketmine\form\Form as IForm;
use pocketmine\Player;
use pocketmine\utils\MainLogger;

abstract class Form implements IForm{

    /** @var String[] */
    protected $data = [];

    public function handleResponse(Player $player, $data) : void{
        $this->processData($data);
        if($data === null){
            $this->onClose();
        }else{
            $this->onSubmit($player, $data);
        }
    }

    public function processData(&$data) : void{
    }

    public function jsonSerialize(){
        return $this->data;
    }

    protected abstract function onSubmit(Player $player, $data);

    protected function onClose(){

    }
}
