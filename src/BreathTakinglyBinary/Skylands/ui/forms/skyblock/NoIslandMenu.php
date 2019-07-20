<?php
declare(strict_types=1);

namespace BreathTakinglyBinary\Skylands\ui\forms\skyblock;


use BreathTakinglyBinary\Skylands\Skylands;
use BreathTakinglyBinary\Skylands\ui\forms\SimpleForm;
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

    protected function onSubmit(Player $player, $data){
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