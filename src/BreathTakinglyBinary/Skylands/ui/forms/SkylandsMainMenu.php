<?php
declare(strict_types=1);

namespace BreathTakinglyBinary\Skylands\ui\forms;


use BreathTakinglyBinary\libDynamicForms\SimpleForm;
use BreathTakinglyBinary\Skylands\isle\Isle;
use BreathTakinglyBinary\Skylands\locale\TranslationManager;
use BreathTakinglyBinary\Skylands\session\SessionManager;
use BreathTakinglyBinary\Skylands\session\SkylandsSession;
use BreathTakinglyBinary\Skylands\Skylands;
use pocketmine\Player;

class SkylandsMainMenu extends SimpleForm{

    private const CREATE = "create";
    private const GO = "go";
    private const INVITES = "invites";
    private const SETTINGS = "settings";
    private const VISIT = "visit";

    /**
     * This form should only be sent to a player if they have an island.
     */

    /**
     * SkylandsMainMenu constructor.
     *
     * @param Player      $player
     * @param string|null $msg
     */
    public function __construct(Player $player, ?string $msg = null){
        parent::__construct();
        $this->setTitle("SkyBlock Menu");
        if($msg !== null){
            $this->setContent($msg);
        }
        $hasIsle = false;
        if(SessionManager::getInstance()->getSession($player)->getIsle() instanceof Isle){
            $hasIsle = true;
            $this->addButton("Go to Your Isle", self::GO);
        }else{
            $this->addButton("Create an Isle", self::CREATE);
        }
        $this->addButton("Manage Invites", self::INVITES);
        $this->addButton("Visit an Isle", self::VISIT);
        if($hasIsle){
            $this->addButton("Isle Settings", self::SETTINGS);
        }
    }

    public function onResponse(Player $player, $data) : void{
        $session = SessionManager::getInstance()->getSession($player);
        switch($data){
            case self::CREATE:
                $player->sendForm(new CreateIslandMenu());
                break;
            case self::GO:
                $player->teleport($session->getIsle()->getLevel()->getSpawnLocation());
                break;
            case self::VISIT:
                $player->sendForm(new VisitIslandsMenu($player));
                break;
            case self::SETTINGS:
                $island = $session->getIsle();
                if($island instanceof Isle){
                    $player->sendForm(new IslandInformationMenu($island));
                }else{
                    //** This should never happen. */
                    $player->sendForm(new SkylandsMainMenu($player, TranslationManager::getTranslatedMessage("MENU_MESSAGE_COULD_NOT_FIND_YOUR_ISLE")));
                }
                break;
            case 3:
                $player->sendForm(new IslandOwnerSettingsMenu($player));
                break;
        }
    }
}