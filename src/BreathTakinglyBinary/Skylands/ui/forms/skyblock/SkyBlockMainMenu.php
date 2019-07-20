<?php
declare(strict_types=1);

namespace BreathTakinglyBinary\Skylands\ui\forms\skyblock;


use BreathTakinglyBinary\Skylands\ui\forms\SimpleForm;
use pocketmine\Player;
use BreathTakinglyBinary\Skylands\isle\Isle;
use BreathTakinglyBinary\Skylands\Skylands;

class SkyBlockMainMenu extends SimpleForm{

    /**
     * This form should only be sent to a player if they have an island.
     */

    /**
     * SkyBlockMainMenu constructor.
     *
     * @param string|null $msg
     */
    public function __construct(?string $msg = null){
        parent::__construct();
        $this->setTitle("SkyBlock Menu");
        if($msg !== null){
            $this->setContent($msg);
        }
        $this->addButton("Go to Your Island");
        $this->addButton("Visit Islands");
        $this->addButton("Island Information");
        $this->addButton("Island Settings");
    }

    protected function onSubmit(Player $player, $data){
        $skyBlock = Skylands::getInstance();
        switch($data){
            case 0:
                $player->teleport($skyBlock->getSessionManager()->getSession($player)->getIsle()->getSpawnLocation());
                break;
            case 1:
                $player->sendForm(new VisitIslandsMenu($player));
                break;
            case 2:
                $island = Skylands::getInstance()->getSessionManager()->getSession($player)->getIsle();
                if($island instanceof Isle){
                    $player->sendForm(new IslandInformationMenu($island));
                }else{
                    //** This should never happen. */
                    $player->sendForm(new SkyBlockMainMenu("!!Couldn't find your island!!"));
                }
                break;
            case 3:
                $player->sendForm(new IslandOwnerSettingsMenu());
                break;
        }

    }

}