<?php
declare(strict_types=1);

namespace BreathTakinglyBinary\Skylands\ui\forms\skyblock;


use BreathTakinglyBinary\Skylands\Skylands;
use BreathTakinglyBinary\Skylands\ui\forms\ModalForm;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class ConfirmDisbandIslandMenu extends ModalForm{

    public function __construct(){
        parent::__construct();
        $this->setTitle("Confirm Island Disband");
        $content = TextFormat::BOLD . TextFormat::RED . "This will delete your island!!" . TextFormat::RESET . "\n\n";
        $content .= TextFormat::GOLD . "Are you sure you want to disband your island?";
        $this->setContent($content);
        $this->setButton1("Yes");
        $this->setButton2("No");
    }

    protected function onSubmit(Player $player, $data){
        if($data){
            $isle = Skylands::getInstance()->getSessionManager()->getSession($player)->getIsle();
            Skylands::getInstance()->getIsleManager()->disbandIsle($isle);
        }
    }
}