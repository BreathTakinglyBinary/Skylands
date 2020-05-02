<?php
declare(strict_types=1);

namespace BreathTakinglyBinary\Skylands\ui\forms;


use BreathTakinglyBinary\libDynamicForms\CustomForm;
use BreathTakinglyBinary\Skylands\isle\IsleManager;
use BreathTakinglyBinary\Skylands\isle\IsleType;
use BreathTakinglyBinary\Skylands\locale\TranslationManager;
use BreathTakinglyBinary\Skylands\Skylands;
use pocketmine\form\FormValidationException;
use pocketmine\Player;
use function filter_var;
use const FILTER_SANITIZE_EMAIL;

class CreateIslandMenu extends CustomForm{

    /** @var IsleManager */
    private $isleManager;

    private $types = [];

    public function __construct(){
        $this->isleManager = IsleManager::getInstance();
        parent::__construct("Create an Island");
        $this->addInput("Isle Name (30 Characters Max)", "name", "MyIsle");
        $this->addToggle("Allow vistors? (Default is No)", "visitors", false);
        foreach($this->isleManager->getIsleTypes() as $type){
            $this->types[] = $type->getName();
        }
        $this->addDropdown("Isle Type", "types", $this->types);
    }

    public function onResponse(Player $player, $data) : void{
        $skyBlock = Skylands::getInstance();
        $session = $skyBlock->getSessionManager()->getSession($player);
        if($session->hasIsle()){
            $player->sendMessage(TranslationManager::getTranslatedMessage("ALREADY_HAVE_ISLE"));
            return;
        }
        if(!isset($data["name"])){
            $player->sendForm(new SkylandsMainMenu($player, TranslationManager::getTranslatedMessage("MENU_MESSAGE_ISLE_NAME_REQUIRED")));
            return;
        }

        $name = filter_var($data["name"], FILTER_SANITIZE_EMAIL);

        if(!isset($data["types"])){
            throw new FormValidationException("Isle type was not found in the returned form data. Repsone provided by " . $player->getName());
        }
        if(!isset($this->types[$data["types"]])){
            throw new FormValidationException("Provided type \"" . $data["types"] . "\" is not a verified type.  Repsone provided by " . $player->getName());
        }
        $type = $this->isleManager->getIsleTypeByName($this->types[$data["types"]]);
        if(!$type instanceof IsleType){
            throw new \RuntimeException("Invalid type " . $this->types[$data["types"]] . " found in verified types!");
        }

        if(!isset($data["visitors"])){
            throw new FormValidationException("Allow visitors data was not found in the returned form data. Repsone provided by " . $player->getName());
        }

        $locked = (bool) $data["visitors"];
        $isle = $this->isleManager->createIsle($player, $type, $name, $locked);
        $session->setIsleId($isle->getIdentifier());
        $session->save();
        $player->sendMessage(TranslationManager::getTranslatedMessage("SUCCESSFULLY_CREATED_A_ISLE"));
    }
}