<?php
declare(strict_types=1);

namespace BreathTakinglyBinary\Skylands\ui\forms\skyblock;


use BreathTakinglyBinary\libDynamicForms\SimpleForm;
use BreathTakinglyBinary\Skylands\Skylands;
use pocketmine\Player;

class NoIslandMenu extends SimpleForm{

    public function __construct(string $msg = ""){
        parent::__construct();
        $this->setTitle("SkyBlock Menu");
        $this->setContent($msg);
        $this->addButton("Create an Island");
        $this->addButton("Accept Invitations");
        $this->addButton("Visit Islands");
    }

    public function onResponse(Player $player, $data) : void{
        $skyBlock = Skylands::getInstance();
        $session = $skyBlock->getSessionManager()->getSession($player);
        switch($data){
            case 0:
                $form = new CreateIslandMenu();
                break;
            case 1:
                $form = new ViewIslandInvitesMenu($session);
                break;
            case 2:
                $form = new VisitIslandsMenu($player);
                break;
            default:
                $form = new SkyBlockMainMenu();
        }
        $player->sendForm($form);
    }
}