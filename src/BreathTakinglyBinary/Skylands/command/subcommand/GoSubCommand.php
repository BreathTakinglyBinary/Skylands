<?php
declare(strict_types=1);

namespace BreathTakinglyBinary\Skylands\command\subcommand;


use BreathTakinglyBinary\Skylands\command\argument\IsleNameArgument;
use BreathTakinglyBinary\Skylands\isle\Isle;
use BreathTakinglyBinary\Skylands\locale\TranslationManager;
use BreathTakinglyBinary\Skylands\session\SessionManager;
use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use pocketmine\Player;

class GoSubCommand extends BaseSubCommand{

    public function __construct(){
        parent::__construct("go", TranslationManager::getTranslatedMessage("COMMAND_DESCRIPTION_GO"), ["Go", "join", "Join"]);
    }

    /**
     * This is where all the arguments, permissions, sub-commands, etc would be registered
     */
    protected function prepare() : void{
        //TODO: Allow joining helper islands directly.
        //$this->registerArgument(0, new IsleNameArgument("IsleName", true));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
        if(!$sender instanceof Player){
            $sender->sendMessage("This command can only be used in game.");

            return;
        }
        $session = SessionManager::getInstance()->getSession($sender);
        $isle = $session->getIsle();
        if(!$isle instanceof Isle){
            $sender->sendMessage(TranslationManager::getTranslatedMessage("COMMAND_MESSAGE_ERROR_NO_ISLE"));

            return;
        }
        $sender->teleport($isle->getLevel()->getSpawnLocation());
    }
}