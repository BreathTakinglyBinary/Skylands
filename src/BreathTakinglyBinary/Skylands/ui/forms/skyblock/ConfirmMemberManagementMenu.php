<?php
declare(strict_types=1);

namespace BreathTakinglyBinary\Skylands\ui\forms\skyblock;


use BreathTakinglyBinary\Skylands\ui\forms\ModalForm;
use BreathTakinglyBinary\DynamicCore\util\Utils;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class ConfirmMemberManagementMenu extends ModalForm implements MemeberManagementActions{

    /** @var int */
    private $action;

    /** @var string */
    private $playerToManage;

    public function __construct(int $action, string $playerToManage){
        parent::__construct();
        $this->action = $action;
        $this->playerToManage = $playerToManage;

        $actionWord = Utils::getMemberManagementActionWord($action);

        $this->setTitle("Manage Member");
        $content = "You are about to \n";
        $content .= TextFormat::BOLD . TextFormat::RED . $actionWord . "\n";
        $content .= TextFormat::RESET . "player " . TextFormat::BOLD . TextFormat::GOLD . $playerToManage;
        $content .= "\n\nAre you sure?";
        $this->setContent($content);
        $this->setButton1("Yes");
        $this->setButton2("No");
    }

    protected function onSubmit(Player $player, $data){
        if($data){
            switch($this->action){
                case self::ACTION_DEMOTE:
                    Server::getInstance()->dispatchCommand($player, "isle demote " . $this->playerToManage);
                    break;
                case self::ACTION_FIRE:
                    Server::getInstance()->dispatchCommand($player, "isle fire " . $this->playerToManage);
                    break;
                case self::ACTION_PROMOTE:
                    Server::getInstance()->dispatchCommand($player, "isle promote " . $this->playerToManage);
                    break;
            }
        }
    }

}